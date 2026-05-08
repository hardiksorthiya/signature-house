<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Old Data</h1>
            <p class="text-muted mb-0">Update old data and machine details.</p>
        </div>
        <a href="{{ route('old-data.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('old-data.update', $oldDatum) }}">
        @csrf
        @method('PUT')
        @php($submitLabel = 'Update Old Data')
        @include('old-data._form')
    </form>
</x-app-layout>
