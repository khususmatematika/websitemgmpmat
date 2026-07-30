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
        'headmaster_name' => 'nullable|string|max:255',
        'headmaster_nip' => 'nullable|string|max:50',
        'logo' => 'nullable|image|max:2048',
        'line1_text' => 'required|string|max:255',
        'line1_size' => 'required|integer|min:6|max:40',
        'line1_bold' => 'nullable|boolean',
        'line2_text' => 'required|string|max:255',
        'line2_size' => 'required|integer|min:6|max:40',
        'line2_bold' => 'nullable|boolean',
        'line3_text' => 'required|string|max:255',
        'line3_size' => 'required|integer|min:6|max:40',
        'line3_bold' => 'nullable|boolean',
        'line4_text' => 'nullable|string|max:255',
        'line4_size' => 'required|integer|min:6|max:40',
        'line4_bold' => 'nullable|boolean',
        'line5_text' => 'nullable|string|max:255',
        'line5_size' => 'required|integer|min:6|max:40',
        'line5_bold' => 'nullable|boolean',
    ]);

    $letterhead = Letterhead::current();

    if ($request->hasFile('logo')) {
        $data['logo_path'] = $request->file('logo')->store('letterhead', 'public');
    }

    $data['school_name'] = $data['line3_text']; // tetap sinkron untuk kompatibilitas fitur lain (Modul Ajar dll)
    $data['line1_bold'] = $request->boolean('line1_bold');
    $data['line2_bold'] = $request->boolean('line2_bold');
    $data['line3_bold'] = $request->boolean('line3_bold');
    $data['line4_bold'] = $request->boolean('line4_bold');
    $data['line5_bold'] = $request->boolean('line5_bold');

    $letterhead->update($data);

    return back()->with('status', 'Konfigurasi kop surat berhasil disimpan.');
}
}