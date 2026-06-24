<x-app-layout>
    <div class="mb-4">
        <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Reports</h1>
        <p class="text-muted mb-0 small">View and export reports</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.leads') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Leads Report</h2>
                            <p class="text-muted small mb-0">Filter, sort and export leads by date and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.contracts') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Contract Report</h2>
                            <p class="text-muted small mb-0">Filter, sort and export contracts by date and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.pi') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">PI Report</h2>
                            <p class="text-muted small mb-0">Filter, sort and export proforma invoices by date and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.po') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">PO Report</h2>
                            <p class="text-muted small mb-0">Filter, sort and export purchase orders by date and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.payments') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Payment Report</h2>
                            <p class="text-muted small mb-0">Filter, sort and export payments by date and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.customers') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-address-book text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Customer Report</h2>
                            <p class="text-muted small mb-0">Customer ledger: leads, contract, payments, PI, PO, MS unloading, complaints</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.sellers') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-store text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Seller Report</h2>
                            <p class="text-muted small mb-0">Seller company, machines sold, prices and full details per seller</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.complaints') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Complaints Report</h2>
                            <p class="text-muted small mb-0">Recurring, machine, date, area and engineer-wise completed complaints</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('reports.spare-used') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 8%, #ffffff) 100%); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                            <i class="fas fa-cogs text-white"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-semibold mb-1" style="color: #1f2937;">Spare Used Report</h2>
                            <p class="text-muted small mb-0">Filter and export spares used in complaints by date used and creator</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
