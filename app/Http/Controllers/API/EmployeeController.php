<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::select('SELECT e.*,d.name as department_name, r.name as room_name FROM employees as e 
        LEFT JOIN departments as d ON e.department_id=d.id
        LEFT JOIN rooms as r ON e.room_id=r.id');
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
            'firstname'=>'required',
            'lastname'=>'required',
            'birthday'=>'required',
            'email'=>'required|email',
            'is_manager'=>'required',
            'department_id'=>'nullable',
            'room_id'=>'nullable',
        ]);

        return DB::insert("INSERT into employees (firstname,lastname,birthday,email,is_manager,department_id,room_id) VALUES (?,?,?,?,?,?,?)",array_values($data));
    }

    /**
     * Get the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $query = DB::select('SELECT e.*,d.name as department_name, r.name as room_name FROM employees as e 
        LEFT JOIN departments as d ON e.department_id=d.id
        LEFT JOIN rooms as r ON e.room_id=r.id WHERE e.id=?',[$id]);

        return isset($query[0])? (array)$query[0]: null;
    }

    /**
     * Get salaries for the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function getSalaries($id)
    {
        return DB::select('SELECT * FROM salaries WHERE employee_id=?', [$id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'birthday'=>'required',
            'email'=>'required|email',
            'is_manager'=>'required',
            'department_id'=>'nullable',
            'room_id'=>'nullable',
        ]);

        return DB::update('UPDATE employees SET firstname=?,lastname=?,birthday=?,email=?,is_manager=?,department_id=?,room_id=? WHERE id=?', [...array_values($data),$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::delete('DELETE FROM employees WHERE id=?',[$id]);
    }
}
