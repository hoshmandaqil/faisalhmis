<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class ApiPatientContrller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 123;
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
        $patient = new Patient();
        $patient->patient_generated_id = $request->recept_id;
        $patient->patient_name = $request->name;
        $patient->patient_fname = $request->father_name;
        if ($request->gender == "female"){
            $patient->gender = 1;
        }
        else {
            $patient->gender = 0;
        }
        if ($request->marital_status == "married"){
            $patient->marital_status = 1;
        }
        else {
            $patient->marital_status = 0;
        }
        $patient->age = $request->age;
        if ($request->blood_na == 6){
            $patient->blood_group = "A";
        }
        elseif ($request->blood_na == 8){
            $patient->blood_group = "B";
        }
        elseif ($request->blood_na == 9){
            $patient->blood_group = "AB";
        }
        elseif ($request->blood_na == 10){
            $patient->blood_group = "O";
        }
        $patient->reg_date = $request->date;
        $patient->patient_phone = $request->phone_no;
        $patient->type = 1;
        $patient->save();
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
