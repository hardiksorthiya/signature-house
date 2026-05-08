<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\ProformaInvoice;
use App\Models\User;
use App\Notifications\ContractPendingApprovalNotification;
use App\Models\MachineCategory;
use App\Models\Brand;
use App\Models\BusinessFirm;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\DeliveryTerm;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts with other contract details.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['lead', 'creator', 'businessFirm', 'state', 'city', 'area']);
        
        // Only Admin/Super Admin can view all contracts.
        // All other roles can see only contracts they created.
        if (!$this->canViewAllContracts()) {
            $query->where('created_by', auth()->id());
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('phone_number_2', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('area', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('businessFirm', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter by business firm
        if ($request->filled('business_firm_id')) {
            $query->where('business_firm_id', $request->business_firm_id);
        }
        
        $contracts = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $states = \App\Models\State::orderBy('name')->get();
        $businessFirms = \App\Models\BusinessFirm::orderBy('name')->get();
        $cities = $request->filled('state_id') 
            ? \App\Models\City::where('state_id', $request->state_id)->orderBy('name')->get()
            : collect([]);
        $areas = $request->filled('city_id') 
            ? \App\Models\Area::where('city_id', $request->city_id)->orderBy('name')->get()
            : collect([]);
        
        return view('contracts.index', compact('contracts', 'states', 'cities', 'areas', 'businessFirms'));
    }

    /**
     * Show the form for creating a new contract (without a lead).
     */
    public function create()
    {
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $stateId = old('state_id');
        $cityId = old('city_id');
        $cities = $stateId ? City::where('state_id', $stateId)->orderBy('name')->get() : collect([]);
        $areas = $cityId ? Area::where('city_id', $cityId)->orderBy('name')->get() : collect([]);
        $categories = MachineCategory::orderBy('name', 'desc')->get();
        $brands = Brand::orderBy('name')->get();
        $settings = Setting::firstOrCreate(['id' => 1]);

        $lastContract = Contract::orderBy('id', 'desc')->first();
        $contractNumber = 'CNT-' . str_pad(($lastContract ? $lastContract->id : 0) + 1, 6, '0', STR_PAD_LEFT);

        return view('contracts.create', compact('businessFirms', 'states', 'cities', 'areas', 'categories', 'brands', 'contractNumber', 'settings'));
    }

    /**
     * Store a newly created contract (without a lead).
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_firm_id' => 'required|exists:business_firms,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
            'buyer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
            'gst' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'token_amount' => 'nullable|numeric|min:0',
            'machines' => 'required|array|min:1',
            'machines.*.machine_category_id' => 'required|exists:machine_categories,id',
            'machines.*.brand_id' => 'nullable|exists:brands,id',
            'machines.*.machine_model_id' => 'nullable|exists:machine_models,id',
            'machines.*.seller_id' => 'nullable|exists:sellers,id',
            'machines.*.quantity' => 'required|integer|min:1',
            'machines.*.amount' => 'required|numeric|min:0',
            'machines.*.description' => 'nullable|string',
            'machines.*.feeder_id' => 'nullable|exists:feeders,id',
            'machines.*.machine_hook_id' => 'nullable|exists:machine_hooks,id',
            'machines.*.machine_e_read_id' => 'nullable|exists:machine_e_reads,id',
            'machines.*.color_id' => 'nullable|exists:colors,id',
            'machines.*.machine_nozzle_id' => 'nullable|exists:machine_nozzles,id',
            'machines.*.machine_dropin_id' => 'nullable|exists:machine_dropins,id',
            'machines.*.machine_beam_id' => 'nullable|exists:machine_beams,id',
            'machines.*.machine_cloth_roller_id' => 'nullable|exists:machine_cloth_rollers,id',
            'machines.*.machine_software_id' => 'nullable|exists:machine_softwares,id',
            'machines.*.hsn_code_id' => 'nullable|exists:hsn_codes,id',
            'machines.*.wir_id' => 'nullable|exists:wirs,id',
            'machines.*.machine_shaft_id' => 'nullable|exists:machine_shafts,id',
            'machines.*.machine_lever_id' => 'nullable|exists:machine_levers,id',
            'machines.*.machine_chain_id' => 'nullable|exists:machine_chains,id',
            'machines.*.machine_heald_wire_id' => 'nullable|exists:machine_heald_wires,id',
            'machines.*.machine_size_id' => 'nullable|exists:machine_sizes,id',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $contract = Contract::create(array_merge([
                    'lead_id' => null,
                    'created_by' => auth()->id(),
                    'business_firm_id' => $request->business_firm_id,
                    'contract_number' => $request->contract_number,
                    'buyer_name' => $request->buyer_name,
                    'company_name' => $request->company_name,
                    'contact_address' => $request->contact_address,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'area_id' => $request->area_id,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'phone_number_2' => $request->phone_number_2,
                    'gst' => $request->gst,
                    'pan' => $request->pan,
                ], Contract::otherBuyerExpensesForStore($request), [
                    'payment_terms' => $request->payment_terms,
                    'quote_validity' => $request->quote_validity,
                    'loading_terms' => $request->loading_terms,
                    'warranty' => $request->warranty,
                    'complimentary_spares' => $request->complimentary_spares,
                    'other_details_in_print' => $request->has('other_details_in_print') ? (bool)$request->other_details_in_print : true,
                    'spec_color_8_to_12_selectors' => $request->spec_color_8_to_12_selectors,
                    'spec_extra_feeder_per_pc' => $request->spec_extra_feeder_per_pc,
                    'spec_extra_warp_beam_per_pc' => $request->spec_extra_warp_beam_per_pc,
                    'spec_reed_reduction_per_20cm' => $request->spec_reed_reduction_per_20cm,
                    'spec_reed_increase_per_20cm' => $request->spec_reed_increase_per_20cm,
                    'spec_increase_380_to_480cm' => $request->spec_increase_380_to_480cm,
                    'spec_electronic_weft_cutter' => $request->spec_electronic_weft_cutter,
                    'spec_hooks_5376_to_6144' => $request->spec_hooks_5376_to_6144,
                    'spec_hooks_5376_to_10240' => $request->spec_hooks_5376_to_10240,
                    'spec_hooks_5376_to_2688' => $request->spec_hooks_5376_to_2688,
                    'spec_changshu_to_sns_cam' => $request->spec_changshu_to_sns_cam,
                    'spec_changshu_to_sns_chain_24' => $request->spec_changshu_to_sns_chain_24,
                    'spec_changshu_to_sns_chain_16' => $request->spec_changshu_to_sns_chain_16,
                    'spec_changshu_to_jkd_or_changfang' => $request->spec_changshu_to_jkd_or_changfang,
                    'spec_changshu_to_wumu' => $request->spec_changshu_to_wumu,
                    'spec_bintian_8_shaft_cam_heald' => $request->spec_bintian_8_shaft_cam_heald,
                    'spec_bintian_10_shaft_cam_heald' => $request->spec_bintian_10_shaft_cam_heald,
                    'spec_changshu_16_shaft_2861_dobby' => $request->spec_changshu_16_shaft_2861_dobby,
                    'spec_staubli_16_shaft_dobby' => $request->spec_staubli_16_shaft_dobby,
                    'spec_increase_1_nozzle' => $request->spec_increase_1_nozzle,
                    'spec_increase_1_feeder' => $request->spec_increase_1_feeder,
                    'spec_reed_increase_per_10cm' => $request->spec_reed_increase_per_10cm,
                    'spec_bintian_cam_plate_extra' => $request->spec_bintian_cam_plate_extra,
                    'spec_bintian_gear_extra' => $request->spec_bintian_gear_extra,
                    'difference_specification_in_print' => $request->has('difference_specification_in_print') ? (bool)$request->difference_specification_in_print : true,
                    'difference_specification_extended_in_print' => $request->boolean('difference_specification_extended_in_print'),
                    'spec_3_niupai_10_shaft_410_cam_heald_frames' => $request->spec_3_niupai_10_shaft_410_cam_heald_frames,
                    'spec_3_niupai_12_shaft_411_cam_heald_frames' => $request->spec_3_niupai_12_shaft_411_cam_heald_frames,
                    'spec_3_niupai_16_electronic_dobby_5400d_invertor' => $request->spec_3_niupai_16_electronic_dobby_5400d_invertor,
                    'spec_3_sanhe_s650_controller_accessories' => $request->spec_3_sanhe_s650_controller_accessories,
                    'spec_3_increase_1_colour' => $request->spec_3_increase_1_colour,
                    'spec_3_increase_1_feeder' => $request->spec_3_increase_1_feeder,
                    'spec_3_reed_increase_per_20cm' => $request->spec_3_reed_increase_per_20cm,
                    'spec_3_single_pump_to_double_pump' => $request->spec_3_single_pump_to_double_pump,
                    'difference_specification_3_in_print' => $request->boolean('difference_specification_3_in_print'),
                    'terms_government_policies' => $request->terms_government_policies,
                    'terms_currency' => $request->terms_currency,
                    'terms_licenses_bank_payment' => $request->terms_licenses_bank_payment,
                    'terms_demurrage_detentions' => $request->terms_demurrage_detentions,
                    'terms_cancellation_order' => $request->terms_cancellation_order,
                    'terms_jurisdiction_seller_rights' => $request->terms_jurisdiction_seller_rights,
                    'terms_conditions_in_print' => $request->boolean('terms_conditions_in_print', true),
                    'not_included_in_offer_in_print' => $request->has('not_included_in_offer_in_print') ? (bool) $request->not_included_in_offer_in_print : true,
                    'not_included_in_offer' => Contract::notIncludedInOfferPayloadFromRequest($request, 'not_included_in_offer'),
                ]));

                $totalAmount = 0;
                $machineDetails = [];

                foreach ($request->machines as $machineData) {
                    $quantity = (int)($machineData['quantity'] ?? 1);
                    $amount = (float)($machineData['amount'] ?? 0);
                    $machineTotal = $quantity * $amount;
                    $totalAmount += $machineTotal;

                    $machineDetail = [
                        'machine_category_id' => $machineData['machine_category_id'],
                        'brand_id' => $machineData['brand_id'] ?? null,
                        'machine_model_id' => $machineData['machine_model_id'] ?? null,
                        'seller_id' => $machineData['seller_id'] ?? null,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'machine_total' => $machineTotal,
                        'description' => $machineData['description'] ?? null,
                        'feeder_id' => $machineData['feeder_id'] ?? null,
                        'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                        'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                        'color_id' => $machineData['color_id'] ?? null,
                        'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                        'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                        'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                        'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                        'machine_software_id' => $machineData['machine_software_id'] ?? null,
                        'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                        'wir_id' => $machineData['wir_id'] ?? null,
                        'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                        'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                        'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                        'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                        'machine_size_id' => $machineData['machine_size_id'] ?? null,
                    ];
                    $machineDetails[] = $machineDetail;

                    ContractMachine::create([
                        'contract_id' => $contract->id,
                        'machine_category_id' => $machineData['machine_category_id'],
                        'brand_id' => $machineData['brand_id'] ?? null,
                        'machine_model_id' => $machineData['machine_model_id'] ?? null,
                        'machine_size_id' => $machineData['machine_size_id'] ?? null,
                        'seller_id' => $machineData['seller_id'] ?? null,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'description' => $machineData['description'] ?? null,
                        'feeder_id' => $machineData['feeder_id'] ?? null,
                        'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                        'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                        'color_id' => $machineData['color_id'] ?? null,
                        'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                        'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                        'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                        'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                        'machine_software_id' => $machineData['machine_software_id'] ?? null,
                        'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                        'wir_id' => $machineData['wir_id'] ?? null,
                        'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                        'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                        'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                        'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                    ]);
                }

                $contract->update([
                    'total_amount' => $totalAmount,
                    'token_amount' => $request->token_amount ?? null,
                    'machine_details' => $machineDetails,
                ]);

                return redirect()->route('contracts.signature', $contract)
                    ->with('success', 'Contract created successfully. Please sign the contract.');
            });
        } catch (QueryException $e) {
            Log::error('Contract create failed (database)', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), 'contracts_contract_number_unique') || str_contains($e->getMessage(), 'contract_number')) {
                return back()
                    ->withErrors(['contract_number' => 'Contract number already exists. Please refresh the page and try again.'])
                    ->withInput();
            }

            return back()
                ->withErrors(['error' => 'Could not save the contract. Please check your data and try again.'])
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Contract create failed', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return back()
                ->withErrors(['error' => 'Could not save the contract. If this keeps happening, try a different browser or contact support.'])
                ->withInput();
        }
    }

    /**
     * Display the specified contract with all details.
     */
    public function show(Contract $contract)
    {
        if ($this->canViewAllApprovals()) {
            $contract->load(['creator', 'approver', 'businessFirm', 'state', 'city', 'area', 'contractMachines']);
            
            // Load related data for machine details
            foreach ($contract->contractMachines as $machine) {
                $machine->load([
                    'machineCategory',
                    'brand',
                    'machineModel',
                    'seller',
                    'feeder.feederBrand',
                    'machineHook',
                    'machineERead',
                    'color',
                    'machineNozzle',
                    'machineDropin',
                    'machineBeam',
                    'machineClothRoller',
                    'machineSoftware',
                    'hsnCode',
                    'wir',
                    'machineShaft',
                    'machineLever',
                    'machineChain',
                    'machineHealdWire',
                    'deliveryTerm',
                    'machineSize',
                ]);
            }
            
            return view('contracts.show', compact('contract'));
        }

        $this->authorizeContractAccess($contract, 'view');
        
        $contract->load(['creator', 'approver', 'businessFirm', 'state', 'city', 'area', 'contractMachines']);
        
        // Load related data for machine details
        foreach ($contract->contractMachines as $machine) {
            $machine->load([
                'machineCategory',
                'brand',
                'machineModel',
                'seller',
                'feeder.feederBrand',
                'machineHook',
                'machineERead',
                'color',
                'machineNozzle',
                'machineDropin',
                'machineBeam',
                'machineClothRoller',
                'machineSoftware',
                'hsnCode',
                'wir',
                'machineShaft',
                'machineLever',
                'machineChain',
                'machineHealdWire',
                'deliveryTerm',
                'machineSize',
            ]);
        }
        
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing other contract details.
     */
    public function edit(Contract $contract)
    {
        $this->authorizeContractAccess($contract, 'edit');
        
        $contract->load(['lead', 'creator', 'businessFirm', 'state', 'city', 'area', 'contractMachines', 'approver']);
        $categories = MachineCategory::orderBy('name', 'desc')->get();
        $brands = Brand::orderBy('name')->get();
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $contract->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $contract->city_id)->orderBy('name')->get();
        $deliveryTerms = DeliveryTerm::orderBy('name')->get();
        
        // Prepare machine data for JavaScript
        $machinesData = $contract->contractMachines->map(function($machine) {
            return [
                'machine_category_id' => (string)$machine->machine_category_id,
                'brand_id' => $machine->brand_id ? (string)$machine->brand_id : '',
                'machine_model_id' => $machine->machine_model_id ? (string)$machine->machine_model_id : '',
                'machine_size_id' => $machine->machine_size_id ? (string)$machine->machine_size_id : '',
                'seller_id' => $machine->seller_id ? (string)$machine->seller_id : '',
                'quantity' => $machine->quantity,
                'amount' => $machine->amount,
                'description' => $machine->description ?? '',
                'feeder_id' => $machine->feeder_id ? (string)$machine->feeder_id : '',
                'machine_hook_id' => $machine->machine_hook_id ? (string)$machine->machine_hook_id : '',
                'machine_e_read_id' => $machine->machine_e_read_id ? (string)$machine->machine_e_read_id : '',
                'color_id' => $machine->color_id ? (string)$machine->color_id : '',
                'machine_nozzle_id' => $machine->machine_nozzle_id ? (string)$machine->machine_nozzle_id : '',
                'machine_dropin_id' => $machine->machine_dropin_id != null ? (string)$machine->machine_dropin_id : '',
                'machine_beam_id' => $machine->machine_beam_id ? (string)$machine->machine_beam_id : '',
                'machine_cloth_roller_id' => $machine->machine_cloth_roller_id ? (string)$machine->machine_cloth_roller_id : '',
                'machine_software_id' => $machine->machine_software_id ? (string)$machine->machine_software_id : '',
                'hsn_code_id' => $machine->hsn_code_id ? (string)$machine->hsn_code_id : '',
                'wir_id' => $machine->wir_id ? (string)$machine->wir_id : '',
                'machine_shaft_id' => $machine->machine_shaft_id ? (string)$machine->machine_shaft_id : '',
                'machine_lever_id' => $machine->machine_lever_id ? (string)$machine->machine_lever_id : '',
                'machine_chain_id' => $machine->machine_chain_id ? (string)$machine->machine_chain_id : '',
                'machine_heald_wire_id' => $machine->machine_heald_wire_id ? (string)$machine->machine_heald_wire_id : '',
                'delivery_term_id' => $machine->delivery_term_id ? (string)$machine->delivery_term_id : '',
                'categoryItems' => null,
                'machineModels' => []
            ];
        })->toArray();
        
        $settings = Setting::firstOrCreate(['id' => 1]);

        return view('contracts.edit', compact('contract', 'categories', 'brands', 'machinesData', 'businessFirms', 'states', 'cities', 'areas', 'deliveryTerms', 'settings'));
    }

    /**
     * Update the other contract details.
     */
    public function update(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract, 'update');
        $request->validate([
            // Personal Information
            'business_firm_id' => 'required|exists:business_firms,id',
            'buyer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
            'gst' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'token_amount' => 'nullable|numeric|min:0',
            // Machine Details
            'machines' => 'nullable|array|min:1',
            'machines.*.machine_category_id' => 'required_with:machines|exists:machine_categories,id',
            'machines.*.brand_id' => 'nullable|exists:brands,id',
            'machines.*.machine_model_id' => 'nullable|exists:machine_models,id',
            'machines.*.seller_id' => 'nullable|exists:sellers,id',
            'machines.*.quantity' => 'required_with:machines|integer|min:1',
            'machines.*.amount' => 'required_with:machines|numeric|min:0',
            'machines.*.description' => 'nullable|string',
            'machines.*.feeder_id' => 'nullable|exists:feeders,id',
            'machines.*.machine_hook_id' => 'nullable|exists:machine_hooks,id',
            'machines.*.machine_e_read_id' => 'nullable|exists:machine_e_reads,id',
            'machines.*.color_id' => 'nullable|exists:colors,id',
            'machines.*.machine_nozzle_id' => 'nullable|exists:machine_nozzles,id',
            'machines.*.machine_dropin_id' => 'nullable|exists:machine_dropins,id',
            'machines.*.machine_beam_id' => 'nullable|exists:machine_beams,id',
            'machines.*.machine_cloth_roller_id' => 'nullable|exists:machine_cloth_rollers,id',
            'machines.*.machine_software_id' => 'nullable|exists:machine_softwares,id',
            'machines.*.hsn_code_id' => 'nullable|exists:hsn_codes,id',
            'machines.*.wir_id' => 'nullable|exists:wirs,id',
            'machines.*.machine_shaft_id' => 'nullable|exists:machine_shafts,id',
            'machines.*.machine_lever_id' => 'nullable|exists:machine_levers,id',
            'machines.*.machine_chain_id' => 'nullable|exists:machine_chains,id',
            'machines.*.machine_heald_wire_id' => 'nullable|exists:machine_heald_wires,id',
            'machines.*.delivery_term_id' => 'nullable|exists:delivery_terms,id',
            'machines.*.machine_size_id' => 'nullable|exists:machine_sizes,id',
            // Other Buyer Expenses Details
            'overseas_freight' => 'nullable|string|max:255',
            'demurrage_detention_cfs_charges' => 'nullable|string|max:255',
            'air_pipe_connection' => 'nullable|string|max:255',
            'custom_duty' => 'nullable|string|max:255',
            'port_expenses_transport' => 'nullable|string|max:255',
            'crane_foundation' => 'nullable|string|max:255',
            'humidification' => 'nullable|string|max:255',
            'damage' => 'nullable|string|max:255',
            'gst_custom_charges' => 'nullable|string|max:255',
            'compressor' => 'nullable|string|max:255',
            'optional_spares' => 'nullable|string|max:255',
            'other_buyer_expenses_in_print' => 'nullable|boolean',
            // Other Details
            'payment_terms' => 'nullable|string|max:255',
            'quote_validity' => 'nullable|string|max:255',
            'loading_terms' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'complimentary_spares' => 'nullable|string|max:255',
            'other_details_in_print' => 'nullable|boolean',
            // Difference of Specification
            'spec_color_8_to_12_selectors' => 'nullable|string|max:255',
            'spec_extra_feeder_per_pc' => 'nullable|string|max:255',
            'spec_extra_warp_beam_per_pc' => 'nullable|string|max:255',
            'spec_reed_reduction_per_20cm' => 'nullable|string|max:255',
            'spec_reed_increase_per_20cm' => 'nullable|string|max:255',
            'spec_increase_380_to_480cm' => 'nullable|string|max:255',
            'spec_electronic_weft_cutter' => 'nullable|string|max:255',
            'spec_hooks_5376_to_6144' => 'nullable|string|max:255',
            'spec_hooks_5376_to_10240' => 'nullable|string|max:255',
            'spec_hooks_5376_to_2688' => 'nullable|string|max:255',
            'spec_changshu_to_sns_cam' => 'nullable|string|max:255',
            'spec_changshu_to_sns_chain_24' => 'nullable|string|max:255',
            'spec_changshu_to_sns_chain_16' => 'nullable|string|max:255',
            'spec_changshu_to_jkd_or_changfang' => 'nullable|string|max:255',
            'spec_changshu_to_wumu' => 'nullable|string|max:255',
            'spec_bintian_8_shaft_cam_heald' => 'nullable|string|max:255',
            'spec_bintian_10_shaft_cam_heald' => 'nullable|string|max:255',
            'spec_changshu_16_shaft_2861_dobby' => 'nullable|string|max:255',
            'spec_staubli_16_shaft_dobby' => 'nullable|string|max:255',
            'spec_increase_1_nozzle' => 'nullable|string|max:255',
            'spec_increase_1_feeder' => 'nullable|string|max:255',
            'spec_reed_increase_per_10cm' => 'nullable|string|max:255',
            'spec_bintian_cam_plate_extra' => 'nullable|string|max:255',
            'spec_bintian_gear_extra' => 'nullable|string|max:255',
            'difference_specification_in_print' => 'nullable|boolean',
            'difference_specification_extended_in_print' => 'nullable|boolean',
            'spec_3_niupai_10_shaft_410_cam_heald_frames' => 'nullable|string|max:255',
            'spec_3_niupai_12_shaft_411_cam_heald_frames' => 'nullable|string|max:255',
            'spec_3_niupai_16_electronic_dobby_5400d_invertor' => 'nullable|string|max:255',
            'spec_3_sanhe_s650_controller_accessories' => 'nullable|string|max:255',
            'spec_3_increase_1_colour' => 'nullable|string|max:255',
            'spec_3_increase_1_feeder' => 'nullable|string|max:255',
            'spec_3_reed_increase_per_20cm' => 'nullable|string|max:255',
            'spec_3_single_pump_to_double_pump' => 'nullable|string|max:255',
            'difference_specification_3_in_print' => 'nullable|boolean',
            'terms_government_policies' => 'nullable|string|max:65535',
            'terms_currency' => 'nullable|string|max:65535',
            'terms_licenses_bank_payment' => 'nullable|string|max:65535',
            'terms_demurrage_detentions' => 'nullable|string|max:65535',
            'terms_cancellation_order' => 'nullable|string|max:65535',
            'terms_jurisdiction_seller_rights' => 'nullable|string|max:65535',
            'terms_conditions_in_print' => 'nullable|boolean',
        ]);

        try {
            return DB::transaction(function () use ($request, $contract) {
        // Update machine details if provided
        if ($request->has('machines') && is_array($request->machines)) {
            // Delete existing contract machines
            $contract->contractMachines()->delete();
            
            // Calculate total amount and prepare machine details for storage
            $totalAmount = 0;
            $machineDetails = [];
            
            foreach ($request->machines as $machineData) {
                $quantity = (int)($machineData['quantity'] ?? 1);
                $amount = (float)($machineData['amount'] ?? 0);
                $machineTotal = $quantity * $amount;
                $totalAmount += $machineTotal;
                
                // Prepare machine detail for JSON storage
                $machineDetail = [
                    'machine_category_id' => $machineData['machine_category_id'],
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'seller_id' => $machineData['seller_id'] ?? null,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'machine_total' => $machineTotal,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $machineData['feeder_id'] ?? null,
                    'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                    'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                    'color_id' => $machineData['color_id'] ?? null,
                    'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                    'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                    'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                    'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                    'machine_software_id' => $machineData['machine_software_id'] ?? null,
                    'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                    'wir_id' => $machineData['wir_id'] ?? null,
                    'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                    'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                    'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                    'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                    'delivery_term_id' => $machineData['delivery_term_id'] ?? null,
                    'machine_size_id' => $machineData['machine_size_id'] ?? null,
                ];
                
                $machineDetails[] = $machineDetail;
                
                // Store in contract_machines table
                ContractMachine::create([
                    'contract_id' => $contract->id,
                    'machine_category_id' => $machineData['machine_category_id'],
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'machine_size_id' => $machineData['machine_size_id'] ?? null,
                    'seller_id' => $machineData['seller_id'] ?? null,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $machineData['feeder_id'] ?? null,
                    'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                    'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                    'color_id' => $machineData['color_id'] ?? null,
                    'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                    'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                    'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                    'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                    'machine_software_id' => $machineData['machine_software_id'] ?? null,
                    'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                    'wir_id' => $machineData['wir_id'] ?? null,
                    'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                    'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                    'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                    'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                    'delivery_term_id' => $machineData['delivery_term_id'] ?? null,
                ]);
            }
            
            // Update contract with total amount and machine details
            $contract->update([
                'total_amount' => $totalAmount,
                'machine_details' => $machineDetails
            ]);
        }

        $contract->update(array_merge([
            // Personal Information
            'business_firm_id' => $request->business_firm_id,
            'buyer_name' => $request->buyer_name,
            'company_name' => $request->company_name,
            'contact_address' => $request->contact_address,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'phone_number_2' => $request->phone_number_2,
            'gst' => $request->gst,
            'pan' => $request->pan,
            'token_amount' => $request->token_amount ?? null,
        ], Contract::otherBuyerExpensesForUpdate($request, $contract), [
            // Other Details
            'payment_terms' => $request->payment_terms,
            'quote_validity' => $request->quote_validity,
            'loading_terms' => $request->loading_terms,
            'warranty' => $request->warranty,
            'complimentary_spares' => $request->complimentary_spares,
            'other_details_in_print' => $request->has('other_details_in_print') ? (bool)$request->other_details_in_print : $contract->other_details_in_print,
            // Difference of Specification
            'spec_color_8_to_12_selectors' => $request->spec_color_8_to_12_selectors,
            'spec_extra_feeder_per_pc' => $request->spec_extra_feeder_per_pc,
            'spec_extra_warp_beam_per_pc' => $request->spec_extra_warp_beam_per_pc,
            'spec_reed_reduction_per_20cm' => $request->spec_reed_reduction_per_20cm,
            'spec_reed_increase_per_20cm' => $request->spec_reed_increase_per_20cm,
            'spec_increase_380_to_480cm' => $request->spec_increase_380_to_480cm,
            'spec_electronic_weft_cutter' => $request->spec_electronic_weft_cutter,
            'spec_hooks_5376_to_6144' => $request->spec_hooks_5376_to_6144,
            'spec_hooks_5376_to_10240' => $request->spec_hooks_5376_to_10240,
            'spec_hooks_5376_to_2688' => $request->spec_hooks_5376_to_2688,
            'spec_changshu_to_sns_cam' => $request->spec_changshu_to_sns_cam,
            'spec_changshu_to_sns_chain_24' => $request->spec_changshu_to_sns_chain_24,
            'spec_changshu_to_sns_chain_16' => $request->spec_changshu_to_sns_chain_16,
            'spec_changshu_to_jkd_or_changfang' => $request->spec_changshu_to_jkd_or_changfang,
            'spec_changshu_to_wumu' => $request->spec_changshu_to_wumu,
            'spec_bintian_8_shaft_cam_heald' => $request->spec_bintian_8_shaft_cam_heald,
            'spec_bintian_10_shaft_cam_heald' => $request->spec_bintian_10_shaft_cam_heald,
            'spec_changshu_16_shaft_2861_dobby' => $request->spec_changshu_16_shaft_2861_dobby,
            'spec_staubli_16_shaft_dobby' => $request->spec_staubli_16_shaft_dobby,
            'spec_increase_1_nozzle' => $request->spec_increase_1_nozzle,
            'spec_increase_1_feeder' => $request->spec_increase_1_feeder,
            'spec_reed_increase_per_10cm' => $request->spec_reed_increase_per_10cm,
            'spec_bintian_cam_plate_extra' => $request->spec_bintian_cam_plate_extra,
            'spec_bintian_gear_extra' => $request->spec_bintian_gear_extra,
            'difference_specification_in_print' => $request->has('difference_specification_in_print') ? (bool)$request->difference_specification_in_print : $contract->difference_specification_in_print,
            'difference_specification_extended_in_print' => $request->boolean('difference_specification_extended_in_print', (bool) $contract->difference_specification_extended_in_print),
            'spec_3_niupai_10_shaft_410_cam_heald_frames' => $request->spec_3_niupai_10_shaft_410_cam_heald_frames,
            'spec_3_niupai_12_shaft_411_cam_heald_frames' => $request->spec_3_niupai_12_shaft_411_cam_heald_frames,
            'spec_3_niupai_16_electronic_dobby_5400d_invertor' => $request->spec_3_niupai_16_electronic_dobby_5400d_invertor,
            'spec_3_sanhe_s650_controller_accessories' => $request->spec_3_sanhe_s650_controller_accessories,
            'spec_3_increase_1_colour' => $request->spec_3_increase_1_colour,
            'spec_3_increase_1_feeder' => $request->spec_3_increase_1_feeder,
            'spec_3_reed_increase_per_20cm' => $request->spec_3_reed_increase_per_20cm,
            'spec_3_single_pump_to_double_pump' => $request->spec_3_single_pump_to_double_pump,
            'difference_specification_3_in_print' => $request->boolean('difference_specification_3_in_print', (bool) $contract->difference_specification_3_in_print),
            'terms_government_policies' => $request->terms_government_policies,
            'terms_currency' => $request->terms_currency,
            'terms_licenses_bank_payment' => $request->terms_licenses_bank_payment,
            'terms_demurrage_detentions' => $request->terms_demurrage_detentions,
            'terms_cancellation_order' => $request->terms_cancellation_order,
            'terms_jurisdiction_seller_rights' => $request->terms_jurisdiction_seller_rights,
            'terms_conditions_in_print' => $request->boolean('terms_conditions_in_print', (bool) $contract->terms_conditions_in_print),
            'not_included_in_offer_in_print' => $request->has('not_included_in_offer_in_print') ? (bool) $request->not_included_in_offer_in_print : (bool) ($contract->not_included_in_offer_in_print ?? true),
            'not_included_in_offer' => Contract::notIncludedInOfferPayloadFromRequest($request, 'not_included_in_offer'),
            // Reset approval status - contract needs to be re-approved after update
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ]));

        return redirect()->route('contracts.index')
            ->with('success', 'Contract details updated successfully. Contract has been sent for approval again.');
            });
        } catch (QueryException $e) {
            Log::error('Contract update failed (database)', [
                'contract_id' => $contract->id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Could not update the contract. Please check your data and try again.'])
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Contract update failed', [
                'contract_id' => $contract->id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return back()
                ->withErrors(['error' => 'Could not update the contract. If this keeps happening, try another browser or contact support.'])
                ->withInput();
        }
    }

    /**
     * Show the signature page for a contract.
     */
    public function signature(Contract $contract)
    {
        $this->authorizeContractAccess($contract, 'access');
        
        $contract->load(['lead', 'businessFirm', 'state', 'city', 'area']);
        return view('contracts.signature', compact('contract'));
    }

    /**
     * Store the customer signature.
     */
    public function storeSignature(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract, 'sign');
        
        $request->validate([
            'signature' => 'required|string',
        ]);

        $contract->update([
            'customer_signature' => $request->signature,
            'approval_status' => 'pending',
        ]);

        // Notify all users who can approve contracts (e.g. Admin)
        $admins = User::permission('approve contracts')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ContractPendingApprovalNotification($contract));
        }

        return redirect()->route('contracts.index')
            ->with('success', 'Contract signed successfully. Waiting for approval.');
    }

    /**
     * Show contracts pending approval.
     */
    public function pendingApproval(Request $request)
    {
        $query = Contract::with(['lead', 'businessFirm', 'state', 'city', 'area', 'creator', 'approver']);
        
        // Users with contract-approval visibility can see all.
        // Other users can see only contracts they created.
        if (!$this->canViewAllApprovals()) {
            $query->where('created_by', auth()->id());
        }
        
        // Filter by approval status if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('approval_status', 'pending')
                      ->whereNotNull('customer_signature');
            } else {
                $query->where('approval_status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('contract_number', 'like', "%{$term}%")
                    ->orWhere('buyer_name', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%");
            });
        }

        $sort = $request->get('sort', 'date_desc');
        match ($sort) {
            'name_asc' => $query->orderBy('contract_number', 'asc'),
            'name_desc' => $query->orderBy('contract_number', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $contracts = $query->paginate(10)->withQueryString();
        
        return view('contracts.pending-approval', compact('contracts'));
    }

    /**
     * Approve a contract.
     */
    public function approve(Request $request, Contract $contract)
    {
        $this->authorize('approve contracts');

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $contract->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->route('contracts.pending-approval')
            ->with('success', 'Contract approved successfully.');
    }

    /**
     * Reject a contract.
     */
    public function reject(Request $request, Contract $contract)
    {
        $this->authorize('reject contracts');

        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        $contract->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->route('contracts.pending-approval')
            ->with('success', 'Contract rejected.');
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Download contract as PDF.
     */
    public function downloadPdf(Contract $contract)
    {
        $this->authorizeContractAccess($contract, 'download');

        // Reload flags and text from DB so PDF matches latest Show/Hide and saved content
        $contract->refresh();

        $contract->load(['creator', 'approver', 'businessFirm', 'state', 'city', 'area', 'contractMachines']);
        
        // Load related data for machine details
        foreach ($contract->contractMachines as $machine) {
            $machine->load([
                'machineCategory',
                'brand',
                'machineModel',
                'seller',
                'feeder.feederBrand',
                'machineHook',
                'machineERead',
                'color',
                'machineNozzle',
                'machineDropin',
                'machineBeam',
                'machineClothRoller',
                'machineSoftware',
                'hsnCode',
                'wir',
                'machineShaft',
                'machineLever',
                'machineChain',
                'machineHealdWire',
                'deliveryTerm',
                'machineSize',
            ]);
        }
        
        $pdf = DomPDF::loadView('contracts.pdf', compact('contract'));
        
        // Set options to enable font subsetting and Unicode support
        $pdf->setOption('enable-font-subsetting', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf->download('contract-' . $contract->contract_number . '.pdf');
    }

    /**
     * Display over invoice list - contracts where total PI amount exceeds contract amount
     */
    public function overInvoice(Request $request)
    {
        // Get contracts with their total proforma invoice amounts
        $query = Contract::with(['creator', 'businessFirm'])
            ->select('contracts.*')
            ->selectRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM proforma_invoices WHERE proforma_invoices.contract_id = contracts.id) as total_pi_amount')
            ->whereRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM proforma_invoices WHERE proforma_invoices.contract_id = contracts.id) > contracts.total_amount');

        // If user is Admin or Super Admin, they can see all contracts
        // If user has 'view contract approvals' or 'view over invoice' permission, they can see all contracts
        // Otherwise, filter by their contracts
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals') && !auth()->user()->can('view over invoice')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function($q) use ($teamMemberIds) {
                $q->where('contracts.created_by', auth()->id())
                  ->orWhereIn('contracts.created_by', $teamMemberIds);
            });
        }

        // Filter by Sales Manager
        // If user selected is Admin/Super Admin => show all (no created_by filter)
        if ($request->filled('sales_manager')) {
            $selectedSalesManagerId = $request->sales_manager;
            $isAdminOrSuper = User::where('id', $selectedSalesManagerId)
                ->whereHas('roles', function ($r) {
                    $r->whereIn('name', ['Admin', 'Super Admin']);
                })
                ->exists();

            if (!$isAdminOrSuper) {
                $query->where('contracts.created_by', $selectedSalesManagerId);
            }
        }

        // Filter by Contract Number
        if ($request->filled('contract_number')) {
            $query->where('contracts.contract_number', 'like', "%{$request->contract_number}%");
        }

        // Filter by Customer Name (buyer_name or company_name)
        if ($request->filled('customer_name')) {
            $query->where(function($q) use ($request) {
                $q->where('contracts.buyer_name', 'like', "%{$request->customer_name}%")
                  ->orWhere('contracts.company_name', 'like', "%{$request->customer_name}%");
            });
        }

        // Sales Manager dropdown: include Sales Manager + Admin + Super Admin
        $salesManagers = User::whereHas('roles', function ($r) {
            $r->whereIn('name', ['Sales Manager', 'Admin', 'Super Admin']);
        })->select('id', 'name')->orderBy('name')->get();

        $overInvoices = $query->orderBy('contracts.created_at', 'desc')->paginate(15)->withQueryString();

        // Calculate difference for each contract
        $overInvoices->getCollection()->transform(function ($contract) {
            $contract->difference_amount = ($contract->total_pi_amount ?? 0) - ($contract->total_amount ?? 0);
            return $contract;
        });

        return view('contracts.over-invoice', compact('overInvoices', 'salesManagers'));
    }

    /**
     * Get over-invoice contracts by sales manager for dropdown (AJAX)
     */
    public function getOverInvoiceContractsBySalesManager(Request $request)
    {
        if (!$request->filled('sales_manager_id')) {
            return response()->json([]);
        }

        $selectedSalesManagerId = $request->sales_manager_id;
        $isAdminOrSuper = User::where('id', $selectedSalesManagerId)
            ->whereHas('roles', function ($r) {
                $r->whereIn('name', ['Admin', 'Super Admin']);
            })
            ->exists();

        $query = Contract::with([
                'creator',
                'proformaInvoices:id,contract_id,proforma_invoice_number',
            ])
            ->select('contracts.id', 'contracts.contract_number', 'contracts.buyer_name', 'contracts.company_name', 'contracts.total_amount', 'contracts.created_by')
            ->selectRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM proforma_invoices WHERE proforma_invoices.contract_id = contracts.id) as total_pi_amount')
            ->whereRaw('(SELECT COALESCE(SUM(total_amount), 0) FROM proforma_invoices WHERE proforma_invoices.contract_id = contracts.id) > contracts.total_amount')
            ->orderBy('contracts.contract_number');

        if (!$isAdminOrSuper) {
            $query->where('contracts.created_by', $selectedSalesManagerId);
        }

        $contracts = $query->get()->map(function ($contract) {
            $piNumbers = $contract->proformaInvoices
                ->pluck('proforma_invoice_number')
                ->filter()
                ->values()
                ->all();

            return [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'buyer_name' => $contract->buyer_name,
                'company_name' => $contract->company_name,
                'total_amount' => $contract->total_amount,
                'total_pi_amount' => $contract->total_pi_amount,
                'pi_numbers' => $piNumbers,
                'creator' => $contract->creator ? ['name' => $contract->creator->name] : null,
            ];
        });

        return response()->json($contracts);
    }

    /**
     * View one over-invoice contract: amounts + enter USD→INR rate to see difference in ₹
     */
    public function showOverInvoice(Contract $contract)
    {
        $this->authorizeOverInvoiceContractAccess($contract);
        $totals = $this->getOverInvoiceTotals($contract);
        if (!$totals['is_over_invoice']) {
            abort(404);
        }

        return view('contracts.over-invoice-show', [
            'contract' => $contract->loadMissing(['creator', 'businessFirm']),
            'total_pi_amount' => $totals['total_pi_amount'],
            'difference_amount' => $totals['difference_amount'],
            'pageTitle' => 'View Over Invoice',
            'isEdit' => false,
        ]);
    }

    /**
     * Edit over-invoice detail (same screen as view; rate for ₹ conversion)
     */
    public function editOverInvoice(Contract $contract)
    {
        $this->authorizeOverInvoiceContractAccess($contract);
        $totals = $this->getOverInvoiceTotals($contract);
        if (!$totals['is_over_invoice']) {
            abort(404);
        }

        return view('contracts.over-invoice-show', [
            'contract' => $contract->loadMissing(['creator', 'businessFirm']),
            'total_pi_amount' => $totals['total_pi_amount'],
            'difference_amount' => $totals['difference_amount'],
            'pageTitle' => 'Edit Over Invoice',
            'isEdit' => true,
        ]);
    }

    /**
     * Persist USD→INR rate and computed over-invoice difference in ₹ (shown on list).
     */
    public function saveOverInvoiceInr(Request $request, Contract $contract)
    {
        $this->authorizeOverInvoiceContractAccess($contract);
        $totals = $this->getOverInvoiceTotals($contract);
        if (!$totals['is_over_invoice']) {
            abort(404);
        }

        $validated = $request->validate([
            'usd_inr_rate' => 'required|numeric|min:0',
        ]);

        $rate = (float) $validated['usd_inr_rate'];
        $differenceUsd = (float) $totals['difference_amount'];
        $differenceInr = round($differenceUsd * $rate, 2);

        $contract->update([
            'over_invoice_usd_inr_rate' => $rate,
            'over_invoice_difference_inr' => $differenceInr,
        ]);

        return redirect()
            ->route('contracts.over-invoice')
            ->with('success', 'INR difference saved for contract ' . $contract->contract_number . '.');
    }

    private function authorizeOverInvoiceContractAccess(Contract $contract): void
    {
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return;
        }
        if (auth()->user()->can('view contract approvals') || auth()->user()->can('view over invoice')) {
            return;
        }
        $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
        if ($contract->created_by === auth()->id() || in_array($contract->created_by, $teamMemberIds, true)) {
            return;
        }
        abort(403, 'You cannot view this over-invoice record.');
    }

    private function canViewAllContracts(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    private function canViewAllApprovals(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    private function authorizeContractAccess(Contract $contract, string $action): void
    {
        if ($this->canViewAllContracts()) {
            return;
        }

        if ($contract->created_by === auth()->id()) {
            return;
        }

        abort(403, "You can only {$action} contracts you created.");
    }

    /**
     * @return array{total_pi_amount: float, difference_amount: float, is_over_invoice: bool}
     */
    private function getOverInvoiceTotals(Contract $contract): array
    {
        $totalPi = (float) ProformaInvoice::where('contract_id', $contract->id)->sum('total_amount');
        $contractTotal = (float) ($contract->total_amount ?? 0);
        $isOver = $totalPi > $contractTotal;

        return [
            'total_pi_amount' => $totalPi,
            'difference_amount' => $totalPi - $contractTotal,
            'is_over_invoice' => $isOver,
        ];
    }
}
