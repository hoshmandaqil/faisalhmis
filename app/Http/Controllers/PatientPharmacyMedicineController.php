<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientPharmacyMedicine;
use Illuminate\Http\Request;

class PatientPharmacyMedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medicinePatients = Patient::with('pharmacyMedicines', 'createdBy', 'doctor')
            ->when(request('search'), function ($query) {
                $query->where('patient_name', 'Like', '%' . request('search') . '%')
                    ->orwhere('patient_fname', 'Like', '%' . request('search') . '%')
                    ->orwhere('patient_phone', 'Like', '%' . request('search') . '%')
                    ->orwhere('patient_generated_id', 'Like', '%' . request('search') . '%');
            })
            ->whereHas('medicines')
            ->latest()
            ->paginate(30)->appends(request()->except('page'));
        return view('pharmacy.pharmacy_medicine_patients', compact('medicinePatients'));
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
        $patient_id = $request->patient_id;
        $medicine_id = $request->medicine_id;
        $quantity = $request->quantity;
        $sell_price = $request->sell_price;
        foreach ($medicine_id ?? [] as $key => $medicine) {
            $patientMedicine = new PatientPharmacyMedicine();
            $patientMedicine->patient_id = $patient_id;
            $patientMedicine->medicine_id = $medicine;
            $patientMedicine->quantity = $quantity[$key];
            $patientMedicine->unit_price = $sell_price[$key];
            $patientMedicine->created_by = \Auth::user()->id;
            $patientMedicine->save();
        }

        return  redirect()->back()->with('alert', 'The Medicine has been sold Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientPharmacyMedicine  $patientPharmacyMedicine
     * @return \Illuminate\Http\Response
     */
    public function show(PatientPharmacyMedicine $patientPharmacyMedicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientPharmacyMedicine  $patientPharmacyMedicine
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientPharmacyMedicine $patientPharmacyMedicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientPharmacyMedicine  $patientPharmacyMedicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $deletePreviousMedicine = PatientPharmacyMedicine::where('patient_id', $id)->delete();
        $patient_id = $request->patient_id;
        $medicine_id = $request->medicine_id;
        $quantity = $request->quantity;
        $sell_price = $request->sell_price;
        foreach ($medicine_id as $key => $medicine) {
            $patientMedicine = new PatientPharmacyMedicine();
            $patientMedicine->patient_id = $patient_id;
            $patientMedicine->medicine_id = $medicine;
            $patientMedicine->quantity = $quantity[$key];
            $patientMedicine->unit_price = $sell_price[$key];
            $patientMedicine->created_by = \Auth::user()->id;
            $patientMedicine->save();
        }

        return  redirect()->back()->with('alert', 'The Medicine has been edited Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientPharmacyMedicine  $patientPharmacyMedicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientPharmacyMedicine $patientPharmacyMedicine)
    {
        //
    }

    public function previewPatientMedicines()
    {
        $patient_id = $_GET['patient_id'];
        $soldMedicines =  PatientPharmacyMedicine::where('patient_id', $patient_id)->get();
        $patient = Patient::where('id', $patient_id)->with('createdBy')->first();
        return view('ajax.ajax_preview_medicines', compact('soldMedicines', 'patient'));
    }

    public function patient_reception_medicine()
    {
        // $medicinePatients = Patient::wherehas('pharmacyMedicines', function ($q){
        // })->with('pharmacyMedicines')->latest()->paginate(20);


        $medicinePatients = Patient::whereIn('patients.id', function ($query) {
            $query->from('patient_pharmacy_medicines')
                ->select('patient_pharmacy_medicines.patient_id');
        })->with('pharmacyMedicines', 'createdBy', 'doctor')->latest()->paginate(20);

        return view('reception.reception_pharmacy_patients', compact('medicinePatients'));
    }

    public function pharmacyEditPatientMedicines()
    {
        $patient_id = $_GET['patient_id'];
        $medicines = PatientPharmacyMedicine::where('patient_id', $patient_id)->with('patient', 'medicine')->get();
        return view('ajax.ajax_pharmacy_edit_patient_medicine', compact('medicines', 'patient_id'));
    }

    public function complete_medicine($id)
    {
        \DB::table('patient_medicines')->where('patient_id', $id)->update(['status' => 1]);
        \DB::table('patient_pharmacy_medicines')->where('patient_id', $id)->update(['status' => 1]);
        return  redirect()->back()->with('alert', 'The Medicine has been Completed Successfully')->with('alert-type', 'alert-info');
    }

    //Uncomplete Medicine
    public function uncomplete_medicine($id)
    {
        \DB::table('patient_medicines')->where('patient_id', $id)->update(['status' => 0]);
        \DB::table('patient_pharmacy_medicines')->where('patient_id', $id)->update(['status' => 0]);
        return  redirect()->back()->with('alert', 'The Medicine has been UnCompleted Successfully')->with('alert-type', 'alert-info');
    }

    public function search_reception_medicine_patient(Request $request)
    {
        $patientSearchDetail = $request->search_patient;
        $medicinePatients = Patient::whereIn('patients.id', function ($query) {
            $query->from('patient_pharmacy_medicines')
                ->select('patient_pharmacy_medicines.patient_id');
        })->where(function ($query) use ($patientSearchDetail) {
            $query->where('patient_name', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail . '%');
        })->with('pharmacyMedicines', 'createdBy', 'doctor')->latest()->paginate(100);
        return view('reception.reception_pharmacy_patients', compact('medicinePatients', 'patientSearchDetail'));
    }
}