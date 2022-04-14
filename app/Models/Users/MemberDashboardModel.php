<?php

namespace App\Models\Users;

use DB;
use Carbon\Carbon;
use App\Models\Company\Location;
use App\Models\Company\CheckinHistory;
use App\Models\Company\ActivityHistory;
use App\Models\Company\CompanyProgram;
use App\Models\Concierge\MemberWellnessModel;
use Illuminate\Support\Facades\Log;

class MemberDashboardModel
{
    /**
     * Get member dashboard data
     * @param integer $memberId
     * @return array
     */
    public static function getDashboard($memberId)
    {
        $usage = "";
        $wellness = [];
        $checkins = [];
        $activity = [];
        $start    = now()->subYears(1)->format('Y-m-01 00:00:00');
        $startMonth    = now()->format('Y-m-01');
        $member_access = null;
        $company_program = null;
        $member = null;
        $corporate= [
            'activity_score' => 0,
            'activity_calories' => 0,
            'last_seen' => now()->format('m/d'),
            'trophy' => '3G',
            'badge' => 0,
            'overall' => 0,
        ];

        $locationId = auth()->user()->location_id;


        $member = User::whereId($memberId)
                        ->with('program', 'member_program', 'member_devices')
                        ->first();

        $program = $member && $member->member_program? $member->member_program:null;

        if ($program) {
            $company_program = CompanyProgram::whereCompanyId(auth()->user()->company->id)
                                            ->where('program_id', '=', $program->program_id)->first();

            $member_access  = CheckinHistory::selectRaw('COUNT(*) as count')
                                ->where(function ($query) use ($locationId) {
                                    if (!auth()->user()->isWellness())
                                        $query->where('location_id','=', $locationId);
                                })
                                ->whereUserId($memberId)
                                ->has('user')
                                ->where('timestamp', '>=', $startMonth)
                                ->where('program_id', '=', $program->program_id)
                                ->first();
        }

        if ($member_access && $company_program) {
            $usage = $member_access->count . " OF " . $company_program->allowance;
        }

        if ($program && $company_program) {
            if ($company_program->allowance == 0)
                $usage = "--";
            }

        $checkins = DB::select("select name, timestamp,
            1 as type from checkin_history
            inner join locations on locations.id = checkin_history.location_id
            where ".(auth()->user()->isWellness()?"":"location_id = $locationId and ")."user_id = $memberId and timestamp >= '$start'
            union select name, timestamp,
            2 as type from activity where client_id NOT IN (0) and user_id = $memberId and timestamp >= '$start'");

        $checkins = collect($checkins)
        ->map(function($item) {
            return [
                'locationName' => $item->name ?? '-',
                'date'         => Carbon::parse($item->timestamp)->format('Y-m-d'),
                'type'         => $item->type,
            ];
        })
        ->groupBy('date')
        ->toArray();

        if ($member) {
            $wellness = MemberWellnessModel::getWellness($member);
            if ($wellness) {
                $lastCheckin = CheckinHistory::where('user_id', auth()->id())->orderBy('timestamp','DESC')->first();
                $corporate['activity_score']    = isset($wellness['points'])?$wellness['points']['value']:0;
                $corporate['activity_calories'] = isset($wellness['calories'])?$wellness['calories']['value']:0;


                if ($lastCheckin)
                    $corporate['last_seen']  = Carbon::parse($lastCheckin->timestamp)->toDateString();

                $totalBadge  = 0;
                if ($corporate['activity_score'] > 0) $totalBadge++;
                if ($corporate['activity_score'] >= 10) $totalBadge++;
                for ($i=0; $i<8; $i++) {
                    if ($corporate['activity_score'] >= ($i+1)*25) $totalBadge++;
                }

                $corporate['badge']             = $totalBadge;
            }
            $member->attachMemberDevices();

            $calories =  ActivityHistory::selectRaw("sum(calories) as sum,TO_CHAR(timestamp,'YYYY-MM-DD') as date")
                ->where('user_id', $memberId)
                ->where('timestamp', '>', Carbon::now()->subDays(30)->format('Y-m-d 00:00:00'))
                ->groupBy('date')
                ->orderBy('date')->get();
                //->pluck('sum')->toArray();


            $distance =  ActivityHistory::selectRaw("sum(distance) as sum,TO_CHAR(timestamp,'YYYY-MM-DD') as date")
                ->where('user_id', $memberId)
                ->where('timestamp', '>', Carbon::now()->subDays(30)->format('Y-m-d 00:00:00'))
                ->groupBy('date')
                ->orderBy('date')->get();
                //->pluck('sum')->toArray();

        $dd = [];

        foreach ($distance as $item) {
            $dd[] = array(strtotime($item->date), ($item->sum) * 1);
        }
        $distance = $dd;


        $dd = [];
        foreach ($calories as $item) {
            $dd[] = array(strtotime($item->date), ($item->sum) * 1);
        }
        $calories = $dd;

            // get challenge data
            $chartData = array();

            $chartData[] = array(
                'name' => 'distance',
                'data' => $distance
            );

            $chartData[] = array(
                'name' => 'calories',
                'data' => $calories
            );

            $step_points = ActivityHistory::selectRaw("sum(score) as step_points, avg(steps) as avg_step_count")
                ->where('user_id', $memberId)
                ->where('steps', '>', 0)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();

            $calorie_points = ActivityHistory::selectRaw("sum(score) as calories_points, avg(calories) as avg_calories_count")
                ->where('user_id', $memberId)
                ->where('calories', '>', 0)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();
            $distance_points = ActivityHistory::selectRaw("sum(score) as distance_points, avg(distance) as avg_distance_count")
                ->where('user_id', $memberId)
                ->where('distance', '>', 0)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();
            $activity_points = ActivityHistory::selectRaw("sum(score) as activity_time_points, avg(duration/60) as avg_activity_time_min")
                ->where('user_id', $memberId)
                ->where('duration', '>', 0)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();
            $watts_points = ActivityHistory::selectRaw("sum(score) as watts_points, avg(watts) as avg_watts_count")
                ->where('user_id', $memberId)
                ->where('watts', '>', 0)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();
            $total_points = ActivityHistory::selectRaw("sum(score) as total_points")
                ->where('user_id', $memberId)
                ->where('timestamp', '<=', Carbon::now()->format('Y-m-d 00:00:00'))
                ->where('timestamp', '>=', Carbon::now()->format('Y-m-d 23:59:59'))
                ->get();


            $activity = array(
                'total_points'    => $total_points[0]->total_points == null ? 0 : $total_points[0]->total_points,
                'steps'           => array(
                    'score'   => $step_points[0]->step_points == null ? 0 : $step_points[0]->step_points,
                    'avg'      => round($step_points[0]->avg_step_count)
                ),
                'calories'           => array(
                    'score'   => $calorie_points[0]->calories_points == null ? 0 : $calorie_points[0]->calories_points,
                    'avg'      => round($calorie_points[0]->avg_calories_count)
                ),
                'distance'           => array(
                    'score'   => $distance_points[0]->distance_points == null ? 0 : $distance_points[0]->distance_points,
                    'avg'      => round($distance_points[0]->avg_distance_count)
                ),
                'activity'           => array(
                    'score'   => $activity_points[0]->activity_time_points == null ? 0 : $activity_points[0]->activity_time_points,
                    'avg'      => round($activity_points[0]->avg_avtivity_time_min)
                ),
                'watts'           => array(
                    'score'   => $watts_points[0]->watts_points == null ? 0 : $watts_points[0]->watts_points,
                    'avg'      => round($watts_points[0]->avg_watts_count)
                ),
                'chartData'  => $chartData
            );
        }

        return [
            'start'    => $start,
            'member'   => (object) $member,
            'checkins' => $checkins,
            'wellness' => $wellness,
            'usage'    => $usage,
            'corporate'=> $corporate,
            'activity' => $activity,
        ];
    }

     public static function getCorporateDashboard($memberId)
    {
        $usage = "";
        $wellness = [];
        $checkins = [];
        $array_merge = [];
        $start    = now()->subYears(1)->format('Y-m-01 00:00:00');
        $startMonth    = now()->format('Y-m-01');
        $member_access = null;
        $company_program = null;

        $memberRole = Roles::where('slug', 'club_member')->first();
        $companyId = auth()->user()->company_id;

        if ($companyId) {
            $member = User::whereId($memberId)
                          ->where('role_id', $memberRole->id)
                          ->with('program', 'member_program')
                          ->first();

            $program = $member && $member->member_program? $member->member_program:null;

            if ($program) {
                $company_program = CompanyProgram::whereCompanyId(auth()->user()->company->id)
                                                ->where('program_id', '=', $program->program_id)->first();

                $member_access  = CheckinHistory::selectRaw('COUNT(*) as count')
                                    ->whereUserId($memberId)
                                    ->has('user')
                                    ->where('timestamp', '>=', $startMonth)
                                    ->where('program_id', '=', $program->program_id)
                                    ->first();
            }

            if ($member_access && $company_program) {
                $usage = $member_access->count . " OF " . $company_program->allowance;
            }

            if ($program && $company_program) {
                if ($company_program->allowance == 0)
                    $usage = "--";
             }

            $checkins = DB::select("select name, timestamp,
                1 as type from checkin_history
                inner join locations on locations.id = checkin_history.location_id
                where  user_id = $memberId and timestamp >= '$start'
                union select name, timestamp,
                2 as type from activity where client_id NOT IN (0) and user_id = $memberId and timestamp >= '$start'");

            $checkins = collect($checkins)
            ->map(function($item) {
                return [
                    'locationName' => $item->name ?? '-',
                    'date'         => Carbon::parse($item->timestamp)->format('Y-m-d'),
                    'type'         => $item->type,
                ];
            })
            ->groupBy('date')
            ->toArray();

            if ($member) {
                $wellness = MemberWellnessModel::getWellness($member);

                $member->attachMemberDevices();
            }
        } else {
            $member = null;
        }

        return [
            'start'    => $start,
            'member'   => (object) $member,
            'checkins' => $checkins,
            'wellness' => $wellness,
            'usage'    => $usage,
        ];
    }

}
