<?php

namespace App\Models\Crossfit;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    const
        SOURCES_LIST = [
            'Another Affiliate owner',
            'Affiliate Partner Network announcement email',
            'Optum Program Member',
            'Social Media',
            'Weekly Affiliate Newsletter',
            'US District Rep; Country Manager',
            'Affiliate Town Hall',
        ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crossfit_companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'legal_business_entity',
        'affiliate_name',
        'location_address',
        'location_city',
        'location_state',
        'location_zip',
        'membership_rate',
        'source',
    ];

    /**
     * Get the contact record associated with the company.
     */
    public function contact()
    {
        return $this->hasOne('App\Models\Crossfit\ContactInformation', 'crossfit_company_id', 'id');
    }
}
