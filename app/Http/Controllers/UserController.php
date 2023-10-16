<?php

namespace App\Http\Controllers;

use Auth;

class UserController extends Controller
{
	public function index(){
		return view('functions.employee.user.user');
 	}

 	public function aboutMe(){
		return response()->json(Auth::user());
	}

 	public function create(){
 	}

 	public function store(){
 	}

 	public function edit(){
 	}

 	public function update(){
 	}

 	public function destroy(){
 	}
}
