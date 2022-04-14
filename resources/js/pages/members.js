var Paginate = require('vuejs-paginate')

const members = new Vue({
    el: '#membersList',

    components: {
        paginate: Paginate,
    },

    data: {
        timeout:      null,
        search:       '',
        isLoad:       false,
        list:         window.Laravel.members,
        isAdmin:      window.dashboard.isAdmin,
        isEnterprise: window.Laravel.isEnterprise,
        isCorporate:  window.Laravel.isCorporate,
        ledgerId:     window.Laravel.ledgerId,
        page:         1,
        pages:        window.Laravel.pages,
        searchId:     0,
        myclub:       false,

        memberId:     null,

        checkin: {
            modal:      null,
            datepicker: null,
        },
        qrModal:      null,
    },

    watch: {
        search() {
            let v = this

            v.searchMembers()
        },
        myclub() {
            let v = this

            v.searchMembers()
        }
    },

    methods: {
        searchMembers(resetPage = true) {
            let v = this

            if (resetPage) {
                v.page = 1
            }

            v.searchId += 1

            clearTimeout(v.timeout)
            v.timeout = setTimeout(() => {
                $link = ''
                if (v.ledgerId) $link = '/checkin/'+ v.ledgerId +'/members/search'
                else if (v.isCorporate) $link = '/corporate/members/search'
                else if (v.isEnterprise) $link = '/enterprise/members/search'
                else $link = '/club/members/search'

                axios.post($link, {
                        searchId: v.searchId,
                        search:   v.search,
                        page:     v.page,
                        myclub:   v.myclub,
                     })
                     .then(response => {
                         if (response.data.success && response.data.searchId == v.searchId) {
                             v.list  = response.data.list
                             v.pages = response.data.pages
                         }
                     })
                     .catch(error => {
                         console.log(error.response)
                     })
            }, 500)
        },

        setPage(page) {
            this.page = page

            this.searchMembers(false)
        },

        redirectToViewPage(id) {
            let v = this
            if (v.checkin.modal.isOpen) return;

            $link = ''
            if (v.isCorporate) $link = '/corporate/members/'+id+'/view'
            else if (v.isEnterprise) $link = '/enterprise/members/'+id+'/view'
            else $link = '/club/members/'+id+'/view'

            window.location = $link;
        },

        localTime(utcTime) {
            return moment(utcTime).local().format('MM/DD/YYYY hh:mma')
        },

        openCheckinModal(id) {
            let v = this
            v.memberId = id
            v.checkin.modal.open()
        },
        checkinMember() {
            let v = this

            v.checkin.modal.close()
            axios.post((v.isEnterprise ? '/enterprise' : '/club') +'/members/'+ v.memberId +'/checkin', {
                    date: moment(v.checkin.datepicker.date).format('YYYY-MM-DD'),
                    timezoneOffset: new Date().toString().match(/([-\+][0-9]+)\s/)[1],
                })
                .then(response => {
                     if (response.data.success) {
                         M.toast({
                             html:          response.data.message,
                             displayLength: 6000,
                             classes:       'blue darken-1 white-text',
                         });
                     } else {
                         M.toast({
                             html:          response.data.message,
                             displayLength: 6000,
                             classes:       'blue darken-1 white-text',
                         });
                     }
                 })
                 .catch(error => {
                     console.error(error.response)
                 })
        },
        openQRModal() {
            let v = this
            v.qrModal.open()
        },
        printCode() {
            $('#printQrCode').print()
        }
    },

    mounted() {
        let v = this

        v.isLoad = true

        v.checkin.modal      = M.Modal.init(v.$refs.checkinDateModal)
        v.checkin.datepicker = M.Datepicker.init(v.$refs.checkinDateDatepicker, {
            container: v.$refs.container,
        })
        v.qrModal =  M.Modal.init(v.$refs.qrModal)
        v.checkin.datepicker.setDate(new Date())
        v.checkin.datepicker.setInputValue()
    },
});
