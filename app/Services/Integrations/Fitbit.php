<?php

namespace App\Services\Integrations;

use App\User;
use App\Models\Statistics\Activity;
use App\Models\Integration\IntegrationCredential;

use djchen\OAuth2\Client\Provider\Fitbit as FitbitProvider;

class Fitbit
{

    protected $clientId           = null;
    protected $clientSecret       = null;
    protected $defaultRedirectUri = null;
    protected $provider           = null;


    /**
     * Construct Fitbit class
     */
    function __construct() {
        $this->clientId           = config('services.fitbit.client_id');
        $this->clientSecret       = config('services.fitbit.client_secret');
        $this->defaultRedirectUri = url('/oauth/fitbit');
    }

    /**
     * Set Fitbit Provider
     * @param string $redirect
     * @return boolean
     */
    private function setProvider($redirect = null) {
        $settings = [
            'clientId'          => $this->clientId,
            'clientSecret'      => $this->clientSecret,
        ];

        if ($redirect) {
            $settings['redirectUri'] = $redirect;
        }

        $this->provider = new FitbitProvider($settings);

        return true;
    }

    /**
     * Get Fitbit Oauth link
     * @return string
     */
    public function getOauthLink($redirect) {
        $this->setProvider($redirect);

        return $this->provider->getAuthorizationUrl();
    }

