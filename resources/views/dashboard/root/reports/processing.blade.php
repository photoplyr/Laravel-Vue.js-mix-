@extends('layouts.dashboard')

@section('content')
<style>

</style>
<div class="wrapper" id="billingAccount">
    <div class="pageLoading" v-if="isLoad">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="billingAccountHeader">
        <div class="stylizedSelect left" ref="stylizedSelectYear">
            <div class="stylizedSelectPlaceholder" @click="toggleYearSelect">@{{ selectedYear.id }}</div>
            <ul :class="{'active': selectYearOpen}">
                <li class="option" v-for="year in years" :class="{'active': year.active}" @click="selectYear(year.id)">@{{ year.id }}</li>
            </ul>
        </div>
        <div class="stylizedSelect left" ref="stylizedSelectMonth">
            <div class="stylizedSelectPlaceholder" @click="toggleMonthSelect">@{{ selectedMonth.title }}</div>
            <ul :class="{'active': selectMonthOpen}">
                <li class="option" v-for="month in months" :class="{'active': month.active}" @click="selectMonth(month.id)">@{{ month.title }}</li>
            </ul>
        </div>
        <div class="stylizedSelect left" ref="stylizedSelectProgram">
            <div class="stylizedSelectPlaceholder" @click="toggleProgramSelect">@{{ selectedProgram.title }}</div>
            <ul :class="{'active': selectProgramOpen}">
                <li class="option" v-for="program in programs" :class="{'active': program.active}" @click="selectProgram(program.id)">@{{ program.title }}</li>
            </ul>
        </div>
    </div>

    <h1 class="newTitle">Transactions</h1>

    <div class="billingAccountInfo" style="display: flex">
        <div class="billingAccountInfoLeft">
            <span>Total Members</span>
            <div class="totalBlock">
                <span>Total</span>
                <i>@{{ info.total }}</i>
            </div>
            <div class="infoBlock">
                <span>female</span>
                <i>@{{ femalePercentage }}%</i>
            </div>
            <div class="infoBlock">
                <span>male</span>
                <i>@{{ malePercentage }}%</i>
            </div>
            <div class="infoBlock">
                <span>avg.age</span>
                <i>@{{ info.age }}</i>
            </div>
        </div>
    </div>

    <template v-if="transfers.length == 0">
        <center><img src = "/images/concierge/no-location.13c24c22.png" width=200px><br><div style = "font-size: 20px;">No Transactions</div></center>
    </template>
    <template v-if="transfers.length > 0">
        <table class="striped highlight low-padding">
            <thead>
                <tr>
                    <th class="padding-left-20">Program</th>
                    <th>Date</th>
                    <th class="right-align">Visits</th>
                    <th class="right-align">Cost</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="transfer in transfers">
                    <td class="padding-left-20">@{{ transfer.name }}</td>
                    <td>@{{ transfer.date }}</td>
                    <td class="right-align">@{{ transfer.visits }}</td>
                    <td class="right-align">$@{{ transfer.cost.toFixed(2) }}</td>
                </tr>
            </tbody>
        </table>
    </template>
</div>
@endsection

@push('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
window.Laravel.billingAccount = {!! json_encode([
    'isEnterprise' => false,
    'isActivity'   => true,
    'programs'    => $programs,
    'transfers'    => $transfers,
    'years'        => $years,
    'months'       => $months,
    'info'         => $info,
]) !!};
</script>
<script src="{{ asset('js/pages/root/processing.js') }}?v=1.1"></script>
@endpush
