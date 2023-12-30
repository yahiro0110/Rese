<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UploadController extends Controller
{
    public function index()
    {
        // return view('index');
        return Inertia::render('Index');
    }

    public function store(Request $request)
    {
        $request->file('file')->store('');
    }
}
