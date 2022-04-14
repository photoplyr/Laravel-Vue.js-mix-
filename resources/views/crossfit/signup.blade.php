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
        <form method="POST" action="{{ route('crossfit.signup') }}">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h2 class="large">Company Information</h2>
            <div class="formInput lock">
                <input type="text" name="legal_business_entity" placeholder="Legal Buisness Entity" value="{{ old('legal_business_entity', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="affiliate_name" placeholder="Name of CrossFit Affiliate" value="{{ old('affiliate_name', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="location_address" placeholder="Location Address" value="{{ old('location_address', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="location_city" placeholder="Location City" value="{{ old('location_city', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="location_state" placeholder="Location State" value="{{ old('location_state', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="location_zip" placeholder="Location Zip" value="{{ old('location_zip', '') }}" />
            </div>

            <h2>Contact Information</h2>
            <div class="formInput lock">
                <input type="text" name="first_name" placeholder="First Name of direct point of contact" value="{{ old('first_name', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="last_name" placeholder="Last Name of direct point of contact" value="{{ old('last_name', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="email" placeholder="Email of direct point of contact" value="{{ old('email', '') }}" />
            </div>
            <div class="formInput lock">
                <input type="text" name="phone" placeholder="Phone of direct point of contact" value="{{ old('phone', '') }}" />
            </div>

            <h2 class="large">Membership Rate</h2>
            <div class="formInput lock">
                <input type="text" name="membership_rate" placeholder="Retail Membership Rate for your standard membership offering" value="{{ old('membership_rate', '') }}" />
            </div>

            <h2>How did you hear about the Optum programs?</h2>
            <div class="formInput lock">
                <select name="source">
                    @foreach($sources as $source)
                    <option value="{{ $source }}" {{ old('source', '') == $source ? ' selected' : '' }}>{{ $source }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit">Sign Up</button>

            <div class="footer">
                <img src="/images/crossfit/header_logos.png" />
            </div>
        </form>
    </div>
    <div class="background"></div>
    <div class="clear"></div>
</div>
@endsection
