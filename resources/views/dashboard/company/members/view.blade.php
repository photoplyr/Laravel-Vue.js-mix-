@extends('layouts.dashboard')

@section('content')
<div class="memberDashboard @if(auth()->user()->isWellness()) wellnessMember @endif" id="memberDashboard" ref="container">
    @if(auth()->user()->isWellness())
    <div class="hero">Welcome {{ $member->fname }}</div>
    <div class="mx-1">
    @endif
    <div class="memberDashboard__left">
        <div class="memberDashboard__avatar" style="background-image: url('{{ $member->photo ? $member->photo : 'https://d2x5ku95bkycr3.cloudfront.net/App_Themes/Common/images/profile/0_200.png' }}')"></div>
        @if (auth()->user()->isWellness())
        <div class="memberDashboard__section">
            <ul>
                <li>
                    Total Activity Score<span><span class="new badge blue darken-3" data-badge-caption="">{{ $corporate['activity_score'] }}</span><i class="material-icons right grey-text text-lighten-1">radio_button_checked</i></span>
                </li>
                <li>
                    Total Badge<span><span class="new badge light-blue" data-badge-caption="">{{ $corporate['badge'] }}</span><i class="material-icons right grey-text text-lighten-1">local_florist</i></span>
                </li>
                <li>
                    Total Trophy<span><span class="new badge green" data-badge-caption="">{{ $corporate['trophy'] }}</span><i class="material-icons right grey-text text-lighten-1">star</i></span>
                </li>
                <li>
                    Last seen<span><span class="new badge light-blue" data-badge-caption="">{{ $corporate['last_seen'] }}</span></span>
                </li>
                <li>
                    Last week fat burn ranking<span><span class="new badge yellow darken-3" data-badge-caption="">{{ $corporate['activity_calories'] }}</span></span>
                </li>
                <li>
                    Last month overall ranking<span><span class="new badge grey" data-badge-caption="">{{ $corporate['overall'] }}</span></span>
                </li>
            </ul>
        </div>
        <div class="memberDashboard__section memberDashboard__level">
            <div class="wellnessDashboard__title">WELLNESS LEVEL</div>
            <div class="wellnessLevel">
                @for ($i=0; $i<$corporate['badge']; $i++)
                    @if($corporate['badge'] > 7)
                        @if($i > $corporate['badge'] - 8)
                            <img src="{{asset('images/concierge/level'.$i.'.png')}}"/>
                        @else
                        @endif
                    @else
                        <img src="{{asset('images/concierge/level'.$i.'.png')}}"/>
                    @endif
                @endfor
            </div>
        </div>
        @else
        <div class="memberDashboard__section memberDashboard__basic">
            <div class="memberDashboard__title">Basic data</div>
            <ul>
                <li>
                    <span><b>Member Since</b></span>{{ $member->created_date }}
                </li>

                <li>
                    <span><b>Birthday</b></span>{{ $member->birthday_date }}
                </li>
            </ul>
        </div>

        @if (!auth()->user()->isMember() && $member->program)
        <div class="memberDashboard__section memberDashboard__basic">
            <div class="memberDashboard__title">Insurance data</div>
            <ul>
                <li>
                    <span><b>Program</b></span>{{ $member->program->name }}
                </li>

                <li>
                    <span><b>Account</b></span>{{ $member->member_program ? $member->member_program->membership: 'None' }}
                </li>
            </ul>
        </div>

        <div class="memberDashboard__section memberDashboard__basic">
            <div class="memberDashboard__title">Company data</div>
            <ul>
                <li>
                    <span><b>Name</b></span>{{ $member->company? $member->company->name: 'None' }}
                </li>
            </ul>
        </div>
        @endif
        @endif
    </div>
    <div class="memberDashboard__right">
        <div class="memberDashboard__tabs">
            <span data-target="memberDashboard__checkins" :class="{'active': tab == 'calendar'}" @click="switchTab('calendar')">Calendar</span>
            <span data-target="memberDashboard__wellness" :class="{'active': tab == 'wellness'}" @click="switchTab('wellness')">Wellness</span>

            @if (auth()->user()->isWellness() || auth()->user()->isInsurance())
             <span data-target="memberDashboard__challenges" :class="{'active': tab == 'challenges'}" @click="switchTab('challenges')">Challenges</span>
            @endif
           <!--  <span data-target="memberDashboard__devices"  :class="{'active': tab == 'rewards'}"  @click="switchTab('rewards')">Rewards</span>
 <span data-target="memberDashboard__devices"  :class="{'active': tab == 'communicattions'}"  @click="switchTab('communicattions')">Communications</span>
                   -->

            <span data-target="memberDashboard__devices"  :class="{'active': tab == 'devices'}"  @click="switchTab('devices')">Connected</span>

   <span data-target="memberDashboard__articles"  :class="{'active': tab == 'articles'}"  @click="switchTab('articles')">Articles</span>

            <!-- @if (auth()->user()->isAdmin() && !auth()->user()->isWellness()) -->
            <div align="right" style="padding-right: 16px; padding-top: 17px">
                <a href="{{ route($isEnterprise ? 'enterprise.members.edit' : 'club.members.edit', ['memberId' => $member->id]) }}">Manage</a>
            </div>
            <!-- @endif -->
        </div>
        @if(!auth()->user()->isWellness())
        <div class="memberDashboard__user">
            @if (!auth()->user()->isMember())
            <button type="button" class="right waves-effect waves-light blue btn btn-primary mainColorBackground" @click="openCheckinModal">Checkin</button>
            @endif

            <span>{{ $member->displayName }}</span>
            <!-- <i>{{ $member->membership }}</i> -->
            @if ($member->eligibility_status)
            <b class="{{ $member->eligible_color }}">{{ $member->eligibility_status }}</b>
            @endif
        </div>
        @endif

        @include('dashboard.company.members.dashboard.calendar')
        @include('dashboard.company.members.dashboard.wellness')
        @include('dashboard.company.members.dashboard.devices')
        @include('dashboard.company.members.dashboard.challenges')
         @include('dashboard.company.members.dashboard.articles')
    </div>
    @if(auth()->user()->isWellness())
    </div>
    @endif
    <div class="modal modal-small" ref="checkinDateModal">
        <div class="modal-content">
            <h5 class="center-align">Checkin Member</h5>
            <div class="row margin-0">
                <div class="col s12 input-field">
                    <input type="text" class="datepicker" ref="checkinDateDatepicker">
                    <label for="email">Pick a date</label>
                </div>
            </div>
        </div>
        <div class="modal-footer modal-footer-styled">
            <div class="padding-0-20-20 overflow-hidden">
                <button type="button" class="margin-0 left waves-effect waves-dark btn-flat modal-close">Cancel</button>
                <button type="button" class="margin-0 right waves-effect waves-light blue btn btn-primary mainColorBackground" @click="checkinMember">Confirm</button>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
</div>
@endsection

@push('js')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
{{-- <script src="{{ asset('assets/js/Chart.bundle.min.js') }}"></script> --}}
<script>
    window.Laravel.member = {!! json_encode([
        'id'       => $member->id,
        'start'    => $start,
        'checkins' => $checkins,
        'wellness' => $wellness,
        'devices'  => $member->devices,
        'myData'   => $activity,
    ]) !!};
    window.Laravel.isEnterprise = {!! $isEnterprise ? 1 : 0 !!};
    window.Laravel.challengeUrl = "{!! route('member.dasbhoard.challenges') !!}";
    window.Laravel.memberUrl = "{!! route('member.dasbhoard.getMembers') !!}";
    window.Laravel.setMemberUrl = "{!! route('member.dasbhoard.setMembers') !!}";
</script>
<script src="{{ asset('js/pages/memberDashboard.js') }}?v=1.1"></script>
@endpush
