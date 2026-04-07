<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageController extends Controller
{
    /**
     * Serve storage files publicly
     */
    public function serve(Request $request, string $path)
    {
        // Ensure the file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        // Get the file path
        $filePath = Storage::disk('public')->path($path);
        
        // Get file info
        $mimeType = Storage::disk('public')->mimeType($path);
        $fileSize = Storage::disk('public')->size($path);
        
        // Return the file with proper headers
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
