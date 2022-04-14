@extends('layouts.dashboard')

@section('content')
    <div class="content" id="memberDashboard__challenges" ref="container">
        <div class="challengeDashboard__left">
            <div class="row">
                <div class="col s6">
                    <h2 class="page-title">
                        Challenge: {{ isset($challenge) ? 'Edit' : 'Add' }}
                    </h2>
                </div>
                <div class="col s6">
                    <div class="challenge-calendar">
                        <span>@{{ new Date().getFullYear() }}</span>
                        <div class="month-list">
                            <span class="challenge-month" v-for="(month, index) in months" v-if="index < 6" :class='{"active": index == selectedMonth}'>@{{ month }}</span>
                            <span class="challenge-month" @click="showMoreMonths()" ref="stylizedMoreMonths">
                            <i class="material-icons">event_note</i>
                            <div class="challenge-month-panel" v-show="showMonths">
                                <span class="challenge-month" v-for="(month, index) in months" v-if="index > 5" :class='{"active": index == selectedMonth}'>@{{ month }}</span>
                            </div>
                        </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col s12">
                        <form method="POST" action="{{ route('corporate.challenges.save') }}">
                            @csrf
                            <input type="hidden" value="{{ isset($challenge) ? $challenge->id : 0 }}" name="id" />

                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" id="title" name="title" value="{{ old('title', isset($challenge) ? $challenge->title : null) }}" @if ($errors->has('title')) class="invalid" @endif @if(isset($challenge)) v-model="challenge.title" @endif required>
                                    <label for="title">Title</label>
                                    @if ($errors->has('title'))
                                        <span class="helper-text" data-error="{{ $errors->first('title') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', isset($challenge) ? $challenge->subtitle : null) }}" @if ($errors->has('subtitle')) class="invalid" @endif @if(isset($challenge)) v-model="challenge.subtitle" @endif required>
                                    <label for="subtitle">Sub Title</label>
                                    @if ($errors->has('subtitle'))
                                        <span class="helper-text" data-error="{{ $errors->first('subtitle') }}"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <select name="type_id" id="type_id" @if ($errors->has('type_id')) class="invalid" @endif>
                                        <option value="">Choose Type</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}" @if (isset($challenge) && $challenge->type_id == $type->id) selected @endif>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="type">Type</label>
                                    @if ($errors->has('type_id'))
                                        <span class="helper-text" data-error="{{ $errors->first('type_id') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s6">
                                    <input type="number" id="price" step="0.01" name="price" value="{{ old('price', isset($challenge) ? $challenge->price : null) }}" @if ($errors->has('price')) class="invalid" @endif required>
                                    <label for="price">Price</label>
                                    @if ($errors->has('price'))
                                        <span class="helper-text" data-error="{{ $errors->first('price') }}"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea name="desc" id="desc" class="materialize-textarea" @if(isset($challenge)) v-model="challenge.desc" @endif>{{ old('responses', isset($challenge) ? $challenge->desc : '') }}</textarea>
                                    <label for="desc">Description</label>
                                    @if ($errors->has('desc'))
                                        <span class="helper-text" data-error="{{ $errors->first('desc') }}"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" id="start_date" name="start_date" value="{{ old('start_date', isset($challenge) ? $challenge->start_date : null) }}" ref="sdatepicker" @if ($errors->has('start_date')) class="invalid datepicker" @else class="datepicker" @endif @if(isset($challenge)) v-model="challenge.start_date" @change="handleChange" @endif required>
                                    <label for="start_date">Start Date</label>
                                    @if ($errors->has('start_date'))
                                        <span class="helper-text" data-error="{{ $errors->first('start_date') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" id="end_date" name="end_date" value="{{ old('end_date', isset($challenge) ? $challenge->end_date : null) }}" ref="edatepicker" @if ($errors->has('end_date')) class="invalid datepicker" @else class="datepicker" @endif required>
                                    <label for="end_date">End Date</label>
                                    @if ($errors->has('end_date'))
                                        <span class="helper-text" data-error="{{ $errors->first('end_date') }}"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input type="text" id="photo" name="photo" value="{{ old('photo', isset($challenge) ? $challenge->photo : null) }}" @if ($errors->has('photo')) class="invalid" @endif @if(isset($challenge)) v-model="challenge.photo" @endif>
                                    <label for="photo">Photo URL</label>
                                    @if ($errors->has('photo'))
                                        <span class="helper-text" data-error="{{ $errors->first('photo') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s6">
                                    <input type="text" id="medal_url" name="medal_url" value="{{ old('medal_url', isset($challenge) ? $challenge->medal_url : null) }}" @if ($errors->has('medal_url')) class="invalid" @endif>
                                    <label for="medal_url">Medal URL</label>
                                    @if ($errors->has('medal_url'))
                                        <span class="helper-text" data-error="{{ $errors->first('medal_url') }}"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s2">
                                    <input type="number" id="distance" name="distance" value="{{ old('distance', isset($challenge) ? $challenge->distance : null) }}" @if ($errors->has('distance')) class="invalid" @endif required>
                                    <label for="distance">Distance</label>
                                    @if ($errors->has('distance'))
                                        <span class="helper-text" data-error="{{ $errors->first('distance') }}"></span>
                                    @endif
                                </div>

                                <div class="input-field col s2">
                                    <input type="number" id="steps" name="steps" value="{{ old('steps', isset($challenge) ? $challenge->steps : null) }}" @if ($errors->has('steps')) class="invalid" @endif>
                                    <label for="steps">Steps</label>
                                    @if ($errors->has('steps'))
                                        <span class="helper-text" data-error="{{ $errors->first('steps') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s2">
                                    <input type="number" id="duration" name="duration" value="{{ old('duration', isset($challenge) ? $challenge->duration : null) }}" @if ($errors->has('duration')) class="invalid" @endif>
                                    <label for="duration">Duration</label>
                                    @if ($errors->has('duration'))
                                        <span class="helper-text" data-error="{{ $errors->first('duration') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s2">
                                    <input type="number" id="calories" name="calories" value="{{ old('calories', isset($challenge) ? $challenge->calories : null) }}" @if ($errors->has('calories')) class="invalid" @endif>
                                    <label for="calories">Calories</label>
                                    @if ($errors->has('calories'))
                                        <span class="helper-text" data-error="{{ $errors->first('calories') }}"></span>
                                    @endif
                                </div>
                                <div class="input-field col s2">
                                    <div class="switch">
                                        <label>
                                            Active
                                            <input type="checkbox" id="active" name="active" {{ old('active', isset($challenge) && $challenge->active == 1 ? true : false) ? 'checked="checked"' : '' }}>
                                            <span class="lever"></span>
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
        </div>
        <div class="challengeDashboard__right">
            <div class="content">
                <div class="row">
                    <div class="col s12">
                        <h2 class="left">Leader Board</h2>
                        <a href="#" class="right btn-all-view">View All</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="newChallenge_right">
                            @if(!isset($challenge) || $challenge === null)
                                <template>
                                    <h2>Waiting to build your challenge</h2>
                                    <img src = "/images/concierge/no-location.13c24c22.png" width=200px>

                                </template>
                            @else
                                <div class="card">
                                    <div class="card-content" :style="{backgroundImage:`linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.4) 59%, rgba(0,0,0,0) 100%), url(${challenge.photo})`}">
                                        <div>
                                            <span class="date-panel">@{{ startDate }}</span>

                                        </div>
                                        <div>
                                            <span class="card-title">@{{ challenge.title }}</span>
                                        </div>
                                        <div v-if="challenge.type_id == 1 && challenge.steps > 0">
                                            <span class="card-title">@{{ challenge.steps }} step challenge</span>
                                        </div>
                                        <div v-if="challenge.type_id == 1 && challenge.distance > 0">
                                            <span class="card-title">@{{ challenge.distance }} mile challenge</span>
                                        </div>

                                        <div v-if="challenge.type_id == 2">
                                            <span class="card-title">@{{ challenge.distance }} mile challenge</span>
                                        </div>

                                        <div v-if="challenge.type_id == 3 && challenge.duration > 0">
                                            <span class="card-title">@{{ Math.round(challenge.duration/60) }} hours challenge</span>
                                        </div>

                                        <div v-if="challenge.type_id == 4">
                                            <span class="card-title">@{{ Math.round(challenge.calories) }} calorie challenge</span>
                                        </div>
                                        <p class="challenge-description">@{{ challenge.desc }}</p>
                                        <div class="challenge-participants">
                                            <p class="participants-title">Participants</p>
                                            <div class="participants-photos">
                                                <div class="participant-photo" v-for="(member, index) in challenge.users">
                                                    <img :src="member.photo" v-if="index < 3">
                                                    <span v-else-if="index == 3">+@{{ challenge.users.length - 3 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    window.Laravel.member = {!! json_encode([
        'id'       => $member->id,
        'challenge' => isset($challenge)? $challenge : '',
    ]) !!};
    window.Laravel.isEnterprise = {!! $isEnterprise ? 1 : 0 !!};
</script>
<script src="{{ asset('js/pages/corporate/manage.js') }}?v=1.1"></script>
@endpush
