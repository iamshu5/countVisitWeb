<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');
        $currentDate = now();

        $existingVisit = DB::table('countVisit')
            ->where('ip_address', $ipAddress)
            ->where('date', $currentDate)
            ->exists();

        if (!$existingVisit) {
            $location = 'Unknown'; // Default jika API gagal
            try {
                $response = Http::get("http://ip-api.com/json/{$ipAddress}");
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'success') {
                        $location = "{$data['city']}, {$data['regionName']}, {$data['country']}";
                    }
                }
            } catch (\Exception $e) {
                // Jika API gagal, biarkan lokasi sebagai "Unknown"
            }

            DB::table('countVisit')->insert([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'visited_at' => now(),
                'date' => $currentDate,
                'location' => $location,
            ]);
        }

        $count = DB::table('countVisit')->count();

        return view('welcome', ['count' => $count]);
    }
}
