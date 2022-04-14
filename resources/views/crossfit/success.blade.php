@extends('layouts.landing')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
<link href="{{ asset('css/crossfit.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="signupForm">
    <div class="leftSide">
        <div class="successScreen">
            <p>Thanks for completing your application for Renew Active/One Pass Medicare & One Pass Commercial. We are excited you will be joining Optum’s Medicare & Commercial Fitness Reimbursement networks. A contract will be requested with the details provided in your application and you will receive within 3 business days via Adobe for electronic signature.<br /><br />Once the contract is signed, an Optum representative will reach out to you with next steps. If you have any questions on the contract, please reach out to <a href="email:melissa.braem@optum.com">Melissa.braem@optum.com</a>. We look forward to working with you.</p>
        </div>
        <div class="footer">
            <img src="/images/crossfit/header_logos.png" />
        </div>
    </div>
    <div class="background"></div>
    <div class="clear"></div>
</div>
@endsection