    /**
     * Get Fitbit Token
     * @param string $code
     * @param int $memberId
     *
     * @return array
     */
    public function getUserAccessToken($code, $memberId) {
        $this->setProvider($this->defaultRedirectUri);

        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $token = IntegrationCredential::where('user_id', $memberId)
                                          ->where('provider', 'fitbit')
                                          ->first();

            if (!$token) {
                $token = new IntegrationCredential();
                $token->user_id  = $memberId;
                $token->provider = 'fitbit';
            }

            $token->token_type    = '';
            $token->access_token  = $accessToken->getToken();
            $token->refresh_token = $accessToken->getRefreshToken();
            $token->expires_at    = date('Y-m-d H:i:s', $accessToken->getExpires());
            $token->options       = json_encode([]);
            $token->save();

            return [
                'success' => true,
                'message' => 'Fitbit token successfully saved',
            ];
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refresh Fitbit Token
     * @param string $refresh_token
     *
     * @return array
     */
    public function refresh($id, $refresh_token)
    {
        $this->setProvider($this->defaultRedirectUri);

        try {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $refresh_token,
            ]);

            $token = IntegrationCredential::where('id', $id)->first();
            $token->access_token  = $newAccessToken->getToken();
            $token->refresh_token = $newAccessToken->getRefreshToken();
            $token->expires_at    = date('Y-m-d H:i:s', $newAccessToken->getExpires());
            $token->save();

            return [
                'success' => true,
                'message' => 'Fitbit token successfully refreshed',
                'token'   => $token,
            ];
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Revoke Fitbit Token
     * @param string $refresh_token
     *
     * @return array
     */
    public function revoke($refresh_token)
    {
        $this->setProvider($this->defaultRedirectUri);

        try {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $refresh_token
            ]);

            $this->provider->revoke($newAccessToken);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Fitbit authenticated response
     * @param string $accessToken
     * @param string $url
     */
    private function getResponse($accessToken, $url) {
        $request = $this->provider->getAuthenticatedRequest(
           FitbitProvider::METHOD_GET,
           $url,
           $accessToken,
           ['headers' => [FitbitProvider::HEADER_ACCEPT_LANG => 'en_US'], [FitbitProvider::HEADER_ACCEPT_LOCALE => 'en_US']]
        );
        // Make the authenticated API request and get the parsed response.
        return $this->provider->getParsedResponse($request);
    }

    /**
     * Parse Data by Fitbit Token
     * @param string $access_token
     *
     * @return array
     */
    public function parseData($access_token, $memberId)
    {
        $this->setProvider($this->defaultRedirectUri);

        $currentDate = date('Y-m-d');

        // create a $dt object with the UTC timezone
        $dt = new \DateTime($currentDate);

        // change the timezone of the object without changing it's time
        $dt->setTimezone(new \DateTimeZone('America/Los_Angeles'));

        // format the datetime
        $currentDate = $dt->format('Y-m-d');

        /*
        // Get body weight for 30 days information
        $bodyWeight = $this->getResponse($access_token, FitbitProvider::BASE_FITBIT_API_URL . '/1/user/-/body/weight/date/'. $currentDate . '/30d.json');

        foreach ($bodyWeight['body-weight'] as $weightData) {
            $weight = Activity::where('user_id', $memberId)
                              ->where('timestamp', $weightData['dateTime'])
                              ->first();

            if ($weight) {
                $weight->weight       = $weightData['value'];
                $weight->name         = 'Fitbit';
                $weight->equipment_id = 52;
                $weight->client_id    = 12;

                 if ($weightData['value'] > 0)
                $weight->save();
            } else {
                $lastWeight = Activity::where('user_id', $memberId)
                                      ->orderBy('timestamp', 'DESC')
                                      ->first();

                $weight = new Activity();
                $weight->user_id      = $memberId;
                $weight->weight       = $weightData['value'];
                $weight->timestamp    = $weightData['dateTime'];
                $weight->name         = 'Fitbit';
                $weight->equipment_id = 52;
                $weight->client_id    = 12;
                if ($weightData['value'] > 0)
                $weight->save();
            }
        }

        // Get body log fat for 30 days information
        $bodyFat = $this->getResponse($access_token, FitbitProvider::BASE_FITBIT_API_URL . '/1/user/-/body/log/fat/date/'. $currentDate . '/30d.json');

        foreach ($bodyFat['fat'] as $weightData) {
            $weight = Activity::where('user_id', $memberId)
                              ->where('timestamp', $weightData['date'])
                              ->first();

            if ($weightData['fat'] > 0) {
                if ($weight) {
                    $weight->bmi          = $weightData['fat'];
                    $weight->name         = 'Fitbit';
                    $weight->equipment_id = 52;
                    $weight->client_id    = 12;
                    if ($weightData['fat'] > 0)
                    $weight->save();
                } else {
                    $lastWeight = Activity::where('user_id', $memberId)
                                          ->orderBy('timestamp', 'DESC')
                                          ->first();

                    $weight = new Activity();
                    $weight->user_id      = $memberId;
                    $weight->bmi          = $weightData['fat'];
                    $weight->name         = 'Fitbit';
                    $weight->timestamp    = $weightData['date'];
                    $weight->equipment_id = 52;
                    $weight->client_id    = 12;
                    if ($weightData['fat'] > 0)
                    $weight->save();
                }
            }
        }

        // Get foods log water for 30 days information
        $bodyWater = $this->getResponse($access_token, FitbitProvider::BASE_FITBIT_API_URL . '/1/user/-/foods/log/water/date/'. $currentDate . '/30d.json');
        foreach ($bodyWater['foods-log-water'] as $waterData) {
            $water = Activity::where('user_id', $memberId)
                             ->where('timestamp', $waterData['dateTime'])
                             ->first();

            // If the food items exists for the day/meal update the values
            if ($waterData['value'] > 0) {
                if ($water) {
                    if ($water->water < ($waterData['value'] / 8)) { // Convert 1 cup = 8oz
                        $water->water        = ($waterData['value'] / 8);
                        $water->name         = 'Fitbit';
                        $water->equipment_id = 52;
                        $water->client_id    = 12;

                        if ($waterData['value'] > 0)
                        $water->save();
                    }
                } else {
                    // else crate the food item
                    $water = new Activity();
                    $water->user_id      = $memberId;
                    $water->timestamp    = $waterData['dateTime'];
                    $water->name         = 'Fitbit';
                    $water->equipment_id = 52;
                    $water->water        = ($waterData['value'] / 8);
                    $water->client_id    = 12;
                    if ($waterData['value'] > 0)
                    $water->save();
                }
            }
        }
        */

        // Get user activities for last 7 days information
        for ($i = 6; $i >= 0; $i--) {
            $currentDate = date('Y-m-d', strtotime('now - '.$i.' days'));

            // create a $dt object with the UTC timezone
            // $dt = new \DateTime($currentDate);
            //
            // change the timezone of the object without changing it's time
            // $dt->setTimezone(new \DateTimeZone('America/Los_Angeles'));
            //
            // format the datetime
            // $currentDate = $dt->format('Y-m-d');

            $activities = $this->getResponse($access_token, FitbitProvider::BASE_FITBIT_API_URL . '/1/user/-/activities/date/'. $currentDate . '.json');
            foreach ($activities['activities'] as $activityData) {
                $lastModified = date('Y-m-d H:i:s', strtotime($activityData['lastModified']));

                $activity = Activity::where('user_id', $memberId)
                                    ->where('timestamp', $lastModified)
                                    ->first();
                if ($activity) {
                    $distance = 0.0;
                    if (array_key_exists('distance',$activityData)) {
                        $distance = $activityData['distance'];
                    }

                    $duration = 0.0;
                    if (array_key_exists('duration',$activityData) && $activityData['duration'] > 0) {
                        $duration = $activityData['duration'] / 1000;
                    }

                    Activity::where('user_id', $memberId)
                            ->where('timestamp', $lastModified )
                            ->update([
                                'steps'        => $activityData['steps'],
                                'calories'     => $activityData['calories'],
                                'name'         => $activityData['name'] . ' ' . $activityData['description'],
                                // 'equipment_id' => 52,
                                // 'client_id'    => 12,
                                'distance'     => $distance,
                                'duration'     => $duration,
                            ]);
                } else {
                    $distance = 0.0;

                    if (array_key_exists('distance',$activityData)) {
                        $distance = $activityData['distance'];
                    }

                    $duration = 0.0;
                    if (array_key_exists('duration',$activityData) && $activityData['duration'] > 0) {
                        $duration = $activityData['duration'] / 1000;
                    }

                    $exercise = new Activity();
                    $exercise->user_id      = $memberId;
                    $exercise->name         = $activityData['name'] . ' ' . $activityData['description'];
                    $exercise->equipment_id = 52;
                    $exercise->client_id    = 12;
                    $exercise->timestamp    = $lastModified ;
                    $exercise->calories     = $activityData['calories'];
                    $exercise->steps        = $activityData['steps'];
                    $exercise->distance     = $distance;
                    $exercise->duration     = $duration;

                    if ($duration > 0) {
                        $exercise->save();
                    }
                }
            }

            $summary = $activities['summary'];

            if ($summary['activityCalories'] > 0) {
                $steps         = $summary['steps'];
                $calories      = $summary['activityCalories'];
                // $activeSeconds = ($summary['fairlyActiveMinutes'] + $summary['lightlyActiveMinutes'] + $summary['veryActiveMinutes']) * 60;
                $activeSeconds = ($summary['fairlyActiveMinutes'] + $summary['veryActiveMinutes']) * 60;
                $distance      = collect($summary['distances'])->filter(function($item) {
                                                                  return $item['activity'] == 'total';
                                                              })
                                                              ->first();
                $heartPeak     = isset($summary['heartRateZones']) ? collect($summary['heartRateZones'])->filter(function($item) {
                                                                                                            return $item['name'] == 'Cardio';
                                                                                                        })
                                                                                                        ->first() : null;

                $distanceValue = $distance && isset($distance['distance']) ? $distance['distance'] : 0;
                $heartValue    = $heartPeak && isset($heartPeak['max']) ? $heartPeak['max'] : 0;

                $exercise = Activity::where('user_id', $memberId)
			                        ->where('timestamp', $currentDate)
                                    ->where('client_id', 12)
                                    ->count();

                if ($exercise > 0) {
			        // var_dump($currentDate);
                    Activity::where('user_id', $memberId)
                            ->where('timestamp', $currentDate)
                            ->update([
                                'steps'        => $steps,
                                'calories'     => $calories,
                                // 'name'         => 'Fitbit',
                                // 'equipment_id' => 52,
                                // 'client_id'    => 12,
                                'distance'     => $distanceValue,
                                'duration'     => $activeSeconds,
                                'heart'        => $heartValue,
                            ]);
		        } else {
                    $exercise = new Activity();
                    $exercise->user_id      = $memberId;
                    $exercise->name         = 'Fitbit';
                    $exercise->equipment_id = 52;
                    $exercise->client_id    = 12;
                    $exercise->timestamp    = $currentDate;
                    $exercise->calories     = $calories;
                    $exercise->steps        = $steps;
                    $exercise->distance     = $distanceValue;
                    $exercise->duration     = $activeSeconds;
                    $exercise->heart        = $heartValue;

                    if ($calories > 0) {
                        $exercise->save();
                    }
                }
            }
        }

        return [
            'success' => true,
        ];
    }

}
