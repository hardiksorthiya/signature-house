<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'contract_id',
        'seller_id',
        'proforma_invoice_number',
        'created_by',
        'total_amount',
        'notes',
        'type_of_sale',
        'currency',
        'usd_rate',
        'commission',
        'buyer_company_name',
        'pan',
        'gst',
        'phone_number',
        'phone_number_2',
        'ifc_certificate_number',
        'billing_address',
        'shipping_address',
        'overseas_freight',
        'port_expenses_clearing',
        'gst_percentage',
        'gst_amount',
        'final_amount_with_gst',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'usd_rate' => 'decimal:2',
        'commission' => 'decimal:2',
        'overseas_freight' => 'decimal:2',
        'port_expenses_clearing' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'final_amount_with_gst' => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function proformaInvoiceMachines()
    {
        return $this->hasMany(ProformaInvoiceMachine::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function deliveryDetails()
    {
        return $this->hasMany(PIDeliveryDetail::class)->orderBy('sort_order');
    }

    public function documents()
    {
        return $this->hasMany(PIDocument::class)->orderBy('row_number');
    }

    public function machineStatus()
    {
        return $this->hasOne(MachineStatus::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function preErectionDetails()
    {
        return $this->hasMany(PreErectionDetail::class)->orderBy('sort_order');
    }

    public function msUnloadingImages()
    {
        return $this->hasMany(MsUnloadingImage::class)->orderBy('created_at', 'desc');
    }

    public function damageDetails()
    {
        return $this->hasMany(DamageDetail::class)->orderBy('created_at', 'desc');
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class)->orderBy('created_at', 'desc');
    }

    public function machineErectionDetails()
    {
        return $this->hasMany(MachineErectionDetail::class)->orderBy('machine_category_id')->orderBy('sort_order')->orderBy('machine_number');
    }

    public function iaFittingDetails()
    {
        return $this->hasMany(IAFittingDetail::class)->orderBy('machine_category_id')->orderBy('machine_number')->orderBy('sort_order');
    }

    public function piSpareLists()
    {
        return $this->hasMany(PiSpareList::class)->orderBy('sort_order')->orderBy('id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Calculate total amount from stored machines and expenses
     * Matches the frontend calculation: per-machine expenses, commission, and GST
     * Uses stored totals (overseas_freight, port_expenses_clearing, gst_amount) which are now calculated correctly
     */
    public function calculateTotalAmount()
    {
        // Load machines if not already loaded
        if (!$this->relationLoaded('proformaInvoiceMachines')) {
            $this->load('proformaInvoiceMachines');
        }

        // Calculate total from machines with per-machine commission
        $totalMachineAmount = 0;
        $totalCommissionAmount = 0;
        
        foreach ($this->proformaInvoiceMachines as $machine) {
            $unitAmount = $machine->amount ?? 0;
            $amcPrice = $machine->amc_price ?? 0;
            $piMachineAmount = $unitAmount * ($machine->quantity ?? 0);
            $piTotalAmount = $piMachineAmount + $amcPrice;
            $totalMachineAmount += $piTotalAmount;
            
            // Commission Amount (for High Seas only, per machine)
            if ($this->type_of_sale === 'high_seas' && $this->commission) {
                $commissionAmount = ($piTotalAmount * $this->commission) / 100;
                $totalCommissionAmount += $commissionAmount;
            }
        }
        
        // Add stored totals (now calculated correctly per-machine and summed)
        $overseasFreight = $this->overseas_freight ?? 0;
        $portExpensesClearing = $this->port_expenses_clearing ?? 0;
        $gstAmount = $this->gst_amount ?? 0; // Stored GST amount (calculated per-machine)
        
        // Final amount = Machines + AMC + Commission + Freight + Port + GST
        $finalAmountWithGST = $totalMachineAmount + $totalCommissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
        
        // Store USD amount for all types (frontend handles display conversion)
        // For local sales, amounts are calculated in USD and displayed with ₹ symbol
        // The USD equivalent shown in parentheses is calculated as: displayAmount / usd_rate
        // So we return the USD amount (finalAmountWithGST) without conversion
        $displayAmount = $finalAmountWithGST;
        
        return $displayAmount;
    }
}
