<?php

namespace App\Http\Controllers;

use App\Models\MedicineName;
use App\Models\Patient;
use App\Models\PatientMedicine;
use App\Models\Pharmacy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientMedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $patientMedicineData = [];
        $medicines = $request->medicine_id;
        $quantity = $request->quantity;
        $patientId = $request->patient_id;
        $remark = $request->remark;
        foreach ($medicines as $key => $medicine){
            $patientMedicineData[$key]['id'] = $medicine;
            $patientMedicineData[$key]['qty'] = $quantity[$key];
            $patientMedicineData[$key]['remark'] = $remark[$key];
        }
        foreach ($patientMedicineData as $data){
            if ($data['id'] != NULL && $data['qty'] != NULL){
                \DB::table('patient_medicines')->insert(['patient_id'=> $patientId, 'medicine_id' => $data['id'],
                    'quantity' => $data['qty'], 'remark' => $data['remark'], 'created_by' => Auth::user()->id,'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        }
        
        return  redirect()->route('my_patients')->
        with('alert', 'The Medicine added To patient Successfully')->
        with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientMedicine  $patientMedicine
     * @return \Illuminate\Http\Response
     */
    public function show(PatientMedicine $patientMedicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientMedicine  $patientMedicine
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientMedicine $patientMedicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientMedicine  $patientMedicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $patientMedicineData = [];
        $medicines = $request->medicine_id;
        $quantity = $request->quantity;
        $patientId = $request->patient_id;
        $remark = $request->remark;
        $deletePreviousMedicine = PatientMedicine::where('patient_id', $id)->delete();
        foreach ($medicines as $key => $medicine){
            $patientMedicineData[$key]['id'] = $medicine;
            $patientMedicineData[$key]['qty'] = $quantity[$key];
            $patientMedicineData[$key]['remark'] = $remark[$key];
        }
        foreach ($patientMedicineData as $data){
            if ($data['id'] != NULL && $data['qty'] != NULL){
                \DB::table('patient_medicines')->insert(['patient_id'=> $patientId, 'medicine_id' => $data['id'],
                    'quantity' => $data['qty'], 'remark' => $data['remark'], 'created_by' => Auth::user()->id,'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        }
        return  redirect()->back()->with('alert', 'The Medicine Updated Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientMedicine  $patientMedicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientMedicine $patientMedicine)
    {
        //
    }

    public function getPatientMedicines()
    {
        $patient_id = $_GET['patient_id'];
        $medicines = PatientMedicine::where('patient_id', $patient_id)->with('patient', 'medicine')->get();
        return view('ajax.ajax_patient_medicine', compact('medicines', 'patient_id'));

    }

    public function getPatientMedicinesForEdit($id)
    {
        $medicines = PatientMedicine::where('patient_id', $id)->with('patient', 'medicine')->get();
        
        $selectPharmacy = MedicineName::whereIn('medicine_names.id', function ($query) {
            $query->from('pharmacies')
                ->select('pharmacies.medicine_id')->where('returned', 0)->where('expired', 0);
            })->latest()
            ->with('thisMedicinePharmacy')
            ->select('id', 'medicine_name')
            ->get()
            ->map(function ($pharmacy) {
                $pharmacy->setRelation('thisMedicinePharmacy', $pharmacy->thisMedicinePharmacy->take(2));
                return $pharmacy;
            })->lazy();


        return view('ajax.ajax_edit_patient_medicine', compact('medicines', 'id', 'selectPharmacy'));
    }

    public function search_medicine_patient(Request $request)
    {
        return redirect('/patient_pharmacy_medicine?search='.$request->search_patient);
    }
}
