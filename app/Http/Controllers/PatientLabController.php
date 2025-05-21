<?php

namespace App\Http\Controllers;

use App\Models\LabDepartment;
use App\Models\Patient;
use App\Models\PatientLab;
use Illuminate\Http\Request;

class PatientLabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labPatients = Patient::wherehas('labs', function ($q){
        })->with('labs', 'laboratoryTests', 'labs.lab.mainDepartment', 'doctor', 'createdBy')->latest()->paginate(30);
        return view('Laboratory.labratory_patients_lab', compact('labPatients'));
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
        $deps = $request->labDeps;
        $remark = $request->remark;
        foreach ($deps as $key => $dep){
            $patientLab = new PatientLab();
            $patientLab->patient_id = $patient_id;
            $patientLab->lab_id = $dep;
            $patientLab->remark = $remark[$key];
            $patientLab->created_by = \Auth::user()->id;
            $patientLab->save();
        }
        return  redirect()->route('my_patients')->with('alert', 'The Labs added Successfully')->with('alert-type', 'alert-success');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientLab  $patientLab
     * @return \Illuminate\Http\Response
     */
    public function show(PatientLab $patientLab)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientLab  $patientLab
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientLab $patientLab)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientLab  $patientLab
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $patient_id = $request->patient_id;
        $deps = $request->labDeps;
        $remark = $request->remark;
        $deletePreviousLabs = PatientLab::where('patient_id', $patient_id)->delete();
        foreach ($deps as $key => $dep){
            $patientLab = new PatientLab();
            $patientLab->patient_id = $patient_id;
            $patientLab->lab_id = $dep;
            $patientLab->remark = $remark[$key];
            $patientLab->created_by = \Auth::user()->id;
            $patientLab->save();
        }
        return  redirect()->route('my_patients')->with('alert', 'The Labs Updated Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientLab  $patientLab
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientLab $patientLab)
    {
        //
    }

    public function laboratoryGetPatientLabs()
    {
        $patient_id = $_GET['patient_id'];
        $labs = PatientLab::where('patient_id', $patient_id)->with('patient', 'lab', 'lab.mainDepartment')->get();
        return view('ajax.ajax_laboratory_set_lab', compact('labs', 'patient_id'));
    }

    public function getPatientLabsForEdit($id)
    {
        $patient_id = $id;
        $labs = PatientLab::where('patient_id', $patient_id)->with('patient', 'lab')->get();
        $patient = Patient::find($id);
        $selectLab = LabDepartment::latest()->select('id', 'dep_name', 'price', 'normal_range', 'main_dep_id')->with('mainDepartment')->get();
        return view('ajax.ajax_patient_labs_edit', compact('labs', 'selectLab', 'patient_id', 'patient'));
    }

    public function search_laboratory_lab_patients(Request $request)
    {
        $patientSearchDetail = $request->search_patient;
        $labPatients = Patient::wherehas('labs', function ($q){
        })->where(function ($query) use ($patientSearchDetail) {
            $query->where('patient_name', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail. '%');
        })->with('labs', 'laboratoryTests', 'labs.lab.mainDepartment', 'createdBy', 'doctor')->latest()->paginate(100);
        return view('Laboratory.labratory_patients_lab', compact('labPatients', 'patientSearchDetail'));
    }
    
    
    public function search_reception_lab_patient(Request $request)
    {

        $patientSearchDetail = $request->search_patient;
        $labPatients = Patient::whereIn('patients.id', function($query){
            $query->from('laboratory_patient_labs')
                ->select('laboratory_patient_labs.patient_id');
        })->where(function ($query) use ($patientSearchDetail) {
            $query->where('patient_name', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail. '%');
        })->with('laboratoryTests', 'laboratoryTests.testName', 'createdBy', 'doctor')->latest()->paginate(100);
        return view('reception.reception_lab_patients', compact('labPatients', 'patientSearchDetail'));
    }
}
