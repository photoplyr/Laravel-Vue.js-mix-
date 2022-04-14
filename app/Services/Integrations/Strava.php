<?php

namespace App\Services\Integrations;

use cURL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Integration\IntegrationCredential;
use App\Models\Statistics\Activity;

class Strava
{
    /**
     * Client ID
     * @var string|null
     */
    private $clientId = null;

    /**
     * Client Secret
     * @var string|null
     */
    private $clientSecret = null;

    /**
     * Client redirect uri
     * @var string|null
     */
    private $redirectURI = null;

    /**
     * Api Version
     * @var string
     */
    private $apiVersion = 'v3';

    /**
     * curl
     * @var cURL|null
     */
    private $curl = null;

    /**
     * Construct Strava
     */
    function __construct() {
        $this->clientId     = config('services.strava.client_id');
        $this->clientSecret = config('services.strava.client_secret');
        $this->redirectURI  = config('services.strava.redirect_uri');
    }

    /**
     * Setup curl
     */
    function setupCurl() {
        cURL::setDefaultHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Get Strava oauth link
     * @return string
     */
    public function getOauthLink()
    {
        return 'https://www.strava.com/oauth/authorize?client_id='. $this->clientId .'&response_type=code&redirect_uri='. $this->redirectURI .'&approval_prompt=auto&scope=activity:read_all';
    }

    /**
     * Get Strava access token
     * @param  string $code
     * @param  integer $memberId
     * @return array
     */
    public function getUserAccessToken($code, $memberId)
    {
        $this->setupCurl();

        $data = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
        ];

        $response = cURL::rawPost('https://www.strava.com/api/'. $this->apiVersion .'/oauth/token', json_encode($data, JSON_FORCE_OBJECT));
        if ($response->statusCode == 200) {
            $body = json_decode($response->body);

            if (isset($body->access_token) && $body->access_token) {
                $token = IntegrationCredential::where('user_id', $memberId)
                                              ->where('provider', 'strava')
                                              ->first();

                if (!$token) {
                    $token = new IntegrationCredential;
                    $token->user_id = $memberId;
                    $token->provider = 'strava';
                }

                $options = [
                    'strava_user_id' => $body->athlete->id ?? '',
                ];

                $token->token_type    = $body->token_type;
                $token->access_token  = $body->access_token;
                $token->refresh_token = $body->refresh_token;
                $token->expires_at    = Carbon::createFromTimestamp($body->expires_at)->format('Y-m-d H:i:s');
                $token->options       = json_encode($options);
                $token->save();

                return [
                    'success' => true,
                    'token'   => $token,
                ];
            } else {
                Log::info('[Strava] Response error: '. $response->statusCode, (array) json_decode($response->body));
            }
        } else {
            Log::info('[Strava] Response error: '. $response->statusCode, [$response->statusText]);
        }

        return [
            'success' => false,
            'message' => 'Strava response error',
        ];
    }

    /**
     * Get Strava Data for user
     * @param  MywellnessToken $token
     * @param  string|null     $date
     * @return array
     */
    public function parseData($token, $date = null)
    {
        $this->setupCurl();

        if ($token->is_expired) {
            $token = $token->refresh();
        }

        $data = [
            'after'    => Carbon::parse('- 1 week')->timestamp,
            'page'     => 1,
            'per_page' => 100,
        ];

        if ($date) {
            $data['after']  = Carbon::parse($date)->setTime(0, 0, 0, 0)->timestamp;
            $data['before'] = Carbon::parse($date)->setTime(23, 59, 59, 0)->timestamp;
        }

        $url      = cURL::buildUrl('https://www.strava.com/api/'. $this->apiVersion .'/athlete/activities', $data);
        $response = cURL::newRequest('GET', $url)
                        ->setHeader('Authorization', $token->token_type.' '.$token->access_token)
                        ->send();

        if ($response->statusCode == 200) {
            $body = json_decode($response->body);

            if (is_array($body)) {
                foreach ($body as $activity) {
                    $startDate = Carbon::parse($activity->start_date);

                    $equipment_id = 59;
                    if (in_array($activity->type, ['EBikeRide', 'Ride', 'VirtualRide'])) {
                        $equipment_id = 24;
                    } elseif (in_array($activity->type, ['Run', 'VirtualRun', 'Walk'])) {
                        $equipment_id = 49;
                    }

                    $chActivity = Activity::firstOrNew([
                        'user_id'      => $token->user_id,
                        'timestamp'    => $startDate,
                        'client_id'    => 20,
                        'equipment_id' => $equipment_id,
                        'name'         => $activity->name,
                    ]);

                    $seconds = $activity->moving_time;

                    $chActivity->distance = $activity->distance / 1609 ?? 0; // meters
                    $chActivity->duration = $seconds;
                    $chActivity->minutes  = floor($seconds/60);
                    $chActivity->heart    = $activity->has_heartrate ? $activity->average_heartrate : 0;
                    $chActivity->watts    = isset($activity->average_watts) ? $activity->average_watts : 0;
                    $chActivity->save();
                }
            }

            return [
                'success' => true,
            ];
        }

        return [
            'success' => false,
        ];
    }

    /**
     * Refresh token
     * @param  StravaToken $token
     * @return array
     */
    public function refresh($token)
    {
        $this->setupCurl();

        $data = [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $token->refresh_token,
        ];

        $response = cURL::rawPost('https://www.strava.com/api/'. $this->apiVersion .'/oauth/token', json_encode($data, JSON_FORCE_OBJECT));
        if ($response->statusCode == 200) {
            $body = json_decode($response->body);

            if (isset($body->access_token) && $body->access_token) {
                $token = IntegrationCredential::where('user_id', $token->user_id)
                                              ->where('provider', 'strava')
                                              ->first();

                if (!$token) {
                    $token = new IntegrationCredential;
                    $token->user_id  = $token->user_id;
                    $token->provider = 'strava';
                }

                $options = [
                    'strava_user_id' => $body->athlete->id ?? '',
                ];

                $token->token_type    = $body->token_type;
                $token->access_token  = $body->access_token;
                $token->refresh_token = $body->refresh_token;
                $token->expires_at    = Carbon::createFromTimestamp($body->expires_at)->format('Y-m-d H:i:s');
                $token->options       = json_encode($options);
                $token->save();

                return [
                    'success' => true,
                    'token'   => $token,
                ];
            } else {
                Log::info('[Strava] Response error: '. $response->statusCode, (array) json_decode($response->body));
            }
        } else {
            Log::info('[Strava] Response error: '. $response->statusCode, [$response->statusText]);
        }

        return [
            'success' => false,
            'message' => 'Strava response error',
        ];
    }

    /**
     * Revoke token
     * @param  StravaToken $token
     * @return array
     */
    public function revoke($token)
    {
        $this->setupCurl();

        $data = [
            'access_token' => $token->access_token,
        ];

        $response = cURL::rawPost('https://www.strava.com/oauth/deauthorize', json_encode($data, JSON_FORCE_OBJECT));
        return true;
    }

}
