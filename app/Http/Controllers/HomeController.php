<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');
        $currentDate = now();

        // Periksa apakah kunjungan dari IP dan tanggal ini sudah ada
        $existingVisit = DB::table('countVisit')
            ->where('ip_address', $ipAddress)
            ->where('date', $currentDate)
            ->exists();

        if (!$existingVisit) {
            // Jika belum ada, catat kunjungan
            DB::table('countVisit')->insert([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'visited_at' => now(),
                'date' => $currentDate,
            ]);
        }

        $count = DB::table('countVisit')->count();

        return view('welcome', ['count' => $count]);
    }
}
