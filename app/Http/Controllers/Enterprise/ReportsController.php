<?php

namespace App\Http\Controllers\Enterprise;

use DB;
use App\Models\Crossfit\Company as CrossfitCompany;

class ReportsController extends \App\Http\Controllers\Controller
{
    /**
     * Download Onboard report as .csv
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function downloadOnboardReport()
    {

        $report = DB::select("select *  FROM _report_amenities_detail order by ch_id,parent_id");

        $csv = fopen('php://memory', 'w');

        $header = [
            'Brand',
            'CH ID',
            'Club ID',
            'Parent ID',
            'Location',
            'Address',
            'City',
            'State',
            'Postal',
            'Phone',
            'Enrolled',
            'Program Name',
            'Role',
            'First Name',
            'Last Name',
            'Email',
            'Credit Card Required',
            'Bank Account Required',
            'Amenities Waived',
            'Completed Amenities',
            'Credit Card on File',
            'Banking Account on File',
            'Active',
            'Onboarding Date',
            'Shipping',
        ];
        fputcsv($csv, $header);

        foreach ($report as $item) {
            fputcsv($csv, [
                $item->brand,
                $item->ch_id,
                $item->club_id,
                $item->parent_id,
                $item->brand_location,
                $item->brand_address,
                $item->brand_city,
                $item->brand_state,
                $item->brand_postal,
                $item->brand_phone,
                $item->enrolled,
                $item->program_name,

                $item->role_name,
                $item->fname,
                $item->lname,
                $item->email,
                $item->credit_card_required ? 'YES' : 'NO',
                $item->payment_required ? 'YES' : 'NO',
                $item->amenities_waived ? 'YES' : 'NO',
                $item->completed_amenities ? 'YES' : 'NO',
                $item->card_on_file ? 'YES' : 'NO',
                $item->bank_on_file ? 'YES' : 'NO',
                $item->status ? 'YES' : 'NO',
                $item->created ? date('m/d/Y', strtotime($item->created)) : '-',
                $item->shipping,
                // $item->updated,
            ]);
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="Onboard-Report-'.date('Y-m-d').'.csv";');

        fseek($csv, 0);
        fpassthru($csv);
        fclose($csv);

        die();
    }

    /**
     * Download Crossfit Onboarding report as .csv
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function downloadCrossfitOnboarding()
    {
        return response()->streamDownload(function () {
            $report = CrossfitCompany::with('contact')->get();

            $csv = fopen('php://output', 'w');

            $header = [
                'Legal Business Entity',
                'Name of CrossFit Affiliate',
                'Location Address',
                'Location City',
                'Location State',
                'Location Zip',
                'Membership Rate',
                'Source',
                'Contact First Name',
                'Contact Last Name',
                'Contact Email',
                'Contact Phone',
                'Created At',
            ];
            fputcsv($csv, $header);

            foreach ($report as $item) {
                fputcsv($csv, [
                    $item->legal_business_entity,
                    $item->affiliate_name,
                    $item->location_address,
                    $item->location_city,
                    $item->location_state,
                    $item->location_zip,
                    $item->membership_rate,
                    $item->source,
                    $item->contact->first_name,
                    $item->contact->last_name,
                    $item->contact->email,
                    $item->contact->phone,
                    $item->created_at->format('m/d/Y h:ia'),
                ]);
            }

            fclose($csv);
        }, 'CrossFit-Onboarding-'.date('Y-m-d').'.csv');
    }
}
