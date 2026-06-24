<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MobileAppController extends Controller
{
    public function index()
    {
        $iosTestFlightUrl = config('mobile-app.ios_testflight_url');
        $androidAvailable = $this->androidApkPath() !== null;

        return view('mobile-app.index', compact(
            'iosTestFlightUrl',
            'androidAvailable'
        ));
    }

    public function android(): BinaryFileResponse
    {
        $path = $this->androidApkPath();

        if (! $path) {
            abort(404, 'Android app is not available yet. Please contact your administrator.');
        }

        return response()->download(
            $path,
            'signature-in-house.apk',
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }

    public function manifest(Request $request)
    {
        $iconUrl = asset('images/sifavicon.png');

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::first();
                if ($settings?->favicon) {
                    $iconUrl = Storage::url($settings->favicon);
                }
            }
        } catch (\Throwable $e) {
            // use default icon
        }

        $startUrl = url('/login');

        return response()->json([
            'name' => config('app.name', 'Signature In House'),
            'short_name' => 'Signature',
            'description' => 'Signature In House — textile ERP mobile access',
            'start_url' => $startUrl,
            'scope' => url('/'),
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#e74343',
            'icons' => [
                [
                    'src' => $iconUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => $iconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ], 200, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }

    protected function androidApkPath(): ?string
    {
        $candidates = [
            base_path('../downloads/signature-in-house.apk'),
            base_path('../signature-mobile/dist/signature-in-house.apk'),
        ];

        foreach ($candidates as $path) {
            if (File::isFile($path)) {
                return $path;
            }
        }

        return null;
    }
}
