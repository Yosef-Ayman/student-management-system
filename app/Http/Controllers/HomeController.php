<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function count()
    {
        $count = [
            'teacher' => User::teacherUsers()->count(),
            'parent' => User::parentUsers()->count(),
            'student' => User::studentUsers()->count()
        ];

        return view('welcome', compact('count'));
    }
}
