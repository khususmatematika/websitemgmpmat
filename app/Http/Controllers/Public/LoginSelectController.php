<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class LoginSelectController extends Controller
{
    public function index()
    {
        return view('public.login-select');
    }
}