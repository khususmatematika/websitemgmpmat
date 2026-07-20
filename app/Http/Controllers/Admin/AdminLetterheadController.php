<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letterhead;
use Illuminate\Http\Request;

class AdminLetterheadController extends Controller
{
   protected function nav(): array
{
    return [
        'navItems' => \App\Support\AdminNav::items(),
        'guard' => 'admin',
        'panelTitle' => 'Panel Admin',
    ];
}

    public function edit()
    {
        $letterhead = Letterhead::current();
        return view('admin.letterhead.edit', ['letterhead' => $letterhead] + $this->nav());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
        ]);

        $letterhead = Letterhead::current();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('letterhead', 'public');
        }

        $letterhead->update(collect($data)->except('logo')->toArray() + ($request->hasFile('logo') ? ['logo_path' => $data['logo_path']] : []));

        return back()->with('status', 'Konfigurasi kop surat berhasil disimpan.');
    }
}