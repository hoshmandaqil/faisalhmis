<?php

namespace App\Http\Controllers;

use App\Models\LabDepartment;
use App\Models\MainLabDepartment;
use Illuminate\Http\Request;

class LabDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!in_array('lab_list', user_permissions())){
            return view('access_denied');
        }
        $departments = LabDepartment::with('mainDepartment')->latest()->get();
        $mainDepartments = MainLabDepartment::latest()->get();
        return view('Department.lab_departments', compact('departments', 'mainDepartments'));
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
        if (!in_array('add_lab', user_permissions())){
            return view('access_denied');
        }
        $dep = new LabDepartment();
        $dep->dep_name = $request->dep_name;
        $dep->price = $request->price;
        $dep->quantity = $request->quantity;
        $dep->main_dep_id = $request->main_dep_id;
        $dep->normal_range = $request->normal_range;
        $dep->save();
        return  redirect()->back()->with('alert', 'The Department added Successfully')->with('alert-type', 'alert-success');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LabDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(LabDepartment $labDepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LabDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function edit(LabDepartment $labDepartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LabDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LabDepartment $labDepartment)
    {
        $dep = LabDepartment::find($labDepartment->id);
        $dep->dep_name = $request->dep_name;
        $dep->price = $request->price;
        $dep->quantity = $request->quantity;
        $dep->main_dep_id = $request->main_dep_id;
        $dep->normal_range = $request->normal_range;
        $dep->save();
        return  redirect()->back()->with('alert', 'The Department Updated Successfully')->with('alert-type', 'alert-info');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LabDepartment  $labDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(LabDepartment $labDepartment)
    {
        $dep = LabDepartment::find($labDepartment->id);
        $dep->delete();
        return  redirect()->back()->with('alert', 'The Department Deleted Successfully')->with('alert-type', 'alert-info');

    }
}
