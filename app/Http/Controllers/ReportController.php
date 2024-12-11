<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseItem;
use App\Models\Expense\ExpenseSlip;
use App\Models\LabDepartment;
use App\Models\LaboratoryPatientLab;
use App\Models\MainLabDepartment;
use App\Models\MiscellaneousIncome;
use App\Models\Patient;
use App\Models\PatientIPD;
use App\Models\PatientPharmacyMedicine;
use App\Models\PayrollPayment;
use App\Models\Pharmacy;
use App\Models\RequestedMedicine;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{

    public function dashboard()
    {
        $today = today()->format('Y-m-d');
        $currentYear = date('Y');
        $currentMonth = date('m');

        $previousDay = Carbon::now()->subDays(1)->format('Y-m-d');
        $previousMonthDate = Carbon::now()->subMonthNoOverflow()->format('Y-m');
        $previousMonth = explode('-', $previousMonthDate)[1];
        $previousMonthYear = explode('-', $previousMonthDate)[0];

        // dd($today, $currentMonth, $currentYear, $previousDay, $previousMonth, $previousMonthYear);

        $todayPatient = DB::table('patients')->whereDate('created_at', $today)->count();
        $outDorPaitent = DB::table('patients')->where('doctor_id', 28)->whereDate('created_at', $today)->count();
        $yesterdayPatient = DB::table('patients')->whereDate('created_at', $previousDay)->count();
        $currentMonthPatient = DB::table('patients')->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)->count();
        $previousMonthPatient = DB::table('patients')->whereYear('created_at', $previousMonthYear)
            ->whereMonth('created_at', $previousMonth)->count();
        $totalAllPatients = DB::table('patients')->count();
        $currentYearAllPatients = DB::table('patients')->whereYear('created_at', $currentYear)->count();

        return view('dashboard', compact('todayPatient', 'yesterdayPatient', 'currentMonthPatient', 'previousMonthPatient', 'totalAllPatients', 'currentYearAllPatients', 'outDorPaitent'));
    }


    public function date_wise_procurement_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $type = $_GET['type'] ?? '';
        $pharmacies = [];
        $created_users = Pharmacy::groupBy('created_by')->select('created_by')->with('user')->get()->toArray();
        $pharmacyInvoiceNumbers = Pharmacy::groupBy('invoice_no')->select('invoice_no')->get()->toArray();
        $pharmacyVendors = Pharmacy::groupBy('supplier_id')->select('supplier_id')->with('supplier')->get()->toArray();
        $theads = [];
        //        if ($from == null && $to == null && $type == null){
        //            return view('report.date_wise_procurement_report', compact('pharmacies','from', 'to', 'type', 'created_users', 'pharmacyInvoiceNumbers', 'pharmacyVendors', 'theads'));
        //        }

        $query = Pharmacy::query();
        if ($from != null) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to != null) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($type == 'general') {
            $theads = ['Medicine Name', 'Purchase Price', 'QTY', 'Total Purchase', 'Sale Price', 'Total Sale', 'Invoice No#', 'Vendor', 'Created By'];
        }
        if ($type == 'invoice_base') {
            $theads = ['Invoice No#', 'Medicine Name', 'Purchase Price', 'QTY', 'Total Purchase', 'Sale Price', 'Total Sale', 'Vendor', 'Created By'];
            $query->where('invoice_no', $_GET['invoice_base']);
        }
        if ($type == 'user_base') {
            $theads = ['Created By', 'Medicine Name', 'Purchase Price', 'QTY', 'Total Purchase', 'Sale Price', 'Total Sale', 'Vendor', 'Invoice No#'];
            $query->where('created_by', $_GET['user_base']);
        }
        if ($type == 'vendor_base') {
            $theads = ['Vendor', 'Medicine Name', 'Purchase Price', 'QTY', 'Total Purchase', 'Sale Price', 'Total Sale', 'Invoice No#', 'Created By'];
            $query->where('supplier_id', $_GET['vendor_base']);
        }
        $pharmacies = $query->latest()->get();

        return view('report.date_wise_procurement_report', compact('pharmacies', 'from', 'to', 'type', 'created_users', 'pharmacyInvoiceNumbers', 'pharmacyVendors', 'theads'));
    }

    public function date_wise_sale_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $type = $_GET['type'] ?? '';
        $doc = $_GET['doc'] ?? '';
        $pharmacies = [];

        $doctors = User::where('type', 3)->where('status', 1)->select('id', 'name')->get();

        if ($from != null || $to != null) {
            $query = PatientPharmacyMedicine::query();
        }

        if ($from != null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to != null) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($doc != null) {
            $query->whereHas('patient', function ($q) use ($doc) {
                $q->where('doctor_id', $doc);
            });
        }

        if ($from != null || $to != null) {
            $pharmacies = $query->select('patient_id', 'medicine_id', 'quantity', 'unit_price', 'created_by')->with('patient:id,patient_name,patient_generated_id')
                ->with('patient.createdBy')->with('medicine:id,medicine_name')->with('user:id,name')->latest()->get()->groupBy('patient_id');
        }

        return view('report.date_wise_sale_report', compact('pharmacies', 'from', 'to', 'doctors'));
    }

    public function available_stock_report()
    {
        $display = Pharmacy::select(
            'medicine_id',
            'id',
            'returned',
            'expired',
            'sale_price',
            DB::raw('SUM(quantity) AS totalPurchaseQTY'),
            DB::raw('SUM(quantity * purchase_price) AS totalPurchasePrice')
        )->groupBy('medicine_id')->latest()->with('medicineName', 'medicineName.patientPharmacyMedicines:medicine_id,quantity,unit_price')->lazy();

        return view('report.available_stock_report', compact('display'));
    }

    public function short_pharmacy_report()
    {
        $availableLessThanTenPercent = [];
        $pharmacies = Pharmacy::groupBy('medicine_id')
            ->select('medicine_id', 'id', DB::raw('SUM(quantity) AS totalPurchaseQTY'))
            ->with('medicineName', 'medicineName.patientPharmacyMedicines')->get();
        foreach ($pharmacies as $pharmacy) {
            $totalPurchaseQTY = $pharmacy->totalPurchaseQTY;
            $totalSaleQTY = $pharmacy->medicineName->patientPharmacyMedicines->sum('quantity');
            $available = $totalPurchaseQTY - $totalSaleQTY;
            $percentage = 10;
            $tenPercentOfMedicine = ($percentage / 100) * $totalPurchaseQTY;
            if ($available <= $tenPercentOfMedicine) {
                $availableLessThanTenPercent[$pharmacy->id]['medicine_name'] = $pharmacy->medicineName->medicine_name;
                $availableLessThanTenPercent[$pharmacy->id]['available'] = $available;
            }
        }
        return view('report.short_pharmacy_report', compact('availableLessThanTenPercent'));
    }

    public function expired_medicine_report()
    {
        $today = date('Y-m-d');
        //increment 15 days
        $upTo15Days = date('Y-m-d', strtotime($today . "+ 15 days"));
        $expiredMedicines = Pharmacy::where('exp_date', '<', $upTo15Days)->paginate(100);
        return view('report.expired_medicine_report', compact('expiredMedicines'));
    }

    public function pharmacy_percentage_report()
    {
        $salePercentage = $_GET['percentage'] ?? '';
        $pharmacySalePercentages = Pharmacy::groupBy('sale_percentage')->select('sale_percentage')->get();
        $pharmacies = [];
        if ($salePercentage != null) {
            $query = Pharmacy::query();
            $query->where('sale_percentage', $salePercentage);
            $pharmacies = $query->latest()->get();
        }
        return view('report.pharmacy_percentage_report', compact('pharmacies', 'salePercentage', 'pharmacySalePercentages'));
    }

    public function requested_medicine_report()
    {
        $requestedUsers = RequestedMedicine::groupBy('created_by')->select('created_by')->with('user')->get()->toArray();
        $requestedMedicine = RequestedMedicine::latest()->select('medicine_name', 'created_by', 'id')->with('user')->paginate(100);
        $doctorId = $_GET['doctor_id'] ?? '';
        if ($doctorId != NULL) {
            $requestedMedicine = RequestedMedicine::where('created_by', $doctorId)->latest()->select('medicine_name', 'created_by', 'id')->with('user')->paginate(100);
        }

        return view('report.requested_medicine_report', compact('requestedMedicine', 'doctorId', 'requestedUsers'));
    }

    public function medication_report()
    {
        $editedMedicines = Patient::wherehas('medicines')->select('id', 'patient_name', 'patient_generated_id')
            ->with('medicines', 'pharmacyMedicines', 'medicines.medicine', 'pharmacyMedicines.medicine')
            ->paginate(100);
        return view('report.medication_report', compact('editedMedicines'));
    }

    public function laboratory_sale_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $department_id = $_GET['department'] ?? '';
        $doctor_id = $_GET['doctor'] ?? '';
        $labMainDepartments = MainLabDepartment::select('id', 'dep_name')->get();
        $labMainDepartmentName = MainLabDepartment::where('id', $department_id)->value('dep_name');
        $doctors = User::where('type', 3)->where('status', 1)->select('id', 'name')->get();
        $labSalePatients = [];
        if ($from != null && $to != NULL) {
            $labSalePatients = Patient::whereHas('laboratoryTests',  function ($q) use ($from, $to) {
                $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
            })->select('id', 'patient_name', 'patient_generated_id', 'doctor_id', 'created_by')
                ->with(['doctor', 'createdBy', 'laboratoryTests.testName', 'laboratoryTests.testName.mainDepartment', 'laboratoryTests' => function ($q) use ($from, $to) {
                    $q->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                }]);

            if ($department_id) {
                $labSalePatients = $labSalePatients->with(['laboratoryTests' => function ($q) use ($from, $to) {
                    $q->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                }, 'laboratoryTests.testName' => function ($q) use ($department_id) {
                    $q->where('main_dep_id', $department_id);
                }]);
            }
            if ($doctor_id) {
                $labSalePatients = $labSalePatients->where('doctor_id', $doctor_id);
            }

            $labSalePatients = $labSalePatients->get();
        }
        return view('report.laboratory_sale_report', compact('labSalePatients', 'from', 'to', 'labMainDepartments', 'department_id', 'labMainDepartmentName', 'doctors', 'doctor_id'));
    }

    public function laboratory_tests_report()
    {
        $departmentName = $_GET['department'] ?? '';
        $mainLabDepartments = MainLabDepartment::select('dep_name', 'id')->latest()->get();
        $labTests = [];
        if ($departmentName != NULL) {
            $labTests = LabDepartment::where('main_dep_id', $departmentName)->latest()->with('mainDepartment')->get();
        }
        return view('report.laboratory_tests_report', compact('labTests', 'departmentName', 'mainLabDepartments'));
    }

    public function ipd_patient_report()
    {
        $from = $_GET['from'] ?? '';
        $until = $_GET['to'] ?? '';
        $charge_type = $_GET['charge_type'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $ipdPatients = [];

        if ($from != null && $until != NULL) {
            $ipdPatients = Patient::wherehas('ipds', function ($q) use ($from, $until, $charge_type, $doctor_id) {
                if ($charge_type == 1) {
                    $q->where('status', 0)->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $until);
                } elseif ($charge_type == 2) {
                    $q->where('status', 1)->whereDate('discharge_date', '>=', $from)
                        ->whereDate('discharge_date', '<=', $until);
                } else {
                    $q->whereDate('discharge_date', '>=', $from)
                        ->whereDate('discharge_date', '<=', $until);
                }
            });

            if ($doctor_id != 0) {
                $ipdPatients =  $ipdPatients->where('doctor_id', $doctor_id)->latest()->with(['ipds' => function ($q) use ($from, $until, $charge_type, $doctor_id) {
                    if ($charge_type == 1) {
                        $q->where('status', 0)->whereDate('created_at', '>=', $from)
                            ->whereDate('created_at', '<=', $until);
                    } elseif ($charge_type == 2) {
                        $q->where('status', 1)->whereDate('discharge_date', '>=', $from)
                            ->whereDate('discharge_date', '<=', $until);
                    } else {
                        $q->where(function ($q) use ($from, $until) {
                            $q->whereDate('discharge_date', '>=', $from)
                                ->whereDate('discharge_date', '<=', $until);
                        })->orwhere(function ($q) use ($from, $until) {
                            $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $until);
                        });
                    }
                }, 'ipds.floor', 'createdBy', 'ipds.dischargedBy'])->get();
            } else {
                $ipdPatients =  $ipdPatients->latest()->with(['ipds' => function ($q) use ($from, $until, $charge_type, $doctor_id) {
                    if ($charge_type == 1) {
                        $q->where('status', 0)->whereDate('created_at', '>=', $from)
                            ->whereDate('created_at', '<=', $until);
                    } elseif ($charge_type == 2) {
                        $q->where('status', 1)->whereDate('discharge_date', '>=', $from)
                            ->whereDate('discharge_date', '<=', $until);
                    } else {
                        $q->where(function ($q) use ($from, $until) {
                            $q->whereDate('discharge_date', '>=', $from)
                                ->whereDate('discharge_date', '<=', $until);
                        })->orwhere(function ($q) use ($from, $until) {
                            $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $until);
                        });
                    }
                }, 'ipds.floor', 'createdBy', 'ipds.dischargedBy'])->get();
            }
        }

        $doctors = User::where('type', 3)->select('id', 'name')->get();
        return view('report.ipd_patient_report', compact('ipdPatients', 'from', 'until', 'charge_type', 'doctors', 'doctor_id'));
    }

    public function general_profits_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $registered_by  = $_GET['registered_by'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';

        $kblTotalIncomes = 0;
        $kblTotalExpense = 0;

        $days = [];
        $data = [];
        $seenPatientsByDoctor = [];
        $allIncomes = 0;
        $allExpenses = 0;
        $otherIncome = 0;
        $totalPayrollPayment = 0;

        // Extract registered by list
        $patientsRegisteredBy = Patient::where('created_by', '!=', 'NULL')->groupBy('created_by')->with('createdBy')->get()->pluck('createdBy.name', 'created_by');

        // Extract doctors list
        $doctors = User::where('type', 3)->where('status', 1)->select('id', 'name')->get();

        if ($from != null && $to != NULL) {

            // This is Overall Profit and expenses Codes.
            // We are getting expenses and incomes from Kblhms too as an API.

            // // Get all expenses from kblhms
            // $client = new \GuzzleHttp\Client(['verify' => false]);
            // $allExpensesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_expenses");
            $kblAllExpenses = ExpenseItem::whereHas('slip', function ($q) {
                $q->whereNull('deleted_at');
            })->sum('amount');
            $otherIncome = MiscellaneousIncome::whereNull('deleted_at')->sum('amount');
            $allExpenses += $kblAllExpenses;

            // Get all income from kblhms
            // $allIncomesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_incomes");
            // $kblAllIncomes = json_decode($allIncomesKbl->getBody()->getContents());
            // $allIncomes += $kblAllIncomes;

            // OPD
            $allIncomes += Patient::sum('OPD_fee');
            //Get medicines sales
            $allMedicineProfitQuery = PatientPharmacyMedicine::select(DB::raw('SUM(quantity*unit_price) as medicineProfit'))->first();
            $allIncomes += $allMedicineProfitQuery->medicineProfit;

            //Get sum laboratory
            $allLabQuery = LaboratoryPatientLab::select(DB::raw('SUM(price-((discount * price)/100)) as labProfit'))->first();
            $allIncomes += $allLabQuery->labProfit;

            // Get sum IPD income
            $allIPDQuery = PatientIPD::where('status', 1)->whereNotNull('discharge_date')->select('price', 'discount', 'discharge_date', 'created_at')->lazy();
            foreach ($allIPDQuery  as $ipdProfit) {
                $totalPrice = 0;
                $totalDiscount = 0;
                $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit->created_at)));
                $discharge_date = $ipdProfit->discharge_date;
                $ipdDays = $register_date->diffInDays($discharge_date);

                for ($i = 1; $i <= $ipdDays; $i++) {
                    $totalPrice += $ipdProfit->price;
                    $discountForTest = ($ipdProfit->discount * $ipdProfit->price) / 100;
                    $totalDiscount += $discountForTest;
                }
                $allIncomes += $totalPrice - $totalDiscount;
            }

            $labMainDepartments = MainLabDepartment::select('id', 'dep_name')->with('thisDepTests')->lazy();

            /// This is Datewise Profit and expenses Codes.

            $period = CarbonPeriod::create($from, $to);
            // Convert the period to an array of dates
            $datesBetween = $period->toArray();
            foreach ($datesBetween as $date) {
                $totalProfitIPD = 0;
                $totalIPDPatients = 0;
                $totalProfitMedicine = 0;
                $day = $date->format('Y-m-d');
                $dayDate = explode('-', $day)[2];
                array_push($days, $dayDate);

                if ($registered_by != 0 || $doctor_id != 0) {
                    $patientQuery = DB::table('patients')->whereDate('created_at', $day);
                    if ($registered_by != 0) {
                        $data['Patients'][$dayDate] = $patientQuery->where('created_by', $registered_by)->count();
                    }
                    if ($doctor_id != 0) {
                        $data['Patients'][$dayDate] = $patientQuery->where('doctor_id', $doctor_id)->count();
                    }
                } else {
                    $data['Patients'][$dayDate] = DB::table('patients')->whereDate('created_at', $day)->count();
                }


                if ($registered_by != 0 || $doctor_id != 0) {
                    $patientQueryForOPD = DB::table('patients')->whereDate('created_at', $day);
                    if ($registered_by != 0) {
                        $data['OPD Incomes'][$dayDate] = $patientQueryForOPD->where('created_by', $registered_by)->sum('OPD_fee');
                    }
                    if ($doctor_id != 0) {
                        $data['OPD Incomes'][$dayDate] = $patientQueryForOPD->where('doctor_id', $doctor_id)->sum('OPD_fee');
                    }
                } else {
                    $data['OPD Incomes'][$dayDate] = DB::table('patients')->whereDate('created_at', $day)->sum('OPD_fee');
                }


                // $kblData = $client->get("https://kblhms.rokhan.co/api_get_appointments", [
                //     "query" => ['from' => $day, 'to' => $day]
                // ]);
                // $kblTotalIncomes = json_decode($kblData->getBody()->getContents());
                // $data['OPD Incomes'][$dayDate] = $kblTotalIncomes->appointmentsIncome;
                // $data['Other Incomes'][$dayDate] = $kblTotalIncomes->otherIncomes;

                // IPD Report


                $ipdProfitQuery = Patient::wherehas('ipd', function ($q) use ($day) {
                    $q->where('status', 1)->whereDate('discharge_date', '=', $day);
                });

                if ($registered_by != 0) {
                    $ipdProfitQuery = $ipdProfitQuery->where('created_by', $registered_by);
                }
                if ($doctor_id != 0) {
                    $ipdProfitQuery = $ipdProfitQuery->where('doctor_id', $doctor_id);
                }

                $ipdProfitQuery = $ipdProfitQuery->latest()->with(['ipd' => function ($q) use ($day) {
                    $q->where('status', 1)->whereDate('discharge_date', '=', $day);
                }])->get();

                $data['Total Patients Per Day']['IPD'][$dayDate][0] = $totalIPDPatients;

                foreach ($ipdProfitQuery as $ipdProfit) {
                    $totalPrice = 0;
                    $totalDiscount = 0;
                    $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit->ipd->created_at)));
                    $discharge_date = $ipdProfit->ipd->discharge_date;
                    $ipdDays = $register_date->diffInDays($discharge_date);

                    for ($i = 1; $i <= $ipdDays; $i++) {
                        $totalPrice += $ipdProfit->ipd->price;
                        $discountForTest = ($ipdProfit->ipd->discount * $ipdProfit->ipd->price) / 100;
                        $totalDiscount += $discountForTest;
                    }
                    $totalProfitIPD += $totalPrice - $totalDiscount;

                    $totalIPDPatients++;

                    $data['Total Patients Per Day']['IPD'][$dayDate][$ipdProfit->id] = $totalIPDPatients;
                }

                $data['IPD'][$dayDate] = $totalProfitIPD;

                if ($registered_by != 0 || $doctor_id != 0) {
                    $medicineProfitQuery = PatientPharmacyMedicine::whereIn('patient_pharmacy_medicines.patient_id', function ($q) use ($registered_by, $doctor_id) {
                        $q->from('patients')->select('patients.id');

                        if ($registered_by != 0) {
                            $q->where('created_by', $registered_by)->count();
                        }
                        if ($doctor_id != 0) {
                            $q->where('doctor_id', $doctor_id)->count();
                        }
                    })->whereDate('created_at', $date)->select('quantity', 'unit_price', 'patient_id')->lazy();
                } else {
                    $medicineProfitQuery = PatientPharmacyMedicine::whereDate('created_at', $date)
                        ->select('quantity', 'unit_price', 'patient_id')->lazy();
                }

                $data['Total Patients Per Day']['Pharmacy'][$dayDate][0] = 0;

                foreach ($medicineProfitQuery as $medicineProfit) {
                    $totalProfitMedicine += $medicineProfit->quantity * $medicineProfit->unit_price;
                    $data['Total Patients Per Day']['Pharmacy'][$dayDate][$medicineProfit->patient_id] = 1;
                }

                $data['Pharmacy'][$dayDate] = (int)$totalProfitMedicine;

                foreach ($labMainDepartments as $test) {
                    $totalProfitLaboratory = 0;
                    $data['Total Patients Per Day'][$test->dep_name][$dayDate][0] = 0;
                    foreach ($test->thisDepTests as $labTest) {

                        if ($registered_by != 0 || $doctor_id != 0) {
                            $laboratoryProfitQuery = LaboratoryPatientLab::whereIn('laboratory_patient_labs.patient_id', function ($q) use ($registered_by, $doctor_id) {
                                $q->from('patients')->select('patients.id');
                                if ($registered_by != 0) {
                                    $q->where('created_by', $registered_by)->count();
                                }
                                if ($doctor_id != 0) {
                                    $q->where('doctor_id', $doctor_id)->count();
                                }
                            })->where('lab_id', $labTest->id)->whereDate('created_at', $day)
                                ->select('price', 'discount', 'patient_id')->get();
                        } else {
                            $laboratoryProfitQuery = LaboratoryPatientLab::where('lab_id', $labTest->id)->whereDate('created_at', $day)
                                ->select('price', 'discount', 'patient_id')->get();
                        }

                        foreach ($laboratoryProfitQuery as $labProfit) {
                            $totalProfitLaboratory += $labProfit->price - ($labProfit->discount * $labProfit->price) / 100;
                            $data['Total Patients Per Day'][$test->dep_name][$dayDate][$labProfit->patient_id] = 1;
                        }
                        $data[$test->dep_name][$dayDate] = (int)$totalProfitLaboratory;
                    }
                }

                foreach ($doctors as $doctor) {
                    $seenPatientsByDoctor[$doctor->name][$dayDate] = DB::table('patients')->where('doctor_id', $doctor->id)->whereDate('created_at', $day)->count();
                }
                // $kblHMSExpenses = $client->get("https://kblhms.rokhan.co/api_get_expenses", [
                //     "query" => ['from' => $from, 'to' => $to]
                // ]);
                // $kblTotalExpense = json_decode($kblHMSExpenses->getBody()->getContents());

                if ($kblTotalExpense == NULL) {
                    $kblTotalExpense = 0;
                }

                $totalPayrollPayment = PayrollPayment::sum('amount');
            }
        }
        return view('report.general_profits_report', compact(
            'to',
            'from',
            'data',
            'days',
            'seenPatientsByDoctor',
            'kblTotalExpense',
            'patientsRegisteredBy',
            'registered_by',
            'doctor_id',
            'doctors',
            'allExpenses',
            'allIncomes',
            'otherIncome',
            'totalPayrollPayment'
        ));
    }

    public function new_general_profits_report()
    {
        return "We're temporarily offline for maintenance. We'll be back soon. Thank you for your patience!";

        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $registered_by  = $_GET['registered_by'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';

        $kblTotalIncomes = 0;
        $kblTotalExpense = 0;

        $days = [];
        $data = [];
        $seenPatientsByDoctor = [];
        $allIncomes = 0;
        $allExpenses = 0;

        // Extract registered by list
        $patientsRegisteredBy = Patient::where('created_by', '!=', 'NULL')->groupBy('created_by')->with('createdBy')->get()->pluck('createdBy.name', 'created_by');

        // Extract doctors list
        $doctors = User::where('type', 3)->where('status', 1)->select('id', 'name')->get();

        if ($from != null && $to != NULL) {

            // This is Overall Profit and expenses Codes.
            // We are getting expenses and incomes from Kblhms too as an API.

            // Get all expenses from kblhms
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $allExpensesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_expenses");
            $kblAllExpenses = json_decode($allExpensesKbl->getBody()->getContents());
            $allExpenses += $kblAllExpenses;

            // Get all income from kblhms
            $allIncomesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_incomes");
            $kblAllIncomes = json_decode($allIncomesKbl->getBody()->getContents());
            $allIncomes += $kblAllIncomes;

            //Get medicines sales
            $allMedicineProfitQuery = PatientPharmacyMedicine::select(DB::raw('SUM(quantity*unit_price) as medicineProfit'))->first();
            $allIncomes += $allMedicineProfitQuery->medicineProfit;

            //Get sum laboratory
            $allLabQuery = LaboratoryPatientLab::select(DB::raw('SUM(price-((discount * price)/100)) as labProfit'))->first();
            $allIncomes += $allLabQuery->labProfit;

            // Get sum IPD income
            $allIPDQuery = PatientIPD::where('status', 1)->whereNotNull('discharge_date')->select('price', 'discount', 'discharge_date', 'created_at')->lazy();
            foreach ($allIPDQuery  as $ipdProfit) {
                $totalPrice = 0;
                $totalDiscount = 0;
                $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit->created_at)));
                $discharge_date = $ipdProfit->discharge_date;
                $ipdDays = $register_date->diffInDays($discharge_date);

                for ($i = 1; $i <= $ipdDays; $i++) {
                    $totalPrice += $ipdProfit->price;
                    $discountForTest = ($ipdProfit->discount * $ipdProfit->price) / 100;
                    $totalDiscount += $discountForTest;
                }
                $allIncomes += $totalPrice - $totalDiscount;
            }

            $labMainDepartments = MainLabDepartment::select('id', 'dep_name')->with('thisDepTests')->lazy();

            /// This is Datewise Profit and expenses Codes.

            $period = CarbonPeriod::create($from, $to);
            // Convert the period to an array of dates
            $datesBetween = $period->toArray();
            foreach ($datesBetween as $date) {
                $totalProfitIPD = 0;
                $totalIPDPatients = 0;
                $totalProfitMedicine = 0;
                $day = $date->format('Y-m-d');
                $dayDate = explode('-', $day)[2];
                array_push($days, $dayDate);

                // OPD Patients
                if ($registered_by != 0 || $doctor_id != 0) {
                    $patientQuery = DB::table('patients')->whereDate('created_at', $day);
                    if ($registered_by != 0) {
                        $data['Patients'][$dayDate] = $patientQuery->where('created_by', $registered_by)->count();
                    }
                    if ($doctor_id != 0) {
                        $data['Patients'][$dayDate] = $patientQuery->where('doctor_id', $doctor_id)->count();
                    }
                } else {
                    $data['Patients'][$dayDate] = DB::table('patients')->whereDate('created_at', $day)->count();
                }


                // OPD Income
                if ($registered_by != 0 || $doctor_id != 0) {
                    $patientQueryForOPD = DB::table('patients')->whereDate('created_at', $day);
                    if ($registered_by != 0) {
                        $data['OPD Incomes'][$dayDate] = $patientQueryForOPD->where('created_by', $registered_by)->sum('OPD_fee');
                    }
                    if ($doctor_id != 0) {
                        $data['OPD Incomes'][$dayDate] = $patientQueryForOPD->where('doctor_id', $doctor_id)->sum('OPD_fee');
                    }
                } else {
                    $data['OPD Incomes'][$dayDate] = DB::table('patients')->whereDate('created_at', $day)->sum('OPD_fee');
                }

                // Other Income
                $kblData = $client->get("https://kblhms.rokhan.co/api_get_appointments", [
                    "query" => ['from' => $day, 'to' => $day]
                ]);
                $kblTotalIncomes = json_decode($kblData->getBody()->getContents());

                $data['Other Incomes'][$dayDate] = $kblTotalIncomes->otherIncomes;

                // IPD Report
                $ipdProfitQuery = PatientIPD::where('status', 1)->whereDate('discharge_date', '=', $day)
                    ->whereHas('patient', function ($q) use ($registered_by, $doctor_id) {
                        if ($registered_by != 0) {
                            $q->where('created_by', $registered_by);
                        }

                        if ($doctor_id != 0) {
                            $q->where('doctor_id', $doctor_id);
                        }
                    })->select(
                        DB::raw('SUM(DATEDIFF(discharge_date, created_at)*(price-(price*discount/100))) as total_inc'),
                        DB::raw('count(*) as number_of_patients')
                    )->first();

                $data['Total Patients Per Day']['IPD'][$dayDate][0] = $ipdProfitQuery->number_of_patients;

                $data['IPD'][$dayDate] = $ipdProfitQuery->total_inc;


                // Medicine Income

                $medicineProfitQuery = PatientPharmacyMedicine::whereIn('patient_pharmacy_medicines.patient_id', function ($q) use ($registered_by, $doctor_id) {
                    $q->from('patients')->select('patients.id');

                    if ($registered_by != 0) {
                        $q->where('created_by', $registered_by)->count();
                    }
                    if ($doctor_id != 0) {
                        $q->where('doctor_id', $doctor_id)->count();
                    }
                })->whereDate('created_at', $date)->select(DB::raw('SUM(quantity*unit_price) as income'), DB::raw('count(DISTINCT patient_id) as count_patients'))->first();

                $data['Total Patients Per Day']['Pharmacy'][$dayDate][0] = $medicineProfitQuery->count_patients;

                $data['Pharmacy'][$dayDate] = (int)$medicineProfitQuery->income;

                foreach ($labMainDepartments as $test) {
                    $totalProfitLaboratory = 0;
                    $data['Total Patients Per Day'][$test->dep_name][$dayDate][0] = 0;
                    $data[$test->dep_name][$dayDate] = 0;
                    foreach ($test->thisDepTests as $labTest) {

                        $laboratoryProfitQuery = LaboratoryPatientLab::whereIn('laboratory_patient_labs.patient_id', function ($q) use ($registered_by, $doctor_id) {
                            $q->from('patients')->select('patients.id');
                            if ($registered_by != 0) {
                                $q->where('created_by', $registered_by)->count();
                            }
                            if ($doctor_id != 0) {
                                $q->where('doctor_id', $doctor_id)->count();
                            }
                        })->where('lab_id', $labTest->id)->whereDate('created_at', $day)
                            ->select(DB::raw('SUM(price - (price*discount/100)) as income, count(DISTINCT patient_id) as patient_count'))->first();

                        $data['Total Patients Per Day'][$test->dep_name][$dayDate][0] += $laboratoryProfitQuery->patient_count;
                        $data[$test->dep_name][$dayDate] += (int)$laboratoryProfitQuery->income;

                        // foreach ($laboratoryProfitQuery as $labProfit) {
                        //     $totalProfitLaboratory += $labProfit->price - ($labProfit->discount * $labProfit->price) / 100;
                        //     $data['Total Patients Per Day'][$test->dep_name][$dayDate][$labProfit->patient_id] = 1;
                        // }
                        // $data[$test->dep_name][$dayDate] = (int)$totalProfitLaboratory;
                    }
                }

                foreach ($doctors as $doctor) {
                    $seenPatientsByDoctor[$doctor->name][$dayDate] = DB::table('patients')->where('doctor_id', $doctor->id)->whereDate('created_at', $day)->count();
                }

                $kblHMSExpenses = $client->get("https://kblhms.rokhan.co/api_get_expenses", [
                    "query" => ['from' => $from, 'to' => $to]
                ]);
                $kblTotalExpense = json_decode($kblHMSExpenses->getBody()->getContents());

                if ($kblTotalExpense == NULL) {
                    $kblTotalExpense = 0;
                }
            }
        }
        return view('report.general_profits_report', compact(
            'to',
            'from',
            'data',
            'days',
            'seenPatientsByDoctor',
            'kblTotalExpense',
            'patientsRegisteredBy',
            'registered_by',
            'doctor_id',
            'doctors',
            'allExpenses',
            'allIncomes'
        ));
    }

    public function cumulative_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $registered_by  = $_GET['registered_by'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';

        $kblTotalIncomes = 0;
        $kblTotalExpense = 0;
        $days = [];
        $data = [];
        $seenPatientsByDoctor = [];
        $allIncomes = 0;
        $allExpenses = 0;
        $count_patients = 0;
        $sumOPD = 0;
        $other_incomes = 0;
        $totalProfitIPD = 0;
        $totalIPDPatients = 0;
        $sumPharmacy = 0;
        $sumLabratory = 0;

        $patientsRegisteredBy = Patient::where('created_by', '!=', 'NULL')->groupBy('created_by')->with('createdBy')->lazy()->pluck('createdBy.name', 'created_by');

        $doctors = User::where('type', 3)->where('status', 1)->select('id', 'name')->lazy();

        if ($from != null && $to != NULL) {

            // This is Overall Profit and expenses Codes.
            // We are getting expenses and incomes from Kblhms too as an API.

            $client = new \GuzzleHttp\Client(['verify' => false]);
            $allExpensesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_expenses");
            $kblAllExpenses = json_decode($allExpensesKbl->getBody()->getContents());
            $allExpenses += $kblAllExpenses;

            $allIncomesKbl = $client->get("https://kblhms.rokhan.co/api_get_all_incomes");
            $kblAllIncomes = json_decode($allIncomesKbl->getBody()->getContents());
            $allIncomes += $kblAllIncomes;

            // Total Patients
            if ($registered_by != 0 || $doctor_id != 0) {
                $count_patients = DB::table('patients')->whereBetween('created_at', [$from, $to]);
                if ($registered_by != 0) {
                    $count_patients = $count_patients->where('created_by', $registered_by)->count();
                }
                if ($doctor_id != 0) {
                    $count_patients = $count_patients->where('doctor_id', $doctor_id)->count();
                }
            } else {
                $count_patients = DB::table('patients')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->count();
            }


            // Sum OPD Fee
            if ($registered_by != 0 || $doctor_id != 0) {
                $sumOPD = DB::table('patients')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
                if ($registered_by != 0) {
                    $sumOPD = $sumOPD->where('created_by', $registered_by)->sum('OPD_fee');
                }
                if ($doctor_id != 0) {
                    $sumOPD = $sumOPD->where('doctor_id', $doctor_id)->sum('OPD_fee');
                }
            } else {
                $sumOPD = DB::table('patients')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->sum('OPD_fee');
            }

            // Sum Other Incomes
            $kblData = $client->get("https://kblhms.rokhan.co/api_get_appointments", [
                "query" => ['from' => $from, 'to' => $to]
            ]);
            $kblTotalIncomes = json_decode($kblData->getBody()->getContents());
            $other_incomes = $kblTotalIncomes->otherIncomes;

            // Sum IPD
            if ($registered_by != 0 || $doctor_id != 0) {
                $ipdProfitQuery = PatientIPD::whereIn('patient_ipds.patient_id', function ($q) use ($registered_by, $doctor_id) {
                    $q->from('patients')->select('patients.id');

                    if ($registered_by != 0) {
                        $q->where('created_by', $registered_by)->count();
                    }
                    if ($doctor_id != 0) {
                        $q->where('doctor_id', $doctor_id)->count();
                    }
                })->whereDate('discharge_date', '>=', $from)->whereDate('discharge_date', '<=', $to)->where('status', 1)
                    ->select('price', 'discount', 'discharge_date', 'patient_id', 'created_at')->get()->toArray();
            } else {
                $ipdProfitQuery = PatientIPD::whereDate('discharge_date', '>=', $from)->whereDate('discharge_date', '<=', $to)->where('status', 1)
                    ->select('price', 'discount', 'discharge_date', 'patient_id', 'created_at')->get()->toArray();
            }

            $totalProfitIPD = 0;
            $totalIPDPatients = 0;

            foreach ($ipdProfitQuery as $ipdProfit) {
                $totalPrice = 0;
                $totalDiscount = 0;
                $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit['created_at'])));
                $discharge_date = $ipdProfit['discharge_date'];
                $ipdDays = $register_date->diffInDays($discharge_date);

                for ($i = 1; $i <= $ipdDays; $i++) {
                    $totalPrice += $ipdProfit['price'];
                    $discountForTest = ($ipdProfit['discount'] * $ipdProfit['price']) / 100;
                    $totalDiscount += $discountForTest;
                }
                $totalProfitIPD += $totalPrice - $totalDiscount;

                $totalIPDPatients++;
            }


            //sumPharmacy
            $sumPharmacy = PatientPharmacyMedicine::select(DB::raw('SUM(quantity*unit_price) as medicineProfit'))
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->first()->medicineProfit;

            // Sum Labratory
            $sumLabratory = LaboratoryPatientLab::select(DB::raw('SUM(price-((discount * price)/100)) as labProfit'))
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)->first()->labProfit;
        }
        return view('report.cumulative_report', compact(
            'to',
            'from',
            'data',
            'days',
            'doctors',
            'count_patients',
            'sumOPD',
            'other_incomes',
            'totalProfitIPD',
            'totalIPDPatients',
            'sumPharmacy',
            'sumLabratory',
            'allExpenses',
            'allIncomes',
            'patientsRegisteredBy',
            'registered_by',
            'doctor_id'
        ));
    }

    public function getDatesUntilToday()
    {
        $dates = [];
        $today = today();
        // $currentMonthName= \Carbon\Carbon::now()->format('F');

        for ($i = 1; $i < $today->daysInMonth + 1; ++$i) {
            $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
        }
        $untilToday = array_search($today->format('Y-m-d'), $dates);
        // Find the position of the key you're looking for.
        $position = array_search($untilToday, array_keys($dates));
        // If a position is found, splice the array.
        if ($position !== false) {
            array_splice($dates, ($position + 1));
        }

        return $dates;
    }

    public function get_daily_patient_count()
    {
        $datesUntilToday = $this->getDatesUntilToday();
        $dailyPatientCountData = [];
        array_push($dailyPatientCountData, ['Day', 'Daily Patients']);

        foreach ($datesUntilToday as $date) {
            $dayDate = explode('-', $date)[2];
            $totalPatient = 0;

            $totalPatient = DB::table('patients')->whereDate('created_at', $date)->count();
            array_push($dailyPatientCountData, [$dayDate, (int)$totalPatient]);
        }
        return $dailyPatientCountData;
    }

    public function get_monthly_patient_count()
    {
        $currentYear = date('Y');
        $monthlyPatientCountData = [];
        array_push($monthlyPatientCountData, ['Month', 'Monthly Patients']);
        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('M', mktime(0, 0, 0, $m, 10));
            $totalPatient = DB::table('patients')->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)->count();
            array_push($monthlyPatientCountData, [$monthName, (int)$totalPatient]);
        }
        return $monthlyPatientCountData;
    }


    public function get_daily_based_income_data()
    {
        $datesUntilToday = $this->getDatesUntilToday();
        $dailyPatientData = [];
        array_push($dailyPatientData, ['Day', 'OPD Income', 'Medicine Income', 'Lab Income', 'IPD Income', 'Total Income']);

        foreach ($datesUntilToday as $date) {
            $dayDate = explode('-', $date)[2];
            $totalLab = 0;
            $totalMedicineSale = 0;
            $totalIPDIncome = 0;
            $grandTotalOfAll = 0;

            $opdQuery = DB::table('patients')->whereDate('created_at', $date)->sum('OPD_fee');
            $labQuery = DB::table('laboratory_patient_labs')->whereDate('created_at', $date)->select('price', 'discount')->get();
            $medicineSaleQuery = DB::table('patient_pharmacy_medicines')->whereDate('created_at', $date)->select('unit_price', 'quantity')->get();
            $ipdProfitQuery = PatientIPD::where('discharge_date', $date)->where('status', 1)->select('price', 'discount', 'discharge_date', 'created_at')->get();

            foreach ($labQuery as $lab) {
                $totalLab += $lab->price - ($lab->discount * $lab->price) / 100;
            }
            foreach ($medicineSaleQuery as $medicine) {
                $totalMedicineSale += $medicine->quantity * $medicine->unit_price;
            }
            foreach ($ipdProfitQuery as $ipdProfit) {
                $totalPrice = 0;
                $totalDiscount = 0;
                $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit->created_at)));
                $discharge_date = $ipdProfit->discharge_date;
                $ipdDays = $register_date->diffInDays($discharge_date);

                for ($i = 1; $i <= $ipdDays; $i++) {
                    $totalPrice += $ipdProfit->price;
                    $discountForTest = ($ipdProfit->discount * $ipdProfit->price) / 100;
                    $totalDiscount += $discountForTest;
                }
                $totalIPDIncome += $totalPrice - $totalDiscount;
            }

            $grandTotalOfAll = $totalMedicineSale + $totalLab + $totalIPDIncome + $opdQuery;
            array_push($dailyPatientData, [$dayDate, (int)$opdQuery, (int)$totalMedicineSale, (int)$totalLab, (int)$totalIPDIncome, (int)$grandTotalOfAll]);
        }
        return $dailyPatientData;
    }

    public function get_monthly_based_income_data()
    {
        $currentYear = date('Y');
        $monthlyPatientIncome = [];
        array_push($monthlyPatientIncome, ['Month', 'OPD Income', 'Medicine Income', 'Lab Income', 'IPD Income', 'Total Income']);

        for ($m = 1; $m <= 12; $m++) {
            $totalLab = 0;
            $totalMedicineSale = 0;
            $totalIPDIncome = 0;
            $grandTotalOfAll = 0;

            $monthName = date('M', mktime(0, 0, 0, $m, 10));

            $opdQuery = DB::table('patients')->whereYear('reg_date', $currentYear)->whereMonth('reg_date', $m)->sum('OPD_fee');

            $labQuery = DB::table('laboratory_patient_labs')->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)->select('price', 'discount')->get();

            $medicineSaleQuery = DB::table('patient_pharmacy_medicines')->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)->select('unit_price', 'quantity')->get();

            $ipdProfitQuery = PatientIPD::whereYear('discharge_date', $currentYear)
                ->whereMonth('discharge_date', $m)->where('status', 1)->select('price', 'discount', 'discharge_date', 'created_at')->get();

            foreach ($labQuery as $lab) {
                $totalLab += $lab->price - ($lab->discount * $lab->price) / 100;
            }

            foreach ($medicineSaleQuery as $medicine) {
                $totalMedicineSale += $medicine->quantity * $medicine->unit_price;
            }

            foreach ($ipdProfitQuery as $ipdProfit) {
                $totalPrice = 0;
                $totalDiscount = 0;
                $register_date = \Carbon\Carbon::parse(date('Y-m-d', strtotime($ipdProfit->created_at)));
                $discharge_date = $ipdProfit->discharge_date;
                $ipdDays = $register_date->diffInDays($discharge_date);

                for ($i = 1; $i <= $ipdDays; $i++) {
                    $totalPrice += $ipdProfit->price;
                    $discountForTest = ($ipdProfit->discount * $ipdProfit->price) / 100;
                    $totalDiscount += $discountForTest;
                }
                $totalIPDIncome += $totalPrice - $totalDiscount;
            }

            $grandTotalOfAll = $totalMedicineSale + $totalLab + $totalIPDIncome + $opdQuery;
            array_push($monthlyPatientIncome, [$monthName, (int)$opdQuery, (int)$totalMedicineSale, (int)$totalLab, (int)$totalIPDIncome, (int)$grandTotalOfAll]);
        }

        return $monthlyPatientIncome;
    }


    public function registered_in_door_patient_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $registeredPatients = [];
        if ($from != null && $to != NULL) {
            $registeredPatients = Patient::whereDate('created_at', '>=', $from)
                ->where('doctor_id', '!=', 28)
                ->whereDate('created_at', '<=', $to);
            if ($doctor_id  != 0) {
                $registeredPatients->where('doctor_id', $doctor_id);
            }
            $registeredPatients = $registeredPatients->latest()->with('doctor', 'createdBy')->get();
        }
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        return view('report.registered_patient_report', compact('from', 'to', 'doctors', 'doctor_id', 'registeredPatients'));
    }

    public function registered_all_patient_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $registeredPatients = [];
        if ($from != null && $to != NULL) {
            $registeredPatients = Patient::whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
            if ($doctor_id  != 0) {
                $registeredPatients->where('doctor_id', $doctor_id);
            }
            $registeredPatients = $registeredPatients->latest()->with('doctor', 'createdBy')->get();
        }
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        return view('report.registered_patient_report', compact('from', 'to', 'doctors', 'doctor_id', 'registeredPatients'));
    }

    public function registered_out_door_patient_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $registeredPatients = [];
        if ($from != null && $to != NULL) {
            $registeredPatients = Patient::whereDate('created_at', '>=', $from)
                ->where('doctor_id', 28)
                ->whereDate('created_at', '<=', $to);
            if ($doctor_id  != 0) {
                $registeredPatients->where('doctor_id', $doctor_id);
            }
            $registeredPatients = $registeredPatients->latest()->with('doctor', 'createdBy')->get();
        }
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        return view('report.registered_patient_report', compact('from', 'to', 'doctors', 'doctor_id', 'registeredPatients'));
    }


    public function returned_medicines_report()
    {
        $returnedMedicines =  Pharmacy::where('returned', 1)->select('id', 'medicine_id', 'quantity', 'purchase_price', 'sale_price', 'invoice_no', 'returned_by')->latest()->with('medicineName', 'returnedBy')->get();
        return view('report.returned_medicines_report', compact('returnedMedicines'));
    }

    public function manual_expired_medicines_report()
    {
        $manuallMedicines =  Pharmacy::where('expired', 1)->select('id', 'medicine_id', 'quantity', 'purchase_price', 'sale_price', 'invoice_no', 'expired_by')->latest()->with('medicineName', 'expiredBy')->get();
        return view('report.manuall_expired_midicines_report', compact('manuallMedicines'));
    }


    public function set_permissions_from_lab()
    {

        $labMainDepartments = MainLabDepartment::all();
        foreach ($labMainDepartments as $depaName) {

            $permission = new Permission();
            $permission->permission_name = $depaName->dep_name;
            $permission->permission_group = "Department";
            $permission->save();
        }
    }

    public function OPD_fee_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $OPDPatients = [];
        if ($from != null && $to != NULL) {
            $OPDPatients = Patient::whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            if ($doctor_id  != 0) {
                $OPDPatients->where('doctor_id', $doctor_id);
            }
            $OPDPatients = $OPDPatients->latest()->with('doctor', 'createdBy')->get();
        }
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        return view('report.OPD_patients_report', compact('from', 'to', 'doctors', 'doctor_id', 'OPDPatients'));
    }


    public function referral_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $department_id = $_GET['department'] ?? '';
        $doctor_id = $_GET['doctor'] ?? '';
        $labMainDepartments = MainLabDepartment::select('id', 'dep_name')->get();
        $labMainDepartmentName = MainLabDepartment::where('id', $department_id)->value('dep_name');
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        $labSalePatients = [];
        if ($from != null && $to != NULL) {
            $labSalePatients = Patient::whereIn('patients.id',  function ($q) use ($from, $to, $doctor_id) {
                $q->from('laboratory_patient_labs')->select('laboratory_patient_labs.patient_id');
                $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                if ($doctor_id != 0) {
                    $q->where('created_by', $doctor_id);
                }
            })->select('id', 'patient_name', 'patient_generated_id', 'doctor_id', 'created_by')
                ->with(['doctor', 'createdBy', 'laboratoryTests.testName', 'laboratoryTests.testName.mainDepartment', 'laboratoryTests.createdBy', 'laboratoryTests' => function ($q) use ($from, $to, $doctor_id) {
                    $q->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                    if ($doctor_id != 0) {
                        $q->where('created_by', $doctor_id);
                    }
                }]);

            if ($department_id) {
                $labSalePatients = $labSalePatients->with(['laboratoryTests.testName' => function ($q) use ($department_id) {
                    $q->where('main_dep_id', $department_id);
                }]);
            }

            $labSalePatients = $labSalePatients->latest()->get();
        }
        return view('report.referral_report', compact('labSalePatients', 'from', 'to', 'labMainDepartments', 'department_id', 'labMainDepartmentName', 'doctors', 'doctor_id'));
    }

    public function employee_percentage_report()
    {
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $department_id = $_GET['department'] ?? '';
        $doctor_id = $_GET['doctor'] ?? '';
        $labMainDepartments = MainLabDepartment::select('id', 'dep_name')->get();
        $labMainDepartmentName = MainLabDepartment::where('id', $department_id)->value('dep_name');
        $doctors = User::where('type', 3)->select('id', 'name')->get();
        $employees = [];

        if ($from != null && $to != NULL) {
            $employees = Employee::whereHas('user')->withCount([
                'patients as opd_sum' => function ($q) use ($from, $to) {
                    $q->select(DB::raw('sum(OPD_fee)'))
                        ->where('OPD_fee', '>', 30)
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                },
                'patients as opd_count' => function ($q) use ($from, $to) {
                    $q->where('OPD_fee', '>', 30)
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                },
                'ipd as ipd_sum' => function ($q) use ($from, $to) {
                    //Sum discounted price * how many days the patient was hospitalized. It is calculated on daily basis.
                    $q->select(DB::raw('sum((price*(100-discount)/100)*(DATEDIFF(discharge_date, patient_ipds.created_at)))'))
                        ->whereDate('patient_ipds.discharge_date', '>=', $from)
                        ->whereDate('patient_ipds.discharge_date', '<=', $to)
                        ->orWhereDate('patient_ipds.created_at', '>=', $from)
                        ->whereDate('patient_ipds.created_at', '<=', $to);
                },
                'ipd as ipd_count' => function ($q) use ($from, $to) {
                    $q->whereDate('patient_ipds.created_at', '>=', $from)
                        ->whereDate('patient_ipds.created_at', '<=', $to);
                },
            ])
                ->with(['laboratoryTests' => function ($q) use ($from, $to) {
                    $q->whereDate('laboratory_patient_labs.created_at', '>=', $from)
                        ->whereDate('laboratory_patient_labs.created_at', '<=', $to);
                }])
                ->get();

            //return $employees;
        }

        return view('report.employees_percentage_report', compact('employees', 'from', 'to', 'labMainDepartments', 'department_id', 'labMainDepartmentName', 'doctors', 'doctor_id'));
    }

    public function overview_report()
    {
        $from = request('from') ?? now()->startOfMonth()->toDateString();
        $to = request('to') ?? now()->endOfMonth()->toDateString();

        $selectedReportType = request('report_type');
        $selectedIncomeCategory = request('income_category');
        $selectedExpenseCategory = request('expense_category');
        $categoryId = request('category');
        $searchTerm = request('searchTerm');

        if ($selectedReportType == 'expense') {
            $expenseData = $this->expense_report($from, $to, $selectedReportType, $categoryId, $searchTerm);

            return view('report.expense_report', $expenseData);
        }


        if ($selectedReportType == 'income') {
            $otherIncomeData = $this->other_income_report($from, $to);

            // Return a different view for expense reports
            return view('report.other_income', $otherIncomeData);
        }
        if ($selectedReportType == 'salary') {
            $otherIncomeData = $this->salary_report($from, $to);

            // Return a different view for expense reports
            return view('report.salary', $otherIncomeData);
        }

        // Adjust the to date to include the entire day
        if ($from == $to) {
            $from = $from . ' 00:00:00';
            $to = $to . ' 23:59:59';
        }

        // Calculate total income within the date range
        $totalIncome = 0;

        // OPD Income / correct 
        $opdIncome = Patient::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])->sum('OPD_fee');
        $totalIncome += $opdIncome;

        // IPD Income / correct
        $ipdIncome = PatientIPD::where('status', 1)
            ->whereBetween('discharge_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ])
            ->sum(DB::raw('DATEDIFF(discharge_date, created_at) * (price - (price * discount / 100))'));
        $totalIncome += $ipdIncome;

        // Pharmacy Income / correct 
        $pharmacyIncome = PatientPharmacyMedicine::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])
            ->sum(DB::raw('quantity * unit_price'));
        $totalIncome += $pharmacyIncome;

        // Laboratory Income
        $labIncome = LaboratoryPatientLab::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])
            ->sum(DB::raw('price - (price * discount / 100)'));
        $totalIncome += $labIncome;

        // Miscellaneous Income
        $miscIncome = MiscellaneousIncome::whereBetween('date', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])->sum('amount');
        $totalIncome += $miscIncome;

        // Calculate total expenses within the date range
        $totalExpenses = ExpenseSlip::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])->get()->sum(function ($expenseSlip) {
            return $expenseSlip->expenses->sum('amount');
        });


        // Calculate total payroll payment within the date range
        $totalPayrollPayment = PayrollPayment::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])->sum('amount');

        // Calculate available cash (Total Income - Total Expenses - Payroll Payment for the date range)
        $availableCash = $totalIncome - ($totalExpenses + $totalPayrollPayment);

        // Calculate income by categories within the date range
        $incomeCategories = [
            'OPD' => $opdIncome,
            'Pharmacy' => $pharmacyIncome,
            'Laboratory' => $labIncome,
            'IPD' => $ipdIncome,
            'Miscellaneous Income' => $miscIncome,
        ];

        // Calculate expenses by categories within the date range
        $expenseCategories = ExpenseSlip::whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ])
            ->get()
            ->groupBy(function ($expenseSlip) {
                return $expenseSlip->expenseCategory->name;
            })
            ->mapWithKeys(function ($expenseSlips, $category) {
                return [$category => $expenseSlips->sum(function ($expenseSlip) {
                    return $expenseSlip->expenseItems->sum('amount');
                })];
            });

        // Calculate total available cash across all records (not limited by date)
        $totalIncomeAllTime = Patient::sum('OPD_fee') +
            PatientPharmacyMedicine::sum(DB::raw('quantity * unit_price')) +
            LaboratoryPatientLab::sum(DB::raw('price - (price * discount / 100)')) +
            PatientIPD::where('status', 1)
            ->sum(DB::raw('DATEDIFF(discharge_date, created_at) * (price - (price * discount / 100))')) +
            MiscellaneousIncome::whereNull('deleted_At')->sum('amount');

        // $totalIncomeAllTime +=3000;

        $totalExpensesAllTime = ExpenseItem::whereHas('slip', function ($q) {
            $q->whereNull('deleted_at');
        })->sum('amount');


        $totalPayrollAllTime = PayrollPayment::sum('amount');

        $totalAvailableCash = $totalIncomeAllTime - ($totalExpensesAllTime + $totalPayrollAllTime);

        return view('report.overview_report', compact(
            'from',
            'to',
            'totalIncome',
            'totalExpenses',
            'totalPayrollPayment',
            'incomeCategories',
            'expenseCategories',
            'availableCash',
            'totalAvailableCash'
        ));
    }


    public function expense_report($from, $to, $reportType, $categoryId = null, $searchTerm)
    {
        $query = ExpenseSlip::whereBetween('date', [$from, $to])
            ->with('expenseCategory');

        if ($categoryId) {
            $query->where('category', $categoryId);
        }

        $expenses = $query->orderByDesc('id')->paginate(3000);

        $categories = ExpenseCategory::all();

        return [
            'expenses' => $expenses,
            'from' => $from,
            'to' => $to,
            'reportType' => $reportType,
            'categories' => $categories
        ];
    }

    public function other_income_report($from, $to)
    {
        $incomes = MiscellaneousIncome::whereBetween('date', [$from, $to])
            ->with('incomeCategory')
            ->orderByDesc('id')
            ->paginate(3000);

        // Debugging the results
        return [
            'incomes' => $incomes,
        ];
    }

    public function salary_report($from, $to)
    {
        $payrollPayments = PayrollPayment::whereBetween('payment_date', [$from, $to])
            ->with('employee')
            ->orderByDesc('id')
            ->get();
        return [
            'payrollPayments' => $payrollPayments,
        ];
    }
}
