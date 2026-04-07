<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeLocationController extends Controller
{
    /**
     * Show the employee location map (admin only).
     */
    public function index()
    {
        $this->authorize('view employee location');
        $mapsKey = config('services.google.maps_key');
        return view('employee-location.index', compact('mapsKey'));
    }

    /**
     * JSON: latest location per user for map markers.
     */
    public function locations()
    {
        $this->authorize('view employee location');

        $sub = UserLocation::query()
            ->select('user_id', DB::raw('MAX(recorded_at) as recorded_at'))
            ->groupBy('user_id');

        $locations = UserLocation::query()
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('user_locations.user_id', '=', 'latest.user_id')
                    ->on('user_locations.recorded_at', '=', 'latest.recorded_at');
            })
            ->with('user:id,name,email')
            ->select('user_locations.*')
            ->get();

        return response()->json([
            'locations' => $locations->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'user_id' => $loc->user_id,
                    'user_name' => $loc->user->name ?? '—',
                    'user_email' => $loc->user->email ?? '',
                    'latitude' => (float) $loc->latitude,
                    'longitude' => (float) $loc->longitude,
                    'accuracy' => $loc->accuracy,
                    'recorded_at' => $loc->recorded_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Store or update current user's location (any authenticated user).
     * Throttle: minimum 15 seconds between updates per user for live tracking.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $last = UserLocation::where('user_id', $user->id)->orderByDesc('recorded_at')->first();
        if ($last && $last->recorded_at->diffInSeconds(now()) < 15) {
            return response()->json(['success' => true, 'message' => 'Location updated.', 'throttled' => true]);
        }

        UserLocation::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Location updated.']);
    }

    /**
     * Page for any employee to share their current location.
     */
    public function shareLocationPage()
    {
        return view('employee-location.share');
    }
}
