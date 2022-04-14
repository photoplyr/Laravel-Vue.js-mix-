var Paginate = require('vuejs-paginate')
import sortableTableHeader from '../components/sortableTableHeader.vue';

const locations = new Vue({
    el: '#locationsList',

    components: {
        Paginate,
        sortableTableHeader,
    },

    data: {
        timeout:      null,
        search:       '',
        isLoad:       false,
        list:         window.Laravel.locations,
        isAdmin:      window.dashboard.isAdmin,
        isEnterprise: window.dashboard.isEnterprise,
        isClubPage:   window.Laravel.enterpriseLocations ? false : true,
        page:         1,
        pages:        window.Laravel.pages,

        header: {
            asc: true,
            active: 'location-id',
            list: [
                {
                    id:       'location-id',
                    title:    'Location ID',
                    sortable: true,
                },
                {
                    title:    'Club ID',
                    sortable: false,
                },
                {
                    id:       'primary-location',
                    title:    'Primary Location',
                    sortable: true,
                },
                {
                    title:    'Address',
                    sortable: false,
                },
                {
                    id:       'city',
                    title:    'City',
                    sortable: true,
                },
                {
                    id:       'state',
                    title:    'State',
                    sortable: true,
                },
                {
                    title:    'Phone',
                    sortable: false,
                },
                {
                    title:    '',
                    sortable: false,
                },
            ]
        }
    },

    watch: {
        search() {
            let v = this

            v.searchLocations()
        },
    },

    methods: {
        searchLocations(resetPage = true) {
            let v = this

            if (resetPage) {
                v.page = 1
            }

            clearTimeout(v.timeout)
            v.timeout = setTimeout(() => {
                axios.post(window.Laravel.enterpriseLocations ? '/enterprise/locations/search' : '/club/locations/search', {
                        search: v.search,
                        page:   v.page,
                        sort:   {
                            id:  v.header.active,
                            asc: v.header.asc,
                        },
                     })
                     .then(response => {
                         if (response.data.success) {
                             v.list = response.data.list
                             v.pages = response.data.pages
                         }
                     })
                     .catch(error => {
                         console.error(error)
                     })
            }, 500)
        },

        setPage(page) {
            this.page = page

            this.searchLocations(false)
        },

        handleSort(sort) {
            let v = this

            if (sort.sortable) {
                if (v.header.active == sort.id) {
                    // toggle asc/desc
                    v.header.asc = !v.header.asc
                } else {
                    v.header.asc    = true
                    v.header.active = sort.id
                }

                this.searchLocations(false)
            }
        },
    },

    mounted() {
        let v = this

        v.isLoad = true

        if (v.isAdmin) {
            v.header.list.push({
                title:    '',
                sortable: false,
            })
        }
    },
});
