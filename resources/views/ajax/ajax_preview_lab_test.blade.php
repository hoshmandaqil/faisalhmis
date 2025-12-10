
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
                <?php $grandTotal = 0; $totalDiscount = 0; $grandTotalAfterDiscount = 0; $hasFile = false;?>
                @foreach($labs as $lab)
                    <?php
                        // The price field now contains the original price
                        $originalPrice = (float) $lab->price;
                        $discountPercentage = (float) ($lab->discount ?? 0);

                        // Calculate discount amount from percentage
                        $discountForTest = ($originalPrice * $discountPercentage) / 100;

                        // Calculate payable amount (original price - discount)
                        $priceAfterDiscount = $originalPrice - $discountForTest;

                        $grandTotal += $originalPrice;
                        $totalDiscount += $discountForTest;
                        $grandTotalAfterDiscount += $priceAfterDiscount;

                        if($lab->file != NULL){
                            $hasFile = true;
                        }
                    ?>
                    <tr>
                        <td>{{ucfirst($lab->testName->dep_name)}}</td>
                        <td>{{round($priceAfterDiscount)}}</td>
                        <td class="remark">{{$lab->result}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="border-top: 1px solid lightgray">Total:</td>
                    <td style="border-top: 1px solid lightgray">{{round($grandTotal)}} AFN</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Discount <br>
                        (تخفیف ویژه):</td>
                    <td>{{round($totalDiscount)}} AFN</td>
                    <td></td>
                    <td></td>

                </tr>
                <tr>
                    <td><b>Payable:</b></td>
                    <td><b>{{round($grandTotalAfterDiscount)}} AFN</b></td>
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

