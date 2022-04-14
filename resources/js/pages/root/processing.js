const processingFee = new Vue({
    el: '#billingAccount',

    data: {
        isLoad:            false,

        selectYearOpen:    false,
        selectMonthOpen:   false,
        selectProgramOpen: false,
        timeout:           null,

        isEnterprise:      window.Laravel.billingAccount.isEnterprise,
        isActivity:        window.Laravel.billingAccount.isActivity,
        programs:         window.Laravel.billingAccount.programs,
        transfers:         window.Laravel.billingAccount.transfers,
        years:             window.Laravel.billingAccount.years,
        months:            window.Laravel.billingAccount.months,
        info:              window.Laravel.billingAccount.info,
    },

    methods: {
        toggleYearSelect() {
            this.selectYearOpen = !this.selectYearOpen
        },
        toggleMonthSelect() {
            this.selectMonthOpen = !this.selectMonthOpen
        },
        toggleProgramSelect() {
            this.selectProgramOpen = !this.selectProgramOpen
        },
        selectProgram(programID) {
            let v = this

            let activeProgram = v.programs.find(program => program.active === true)

            if (activeProgram.id != programID) {
                for (i in v.programs) {
                    if (v.programs[i].id == activeProgram.id) {
                        v.programs[i].active = false
                    }

                    if (v.programs[i].id == programID) {
                        v.programs[i].active = true
                    }
                }

                v.selectProgramOpen = false
                v.refreshCounters()
            }
        },
        selectMonth(monthId) {
            let v = this

            let activeMonth = v.months.find(month => month.active === true)

            if (activeMonth.id != monthId) {
                for (i in v.months) {
                    if (v.months[i].id == activeMonth.id) {
                        v.months[i].active = false
                    }

                    if (v.months[i].id == monthId) {
                        v.months[i].active = true
                    }
                }

                v.selectMonthOpen = false
                v.refreshCounters()
            }
        },
        selectYear(yearId) {
            let v = this
            let activeYear = v.years.find(year => year.active === true)

            if (activeYear.id != yearId) {
                for (i in v.years) {
                    if (v.years[i].id == activeYear.id) {
                        v.years[i].active = false
                    }

                    if (v.years[i].id == yearId) {
                        v.years[i].active = true
                    }
                }

                v.selectYearOpen = false
                v.refreshCounters()
            }
        },
        tryCloseDropdowns(event) {
            let v = this

            if (v.selectYearOpen && !v.$refs.stylizedSelectYear.contains(event.target))  {
                this.selectYearOpen = false
            }

            if (v.selectMonthOpen && !v.$refs.stylizedSelectMonth.contains(event.target))  {
                this.selectMonthOpen = false
            }

            if (v.selectProgramOpen && !v.$refs.stylizedSelectProgram.contains(event.target))  {
                this.selectProgramOpen = false
            }
        },
        refreshCounters() {
            let v = this

            v.isLoad = true
            clearTimeout(v.timeout)
            v.timeout = setTimeout(() => {
                axios.post('/root/report/processing', {
                year:       v.selectedYear.id,
                month:      v.selectedMonth.id,
                program_id: v.selectedProgram.id,
            })
                .then(response => {
                v.isLoad = false
            if (response.data.success) {
                v.programs = response.data.programs
                v.transfers = response.data.transfers
                v.years     = response.data.years
                v.months    = response.data.months
                v.info      = response.data.info
            }
        })
        .catch(error => {
                v.isLoad = false
            console.log('error', error.message);
        })
        }, 500)
},
    },
    computed: {
        selectedYear() {
            let v = this

            return v.years.find(year => year.active == true)
        },
        selectedMonth() {
            let v = this

            return v.months.find(month => month.active == true)
        },
        selectedProgram() {
            let v = this

            return v.programs.find(program => program.active == true)
        },
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
        document.addEventListener('click', v.tryCloseDropdowns)

    },
})