<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected function leadsQuery()
    {
        $query = Lead::with(['business', 'state', 'city', 'area', 'status', 'brand', 'creator']);
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())->orWhereIn('created_by', $teamMemberIds);
            });
        }
        return $query;
    }

    protected function contractsQuery()
    {
        $query = Contract::with(['creator', 'state', 'city', 'area', 'businessFirm']);
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())->orWhereIn('created_by', $teamMemberIds);
            });
        }
        return $query;
    }

    protected function proformaInvoicesQuery()
    {
        $query = ProformaInvoice::with(['contract.creator', 'seller', 'creator']);
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->whereHas('contract', function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())->orWhereIn('created_by', $teamMemberIds);
            });
        }
        return $query;
    }

    protected function purchaseOrdersQuery()
    {
        $query = PurchaseOrder::with(['proformaInvoice.contract.creator', 'creator', 'portOfDestination']);
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->whereHas('proformaInvoice.contract', function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())->orWhereIn('created_by', $teamMemberIds);
            });
        }
        return $query;
    }

    protected function paymentsQuery()
    {
        return Payment::with(['contract', 'proformaInvoice.contract', 'creator', 'payeeCountry']);
    }

    /**
     * Apply date range on payment_date for payments report.
     */
    protected function applyPaymentDateRange($query, Request $request)
    {
        $period = $request->get('period', 'last_month');
        $today = Carbon::today();
        $col = 'payments.payment_date';

        if ($period === 'today') {
            $query->whereDate($col, $today);
        } elseif ($period === 'yesterday') {
            $query->whereDate($col, $today->copy()->subDay());
        } elseif ($period === 'last_week') {
            $query->whereBetween($col, [$today->copy()->subWeek()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_month') {
            $query->whereBetween($col, [$today->copy()->subMonth()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_year') {
            $query->whereBetween($col, [$today->copy()->subYear()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'custom' && $request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween($col, [$from, $to]);
        }
        return $query;
    }

    /**
     * Apply free-text search to leads report (name, phone, business, state, city, area, status, brand).
     */
    protected function applySearchLeads($query, $term)
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like, $term) {
            $q->where('leads.name', 'like', $like)
                ->orWhere('leads.phone_number', 'like', $like)
                ->orWhereHas('business', fn($b) => $b->where('name', 'like', $like))
                ->orWhereHas('state', fn($s) => $s->where('name', 'like', $like))
                ->orWhereHas('city', fn($c) => $c->where('name', 'like', $like))
                ->orWhereHas('area', fn($a) => $a->where('name', 'like', $like))
                ->orWhereHas('status', fn($s) => $s->where('name', 'like', $like))
                ->orWhereHas('brand', fn($b) => $b->where('name', 'like', $like));
        });
    }

    /**
     * Apply free-text search to contracts report (contract_number, buyer_name, company_name, state, city).
     */
    protected function applySearchContracts($query, $term)
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like) {
            $q->where('contracts.contract_number', 'like', $like)
                ->orWhere('contracts.buyer_name', 'like', $like)
                ->orWhere('contracts.company_name', 'like', $like)
                ->orWhereHas('state', fn($s) => $s->where('name', 'like', $like))
                ->orWhereHas('city', fn($c) => $c->where('name', 'like', $like));
        });
    }

    /**
     * Apply free-text search to PI report (PI number, buyer, contract number, seller).
     */
    protected function applySearchPi($query, $term)
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like) {
            $q->where('proforma_invoices.proforma_invoice_number', 'like', $like)
                ->orWhere('proforma_invoices.buyer_company_name', 'like', $like)
                ->orWhereHas('contract', fn($c) => $c->where('contract_number', 'like', $like)->orWhere('buyer_name', 'like', $like)->orWhere('company_name', 'like', $like))
                ->orWhereHas('seller', fn($s) => $s->where('seller_name', 'like', $like));
        });
    }

    /**
     * Apply free-text search to PO report (PO number, buyer, PI number, port).
     */
    protected function applySearchPo($query, $term)
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like) {
            $q->where('purchase_orders.purchase_order_number', 'like', $like)
                ->orWhere('purchase_orders.buyer_name', 'like', $like)
                ->orWhereHas('proformaInvoice', fn($pi) => $pi->where('proforma_invoice_number', 'like', $like))
                ->orWhereHas('portOfDestination', fn($p) => $p->where('name', 'like', $like));
        });
    }

    /**
     * Apply free-text search to payments report (transaction_id, contract number, PI number, customer).
     */
    protected function applySearchPayments($query, $term)
    {
        $like = '%' . $term . '%';
        $query->where(function ($q) use ($like) {
            $q->where('payments.transaction_id', 'like', $like)
                ->orWhereHas('contract', fn($c) => $c->where('contract_number', 'like', $like)->orWhere('buyer_name', 'like', $like)->orWhere('company_name', 'like', $like))
                ->orWhereHas('proformaInvoice', fn($pi) => $pi->where('proforma_invoice_number', 'like', $like)->orWhere('buyer_company_name', 'like', $like));
        });
    }

    /**
     * Apply date range. $table = table name for created_at column (e.g. 'leads', 'contracts', 'proforma_invoices', 'purchase_orders').
     */
    protected function applyDateRange($query, Request $request, $table = 'leads')
    {
        $period = $request->get('period', 'last_month');
        $today = Carbon::today();
        $col = $table . '.created_at';

        if ($period === 'today') {
            $query->whereDate($col, $today);
        } elseif ($period === 'yesterday') {
            $query->whereDate($col, $today->copy()->subDay());
        } elseif ($period === 'last_week') {
            $query->whereBetween($col, [$today->copy()->subWeek()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_month') {
            $query->whereBetween($col, [$today->copy()->subMonth()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_year') {
            $query->whereBetween($col, [$today->copy()->subYear()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'custom' && $request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween($col, [$from, $to]);
        }
        return $query;
    }

    /**
     * Reports index - list available reports.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Leads report page: filters, sort, table, export options.
     */
    public function leadsReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->leadsQuery();
        $this->applyDateRange($query, $request, 'leads');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchLeads($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('leads.created_by', $request->created_by);
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'name'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('leads.' . $sortCol, $sortDir);

        $leads = $query->paginate(20)->withQueryString();

        $creators = User::whereIn('id', $this->leadsQuery()->distinct()->pluck('created_by')->filter())->orderBy('name')->get(['id', 'name']);

        return view('reports.leads', compact('leads', 'creators'));
    }

    /**
     * Export leads report as Excel or PDF.
     */
    public function exportLeads(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->leadsQuery();
        $this->applyDateRange($query, $request, 'leads');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchLeads($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('leads.created_by', $request->created_by);
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'name'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('leads.' . $sortCol, $sortDir);

        $leads = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            return $this->exportLeadsPdf($leads, $request);
        }
        return $this->exportLeadsExcel($leads, $request);
    }

    protected function exportLeadsExcel($leads, Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads Report');

        $headers = ['Date', 'Name', 'Phone', 'Business', 'State', 'City', 'Area', 'Status', 'Type', 'Created By'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');

        $row = 2;
        foreach ($leads as $lead) {
            $sheet->setCellValue('A' . $row, $lead->created_at->format('d M Y'));
            $sheet->setCellValue('B' . $row, $lead->name);
            $sheet->setCellValue('C' . $row, $lead->phone_number);
            $sheet->setCellValue('D' . $row, $lead->business->name ?? '—');
            $sheet->setCellValue('E' . $row, $lead->state->name ?? '—');
            $sheet->setCellValue('F' . $row, $lead->city->name ?? '—');
            $sheet->setCellValue('G' . $row, $lead->area->name ?? '—');
            $sheet->setCellValue('H' . $row, $lead->status->name ?? '—');
            $sheet->setCellValue('I' . $row, $lead->type ?? '—');
            $sheet->setCellValue('J' . $row, $lead->creator->name ?? '—');
            $row++;
        }

        foreach (range('A', 'J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'leads-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    protected function exportLeadsPdf($leads, Request $request)
    {
        $pdf = DomPDF::loadView('reports.leads-pdf', compact('leads'));
        $filename = 'leads-report-' . now()->format('Y-m-d-His') . '.pdf';
        return $pdf->download($filename);
    }

    // ----- Contract Report -----

    public function contractsReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->contractsQuery();
        $this->applyDateRange($query, $request, 'contracts');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchContracts($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('contracts.created_by', $request->created_by);
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'contract_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('contracts.' . $sortCol, $sortDir);

        $contracts = $query->paginate(20)->withQueryString();
        $creators = User::whereIn('id', $this->contractsQuery()->distinct()->pluck('created_by')->filter())->orderBy('name')->get(['id', 'name']);

        return view('reports.contracts', compact('contracts', 'creators'));
    }

    public function exportContracts(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->contractsQuery();
        $this->applyDateRange($query, $request, 'contracts');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchContracts($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('contracts.created_by', $request->created_by);
        }
        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'contract_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('contracts.' . $sortCol, $sortDir);
        $contracts = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.contracts-pdf', compact('contracts'));
            return $pdf->download('contracts-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportContractsExcel($contracts);
    }

    protected function exportContractsExcel($contracts)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Contracts Report');
        $headers = ['Date', 'Contract No', 'Buyer / Company', 'State', 'City', 'Amount', 'Status', 'Created By'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($contracts as $c) {
            $sheet->setCellValue('A' . $row, $c->created_at->format('d M Y'));
            $sheet->setCellValue('B' . $row, $c->contract_number);
            $sheet->setCellValue('C' . $row, $c->company_name ?: $c->buyer_name);
            $sheet->setCellValue('D' . $row, $c->state->name ?? '—');
            $sheet->setCellValue('E' . $row, $c->city->name ?? '—');
            $sheet->setCellValue('F' . $row, format_amount($c->total_amount, 'USD'));
            $sheet->setCellValue('G' . $row, $c->approval_status ?? '—');
            $sheet->setCellValue('H' . $row, $c->creator->name ?? '—');
            $row++;
        }
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'contracts-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ----- PI Report -----

    public function piReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->proformaInvoicesQuery();
        $this->applyDateRange($query, $request, 'proforma_invoices');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPi($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('created_by', $request->created_by);
            });
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'proforma_invoice_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('proforma_invoices.' . $sortCol, $sortDir);

        $proformaInvoices = $query->paginate(20)->withQueryString();
        $creators = User::whereIn('id', $this->contractsQuery()->whereHas('proformaInvoices')->distinct()->pluck('created_by')->filter())->orderBy('name')->get(['id', 'name']);

        return view('reports.pi', compact('proformaInvoices', 'creators'));
    }

    public function exportPi(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->proformaInvoicesQuery();
        $this->applyDateRange($query, $request, 'proforma_invoices');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPi($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('created_by', $request->created_by);
            });
        }
        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'proforma_invoice_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('proforma_invoices.' . $sortCol, $sortDir);
        $proformaInvoices = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.pi-pdf', compact('proformaInvoices'));
            return $pdf->download('pi-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportPiExcel($proformaInvoices);
    }

    protected function exportPiExcel($proformaInvoices)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PI Report');
        $headers = ['Date', 'PI Number', 'Contract', 'Buyer/Company', 'Seller', 'Amount'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($proformaInvoices as $pi) {
            $sheet->setCellValue('A' . $row, $pi->created_at->format('d M Y'));
            $sheet->setCellValue('B' . $row, $pi->proforma_invoice_number);
            $sheet->setCellValue('C' . $row, $pi->contract->contract_number ?? '—');
            $sheet->setCellValue('D' . $row, $pi->buyer_company_name ?: ($pi->contract->company_name ?? $pi->contract->buyer_name ?? '—'));
            $sheet->setCellValue('E' . $row, $pi->seller->seller_name ?? '—');
            $sheet->setCellValue('F' . $row, format_amount($pi->total_amount, $pi->currency));
            $row++;
        }
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'pi-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ----- PO Report -----

    public function poReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->purchaseOrdersQuery();
        $this->applyDateRange($query, $request, 'purchase_orders');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPo($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->whereHas('proformaInvoice.contract', function ($q) use ($request) {
                $q->where('created_by', $request->created_by);
            });
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'purchase_order_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('purchase_orders.' . $sortCol, $sortDir);

        $purchaseOrders = $query->paginate(20)->withQueryString();
        $creators = User::whereIn('id', $this->contractsQuery()->whereHas('proformaInvoices')->distinct()->pluck('created_by')->filter())->orderBy('name')->get(['id', 'name']);

        return view('reports.po', compact('purchaseOrders', 'creators'));
    }

    public function exportPo(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->purchaseOrdersQuery();
        $this->applyDateRange($query, $request, 'purchase_orders');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPo($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->whereHas('proformaInvoice.contract', function ($q) use ($request) {
                $q->where('created_by', $request->created_by);
            });
        }
        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['created_at', 'purchase_order_number'])) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('purchase_orders.' . $sortCol, $sortDir);
        $purchaseOrders = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.po-pdf', compact('purchaseOrders'));
            return $pdf->download('po-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportPoExcel($purchaseOrders);
    }

    protected function exportPoExcel($purchaseOrders)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PO Report');
        $headers = ['Date', 'PO Number', 'PI Number', 'Buyer', 'Port'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($purchaseOrders as $po) {
            $sheet->setCellValue('A' . $row, $po->created_at->format('d M Y'));
            $sheet->setCellValue('B' . $row, $po->purchase_order_number);
            $sheet->setCellValue('C' . $row, $po->proformaInvoice->proforma_invoice_number ?? '—');
            $sheet->setCellValue('D' . $row, $po->buyer_name);
            $sheet->setCellValue('E' . $row, $po->portOfDestination->name ?? '—');
            $row++;
        }
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'po-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ----- Payment Report -----

    public function paymentsReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->paymentsQuery();
        $this->applyPaymentDateRange($query, $request);
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPayments($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $sortCol = $request->get('sort', 'payment_date');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['payment_date', 'amount', 'type'])) {
            $sortCol = 'payment_date';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('payments.' . $sortCol, $sortDir);

        $payments = $query->paginate(20)->withQueryString();
        $creators = User::whereIn('id', Payment::distinct()->pluck('created_by')->filter())->orderBy('name')->get(['id', 'name']);

        return view('reports.payments', compact('payments', 'creators'));
    }

    public function exportPayments(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->paymentsQuery();
        $this->applyPaymentDateRange($query, $request);
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPayments($query, $term);
            }
        }
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }
        $sortCol = $request->get('sort', 'payment_date');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['payment_date', 'amount', 'type'])) {
            $sortCol = 'payment_date';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy('payments.' . $sortCol, $sortDir);
        $payments = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.payments-pdf', compact('payments'));
            return $pdf->download('payment-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportPaymentsExcel($payments);
    }

    protected function exportPaymentsExcel($payments)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Payment Report');
        $headers = ['Payment Date', 'Type', 'Contract', 'PI Number', 'Customer', 'Amount', 'Method', 'Created By'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($payments as $p) {
            $contractNumber = $p->contract->contract_number ?? ($p->proformaInvoice->contract->contract_number ?? '—');
            $piNumber = $p->proformaInvoice->proforma_invoice_number ?? '—';
            $customer = $p->contract->buyer_name ?? ($p->proformaInvoice->buyer_company_name ?? '—');
            $currency = $p->payeeCountry && $p->payeeCountry->currency ? $p->payeeCountry->currency : '₹';
            $sheet->setCellValue('A' . $row, $p->payment_date->format('d M Y'));
            $sheet->setCellValue('B' . $row, ucfirst($p->type ?? '—'));
            $sheet->setCellValue('C' . $row, $contractNumber);
            $sheet->setCellValue('D' . $row, $piNumber);
            $sheet->setCellValue('E' . $row, $customer);
            $sheet->setCellValue('F' . $row, $currency . number_format($p->amount, 2));
            $sheet->setCellValue('G' . $row, $p->payment_method ?? '—');
            $sheet->setCellValue('H' . $row, $p->creator->name ?? '—');
            $row++;
        }
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'payment-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ----- Customer Report (Customer Ledger) -----

    /**
     * List contracts; user picks one to view full customer ledger.
     */
    public function customersReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->contractsQuery()->orderBy('contracts.created_at', 'desc');

        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('contracts.contract_number', 'like', $term)
                    ->orWhere('contracts.buyer_name', 'like', $term)
                    ->orWhere('contracts.company_name', 'like', $term);
            });
        }

        $contracts = $query->paginate(20)->withQueryString();

        return view('reports.customers', compact('contracts'));
    }

    /**
     * Full customer ledger for one contract: Lead → Contract → Payments → PIs → POs → MS Unloading → Complaints.
     */
    public function customerLedger(Contract $contract)
    {
        $this->authorize('view reports');

        // Restrict to same visibility as contract report
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $allowed = in_array($contract->created_by, array_merge([auth()->id()], $teamMemberIds));
            if (!$allowed) {
                abort(404);
            }
        }

        $contract->load([
            'lead.business', 'lead.state', 'lead.city', 'lead.area', 'lead.status', 'lead.brand', 'lead.creator',
            'state', 'city', 'area', 'creator', 'businessFirm',
            'proformaInvoices.seller',
            'proformaInvoices.purchaseOrders.portOfDestination',
            'proformaInvoices.msUnloadingImages',
            'proformaInvoices.piSpareLists',
            'proformaInvoices.preErectionDetails',
            'proformaInvoices.damageDetails',
            'proformaInvoices.serialNumbers.machineCategory',
            'proformaInvoices.machineErectionDetails.machineCategory',
            'proformaInvoices.iaFittingDetails.machineCategory',
            'complaints.complainType',
            'complaints.machineCategory',
            'complaints.spares',
            'complaints.creator',
        ]);

        $piIds = $contract->proformaInvoices->pluck('id')->toArray();
        $payments = Payment::with(['payeeCountry', 'creator', 'proformaInvoice'])
            ->where(function ($q) use ($contract, $piIds) {
                $q->where('contract_id', $contract->id);
                if (count($piIds)) {
                    $q->orWhereIn('proforma_invoice_id', $piIds);
                }
            })
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('reports.customer-ledger', compact('contract', 'payments'));
    }

    // ----- Spare Used Report -----

    protected function spareUsedQuery()
    {
        $query = DB::table('complaint_spare')
            ->join('complaints', 'complaint_spare.complaint_id', '=', 'complaints.id')
            ->join('contracts', 'complaints.contract_id', '=', 'contracts.id')
            ->join('spares', 'complaint_spare.spare_id', '=', 'spares.id')
            ->leftJoin('users as complaint_creator', 'complaints.created_by', '=', 'complaint_creator.id')
            ->select(
                'complaint_spare.id',
                'complaint_spare.used_at',
                'complaint_spare.quantity',
                'complaints.id as complaint_id',
                'contracts.contract_number',
                'contracts.buyer_name',
                'contracts.company_name',
                'spares.name as spare_name',
                'complaints.created_by as complaint_created_by',
                'complaint_creator.name as created_by_name'
            );

        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->whereIn('complaints.created_by', array_merge([auth()->id()], $teamMemberIds));
        }
        return $query;
    }

    protected function applySpareUsedDateRange($query, Request $request)
    {
        $period = $request->get('period', 'last_month');
        $today = Carbon::today();
        $col = 'complaint_spare.used_at';

        if ($period === 'today') {
            $query->whereDate($col, $today);
        } elseif ($period === 'yesterday') {
            $query->whereDate($col, $today->copy()->subDay());
        } elseif ($period === 'last_week') {
            $query->whereBetween($col, [$today->copy()->subWeek()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_month') {
            $query->whereBetween($col, [$today->copy()->subMonth()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'last_year') {
            $query->whereBetween($col, [$today->copy()->subYear()->startOfDay(), $today->endOfDay()]);
        } elseif ($period === 'custom' && $request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween($col, [$from, $to]);
        }
        return $query;
    }

    public function spareUsedReport(Request $request)
    {
        $this->authorize('view reports');

        $query = $this->spareUsedQuery();
        $this->applySpareUsedDateRange($query, $request);

        if ($request->filled('created_by')) {
            $query->where('complaints.created_by', $request->created_by);
        }

        $sortCol = $request->get('sort', 'used_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['used_at', 'quantity', 'spare_name', 'contract_number'])) {
            $sortCol = 'used_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy($sortCol, $sortDir);

        $usages = $query->paginate(20, ['*'], 'page')->withQueryString();
        $creators = User::whereIn('id', array_filter(DB::table('complaint_spare')->join('complaints', 'complaints.id', '=', 'complaint_spare.complaint_id')->distinct()->pluck('complaints.created_by')->toArray()))->orderBy('name')->get(['id', 'name']);

        return view('reports.spare-used', compact('usages', 'creators'));
    }

    public function exportSpareUsed(Request $request)
    {
        $this->authorize('export reports');

        $query = $this->spareUsedQuery();
        $this->applySpareUsedDateRange($query, $request);
        if ($request->filled('created_by')) {
            $query->where('complaints.created_by', $request->created_by);
        }
        $sortCol = $request->get('sort', 'used_at');
        $sortDir = $request->get('dir', 'desc');
        if (!in_array($sortCol, ['used_at', 'quantity', 'spare_name', 'contract_number'])) {
            $sortCol = 'used_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $query->orderBy($sortCol, $sortDir);
        $usages = $query->get();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.spare-used-pdf', compact('usages'));
            return $pdf->download('spare-used-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportSpareUsedExcel($usages);
    }

    protected function exportSpareUsedExcel($usages)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Spare Used Report');
        $headers = ['Date Used', 'Spare Name', 'Quantity', 'Contract', 'Customer', 'Created By'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($usages as $u) {
            $sheet->setCellValue('A' . $row, $u->used_at ? Carbon::parse($u->used_at)->format('d M Y') : '—');
            $sheet->setCellValue('B' . $row, $u->spare_name ?? '—');
            $sheet->setCellValue('C' . $row, $u->quantity ?? 0);
            $sheet->setCellValue('D' . $row, $u->contract_number ?? '—');
            $sheet->setCellValue('E' . $row, $u->company_name ?: $u->buyer_name ?? '—');
            $sheet->setCellValue('F' . $row, $u->created_by_name ?? '—');
            $row++;
        }
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'spare-used-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ----- Seller Report -----

    /**
     * Seller report: list sellers with company details, machine count, prices, totals.
     */
    public function sellersReport(Request $request)
    {
        $this->authorize('view reports');

        $piQuery = $this->proformaInvoicesQuery();
        $this->applyDateRange($piQuery, $request, 'proforma_invoices');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPi($piQuery, $term);
            }
        }
        if ($request->filled('seller_id')) {
            $piQuery->where('seller_id', $request->seller_id);
        }
        $piQuery->whereNotNull('seller_id');

        $pis = $piQuery->with(['proformaInvoiceMachines', 'seller.country'])->get();
        $bySeller = $pis->groupBy('seller_id');
        $sellerIds = $bySeller->keys()->filter()->toArray();
        $sellersBase = Seller::with(['country'])->whereIn('id', $sellerIds)->orderBy('seller_name')->get()->keyBy('id');

        $sellerData = collect();
        foreach ($sellersBase as $seller) {
            $sellerPis = $bySeller->get($seller->id, collect());
            $sellerData->push((object)[
                'seller' => $seller,
                'pi_count' => $sellerPis->count(),
                'total_machines' => $sellerPis->sum(fn($pi) => $pi->proformaInvoiceMachines->sum('quantity')),
                'total_amount' => $sellerPis->sum('total_amount'),
                'currency' => $sellerPis->first()->currency ?? 'USD',
            ]);
        }

        $sortCol = $request->get('sort', 'seller_name');
        $sortDir = $request->get('dir', 'asc');
        if (!in_array($sortCol, ['seller_name', 'pi_count', 'total_machines', 'total_amount'])) {
            $sortCol = 'seller_name';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }
        $sellerData = $sellerData->sort(function ($a, $b) use ($sortCol, $sortDir) {
            $va = $sortCol === 'seller_name' ? ($a->seller->seller_name ?? '') : $a->{$sortCol};
            $vb = $sortCol === 'seller_name' ? ($b->seller->seller_name ?? '') : $b->{$sortCol};
            $cmp = is_numeric($va) && is_numeric($vb) ? $va <=> $vb : strcasecmp((string)$va, (string)$vb);
            return $sortDir === 'desc' ? -$cmp : $cmp;
        })->values();

        $perPage = 20;
        $page = $request->get('page', 1);
        $sellers = new \Illuminate\Pagination\LengthAwarePaginator(
            $sellerData->forPage($page, $perPage)->values(),
            $sellerData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $allSellers = Seller::orderBy('seller_name')->get(['id', 'seller_name']);

        return view('reports.sellers', compact('sellers', 'allSellers'));
    }

    /**
     * Export seller report as Excel or PDF.
     */
    public function exportSellers(Request $request)
    {
        $this->authorize('export reports');

        $piQuery = $this->proformaInvoicesQuery();
        $this->applyDateRange($piQuery, $request, 'proforma_invoices');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $this->applySearchPi($piQuery, $term);
            }
        }
        if ($request->filled('seller_id')) {
            $piQuery->where('seller_id', $request->seller_id);
        }
        $piQuery->whereNotNull('seller_id');

        $pis = $piQuery->with(['proformaInvoiceMachines', 'seller.country'])->get();
        $bySeller = $pis->groupBy('seller_id');
        $sellerIds = $bySeller->keys()->filter()->toArray();
        $sellersBase = Seller::with(['country'])->whereIn('id', $sellerIds)->orderBy('seller_name')->get()->keyBy('id');

        $sellerData = collect();
        foreach ($sellersBase as $seller) {
            $sellerPis = $bySeller->get($seller->id, collect());
            $sellerData->push((object)[
                'seller' => $seller,
                'pi_count' => $sellerPis->count(),
                'total_machines' => $sellerPis->sum(fn($pi) => $pi->proformaInvoiceMachines->sum('quantity')),
                'total_amount' => $sellerPis->sum('total_amount'),
                'currency' => $sellerPis->first()->currency ?? 'USD',
            ]);
        }

        $sortCol = $request->get('sort', 'seller_name');
        $sortDir = $request->get('dir', 'asc');
        if (!in_array($sortCol, ['seller_name', 'pi_count', 'total_machines', 'total_amount'])) {
            $sortCol = 'seller_name';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }
        $sellerData = $sellerData->sort(function ($a, $b) use ($sortCol, $sortDir) {
            $va = $sortCol === 'seller_name' ? ($a->seller->seller_name ?? '') : $a->{$sortCol};
            $vb = $sortCol === 'seller_name' ? ($b->seller->seller_name ?? '') : $b->{$sortCol};
            $cmp = is_numeric($va) && is_numeric($vb) ? $va <=> $vb : strcasecmp((string)$va, (string)$vb);
            return $sortDir === 'desc' ? -$cmp : $cmp;
        })->values();

        $format = $request->get('format', 'excel');
        if ($format === 'pdf') {
            $pdf = DomPDF::loadView('reports.sellers-pdf', compact('sellerData'));
            return $pdf->download('seller-report-' . now()->format('Y-m-d-His') . '.pdf');
        }
        return $this->exportSellersExcel($sellerData);
    }

    protected function exportSellersExcel($sellerData)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Seller Report');
        $headers = ['Seller Company', 'Email', 'Mobile', 'Address', 'Country', 'GST No', 'PI Short Name', 'PI Count', 'Total Machines', 'Total Amount'];
        foreach (range(0, count($headers) - 1) as $i) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $headers[$i]);
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');
        $row = 2;
        foreach ($sellerData as $item) {
            $s = $item->seller;
            $sheet->setCellValue('A' . $row, $s->seller_name ?? '—');
            $sheet->setCellValue('B' . $row, $s->email ?? '—');
            $sheet->setCellValue('C' . $row, $s->mobile ?? '—');
            $sheet->setCellValue('D' . $row, $s->address ?? '—');
            $sheet->setCellValue('E' . $row, $s->country->name ?? '—');
            $sheet->setCellValue('F' . $row, $s->gst_no ?? '—');
            $sheet->setCellValue('G' . $row, $s->pi_short_name ?? '—');
            $sheet->setCellValue('H' . $row, $item->pi_count);
            $sheet->setCellValue('I' . $row, $item->total_machines);
            $sheet->setCellValue('J' . $row, format_amount($item->total_amount, $item->currency));
            $row++;
        }
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'seller-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($temp);
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Seller ledger: full detail for one seller - company info, PIs, machines, prices.
     */
    public function sellerLedger(Seller $seller, Request $request)
    {
        $this->authorize('view reports');

        $piQuery = ProformaInvoice::with([
            'contract',
            'proformaInvoiceMachines.machineCategory',
            'proformaInvoiceMachines.brand',
            'proformaInvoiceMachines.machineModel',
        ])->where('seller_id', $seller->id);

        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !auth()->user()->can('view contract approvals')) {
            $teamMemberIds = User::where('created_by', auth()->id())->pluck('id')->toArray();
            $piQuery->whereHas('contract', function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())->orWhereIn('created_by', $teamMemberIds);
            });
        }

        $this->applyDateRange($piQuery, $request, 'proforma_invoices');
        if ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $like = '%' . $term . '%';
                $piQuery->where(function ($q) use ($like) {
                    $q->where('proforma_invoice_number', 'like', $like)
                        ->orWhere('buyer_company_name', 'like', $like)
                        ->orWhereHas('contract', fn($c) => $c->where('contract_number', 'like', $like)->orWhere('buyer_name', 'like', $like)->orWhere('company_name', 'like', $like));
                });
            }
        }

        $proformaInvoices = $piQuery->orderBy('created_at', 'desc')->get();

        $seller->load(['country', 'bankDetails']);

        return view('reports.seller-ledger', compact('seller', 'proformaInvoices'));
    }
}
