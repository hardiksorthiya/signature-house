<?php

namespace App\Http\Controllers;

use App\Models\MachineCategory;
use App\Models\Area;
use App\Models\City;
use App\Models\OldData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class OldDataController extends Controller
{
    private const MACHINE_MODELS = [
        'WL-808',
        'JQ Changshu',
        'JQ SuperCAM',
        'JQ Wumu',
        'JQ SNS',
        'JQ CFang',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $area = trim((string) $request->get('area', ''));

        $areas = OldData::query()
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->pluck('area')
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->sort()
            ->values();

        $oldData = OldData::query()
            ->withCount('machines')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('firm_name', 'like', $like)
                        ->orWhere('client_name', 'like', $like)
                        ->orWhere('phone_number_1', 'like', $like)
                        ->orWhere('phone_number_2', 'like', $like)
                        ->orWhere('city', 'like', $like)
                        ->orWhere('area', 'like', $like);
                });
            })
            ->when($area !== '', function ($query) use ($area) {
                $query->whereRaw('LOWER(TRIM(area)) = ?', [mb_strtolower($area)]);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('old-data.index', compact('oldData', 'search', 'areas', 'area'));
    }

    public function create()
    {
        $machineCategories = MachineCategory::orderBy('name')->get(['id', 'name']);
        $machineModels = self::MACHINE_MODELS;
        $cities = City::orderBy('name')->pluck('name')->values();
        $areas = Area::orderBy('name')->pluck('name')->values();

        return view('old-data.create', compact('machineCategories', 'machineModels', 'cities', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($validated) {
            $record = OldData::create([
                'firm_name' => $validated['firm_name'],
                'client_name' => $validated['client_name'],
                'phone_number_1' => $validated['phone_number_1'],
                'phone_number_2' => $validated['phone_number_2'] ?? null,
                'city' => $validated['city'] ?? null,
                'area' => $validated['area'] ?? null,
            ]);

            $record->machines()->createMany($this->normalizedMachines($validated['machines'] ?? []));
        });

        return redirect()->route('old-data.index')->with('success', 'Old data record created successfully.');
    }

    public function edit(OldData $oldDatum)
    {
        $oldDatum->load('machines');
        $machineCategories = MachineCategory::orderBy('name')->get(['id', 'name']);
        $machineModels = self::MACHINE_MODELS;
        $cities = City::orderBy('name')->pluck('name')->values();
        $areas = Area::orderBy('name')->pluck('name')->values();

        return view('old-data.edit', compact('oldDatum', 'machineCategories', 'machineModels', 'cities', 'areas'));
    }

    public function update(Request $request, OldData $oldDatum)
    {
        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($oldDatum, $validated) {
            $oldDatum->update([
                'firm_name' => $validated['firm_name'],
                'client_name' => $validated['client_name'],
                'phone_number_1' => $validated['phone_number_1'],
                'phone_number_2' => $validated['phone_number_2'] ?? null,
                'city' => $validated['city'] ?? null,
                'area' => $validated['area'] ?? null,
            ]);

            $oldDatum->machines()->delete();
            $oldDatum->machines()->createMany($this->normalizedMachines($validated['machines'] ?? []));
        });

        return redirect()->route('old-data.index')->with('success', 'Old data record updated successfully.');
    }

    public function destroy(OldData $oldDatum)
    {
        $oldDatum->delete();

        return redirect()->route('old-data.index')->with('success', 'Old data record deleted successfully.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Old Data');

        $headers = [
            'Firm Name',
            'Client Name',
            'Phone Number 1',
            'Phone Number 2',
            'City',
            'Area',
            'Machine Category',
            'Machine Model',
            'Serial Number',
            'Khata Number',
            'Date of Manufacturing',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $example = [
            'ABC Textiles',
            'Ramesh Patel',
            '9876543210',
            '',
            'Surat',
            'Ring Road',
            'Water Jet',
            'WL-808',
            'SN-001',
            'KH-101',
            date('Y-m-d'),
        ];

        $col = 'A';
        foreach ($example as $value) {
            $sheet->setCellValue($col . '2', $value);
            $col++;
        }

        $filename = 'old_data_import_template_' . date('Y-m-d') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'Please select an Excel file.',
            'file.mimes' => 'The file must be an Excel file (.xlsx or .xls).',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        if (count($rows) < 2) {
            return redirect()->route('old-data.index')->with('error', 'Excel file must contain at least one data row.');
        }

        $headerRow = $rows[0];
        $colMap = [];
        $expected = [
            'firm_name' => ['firm name', 'firm_name'],
            'client_name' => ['client name', 'client_name'],
            'phone_number_1' => ['phone number 1', 'phone 1', 'phone_number_1'],
            'phone_number_2' => ['phone number 2', 'phone 2', 'phone_number_2'],
            'city' => ['city'],
            'area' => ['area'],
            'machine_category' => ['machine category', 'machine_category'],
            'machine_model' => ['machine model', 'machine_model'],
            'serial_number' => ['serial number', 'serial_number'],
            'khata_number' => ['khata number', 'khata_number'],
            'date_of_manufacturing' => ['date of manufacturing', 'date_of_manufacturing'],
        ];

        foreach ($headerRow as $colIndex => $val) {
            $v = trim(strtolower((string) $val));
            foreach ($expected as $key => $aliases) {
                if (in_array($v, $aliases, true)) {
                    $colMap[$key] = $colIndex;
                    break;
                }
            }
        }

        if (empty($colMap)) {
            return redirect()->route('old-data.index')
                ->with('error', 'Excel must contain at least one supported old data column.');
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNum = $i + 1;

                $firmName = trim((string) ($row[$colMap['firm_name'] ?? -1] ?? ''));
                $clientName = trim((string) ($row[$colMap['client_name'] ?? -1] ?? ''));
                $phone1 = trim((string) ($row[$colMap['phone_number_1'] ?? -1] ?? ''));
                $phone2 = trim((string) ($row[$colMap['phone_number_2'] ?? -1] ?? ''));
                $city = trim((string) ($row[$colMap['city'] ?? -1] ?? ''));
                $area = trim((string) ($row[$colMap['area'] ?? -1] ?? ''));

                $machineCategoryName = trim((string) ($row[$colMap['machine_category'] ?? -1] ?? ''));
                $machineModel = trim((string) ($row[$colMap['machine_model'] ?? -1] ?? ''));
                $serialNumber = trim((string) ($row[$colMap['serial_number'] ?? -1] ?? ''));
                $khataNumber = trim((string) ($row[$colMap['khata_number'] ?? -1] ?? ''));
                $dom = trim((string) ($row[$colMap['date_of_manufacturing'] ?? -1] ?? ''));

                if (
                    $firmName === '' &&
                    $clientName === '' &&
                    $phone1 === '' &&
                    $phone2 === '' &&
                    $city === '' &&
                    $area === '' &&
                    $machineCategoryName === '' &&
                    $machineModel === '' &&
                    $serialNumber === '' &&
                    $khataNumber === '' &&
                    $dom === ''
                ) {
                    continue;
                }

                $oldData = OldData::create([
                    'firm_name' => $firmName,
                    'client_name' => $clientName,
                    'phone_number_1' => $phone1,
                    'phone_number_2' => $phone2 !== '' ? $phone2 : null,
                    'city' => $city !== '' ? $city : null,
                    'area' => $area !== '' ? $area : null,
                ]);

                if ($machineCategoryName !== '' || $machineModel !== '' || $serialNumber !== '' || $khataNumber !== '' || $dom !== '') {
                    $categoryId = null;
                    if ($machineCategoryName !== '') {
                        $categoryId = MachineCategory::where('name', 'like', $machineCategoryName)->value('id');
                    }

                    $parsedDom = null;
                    if ($dom !== '') {
                        $timestamp = strtotime($dom);
                        $parsedDom = $timestamp !== false ? date('Y-m-d', $timestamp) : null;
                    }

                    $oldData->machines()->create([
                        'machine_category_id' => $categoryId,
                        'machine_model' => $machineModel !== '' ? $machineModel : null,
                        'serial_number' => $serialNumber !== '' ? $serialNumber : null,
                        'khata_number' => $khataNumber !== '' ? $khataNumber : null,
                        'date_of_manufacturing' => $parsedDom,
                    ]);
                }

                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('old-data.index')->with('error', 'Import failed: ' . $e->getMessage());
        }

        $msg = "{$imported} old data row(s) imported successfully.";
        if ($skipped > 0) {
            $msg .= " {$skipped} row(s) skipped.";
        }

        return redirect()->route('old-data.index')
            ->with('success', $msg)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'firm_name' => ['required', 'string', 'max:255'],
            'client_name' => ['required', 'string', 'max:255'],
            'phone_number_1' => ['required', 'string', 'max:30'],
            'phone_number_2' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'machines' => ['nullable', 'array'],
            'machines.*.machine_category_id' => ['nullable', 'exists:machine_categories,id'],
            'machines.*.machine_model' => ['nullable', 'string', 'max:255'],
            'machines.*.serial_number' => ['nullable', 'string', 'max:255'],
            'machines.*.khata_number' => ['nullable', 'string', 'max:255'],
            'machines.*.date_of_manufacturing' => ['nullable', 'date'],
        ]);
    }

    private function normalizedMachines(array $machines): array
    {
        $normalized = [];

        foreach ($machines as $machine) {
            $categoryId = $machine['machine_category_id'] ?? null;
            $model = isset($machine['machine_model']) ? trim((string) $machine['machine_model']) : '';
            $serial = isset($machine['serial_number']) ? trim((string) $machine['serial_number']) : '';
            $khata = isset($machine['khata_number']) ? trim((string) $machine['khata_number']) : '';
            $dom = $machine['date_of_manufacturing'] ?? null;

            // Skip fully empty rows.
            if (empty($categoryId) && $model === '' && $serial === '' && $khata === '' && empty($dom)) {
                continue;
            }

            $normalized[] = [
                'machine_category_id' => $categoryId ?: null,
                'machine_model' => $model !== '' ? $model : null,
                'serial_number' => $serial !== '' ? $serial : null,
                'khata_number' => $khata !== '' ? $khata : null,
                'date_of_manufacturing' => !empty($dom) ? $dom : null,
            ];
        }

        return $normalized;
    }
}
