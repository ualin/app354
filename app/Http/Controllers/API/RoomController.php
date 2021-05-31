<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::select('SELECT * FROM rooms');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|unique:rooms',
        ]);

        return DB::insert("INSERT into rooms (name) VALUES (?)",array_values($data));
    }

    /**
     * Get the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function get(Room $room)
    {
        return $room;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'=>'required|unique:rooms,name,'.$id,
        ]);

        return DB::insert("UPDATE rooms SET name=? WHERE id=?",[$data['name'],$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $room;
        return DB::delete('DELETE FROM rooms WHERE id=?',[$id]);
    }
}
