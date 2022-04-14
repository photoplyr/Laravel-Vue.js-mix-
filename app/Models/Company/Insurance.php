<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{

 protected $table = 'insurance_company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'insurance_id',
        'company_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Always take model with relations list
     *
     * @var array
     */
    protected $with = ['insurance', 'company'];

    /**
     * Get the insurance company record associated with the company.
     */
    public function insurance()
    {
        return $this->hasOne('App\Models\Company\Company', 'id', 'insurance_id');
    }

    /**
     * Get the company record.
     */
    public function company()
    {
        return $this->hasOne('App\Models\Company\Company', 'id', 'company_id');
    }

    /**
     * Get Cross Fit companies ids.
     */
    public static function getCrossFitCompaniesIds()
    {
        return self::where('insurance_id', 29)->pluck('company_id')->toArray();
    }

}
