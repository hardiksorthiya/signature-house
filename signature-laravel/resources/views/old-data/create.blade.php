<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Add Old Data</h1>
            <p class="text-muted mb-0">Create a new old data entry with machine details.</p>
        </div>
        <a href="{{ route('old-data.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('old-data.store') }}">
        @csrf
        @php($submitLabel = 'Save Old Data')
        @include('old-data._form')
    </form>
</x-app-layout>
