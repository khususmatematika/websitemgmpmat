<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;

class GuruDashboardController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\GuruNav::items(),
            'guard' => 'guru',
            'panelTitle' => 'Panel Guru',
        ];
    }

    public function index()
    {
        return view('guru.dashboard', $this->nav());
    }
}