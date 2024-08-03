<?php

namespace App\Http\Controllers;

use App\Models\MedicineName;
use Illuminate\Http\Request;

class MedicineNameController extends Controller
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
        $medicines = $request->values;
        foreach ($medicines as $medicine){
            if ($medicine != NULL){
                $med = new MedicineName();
                $med->medicine_name= $medicine;
                $med->save();
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MedicineName  $medicineName
     * @return \Illuminate\Http\Response
     */
    public function show(MedicineName $medicineName)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MedicineName  $medicineName
     * @return \Illuminate\Http\Response
     */
    public function edit(MedicineName $medicineName)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicineName  $medicineName
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicineName $medicineName)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedicineName  $medicineName
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicineName $medicineName)
    {
        //
    }

    public function getMedicines()
    {
         $medicines = MedicineName::pluck('medicine_name','id')->toArray();
//         $medicines =array_reverse($medicines);
         return $medicines;
    }
}
