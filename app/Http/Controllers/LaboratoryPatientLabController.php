<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryPatientLab;
use App\Models\Patient;
use App\Models\PatientLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class LaboratoryPatientLabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $lab_ids = $request->lab_id;
        $patient_id = $request->patient_id;
        $price = $request->price;
        $result = $request->result;
        $discount = $request->lab_discounts;
        $payable_amount = $request->payable_amount;
        foreach ($lab_ids as $key => $lab){
            $patientLab = new LaboratoryPatientLab();
            $patientLab->patient_id = $patient_id;
            $patientLab->lab_id = $lab;
            $patientLab->price = $payable_amount[$key];
            $patientLab->result = $result[$key];
            if (Patient::find($patient_id)->no_discount == 0) {
                $patientLab->discount = $discount[$key];
            } else {
                $patientLab->discount = 0;
            }
            if($request->hasFile('attachments')){
                if (array_key_exists($key, $request->file('attachments'))){
                    $imageName = time().'_'.$request->file('attachments')[$key]->getClientOriginalName();
                    $request->file('attachments')[$key]->move(public_path('laboratoryAttachments'), $imageName);
                    $patientLab->file = $imageName;
                }
            }
            $patientLab->created_by = Auth::user()->id;
            $patientLab->save();
        }
        return  redirect()->back()->with('alert', 'The Results  added Successfully')->with('alert-type', 'alert-success');

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
    public function update(Request $request, PatientLab $patientLab)
    {
        //
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

    public function reception_patient_labs()
    {
        // $labPatients = Patient::wherehas('laboratoryTests', function ($q){
        // })->with('laboratoryTests', 'laboratoryTests.testName')->latest()->paginate(20);

          $labPatients = Patient::whereIn('patients.id', function($query){
            $query->from('laboratory_patient_labs')
                ->select('laboratory_patient_labs.patient_id');
        })->with('laboratoryTests', 'laboratoryTests.testName', 'doctor', 'createdBy')->latest()->paginate(20);
        return view('reception.reception_lab_patients', compact('labPatients'));
    }

    public function previewPatientLabTests()
    {
        $patient_id = $_GET['patient_id'];
        $labs =  LaboratoryPatientLab::where('patient_id', $patient_id)->get();
        $patient = Patient::where('id', $patient_id)->first();
        return view('ajax.ajax_preview_lab_test', compact('labs', 'patient', 'patient_id'));

    }

    public function recent_entries_lab_patients()
    {
        $recent_entries = LaboratoryPatientLab::with('testName', 'patient')->latest()->where('patient_id', 'LIKE', '%'.request('search').'%')->paginate(100);
        return view('Laboratory.last_entries', compact('recent_entries'));
    }

    public function recent_entries_lab_patients_search(Request $request)
    {
        return redirect('recent_entries_lab_patients' . '?search=' . $request->search);
    }

    public function delete_patient_test($id)
    {
        $entry = LaboratoryPatientLab::where('id', $id)->first();
        $entry->delete();
        return  redirect()->back()->with('alert', 'The Test deleted Successfully')->with('alert-type', 'alert-success');

    }


    public function download_lab_files($id)
    {
        $files = DB::table('laboratory_patient_labs')->where('patient_id', $id)->select('file')->get();
        $downloadableFiles = [];
        foreach ($files as $file) {
            if ($file->file != NULL) {
                array_push($downloadableFiles, public_path('laboratoryAttachments/' . $file->file));
            }
        }
        $zip = new ZipArchive;
        $fileName = $id . '.zip';

        if(!empty($downloadableFiles)){
            if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {

                foreach ($downloadableFiles as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    $zip->addFile($value, $relativeNameInZipFile);
                }

                $zip->close();
            }

            return response()->download(public_path($fileName));
        }
        else {
            return view('no_file');
        }


    }


}
