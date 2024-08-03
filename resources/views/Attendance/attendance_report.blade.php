@extends('layouts.master')

@section('page_title')
    Attendance Report
@endsection

@section('page-action')

  @if(in_array('attendance_approve', $user_permissions))
  <a href="/approveAttendance?from={{ $from }}&to={{ $to }}"
      class="btn btn-danger btn-sm pull-right hidden-print" onclick="return confirm('Are you sure you want to Approve this Attendance?')">
      Approve This Attendance
  </a>
  @endif
    <button onclick="javascript:window.print();" class="btn btn-info btn-sm pull-success hidden-print">
        Print
    </button>

@endsection
@section('styles')
    <style>
        .redSpan {
            border: 2px solid red;
            border-radius: 50%;
            padding: 5px;
        }

        .adjustAtt {
            margin-left: 10px !important;
            cursor: pointer;
            color: blue;
            visibility: hidden;
            font-size: 12px;
        }

        .adjustComment {
            display: none;
            font-size: 12px;
        }

        .time_td:hover i {
            visibility: visible;
        }

        .time_td:hover span.adjustComment {
            display: inline;
        }


        .greenSpan {
            border: 2px solid #27e027;
            border-radius: 50%;
            padding: 5px;
        }

        .blueSpan {
            border: 2px solid #2a27e0;
            border-radius: 50%;
            padding: 5px;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: white !important;
        }

        .table tbody tr td {
            border: 1px solid black !important
        }

        .modal-body input,
        .modal-body select {
            height: 30px !important;
        }

        .modal-body div.form-group {
            margin-top: -10px !important;
        }

    </style>
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
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <h4><b> Staff On-Duty/Off-Duty <span>({{ $from }} - {{ $to }})</span></b></h4>
        </div>
    </div>
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="">
                <table class="table">
                    <thead>
                    </thead>
                    <tbody>
                        @foreach ($returnData as $userId => $report)
                            @foreach ($report as $name => $data)
                                <tr class="first-row">
                                    <td colspan="2" class="text-center">
                                        <h4><b>{{ ucfirst($name) }} ({{ $userId }})</b></h4>
                                    </td>
                                </tr>
                                @foreach ($data as $date => $dates)
                                    <tr style="display: inline-grid">
                                        <td class="text-center" style="width: 130px; font-size: 12px">
                                            {{ date('d', strtotime($date)) }} {{ date('D', strtotime($date)) }}
                                            ({{ date('M-d', strtotime($date)) }})
                                        </td>
                                        <td class="text-center time_td"
                                            style="width: 130px; font-size: 10px; {{ date('D', strtotime($date)) == 'Fri' || in_array($date, $holidays) ? 'background-color: #ffc8ee' : '' }} ">
                                            {{ $dates['checkIn'] }}

                                            @if (($dates['checkIn'] == null || $dates['checkOut'] == null) && date('D', strtotime($date)) != 'Fri' && ($dates['absent'] == 1 || $dates['absent'] == 0.5) && $dates['comment'] == null)
                                                <span class="redSpan" id="{{ $dates['databaseId'] }}">-</span>
                                            @elseif(($dates['checkIn'] == '00:00:00' || $dates['checkOut'] == '00:00:00') && date('D', strtotime($date)) != 'Fri' && ($dates['absent'] == 0) && $dates['comment'] != null)
                                                <span class="greenSpan" id="{{ $dates['databaseId'] }}">-</span>
                                                <span class="adjustComment">{{ $dates['comment'] }}</span>
                                            @elseif(($dates['checkIn'] == '00:00:00' || $dates['checkOut'] == '00:00:00') && date('D', strtotime($date)) != 'Fri' && ($dates['absent'] == 1 || $dates['absent'] == 0.5) && $dates['comment'] != null)
                                                <span class="blueSpan" id="{{ $dates['databaseId'] }}">-</span>
                                                <span class="adjustComment">{{ $dates['comment'] }}</span>
                                            @elseif (date('D', strtotime($date)) != 'Fri' && ($dates['absent'] == 1 || $dates['absent'] == 0.5))
                                                <span class="redSpan" id="{{ $dates['databaseId'] }}">-</span>
                                                <span class="adjustComment">{{ $dates['comment'] }}</span>
                                            @else
                                                <span>-</span>
                                                <span class="adjustComment">{{ $dates['comment'] }}</span>
                                            @endif
                                            {{ $dates['checkOut'] }}
                                            @if ($dates['approved'] == 0 && in_array('attendance_modify', $user_permissions))
                                                <i class="icon icon-edit adjustAtt hidden-print"
                                                    onclick="justifyDay({{ $dates['databaseId'] }})"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Justify this Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" id="rowDbId">
                        <div class="form-group">
                            <label>Remark (Specify the Adjust Details):</label>
                            <textarea class="form-control" name="remark" id="remark"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="isPresent" id="isPresent">
                                <label class="custom-control-label" for="isPresent">Check this if it is a Present
                                    Day</label>
                            </div>
                        </div>
                    </form>
                    <div class="submit-section">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary submit-btn btn-sm" onclick="saveJustifyReason()">Submit</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function justifyDay(id) {
            $('#exampleModal').modal('show');
            $('input#rowDbId').val(id);
        }

        function saveJustifyReason() {
            var dbId = $('input#rowDbId').val();
            var message = $('textarea#remark').val();
            var isPresent = false;
            if ($('input#isPresent').is(':checked')) {
                isPresent = true;
            }
            if (!$.trim(message)) {
                alert("you should wite a reason for present!");
                return;
            }
            // alert(isPresent)
            $.ajax({
                type: 'POST',
                url: 'saveJustifyReason',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: dbId,
                    message: message,
                    isPresent: isPresent
                },
                success: function(response) {
                    if (response == "true") {
                        $('span#' + dbId).removeClass('redSpan');
                        $('span#' + dbId).removeClass('blueSpan');
                        $('span#' + dbId).removeClass('greenSpan');
                        if (message !== "" && isPresent) {
                            $('span#' + dbId).addClass('greenSpan');
                        } else {
                            $('span#' + dbId).addClass('blueSpan');
                        }
                        $('#exampleModal').modal('hide');
                    } else {
                        alert("Something Went Wrong, Please Try again!");
                    }
                }
            });
        }
    </script>
@endsection
