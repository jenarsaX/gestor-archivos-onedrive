<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Dflydev\DotAccessData\Data;

class RegisteredUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        

        return response()->json(['message'=>'Usuario Registrado Correctamente']);
    }    


    protected function validator(array $data)
    {
        return Validator::make($data,[
            'name' => ['required', 'string', 'max:"255'],
            'email' => ['required', 'string','email' ,'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

}
