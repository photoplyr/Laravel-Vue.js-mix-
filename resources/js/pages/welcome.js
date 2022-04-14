/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

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

const registrationPage = new Vue({
    el: '#registrationPage',

    data: {
        isLoad:           true,

        timeout:          null,
        stepper:          null,
        type:             'join_to_company',
        company:          null,
        company_id:       null,

        search_company:   '',

        ccode:            null,
        rcode:            null,
        terms:            false,
        isSending:        false,

        user_email:       '',
        user_first_name:  '',
        user_last_name:   '',
        user_phone:       '',
        user_password:    '',
        user_password_confirmation: '',

        invalid_domain:   false,

        companies: [],

        argyleProfile:    null,
        argyleEmployment: null,

        status:           false,
        eligibility_status:  'Unknown',
    },

    watch: {
        search_company() {
            let v = this

            clearTimeout(v.timeout)

            v.timeout = setTimeout(function() {
                if (v.search_company == '') {
                    v.companies = []
                } else {
                    axios.post('/company/search', {
                            search: v.search_company,
                         })
                         .then(response => {
                             if (response.data.success) {
                                 v.companies = response.data.companies
                             } else {
                                 v.companies = []
                             }
                         })
                         .catch(error => {
                             console.error(error)
                         })
                }
            }, 500)
        },
    },

    methods: {
        selectCompany(company) {
            let v = this

            v.company = company
            v.company_id = company.id

            console.log('xx', v.company)
            if (v.company.argyle) {
                const argyle = Argyle.create({
                    // pluginKey: '017ec111-aa56-8f2f-b096-a9374c3c52f1',
                    pluginKey: '017ec111-aa5b-25d6-c9f2-baff81c9eb92',
                    apiHost: 'https://api-sandbox.argyle.com/v1',
                    linkItems: [],
                    onAccountConnected: ({ accountId, userId, linkItemId }) => {
                        console.log('Account connected: ', accountId, ' User ID:', userId, ' Link Item ID:', linkItemId)
                    },
                    onUserCreated: ({ userId, userToken }) => {
                        console.log('User created: ', userId, 'User token:', userToken)
                        fetch("https://api.argyle.com/v1/employments?user="+userId, {
                            method: "GET",
                            headers: {'Authorization': 'Bearer ' + userToken}
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Employments result', data)
                            if (data.count > 0) {
                                v.argyleEmployment = data.results[0]
                                if (v.argyleEmployment.status == 'active') {
                                    v.eligibility_status = 'Eligible'
                                    v.status = 1
                                } else if (v.argyleEmployment.status == 'inactive' || v.argyleEmployment.status == 'terminated') {
                                    v.eligibility_status = 'Ineligible'
                                    v.status = 0
                                } else {
                                    v.eligibility_status = 'Unknown'
                                    v.status = 0
                                }
                                fetch("https://api.argyle.com/v1/profiles?user="+userId, {
                                    method: "GET",
                                    headers: {'Authorization': 'Bearer ' + userToken}
                                })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Profile result', data)
                                    if (data.count > 0) {
                                        v.argyleProfile = data.results[0]
                                        v.user_email = v.argyleProfile.email
                                        v.user_first_name = v.argyleProfile.first_name
                                        v.user_last_name = v.argyleProfile.last_name
                                        v.user_phone = v.argyleProfile.phone_number
                                    } else {
                                        v.argyleProfile = null;
                                    }
                                    v.nextStep()
                                })
                                .catch(err => {
                                    console.log('err', err)
                                });
                            } else {
                                v.argyleEmployment = null;
                            }

                            v.nextStep()
                        })
                        .catch(err => {
                            console.log('err', err)
                        });

                        ///////////////////////////
                        // fetch("https://api.argyle.com/v1/employments?account=017ec112-9bb7-794d-fae0-143fdc02fe17", {
                        //     method: "GET",
                        //     headers: {'Authorization': 'Basic MTA2ZTMxNGI4MWU1NDNmYmE4MGNlNzUzZTkwMDdhNzA6Qktzb0Q1TEhqamU3TW5neQ=='}
                        // })
                        // .then(response => response.json())
                        // .then(data => {
                        //     console.log('Employments result', data)
                        //     if (data.count > 0) {
                        //         v.argyleEmployment = data.results[0]
                        //         if (v.argyleEmployment.status == 'active') {
                        //             v.eligibility_status = 'Eligible'
                        //             v.status = 1
                        //         } else if (v.argyleEmployment.status == 'inactive' || v.argyleEmployment.status == 'terminated') {
                        //             v.eligibility_status = 'Ineligible'
                        //             v.status = 0
                        //         } else {
                        //             v.eligibility_status = 'Unknown'
                        //             v.status = 0
                        //         }
                        //         fetch("https://api.argyle.com/v1/profiles?account=017ec112-9bb7-794d-fae0-143fdc02fe17", {
                        //             method: "GET",
                        //             headers: {'Authorization': 'Basic MTA2ZTMxNGI4MWU1NDNmYmE4MGNlNzUzZTkwMDdhNzA6Qktzb0Q1TEhqamU3TW5neQ=='}
                        //         })
                        //         .then(response => response.json())
                        //         .then(data => {
                        //             console.log('Profile result', data)
                        //             if (data.count > 0) {
                        //                 v.argyleProfile = data.results[0]
                        //                 v.user_email = v.argyleProfile.email
                        //                 v.user_first_name = v.argyleProfile.first_name
                        //                 v.user_last_name = v.argyleProfile.last_name
                        //                 v.user_phone = v.argyleProfile.phone_number
                        //                 // $('#user_email').focus();
                        //                 // $('#user_first_name').focus();
                        //                 // $('#user_last_name').focus();
                        //                 // $('#user_phone').focus();
                        //             } else {
                        //                 v.argyleProfile = null;
                        //             }
                        //             v.nextStep()
                        //         })
                        //         .catch(err => {
                        //             console.log('err', err)
                        //         });
                        //     } else {
                        //         v.argyleEmployment = null;
                        //     }

                        //     v.nextStep()
                        // })
                        // .catch(err => {
                        //     console.log('err', err)
                        // });
                    }
                })
                argyle.open()
            } else v.nextStep()
        },
        sendCode() {
            let v = this

            clearTimeout(v.ccodeTimeout)
            v.isSending = true;
            v.ccodeTimeout = setTimeout(function() {
                axios.post('/welcome/verify', {
                    email: v.user_email,
                })
                .then(response => {
                    v.isSending = false;
                    if (response.data.success) {
                        v.rcode = response.data.code
                        M.toast({
                            html:          'Verification Code has been sent to your E-Mail!',
                            displayLength: 6000,
                            classes:       'blue darken-1 white-text',
                        })
                    }
                })
                .catch(error => {
                    console.error(error)
                })
            }, 12000)
        },
        nextStep() {
            let v = this

            let valid = v.validate()

            if (!valid) {
                v.stepper.wrongStep()
            } else {
                v.stepper.nextStep()
            }

        },
        validate() {
            let v = this
            let valid = true
            let steps = v.stepper.getSteps()

            if (steps.active.index == 0) {
                switch (v.type) {
                    case 'join_to_company':
                        if (v.company_id == null) {
                            valid = false
                        }
                        break;
                }
            } else if (steps.active.index == 1) {
                if (v.user_email ==    '' ||
                    v.user_first_name ==    '' ||
                    v.user_last_name ==   '' ||
                    v.user_phone ==  '' ||
                    v.user_password == '' ||
                    v.user_password_confirmation == ''
                ) {
                    valid = false
                }

                let regex = new RegExp(v.company.domain+'\\s*$')
                if (!regex.test(v.user_email)){
                    valid = false
                    v.invalid_domain = true
                }
            }
            return valid
        },
        beforeSubmit() {
            let v = this

            axios.post('/welcome/validate', {
                    company_id:       v.company_id,
                    ccode:            v.ccode,
                    status:           v.status,
                    eligibility_status: v.eligibility_status,
                    user_email:       v.user_email,
                    user_first_name:  v.user_first_name,
                    user_last_name:   v.user_last_name,
                    user_phone:       v.user_phone,
                    user_password:    v.user_password,
                    user_password_confirmation: v.user_password_confirmation,

                    terms: v.terms,
                 })
                 .then(response => {
                     if (response.data.success) {
                         v.$refs.registerForm.submit();
                     } else {
                         v.stepper.openStep(response.data.step)

                         M.toast({
                             html:          response.data.error,
                             displayLength: 6000,
                             classes:       'blue darken-1 white-text',
                         })
                     }
                 })
                 .catch(error => {
                     console.error(error)
                 })
        },
    },

    mounted() {
        let v = this

        v.stepper = new MStepper(document.querySelector('.stepper'), {
            // options
            firstActive: 0, // this is the default
            linearStepsNavigation: true,
            validationFunction: v.validate,
        })

        v.isLoad = false
    },
});
