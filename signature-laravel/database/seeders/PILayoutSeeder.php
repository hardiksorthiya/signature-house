<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PILayout;

class PILayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update or create default layout
        $defaultLayout = PILayout::where('is_default', true)->first();
        
        if ($defaultLayout) {
            // Update existing default layout
            $defaultLayout->update([
                'template_html' => $this->getDefaultTemplate(),
                'description' => 'Default proforma invoice layout template with centered seller logo',
            ]);
        } else {
            // Create new default layout
            PILayout::create([
                'name' => 'Default Layout',
                'description' => 'Default proforma invoice layout template with centered seller logo',
                'template_html' => $this->getDefaultTemplate(),
                'is_active' => true,
                'is_default' => true,
            ]);
        }
    }
    
    private function getDefaultTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $proformaInvoice->proforma_invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; line-height: 1.6; }
        .container { max-width: 210mm; margin: 0 auto; }
        
        /* Header Section */
        .header-section { margin-bottom: 30px; }
        .logo-container { text-align: center; margin-bottom: 20px; }
        .logo-container img { max-width: 300px; max-height: 100px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .invoice-number { text-align: right; }
        .invoice-number p { margin: 5px 0; }
        
        /* Recipient Section */
        .recipient-section { margin-bottom: 20px; }
        .recipient-section p { margin: 5px 0; }
        
        /* Title */
        .invoice-title { text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0; text-transform: uppercase; }
        
        /* Product Table */
        .product-section { margin: 30px 0; }
        .product-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
        .product-table th, .product-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .product-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .product-table td { vertical-align: top; }
        .product-table .sr-no { text-align: center; width: 5%; }
        .product-table .description { width: 50%; }
        .product-table .quantity { text-align: center; width: 10%; }
        .product-table .unit-price { text-align: right; width: 15%; }
        .product-table .total-price { text-align: right; width: 15%; }
        .pricing-section { margin: 20px 0; }
        .pricing-table { width: 100%; margin: 20px 0; }
        .pricing-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #ccc; }
        .pricing-label { font-weight: bold; }
        .pricing-value { text-align: right; }
        .total-row { font-weight: bold; font-size: 14px; margin-top: 10px; padding-top: 10px; border-top: 2px solid #333; }
        .amount-in-words { text-align: center; font-weight: bold; text-transform: uppercase; margin: 20px 0; padding: 10px; background-color: #f5f5f5; }
        
        /* Terms and Bank Details */
        .terms-section { margin: 30px 0; }
        .terms-row { display: flex; justify-content: space-between; margin: 8px 0; }
        .terms-label { font-weight: bold; }
        .bank-details { margin-top: 20px; }
        .bank-row { display: flex; justify-content: space-between; margin: 5px 0; }
        
        /* Signature Section */
        .signature-section { margin-top: 40px; text-align: right; }
        
        /* Footer */
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #666; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Centered Logo -->
        <div class="header-section">
            <div class="logo-container">
                @if($seller && $seller->signature)
                    <img src="{{ asset(\'public/\' . $seller->signature) }}" alt="{{ $seller->seller_name ?? \'Logo\' }}" />
                @else
                    <h2 style="font-size: 24px; font-weight: bold;">{{ $seller->seller_name ?? \'Company Name\' }}</h2>
                @endif
            </div>
            
            <div class="invoice-header">
                <div></div>
                <div class="invoice-number">
                    <p><strong>Performa No.</strong> {{ $proformaInvoice->proforma_invoice_number }}</p>
                    <p><strong>DATE:</strong> {{ $proformaInvoice->created_at->format(\'d-m-Y\') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Recipient Information -->
        <div class="recipient-section">
            <p><strong>TO</strong></p>
            <p>{{ $proformaInvoice->buyer_company_name }}</p>
            @if($proformaInvoice->billing_address)
            <p>{{ $proformaInvoice->billing_address }}</p>
            @endif
            <p style="margin-top: 10px;">Dear Sir,</p>
        </div>
        
        <!-- Invoice Title -->
        <div class="invoice-title">PROFORMA INVOICE</div>
        
        <!-- Product Details Table -->
        <div class="product-section">
            @php
                $totalQtyPrice = 0;
                $totalPIPrice = 0;
                $gstPercentage = $proformaInvoice->gst_percentage ?? 18;
                $gstAmount = 0;
                $finalAmount = 0;
            @endphp
            
            <table class="product-table">
                <thead>
                    <tr>
                        <th class="sr-no">Sr. No.</th>
                        <th class="description">Description</th>
                        <th class="quantity">Quantity</th>
                        <th class="unit-price">Unit Price</th>
                        <th class="total-price">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proformaInvoice->proformaInvoiceMachines as $index => $machine)
                        @php
                            $contractMachine = $machine->contractMachine;
                            $qty = $machine->quantity;
                            $unitPrice = $machine->amount;
                            $qtyPrice = $qty * $unitPrice;
                            $totalQtyPrice += $qtyPrice;
                            $totalPIPrice += $qtyPrice;
                            
                            // Build description
                            $description = [];
                            if($contractMachine->brand) $description[] = \'Brand: \' . $contractMachine->brand->name;
                            if($contractMachine->machineCategory) $description[] = \'Category: \' . $contractMachine->machineCategory->name;
                            if($contractMachine->machineModel) $description[] = \'Model: \' . $contractMachine->machineModel->model_no;
                            if($contractMachine->feeder) $description[] = \'Feeder: \' . $contractMachine->feeder->name;
                            if($contractMachine->machineSoftware) $description[] = \'Software: \' . $contractMachine->machineSoftware->name;
                            if($contractMachine->machineNozzle) $description[] = \'Nozzle: \' . $contractMachine->machineNozzle->name;
                            if($contractMachine->machineBeam) $description[] = \'Beam: \' . $contractMachine->machineBeam->name;
                            if($contractMachine->machineClothRoller) $description[] = \'Cloth Roller: \' . $contractMachine->machineClothRoller->name;
                            if($contractMachine->machineShaft) $description[] = \'Shaft: \' . $contractMachine->machineShaft->name;
                            if($contractMachine->machineChain) $description[] = \'CAM-Chain: \' . $contractMachine->machineChain->name;
                            if($contractMachine->machineHealdWire) $description[] = \'Heald Wires: \' . $contractMachine->machineHealdWire->name;
                            if($contractMachine->machineERead) $description[] = \'E Read: \' . $contractMachine->machineERead->name;
                            $descriptionText = implode(\', \', $description);
                        @endphp
                        <tr>
                            <td class="sr-no">{{ $index + 1 }}</td>
                            <td class="description">{{ $descriptionText ?: \'N/A\' }}</td>
                            <td class="quantity">{{ $qty }}</td>
                            <td class="unit-price">{{ $proformaInvoice->currency }} {{ number_format($unitPrice, 2) }}</td>
                            <td class="total-price">{{ $proformaInvoice->currency }} {{ number_format($qtyPrice, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pricing Breakdown -->
            <div class="pricing-section">
                <div class="pricing-table">
                    @php
                        $gstAmount = ($totalPIPrice * $gstPercentage) / 100;
                        $finalAmount = $totalPIPrice + $gstAmount;
                    @endphp
                    <div class="pricing-row">
                        <span class="pricing-label">Subtotal</span>
                        <span class="pricing-value">{{ $proformaInvoice->currency }} {{ number_format($totalPIPrice, 2) }}</span>
                    </div>
                    <div class="pricing-row">
                        <span class="pricing-label">GST ({{ $gstPercentage }}%)</span>
                        <span class="pricing-value">{{ $proformaInvoice->currency }} {{ number_format($gstAmount, 2) }}</span>
                    </div>
                    <div class="pricing-row total-row">
                        <span class="pricing-label">Final Amount (Including GST)</span>
                        <span class="pricing-value">{{ $proformaInvoice->currency }} {{ number_format($finalAmount, 2) }}</span>
                    </div>
                </div>
            
                <!-- Amount in Words -->
                <div class="amount-in-words">
                    @php
                        function numberToWords($number) {
                            $ones = ["", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN"];
                            $tens = ["", "", "TWENTY", "THIRTY", "FORTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY"];
                            
                            $whole = floor($number);
                            $fraction = round(($number - $whole) * 100);
                            
                            $result = "";
                            if ($whole >= 10000000) {
                                $crores = floor($whole / 10000000);
                                $result .= numberToWordsHelper($crores) . " CRORE ";
                                $whole %= 10000000;
                            }
                            if ($whole >= 100000) {
                                $lakhs = floor($whole / 100000);
                                $result .= numberToWordsHelper($lakhs) . " LAKH ";
                                $whole %= 100000;
                            }
                            if ($whole >= 1000) {
                                $thousands = floor($whole / 1000);
                                $result .= numberToWordsHelper($thousands) . " THOUSAND ";
                                $whole %= 1000;
                            }
                            if ($whole >= 100) {
                                $result .= $ones[floor($whole / 100)] . " HUNDRED ";
                                $whole %= 100;
                            }
                            if ($whole >= 20) {
                                $result .= $tens[floor($whole / 10)] . " ";
                                $whole %= 10;
                            }
                            if ($whole > 0) {
                                $result .= $ones[$whole] . " ";
                            }
                            if ($fraction > 0) {
                                $result .= "AND " . numberToWordsHelper($fraction) . " PAISE ";
                            }
                            return trim($result) . " ONLY";
                        }
                        
                        function numberToWordsHelper($num) {
                            $ones = ["", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN"];
                            $tens = ["", "", "TWENTY", "THIRTY", "FORTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY"];
                            
                            if ($num < 20) {
                                return $ones[$num];
                            } else {
                                return $tens[floor($num / 10)] . " " . $ones[$num % 10];
                            }
                        }
                    @endphp
                    {{ strtoupper(numberToWords($finalAmount)) }}
                </div>
            </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms-section">
            <p><strong>Terms and Conditions: -</strong></p>
            <div class="terms-row">
                <span class="terms-label">PAYMENT TERMS :</span>
                <span>25% ADVANCE (PART PAYMENT ALLOWED) & 75% BALANCE BY T/T OR L/C BEFORE SHIPMENT</span>
            </div>
            <div class="terms-row">
                <span class="terms-label">PARTIAL SHIPMENT :</span>
                <span>ALLOWED</span>
            </div>
            
            <!-- Bank Details -->
            <div class="bank-details">
                <p><strong>Bank Detail:-</strong></p>
                @if($seller && $seller->bankDetails->count() > 0)
                    @foreach($seller->bankDetails as $bank)
                        <div class="bank-row">
                            <span class="terms-label">Firm Name :</span>
                            <span>{{ $seller->seller_name }}</span>
                        </div>
                        <div class="bank-row">
                            <span class="terms-label">A/c No :</span>
                            <span>{{ $bank->account_number }}</span>
                        </div>
                        <div class="bank-row">
                            <span class="terms-label">Bank Name :</span>
                            <span>{{ $bank->bank_name }}@if($bank->branch_name), {{ $bank->branch_name }}@endif</span>
                        </div>
                        @if($bank->ifsc_code)
                        <div class="bank-row">
                            <span class="terms-label">IFSC Code :</span>
                            <span>{{ $bank->ifsc_code }}</span>
                        </div>
                        @endif
                        @break
                    @endforeach
                @endif
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <p>For,</p>
            <p><strong>{{ $seller->seller_name ?? \'Company Name\' }}</strong></p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $seller->seller_name ?? \'Company Name\' }}</strong></p>
            @if($seller && $seller->address)
            <p>{{ $seller->address }}</p>
            @endif
            @if($seller && $seller->mobile)
            <p>{{ $seller->mobile }}</p>
            @endif
            @if($seller && $seller->email)
            <p>{{ $seller->email }}</p>
            @endif
        </div>
    </div>
</body>
</html>';
    }
}
