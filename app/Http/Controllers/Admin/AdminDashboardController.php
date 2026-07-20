<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\AdminNav::items(),
            'guard' => 'admin',
            'panelTitle' => 'Panel Admin',
        ];
    }

    public function index()
    {
        return view('admin.dashboard', $this->nav());
    }
}