<?php

namespace App\Http\Controllers;

use App\Models\MainLabDepartment;
use Illuminate\Http\Request;
use App\Models\Permission;

class MainLabDepartmentController extends Controller
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
        $mainDepartments = MainLabDepartment::latest()->get();
        return view('Department.main_departments', compact('mainDepartments'));
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
        $dep = new MainLabDepartment();
        $dep->dep_name = $request->dep_name;
        $dep->discount = $request->discount;
        $dep->save();
        
         // It is that we should add every Lab department to a permission.
        $permission = new Permission();
        $permission->permission_name = $request->dep_name;
        $permission->permission_group = "Department";
        $permission->save();
        
        
        return  redirect()->back()->with('alert', 'The Department added Successfully')->with('alert-type', 'alert-success');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MainLabDepartment  $mainLabDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(MainLabDepartment $mainLabDepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MainLabDepartment  $mainLabDepartment
     * @return \Illuminate\Http\Response
     */
    public function edit(MainLabDepartment $mainLabDepartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MainLabDepartment  $mainLabDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MainLabDepartment $mainLabDepartment)
    {
        $dep = MainLabDepartment::find($request->dep_id);
        $dep->dep_name = $request->dep_name;
        $dep->discount = $request->discount;
        $dep->save();
        return  redirect()->back()->with('alert', 'The Department Updated Successfully')->with('alert-type', 'alert-info');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MainLabDepartment  $mainLabDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MainLabDepartment $mainLabDepartment, $id)
    {
        $dep = MainLabDepartment::find($id);
        $dep->delete();
        return  redirect()->back()->with('alert', 'The Department Deleted Successfully')->with('alert-type', 'alert-info');

    }
}
