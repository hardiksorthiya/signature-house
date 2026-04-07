<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Proforma Invoice Layouts</h1>
            <p class="text-muted mb-0">Manage layouts for proforma invoices. Create and assign layouts to sellers.</p>
        </div>
        <a href="{{ route('pi-layouts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Layout
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($layouts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Default</th>
                                        <th>Sellers Using</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($layouts as $layout)
                                        <tr>
                                            <td class="fw-medium">{{ $layout->name }}</td>
                                            <td>{{ $layout->description ?? 'N/A' }}</td>
                                            <td>
                                                @if($layout->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($layout->is_default)
                                                    <span class="badge bg-primary">Default</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $layout->sellers_count ?? 0 }} Seller(s)</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('pi-layouts.show', $layout) }}" class="btn btn-sm btn-outline-info" title="View layout">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pi-layouts.edit', $layout) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('pi-layouts.destroy', $layout) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this layout?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No layouts found. Create your first layout to get started.</p>
                            <a href="{{ route('pi-layouts.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Create First Layout
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
