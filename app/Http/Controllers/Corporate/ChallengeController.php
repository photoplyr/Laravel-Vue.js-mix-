<?php

namespace App\Http\Controllers\Corporate;

use DB;
use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Users\Roles;
use App\Models\Users\MemberDashboardModel;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
 /**
     * Instantiate a new ClubProgramsController instance.
     */
    public function __construct()
    {
     
    }

    /**
     * Show programs list
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $member = MemberDashboardModel::getDashboard(auth()->user()->id);

        $today = date('Y-m-d');

        $company_id = auth()->user()->location->company_id;

        $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();

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

        return view('dashboard.corporate.challenge.index', [
            'member'       => $member['member'],
            'challenges'   => $challenges,
            'members'      => $members,
            'isEnterprise' => true,
        ]);
    }

    /**
     * Show create challenge page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $types = ChallengeType::all();
        $member = MemberDashboardModel::getDashboard(auth()->user()->id);

        return view('dashboard.corporate.challenge.manage', [
            'types'        => $types,
            'member'       => $member['member'],
            'isEnterprise' => true,
        ]);
    }

    /**
     * Show challenges edit page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($challengeId)
    {
        $challenge = Challenge::whereId($challengeId)->with('users')->first();
        $types = ChallengeType::all();
        $member = MemberDashboardModel::getDashboard(auth()->user()->id);

        if (!$challenge) {
            return abort(404);
        }

        return view('dashboard.corporate.challenge.manage', [
            'challenge'           => $challenge,
            'types'        => $types,
            'member'       => $member['member'],
            'isEnterprise' => true,
        ]);
    }

    /**
     * Save challenge
     *
     * @return \Illuminate\Contracts\Support\
     */
    public function save()
    {
        $challengeId = request()->has('id') ? request()->get('id') : null;
        $company_id = auth()->user()->location->company_id;

        if (!$challengeId) {
            $challenge = new Challenge();
        } else {
            $challenge = Challenge::find($challengeId);
            if (!$challenge) {
                return abort(404);
            }
        }

        $challenge->title = request()->get('title');
        $challenge->subtitle = request()->get('subtitle');
        $challenge->distance = request()->get('distance');
        $challenge->start_date = request()->get('start_date');
        $challenge->end_date = request()->get('end_date');
        $challenge->photo = request()->get('photo');
        $challenge->type_id = request()->get('type_id');
        $challenge->desc = request()->get('desc');
        $challenge->price = request()->get('price');
        $challenge->steps = request()->get('steps');
        $challenge->duration = request()->get('duration');
        $challenge->calories = request()->get('calories');
        $challenge->medal_url = request()->get('medal_url');
        $challenge->active = request()->has('active') ? 1 : 0;
        $challenge->company_id = $company_id;
        $challenge->save();

        return redirect(route('corporate.challenge'))->with('successMessage', 'Your updates have been saved');
    }

    public function getChallenges(Request $request)
    {
        $today = date('Y-m-d');
        $curMonth = date('m');
        $curYear = date('Y');
        $company_id = auth()->user()->location->company_id;

        $status = empty($request->input('status')) ? 'Ongoing' : $request->input('status');
        $selectedMonth = empty($request->input('selectedMonth')) ? $curMonth : $request->input('selectedMonth');

        if($curMonth != $selectedMonth)
        {

            $firstDay = date("Y-m-01", strtotime("$curYear-$selectedMonth-01"));
            $lastDay = date("Y-m-t", strtotime("$curYear-$selectedMonth-01"));

            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>=', $firstDay )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $lastDay)->where('end_date', '>', $lastDay)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '>=', $firstDay)->where('end_date', '<=', $lastDay)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
            }
        } else {
            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>', $today )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '<=', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
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

        $data['challenges'] = $challenges;
        $data['members'] = $members;
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
        $company_id = auth()->user()->location->company_id;

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
                $challenges = Challenge::where('start_date', '>=', $firstDay )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $lastDay)->where('company_id', '=', $company_id)->where('end_date', '>', $lastDay)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '>=', $firstDay)->where('company_id', '=', $company_id)->where('end_date', '<=', $lastDay)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
            }
        } else {
            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>', $today )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '<=', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
            }
        }

        $data['challenges'] = $challenges;
        $data['members'] = $members;
        $data['success'] = true;
        return response()->json($data);
    }

    public function remove(Request $request) {
        $today = date('Y-m-d');
        $curMonth = date('m');
        $curYear = date('Y');
        $company_id = auth()->user()->location->company_id;

        $status = empty($request->input('status')) ? 'Ongoing' : $request->input('status');
        $selectedMonth = empty($request->input('selectedMonth')) ? $curMonth : $request->input('selectedMonth');
        $challenge_id = $request->input('challenge_id');
        Challenge::where('id', $challenge_id)->delete();

        if($curMonth != $selectedMonth)
        {

            $firstDay = date("Y-m-01", strtotime("$curYear-$selectedMonth-01"));
            $lastDay = date("Y-m-t", strtotime("$curYear-$selectedMonth-01"));

            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>=', $firstDay )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $lastDay)->where('end_date', '>', $lastDay)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '>=', $firstDay)->where('end_date', '<=', $lastDay)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
            }
        } else {
            if ($status == 'Upcoming') {
                $challenges = Challenge::where('start_date', '>', $today )->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Ongoing') {
                $challenges = Challenge::where('start_date', '<=', $today)->where('end_date', '>', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } elseif ($status == 'Completed') {
                $challenges = Challenge::where('end_date', '<=', $today)->where('company_id', '=', $company_id)->where('active', 1)->with('users')->get();
            } else {
                $challenges = Challenge::where('active', 0)->where('company_id', '=', $company_id)->with('users')->get();
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

        $data['challenges'] = $challenges;
        $data['members'] = $members;
        $data['success'] = true;
        return response()->json($data);
    }

}
