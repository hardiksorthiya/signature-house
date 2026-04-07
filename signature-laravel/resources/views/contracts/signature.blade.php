<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Contract Signature</h1>
            <p class="text-muted mb-0">Contract: {{ $contract->contract_number }} - {{ $contract->buyer_name }}</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Contracts
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-signature text-white"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Customer Signature</h2>
                            <small class="text-muted">Please sign below to submit the contract for approval</small>
                        </div>
                    </div>

                    @if($contract->customer_signature)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            This contract has already been signed. Status: 
                            <strong>
                                @if($contract->approval_status === 'pending')
                                    <span class="badge bg-warning">Pending Approval</span>
                                @elseif($contract->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @if($contract->approver)
                                        <small class="ms-2">by {{ $contract->approver->name }} on {{ $contract->approved_at->format('M d, Y') }}</small>
                                    @endif
                                @elseif($contract->approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @if($contract->approver)
                                        <small class="ms-2">by {{ $contract->approver->name }} on {{ $contract->approved_at->format('M d, Y') }}</small>
                                    @endif
                                @endif
                            </strong>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium mb-3" style="color: #374151;">
                                <i class="fas fa-signature me-2"></i>Customer Signature
                            </label>
                            <div class="border rounded p-3" style="border-color: #e5e7eb !important; background: white;">
                                <img src="{{ $contract->customer_signature }}" alt="Customer Signature" style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                        @if($contract->approval_notes)
                            <div class="mb-4">
                                <label class="form-label fw-medium mb-2" style="color: #374151;">
                                    <i class="fas fa-comment me-2"></i>Approval Notes
                                </label>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0">{{ $contract->approval_notes }}</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <form action="{{ route('contracts.store-signature', $contract) }}" method="POST" id="signatureForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium mb-3" style="color: #374151;">
                                    <i class="fas fa-pen me-2"></i>Sign Here
                                </label>
                                <div class="border rounded" style="border-color: #e5e7eb !important; background: white;">
                                    <canvas id="signaturePad" width="800" height="300" style="width: 100%; height: 300px; cursor: crosshair; touch-action: none;"></canvas>
                                </div>
                                <input type="hidden" name="signature" id="signatureInput">
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Draw your signature in the box above</small>
                                    <button type="button" id="clearSignature" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i>Clear
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Important:</strong> By signing this contract, you agree to all terms and conditions. This contract will be submitted for approval.
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium" id="submitBtn">
                                        <i class="fas fa-check me-2"></i>Sign & Submit for Approval
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$contract->customer_signature)
    <!-- Signature Pad Library -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.9/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signaturePad');
            if (!canvas) return;

            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1,
                maxWidth: 3,
            });

            // Adjust canvas size
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Clear signature
            const clearBtn = document.getElementById('clearSignature');
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    signaturePad.clear();
                });
            }

            // Form submission
            const signatureForm = document.getElementById('signatureForm');
            if (signatureForm) {
                signatureForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (signaturePad.isEmpty()) {
                        alert('Please sign the contract before submitting.');
                        return;
                    }

                    // Convert signature to base64
                    const signatureData = signaturePad.toDataURL('image/png');
                    document.getElementById('signatureInput').value = signatureData;
                    
                    // Submit form
                    this.submit();
                });
            }
        });
    </script>
    @endif

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>
