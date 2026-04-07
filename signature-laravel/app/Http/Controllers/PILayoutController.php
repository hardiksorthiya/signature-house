<?php

namespace App\Http\Controllers;

use App\Models\PILayout;
use Illuminate\Http\Request;

class PILayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layouts = PILayout::orderBy('is_default', 'desc')->orderBy('name')->get();
        return view('pi-layouts.index', compact('layouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $defaultTemplate = $this->getDefaultTemplate();
        $sellers = \App\Models\Seller::orderBy('seller_name')->get();
        return view('pi-layouts.create', compact('defaultTemplate', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            PILayout::where('is_default', true)->update(['is_default' => false]);
        }

        $layout = PILayout::create([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'is_active' => $request->has('is_active'),
            'is_default' => $request->has('is_default'),
        ]);

        // Assign layout to selected sellers
        if ($request->has('sellers') && is_array($request->sellers)) {
            \App\Models\Seller::whereIn('id', $request->sellers)->update(['pi_layout_id' => $layout->id]);
        }

        return redirect()->route('settings.pi-layouts')
            ->with('success', 'PI Layout created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PILayout $piLayout)
    {
        return view('pi-layouts.show', compact('piLayout'));
    }

    /**
     * Preview the layout template
     */
    public function preview(PILayout $piLayout)
    {
        // Get a seller assigned to this layout, or get any seller
        $assignedSeller = $piLayout->sellers()->with(['country', 'bankDetails'])->first();
        
        // If no seller assigned to layout, get any seller
        if (!$assignedSeller) {
            $assignedSeller = \App\Models\Seller::with(['country', 'bankDetails'])->first();
        }

        // Get a sample proforma invoice for preview with the assigned seller
        $sampleProformaInvoice = \App\Models\ProformaInvoice::with([
            'contract.businessFirm',
            'contract.state',
            'contract.city',
            'contract.area',
            'seller.country',
            'seller.bankDetails',
            'creator',
            'proformaInvoiceMachines.contractMachine.machineCategory',
            'proformaInvoiceMachines.contractMachine.brand',
            'proformaInvoiceMachines.contractMachine.machineModel',
            'proformaInvoiceMachines.contractMachine.feeder.feederBrand',
            'proformaInvoiceMachines.contractMachine.machineHook',
            'proformaInvoiceMachines.contractMachine.machineERead',
            'proformaInvoiceMachines.contractMachine.color',
            'proformaInvoiceMachines.contractMachine.machineNozzle',
            'proformaInvoiceMachines.contractMachine.machineDropin',
            'proformaInvoiceMachines.contractMachine.machineBeam',
            'proformaInvoiceMachines.contractMachine.machineClothRoller',
            'proformaInvoiceMachines.contractMachine.machineSoftware',
            'proformaInvoiceMachines.contractMachine.hsnCode',
            'proformaInvoiceMachines.contractMachine.wir',
            'proformaInvoiceMachines.contractMachine.machineShaft',
            'proformaInvoiceMachines.contractMachine.machineLever',
            'proformaInvoiceMachines.contractMachine.machineChain',
            'proformaInvoiceMachines.contractMachine.machineHealdWire',
            'proformaInvoiceMachines.contractMachine.deliveryTerm',
        ])->whereHas('seller', function($query) use ($assignedSeller) {
            if ($assignedSeller) {
                $query->where('sellers.id', $assignedSeller->id);
            }
        })->first();

        // If no proforma invoice exists with this seller, create mock data with the assigned seller
        if (!$sampleProformaInvoice) {
            $sampleProformaInvoice = $this->createMockProformaInvoice($assignedSeller);
        } else {
            // Make sure seller is loaded with all relationships
            $sampleProformaInvoice->load(['seller.country', 'seller.bankDetails']);
            // Ensure country property exists even if null
            if ($sampleProformaInvoice->seller && (!isset($sampleProformaInvoice->seller->country) || $sampleProformaInvoice->seller->country === null)) {
                $sampleProformaInvoice->seller->country = (object)['name' => 'CHINA'];
            }
            // Ensure bankDetails is a collection
            if ($sampleProformaInvoice->seller && (!isset($sampleProformaInvoice->seller->bankDetails) || $sampleProformaInvoice->seller->bankDetails === null)) {
                $sampleProformaInvoice->seller->bankDetails = collect([]);
            }
            // Ensure all machine properties exist (even if null) to prevent undefined property errors
            foreach ($sampleProformaInvoice->proformaInvoiceMachines as $machine) {
                if (isset($machine->contractMachine)) {
                    // Initialize all machine properties to null if they don't exist
                    $properties = [
                        'machineHook', 'machineBeam', 'machineClothRoller', 'machineChain',
                        'machineHealdWire', 'machineERead', 'feeder', 'color', 'machineNozzle',
                        'machineDropin', 'machineSoftware', 'hsnCode', 'wir', 'machineShaft',
                        'machineLever', 'deliveryTerm'
                    ];
                    foreach ($properties as $property) {
                        if (!isset($machine->contractMachine->$property)) {
                            $machine->contractMachine->$property = null;
                        }
                    }
                    // For feeder, ensure feederBrand exists if feeder exists
                    if ($machine->contractMachine->feeder && !isset($machine->contractMachine->feeder->feederBrand)) {
                        $machine->contractMachine->feeder->feederBrand = null;
                    }
                    if (!isset($machine->description)) {
                        $machine->description = null;
                    }
                }
            }
            // Ensure contract has delivery_term (use loading_terms if available, otherwise use default)
            if ($sampleProformaInvoice->contract && !isset($sampleProformaInvoice->contract->delivery_term)) {
                // Contract doesn't have deliveryTerm relationship, use loading_terms field or default
                if (isset($sampleProformaInvoice->contract->loading_terms) && $sampleProformaInvoice->contract->loading_terms) {
                    $sampleProformaInvoice->contract->delivery_term = (object)['name' => $sampleProformaInvoice->contract->loading_terms];
                } else {
                    $sampleProformaInvoice->contract->delivery_term = (object)['name' => 'WITHIN 60 DAYS AFTER RECEIVING PAYMENT'];
                }
            }
        }

        // Compile the template HTML with sample data
        $previewHtml = $this->renderTemplate($piLayout->template_html, $sampleProformaInvoice);

        return response($previewHtml)->header('Content-Type', 'text/html');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PILayout $piLayout)
    {
        $sellers = \App\Models\Seller::orderBy('seller_name')->get();
        // Get sellers that have this layout assigned (hasMany relationship)
        // Query sellers where pi_layout_id matches this layout's id
        $selectedSellerIds = \App\Models\Seller::where('pi_layout_id', $piLayout->id)
            ->pluck('id')
            ->map(function($id) {
                return (string)$id;
            })
            ->toArray();
        return view('pi-layouts.edit', compact('piLayout', 'sellers', 'selectedSellerIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PILayout $piLayout)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default && !$piLayout->is_default) {
            PILayout::where('is_default', true)->where('id', '!=', $piLayout->id)->update(['is_default' => false]);
        }

        $piLayout->update([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'is_active' => $request->has('is_active'),
            'is_default' => $request->has('is_default'),
        ]);

        // Remove layout from all sellers first
        \App\Models\Seller::where('pi_layout_id', $piLayout->id)->update(['pi_layout_id' => null]);

        // Assign layout to selected sellers
        if ($request->has('sellers') && is_array($request->sellers)) {
            \App\Models\Seller::whereIn('id', $request->sellers)->update(['pi_layout_id' => $piLayout->id]);
        }

        return redirect()->route('settings.pi-layouts')
            ->with('success', 'PI Layout updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PILayout $piLayout)
    {
        // Check if any sellers are using this layout
        if ($piLayout->sellers()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete layout. It is being used by ' . $piLayout->sellers()->count() . ' seller(s).']);
        }

        $piLayout->delete();

        return redirect()->route('settings.pi-layouts')
            ->with('success', 'PI Layout deleted successfully.');
    }

    /**
     * Get default template HTML
     */
    private function getDefaultTemplate()
    {
        // Return a basic default template
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROFORMA INVOICE</h1>
        <p>PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
    </div>
    
    <div class="invoice-info">
        <p><strong>Contract:</strong> {{ $proformaInvoice->contract->contract_number }}</p>
        <p><strong>Customer:</strong> {{ $proformaInvoice->buyer_company_name }}</p>
        <p><strong>Date:</strong> {{ $proformaInvoice->created_at->format("d M Y") }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proformaInvoice->proformaInvoiceMachines as $machine)
            <tr>
                <td>{{ $machine->contractMachine->machineCategory->name ?? "Machine" }}</td>
                <td>{{ $machine->quantity }}</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($machine->amount, 2) }}</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($machine->amount * $machine->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" style="text-align: right;">Total:</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($proformaInvoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>';
    }

    /**
     * Render template HTML with proforma invoice data
     */
    private function renderTemplate($templateHtml, $proformaInvoice)
    {
        try {
            // Ensure seller is loaded with all relationships
            if ($proformaInvoice->seller) {
                // Only load relationships if seller is an Eloquent model
                if ($proformaInvoice->seller instanceof \Illuminate\Database\Eloquent\Model) {
                    if (!$proformaInvoice->seller->relationLoaded('country')) {
                        $proformaInvoice->seller->load('country');
                    }
                    if (!$proformaInvoice->seller->relationLoaded('bankDetails')) {
                        $proformaInvoice->seller->load('bankDetails');
                    }
                }
                // Ensure country property exists even if null
                if (!isset($proformaInvoice->seller->country) || $proformaInvoice->seller->country === null) {
                    $proformaInvoice->seller->country = (object)['name' => 'CHINA'];
                }
                // Ensure bankDetails is a collection
                if (!isset($proformaInvoice->seller->bankDetails) || $proformaInvoice->seller->bankDetails === null) {
                    $proformaInvoice->seller->bankDetails = collect([]);
                }
            } else {
                // If seller doesn't exist, create a mock one
                $proformaInvoice->seller = new \stdClass();
                $proformaInvoice->seller->id = 0;
                $proformaInvoice->seller->seller_name = 'Sample Seller';
                $proformaInvoice->seller->seller_name_chinese = '样例卖家';
                $proformaInvoice->seller->address = 'Seller Address, Seller City';
                $proformaInvoice->seller->mobile = '+1234567890';
                $proformaInvoice->seller->email = 'seller@example.com';
                $proformaInvoice->seller->signature = null;
                $proformaInvoice->seller->logo = null;
                $proformaInvoice->seller->country = (object)['name' => 'CHINA'];
                $proformaInvoice->seller->bankDetails = collect([]);
            }
            
            // Ensure contract has country if it exists
            if ($proformaInvoice->contract && !isset($proformaInvoice->contract->country)) {
                $proformaInvoice->contract->country = (object)['name' => 'India'];
            }
            // Ensure contract has delivery_term (Contract model doesn't have deliveryTerm relationship)
            // Use loading_terms field or default value
            if ($proformaInvoice->contract && !isset($proformaInvoice->contract->delivery_term)) {
                if (isset($proformaInvoice->contract->loading_terms) && $proformaInvoice->contract->loading_terms) {
                    $proformaInvoice->contract->delivery_term = (object)['name' => $proformaInvoice->contract->loading_terms];
                } else {
                    $proformaInvoice->contract->delivery_term = (object)['name' => 'WITHIN 60 DAYS AFTER RECEIVING PAYMENT'];
                }
            }
            
            // Ensure all machine relationships are loaded and properties exist
            if ($proformaInvoice->proformaInvoiceMachines) {
                foreach ($proformaInvoice->proformaInvoiceMachines as $machine) {
                    if (isset($machine->contractMachine)) {
                        // Only load relationships if contractMachine is an Eloquent model
                        if ($machine->contractMachine instanceof \Illuminate\Database\Eloquent\Model) {
                            // Load all machine relationships if not already loaded
                            $relationships = [
                                'machineCategory', 'brand', 'machineModel', 'feeder.feederBrand',
                                'machineHook', 'machineERead', 'color', 'machineNozzle',
                                'machineDropin', 'machineBeam', 'machineClothRoller',
                                'machineSoftware', 'hsnCode', 'wir', 'machineShaft',
                                'machineLever', 'machineChain', 'machineHealdWire', 'deliveryTerm'
                            ];
                            foreach ($relationships as $rel) {
                                if (strpos($rel, '.') !== false) {
                                    // Handle nested relationships like feeder.feederBrand
                                    $parts = explode('.', $rel);
                                    if (!$machine->contractMachine->relationLoaded($parts[0])) {
                                        $machine->contractMachine->load($rel);
                                    }
                                } else {
                                    if (!$machine->contractMachine->relationLoaded($rel)) {
                                        $machine->contractMachine->load($rel);
                                    }
                                }
                            }
                        }
                        
                        // Ensure all properties exist (even if null)
                        $properties = [
                            'machineHook', 'machineBeam', 'machineClothRoller', 'machineChain',
                            'machineHealdWire', 'machineERead', 'feeder', 'color', 'machineNozzle',
                            'machineDropin', 'machineSoftware', 'hsnCode', 'wir', 'machineShaft',
                            'machineLever', 'deliveryTerm', 'machineModel', 'brand', 'machineCategory'
                        ];
                        foreach ($properties as $property) {
                            if (!isset($machine->contractMachine->$property)) {
                                $machine->contractMachine->$property = null;
                            }
                        }
                        // For feeder, ensure feederBrand exists if feeder exists
                        if ($machine->contractMachine->feeder && !isset($machine->contractMachine->feeder->feederBrand)) {
                            $machine->contractMachine->feeder->feederBrand = null;
                        }
                        if (!isset($machine->description)) {
                            $machine->description = null;
                        }
                    }
                }
            }
            
            // Create a temporary view file
            $tempPath = storage_path('app/temp_template_' . uniqid() . '.blade.php');
            file_put_contents($tempPath, $templateHtml);

            // Compile the view with data
            $rendered = view()->file($tempPath, [
                'proformaInvoice' => $proformaInvoice,
                'seller' => $proformaInvoice->seller ?? null,
                'machines' => $proformaInvoice->proformaInvoiceMachines ?? collect(),
                'contract' => $proformaInvoice->contract ?? null,
            ])->render();

            // Clean up temporary file
            @unlink($tempPath);

            return $rendered;
        } catch (\Exception $e) {
            // Return error message if template fails to compile
            return '<div class="alert alert-danger">Error rendering template: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Create mock proforma invoice for preview
     */
    private function createMockProformaInvoice($seller = null)
    {
        $mock = new \stdClass();
        
        // Basic invoice data
        $mock->id = 0;
        $mock->proforma_invoice_number = 'PI-PREVIEW-001';
        $mock->buyer_company_name = 'Sample Company Ltd.';
        $mock->currency = 'USD';
        $mock->total_amount = 50000.00;
        $mock->created_at = now();
        $mock->billing_address = "123 Sample Street\nSample City, Sample State 12345";
        $mock->shipping_address = "123 Sample Street\nSample City, Sample State 12345";
        $mock->notes = 'This is a preview with sample data.';
        $mock->gst_percentage = 18;
        
        // Mock contract
        $mock->contract = new \stdClass();
        $mock->contract->contract_number = 'CONTRACT-PREVIEW-001';
        $mock->contract->buyer_name = 'Sample Customer';
        $mock->contract->company_name = 'Sample Company Ltd.';
        $mock->contract->businessFirm = (object)['name' => 'Sample Business Firm'];
        $mock->contract->state = (object)['name' => 'Sample State'];
        $mock->contract->city = (object)['name' => 'Sample City'];
        $mock->contract->area = (object)['name' => 'Sample Area'];
        $mock->contract->country = (object)['name' => 'India'];
        // Use loading_terms for delivery_term since Contract doesn't have deliveryTerm relationship
        $mock->contract->loading_terms = 'FOB';
        $mock->contract->payment_terms = '30 days';
        $mock->contract->contact_address = $mock->billing_address;
        // Create delivery_term object from loading_terms for template compatibility
        $mock->contract->delivery_term = (object)['name' => 'WITHIN 60 DAYS AFTER RECEIVING PAYMENT'];
        
        // Use real seller if provided, otherwise create mock seller
        if ($seller) {
            // Load seller relationships if not already loaded
            // Only load relationships if seller is an Eloquent model
            if ($seller instanceof \Illuminate\Database\Eloquent\Model) {
                if (!$seller->relationLoaded('country')) {
                    $seller->load('country');
                }
                if (!$seller->relationLoaded('bankDetails')) {
                    $seller->load('bankDetails');
                }
            }
            // Ensure country property exists even if null
            if (!isset($seller->country) || $seller->country === null) {
                $seller->country = (object)['name' => 'CHINA'];
            }
            // Ensure bankDetails is a collection
            if (!isset($seller->bankDetails) || $seller->bankDetails === null) {
                $seller->bankDetails = collect([]);
            }
            $mock->seller = $seller;
        } else {
            // Mock seller
            $mock->seller = new \stdClass();
            $mock->seller->id = 0;
            $mock->seller->seller_name = 'Sample Seller';
            $mock->seller->seller_name_chinese = '样例卖家';
            $mock->seller->address = 'Seller Address, Seller City';
            $mock->seller->mobile = '+1234567890';
            $mock->seller->email = 'seller@example.com';
            $mock->seller->signature = null;
            $mock->seller->logo = null;
            $mock->seller->country = (object)['name' => 'CHINA'];
            $mock->seller->bankDetails = collect([]);
        }
        
        // Mock creator
        $mock->creator = new \stdClass();
        $mock->creator->name = 'System User';
        
        // Mock machines collection
        $machine = new \stdClass();
        $machine->id = 1;
        $machine->quantity = 2;
        $machine->amount = 25000.00;
        
        $machine->contractMachine = new \stdClass();
        $machine->contractMachine->machineCategory = (object)['name' => 'Sample Machine Category'];
        $machine->contractMachine->brand = (object)['name' => 'Sample Brand'];
        $machine->contractMachine->machineModel = (object)['model_no' => 'MODEL-001'];
        $machine->contractMachine->color = (object)['name' => 'Red'];
        $machine->contractMachine->hsnCode = (object)['name' => 'HSN123456'];
        // Set all machine properties to null or empty objects to prevent undefined property errors
        $machine->contractMachine->feeder = null;
        $machine->contractMachine->machineSoftware = null;
        $machine->contractMachine->machineNozzle = null;
        $machine->contractMachine->machineBeam = null;
        $machine->contractMachine->machineClothRoller = null;
        $machine->contractMachine->machineShaft = null;
        $machine->contractMachine->machineChain = null;
        $machine->contractMachine->machineHealdWire = null;
        $machine->contractMachine->machineERead = null;
        $machine->contractMachine->machineHook = null;
        $machine->contractMachine->machineDropin = null;
        $machine->contractMachine->machineLever = null;
        $machine->contractMachine->wir = null;
        $machine->contractMachine->deliveryTerm = null;
        $machine->description = null;
        
        $mock->proformaInvoiceMachines = collect([$machine]);
        
        return $mock;
    }
}
