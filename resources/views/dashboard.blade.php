@extends('layouts.master')

@section('styles')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@endsection
@section('page_title')
    Welcome back, {{ Auth::user()->name }}
@endsection

@section('content')
    <!-- Row start -->
    @if (session()->has('alert'))
        <div class="row gutters">
            <div class="alert {{ session()->get('alert-type') }}" role="alert">
                {{ session()->get('alert') }}
            </div>
        </div>
    @endif

    <div class="row gutters justify-content-center">
        <div class="col-12 col-lg-6 row gutters justify-content-between">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Yesterday Patients</h6><br>
                    <?php $yesterday = date('Y-m-d', strtotime('-1 days'));
                    $today = date('Y-m-d'); ?>
                    <h1><a href="{{ url('registered_patient_report') . '?from=' . $yesterday . '&to=' . $yesterday . '&doctor_id=0' }}"
                            target="_blank">{{ $yesterdayPatient }}</a></h1>
                    <div id="apexLineChartGradient3" class="pink-graph"></div>
                </div>

            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Today All Patients</h6><br>
                    <h1><a href="{{ url('registered_patient_report') . '?from=' . $today . '&to=' . $today . '&doctor_id=0' }}"
                            target="_blank">{{ $todayPatient }}</a></h1>
                    <div id="apexLineChartGradient" class="blue-graph"></div>
                </div>


            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Today OD paitents</h6><br>
                    <h1><a href="{{ url('registered_patient_report') . '?from=' . $today . '&to=' . $today . '&doctor_id=0' }}"
                            target="_blank">{{ $outDorPaitent }}</a></h1>
                    <div id="apexLineChartGradient" class="blue-graph"></div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Today IN Door paitents</h6><br>
                    <h1><a href="{{ url('registered_patient_report') . '?from=' . $today . '&to=' . $today . '&doctor_id=0' }}"
                            target="_blank">{{ $todayPatient - $outDorPaitent }}</a></h1>
                    <div id="apexLineChartGradient" class="blue-graph"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 row gutters justify-content-between">

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Previous Month Patients</h6>
                    <h1>{{ $previousMonthPatient }}</h1>
                    <div id="apexLineChartGradient4" class="lavandar-graph"></div>
                </div>

            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Current Month Patients</h6>
                    <h1>{{ $currentMonthPatient }}</h1>
                    <div id="apexLineChartGradient2" class="orange-graph"></div>
                </div>

            </div>


            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Current Year Patients</h6>
                    <h1>{{ $currentYearAllPatients }}</h1>
                    <div id="apexLineChartGradient4" class="lavandar-graph"></div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">

                <div class="daily-sales">
                    <h6>Total Patients</h6><br>
                    <h1>{{ $totalAllPatients }}</h1>
                    <div id="apexLineChartGradient4" class="lavandar-graph"></div>
                </div>
            </div>
        </div>



    </div>

    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Daily Patient Statistics</div>
                    <div class="card-sub-title">Overall Daily patients statistics of current month until today</div>
                </div>
                <div class="card-body  pb-0">
                    <div id="daily_patient_count_chart" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Monthly Patient Statistics</div>
                    <div class="card-sub-title">Overall Monthly patients statistics of current Year</div>
                </div>
                <div class="card-body  pb-0">
                    <div id="monthly_patient_count_chart" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Daily Income Statistics</div>
                    <div class="card-sub-title">Overall Sales Revenue and Profits of current Month until today.</div>
                </div>
                <div class="card-body pb-0">
                    <div id="daily_income_chart" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Monthly Income Statistics</div>
                    <div class="card-sub-title">Overall Sales Revenue and Profits of current year.</div>
                </div>
                <div class="card-body pb-0">
                    <div id="monthly_based_income_data" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        const todayDate = new Date();
        $.ajax({
            type: 'get',
            url: 'get_daily_patient_count',
            success: function(response) {
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawVisualization);

                function drawVisualization() {

                    var data = google.visualization.arrayToDataTable(response);
                    var options = {
                        title: 'Daily Patient Statistics',
                        vAxis: {
                            title: 'Statistics',
                            gridlines: {
                                count: 15
                            }
                        },
                        hAxis: {
                            title: '' + monthNames[todayDate.getMonth()] + ''
                        },
                        seriesType: 'bars',
                        series: {
                            5: {
                                type: 'line'
                            }
                        },
                    };
                    var chart = new google.visualization.ComboChart(document.getElementById(
                        'daily_patient_count_chart'));
                    chart.draw(data, options);
                }


            },
            error: function(e) {
                console.log("Chart Not loaded!");
            }
        });

        $.ajax({
            type: 'get',
            url: 'get_monthly_patient_count',
            success: function(response) {
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawVisualization);

                function drawVisualization() {

                    var data = google.visualization.arrayToDataTable(response);
                    var options = {
                        title: 'Monthly Patient Statistics',
                        vAxis: {
                            title: 'Statistics',
                            gridlines: {
                                count: 15
                            }
                        },
                        hAxis: {
                            title: '' + '' + ''
                        },
                        seriesType: 'bars',
                        series: {
                            5: {
                                type: 'line'
                            },
                            0: {
                                color: '#C414F0'
                            },
                        },

                    };
                    var chart = new google.visualization.ComboChart(document.getElementById(
                        'monthly_patient_count_chart'));
                    chart.draw(data, options);
                }


            },
            error: function(e) {
                console.log("Chart Not loaded!");
            }
        });

        $.ajax({
            type: 'get',
            url: 'get_daily_based_income_data',
            success: function(response) {
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawVisualization);

                function drawVisualization() {

                    var data = google.visualization.arrayToDataTable(response);
                    var options = {
                        title: 'Daily Income Statistics',
                        vAxis: {
                            title: 'Statistics',
                            gridlines: {
                                count: 15
                            }
                        },
                        hAxis: {
                            title: '' + monthNames[todayDate.getMonth()] + ''
                        },
                        seriesType: 'bars',
                        series: {
                            5: {
                                type: 'line'
                            }
                        },
                    };
                    var chart = new google.visualization.ComboChart(document.getElementById(
                        'daily_income_chart'));
                    chart.draw(data, options);
                }


            },
            error: function(e) {
                console.log("Chart Not loaded!");
            }
        });

        $.ajax({
            type: 'get',
            url: 'get_monthly_based_income_data',
            success: function(response) {
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawVisualization);

                function drawVisualization() {

                    var data = google.visualization.arrayToDataTable(response);
                    var options = {
                        title: 'Daily Income Statistics',
                        vAxis: {
                            title: 'Statistics',
                            gridlines: {
                                count: 15
                            }
                        },
                        hAxis: {
                            title: '' + '' + ''
                        },
                        seriesType: 'bars',
                        series: {
                            5: {
                                type: 'line'
                            }
                        },
                    };
                    var chart = new google.visualization.ComboChart(document.getElementById(
                        'monthly_based_income_data'));
                    chart.draw(data, options);
                }


            },
            error: function(e) {
                console.log("Chart Not loaded!");
            }
        });
    </script>
@endsection
