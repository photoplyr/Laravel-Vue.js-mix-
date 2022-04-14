<?php

namespace App\Models\Crossfit;

use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crossfit_contacts_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'crossfit_company_id',
        'email',
        'first_name',
        'last_name',
        'phone',
    ];

    /**
     * Get the company record associated with the contact.
     */
    public function company()
    {
        return $this->hasOne('App\Models\Crossfit\Company', 'id', 'company_id');
    }
}
