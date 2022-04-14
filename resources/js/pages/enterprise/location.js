const challenges = new Vue({
    el: '#locationManagementDashboard',

    data:    {
        qrPopup: {
            open: false,
        }
    },
    methods: {
        openQRModal() {
            let v = this
            v.qrPopup.open = true
        },
        closeQRModal(event) {
            let v= this
            if ( v.qrPopup.open && !v.$refs.popupContainer.contains(event.target) && !event.target.classList.contains("open-modal") ) {
                v.qrPopup.open = false
            }
        },
    },
    mounted() {
        let v = this
        document.addEventListener('click', v.closeQRModal)
    }
})