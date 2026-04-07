<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Layout Preview: {{ $piLayout->name }}</h1>
            <p class="text-muted mb-0">Preview with sample data</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('settings.pi-layouts') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Layouts
            </a>
        </div>
    </div>

    <!-- Layout Preview Only -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div style="overflow: hidden;">
                <iframe 
                    src="{{ route('pi-layouts.preview', $piLayout) }}"
                    style="width: 100%; height: 900px; border: none; display: block;"
                    title="Layout Preview"
                    frameborder="0"
                ></iframe>
            </div>
        </div>
    </div>
</x-app-layout>
