@extends('layouts.dashboard')

@section('content')
<div class="content">
    <div class="row">
        <div class="input-field col s12">
            <h5>Edit Program</h5>
        </div>
    </div>
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <form method="POST" action="{{ route('enterprise.programs.save', $program->id) }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <select name="company_id">
                            @foreach ($companys as $companyId => $company)
                            <option value="{{ $companyId }}" @if (old('gender', $program->company_id == $companyId)) selected @endif>{{ $company }}</option>
                            @endforeach
                        </select>
                        <label for="company_id">Company</label>
                        @if ($errors->has('company_id'))
                        <span class="red-text">{{ $errors->first('company_id') }}</span>
                        @endif
                    </div>
                    <div class="input-field col s6">
                        <select name="program_id">
                            @foreach ($programs as $programId => $p)
                            <option value="{{ $programId }}" @if (old('gender', $program->program_id == $programId)) selected @endif>{{ $p }}</option>
                            @endforeach
                        </select>
                        <label for="program_id">Program</label>
                        @if ($errors->has('program_id'))
                        <span class="red-text">{{ $errors->first('program_id') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <select name="sector_id">
                            @foreach ($sectors as $sectorId => $sector)
                            <option value="{{ $sectorId }}" @if (old('gender', $program->sector_id == $sectorId)) selected @endif>{{ $sector }}</option>
                            @endforeach
                        </select>
                        <label for="sector_id">Sector</label>
                        @if ($errors->has('sector_id'))
                        <span class="red-text">{{ $errors->first('sector_id') }}</span>
                        @endif
                    </div>
                    <div class="input-field col s6">
                        <select name="tier_id">
                            @foreach ($tiers as $tierId => $tier)
                            <option value="{{ $tierId }}" @if (old('gender', $program->tier_id == $tierId)) selected @endif>{{ $tier }}</option>
                            @endforeach
                        </select>
                        <label for="tier_id">Tier</label>
                        @if ($errors->has('tier_id'))
                        <span class="red-text">{{ $errors->first('tier_id') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input type="number" min="0" id="allowance" name="allowance" value="{{ old('allowance', isset($program) ? $program->allowance : null) }}" @if ($errors->has('allowance')) class="invalid" @endif>
                        <label for="allowance">Allowance</label>
                        @if ($errors->has('allowance'))
                        <span class="helper-text" data-error="{{ $errors->first('allowance') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12 m6">
                        <input type="number" min="0" id="restriction" name="restriction" value="{{ old('restriction', isset($program) ? $program->restriction : null) }}" @if ($errors->has('restriction')) class="invalid" @endif>
                        <label for="restriction">Restriction</label>
                        @if ($errors->has('restriction'))
                        <span class="helper-text" data-error="{{ $errors->first('restriction') }}"></span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m4">
                        <input type="number" step="0.01" min="0" id="rate" name="rate" value="{{ old('rate', isset($program) ? $program->rate : null) }}" @if ($errors->has('rate')) class="invalid" @endif>
                        <label for="rate">Rate</label>
                        @if ($errors->has('rate'))
                        <span class="helper-text" data-error="{{ $errors->first('rate') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12 m4">
                        <input type="number" step="0.01" min="0" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', isset($program) ? $program->daily_rate : null) }}" @if ($errors->has('daily_rate')) class="invalid" @endif>
                        <label for="daily_rate">Daily Rate</label>
                        @if ($errors->has('daily_rate'))
                        <span class="helper-text" data-error="{{ $errors->first('daily_rate') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12 m4">
                        <input type="number" step="0.01" min="0" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', isset($program) ? $program->hourly_rate : null) }}" @if ($errors->has('hourly_rate')) class="invalid" @endif>
                        <label for="hourly_rate">Hourly Rate</label>
                        @if ($errors->has('hourly_rate'))
                        <span class="helper-text" data-error="{{ $errors->first('hourly_rate') }}"></span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <label for="status" class="cursor-pointer">Status: </label>
                        <div class="switch right switch-near-inputs">
                            <label>
                                Inactive
                                <input type="checkbox" id="status" name="status" {{ old('status', isset($program) && $program->status ? true : false) ? 'checked="checked"' : '' }}>
                                <span class="lever"></span>
                                Active
                            </label>
                        </div>
                    </div>
                </div>
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
