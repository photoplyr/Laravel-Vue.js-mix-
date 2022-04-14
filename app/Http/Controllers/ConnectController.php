<?php

namespace App\Http\Controllers;

use App\Services\Integrations\Fitbit;
use App\Services\Integrations\MyWellness;

class ConnectController extends Controller
{
    /**
     * Connect MyWellness via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mywellness()
    {
        $memberId = request()->get('connectTo');

        if (!$memberId) {
            return view('dashboard.connectDevice', ['message' => 'The \'connectTo\' param is required.']);
        }

        $myWellness = new MyWellness;

        session()->put('mywellnessOauthMemberId', $memberId);
        session()->put('mywellnessConnectFromApplication', true);

        return redirect($myWellness->getOauthLink());
    }

    /**
     * Connect MyWellness via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mywellnessSuccess()
    {
        return view('dashboard.connectDevice', ['message' => 'Your MyWellness account was successfully connected.']);
    }

    /**
     * Connect MyWellness via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mywellnessError()
    {
        return view('dashboard.connectDevice', ['message' => 'Oops... Something went wrong. Account wasn\'t connected.']);
    }

    /**
     * Connect Fitbit via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fitbit()
    {
        $memberId = request()->get('connectTo');

        if (!$memberId) {
            return view('dashboard.connectDevice', ['message' => 'The \'connectTo\' param is required.']);
        }

        $fitbit = new Fitbit;

        session()->put('fitbitOauthMemberId', $memberId);
        session()->put('fitbitConnectFromApplication', true);

        return redirect($fitbit->getOauthLink(route('member.device.fitbit.save')));
    }

    /**
     * Connect Fitbit via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fitbitSuccess()
    {
        return view('dashboard.connectDevice', ['message' => 'Your Fitbit account was successfully connected.']);
    }

    /**
     * Connect Strava via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function strava()
    {
        $memberId = request()->get('connectTo');

        if (!$memberId) {
            return view('dashboard.connectDevice', ['message' => 'The \'connectTo\' param is required.']);
        }

        $strava = new Strava;

        session()->put('stravaOauthMemberId', $memberId);
        session()->put('stravaConnectFromApplication', true);

        return redirect($strava->getOauthLink(route('member.device.strava.save')));
    }

    /**
     * Connect Strava via Application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stravaSuccess()
    {
        return view('dashboard.connectDevice', ['message' => 'Your Strava account was successfully connected.']);
    }
}
