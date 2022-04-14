const challenges = new Vue({
    el: '#memberDashboard__challenges',

    data: {
        memberId:     window.Laravel.member.id,
        isEnterprise: window.Laravel.isEnterprise,
        isLoad:       false,

        challenges:        window.Laravel.member.challenges,
        members:           window.Laravel.member.members,
        tabs:              ['Upcoming', 'Ongoing', 'Completed', 'Disabled'],
        months:            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        today:             moment().format('D MMM, YYYY'),
        curView:           'Ongoing',
        curCard:           '',
        viewAll:           false,
        maxViews:          5,
        showMonths:        false,
        selectedMonth:     moment().format('M')-1,

    },

    methods: {
        selectTab (i) {
            let v = this
            v.curView = i
            v.curCard = ''
            v.isLoad = true
            v.maxViews = 5
            axios.post(window.Laravel.challengeUrl, {
                status:        v.curView,
                selectedMonth: v.selectedMonth + 1,
                memberId:      v.memberId,
            })
                .then(response => {
                v.isLoad = false
            if (response.data.success) {
                v.challenges = response.data.challenges
                v.members = response.data.members
            }
        })
        .catch(error => {
                v.isLoad = false
            console.log('error', error.message);
        })
        },
        getMembers(id) {
            let v = this
            v.isLoad = true
            v.curCard = id
            axios.post(window.Laravel.memberUrl, {
                challenge_id: id
            }).then(response => {
                v.isLoad = false
            if (response.data.success) {
                v.members = response.data.members
            }
        }).catch(error => {
                v.isLoad = false
            console.log('error', error.message);
        })
        },
        setMembers(id) {
            let v = this
            v.isLoad = true
            v.curCard = id

            axios.post(window.Laravel.setMemberUrl, {
                challenge_id: id,
                member_id: v.memberId,
                status:       v.curView,
                selectedMonth: v.selectedMonth  + 1,
            }).then(response => {
                v.isLoad = false
                if (response.data.success) {
                    v.members = response.data.members
                    v.challenges = response.data.challenges
                }
            }).catch(error => {
                v.isLoad = false
                console.log('error', error.message);
            })
        },
        removeChallenge(id) {
            let v = this
            v.isLoad = true
            axios.post(window.Laravel.removeUrl, {
                challenge_id: id,
                status:       v.curView,
                selectedMonth: v.selectedMonth  + 1,
            }).then(response => {
                v.isLoad = false

                if (response.data.success) {
                    v.members = response.data.members
                    v.challenges = response.data.challenges
                    v.curCard = ''
                }
            }).catch(error => {
                v.isLoad = false
                console.log('error', error.message);
            })
        },
        selectMonth(index) {
            let v = this
            v.selectedMonth = index
            v.curCard = ''
            v.isLoad = true
            v.maxViews = 5
            axios.post(window.Laravel.challengeUrl, {
                status:       v.curView,
                selectedMonth: index + 1,
                memberId:      v.memberId,
            })
                .then(response => {
                v.isLoad = false
            if (response.data.success) {
                v.challenges = response.data.challenges
                v.members = response.data.members
            }
        })
        .catch(error => {
                v.isLoad = false
            console.log('error', error.message);
        })
        },
        showMoreMonths() {
            let v = this
            v.showMonths = true
        },
        closeMoreMonths (event) {
            let v= this
            if ( v.showMonths && !v.$refs.stylizedMoreMonths.contains(event.target) ) {
                v.showMonths = false
            }
        },
        viewDays(challenge) {
            let v = this
            let diff = ''
            if(v.curView == 'Completed') {
                diff = moment(challenge.end_date).startOf('day').fromNow();
            } else if (v.curView == 'Ongoing') {
                diff = moment(challenge.start_date).startOf('day').fromNow();
            } else {
                diff = moment(challenge.start_date).startOf('day').fromNow();
                diff = 'Starts ' + diff;
            }
            return diff;
        },
        viewAllMember(){
            let v = this
            v.viewAll = true
            v.maxViews = v.members.length
        },
        checkJoin(users) {
            let v = this
            let existed = users.filter(function(user){
                if(user.id == v.memberId) return user;
            });
            return existed.length;
        }
    },

    mounted() {
        let v = this
        document.addEventListener('click', v.closeMoreMonths)
        M.Datepicker.init(v.$refs.sdatepicker, {
            container: v.$refs.container,
        })
        M.Datepicker.init(v.$refs.edatepicker, {
            container: v.$refs.container,
        })
    },

});
