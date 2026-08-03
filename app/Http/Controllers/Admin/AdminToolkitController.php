<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toolkit;
use Illuminate\Http\Request;

class AdminToolkitController extends Controller
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
        $toolkits = Toolkit::orderBy('title')->get();
        return view('admin.toolkit.index', ['toolkits' => $toolkits] + $this->nav());
    }

    public function create()
    {
        return view('admin.toolkit.create', $this->nav());
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'icon' => 'required|string|max:100',
        'input_type' => 'required|in:url,code',
    ]);

    $inputType = $request->input('input_type');

    if ($inputType === 'url') {
        $request->validate(['embed_url' => 'required|url']);
    } else {
        $request->validate(['embed_code' => 'required|string']);
    }

    Toolkit::create([
        'title' => $request->title,
        'icon' => $request->icon,
        'input_type' => $inputType,
        'embed_url' => $inputType === 'url' ? $request->embed_url : null,
        'embed_code' => $inputType === 'code' ? $request->embed_code : null,
    ]);

    return redirect()->route('admin.toolkit.index')->with('status', 'Toolkit berhasil ditambahkan.');
}

    public function edit(Toolkit $toolkit)
    {
        return view('admin.toolkit.edit', ['toolkit' => $toolkit] + $this->nav());
    }

    public function update(Request $request, Toolkit $toolkit)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'icon' => 'required|string|max:100',
        'input_type' => 'required|in:url,code',
    ]);

    $inputType = $request->input('input_type');

    if ($inputType === 'url') {
        $request->validate(['embed_url' => 'required|url']);
    } else {
        $request->validate(['embed_code' => 'required|string']);
    }

    $toolkit->update([
        'title' => $request->title,
        'icon' => $request->icon,
        'input_type' => $inputType,
        'embed_url' => $inputType === 'url' ? $request->embed_url : null,
        'embed_code' => $inputType === 'code' ? $request->embed_code : null,
    ]);

    return redirect()->route('admin.toolkit.index')->with('status', 'Toolkit berhasil diperbarui.');
}

    public function destroy(Toolkit $toolkit)
    {
        $toolkit->delete();
        return back()->with('status', 'Toolkit berhasil dihapus.');
    }
}