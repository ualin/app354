<?php

namespace App\Http\Controllers\API;

use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return DB::select('SELECT * FROM departments');
        return DB::select('SELECT d.*, GROUP_CONCAT(r.name SEPARATOR ", ") as rooms FROM departments as d 
            LEFT JOIN rooms_departments as rd ON d.id=rd.department_id 
            LEFT JOIN rooms as r ON rd.room_id=r.id AND rd.deleted=0
            GROUP BY d.id');
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
            'name'=>'required|unique:departments',
            'rooms'=>'nullable',
            'rooms.*'=>'numeric'
        ]);

        $id = DB::table('departments')->insertGetId(['name'=>$data['name']]);

        if($data['rooms'] && count($data['rooms']))
        {
            $rooms = $data['rooms'];

            $addThis = $id;
            $every = 1;

            // prepare query params
            $count = ceil(count($rooms) / $every) - 1;
            for ($i = 0; $i < $count; $i++) {
                array_splice($rooms, $i * ($every + 1) + $every, 0, $addThis);
            }

            $placeholders = [];
            array_walk($rooms, function($v)use(&$placeholders){ $placeholders[]='?';});
            $placeholders = implode(',',$placeholders);

            DB::statement("INSERT into rooms_departments(room_id,department_id,deleted) SELECT rooms.id,$id,0 FROM rooms WHERE rooms.id IN($placeholders) ON DUPLICATE KEY UPDATE deleted = 0 ", $rooms);

            // Soft delete entries
            return DB::update("UPDATE rooms_departments SET deleted=1 WHERE department_id=? AND room_id NOT IN ($placeholders)",[$id, ...$rooms]);
        }
    }

    /**
     * Get the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $departmentQuery = DB::select('SELECT * from departments WHERE id=?',[$id]);
        
        return isset($departmentQuery[0])? (array)$departmentQuery[0]: null;
    }

    /**
     * Get the rooms for the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function getRooms($id)
    {
        
        return DB::select('SELECT r.* FROM departments as d 
        JOIN rooms_departments as rd ON d.id=rd.department_id 
        JOIN rooms as r ON rd.room_id=r.id
        WHERE d.id=? AND rd.deleted=0',[$id]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'=>'required|unique:departments,name,'.$id,
            'rooms'=>'nullable',
            'rooms.*'=>'sometimes|numeric',
        ]);

        DB::update("UPDATE departments SET name=? WHERE id=?", [$data['name'], $id]);

        if($data['rooms'] && count($data['rooms']))
        {
            $rooms = $data['rooms'];

            $addThis = $id;
            $every = 1;

            // how often we need to add this
            $count = ceil(count($rooms) / $every) - 1;
            for ($i = 0; $i < $count; $i++) {
                array_splice($rooms, $i * ($every + 1) + $every, 0, $addThis);
            }

            $placeholders = [];
            array_walk($rooms, function($v)use(&$placeholders){ $placeholders[]='?';});
            $placeholders = implode(',',$placeholders);

            DB::statement("INSERT into rooms_departments(room_id,department_id,deleted) SELECT rooms.id,$id,0 FROM rooms WHERE rooms.id IN($placeholders) ON DUPLICATE KEY UPDATE deleted = 0 ", $rooms);

            // Soft delete 
            return DB::update("UPDATE rooms_departments SET deleted=1 WHERE department_id=? AND room_id NOT IN ($placeholders)",[$id, ...$rooms]);
        }
        else
        {
            // Soft delete all 
            return DB::update('UPDATE rooms_departments SET deleted=1 WHERE department_id=?',[$id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
    }
}
