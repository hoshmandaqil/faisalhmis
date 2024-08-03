<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Patient;
use App\Models\PatientIPD;
use Illuminate\Http\Request;

class PatientIPDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ipdPatients = Patient::wherehas('ipd', function ($q){
            $q->where('status', 0);
        })->latest()->with('ipd.floor', 'createdBy', 'doctor')->paginate(20);
        return view('reception.reception_ipd_patients', compact('ipdPatients'));
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
        $bedDetails = Floor::where('id', $request->bed_id)->first();
        $patientIpd = new PatientIPD();
        $patientIpd->patient_id = $request->patient_id;
        $patientIpd->bed_id = $request->bed_id;
        $patientIpd->price = $bedDetails->price;
        
        if (Patient::find($request->patient_id)->no_discount == 0) {
            $patientIpd->discount = $bedDetails->discount;
        } else {
            $patientIpd->discount = 0;
        }
        
        $patientIpd->created_by = \Auth::user()->id;
        $patientIpd->remark = $request->remark;

        if ($patientIpd->save()){
            // Make the bed status to busy.
            $bedDetails->status = 1;
            $bedDetails->save();
            return  redirect()->back()->with('alert', 'The IPD added Successfully')->with('alert-type', 'alert-success');
        }
        else {
            return  redirect()->back()->with('alert', 'An error Occurred, Please try again!')->with('alert-type', 'alert-danger');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientIPD  $patientIPD
     * @return \Illuminate\Http\Response
     */
    public function show(PatientIPD $patientIPD)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientIPD  $patientIPD
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientIPD $patientIPD)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientIPD  $patientIPD
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $patientIPD = PatientIPD::where('id', $id)->first();
        $time = explode(' ', $patientIPD->created_at)[1];
        $patientIPD->price = $request->price;
        $patientIPD->discount = $request->discount;
        $patientIPD->discharge_date = $request->discharge_date;
        $patientIPD->created_at = $request->admitted_date.' '.$time;
        $patientIPD->save();
        return  redirect()->back()->with('alert', 'Patient IPD Edited Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientIPD  $patientIPD
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientIPD $patientIPD)
    {
        //
    }

    public function search_reception_ipd_patient(Request $request)
    {
        $patientSearchDetail = $request->search_patient;

        $ipdPatients = Patient::wherehas('ipd', function ($q){
            $q->where('status', 0);
        })->where(function ($query) use ($patientSearchDetail) {
            $query->where('patient_name', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail. '%')
                ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail. '%');
        })->latest()->with('ipd.floor', 'doctor', 'createdBy')->paginate(100);
        return view('reception.reception_ipd_patients', compact('ipdPatients', 'patientSearchDetail'));
    }

     public function dischargePatient($id)
    {
        $ipdPatient = PatientIPD::where('id', $id)->orderBy('id', 'desc')->first();
        $ipdPatient->discharge_date = date('Y-m-d');
        $ipdPatient->status = 1;
        $ipdPatient->discharged_by = \Auth::user()->id;

        if ( $ipdPatient->save()){
           $freeTheBed =  Floor::where('id', $ipdPatient->bed_id)->first();
           $freeTheBed->status = 0;
            $freeTheBed->save();
            return  redirect()->back()->with('alert', 'Patient discharged Successfully')->with('alert-type', 'alert-success');
        }
        return  redirect()->back()->with('alert', 'An error Occurred!')->with('alert-type', 'alert-danger');

    }
}
