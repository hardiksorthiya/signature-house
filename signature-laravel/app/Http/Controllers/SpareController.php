<?php

namespace App\Http\Controllers;

use App\Models\Spare;
use App\Models\Seller;
use App\Models\MachineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Spare::with(['sellers', 'machineCategories']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('machineCategories', function ($q) use ($request) {
                $q->where('machine_categories.id', $request->category_id);
            });
        }

        if ($request->filled('spare_type')) {
            $query->where('spare_type', $request->spare_type);
        }

        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $spares = $query->paginate(10)->withQueryString();
        $sellers = Seller::orderBy('seller_name')->get();
        $categories = MachineCategory::orderBy('name')->get();

        return view('spares.index', compact('spares', 'sellers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'spare_type' => 'required|in:mechanical,electrical',
            'quantity' => 'required|integer|min:0',
            'quantity_per_machine' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('spares', 'public');
        }

        $spare = Spare::create([
            'name' => $request->name,
            'description' => $request->description,
            'spare_type' => $request->spare_type,
            'quantity' => $request->quantity ?? 0,
            'quantity_per_machine' => $request->quantity_per_machine ?? 1,
            'image' => $imagePath,
        ]);

        // Attach machine categories
        if ($request->has('categories') && is_array($request->categories)) {
            $spare->machineCategories()->attach($request->categories);
        }

        // Attach sellers
        if ($request->has('sellers') && is_array($request->sellers)) {
            $spare->sellers()->attach($request->sellers);
        }

            return redirect()->route('spares.index')
                ->with('success', 'Spare added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Spare $spare)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'spare_type' => 'required|in:mechanical,electrical',
            'quantity' => 'required|integer|min:0',
            'quantity_per_machine' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $imagePath = $spare->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($spare->image) {
                Storage::disk('public')->delete($spare->image);
            }
            $imagePath = $request->file('image')->store('spares', 'public');
        }

        $spare->update([
            'name' => $request->name,
            'description' => $request->description,
            'spare_type' => $request->spare_type,
            'quantity' => $request->quantity ?? 0,
            'quantity_per_machine' => $request->quantity_per_machine ?? 1,
            'image' => $imagePath,
        ]);

        // Sync machine categories
        if ($request->has('categories')) {
            $spare->machineCategories()->sync($request->categories);
        } else {
            $spare->machineCategories()->detach();
        }

        // Sync sellers
        if ($request->has('sellers')) {
            $spare->sellers()->sync($request->sellers);
        } else {
            $spare->sellers()->detach();
        }

            return redirect()->route('spares.index')
                ->with('success', 'Spare updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spare $spare)
    {
        // Delete image if exists
        if ($spare->image) {
            Storage::disk('public')->delete($spare->image);
        }

        // Detach sellers
        $spare->sellers()->detach();

        // Detach categories
        $spare->machineCategories()->detach();

        $spare->delete();

            return redirect()->route('spares.index')
                ->with('success', 'Spare deleted successfully.');
    }

    /**
     * Show CSV import form
     */
    public function showImport()
    {
        return view('spares.import');
    }

    /**
     * Import spares from CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        
        // Remove header row
        $header = array_shift($data);
        
        // Normalize header (trim and lowercase)
        $header = array_map(function($h) {
            return trim(strtolower($h));
        }, $header);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because header is row 1, and array is 0-indexed
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Map row data to associative array
                $rowData = array_combine($header, array_pad($row, count($header), ''));
                
                // Validate required fields
                if (empty($rowData['name'])) {
                    $errors[] = "Row {$rowNumber}: Name is required";
                    $errorCount++;
                    continue;
                }
                
                if (empty($rowData['spare_type']) || !in_array(strtolower($rowData['spare_type']), ['mechanical', 'electrical'])) {
                    $errors[] = "Row {$rowNumber}: Spare Type must be 'mechanical' or 'electrical'";
                    $errorCount++;
                    continue;
                }
                
                $quantity = isset($rowData['quantity']) ? (int)$rowData['quantity'] : 0;
                if ($quantity < 0) {
                    $errors[] = "Row {$rowNumber}: Quantity must be 0 or greater";
                    $errorCount++;
                    continue;
                }
                
                // Handle image
                $imagePath = null;
                if (!empty($rowData['image'])) {
                    $imageInput = trim($rowData['image']);
                    
                    // Check if it's a URL
                    if (filter_var($imageInput, FILTER_VALIDATE_URL)) {
                        // Download image from URL
                        try {
                            $imageContent = @file_get_contents($imageInput);
                            if ($imageContent !== false) {
                                $extension = pathinfo(parse_url($imageInput, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                $imageName = 'imported_' . time() . '_' . uniqid() . '.' . $extension;
                                $imagePath = 'spares/' . $imageName;
                                Storage::disk('public')->put($imagePath, $imageContent);
                            } else {
                                $errors[] = "Row {$rowNumber}: Failed to download image from URL";
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Row {$rowNumber}: Failed to download image from URL: " . $e->getMessage();
                            // Continue without image
                        }
                    } else {
                        // Check if it's a local file path
                        // Try storage/app/public/spares/ first
                        $storagePath = storage_path('app/public/spares/' . $imageInput);
                        if (file_exists($storagePath)) {
                            $imagePath = 'spares/' . basename($imageInput);
                        } else {
                            // Try public/storage/spares/
                            $publicPath = public_path('storage/spares/' . $imageInput);
                            if (file_exists($publicPath)) {
                                $imageName = basename($imageInput);
                                // Copy file to storage
                                $imageContent = file_get_contents($publicPath);
                                Storage::disk('public')->put('spares/' . $imageName, $imageContent);
                                $imagePath = 'spares/' . $imageName;
                            } else {
                                $errors[] = "Row {$rowNumber}: Image file not found: {$imageInput}. Place images in storage/app/public/spares/ folder.";
                                // Continue without image
                            }
                        }
                    }
                }
                
                // Create spare
                $spare = Spare::create([
                    'name' => trim($rowData['name']),
                    'description' => !empty($rowData['description']) ? trim($rowData['description']) : null,
                    'spare_type' => strtolower(trim($rowData['spare_type'])),
                    'quantity' => $quantity,
                    'image' => $imagePath,
                ]);
                
                // Handle sellers (comma-separated seller names)
                if (!empty($rowData['sellers'])) {
                    $sellerNames = array_map('trim', explode(',', $rowData['sellers']));
                    $sellerIds = [];
                    
                    foreach ($sellerNames as $sellerName) {
                        $seller = Seller::where('seller_name', 'like', trim($sellerName))->first();
                        if ($seller) {
                            $sellerIds[] = $seller->id;
                        }
                    }
                    
                    if (!empty($sellerIds)) {
                        $spare->sellers()->attach($sellerIds);
                    }
                }
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = "Successfully imported {$successCount} inventory item(s).";
            if ($errorCount > 0) {
                $message .= " {$errorCount} row(s) had errors.";
            }
            
            return redirect()->route('spares.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV Import Error: ' . $e->getMessage());
            
            return redirect()->route('spares.index')
                ->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'inventory_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['name', 'description', 'spare_type', 'quantity', 'image', 'sellers']);
            
            // Sample rows
            fputcsv($file, ['Sample Inventory Item 1', 'Sample description', 'mechanical', '10', 'path/to/image.jpg', 'Seller1,Seller2']);
            fputcsv($file, ['Sample Inventory Item 2', 'Another description', 'electrical', '5', 'https://example.com/image.jpg', 'Seller1']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
