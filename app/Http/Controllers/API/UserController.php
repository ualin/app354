<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::select('SELECT id,name,email FROM users');
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
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required',
        ]);

        $data['password'] = Hash::make($data['password']);

        return DB::insert('INSERT into users (name,email,password) VALUES (?,?,?)',array_values($data));
    }

    /**
     * Get the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $query = DB::select('select id,name,email from users where id=?',[$id]);
        return isset($query)? (array)$query[0]: null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,id,'.$id,
            'password'=>'nullable|confirmed',
            'password_confirmation'=>'nullable',
        ]);

        $data['password'] = Hash::make($data['password']);
        $params = 'name=?,email=?,password=?';
        unset($data['password_confirmation']);
        
        if(!isset($data['password']))
        {
            $params = 'name=?,email=?';
            unset($data['password']);
        }


        return DB::update("UPDATE users SET $params WHERE id=?",[...array_values($data),$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
