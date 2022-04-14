@extends('layouts.dashboard')

@section('content')
    <div class="content" id="memberDashboard__challenges" ref="container">
        <div class="pageLoading" v-if="isLoad">
            <div class="preloader-wrapper big active">
                <div class="spinner-layer spinner-blue-only">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                        <div class="circle"></div>
                    </div><div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="challengeDashboard__left">
            <div class="row">
                <div class="col s6">
                    <h2 class="page-title">
                        Challenges
                    </h2>
                </div>
                <div class="col s6">
                    <div class="challenge-calendar">
                        <span>@{{ new Date().getFullYear() }}</span>
                        <div class="month-list">
                            <span class="challenge-month" v-for="(month, index) in months" v-if="index < 6" :class='{"active": index == selectedMonth}' @click="selectMonth(index)">@{{ month }}</span>
                            <span class="challenge-month" @click="showMoreMonths()" ref="stylizedMoreMonths">
                            <i class="material-icons">event_note</i>
                            <div class="challenge-month-panel" v-show="showMonths">
                                <span class="challenge-month" v-for="(month, index) in months" v-if="index > 5" :class='{"active": index == selectedMonth}' @click="selectMonth(index)">@{{ month }}</span>
                            </div>
                        </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col s6">
                        <ul class="tabs tabs-transparent">
                            <li class="tab" v-for="tab in tabs"><a href="#" :class='{"active": (tab == curView)}' @click="selectTab(tab)">@{{ tab }}</a> </li>
                        </ul>
                    </div>
                    <div class="col s6">
                        <a href="{{ route('corporate.challenges.create') }}" class="right new-challenge">Add Challenge</a>
                    </div>
                </div>
                <div class="row card-container">
                    <template v-if="challenges.length > 0">
                        <div class="scroll-content">
                            <div class="card" v-for="(challenge) in challenges" :class='{"active": (challenge.id == curCard)}'>
                                <div class="card-content" @click="getMembers(challenge.id)" :style="{backgroundImage:`linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.4) 59%, rgba(0,0,0,0) 100%), url(${challenge.photo})`}">
                                    <div>
                                        <span class="date-panel">@{{ viewDays(challenge) }}</span>

                                    </div>
                                    <div>
                                        <span class="card-title">@{{ challenge.title }}</span>
                                    </div>
                                    <div v-if="challenge.type_id == 1 && challenge.steps > 0">
                                        <span class="card-title">@{{ challenge.steps }} step challenge</span>
                                    </div>
                                    <div v-if="challenge.type_id == 1 && challenge.distance > 0">
                                        <span class="card-title">@{{ challenge.distance }} mile challenge</span>
                                    </div>

                                    <div v-if="challenge.type_id == 2">
                                        <span class="card-title">@{{ challenge.distance }} mile challenge</span>
                                    </div>

                                    <div v-if="challenge.type_id == 3 && challenge.duration > 0">
                                        <span class="card-title">@{{ Math.round(challenge.duration/60) }} hours challenge</span>
                                    </div>

                                    <div v-if="challenge.type_id == 4">
                                        <span class="card-title">@{{ Math.round(challenge.calories) }} calorie challenge</span>
                                    </div>
                                    <p class="challenge-description">@{{ challenge.desc }}</p>
                                    <div class="challenge-participants">
                                        <p class="participants-title">Participants</p>
                                        <div class="participants-photos">
                                            <div class="participant-photo" v-for="(member, index) in challenge.users">
                                                <img :src="member.photo" v-if="index < 3">
                                                <span v-else-if="index == 3">+@{{ challenge.users.length - 3 }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <span class="edit-challenge hover-view"><a :href="'{{ route('corporate.challenges.edit', '') }}/'+challenge.id">Edit</a></span>
                                <span class="delete-challenge hover-view" v-if="challenge.users.length == 0"><a class="waves-effect red accent-1 btn-small" @click="removeChallenge(challenge.id)">Delete</a></span>
                            </div>
                        </div>
                    </template>
                    <template v-if="challenges.length == 0">
                        <center><img src = "/images/concierge/no-location.13c24c22.png" width=200px><br><div style = "font-size: 20px;">No Challenges</div></center>
                    </template>
                </div>
            </div>
        </div>
        <div class="challengeDashboard__right">
            <div class="content">
                <div class="row">
                    <div class="col s12">
                        <h2 class="left">Leader Board</h2>
                        <a href="#" class="right btn-all-view" @click="viewAllMember()">View All</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <ul class="challenge-member-list" :class='{"view-all": (viewAll == true)}'>
                            <li class="challenge-member" v-for="(member, index) in members" v-if="index < maxViews"><div><span class="trophy-icon" v-if="index == 0"><img src="{{ asset('images/lb-1.png') }}" width="25px" height="30px"></span><span class="trophy-icon" v-else-if="index == 1"><img src="{{ asset('images/lb-2.png') }}" width="25px" height="30px"></span><span class="trophy-icon" v-else-if="index == 2"><img src="{{ asset('images/lb-3.png') }}" width="25px" height="30px"></span><span v-else="index > 0">@{{ index+1 }}</span><img :src="member.user.photo" width="40px" height="40px" class="member-photo"><span>@{{ member.user.fname + ' ' + member.user.lname}}</span></div><span>@{{ Math.round(member.points) }} Pts</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script>
    window.Laravel.member = {!! json_encode([
        'id'       => $member->id,
        'challenges' => $challenges,
        'members'    => $members
    ]) !!};
    window.Laravel.isEnterprise = {!! $isEnterprise ? 1 : 0 !!};
    window.Laravel.challengeUrl = "{!! route('corporate.getChallenges') !!}";
    window.Laravel.memberUrl = "{!! route('corporate.challenge.getMembers') !!}";
    window.Laravel.setMemberUrl = "{!! route('corporate.challenge.setMembers') !!}";
    window.Laravel.removeUrl = "{!! route('corporate.challenges.remove') !!}";
</script>
<script src="{{ asset('js/pages/corporate/challenge.js') }}?v=1.1"></script>
@endpush
