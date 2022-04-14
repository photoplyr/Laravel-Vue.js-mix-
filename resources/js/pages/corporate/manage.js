const challenges = new Vue({
    el: '#memberDashboard__challenges',

    data: {
        memberId:     window.Laravel.member.id,
        isEnterprise: window.Laravel.isEnterprise,
        isLoad:       false,

        challenge:        window.Laravel.member.challenge,
        months:            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        today:             moment().format('D MMM, YYYY'),
        curCard:           '',
        showMonths:        false,
        selectedMonth:     moment().format('M')-1,
        startDate: ''

    },

    methods: {
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
        viewDays() {
            let v = this
            let diff = ''
            if(v.challenge !== '') {
                diff = moment(v.challenge.start_date).startOf('day').fromNow();
                v.startDate = 'Starts ' + diff;
            }
        },
        handleChange(){
            let v = this
            let diff = moment(v.$refs.sdatepicker.value).startOf('day').fromNow();
            v.startDate = 'Starts ' + diff;
        }
    },

    mounted() {
        let v = this
        document.addEventListener('click', v.closeMoreMonths)
        v.viewDays()

        M.Datepicker.init(v.$refs.sdatepicker, {
            container: v.$refs.container,
        })
        M.Datepicker.init(v.$refs.edatepicker, {
            container: v.$refs.container,
        })
    },

});
