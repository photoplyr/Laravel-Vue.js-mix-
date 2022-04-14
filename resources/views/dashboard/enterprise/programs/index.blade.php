@extends('layouts.dashboard')

@section('content')
<div class="content" id="programList">
    <div class="row">
        <div class="input-field col s10">
            <input type="text" id="employeesSearch" v-model="search">
            <label for="employeesSearch">Search by Company Name or Program</label>
        </div>
        <div class="input-field col s2">
            <a class="waves-effect waves-light btn-small green right mainColorBackground" href="{{ route('enterprise.programs.add') }}"><i class="material-icons left">add</i>Program</a>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <table class="striped low-padding">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Program</th>
                        <th>Sector</th>
                        <th>Tier</th>
                        <th>Hourly Rate ($)</th>
                        <th>Daily Rate ($)</th>
                        <th>Rate ($)</th>
                        <th>Allowance</th>
                        <th>Restriction</th>
                        <th>Status</th>
                        <th width="150"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in list">
                        <td>@{{ item.company }}</td>
                        <td>@{{ item.name }}</td>
                        <td>@{{ item.sector }}</td>
                        <td>@{{ item.tier }}</td>
                        <td>@{{ item.hourly_rate }}</td>
                        <td>@{{ item.daily_rate }}</td>
                        <td>@{{ item.rate }}</td>
                        <td>@{{ item.allowance }}</td>
                        <td>@{{ item.restriction }}</td>
                        <td>@{{ item.status ? 'ACTIVE' : 'INACTIVE' }}</td>
                        <td class="right-align">
                            <ul class="actionsList no-margin">
                                <li>
                                    <a :href="'{{ route('enterprise.programs.edit', '') }}/'+item.id" class="btn-floating btn-small waves-effect waves-light green mainColorBackground"><i class="material-icons">edit</i></a>
                                    <a :href="'{{ route('enterprise.programs.disable', '') }}/'+item.id" class="btn-floating btn-small waves-effect waves-light green mainColorBackground"><i class="material-icons">remove</i></a>
                                </li>
                            </ul>
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
</div>
@endsection

@push('js')
<script>
window.Laravel.search_url         = "{{ route('enterprise.programs.search') }}";
window.Laravel.programs           = {!! json_encode($programs) !!};
window.Laravel.pages              = {!! json_encode($pages) !!};
</script>
<script src="{{ asset('js/pages/program.js') }}"></script>
@endpush
