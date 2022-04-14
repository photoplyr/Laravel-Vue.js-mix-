@extends('layouts.dashboard')

@section('content')
<div class="content" id="partnersList">
    <div class="row">
        <div class="input-field col s12">
            <a class="waves-effect waves-light btn-small green right" href="{{ route('root.partners.create') }}"><i class="material-icons left">add</i>Add</a>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="partnersList">
                <div class="card-container">
                    @foreach ($partners as $partner)
                    <div class="card">
                        <div class="card-content">
                            <span class="activityIndicator {{ $partner->is_active ? 'active' : 'deactivated' }}">{{ $partner->is_active ? 'Active' : 'Deactivated' }}</span>
                            <a class="edit" href="{{ route('root.partners.edit', $partner->id) }}">edit</a>
                            <div class="card-header">
                                <img class="device-logo" src="{{ $partner->icon }}" alt="{{ $partner->name }}">
                                <h4 class="card-title">{{ $partner->name }}</h4>
                            </div>
                            <div class="card-body">
                                <p class="device-description">{{ $partner->description }}</p>
                            </div>
                            <div class="card-footer">
                                <a class="waves-effect waves-light" href="{{ $partner->link }}" target="_blank">Visit Website <i class="material-icons">arrow_forward</i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
