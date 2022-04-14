<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

       <link href="https://uploads-ssl.webflow.com/5eb1d6491b62d572c3e6b797/5eb1ff4d61d24fddf72da815_ic_launcher.png" rel="shortcut icon" type="image/x-icon">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/activityDashboard.css') }}" rel="stylesheet">
        @stack('css')
    </head>
    <body>
        <div class="main-activity">
            <nav class="header">
                <div class="nav-wrapper">
                    <div class = "logo"><img src = "/images/concierge/card_logo.png" height ="40"></div>
                </div>
            </nav>   
            <div class="parent">
                <div class="content">
                    <div class="tv-image">
                        <img src = "/images/tv.png" >
                    </div>                
                    <div class="tv-activity">            
                        <div class="dashboard-title">ACTIVITY DASHBOARD</div>
                        <div class="wrapper" id="tv-billingAccount"> 
                            <div class="tv-billingAccountInfo">        
                                <div class="chartBlock" style="flex-grow: 1;display:flex;">
                                    <div id="minsByMonth" style="flex: 1 0 50%"></div>
                                    <div id="dailyByHour" style="flex: 1 0 50%"></div>
                                </div> 
                            </div>   
                        </div>
                        @foreach ($records as $item)
                            <div class="activity">
                                <span class="activity-history">{{$item->concat}}</span>
                                <span class="activity-date">
                                    @php
                                        $time1 = new DateTime($item->timestamp);
                                        $now = new DateTime();
                                        $interval = $time1->diff($now,true);

                                        if ($interval->y) echo $interval->y . ' years ago';
                                        elseif ($interval->m) echo $interval->m . ' months ago';
                                        elseif ($interval->d) echo $interval->d . ' days ago';
                                        elseif ($interval->h) echo $interval->h . ' hours ago';
                                        elseif ($interval->i) echo $interval->i . ' minutes ago';
                                        else echo "less than 1 minute";
                                    @endphp
                                </span>                  
                            </div>       
                        @endforeach
                    </div> 
                </div>                               
            </div> 
        </div>                
        @php
            extract($data);
        @endphp
        <!-- Scripts -->
        <script>
            window.Laravel = {}
            window.auth = {!!
                json_encode([
                    'csrfToken' => csrf_token(),
                    'debug'     => config('app.env') == 'production' ? false : true,
                ])
            !!};
        </script>
        <script src="https://code.highcharts.com/highcharts.js"></script> 
        <script>
            setInterval(function()
            {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $.ajax({
                    type: "get",
                    url: "/TV/activity",
                    success:function(data)
                    {
                        //console.log the response
                        console.log(data);
                    }
                });
            }, 10000); //10000 milliseconds = 10 seconds
        </script>      
        <script>
            window.Laravel.billingAccount = {!! json_encode([
            'isEnterprise' => false,
            'isActivity'   => true,
            'companies'    => $companies,
            'transfers'    => $transfers,
            'years'        => $years,
            'months'       => $months,
            'info'         => $info,
            'issuers'      => [],
            'g1_months'       => $minMonths,
            'g1_mins'         => $monthlyMins,
            'g2_dailyByHour'  => $dailyByHour,
        ]) !!};
        </script>
        <script src="{{ asset('js/pages/tvactivity.js') }}?v=1.1"></script>
        @stack('js')
    </body>
</html>