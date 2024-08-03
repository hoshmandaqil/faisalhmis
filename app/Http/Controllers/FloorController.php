<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!in_array('floor_list', user_permissions())){
            return view('access_denied');
        }
        $floors = Floor::all();
        return view('floor.floors', compact('floors'));
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
        if (!in_array('add_floor', user_permissions())){
            return view('access_denied');
        }
        $floors = [];
        $floorName = $request->floor_name;
        $rooms = $request->rooms;
        $numberBeds = $request->beds;
        $remark = $request->remark;
        $price = $request->prices;
        $discount = $request->discounts;
        foreach ($rooms as $key => $room){
            $floors[$key]['room'] = $room;
            $floors[$key]['beds'] = $numberBeds[$key];
            $floors[$key]['price'] = $price[$key];
            $floors[$key]['discount'] = $discount[$key];
        }
        foreach ($floors as $data){
            if ($data['room'] != NULL && $data['beds'] != NULL){
                for($i=1 ; $i <= $data['beds']; $i++){
                    \DB::table('floors')->insert(['floor_name'=> $floorName, 'room' => $data['room'],
                        'bed' => $i, 'remark' => $remark, 'price' => $data['price'], 'discount' => $data['discount'] ]);
                }
            }
        }
        return  redirect()->back()->with('alert', 'The Floor added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Floor  $floor
     * @return \Illuminate\Http\Response
     */
    public function show(Floor $floor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Floor  $floor
     * @return \Illuminate\Http\Response
     */
    public function edit(Floor $floor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Floor  $floor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Floor $floor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Floor  $floor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Floor $floor)
    {
        //
    }

    public function getRooms()
    {
        $floor_name = $_GET['floor_id'];
        $rooms = Floor::where('floor_name', $floor_name)->select('room')->groupBy('room')->get();
        return view('ajax.ajax_rooms', compact('rooms'));
    }

    public function getBeds()
    {
        $room_name = $_GET['room_id'];
        $beds = Floor::where('room', $room_name)->select('bed', 'id', 'status')->orderBy('bed', 'DESC')->get();
        return view('ajax.ajax_bed', compact('beds'));
    }


}
