@extends('layouts.dashboard')

@section('content')
<div class="content" id="membersList" ref="container">
    <div class="row">
        <div class="input-field col {{ $isEnterprise && auth()->user()->isAdmin() ? 's12' : 's9' }}">
            <input type="text" id="search" v-model="search">
            <label for="search">Search by Name or Phone</label>
        </div>
        @if (!$isEnterprise && auth()->user()->isAdmin())
        <div class="input-field col s1">
            <label>
              <input type="checkbox" class="filled-in" v-model="myclub"/>
              <span>My Club</span>
            </label>
        </div>
        <div class="input-field col s1">
            <a class="waves-effect waves-light btn-small green right mainColorBackground" href="{{ route('club.members.create') }}"><i class="material-icons left">add</i>Add</a>
        </div>
        <div class="input-field col s1">
            <a href="#" class="waves-effect waves-light btn-small green mainColorBackground open-modal" @click="openQRModal"><i class="material-icons left">fullscreen</i>QR</a>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col s12">
            <table class="striped low-padding" id="members">
                <thead>
                    <tr>
                        <th width="60"></th>
                        <th>Name</th>
                        <th>Latest Checkin</th>
                        <th>Member ID</th>
                        <th>Program</th>
                        <th width="150"></th>
                        <th width="80"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in list" @click="redirectToViewPage(item.id)">
                        <td width="60">
                            <div :style="'background-image: url(\''+ item.photo +'\');'" class="avatar square-40x40 circle margin-0-auto"></div>
                        </td>
                        <td>@{{ item.displayName }}</td>
                        <td>@{{ item.latestCheckin?localTime(item.latestCheckin):'None' }}</td>
                        <td>@{{ item.membership ?? 'None' }}</td>
                        <td>@{{ item.program }}</td>
                        <td width="80" class="center-align">
                            <button type="button" class="right btn-floating btn-small waves-effect waves-light green" @click="openCheckinModal(item.id)">
                                <i class="material-icons">check_box</i>
                            </button>
                        </td>
                        <td width="80" class="center-align memberRole">
                            <i v-if="item.isEligible" class="material-icons green-text">verified_user</i>
                            <i v-else class="material-icons-outlined grey-text text-darken-1">verified_user</i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row" v-if="pages > 1">
        <div class="col s12">
            <paginate
                v-model="page"
                :page-count="pages"
                :click-handler="setPage"
                :prev-text="'<i class=\'material-icons\'>chevron_left</i>'"
                :next-text="'<i class=\'material-icons\'>chevron_right</i>'"
                :container-class="'pagination'">
            </paginate>
        </div>
    </div>
    <div class="modal modal-small" ref="checkinDateModal">
        <div class="modal-content">
            <h5 class="center-align">Checkin Member</h5>
            <div class="row margin-0">
                <div class="col s12 input-field">
                    <input type="text" class="datepicker" ref="checkinDateDatepicker">
                    <label for="email">Pick a date</label>
                </div>
            </div>
        </div>
        <div class="modal-footer modal-footer-styled">
            <div class="padding-0-20-20 overflow-hidden">
                <button type="button" class="margin-0 left waves-effect waves-dark btn-flat modal-close">Cancel</button>
                <button type="button" class="margin-0 right waves-effect waves-light blue btn btn-primary mainColorBackground" @click="checkinMember">Confirm</button>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <div class="modal modal-small" ref="qrModal" id="qrContainer">
        <div id="printQrCode">
            <div class="center-align qr-block">
                <img class="logo-image" src="{{asset('images/concierge/_veritap_logo_white.png')}}" width="130px">
                <img class="logo-image print-logo" src="{{asset('images/concierge/_veritap_logo_black.png')}}" width="130px">
                <span class="qr-code">
                    {!! QrCode::size(280)->color(255,255,255)->backgroundColor(0,0,0)->generate('http://app.conciergehealth.co?location_id='.($location?$location->token:'')) !!}
                </span>
                <span class="qr-code print-code">
                    {!! QrCode::size(280)->color(0,0,0)->backgroundColor(255,255,255)->generate('http://app.conciergehealth.co?location_id='.($location?$location->token:'')) !!}
                </span>
            </div>
            <div class="no-print center-align">
                <br>
                <button type="button" class="waves-effect waves-light btn-small mainColorBackground" @click="printCode"><i class="material-icons left">print</i> Print</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
window.Laravel.members      = {!! json_encode($members) !!};
window.Laravel.pages        = {!! json_encode($pages) !!};
window.Laravel.isEnterprise = {!! $isEnterprise ? 1 : 0 !!};
</script>
<script src="{{ asset('js/pages/members.js') }}"></script>
<script src="{{ asset('js/print.min.js') }}"></script>
@endpush
