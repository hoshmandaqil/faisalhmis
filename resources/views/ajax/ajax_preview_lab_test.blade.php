
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Patient User</title>
    <!-- Bootstrap css -->
    {{--    <style  media='screen,print'>--}}
    {{--        <?php include(public_path('assets/css/bootstrap.min.css'));?>--}}
    {{--    </style>--}}
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}"  media='all'>
   <style>
       @media print {
           #labDIv {
               margin-top: 250px;
           }
       }

   </style>
</head>
<body>
<main>

    <div class="col-8 offset-4" id="labDIv">
        <div class="table-responsive table-borderless table-light">
            <table class="table medicine_table">
                <thead>
                <th width="30%">Lab Test Name</th>
                <th width="20%">Price</th>
                <th width="50%" >Remark</th>
                </thead>
                <tbody>
                <?php $grandTotal = 0; $totalDiscount = 0; $hasFile = false;?>
                @foreach($labs as $lab)
                    <tr>
                        <td>{{ucfirst($lab->testName->dep_name)}}</td>
                        <td>{{$lab->price}}</td>
                        <td class="remark">{{$lab->result}}</td>
                        <?php
                        $grandTotal += $lab->price;
                        $discountForTest = ($lab->discount * $lab->price)/100;
                        $totalDiscount += $discountForTest;
                          if($lab->file != NULL){
                            $hasFile = true;
                        }
                        ?>
                    </tr>
                @endforeach
                <tr>
                    <td style="border-top: 1px solid lightgray">Total:</td>
                    <td style="border-top: 1px solid lightgray">{{$grandTotal}}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Discount <br>
                        (تخفیف ویژه):</td>
                    <td>{{$totalDiscount}}</td>
                    <td></td>
                    <td></td>

                </tr>
                <tr>
                    <td><b>Payable:</b></td>
                    <td><b>{{$grandTotal - $totalDiscount}} AFN</b></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
            @if($hasFile)
        <div class="alert alert-info d-print-none">Files are Uploaded already!</div>
        @endif
    </div>



</main>
<div class="submit-section">
    <button class="btn-sm hidden-print d-print-none" type="button" onclick="window.print();"> Print <i class="icon icon-print"></i></button>
</div>
<br class="d-print-none">
</body>
</html>

