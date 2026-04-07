<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Settings') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                            @if (session('success'))
                                <div class="alert alert-success mb-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">Branding</h5>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Logo</label>
                                                    <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                                    @error('logo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    @if ($setting->logo)
                                                        <div class="mt-3">
                                                            <p class="text-muted mb-1">Current logo:</p>
                                                            <img src="{{ Storage::url($setting->logo) }}" alt="Logo" class="img-fluid rounded" style="max-height: 120px;">
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Favicon</label>
                                                    <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/*">
                                                    @error('favicon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    @if ($setting->favicon)
                                                        <div class="mt-3">
                                                            <p class="text-muted mb-1">Current favicon:</p>
                                                            <img src="{{ Storage::url($setting->favicon) }}" alt="Favicon" class="img-fluid rounded" style="max-height: 64px;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">Theme Colors</h5>

                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <label class="form-label fw-medium">Primary Color</label>
                                                        <input type="color" name="primary_color" value="{{ old('primary_color', $setting->primary_color) }}" class="form-control form-control-color w-100 @error('primary_color') is-invalid @enderror" title="Pick a primary color">
                                                        @error('primary_color')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <label class="form-label fw-medium">Secondary Color</label>
                                                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color) }}" class="form-control form-control-color w-100 @error('secondary_color') is-invalid @enderror" title="Pick a secondary color">
                                                        @error('secondary_color')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-muted mb-2">Preview</p>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="flex-grow-1" style="height: 52px; border-radius: 10px; background: linear-gradient(135deg, {{ $setting->primary_color }} 0%, {{ $setting->secondary_color }} 100%); box-shadow: 0 4px 8px rgba(0,0,0,0.08);"></div>
                                                        <span class="badge bg-primary  ">Buttons</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>
                                        Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
