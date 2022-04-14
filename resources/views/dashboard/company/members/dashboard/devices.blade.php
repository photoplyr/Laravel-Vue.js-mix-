<div class="memberDashboard__tab" id="memberDashboard__devices" v-if="tab == 'devices'">
    <div class="row">
        <div class="col s12">
            <div class="card-container">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <img class="device-logo" src="{{ asset('images/partners/technogym.png') }}">
                            <h4 class="card-title">MyWellness Account</h4>
                        </div>
                        <div class="card-body">
                            <p class="device-description">
                                Technogym, the leading designer of gym equipment & fitness solutions trusted by thousands of gyms, fitness centres and home owners around the world
                            </p>
                        </div>
                        <div class="card-footer">
                            <a v-if="!devices.mywellness" class="waves-effect waves-light " href="{{ route('member.device.mywellness.oauth', $member->id) }}">Connect <i class="material-icons">arrow_forward</i></a>
                            <a v-else class="waves-effect waves-light " href="{{ route('member.device.mywellness.revoke', $member->id) }}">Revoke Access <i class="material-icons">arrow_forward</i></a>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <img class="device-logo" src="{{ asset('images/partners/strava.logo.png') }}">
                            <h4 class="card-title">Strava Account</h4>
                        </div>
                        <div class="card-body">
                            <p class="device-description">
                                Designed by athletes, for athletes, Strava’s mobile app and website connect millions of runners and cyclists through the sports they love.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a v-if="!devices.strava" class="waves-effect waves-light" href="{{ route('member.device.strava.oauth', $member->id) }}">Connect <i class="material-icons">arrow_forward</i></a>
                            <a v-else class="waves-effect waves-light " href="{{ route('member.device.strava.revoke', $member->id) }}">Revoke Access <i class="material-icons">arrow_forward</i></a>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <img class="device-logo" src="{{ asset('images/partners/fitbit-logo.png') }}">
                            <h4 class="card-title">Fitbit Device</h4>
                        </div>
                        <div class="card-body">
                            <p class="device-description">
                                Find your fit with Fitbit's family of fitness products that help you stay motivated and improve your health by tracking your activity, exercise, food, weight and sleep.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a v-if="!devices.fitbit" class="waves-effect waves-light " href="{{ route('member.device.fitbit.oauth', $member->id) }}">Connect <i class="material-icons">arrow_forward</i></a>
                            <a v-else class="waves-effect waves-light " href="{{ route('member.device.fitbit.revoke', $member->id) }}">Revoke Access <i class="material-icons">arrow_forward</i></a>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <img class="device-logo" src="{{ asset('images/partners/echelon.png') }}">
                            <h4 class="card-title">Echelon</h4>
                        </div>
                        <div class="card-body">
                            <p class="device-description">
                                Community to inspire you. Classes to challenge you. Instructors to motivate you. Experience connected home fitness unlike ever before with Echelon.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a v-if="!devices.echelon" class="waves-effect waves-light " href="https://echelonpartner.com/affiliate/concierge">Connect <i class="material-icons">arrow_forward</i></a>
                            <a v-else class="waves-effect waves-light " href="https://echelonpartner.com/affiliate/concierge">Revoke Access <i class="material-icons">arrow_forward</i></a>
                        </div>
                    </div>
                </div>
                @forelse($partners as $partner)
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <img class="device-logo" src="{{ $partner->icon }}" alt="{{ $partner->name }}">
                            <h4 class="card-title">{{ $partner->name }}</h4>
                        </div>
                        <div class="card-body">
                            <p class="device-description">{{ $partner->description }}</p>
                        </div>
                        <div class="card-footer">
                            <a class="waves-effect waves-light" href="{{ $partner->link }}" target="_blank">Visit Website <i class="material-icons">arrow_forward</i></a>
                        </div>
                    </div>
                </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
    {{-- <div class="col s6 input-field">
        <input id="memberDashboard__device--gymfarm_id" value="{{ $member->gymfarm_id }}" type="text">
        <label for="memberDashboard__device--gymfarm_id">{{ trans('concierge.device.my_concierge_health') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--humana" value="{{ $member->humana_id }}" type="text">
        <label for="memberDashboard__device--humana">{{ trans('concierge.device.humana') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--fod_id" value="{{ $member->fod_id }}" type="text">
        <label for="memberDashboard__device--fod_id">{{ trans('concierge.device.fitness_on_demand') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--echelon_id" value="{{ $member->echelon_id }}" type="text">
        <label for="memberDashboard__device--echelon_id">{{ trans('concierge.device.echelon') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--peloton_id" value="{{ $member->peloton_id }}" type="text">
        <label for="memberDashboard__device--peloton_id">{{ trans('concierge.device.peloton') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--memnum" value="{{ $member->memnum }}" type="text">
        <label for="memberDashboard__device--memnum">{{ trans('concierge.device.gym_membership') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--heart_band_id" value="{{ $member->nike_user }}" type="text">
        <label for="memberDashboard__device--heart_band_id">{{ trans('concierge.device.heart_rate') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--ash_id" value="{{ $member->ash_id }}" type="text">
        <label for="memberDashboard__device--ash_id">{{ trans('concierge.device.american_specialy') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--optum_id" value="{{ $member->optum_id }}" type="text">
        <label for="memberDashboard__device--optum_id">{{ trans('concierge.device.optum') }}</label>
    </div>

    <div class="col s6 input-field">
        <input id="memberDashboard__device--vsp_id" value="{{ $member->vsp_id }}" type="text">
        <label for="memberDashboard__device--vsp_id">{{ trans('concierge.device.vsp') }}</label>
    </div> --}}
</div>
