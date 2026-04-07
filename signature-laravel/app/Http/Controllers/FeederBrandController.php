<?php

namespace App\Http\Controllers;

use App\Models\FeederBrand;
use Illuminate\Http\Request;

class FeederBrandController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:feeder_brands,name',
        ]);

        $feederBrand = FeederBrand::create([
            'name' => $request->name,
        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'feederBrand' => $feederBrand,
                'message' => 'Feeder brand added successfully.'
            ]);
        }

        // Redirect for form submissions
        return redirect()->back()
            ->with('success', 'Feeder brand added successfully.');
    }

    /**
     * Remove the specified feeder brand.
     */
    public function destroy(Request $request, FeederBrand $feederBrand)
    {
        if ($feederBrand->feeders()->exists()) {
            $message = 'Cannot delete feeder brand because feeders are linked to it.';

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $feederBrand->delete();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feeder brand deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Feeder brand deleted successfully.');
    }
}
