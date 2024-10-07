<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $my_course = $user->courses()->with('category')->orderBy('id','desc')->get();

        return view('student.courses.index', [
            'my_courses'=> $my_course
        ]);
    }
}
