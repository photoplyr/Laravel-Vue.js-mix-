@extends('layouts.dashboard')

@section('content')
<div class="content" id="icompany">
    <div class="row">
        <div class="col s12">
            <h5>Insurance Company: {{ isset($icompany) ? 'Edit' : 'Add' }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <form method="POST" action="{{ route('root.insurance_company.save') }}">
                @csrf
                <input type="hidden" value="{{ isset($icompany) ? $icompany->id : 0 }}" name="id" />

                <div class="row">
                    <div class="input-field col s12">
                        <select name="insurance_id" id="insurance_id" @if ($errors->has('insurance_id')) class="invalid" @endif>
                            <option value="">Choose Insurance</option>
                            @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @if (isset($icompany) && $icompany->insurance_id == $company->id) selected @endif>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <label for="insurance_id">Insurance</label>
                        @if ($errors->has('insurance_id'))
                        <span class="helper-text" data-error="{{ $errors->first('insurance_id') }}"></span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <select name="company_id" id="company_id" @if ($errors->has('company_id')) class="invalid" @endif>
                            <option value="">Choose Company</option>
                            @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @if (isset($icompany) && $icompany->company_id == $company->id) selected @endif>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <label for="company_id">Company</label>
                        @if ($errors->has('company_id'))
                        <span class="helper-text" data-error="{{ $errors->first('company_id') }}"></span>
                        @endif
                    </div>
                </div>
                <div class="row"></div>
                <div class="row"></div>
                <div class="row">
                    <div class="col s12 center-align">
                        <button class="waves-effect waves-light btn green mainColorBackground">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.helper-text:after {
    content: attr(data-error);
    color: #f44336;
}
</style>
@endpush
