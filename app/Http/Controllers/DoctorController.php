<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Floor;
use App\Models\LabDepartment;
use App\Models\MainLabDepartment;
use App\Models\MedicineName;
use App\Models\Patient;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $doctors = Doctor::latest()->paginate(20);
        return view('doctor.doctors', compact('doctors'));
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
        $doctor = new Doctor();
        $doctor->doctor_name = $request->doctor_name;
        $doctor->doctor_fname = $request->doctor_fname;
        $doctor->doctor_phone = $request->doctor_phone;
        $doctor->save();
        return  redirect()->back()->with('alert', 'The Doctor added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        //
    }



    public function search_my_patient(Request $request)
    {
        $patientSearchDetail = $request->search_patient;

        //If patient is null
        if ($patientSearchDetail == null || empty($patientSearchDetail)) {
            return back()->with('alert', 'The search value cannot be null')->with('alert-type', 'alert-danger');
        }

        $patients =  Patient::where(function ($query) use ($patientSearchDetail) {
            $query->where('patient_name', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_fname', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_phone', 'Like', '%' . $patientSearchDetail . '%')
                ->orwhere('patient_generated_id', 'Like', '%' . $patientSearchDetail . '%');
        })->with('medicines', 'doctor', 'ipd', 'ipd.floor', 'labs', 'createdBy')->latest()->paginate(10);

        $floors = Floor::groupBy('floor_name')->pluck('floor_name', 'id')->all();
        $rooms = Floor::groupBy('room')->pluck('room', 'id')->all();
        $beds = Floor::pluck('bed', 'id')->all();

        $selectPharmacy = MedicineName::whereIn('medicine_names.id', function ($query) {
            $query->from('pharmacies')->select('pharmacies.medicine_id')->where('returned', 0)->where('expired', 0);
        })
            ->latest()
            ->select('id', 'medicine_name')
            ->with('thisMedicinePharmacy:medicine_id,quantity,sale_price')
            ->get()
            ->map(function ($pharmacy) {
                $pharmacy->setRelation('thisMedicinePharmacy', $pharmacy->thisMedicinePharmacy->take(2));
                return $pharmacy;
            })
            ->lazy();

        $selectLab = LabDepartment::latest()->select('id', 'dep_name', 'price', 'normal_range', 'main_dep_id')->with('mainDepartment')->get();
        $mainLabDepartments = MainLabDepartment::latest()->select('id', 'dep_name', 'discount')->get();
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
                'patientSearchDetail',
                'mainLabDepartments',
                'medicine_dosage'
            )
        );
    }
}
