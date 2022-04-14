<?php

namespace App\Http\Controllers;

use App\Services\Integrations\OuraClient;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use RuntimeException;
use DB;
use App\Models\Api;
use App\Models\Statistics\Activity;

class TvActivityController extends Controller
{
    public function index()
    {
        $startYear = 2021;
        $selectedYear  = request()->ajax() ? request()->get('year') : now()->format('Y');
        $selectedMonth = request()->ajax() ? request()->get('month') : now()->format('n');
        $selectedApi = request()->ajax() ? request()->get('company_id') : 0;

        $years     = [];
        for ($i = $startYear; $i <= intval(now()->format('Y')); ++$i) {
            $years[] = [
                'id'     => $i,
                'active' => $selectedYear == $i ? true : false,
            ];
        }

        $months = [
            [
                'id' => 0,
                'active' => $selectedMonth == 0 ? true : false,
                'title'  => 'All',
            ],
        ];
        for ($m = 1; $m <= 12; ++$m) {
            $monthName = Carbon::createFromFormat('n', $m)->format('F');

            $months[] = [
                'id'     => $m,
                'active' => $m == $selectedMonth,
                'title'  => $monthName,
            ];
        }

        $apis = Api::orderBy('name', 'ASC')
                            ->get()
                            ->map(function($item) use ($selectedApi) {
                                return [
                                    'id'     => $item->id,
                                    'title'  => $item->name,
                                    'active' => $selectedApi == $item->id,
                                ];
                            })->toArray();

        array_unshift($apis , [
            'id'     => 0,
            'title'  => 'All',
            'active' => $selectedApi == 0,
        ]);

        $whereDate = "EXTRACT(YEAR FROM activity.timestamp) = '{$selectedYear}'";
        if ($selectedMonth > 0) {
            $whereDate .= " AND EXTRACT(MONTH FROM activity.timestamp) = '{$selectedMonth}'";
        }

        /* Get Transfers */
        $transfers = Activity::select('activity.id', 'activity.timestamp', 'user.fname', 'user.lname', 'activity.name', 'activity.duration', 'api.name as program')
                                  ->join('user', 'user.id', '=', 'activity.user_id')
                                  ->join('api', 'api.id', '=', 'activity.client_id')

                                  ->where(function($query) use ($selectedApi) {
                                    if ($selectedApi > 0) $query->where('activity.client_id', $selectedApi);
                                  })
                                  ->whereRaw($whereDate)
                                  ->orderBy('timestamp', 'DESC')
                                  ->get()
                                  ->map(function($item) {
                                    $item->duration = gmdate('H:i:s', $item->duration);

                                    if (strlen($item->name) > 100) {
                                        $item->name = substr($item->name, 0, 70).'...';
                                    }

                                    return $item;
                                  });

        /* Calculate hoursByMonths data */
        $minMonths  = [];
        $monthlyMins = [];

        $start = Carbon::create($selectedYear);

        $yearStart = $start->copy()->startOfYear();
        $yearEnd   = $start->copy()->endOfYear();
        $monthlyMins = Activity::where('timestamp', '>=', $yearStart->format('Y-01-01 00:00:00'))
                                ->where('timestamp', '<=', $yearEnd->format('Y-12-31 23:59:59'))
                                ->where(function($query) use ($selectedApi) {
                                    if ($selectedApi > 0) $query->where('client_id', $selectedApi);
                                })
                                ->get()
                                ->map(function($item) {
                                    $item->month = Carbon::parse($item->timestamp)->format('F');
                                    return $item;
                                })
                                ->groupBy('month')
                                ->map(function($item) {
                                    $mins = 0;

                                    foreach ($item as $m) {
                                        $mins += $m->duration * 0.000277778;
                                    }

                                    return $mins;
                                });

        while ($yearStart->lt($yearEnd)) {
            $minMonths[] = $yearStart->format('F');

            $yearStart->addMonth();
        }

        $apiQuery = $selectedApi > 0?' and client_id = '.$selectedApi:'';
        /* Calculate dailyActivityByHour data */
        $dailyByHour = DB::select("SELECT DISTINCT
                                date_trunc('hour', \"timestamp\") AS hour, count(*)
                                OVER (PARTITION BY date_trunc('hour', \"timestamp\")) AS count
                                FROM activity
                                where timestamp > now() - interval '24 hour'
                                ".$apiQuery."
                                ORDER BY hour, count;");

        /* Calculate info counters */
        $membersCount  = 0;
        $averageAge    = 0;
        $maleMembers   = 0;
        $femaleMembers = 0;

        $members = Activity::with('user')->select('user_id')->where(function($query) use ($selectedApi) {
            if ($selectedApi > 0) $query->where('client_id', $selectedApi);
            })
            ->whereRaw($whereDate)
            ->groupBy('user_id')->get();

        $ageCount = 0;
        foreach ($members as $member) {
            $membersCount ++;
            if ($member->user && $member->user->age > 5) {
                if ($member->user->age > 5) {
                    $ageCount ++;
                    $averageAge += $member->user->age;
                }
                if ($member->user->gender == 0) $femaleMembers ++;
                else $maleMembers ++;
            }
        }

        $averageAge    = $ageCount == 0 ? 0:$averageAge / $ageCount;

        $data = [
            'companies'     => $apis,
            'transfers'     => $transfers,
            'years'         => $years,
            'months'        => $months,
            'info'          => [
                'total'       => number_format($membersCount),
                'age'         => number_format($averageAge, 2),
                'male'        => $maleMembers,
                'female'      => $femaleMembers,
            ],
            'issuers'       => [],
            'minMonths'     => $minMonths,
            'monthlyMins'   => $monthlyMins,
            'dailyByHour'   => $dailyByHour,
        ];

        $tvActivity = DB::select("select CONCAT(fname, ' checked-in at ' , name),timestamp
            from checkin_history
            inner join locations on locations.id = checkin_history.location_id
            inner join public.user on public.user.id = checkin_history.user_id
            union
            select CONCAT(fname, ' completed a workout ' , name),timestamp
            from activity
            inner join public.user on public.user.id = activity.user_id
            order by timestamp desc");
            
        return view('tvactivity',['records'=>array_slice($tvActivity, 0, 5), "data"=>$data]);
        // if (request()->ajax()) {
        //     $data['success'] = true;

        //     return response()->json($data);
        // } else {
        //     return view('tvactivity', $data, ["records"=>$tvActivity]);
        // }
    }   
}
