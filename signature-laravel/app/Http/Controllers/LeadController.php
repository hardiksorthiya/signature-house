<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Business;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\Status;
use App\Models\Brand;
use App\Models\MachineCategory;
use App\Models\BusinessFirm;
use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\MachineModel;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\LeadConnectToManagerNotification;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories', 'contract', 'creator'])
            ->whereDoesntHave('contract', function($q) {
                $q->whereNotNull('approval_status');
            });

        // Filter leads based on user role and team
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Show leads created by current user OR their team members
            $query->where(function($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())
                  ->orWhereIn('created_by', $teamMemberIds);
            });
        }
        // Admin/Super Admin see all leads (no additional filter)

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereHas('business', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('brand', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('area', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by planning timeline
        if ($request->filled('planning_month')) {
            $query->where('planning_month', $request->planning_month);
        }

        if ($request->filled('planning_year')) {
            $query->where('planning_year', $request->planning_year);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('leads.index', compact('leads', 'businesses', 'states', 'statuses', 'brands'));
    }

    public function show(Lead $lead)
    {
        // Check if user can view this lead
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Check if lead was created by current user or their team member
            if ($lead->created_by !== auth()->id() && !in_array($lead->created_by, $teamMemberIds)) {
                abort(403, 'You can only view leads you created or leads created by your team members.');
            }
        }
        
        $lead->load(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories']);
        return view('leads.show', compact('lead'));
    }

    public function create()
    {
        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name', 'desc')->get();
        return view('leads.create', compact('businesses', 'states', 'statuses', 'brands', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:new,old',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:leads,phone_number',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'quantity' => 'required|integer|min:1',
            'planning_month' => 'required|integer|between:1,12',
            'planning_year' => 'required|integer|digits:4|min:2000|max:2100',
            'status_id' => 'required|exists:statuses,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'needs_scheduling' => 'nullable|boolean',
            'scheduled_date' => 'nullable|date|required_if:needs_scheduling,1',
            'scheduled_time' => 'nullable|date_format:H:i|required_if:needs_scheduling,1',
        ];

        if ($request->type === 'new') {
            $rules['business_id'] = 'required|exists:businesses,id';
        } else {
            $rules['brand_name'] = 'required|string|max:255';
            $rules['machine_quantity'] = 'required|integer|min:1';
            $rules['running_since'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $leadData = [
            'type' => $request->type,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'quantity' => $request->quantity,
            'planning_month' => $request->planning_month,
            'planning_year' => $request->planning_year,
            'status_id' => $request->status_id,
            'needs_scheduling' => $request->has('needs_scheduling') ? (bool)$request->needs_scheduling : false,
            'scheduled_date' => $request->needs_scheduling ? $request->scheduled_date : null,
            'scheduled_time' => $request->needs_scheduling ? $request->scheduled_time : null,
        ];

        if ($request->type === 'new') {
            $leadData['business_id'] = $request->business_id;
        } else {
            $leadData['brand_name'] = $request->brand_name;
            $leadData['machine_quantity'] = $request->machine_quantity;
            $leadData['running_since'] = $request->running_since;
        }

        // Add the current user as creator
        $leadData['created_by'] = auth()->id();

        $lead = Lead::create($leadData);

        // Attach categories
        $lead->machineCategories()->attach($request->categories);

        // Handle task creation for scheduled meetings
        $status = Status::find($request->status_id);
        if ($status && $status->requires_scheduling && $request->needs_scheduling && $request->scheduled_date && $request->scheduled_time) {
            // Combine date and time
            $scheduledDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->scheduled_date . ' ' . $request->scheduled_time);
            
            // Create task
            \App\Models\Task::create([
                'user_id' => auth()->id(),
                'lead_id' => $lead->id,
                'title' => 'Meeting with ' . $lead->name,
                'description' => 'Scheduled meeting for lead: ' . $lead->name . ' (Phone: ' . $lead->phone_number . ')',
                'status' => 'pending',
                'due_date' => $scheduledDateTime->format('Y-m-d'),
                'priority' => 2, // High priority for meetings
            ]);
        }

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        // Check if user can edit this lead
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Check if lead was created by current user or their team member
            if ($lead->created_by !== auth()->id() && !in_array($lead->created_by, $teamMemberIds)) {
                abort(403, 'You can only edit leads you created or leads created by your team members.');
            }
        }
        
        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $lead->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $lead->city_id)->orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name', 'desc')->get();
        return view('leads.edit', compact('lead', 'businesses', 'states', 'cities', 'areas', 'statuses', 'brands', 'categories'));
    }

    public function update(Request $request, Lead $lead)
    {
        // Check if user can update this lead
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Check if lead was created by current user or their team member
            if ($lead->created_by !== auth()->id() && !in_array($lead->created_by, $teamMemberIds)) {
                abort(403, 'You can only update leads you created or leads created by your team members.');
            }
        }
        
        $rules = [
            'type' => 'required|in:new,old',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:leads,phone_number,' . $lead->id,
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'quantity' => 'required|integer|min:1',
            'planning_month' => 'required|integer|between:1,12',
            'planning_year' => 'required|integer|digits:4|min:2000|max:2100',
            'status_id' => 'required|exists:statuses,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'needs_scheduling' => 'nullable|boolean',
            'scheduled_date' => 'nullable|date|required_if:needs_scheduling,1',
            'scheduled_time' => 'nullable|date_format:H:i|required_if:needs_scheduling,1',
        ];

        if ($request->type === 'new') {
            $rules['business_id'] = 'required|exists:businesses,id';
        } else {
            $rules['brand_name'] = 'required|string|max:255';
            $rules['machine_quantity'] = 'required|integer|min:1';
            $rules['running_since'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $leadData = [
            'type' => $request->type,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'quantity' => $request->quantity,
            'planning_month' => $request->planning_month,
            'planning_year' => $request->planning_year,
            'status_id' => $request->status_id,
            'needs_scheduling' => $request->has('needs_scheduling') ? (bool)$request->needs_scheduling : false,
            'scheduled_date' => $request->needs_scheduling ? $request->scheduled_date : null,
            'scheduled_time' => $request->needs_scheduling ? $request->scheduled_time : null,
        ];

        if ($request->type === 'new') {
            $leadData['business_id'] = $request->business_id;
            $leadData['brand_id'] = null;
            $leadData['brand_name'] = null;
            $leadData['machine_quantity'] = null;
            $leadData['running_since'] = null;
        } else {
            $leadData['business_id'] = null;
            $leadData['brand_id'] = null;
            $leadData['brand_name'] = $request->brand_name;
            $leadData['machine_quantity'] = $request->machine_quantity;
            $leadData['running_since'] = $request->running_since;
        }

        $lead->update($leadData);

        // Sync categories
        $lead->machineCategories()->sync($request->categories);

        // Handle task creation/update for scheduled meetings
        $status = \App\Models\Status::find($request->status_id);
        if ($status && $status->requires_scheduling && $request->needs_scheduling && $request->scheduled_date && $request->scheduled_time) {
            // Combine date and time
            $scheduledDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->scheduled_date . ' ' . $request->scheduled_time);
            
            // Check if task already exists for this lead
            $existingTask = \App\Models\Task::where('lead_id', $lead->id)->first();
            
            if ($existingTask) {
                // Update existing task
                $existingTask->update([
                    'title' => 'Meeting with ' . $lead->name,
                    'description' => 'Scheduled meeting for lead: ' . $lead->name . ' (Phone: ' . $lead->phone_number . ')',
                    'status' => 'pending',
                    'due_date' => $scheduledDateTime->format('Y-m-d'),
                    'priority' => 2, // High priority for meetings
                ]);
            } else {
                // Create new task
                \App\Models\Task::create([
                    'user_id' => $lead->created_by ?? auth()->id(),
                    'lead_id' => $lead->id,
                    'title' => 'Meeting with ' . $lead->name,
                    'description' => 'Scheduled meeting for lead: ' . $lead->name . ' (Phone: ' . $lead->phone_number . ')',
                    'status' => 'pending',
                    'due_date' => $scheduledDateTime->format('Y-m-d'),
                    'priority' => 2, // High priority for meetings
                ]);
            }
        } else {
            // If scheduling is disabled or status doesn't require scheduling, mark existing task as completed if exists
            $existingTask = \App\Models\Task::where('lead_id', $lead->id)->first();
            if ($existingTask) {
                $existingTask->update(['status' => 'completed']);
            }
        }

        // When salesman sets lead status to "Connect to Manager", notify their creator (manager) for meeting
        $statusNameLower = $status ? strtolower(trim($status->name)) : '';
        $isConnectToManager = $status && (
            $statusNameLower === 'connect to manager'
            || $statusNameLower === 'connect with manager'
            || (str_contains($statusNameLower, 'connect') && str_contains($statusNameLower, 'manager'))
        );
        if ($isConnectToManager) {
            $manager = auth()->user()->creator;
            if ($manager) {
                $manager->notify(new LeadConnectToManagerNotification($lead));
            } else {
                // Fallback: salesman has no creator (e.g. created by seed) – notify all users who can approve contracts (managers/admins)
                $managers = User::permission('approve contracts')->get();
                foreach ($managers as $m) {
                    $m->notify(new LeadConnectToManagerNotification($lead));
                }
            }
        }

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        // Check if user can delete this lead
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Check if lead was created by current user or their team member
            if ($lead->created_by !== auth()->id() && !in_array($lead->created_by, $teamMemberIds)) {
                return redirect()->route('leads.index')
                    ->with('error', 'You can only delete leads you created or leads created by your team members.');
            }
        }
        
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->orderBy('name')->get();
        return response()->json($cities);
    }

    public function getAreas($city_id)
    {
        $areas = Area::where('city_id', $city_id)->orderBy('name')->get();
        return response()->json($areas);
    }

    public function convertToContract(Lead $lead)
    {
        // Check if user can convert this lead
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Get IDs of users created by current user (team members)
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            
            // Check if lead was created by current user or their team member
            if ($lead->created_by !== auth()->id() && !in_array($lead->created_by, $teamMemberIds)) {
                return redirect()->route('leads.index')
                    ->with('error', 'You can only convert leads you created or leads created by your team members.');
            }
        }
        
        // Check if this lead already has a contract
        $existingContract = Contract::where('lead_id', $lead->id)->first();
        if ($existingContract) {
            return redirect()->route('contracts.index')
                ->with('error', 'This lead already has a contract. You can view it in the contracts list.');
        }

        $lead->load(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories']);
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $lead->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $lead->city_id)->orderBy('name')->get();
        $categories = MachineCategory::orderBy('name', 'desc')->get();
        $brands = Brand::orderBy('name')->get();
        
        // Build initial machines from lead's selected categories and brand (so contract form pre-fills)
        $initialMachines = [];
        $leadBrandId = $lead->brand_id ? (string) $lead->brand_id : '';
        if ($leadBrandId === '' && $lead->brand_name) {
            $resolved = Brand::where('name', $lead->brand_name)->first();
            if ($resolved) {
                $leadBrandId = (string) $resolved->id;
            }
        }
        if ($lead->machineCategories && $lead->machineCategories->count() > 0) {
            foreach ($lead->machineCategories as $category) {
                $initialMachines[] = [
                    'machine_category_id' => (string) $category->id,
                    'brand_id' => $leadBrandId,
                    'machine_model_id' => '',
                    'machineModels' => [],
                    'quantity' => '',
                    'description' => '',
                    'categoryItems' => null,
                    'amount' => 0,
                    'feeder_id' => '',
                    'machine_hook_id' => '',
                    'machine_e_read_id' => '',
                    'color_id' => '',
                    'machine_nozzle_id' => '',
                    'machine_dropin_id' => '',
                    'machine_beam_id' => '',
                    'machine_cloth_roller_id' => '',
                    'machine_software_id' => '',
                    'hsn_code_id' => '',
                    'machine_size_id' => '',
                    'wir_id' => '',
                    'machine_shaft_id' => '',
                    'machine_lever_id' => '',
                    'machine_chain_id' => '',
                    'machine_heald_wire_id' => '',
                    'seller_id' => '',
                ];
            }
        }
        
        // Get global contract details settings
        $settings = Setting::firstOrCreate(['id' => 1]);
        
        // Generate contract number
        $lastContract = Contract::orderBy('id', 'desc')->first();
        $contractNumber = 'CNT-' . str_pad(($lastContract ? $lastContract->id : 0) + 1, 6, '0', STR_PAD_LEFT);
        
        return view('leads.convert-to-contract', compact('lead', 'businessFirms', 'states', 'cities', 'areas', 'categories', 'brands', 'contractNumber', 'settings', 'initialMachines'));
    }

    public function storeContract(Request $request, Lead $lead)
    {
        // Check if this lead already has a contract
        $existingContract = Contract::where('lead_id', $lead->id)->first();
        if ($existingContract) {
            return redirect()->route('contracts.index')
                ->with('error', 'This lead already has a contract. You cannot create multiple contracts for the same lead.');
        }

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
            'machines.*.machine_size_id' => 'nullable|exists:machine_sizes,id',
            'machines.*.wir_id' => 'nullable|exists:wirs,id',
            'machines.*.machine_shaft_id' => 'nullable|exists:machine_shafts,id',
            'machines.*.machine_lever_id' => 'nullable|exists:machine_levers,id',
            'machines.*.machine_chain_id' => 'nullable|exists:machine_chains,id',
            'machines.*.machine_heald_wire_id' => 'nullable|exists:machine_heald_wires,id',
        ]);

        $nullableId = static function ($value) {
            return ($value === '' || $value === null || $value === 'null') ? null : $value;
        };

        DB::beginTransaction();
        try {
            $contract = Contract::create(array_merge([
                'lead_id' => $lead->id,
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
                // Other Details
                'payment_terms' => $request->payment_terms,
                'quote_validity' => $request->quote_validity,
                'loading_terms' => $request->loading_terms,
                'warranty' => $request->warranty,
                'complimentary_spares' => $request->complimentary_spares,
                'other_details_in_print' => $request->has('other_details_in_print') ? (bool)$request->other_details_in_print : true,
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
                    'brand_id' => $nullableId($machineData['brand_id'] ?? null),
                    'machine_model_id' => $nullableId($machineData['machine_model_id'] ?? null),
                    'seller_id' => $nullableId($machineData['seller_id'] ?? null),
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'machine_total' => $machineTotal,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $nullableId($machineData['feeder_id'] ?? null),
                    'machine_hook_id' => $nullableId($machineData['machine_hook_id'] ?? null),
                    'machine_e_read_id' => $nullableId($machineData['machine_e_read_id'] ?? null),
                    'color_id' => $nullableId($machineData['color_id'] ?? null),
                    'machine_nozzle_id' => $nullableId($machineData['machine_nozzle_id'] ?? null),
                    'machine_dropin_id' => $nullableId($machineData['machine_dropin_id'] ?? null),
                    'machine_beam_id' => $nullableId($machineData['machine_beam_id'] ?? null),
                    'machine_cloth_roller_id' => $nullableId($machineData['machine_cloth_roller_id'] ?? null),
                    'machine_software_id' => $nullableId($machineData['machine_software_id'] ?? null),
                    'hsn_code_id' => $nullableId($machineData['hsn_code_id'] ?? null),
                    'machine_size_id' => $nullableId($machineData['machine_size_id'] ?? null),
                    'wir_id' => $nullableId($machineData['wir_id'] ?? null),
                    'machine_shaft_id' => $nullableId($machineData['machine_shaft_id'] ?? null),
                    'machine_lever_id' => $nullableId($machineData['machine_lever_id'] ?? null),
                    'machine_chain_id' => $nullableId($machineData['machine_chain_id'] ?? null),
                    'machine_heald_wire_id' => $nullableId($machineData['machine_heald_wire_id'] ?? null),
                ];
                
                $machineDetails[] = $machineDetail;
                
                // Also store in contract_machines table for relational queries
                ContractMachine::create([
                    'contract_id' => $contract->id,
                    'machine_category_id' => $machineData['machine_category_id'],
                    'brand_id' => $nullableId($machineData['brand_id'] ?? null),
                    'machine_model_id' => $nullableId($machineData['machine_model_id'] ?? null),
                    'seller_id' => $nullableId($machineData['seller_id'] ?? null),
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $nullableId($machineData['feeder_id'] ?? null),
                    'machine_hook_id' => $nullableId($machineData['machine_hook_id'] ?? null),
                    'machine_e_read_id' => $nullableId($machineData['machine_e_read_id'] ?? null),
                    'color_id' => $nullableId($machineData['color_id'] ?? null),
                    'machine_nozzle_id' => $nullableId($machineData['machine_nozzle_id'] ?? null),
                    'machine_dropin_id' => $nullableId($machineData['machine_dropin_id'] ?? null),
                    'machine_beam_id' => $nullableId($machineData['machine_beam_id'] ?? null),
                    'machine_cloth_roller_id' => $nullableId($machineData['machine_cloth_roller_id'] ?? null),
                    'machine_software_id' => $nullableId($machineData['machine_software_id'] ?? null),
                    'hsn_code_id' => $nullableId($machineData['hsn_code_id'] ?? null),
                    'wir_id' => $nullableId($machineData['wir_id'] ?? null),
                    'machine_shaft_id' => $nullableId($machineData['machine_shaft_id'] ?? null),
                    'machine_lever_id' => $nullableId($machineData['machine_lever_id'] ?? null),
                    'machine_chain_id' => $nullableId($machineData['machine_chain_id'] ?? null),
                    'machine_heald_wire_id' => $nullableId($machineData['machine_heald_wire_id'] ?? null),
                    'machine_size_id' => $nullableId($machineData['machine_size_id'] ?? null),
                ]);
            }

            // Update contract with total amount, token amount and machine details
            $contract->update([
                'total_amount' => $totalAmount,
                'token_amount' => $request->token_amount ?? null,
                'machine_details' => $machineDetails
            ]);

            DB::commit();

            // Redirect to signature page
            return redirect()->route('contracts.signature', $contract)
                ->with('success', 'Contract created successfully. Please sign the contract.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Lead contract creation failed (DB)', [
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), 'contracts_contract_number_unique') || str_contains($e->getMessage(), 'contract_number')) {
                return back()
                    ->withErrors(['contract_number' => 'Contract number already exists. Please refresh and submit again.'])
                    ->withInput();
            }

            return back()
                ->withErrors(['error' => 'Unable to create contract right now. Please check entered values and try again.'])
                ->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lead contract creation failed', [
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Unable to create contract right now. Please try again.'])
                ->withInput();
        }
    }

    public function getMachineModels(Request $request, $brand_id)
    {
        $categoryId = $request->query('category_id');

        $models = MachineModel::query()
            ->where(function ($query) use ($brand_id) {
                $query->whereHas('brands', function ($brandQuery) use ($brand_id) {
                    $brandQuery->where('brands.id', $brand_id);
                })->orWhere('brand_id', $brand_id);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('machineCategories', function ($categoryQuery) use ($categoryId) {
                    $categoryQuery->where('machine_categories.id', $categoryId);
                });
            })
            ->orderBy('model_no')
            ->get();

        return response()->json($models);
    }

    public function getCategoryItems($category_id)
    {
        $category = MachineCategory::with([
            'brands', 'feeders.feederBrand', 'machineHooks', 'machineEReads', 'colors',
            'machineNozzles', 'machineDropins', 'machineBeams', 'machineSizes',
            'machineClothRollers', 'machineSoftwares', 'hsnCodes',
            'wirs', 'machineShafts', 'machineLevers', 'machineChains',
            'machineHealdWires'
        ])->findOrFail($category_id);

        // Get sellers that have this machine category
        $sellers = Seller::whereHas('machineCategories', function($query) use ($category_id) {
            $query->where('machine_categories.id', $category_id);
        })->orderBy('seller_name')->get();

        return response()->json([
            'brands' => $category->brands->map(function($brand) {
                return ['id' => $brand->id, 'name' => $brand->name];
            })->values(),
            'sellers' => $sellers->map(function($seller) {
                return ['id' => $seller->id, 'seller_name' => $seller->seller_name];
            })->values(),
            'feeders' => $category->feeders->map(function($feeder) {
                return [
                    'id' => $feeder->id,
                    'feeder' => $feeder->feeder . ($feeder->feederBrand ? ' (' . $feeder->feederBrand->name . ')' : '')
                ];
            }),
            'machine_hooks' => $category->machineHooks->map(function($hook) {
                return ['id' => $hook->id, 'hook' => $hook->hook];
            }),
            'machine_e_reads' => $category->machineEReads->map(function($eread) {
                return ['id' => $eread->id, 'name' => $eread->name];
            }),
            'colors' => $category->colors->map(function($color) {
                return ['id' => $color->id, 'name' => $color->name];
            }),
            'machine_nozzles' => $category->machineNozzles->map(function($nozzle) {
                return ['id' => $nozzle->id, 'nozzle' => $nozzle->nozzle];
            }),
            'machine_dropins' => $category->machineDropins->map(function($dropin) {
                return ['id' => $dropin->id, 'name' => $dropin->name];
            }),
            'machine_beams' => $category->machineBeams->map(function($beam) {
                return ['id' => $beam->id, 'name' => $beam->name];
            }),
            'machine_cloth_rollers' => $category->machineClothRollers->map(function($roller) {
                return ['id' => $roller->id, 'name' => $roller->name];
            }),
            'machine_softwares' => $category->machineSoftwares->map(function($software) {
                return ['id' => $software->id, 'name' => $software->name];
            }),
            'hsn_codes' => $category->hsnCodes->map(function($hsn) {
                return ['id' => $hsn->id, 'name' => $hsn->name];
            }),
            'machine_sizes' => $category->machineSizes->map(function($size) {
                return ['id' => $size->id, 'name' => $size->name];
            }),
            'wirs' => $category->wirs->map(function($wir) {
                return ['id' => $wir->id, 'name' => $wir->name];
            }),
            'machine_shafts' => $category->machineShafts->map(function($shaft) {
                return ['id' => $shaft->id, 'name' => $shaft->name];
            }),
            'machine_levers' => $category->machineLevers->map(function($lever) {
                return ['id' => $lever->id, 'name' => $lever->name];
            }),
            'machine_chains' => $category->machineChains->map(function($chain) {
                return ['id' => $chain->id, 'name' => $chain->name];
            }),
            'machine_heald_wires' => $category->machineHealdWires->map(function($wire) {
                return ['id' => $wire->id, 'name' => $wire->name];
            }),
        ]);
    }

    /**
     * Download Excel template for lead import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');

        $headers = ['Name', 'Phone', 'Type', 'State', 'City', 'Area', 'Business', 'Status', 'Brand', 'Quantity'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $exampleRow = ['Example Person', '9876543210', 'new', 'State Name', 'City Name', 'Area Name', 'Business Name', 'Status Name', 'Brand Name', '1'];
        $col = 'A';
        foreach ($exampleRow as $value) {
            $sheet->setCellValue($col . '2', $value);
            $col++;
        }

        $filename = 'leads_import_template_' . date('Y-m-d') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Import leads from Excel file.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'Please select an Excel file.',
            'file.mimes' => 'The file must be an Excel file (.xlsx or .xls).',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return redirect()->route('leads.index')
                ->with('error', 'Excel file must have a header row and at least one data row.');
        }

        $headerRow = $rows[0];
        $colMap = [];
        $expected = ['name' => ['name'], 'phone' => ['phone', 'phone number', 'phonenumber'], 'type' => ['type'], 'state' => ['state'], 'city' => ['city'], 'area' => ['area'], 'business' => ['business'], 'status' => ['status'], 'brand' => ['brand'], 'quantity' => ['quantity']];
        foreach ($headerRow as $colIndex => $val) {
            $v = trim(strtolower((string) $val));
            foreach ($expected as $key => $aliases) {
                if (in_array($v, $aliases) || $v === $key) {
                    $colMap[$key] = $colIndex;
                    break;
                }
            }
        }

        if (empty($colMap['name']) || empty($colMap['phone'])) {
            return redirect()->route('leads.index')
                ->with('error', 'Excel must have columns "Name" and "Phone" (or "Phone Number").');
        }

        $defaultStatus = Status::orderBy('name')->first();
        $defaultCategory = MachineCategory::orderBy('name')->first();
        if (!$defaultStatus || !$defaultCategory) {
            return redirect()->route('leads.index')
                ->with('error', 'Please ensure at least one Status and one Machine Category exist in the system.');
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $rowNum = $i + 2;
            $name = trim((string) ($row[$colMap['name']] ?? ''));
            $phone = trim((string) ($row[$colMap['phone']] ?? ''));
            if ($name === '' || $phone === '') {
                $skipped++;
                continue;
            }

            if (Lead::where('phone_number', $phone)->exists()) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Phone {$phone} already exists.";
                continue;
            }

            $type = isset($colMap['type']) ? strtolower(trim((string) ($row[$colMap['type']] ?? 'new'))) : 'new';
            if (!in_array($type, ['new', 'old'])) {
                $type = 'new';
            }

            $stateName = isset($colMap['state']) ? trim((string) ($row[$colMap['state']] ?? '')) : '';
            $cityName = isset($colMap['city']) ? trim((string) ($row[$colMap['city']] ?? '')) : '';
            $areaName = isset($colMap['area']) ? trim((string) ($row[$colMap['area']] ?? '')) : '';

            $state = $stateName ? State::where('name', 'like', $stateName)->first() : State::orderBy('name')->first();
            $city = null;
            $area = null;
            if ($state) {
                $city = $cityName ? City::where('state_id', $state->id)->where('name', 'like', $cityName)->first() : City::where('state_id', $state->id)->orderBy('name')->first();
                if ($city) {
                    $area = $areaName ? Area::where('city_id', $city->id)->where('name', 'like', $areaName)->first() : Area::where('city_id', $city->id)->orderBy('name')->first();
                }
            }
            if (!$state || !$city || !$area) {
                $errors[] = "Row {$rowNum}: Could not resolve State/City/Area for \"{$stateName}\" / \"{$cityName}\" / \"{$areaName}\". Skipped.";
                $skipped++;
                continue;
            }

            $businessId = null;
            if ($type === 'new' && isset($colMap['business'])) {
                $businessName = trim((string) ($row[$colMap['business']] ?? ''));
                if ($businessName) {
                    $business = Business::where('name', 'like', $businessName)->first();
                    $businessId = $business ? $business->id : null;
                }
            }
            if ($type === 'new' && !$businessId) {
                $businessId = Business::orderBy('name')->first()?->id;
            }

            $status = isset($colMap['status']) ? Status::where('name', 'like', trim((string) ($row[$colMap['status']] ?? '')))->first() : null;
            $statusId = $status ? $status->id : $defaultStatus->id;

            $brandId = null;
            if (isset($colMap['brand'])) {
                $brandName = trim((string) ($row[$colMap['brand']] ?? ''));
                if ($brandName) {
                    $brand = Brand::where('name', 'like', $brandName)->first();
                    $brandId = $brand ? $brand->id : null;
                }
            }

            $quantity = 1;
            if (isset($colMap['quantity'])) {
                $q = (int) ($row[$colMap['quantity']] ?? 1);
                if ($q >= 1) {
                    $quantity = $q;
                }
            }

            $leadData = [
                'type' => $type,
                'name' => $name,
                'phone_number' => $phone,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                'quantity' => $quantity,
                'status_id' => $statusId,
                'created_by' => auth()->id(),
            ];
            if ($type === 'new') {
                $leadData['business_id'] = $businessId;
            } else {
                $leadData['brand_name'] = isset($colMap['brand']) ? trim((string) ($row[$colMap['brand']] ?? '')) : '';
                $leadData['machine_quantity'] = $quantity;
                $leadData['running_since'] = '';
            }
            if ($brandId) {
                $leadData['brand_id'] = $brandId;
            }

            try {
                $lead = Lead::create($leadData);
                $lead->machineCategories()->attach($defaultCategory->id);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
                $skipped++;
            }
        }

        $msg = "{$imported} lead(s) imported successfully.";
        if ($skipped > 0) {
            $msg .= " {$skipped} row(s) skipped.";
        }
        if (count($errors) > 0) {
            return redirect()->route('leads.index')
                ->with('import_errors', array_slice($errors, 0, 20))
                ->with('success', $msg);
        }
        return redirect()->route('leads.index')->with('success', $msg);
    }
}
