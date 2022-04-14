require('../bootstrap');
require('materialize-css');

window.Vue      = require('vue');
window.moment   = require('moment');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 */

 let token = window.auth.csrfToken
 if (token) {
     window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token
 } else {
     console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token', token)
 }

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


const locations = new Vue({
    el: '#tv-billingAccount',

    data: {       
        isEnterprise:      window.Laravel.billingAccount.isEnterprise,
        isActivity:        window.Laravel.billingAccount.isActivity,
        companies:         window.Laravel.billingAccount.companies,
        transfers:         window.Laravel.billingAccount.transfers,
        years:             window.Laravel.billingAccount.years,
        months:            window.Laravel.billingAccount.months,
        info:              window.Laravel.billingAccount.info,
        issuers:           window.Laravel.billingAccount.issuers,

        g1_months:         window.Laravel.billingAccount.g1_months,
        g1_mins:           window.Laravel.billingAccount.g1_mins,
        g2_dailyByHour:    window.Laravel.billingAccount.g2_dailyByHour,

        g1:                null,
        g2:                null,
    },

    methods: {
        getColor(key) {
            let base = '#232a53'

            if (key == 0) {
                return base
            }

            let opacity = key/10
            return Highcharts.color(base).brighten(opacity).get()
        }, 
        
        localTime(utcTime) {
            return moment(utcTime).local().format('MM/DD/YYYY hh:mma')
        },
        renderGraph() {
            let v = this

            let mins = [];

            for (let i in v.g1_months) {
                month = v.g1_months[i]
                mins.push(v.g1_mins[month] ? v.g1_mins[month] : 0);
            }

            let minsByMonth = {
                chart: {
                    type: 'column',
                    backgroundColor:'rgba(255, 255, 255, 0.0)',
                    renderTo: 'minsByMonth'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: v.g1_months,
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Activity Hours by Month'
                    },
                },
                legend: false,
                colors: ['#f08f02'],
                series: [{
                    name: '',
                    data: mins,
                    marker: {
                        enabled: false
                    }
                }]
            };

            v.g1 = new Highcharts.chart(minsByMonth);
            v.g1.setSize($('#minsByMonth').width(), 170, false);

            let hours = [];
            let counts = [];

            for (let i in v.g2_dailyByHour) {
                let data = v.g2_dailyByHour[i];
                hours.push(moment(data['hour']).local().format('DD HH:mm'));
                counts.push(data['count']);
            }

            let dailyByHour = {
                chart: {
                    type: 'column',
                    backgroundColor:'rgba(255, 255, 255, 0.0)',
                    renderTo: 'dailyByHour'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: hours,
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Daily Activity by Hour'
                    },
                },
                legend: false,
                colors: ['#f08f02'],
                series: [{
                    name: '',
                    data: counts,
                    marker: {
                        enabled: false
                    }
                }]
            };

            v.g2 = new Highcharts.chart(dailyByHour);
            v.g2.setSize($('#dailyByHour').width(), 170, false);
        }
    },

    computed: {
       
        malePercentage() {
            let v = this

            return v.info.male > 0 ? Math.round(v.info.male / ((v.info.male + v.info.female)/100)) : 0
        },
        femalePercentage() {
            let v = this

            return v.info.female > 0 ? Math.round(v.info.female / ((v.info.male + v.info.female)/100)) : 0
        },
    },

    mounted() {        
        let v = this
        let colors = []
        let series = []
        for (let i in v.issuers.programs) {
            colors.push(v.getColor(i))

            series.push({
                name: v.issuers.programs[i].name,
                y: v.issuers.programs[i].count
            })
        }

        if (colors.length) {
            Highcharts.chart('pieGraph', {
                chart: {
                    type: 'pie',
                    backgroundColor: null,
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    margin: [0,0,10,0],
                    spacingTop: 0,
                    spacingBottom: 0,
                    spacingLeft: 0,
                    spacingRight: 0,
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        colors: colors,
                        dataLabels: {
                            enabled: false,
                        },
                    }
                },
                series: [{
                    name: 'Checkins',
                    innerSize: '40%',
                    data: series,
                }],
                exporting: {
                    enabled: false
                },
            })
        }

        document.addEventListener('click', v.tryCloseDropdowns)

        if (v.isActivity) v.renderGraph();
    },
});
