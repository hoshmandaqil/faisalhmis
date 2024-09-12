<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Floor;
use App\Models\LabDepartment;
use App\Models\MainLabDepartment;
use App\Models\Patient;
use App\Models\MedicineName;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = Patient::latest()->with('doctor', 'createdBy')->paginate(30);
        //$doctors = User::where('type', 3)->latest()->pluck('name', 'id')->all();
        $doctors = User::where('type', 3)->where('status', 1)->latest()->get();
        $previousPatientId = Patient::max('id');

        return view('patient.patient', compact('patients', 'doctors', 'previousPatientId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!in_array('add_patient', user_permissions())) {
            return view('access_denied');
        }
        $doctorOPDFee = User::where('id', $request->doctor_id)->value('OPD_fee');
        $patient = new Patient();
        $patient->patient_name = $request->patient_name;
        $patient->patient_fname = $request->patient_fname;
        $patient->patient_phone = $request->patient_phone;
        $patient->gender = $request->gender;
        $patient->marital_status = $request->marital_status;
        $patient->age = $request->age;
        $patient->patient_generated_id = NULL;
        // $patient->advance_pay = $request->advance_pay;
        $patient->blood_group = $request->blood_group;
        $patient->reg_date = $request->reg_date;
        $patient->doctor_id = $request->doctor_id;
        $patient->OPD_fee = $request->default_discount == 0 ? 0 : $doctorOPDFee;
        $patient->no_discount = 0;
        $patient->created_by = \Auth::user()->id;
        $patient->save();

        // Update patient id
        $patient_update = Patient::find($patient->id);
        $patient_update->patient_generated_id = 'BRH-' . $patient->id;
        $patient_update->save();

        return  redirect()->back()->with('alert', 'The Patient added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        if (!in_array('edit_patient', user_permissions())) {
            return view('access_denied');
        }
        $doctorOPDFee = User::where('id', $request->doctor_id)->value('OPD_fee');
        $patient = Patient::findorfail($patient->id);
        $patient->patient_name = $request->patient_name;
        $patient->patient_fname = $request->patient_fname;
        $patient->patient_phone = $request->patient_phone;
        $patient->gender = $request->gender;
        $patient->marital_status = $request->marital_status;
        $patient->age = $request->age;
        $patient->advance_pay = $request->advance_pay;
        $patient->blood_group = $request->blood_group;
        $patient->reg_date = $request->reg_date;
        $patient->doctor_id = $request->doctor_id;
        $patient->OPD_fee = $doctorOPDFee;
        $patient->blood_pressure = $request->blood_pressure;
        $patient->respiration_rate = $request->respiration_rate;
        $patient->pulse_rate = $request->pulse_rate;
        $patient->heart_rate = $request->heart_rate;
        $patient->temperature = $request->temperature;
        $patient->weight = $request->weight;
        $patient->height = $request->height;
        $patient->mental_state = $request->mental_state;
        $patient->medical_history = $request->medical_history;
        $patient->no_discount = $request->default_discount || $patient->no_discount;
        $patient->updated_by = \Auth::user()->id;
        $patient->save();
        return  redirect()->back()->with('alert', 'The Patient Updated Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        if (!in_array('delete_patient', user_permissions())) {
            return view('access_denied');
        }
        $patient->delete();
        return  redirect()->back()->with('alert', 'The Patient Deleted Successfully')->with('alert-type', 'alert-info');
    }

    public function my_patients()
    {
        $currentUserId = \Auth::user()->id;

        //If user is Admin, pharmacist of reception
        if (\Auth::user()->type == 1 || \Auth::user()->type == 2 || \Auth::user()->type == 4) {
            $patients = Patient::with('medicines', 'doctor', 'ipd', 'ipd.floor', 'labs', 'createdBy')->latest()->paginate(25);
        }
        //If not:
        else {
            $patients = Patient::where('doctor_id', $currentUserId)->with('medicines', 'doctor', 'ipd', 'ipd.floor', 'labs', 'createdBy')->latest()->paginate(20);
        }

        $floors = Floor::groupBy('floor_name')->pluck('floor_name', 'id')->all();
        $rooms = Floor::groupBy('room')->pluck('room', 'id')->all();
        $beds = Floor::pluck('bed', 'id')->all();

        $selectPharmacy = MedicineName::whereIn('medicine_names.id', function ($query) {
            $query->from('pharmacies')
                ->select('pharmacies.medicine_id')->where('returned', 0)->where('expired', 0);
        })
            ->latest()
            ->with('thisMedicinePharmacy:medicine_id,quantity,sale_price')
            ->select('id', 'medicine_name')
            ->get()
            ->map(function ($pharmacy) {
                $pharmacy->setRelation('thisMedicinePharmacy', $pharmacy->thisMedicinePharmacy->take(2));
                return $pharmacy;
            })->lazy();


        $selectLab = LabDepartment::latest()->select('id', 'dep_name', 'price', 'normal_range', 'main_dep_id')->get();
        $mainLabDepartments = MainLabDepartment::latest()->select('id', 'dep_name')->get();

        $medicine_dosage = DB::table('medicine_dosages')->get();

        return view(
            'patient.my_patients',
            compact(
                'patients',
                'selectPharmacy',
                'floors',
                'selectLab',
                'rooms',
                'beds',
                'mainLabDepartments',
                'medicine_dosage'
            )
        );
    }

    //Portion of my_patients for Set Medicine, in order to control load.
    public function my_patients_medicines()
    {
        $currentUserId = \Auth::user()->id;

        //If user is Admin, pharmacist of reception
        if (\Auth::user()->type == 1 || \Auth::user()->type == 2 || \Auth::user()->type == 4) {
            $patients = Patient::with('medicines', 'doctor', 'createdBy')->latest()->paginate(10);
        } else {
            $patients = Patient::where('doctor_id', $currentUserId)->with('medicines', 'doctor', 'createdBy')->latest()->paginate(10);
        }

        //Pharmacy
        $selectPharmacy = MedicineName::whereIn('medicine_names.id', function ($query) {
            $query->from('pharmacies')
                ->select('pharmacies.medicine_id')->where('returned', 0)->where('expired', 0);
        })
            ->latest()
            ->with('thisMedicinePharmacy:medicine_id,quantity,sale_price')
            ->select('id', 'medicine_name')
            ->get()
            ->map(function ($pharmacy) {
                $pharmacy->setRelation('thisMedicinePharmacy', $pharmacy->thisMedicinePharmacy->take(2));
                return $pharmacy;
            })->lazy();

        $medicine_dosage = DB::table('medicine_dosages')->get();

        return view(
            'patient.my_patients_medicines',
            compact('patients', 'selectPharmacy', 'medicine_dosage')
        );
    }

    //Portion of my patients: Set Lab and IPD
    public function my_patients_lab_ipd()
    {
        $currentUserId = \Auth::user()->id;

        //If user is Admin, pharmacist of reception
        if (\Auth::user()->type == 1 || \Auth::user()->type == 2 || \Auth::user()->type == 4) {
            $patients = Patient::with('doctor', 'ipd', 'ipd.floor', 'labs', 'createdBy')->latest()->paginate(20);
        } else {
            $patients = Patient::where('doctor_id', $currentUserId)->with('doctor', 'ipd', 'ipd.floor', 'labs', 'createdBy')->latest()->paginate(20);
        }

        $floors = Floor::groupBy('floor_name')->pluck('floor_name', 'id')->all();

        $rooms = Floor::groupBy('room')->pluck('room', 'id')->all();

        $beds = Floor::pluck('bed', 'id')->all();

        $selectLab = LabDepartment::latest()->select('id', 'dep_name', 'price', 'normal_range', 'main_dep_id')->get();
        $mainLabDepartments = MainLabDepartment::latest()->select('id', 'dep_name')->get();

        return view(
            'patient.my_patients_lab_ipd',
            compact(
                'patients',
                'floors',
                'selectLab',
                'rooms',
                'beds',
                'mainLabDepartments'
            )
        );
    }

    public function search_patient_list(Request $request)
    {
        $patientSearchDetail = $request->search_patient;

        $patients = Patient::where('patient_name', 'Like', '%' . $patientSearchDetail . '%')
            ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail . '%')
            ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail . '%')
            ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail . '%')
            ->latest()->paginate(100);
        //$doctors = User::where('type', 3)->latest()->pluck('name', 'id')->all();
        $doctors = User::where('type', 3)->latest()->get();
        $previousPatientId = Patient::max('id');
        return view('patient.patient', compact('patients', 'doctors', 'previousPatientId', 'patientSearchDetail'));
    }

    public function patient_invoice($id)
    {
        $patient = Patient::where('id', $id)
            ->with('pharmacyMedicines', 'ipd', 'laboratoryTests.testName')->first();
        // dd($patient);
        return view('patient.patient_invoice', compact('patient'));
    }

    public function printVitalSignOfPatient()
    {
        $patient_id = $_GET['patient_id'];
        $patient = Patient::where('id', $patient_id)->with('doctor', 'createdBy', 'updatedBy')->first();
        return view('ajax.ajax_print_vital_sign', compact('patient', 'patient_id'));
    }

    public function patient_vital_sign(Request $request)
    {
        if (!in_array('add_patient', user_permissions())) {
            return view('access_denied');
        }

        $patient = Patient::findorfail($request->patient_id);
        $patient->blood_pressure = $request->blood_pressure;
        $patient->respiration_rate = $request->respiration_rate;
        $patient->pulse_rate = $request->pulse_rate;
        $patient->heart_rate = $request->heart_rate;
        $patient->temperature = $request->temperature;
        $patient->weight = $request->weight;
        $patient->height = $request->height;
        $patient->mental_state = $request->mental_state;
        $patient->medical_history = $request->medical_history;
        $patient->va_1 = $request->va_1;
        $patient->va_2 = $request->va_2;
        $patient->iop_1 = $request->iop_1;
        $patient->iop_2 = $request->iop_2;
        $patient->chief_complaint = $request->chief_complaint;
        $patient->dx = $request->dx;
        $patient->save();

        return  redirect()->back()->with('alert', 'The Patient Updated Successfully')->with('alert-type', 'alert-info');
    }
}
