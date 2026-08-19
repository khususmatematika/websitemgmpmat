<?php
namespace App\Http\Controllers;

use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Setiap browser context (termasuk Incognito, beda browser, atau setelah cookie dihapus)
        // akan mendapat visitor_uid baru → dihitung sebagai pengunjung unik.
        if (!$request->session()->has('visitor_uid')) {
            $request->session()->put('visitor_uid', (string) Str::uuid());
        }
        $identifier = $request->session()->get('visitor_uid');
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        VisitorLog::firstOrCreate([
            'identifier' => $identifier,
            'visit_date' => $today,
        ]);

        $visitorToday = VisitorLog::where('visit_date', $today)->count();
        $visitorTotal = VisitorLog::count();

        $recentWorks = \App\Models\StudentWork::where('status', 'approved')
        ->withCount('likes')
        ->latest()
        ->take(4)
        ->get();

        return view('public.home', compact('visitorToday', 'visitorTotal', 'recentWorks'));
    }
}