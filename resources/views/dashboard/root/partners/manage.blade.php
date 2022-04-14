@extends('layouts.dashboard')

@section('content')
<div class="content" id="partnerManage">
    <div class="row">
        <div class="col s12">
            <h5>Partners: {{ isset($partner) ? 'Edit' : 'Add' }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <form method="POST" action="{{ route('root.partners.save') }}">
                @csrf
                <input type="hidden" value="{{ isset($partner) ? $partner->id : 0 }}" name="id" />

                <div class="row">
                    <div class="input-field col s8">
                        <input type="text" id="name" name="name" value="{{ old('name', isset($partner) ? $partner->name : null) }}" @if ($errors->has('name')) class="invalid" @endif>
                        <label for="name">Name</label>
                        @if ($errors->has('name'))
                        <span class="helper-text" data-error="{{ $errors->first('name') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s4">
                        <div class="switch">
                            <label>
                                Active
                                <input type="checkbox" id="is_active" name="is_active" {{ old('is_active', isset($partner) && $partner->is_active == 1 ? true : false) ? 'checked="checked"' : '' }}>
                                <span class="lever"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input type="text" id="description" name="description" value="{{ old('description', isset($partner) ? $partner->description : null) }}" @if ($errors->has('description')) class="invalid" @endif>
                        <label for="description">Description</label>
                        @if ($errors->has('description'))
                        <span class="helper-text" data-error="{{ $errors->first('description') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12">
                        <input type="text" id="link" name="link" value="{{ old('link', isset($partner) ? $partner->link : null) }}" @if ($errors->has('link')) class="invalid" @endif>
                        <label for="link">Website Link</label>
                        @if ($errors->has('link'))
                        <span class="helper-text" data-error="{{ $errors->first('link') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12">
                        <input type="text" id="icon" name="icon" value="{{ old('icon', isset($partner) ? $partner->icon : null) }}" @if ($errors->has('icon')) class="invalid" @endif>
                        <label for="icon">Icon Link</label>
                        @if ($errors->has('icon'))
                        <span class="helper-text" data-error="{{ $errors->first('icon') }}"></span>
                        @endif
                    </div>
                    <div class="input-field col s12">
                        <input type="number" id="priority" name="priority" value="{{ old('priority', isset($partner) ? $partner->priority : 0) }}" @if ($errors->has('priority')) class="invalid" @endif>
                        <label for="priority">Priority (asc)</label>
                        @if ($errors->has('priority'))
                        <span class="helper-text" data-error="{{ $errors->first('priority') }}"></span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 center-align">
                        <button class="waves-effect waves-light btn green mainColorBackground" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
