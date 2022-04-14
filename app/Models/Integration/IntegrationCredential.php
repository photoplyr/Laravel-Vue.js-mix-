<?php

namespace App\Models\Integration;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrationCredential extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'integration_credentials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'provider',
        'token_type',
        'access_token',
        'refresh_token',
        'expires_at',
        'options',
    ];

    /**
     * Get Options Json Decoded
     */
    public function getOptionsFormattedAttribute() {
        return json_decode($this->options);
    }

    /**
     * Get Token Expired state
     */
    public function getIsExpiredAttribute() {
        return strtotime($this->expires_at) < strtotime('now');
    }

    /**
     * Revoke Device token
     * @return boolean
     */
    public function revoke() {
        switch ($this->provider) {
            case 'mywellness':
                (new \App\Services\Integrations\MyWellness)->revoke($this);

                $this->delete();
                break;

            case 'fitbit':
                (new \App\Services\Integrations\Fitbit)->revoke($this);

                $this->delete();
                break;

            case 'strava':
                (new \App\Services\Integrations\Strava)->revoke($this);

                $this->delete();
                break;

            default:
                // code...
                break;
        }

        return true;
    }

    /**
     * Refresh Strava token
     * @return boolean
     */
    public function refresh()
    {
        switch ($this->provider) {
            case 'strava':
                $response = (new \App\Services\Integrations\Strava)->refresh($this);

                if ($response['success']) {
                    $this->access_token  = $response['token']->access_token;
                    $this->refresh_token = $response['token']->refresh_token;
                    $this->expires_at    = Carbon::parse($response['token']->expires_at);
                }
                break;

            default:
                // code...
                break;
        }

        return $this;
    }
}
