<?php

namespace App\Http\Controllers\Crossfit;

use App\Models\Crossfit\Company as CrossfitCompany;
use App\Models\Crossfit\ContactInformation as CrossfitContactInformation;

class LandingController extends \App\Http\Controllers\Controller
{

    protected $perPage = 15;

    /**
     * Show CrossFit landing page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('crossfit.landing');
    }

    /**
     * Show CrossFit signup page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function signupForm()
    {
        return view('crossfit.signup', [
            'sources' => CrossfitCompany::SOURCES_LIST,
        ]);
    }

    /**
     * Show locations with filled amenities list page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function signup()
    {
        request()->validate([
            'legal_business_entity' => 'required|string',
            'affiliate_name'        => 'required|string',
            'location_address'      => 'required|string',
            'location_city'         => 'required|string',
            'location_state'        => 'required|string',
            'location_zip'          => 'required|string',
            'first_name'            => 'required|string',
            'last_name'             => 'required|string',
            'email'                 => 'required|string|email',
            'phone'                 => 'required|string',
            'membership_rate'       => 'required|numeric|min:0',
            'source'                => 'required|string|in:'.implode(',', CrossfitCompany::SOURCES_LIST),
        ]);

        $company = CrossfitCompany::create([
            'legal_business_entity' => request()->get('legal_business_entity'),
            'affiliate_name'        => request()->get('affiliate_name'),
            'location_address'      => request()->get('location_address'),
            'location_city'         => request()->get('location_city'),
            'location_state'        => request()->get('location_state'),
            'location_zip'          => request()->get('location_zip'),
            'membership_rate'       => request()->get('membership_rate'),
            'source'                => request()->get('source'),
        ]);

        $contact = CrossfitContactInformation::create([
            'crossfit_company_id' => $company->id,
            'first_name'          => request()->get('first_name'),
            'last_name'           => request()->get('last_name'),
            'email'               => request()->get('email'),
            'phone'               => request()->get('phone'),
        ]);

        return redirect(route('crossfit.signup.success'));
    }

    /**
     * Show CrossFit signup page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function signupSuccess()
    {
        return view('crossfit.success');
    }
}
