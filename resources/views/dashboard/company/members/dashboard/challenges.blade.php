<div class="memberDashboard__tab" id="memberDashboard__challenges" ref="container" v-if="tab == 'challenges'">
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
                <div class="col">
                    <ul class="tabs tabs-transparent">
                        <li class="tab" v-for="tab in tabs"><a href="#" :class='{"active": (tab == curView)}' @click="selectTab(tab)">@{{ tab }}</a> </li>
                    </ul>
                </div>
            </div>
            <div class="row card-container">
                <template v-if="challenges.length > 0">
                    <div class="scroll-content">
                        <div class="card" v-for="(challenge) in challenges" :class='{"active": (challenge.id == curCard)}'>
                            <div class="card-content" @click="getMembers(challenge.id)" :style="{backgroundImage:`linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.4) 59%, rgba(0,0,0,0) 100%), url(${challenge.photo})`}">
                                <span class="date-panel">@{{ viewDays(challenge) }}</span>
                                <span class="card-title">@{{ challenge.title }}</span>

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
                            <button class="btn btn-join hover-view" v-if="curView != 'Completed' && checkJoin(challenge.users) == 0 && challenge.active == 1 " @click="setMembers(challenge.id)">Join</button>
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
            <div class="row">
                <div class="col s12">
                    @if(auth()->user()->isWellness())
                    <h2>My Activity</h2>
                    @else
                    <h2>{{ $member->displayName }}'s Activity</h2>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col s8">Points on <span>@{{ today }}</span></div>
                <div class="col s4 right-align"><span>@{{ myData.total_points }} Pts</span></div>
            </div>
            <div class="row">
                <div class="col s12 activity-block-box">
                    <div class="activity-block-panel">
                        <div class="activity-block blue lighten-2">
                            <span class="activity-block-header"><i class="small material-icons">av_timer</i> STEPS</span>
                            <p><span>@{{ myData.steps.avg }}</span></p>
                            <p>@{{ myData.steps.score }} pts</p>

                        </div>
                        <div class="activity-block blue-grey lighten-2">
                            <span class="activity-block-header"><i class="small material-icons">offline_bolt</i> CALORIES</span>
                            <p><span>@{{ myData.calories.avg }}</span></p>
                            <p>@{{ myData.calories.score }} pts</p>

                        </div>
                        <div class="activity-block light-green lighten-1">
                            <span class="activity-block-header"><i class="small material-icons">view_day</i> DISTANCE</span>
                            <p><span>@{{ myData.distance.avg }}</span></p>
                            <p>@{{ myData.distance.score }} pts</p>
                        </div>
                        <div class="activity-block blue darken-1">
                            <span class="activity-block-header"><i class="small material-icons">accessibility</i> ACTIVITIES</span>
                            <p><span>@{{ myData.activity.avg }}</span></p>
                            <p>@{{ myData.activity.score }} pts</p>
                        </div>
                        <div class="activity-block teal lighten-1">
                            <span class="activity-block-header"><i class="small material-icons">directions_bike</i> WATTS</span>
                            <p><span>@{{ myData.watts.avg }}</span></p>
                            <p>@{{ myData.watts.score }} pts</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col s12">
                    <div id="activityChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--@endsection

@push('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    window.Laravel.challengesData = {!! json_encode([
    'challenges'    => $challenges,
    'members'       => $members,
    'myData'        => $myData,
]) !!};
</script>
<script src="{{ asset('js/pages/corporate/challenge.js') }}?v=1.1"></script>

@endpush--}}
