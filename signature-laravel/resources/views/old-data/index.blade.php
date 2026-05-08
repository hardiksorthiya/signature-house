<x-app-layout>
    <div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Old Data</h1>
            <p class="text-muted mb-0">Manage firm and client historical machine records.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @can('create old data')
            <a href="{{ route('old-data.download-template') }}" class="btn btn-outline-secondary">
                <i class="fas fa-download me-2"></i>Excel Template
            </a>
            <form method="POST" action="{{ route('old-data.import') }}" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" class="form-control form-control-sm" style="max-width: 220px;" required>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-file-import me-2"></i>Upload Excel
                </button>
            </form>
            <a href="{{ route('old-data.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Old Data
            </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('import_errors'))
        <div class="alert alert-warning">
            <strong>Import warnings:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $importError)
                    <li>{{ $importError }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-database text-white small"></i>
                        </div>
                        <div>
                            <h5 class="h5 mb-0 fw-semibold" style="color: #1f2937;">Old Data List</h5>
                            <div class="text-muted small">{{ $oldData->total() }} Total</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                        <form method="GET" action="{{ route('old-data.index') }}"
                              class="d-flex align-items-center gap-2 flex-wrap justify-content-end w-100 old-data-filter-form">
                            <div class="d-flex align-items-center gap-2 flex-grow-1 old-data-search-group">
                                <input type="text"
                                       name="search"
                                       value="{{ $search }}"
                                       class="form-control form-control-sm"
                                       placeholder="Search firm, client, phone..."
                                       style="border-radius: 10px; border: 1px solid #e5e7eb; height: 40px; min-width: 0;">

                                <button type="submit"
                                        class="btn btn-danger d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="border-radius: 10px; width: 44px; height: 40px; min-width: 44px;"
                                        title="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <div class="d-flex align-items-center gap-2 old-data-right-group">
                                <select name="area"
                                        class="form-select form-select-sm flex-shrink-0"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; height: 40px; min-width: 170px;">
                                    <option value="">All Areas</option>
                                    @foreach($areas as $a)
                                        <option value="{{ $a }}" {{ $area === $a ? 'selected' : '' }}>{{ $a }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                        class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="border-radius: 8px; width: 44px; height: 40px; min-width: 44px;"
                                        title="Apply Filter">
                                    <i class="fas fa-check"></i>
                                    <span class="d-inline d-md-none ms-2">Apply</span>
                                </button>

                                @if($search !== '' || $area !== '')
                                    <a href="{{ route('old-data.index') }}"
                                       class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                                       style="border-radius: 8px; height: 40px; min-width: 86px;">
                                        <i class="fas fa-times me-2"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <style>
                .old-data-filter-form {
                    gap: 10px !important;
                }
                .old-data-search-group {
                    min-width: 300px;
                    max-width: 520px;
                }
                .old-data-right-group {
                    flex-wrap: nowrap;
                    justify-content: flex-end;
                    max-width: 100%;
                }
                .old-data-right-group select {
                    min-width: 170px;
                    max-width: 100%;
                }
                @media (max-width: 1199.98px) {
                    .old-data-filter-form {
                        align-items: center !important;
                    }
                    .old-data-search-group {
                        min-width: 260px;
                        max-width: 100%;
                    }
                    .old-data-right-group {
                        width: auto;
                        justify-content: flex-end;
                    }
                }
                @media (max-width: 991.98px) {
                    .old-data-search-group {
                        min-width: 240px;
                        max-width: 100%;
                    }
                }
                @media (max-width: 767.98px) {
                    .old-data-filter-form {
                        flex-direction: column;
                        align-items: stretch !important;
                    }
                    .old-data-search-group,
                    .old-data-right-group {
                        width: 100%;
                    }
                    .old-data-right-group {
                        display: grid !important;
                        grid-template-columns: 1fr auto;
                    }
                    .old-data-right-group .btn {
                        white-space: nowrap;
                    }
                    .old-data-right-group button[type="submit"] {
                        width: auto !important;
                        min-width: 92px !important;
                    }
                }
            </style>

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="px-3 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                            <th class="small fw-semibold" style="color: var(--primary-color) !important;">Firm Name</th>
                            <th class="small fw-semibold" style="color: var(--primary-color) !important;">Client Name</th>
                            <th class="small fw-semibold" style="color: var(--primary-color) !important;">Phone 1</th>
                            <th class="d-none d-md-table-cell small fw-semibold" style="color: var(--primary-color) !important;">Phone 2</th>
                            <th class="small fw-semibold" style="color: var(--primary-color) !important;">Area</th>
                            <th class="d-none d-md-table-cell text-center small fw-semibold" style="color: var(--primary-color) !important;">Machines</th>
                            <th class="text-center small fw-semibold" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($oldData as $row)
                            <tr>
                                <td class="px-3">{{ $oldData->firstItem() + $loop->index }}</td>
                                <td>{{ $row->firm_name }}</td>
                                <td>{{ $row->client_name }}</td>
                                <td>{{ $row->phone_number_1 }}</td>
                                <td class="d-none d-md-table-cell">{{ $row->phone_number_2 ?: '—' }}</td>
                                <td>{{ $row->area ?: '—' }}</td>
                                <td class="d-none d-md-table-cell text-center">{{ $row->machines_count }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        @can('edit old data')
                                        <a href="{{ route('old-data.edit', $row) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete old data')
                                        <form method="POST" action="{{ route('old-data.destroy', $row) }}" onsubmit="return confirm('Delete this old data record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No old data records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $oldData->links() }}
        </div>
    </div>
</x-app-layout>
