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
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:100',
            'embed_url' => 'required|url',
        ]);
        Toolkit::create($data);
        return redirect()->route('admin.toolkit.index')->with('status', 'Toolkit berhasil ditambahkan.');
    }

    public function edit(Toolkit $toolkit)
    {
        return view('admin.toolkit.edit', ['toolkit' => $toolkit] + $this->nav());
    }

    public function update(Request $request, Toolkit $toolkit)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:100',
            'embed_url' => 'required|url',
        ]);
        $toolkit->update($data);
        return redirect()->route('admin.toolkit.index')->with('status', 'Toolkit berhasil diperbarui.');
    }

    public function destroy(Toolkit $toolkit)
    {
        $toolkit->delete();
        return back()->with('status', 'Toolkit berhasil dihapus.');
    }
}