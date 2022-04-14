<?php

namespace App\Http\Controllers;

use App\Services\Integrations\Strava;
use App\Services\Integrations\Fitbit;
use App\Services\Integrations\MyWellness;
use App\Models\Integration\IntegrationCredential;
use App\Models\Users\MemberDashboardModel;
use DB;
use App\Models\Users\User;
use Carbon\Carbon;
use App\Models\Challenge;
use App\Models\Partner;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $member = MemberDashboardModel::getDashboard(auth()->user()->id);

        $partners = Partner::where('is_active', 1)->orderBy('priority', 'asc')->get();

        return view('dashboard.company.members.view', [
            'member'       => $member['member'],
            'checkins'     => $member['checkins'],
            'start'        => $member['start'],
            'wellness'     => $member['wellness'],
            'usage'        => $member['usage'],
            'corporate'    => $member['corporate'],
            'activity'     => $member['activity'],
            'partners'     => $partners,
            'isEnterprise' => true,
        ]);
    }

    /**
     * Redirect to MyWellness oauth page
     * @param int $memberId
     * @return RedirectResponse
     */
    public function redirectToMyWellness($memberId)
    {
        $myWellness = new MyWellness;

        session()->put('mywellnessOauthMemberId', $memberId);

        return redirect($myWellness->getOauthLink());
    }

    /**
     * Save member MyWellness Token
     * @return RedirectResponse
     */
    public function saveMemberMyWellnessToken()
    {
        $code     = request()->get('code');
        $memberId = session()->get('mywellnessOauthMemberId');

        if ($code) {
            $myWellness = new MyWellness;

            $token = $myWellness->getUserAccessToken($code, $memberId);
        } else {
            return redirect(route('connect.account.mywellness.error'));
        }

        if (session()->has('mywellnessConnectFromApplication')) {
            session()->flush('mywellnessConnectFromApplication');

            return redirect(route('connect.account.mywellness.success'));
        }

        return redirect(route('club.members.view', $memberId));
    }

    /**
     * Revoke member MyWellness Token
     * @param int $memberId
     * @return RedirectResponse
     */
    public function revokeMyWellnessAccess($memberId)
    {
        $token = IntegrationCredential::where('user_id', $memberId)
                                      ->where('provider', 'mywellness')
                                      ->first();

        if ($token) {
            $token->revoke();
        }

        return redirect(route('club.members.view', $memberId));
    }

    /**
     * Redirect to fitbit oauth page
     * @param int $memberId
     * @return RedirectResponse
     */
    public function redirectToFitbit($memberId)
    {
        $fitbit = new Fitbit;

        session()->put('fitbitOauthMemberId', $memberId);

        return redirect($fitbit->getOauthLink(route('member.device.fitbit.save')));
    }

    /**
     * Save member Fitbit Token
     * @return RedirectResponse
     */
    public function saveMemberFitbitToken()
    {
        $code     = request()->get('code');
        $state    = request()->get('state');
        $memberId = session()->get('fitbitOauthMemberId');

        if ($state) {
            $fitbit = new Fitbit;

            $token = $fitbit->getUserAccessToken($code, $memberId);
        }

        if (session()->has('fitbitConnectFromApplication')) {
            session()->flush('fitbitConnectFromApplication');

            return redirect(route('connect.account.fitbit.success'));
        }

        return redirect(route('club.members.view', $memberId));
    }

    /**
     * Revoke member Fitbit Token
     * @param int $memberId
     * @return RedirectResponse
     */
    public function revokeFitbitAccess($memberId)
    {
        $token = IntegrationCredential::where('user_id', $memberId)
                                      ->where('provider', 'fitbit')
                                      ->first();
        if ($token) {
            $token->revoke();
        }

        return redirect(route('club.members.view', $memberId));
    }

    /**
     * Redirect to Strava oauth page
     * @param int $memberId
     * @return RedirectResponse
     */
    public function redirectToStrava($memberId)
    {
        $strava = new Strava;

        session()->put('stravaOauthMemberId', $memberId);

        return redirect($strava->getOauthLink());
    }

    /**
     * Save member Strava Token
     * @return RedirectResponse
     */
    public function saveMemberStravaToken()
    {
        $code     = request()->get('code');
        $memberId = session()->get('stravaOauthMemberId');

        if ($code) {
            $strava = new Strava;

            $token = $strava->getUserAccessToken($code, $memberId);
        }

        if (session()->has('stravaConnectFromApplication')) {
            session()->flush('stravaConnectFromApplication');

            return redirect(route('connect.account.strava.success'));
        }

        return redirect(route('club.members.view', $memberId));
    }

    /**
     * Revoke member Strava Token
     * @param int $memberId
     * @return RedirectResponse
     */
    public function revokeStravaAccess($memberId)
    {
        $token = IntegrationCredential::where('user_id', $memberId)
                                      ->where('provider', 'strava')
                                      ->first();

        if ($token) {
            $token->revoke();
        }

        return redirect(route('club.members.view', $memberId));
    }

    public function getChallenges(Request $request)
    {
        $today = date('Y-m-d');
        $curMonth = date('m');
        $curYear = date('Y');

        $status = empty($request->input('status')) ? 'Ongoing' : $request->input('status');
        $selectedMonth = empty($request->input('selectedMonth')) ? $curMonth : $request->input('selectedMonth');

        if($curMonth != $selectedMonth)
        {

            $firstDay = date("Y-m-01", strtotime("$curYear-$selectedMonth-01"));
            $lastDay = date("Y-m-t", strtotime("$curYear-$selectedMonth-01"));

            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>=', $firstDay )->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $lastDay)->where('end_date', '>', $lastDay)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '>=', $firstDay)->where('end_date', '<=', $lastDay)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->with('users')->get();
            }
        } else {
            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>', $today )->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '<=', $today)->with('users')->where('active', 1)->get();
            } else {
                $challenges = Challenge::where('active', 0)->with('users')->get();
            }
        }

        $challengeMembers = DB::table('challenge_members')->selectRaw("sum(points) as points, user_id")->whereIn('challenge_id', $challenges->pluck('id'))->groupBy('user_id')->orderByRaw('SUM(points) DESC')->limit(20)->get();
        $members = array();
        foreach ($challengeMembers as $challengeMember) {
            $user = User::where('id', $challengeMember->user_id)->first();
            if(!is_null($user)) {
                $members[] = array (
                    'user' => $user,
                    'points' => $challengeMember->points
                );
            }
        }

        $member = MemberDashboardModel::getDashboard($request->input('memberId'));
        $data['challenges'] = $challenges;
        $data['members'] = $members;
        $data['activity'] = $member['activity'];
        $data['success'] = true;
        return response()->json($data);
    }

    public function getMembers(Request $request) {
        $challenge_id = $request->input('challenge_id');

        $challenge = Challenge::where('id', $challenge_id)->first();
        if(!empty($challenge))
        {
            $challengeMembers = DB::table('challenge_members')->selectRaw("sum(points) as points, user_id")->where('challenge_id', $challenge->id)->groupBy('user_id')->orderByRaw('SUM(points) DESC')->limit(20)->get();
            $members = array();
            foreach ($challengeMembers as $challengeMember) {
                $user = User::where('id', $challengeMember->user_id)->first();
                if(!is_null($user)) {
                    $members[] = array (
                        'user' => $user,
                        'points' => $challengeMember->points
                    );
                }
            }
            $data = array (
                'success' => true,
                'members'   => $members
            );

        } else {
            $data = array(
                'success' => false
            );
        }

        return response()->json($data);

    }

    public function setMembers(Request $request) {
        $today = date('Y-m-d');
        $curMonth = date('m');
        $curYear = date('Y');
        $members = array();

        $status = empty($request->input('status')) ? 'Ongoing' : $request->input('status');
        $selectedMonth = empty($request->input('selectedMonth')) ? $curMonth : $request->input('selectedMonth');
        $challenge_id = $request->input('challenge_id');
        $member_id = $request->input('member_id');
        $challenge = Challenge::where('id', $challenge_id)->first();

        if(!empty($challenge)) {
            if($status == 'Upcoming') {
                $challenge->users()->attach($member_id, ['entry_date' => date('Y-m-d', strtotime($challenge->start_date))]);
            } elseif ($status == 'Ongoing') {
                $challenge->users()->attach($member_id, ['entry_date' => date('Y-m-d')]);
            }
            $challengeMembers = DB::table('challenge_members')->selectRaw("sum(points) as points, user_id")->where('challenge_id', $challenge->id)->groupBy('user_id')->orderByRaw('SUM(points) DESC')->limit(20)->get();

            foreach ($challengeMembers as $challengeMember) {
                $user = User::where('id', $challengeMember->user_id)->first();
                if(!is_null($user)) {
                    $members[] = array (
                        'user' => $user,
                        'points' => $challengeMember->points
                    );
                }
            }

        }

        if($curMonth != $selectedMonth)
        {

            $firstDay = date("Y-m-01", strtotime("$curYear-$selectedMonth-01"));
            $lastDay = date("Y-m-t", strtotime("$curYear-$selectedMonth-01"));

            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>=', $firstDay )->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $lastDay)->where('end_date', '>', $lastDay)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '>=', $firstDay)->where('end_date', '<=', $lastDay)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->with('users')->get();
            }
        } else {
            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>', $today )->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '<=', $today)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->with('users')->get();
            }
        }

        $data['challenges'] = $challenges;
        $data['members'] = $members;
        $data['success'] = true;
        return response()->json($data);
    }
}
