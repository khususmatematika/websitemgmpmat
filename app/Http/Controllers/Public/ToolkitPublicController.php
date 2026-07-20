<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Toolkit;

class ToolkitPublicController extends Controller
{
    public function index()
    {
        $toolkits = Toolkit::orderBy('title')->get();
        return view('public.toolkits.index', compact('toolkits'));
    }
}