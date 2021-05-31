<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::select('SELECT * from salaries');
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
            'gross'=>'required|numeric',
            'net'=>'nullable|numeric',
            'month'=>'required',
            'year'=>'required',
            'employee_id'=>'required',
        ]);

        if(!isset($data['net'])){
            $data['net'] = 0.6*$data['gross'];
        }

        return DB::insert("INSERT into salaries (gross,net,`month`,`year`,employee_id) VALUES (?,?,?,?,?)",array_values($data));
    }

    /**
     * Get the specified resource.
     *
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $query = DB::select('SELECT * FROM salaries WHERE id=?', [$id]);

        return isset($query[0])? (array)$query[0]: null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'gross'=>'required',
            'net'=>'required',
            'month'=>'required',
            'year'=>'required',
            // 'employee_id'=>'required',
        ]);

        if(!isset($data['net'])){
            $data['net'] = 0.6*$data['gross'];
        }

        return DB::update('UPDATE salaries SET gross=?,net=?,`month`=?, `year`=? WHERE id=?', [...array_values($data),$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salary $salary)
    {
        //
    }
}
