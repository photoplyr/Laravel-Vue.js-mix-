@extends('layouts.landing')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
<link href="{{ asset('css/crossfit.css') }}" rel="stylesheet">
@endpush

@section('content')
<header>
    <img src="/images/crossfit/header_logos.png" />
</header>
<div class="hero">
    <div class="description">
        <h1>CrossFit + Optum have partnered – Affiliate Owners opt in now!</h1>
        <p>Optum is the gold standard of fitness network programs and is launching with CrossFit on January 1, 2022. Affiliate Owners opt in now to get onboarded and ready for the 1/1/22 launch and be a part of the solution in which over 20,000 gyms and studios are already participating. Gain access to 11+ Million (and growing!) eligible members in 2022 through insurance and employer-based fitness programs.</p>
    </div>
    <div class="gradient-bottom"></div>
</div>
<div class="clear"></div>
<div class="two-in-row orange-background">
    <div class="row-block">
        <h2>Renew Active/One Pass Medicare & Medicaid Member Participation Requirement</h2>
        <p>Join the largest Medicare Fitness Network offering in the industry.</p>
    </div>
    <div class="row-block">
        <h2>One Pass Commercial Member Participation Requirement</h2>
        <p>Join our new fitness network providing access to members ages 18-64.</p>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="how-it-works">
    <h2>How it works for you</h2>
    <ul>
        <li>Opt into the program now and your location will be listed on the fitness network websites and promoted to eligible members throughout your community who are covered members of an eligible plan or employer.</li>
        <li>Complete Concierge Health enrollment to enable VeriTap check-in application for IOS and Android which offers you a seamless system to track and report member usage.</li>
        <li>When an eligible member presents a confirmation code, enroll him or her at your affiliate location and instruct them to check-in using the VeriTap system each time they take a class at each of your locations and your facility will receive subsidy payments based on their check-ins.</li>
    </ul>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="squared">
    <div class="two-in-row">
        <div class="row-block">
            <h3>Renew Active/One Pass Medicare Member Participation Requirement</h3>
        </div>
        <div class="row-block">
            <h3>Network Reimbursement amount paid by Optum</h3>
        </div>
        <div class="clear"></div>
    </div>
    <div class="two-in-row">
        <div class="row-block">
            <p>Medicare Member visits CrossFit Affiliate during calendar month Medicare is defined as a federal health insurance program (government)|for people who are 65 or older, certain younger people with disabilities,and/or people with End-Stage Renal Disease. Medicare Payers includeMedicare Advantage, Medicare Supplement, Group Retiree, Medicaid,and DSNP membership.</p>
        </div>
        <div class="row-block">
            <p>$15.00 per visit payable to CrossFit Affiliate<br />with a maximum monthly payment of $60.00<br />per Medicare Member (4 visits max)</p>
            <i>*Affiliates have the option to upsell class packages or unlimited memberships minus the difference of the max reimbursement amount.</i>
        </div>
        <div class="clear"></div>
    </div>
    <div class="two-in-row">
        <div class="row-block">
            <h3>One Pass Commercial Member Participation Requirement</h3>
        </div>
        <div class="row-block">
            <h3>Network Reimbursement amount paid by Optum</h3>
        </div>
        <div class="clear"></div>
    </div>
    <div class="two-in-row">
        <div class="row-block">
            <p>Commercial Member visits CrossFit Affiliate during calendar month Commercial is defined as a health insurance member administered by non-governmental entities, employer groups, administrative serviceorganizations, TPA’s, benefit hubs.</p>
        </div>
        <div class="row-block">
            <p>$15.00 per visit payable to CrossFit Affiliate with a maximum monthly payment of $150.00 (10 visits max).</p>
            <i>*Affiliates have the option to upsell class packages or unlimited memberships minus the difference of the max reimbursement amount.</i>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="steps">
    <h2>Steps to Join</h2>
    <div class="two-in-row">
        <div class="row-block">
            <h3>Step 1</h3>
            <p>Collect the following information</p>
            <ul>
                <li>Legal Business Entity</li>
                <li>Name of CrossFit Affiliate</li>
                <li>Address of location</li>
                <li>First and Last Name and Email of who will be signing the Contract</li>
                <li>First and Last Name and Email of direct point of contact if different from the signee</li>
                <li>Retail Membership Rate for your standard membership offering</li>
            </ul>
            <a href="https://uploads-ssl.webflow.com/5eb1d6491b62d572c3e6b797/615c79586919b3385a36dc32_Fitness%20Passport.pdf" target="_blank">Click here to view sample contract</a>
        </div>
        <div class="row-block">
            <h3>Step 2</h3>
            <p>Once you have all of the information,<br />time to get started</p>
            <a class="button" href="{{ route('crossfit.signup') }}">Get Started</a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<footer>
    Copyright © 2021 Concierge Health Inc. All rights reserved.
</footer>
@endsection
