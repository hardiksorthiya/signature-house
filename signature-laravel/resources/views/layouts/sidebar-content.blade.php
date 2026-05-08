<!-- Logo Section -->
<div class="p-3 p-lg-4 border-bottom bg-white sidebar-logo-section">
    <div class="d-flex align-items-center justify-content-center">
        @if(!empty($logoPath))
            <img 
                src="{{ $logoPath }}" 
                alt="Logo" 
                class="sidebar-logo">
        @else
            <div class="sidebar-logo-placeholder">
                <i class="fas fa-tshirt text-white"></i>
            </div>
        @endif
    </div>
</div>

<!-- Navigation Menu -->
<nav class="flex-grow-1 p-2 p-lg-3 overflow-auto sidebar-nav">
    <!-- Dashboard -->
    <a 
        href="{{ route('dashboard') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}"
        x-bind:title="(typeof $parent !== 'undefined' && $parent.sidebarOpen === false) || (typeof sidebarOpen !== 'undefined' && sidebarOpen === false) ? 'Dashboard' : ''">
        <i class="fas fa-chart-line me-3 sidebar-icon sidebar-icon-only"></i>
        <span class="fw-medium sidebar-text">Dashboard</span>
    </a>

    @can('view leads')
    @php
        $leadsChildrenCount = 0;
        if (auth()->user()->can('view leads')) $leadsChildrenCount++;
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) $leadsChildrenCount += 5;
    @endphp
    @if($leadsChildrenCount == 1)
        @can('view leads')
        <a 
            href="{{ route('leads.index') }}" 
            class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('leads.*') ? 'sidebar-active' : '' }}"
            x-bind:title="(typeof $parent !== 'undefined' && $parent.sidebarOpen === false) || (typeof sidebarOpen !== 'undefined' && sidebarOpen === false) ? 'Leads' : ''">
            <i class="fas fa-user-friends me-3 sidebar-icon sidebar-icon-only"></i>
            <span class="fw-medium sidebar-text">Leads</span>
        </a>
        @endcan
    @else
        <div x-data="{ open: {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'true' : 'false' }} }">
            <button 
                @click="open = !open" 
                class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('leads.*') || request()->routeIs('businesses.*') || request()->routeIs('states.*') || request()->routeIs('cities.*') || request()->routeIs('areas.*') || request()->routeIs('statuses.*') ? 'sidebar-active' : '' }}"
                x-bind:title="(typeof $parent !== 'undefined' && $parent.sidebarOpen === false) ? 'Leads' : ''">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-friends me-3 sidebar-icon sidebar-icon-only"></i>
                    <span class="fw-medium sidebar-text">Leads</span>
                </div>
                <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open && (typeof $parent !== 'undefined' ? $parent.sidebarOpen : true)" x-collapse class="ms-4">
                @can('view leads')
                <a 
                    href="{{ route('leads.index') }}" 
                    class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('leads.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-list me-2 sidebar-icon-small"></i>
                    <span>List Leads</span>
                </a>
                @endcan
                @hasrole('Admin|Super Admin')
                <a href="{{ route('businesses.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('businesses.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-building me-2 sidebar-icon-small"></i>
                    <span>Business</span>
                </a>
                <a href="{{ route('states.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('states.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-map me-2 sidebar-icon-small"></i>
                    <span>State</span>
                </a>
                <a href="{{ route('cities.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('cities.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-city me-2 sidebar-icon-small"></i>
                    <span>City</span>
                </a>
                <a href="{{ route('areas.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('areas.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-map-marker-alt me-2 sidebar-icon-small"></i>
                    <span>Area</span>
                </a>
                <a href="{{ route('statuses.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('statuses.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-info-circle me-2 sidebar-icon-small"></i>
                    <span>Status</span>
                </a>
                @endhasrole
            </div>
        </div>
    @endif
    @endcan

    @canany(['view contract approvals', 'approve contracts', 'reject contracts'])
    <a 
        href="{{ route('contracts.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('contracts.index') || request()->routeIs('contracts.edit') || request()->routeIs('contracts.signature') ? 'sidebar-active' : '' }}">
        <i class="fas fa-file-contract me-3 sidebar-icon"></i>
        <span class="fw-medium">Contracts</span>
    </a>
    @endcanany

    @can('view customers')
    <a 
        href="{{ route('customers.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('customers.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-user-check me-3 sidebar-icon"></i>
        <span class="fw-medium">Customers</span>
    </a>
    @endcan

    @canany(['create proforma invoices', 'view proforma invoices', 'view purchase order', 'view over invoice', 'view delivery detail', 'view status'])
    @php
        $saleChildrenCount = 0;
        $saleSingleItem = null;
        $saleSingleRoute = null;
        $saleSingleIcon = null;
        if (auth()->user()->can('create proforma invoices')) $saleChildrenCount++;
        if (auth()->user()->can('view proforma invoices')) $saleChildrenCount++;
        if (auth()->user()->can('view purchase order')) $saleChildrenCount++;
        if (auth()->user()->can('view over invoice')) $saleChildrenCount++;
        if (auth()->user()->can('view delivery detail')) $saleChildrenCount++;
        if (auth()->user()->can('view status')) $saleChildrenCount++;
        
        if ($saleChildrenCount == 1) {
            if (auth()->user()->can('view proforma invoices')) {
                $saleSingleItem = 'PI List';
                $saleSingleRoute = route('proforma-invoices.index');
                $saleSingleIcon = 'fa-list';
            } elseif (auth()->user()->can('view purchase order')) {
                $saleSingleItem = 'Purchase Order';
                $saleSingleRoute = route('purchase-orders.index');
                $saleSingleIcon = 'fa-shopping-bag';
            } elseif (auth()->user()->can('view over invoice')) {
                $saleSingleItem = 'Over Invoice';
                $saleSingleRoute = route('contracts.over-invoice');
                $saleSingleIcon = 'fa-exclamation-triangle';
            } elseif (auth()->user()->can('view delivery detail')) {
                $saleSingleItem = 'Delivery Details';
                $saleSingleRoute = route('proforma-invoices.delivery-details-index');
                $saleSingleIcon = 'fa-truck';
            } elseif (auth()->user()->can('view status')) {
                $saleSingleItem = 'Status';
                $saleSingleRoute = route('machine-statuses.index');
                $saleSingleIcon = 'fa-tasks';
            }
        }
    @endphp
    @if($saleChildrenCount == 1 && $saleSingleItem)
        <a 
            href="{{ $saleSingleRoute }}" 
            class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->url() === $saleSingleRoute || request()->routeIs('proforma-invoices.*') || request()->routeIs('purchase-orders.*') || request()->routeIs('contracts.over-invoice*') || request()->routeIs('machine-statuses.*') ? 'sidebar-active' : '' }}">
            <i class="fas {{ $saleSingleIcon }} me-3 sidebar-icon"></i>
            <span class="fw-medium">{{ $saleSingleItem }}</span>
        </a>
    @else
        <div x-data="{ open: {{ request()->routeIs('proforma-invoices.*') || request()->routeIs('purchase-orders.*') || request()->routeIs('contracts.over-invoice*') || request()->routeIs('proforma-invoices.delivery-details-index') || request()->routeIs('proforma-invoices.delivery-details') || request()->routeIs('proforma-invoices.delivery-details-view') || request()->routeIs('machine-statuses.*') ? 'true' : 'false' }} }">
            <button 
                @click="open = !open" 
                class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('proforma-invoices.*') || request()->routeIs('purchase-orders.*') || request()->routeIs('contracts.over-invoice*') || request()->routeIs('proforma-invoices.delivery-details-index') || request()->routeIs('proforma-invoices.delivery-details') || request()->routeIs('proforma-invoices.delivery-details-view') || request()->routeIs('machine-statuses.*') ? 'sidebar-active' : '' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-shopping-cart me-3 sidebar-icon"></i>
                    <span class="fw-medium">Sale</span>
                </div>
                <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-collapse class="ms-4">
                
                @can('view proforma invoices')
                <a href="{{ route('proforma-invoices.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('proforma-invoices.index') || request()->routeIs('proforma-invoices.show') || request()->routeIs('proforma-invoices.edit') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-list me-2 sidebar-icon-small"></i>
                    <span>PI List</span>
                </a>
                @endcan
                @can('view purchase order')
                <a href="{{ route('purchase-orders.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('purchase-orders.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-shopping-bag me-2 sidebar-icon-small"></i>
                    <span>Purchase Order</span>
                </a>
                @endcan
                @can('view over invoice')
                <a href="{{ route('contracts.over-invoice') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('contracts.over-invoice*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-exclamation-triangle me-2 sidebar-icon-small"></i>
                    <span>Over Invoice</span>
                </a>
                @endcan
                @can('view delivery detail')
                <a href="{{ route('proforma-invoices.delivery-details-index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('proforma-invoices.delivery-details-index') || request()->routeIs('proforma-invoices.delivery-details') || request()->routeIs('proforma-invoices.delivery-details-view') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-truck me-2 sidebar-icon-small"></i>
                    <span>Delivery Details</span>
                </a>
                @endcan
                @can('view status')
                <a href="{{ route('machine-statuses.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-statuses.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-tasks me-2 sidebar-icon-small"></i>
                    <span>Status</span>
                </a>
                @endcan
            </div>
        </div>
    @endif
    @endcanany

    @can('view payment')
    <a 
        href="{{ route('payments.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('payments.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-money-bill-wave me-3 sidebar-icon"></i>
        <span class="fw-medium">Payment</span>
    </a>
    @endcan

    @canany(['view pre erection', 'view image uploading', 'view damage', 'view serial number', 'view machine erection', 'view ia fitting', 'view spare list'])
    @php
        $msUnloadingChildrenCount = 0;
        $msUnloadingSingleItem = null;
        $msUnloadingSingleRoute = null;
        $msUnloadingSingleIcon = null;
        
        if (auth()->user()->can('view pre erection')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view image uploading')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view damage')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view serial number')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view machine erection')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view ia fitting')) $msUnloadingChildrenCount++;
        if (auth()->user()->can('view spare list')) $msUnloadingChildrenCount++;
        
        if ($msUnloadingChildrenCount == 1) {
            if (auth()->user()->can('view pre erection')) {
                $msUnloadingSingleItem = 'Pre Errection';
                $msUnloadingSingleRoute = route('pre-erection.index');
                $msUnloadingSingleIcon = 'fa-tools';
            } elseif (auth()->user()->can('view image uploading')) {
                $msUnloadingSingleItem = 'Image Uploading';
                $msUnloadingSingleRoute = route('ms-unloading-images.index');
                $msUnloadingSingleIcon = 'fa-images';
            } elseif (auth()->user()->can('view damage')) {
                $msUnloadingSingleItem = 'Damage';
                $msUnloadingSingleRoute = route('damage-details.index');
                $msUnloadingSingleIcon = 'fa-exclamation-triangle';
            } elseif (auth()->user()->can('view serial number')) {
                $msUnloadingSingleItem = 'Serial Number';
                $msUnloadingSingleRoute = route('serial-numbers.index');
                $msUnloadingSingleIcon = 'fa-hashtag';
            } elseif (auth()->user()->can('view machine erection')) {
                $msUnloadingSingleItem = 'Machine Erection';
                $msUnloadingSingleRoute = route('machine-erection.index');
                $msUnloadingSingleIcon = 'fa-cogs';
            } elseif (auth()->user()->can('view ia fitting')) {
                $msUnloadingSingleItem = 'IA Fitting';
                $msUnloadingSingleRoute = route('ia-fitting.index');
                $msUnloadingSingleIcon = 'fa-wrench';
            } elseif (auth()->user()->can('view spare list')) {
                $msUnloadingSingleItem = 'Spare List';
                $msUnloadingSingleRoute = route('ms-unloading-spare-list.index');
                $msUnloadingSingleIcon = 'fa-list-alt';
            }
        }
    @endphp
    @if($msUnloadingChildrenCount == 1 && $msUnloadingSingleItem)
        <a 
            href="{{ $msUnloadingSingleRoute }}" 
            class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('pre-erection.*') || request()->routeIs('ms-unloading-images.*') || request()->routeIs('damage-details.*') || request()->routeIs('serial-numbers.*') || request()->routeIs('machine-erection.*') || request()->routeIs('ia-fitting.*') || request()->routeIs('ms-unloading-spare-list.*') ? 'sidebar-active' : '' }}">
            <i class="fas {{ $msUnloadingSingleIcon }} me-3 sidebar-icon"></i>
            <span class="fw-medium">{{ $msUnloadingSingleItem }}</span>
        </a>
    @else
        <div x-data="{ open: {{ request()->routeIs('pre-erection.*') || request()->routeIs('ms-unloading-images.*') || request()->routeIs('damage-details.*') || request()->routeIs('serial-numbers.*') || request()->routeIs('machine-erection.*') || request()->routeIs('ia-fitting.*') || request()->routeIs('ms-unloading-spare-list.*') ? 'true' : 'false' }} }">
            <button 
                @click="open = !open" 
                class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('pre-erection.*') || request()->routeIs('ms-unloading-images.*') || request()->routeIs('damage-details.*') || request()->routeIs('serial-numbers.*') || request()->routeIs('machine-erection.*') || request()->routeIs('ia-fitting.*') || request()->routeIs('ms-unloading-spare-list.*') ? 'sidebar-active' : '' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-truck-loading me-3 sidebar-icon"></i>
                    <span class="fw-medium text-start">MS Unloading</span>
                </div>
                <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-collapse class="ms-4">
                @can('view pre erection')
                <a href="{{ route('pre-erection.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('pre-erection.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-tools me-2 sidebar-icon-small"></i>
                    <span>Pre Errection</span>
                </a>
                @endcan
                @can('view image uploading')
                <a href="{{ route('ms-unloading-images.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('ms-unloading-images.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-images me-2 sidebar-icon-small"></i>
                    <span>Image Uploading</span>
                </a>
                @endcan
                @can('view damage')
                <a href="{{ route('damage-details.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('damage-details.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-exclamation-triangle me-2 sidebar-icon-small"></i>
                    <span>Damage</span>
                </a>
                @endcan
                @can('view serial number')
                <a href="{{ route('serial-numbers.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('serial-numbers.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-hashtag me-2 sidebar-icon-small"></i>
                    <span>Serial Number</span>
                </a>
                @endcan
                @can('view machine erection')
                <a href="{{ route('machine-erection.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-erection.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-cogs me-2 sidebar-icon-small"></i>
                    <span>Machine Erection</span>
                </a>
                @endcan
                @can('view ia fitting')
                <a href="{{ route('ia-fitting.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('ia-fitting.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-wrench me-2 sidebar-icon-small"></i>
                    <span>IA Fitting</span>
                </a>
                @endcan
                @can('view spare list')
                <a href="{{ route('ms-unloading-spare-list.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('ms-unloading-spare-list.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-list-alt me-2 sidebar-icon-small"></i>
                    <span>Spare List</span>
                </a>
                @endcan
            </div>
        </div>
    @endif
    @endcanany

  

    @can('view spare')
    <a 
        href="{{ route('spares.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('spares.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-puzzle-piece me-3 sidebar-icon"></i>
        <span class="fw-medium">Spare</span>
    </a>
    @endcan

    @can('view reports')
    <a 
        href="{{ route('reports.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-chart-bar me-3 sidebar-icon"></i>
        <span class="fw-medium">Reports</span>
    </a>
    @endcan

    @can('view employee location')
    <a 
        href="{{ route('employee-location.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('employee-location.index') || request()->routeIs('employee-location.locations') ? 'sidebar-active' : '' }}">
        <i class="fas fa-map-marker-alt me-3 sidebar-icon"></i>
        <span class="fw-medium">Employee Location</span>
    </a>
    @endcan

    <a 
        href="{{ route('employee-location.share') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('employee-location.share') ? 'sidebar-active' : '' }}">
        <i class="fas fa-location-crosshairs me-3 sidebar-icon"></i>
        <span class="fw-medium">Share my location</span>
    </a>

    @can('view task')
    <a 
        href="{{ route('tasks.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('tasks.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-tasks me-3 sidebar-icon"></i>
        <span class="fw-medium">Tasks</span>
    </a>
    @endcan

    @can('view old data')
    <a
        href="{{ route('old-data.index') }}"
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('old-data.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-database me-3 sidebar-icon"></i>
        <span class="fw-medium">Old Data</span>
    </a>
    @endcan

    @can('view complain')
    <a 
        href="{{ route('complaints.index') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('complaints.*') ? 'sidebar-active' : '' }}">
        <i class="fas fa-exclamation-triangle me-3 sidebar-icon"></i>
        <span class="fw-medium">Complain</span>
    </a>
    @endcan

    @can('view contract approvals')
    <a 
        href="{{ route('contracts.pending-approval') }}" 
        class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('contracts.pending-approval') || request()->routeIs('contracts.approve') || request()->routeIs('contracts.reject') ? 'sidebar-active' : '' }}">
        <i class="fas fa-check-circle me-3 sidebar-icon"></i>
        <span class="fw-medium">Contract Approvals</span>
        @php
            $pendingCountQuery = \App\Models\Contract::where('approval_status', 'pending')
                ->whereNotNull('customer_signature');

            // Keep sidebar badge aligned with role-based visibility.
            if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
                $pendingCountQuery->where('created_by', auth()->id());
            }

            $pendingCount = $pendingCountQuery->count();
        @endphp
        @if($pendingCount > 0)
            <span class="badge bg-primary ms-auto">{{ $pendingCount }}</span>
        @endif
    </a>
    @endcan

    @canany(['view users', 'create users', 'edit users', 'delete users', 'view roles', 'create roles', 'edit roles', 'delete roles'])
    @php
        $teamChildrenCount = 0;
        $teamSingleItem = null;
        $teamSingleRoute = null;
        $teamSingleIcon = null;
        
        if (auth()->user()->can('view users')) $teamChildrenCount++;
        if (auth()->user()->canany(['view roles', 'create roles', 'edit roles', 'delete roles'])) $teamChildrenCount++;
        
        if ($teamChildrenCount == 1) {
            if (auth()->user()->can('view users')) {
                $teamSingleItem = 'Team List';
                $teamSingleRoute = route('users.index');
                $teamSingleIcon = 'fa-list';
            } elseif (auth()->user()->canany(['view roles', 'create roles', 'edit roles', 'delete roles'])) {
                $teamSingleItem = 'Role Create';
                $teamSingleRoute = route('roles.create');
                $teamSingleIcon = 'fa-user-tag';
            }
        }
    @endphp
    @if($teamChildrenCount == 1 && $teamSingleItem)
        <a 
            href="{{ $teamSingleRoute }}" 
            class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
            <i class="fas {{ $teamSingleIcon }} me-3 sidebar-icon"></i>
            <span class="fw-medium">{{ $teamSingleItem }}</span>
        </a>
    @else
        <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
            <button 
                @click="open = !open" 
                class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users me-3 sidebar-icon"></i>
                    <span class="fw-medium">Team</span>
                </div>
                <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-collapse class="ms-4">
                @can('view users')
                <a href="{{ route('users.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('users.*') && !request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-list me-2 sidebar-icon-small"></i>
                    <span>Team List</span>
                </a>
                @endcan
                @canany(['view roles', 'create roles', 'edit roles', 'delete roles'])
                <a href="{{ route('roles.create') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('roles.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-user-tag me-2 sidebar-icon-small"></i>
                    <span>Role Create</span>
                </a>
                @endcanany
            </div>
        </div>
    @endif
    @endcanany

    @hasrole('Admin|Super Admin')
    <div x-data="{ open: {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') || request()->routeIs('delivery-terms.*') || request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'true' : 'false' }} }">
        <button 
            @click="open = !open" 
            class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('machine-categories.*') || request()->routeIs('sellers.*') || request()->routeIs('countries.*') || request()->routeIs('brands.*') || request()->routeIs('machine-models.*') || request()->routeIs('machine-sizes.*') || request()->routeIs('flange-sizes.*') || request()->routeIs('feeders.*') || request()->routeIs('machine-hooks.*') || request()->routeIs('colors.*') || request()->routeIs('machine-nozzles.*') || request()->routeIs('machine-dropins.*') || request()->routeIs('machine-beams.*') || request()->routeIs('machine-cloth-rollers.*') || request()->routeIs('machine-softwares.*') || request()->routeIs('hsn-codes.*') || request()->routeIs('wirs.*') || request()->routeIs('machine-shafts.*') || request()->routeIs('machine-levers.*') || request()->routeIs('machine-chains.*') || request()->routeIs('machine-heald-wires.*') || request()->routeIs('machine-e-reads.*') || request()->routeIs('delivery-terms.*') || request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'sidebar-active' : '' }}">
            <div class="d-flex align-items-center">
                <i class="fas fa-industry me-3 sidebar-icon"></i>
                <span class="fw-medium">Machine</span>
            </div>
            <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse class="ms-4">
            <a href="{{ route('machine-categories.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-categories.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-list me-2 sidebar-icon-small"></i>
                <span>Category</span>
            </a>
            <a href="{{ route('brands.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('brands.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-tags me-2 sidebar-icon-small"></i>
                <span>Brand</span>
            </a>
            <a href="{{ route('machine-models.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-models.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cog me-2 sidebar-icon-small"></i>
                <span>Machine Model</span>
            </a>
            <a href="{{ route('machine-sizes.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-sizes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-ruler me-2 sidebar-icon-small"></i>
                <span>Machine Size</span>
            </a>
            <a href="{{ route('flange-sizes.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('flange-sizes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-compress-arrows-alt me-2 sidebar-icon-small"></i>
                <span>Flange Size</span>
            </a>
            <a href="{{ route('feeders.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('feeders.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-box me-2 sidebar-icon-small"></i>
                <span>Feeder</span>
            </a>
            <a href="{{ route('machine-hooks.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-hooks.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-link me-2 sidebar-icon-small"></i>
                <span>Machine Hook</span>
            </a>
            <a href="{{ route('colors.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('colors.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-palette me-2 sidebar-icon-small"></i>
                <span>Color Selector</span>
            </a>
            <a href="{{ route('machine-nozzles.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-nozzles.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-spray-can me-2 sidebar-icon-small"></i>
                <span>Machine Nozzle</span>
            </a>
            <a href="{{ route('machine-dropins.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-dropins.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-tint-droplet me-2 sidebar-icon-small"></i>
                <span>Machine Droppins</span>
            </a>
            <a href="{{ route('machine-beams.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-beams.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-chart-line me-2 sidebar-icon-small"></i>
                <span>Machine Beam</span>
            </a>
            <a href="{{ route('machine-cloth-rollers.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-cloth-rollers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-rotate me-2 sidebar-icon-small"></i>
                <span>Machine Cloth Roller</span>
            </a>
            <a href="{{ route('machine-softwares.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-softwares.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-code me-2 sidebar-icon-small"></i>
                <span>Machine Software</span>
            </a>
            <a href="{{ route('hsn-codes.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('hsn-codes.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-hashtag me-2 sidebar-icon-small"></i>
                <span>HSN Code</span>
            </a>
            <a href="{{ route('wirs.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('wirs.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-file-invoice me-2 sidebar-icon-small"></i>
                <span>WIR</span>
            </a>
            <a href="{{ route('machine-shafts.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-shafts.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-circle-notch me-2 sidebar-icon-small"></i>
                <span>Machine Shaft</span>
            </a>
            <a href="{{ route('machine-levers.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-levers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-toggle-on me-2 sidebar-icon-small"></i>
                <span>Machine Lever</span>
            </a>
            <a href="{{ route('machine-chains.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-chains.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-link me-2 sidebar-icon-small"></i>
                <span>Machine Chain</span>
            </a>
            <a href="{{ route('machine-heald-wires.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-heald-wires.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-slash me-2 sidebar-icon-small"></i>
                <span>Machine Heald Wires</span>
            </a>
            <a href="{{ route('machine-e-reads.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('machine-e-reads.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-book-reader me-2 sidebar-icon-small"></i>
                <span>Machine E-Reed</span>
            </a>
            <a href="{{ route('delivery-terms.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('delivery-terms.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-shipping-fast me-2 sidebar-icon-small"></i>
                <span>Delivery Term</span>
            </a>
            <a href="{{ route('sellers.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('sellers.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-user-tie me-2 sidebar-icon-small"></i>
                <span>Seller</span>
            </a>
            <a href="{{ route('countries.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('countries.*') ? 'sidebar-active' : '' }}">
                <i class="fas fa-globe me-2 sidebar-icon-small"></i>
                <span>Seller country</span>
            </a>
            <a href="{{ route('settings.contract-details') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('settings.contract-details') || request()->routeIs('settings.update-contract-details') ? 'sidebar-active' : '' }}">
                <i class="fas fa-cog me-2 sidebar-icon-small"></i>
                <span>Other Contract Details</span>
            </a>
        </div>
    </div>
    @endhasrole

    @canany(['view settings', 'edit settings'])
    @php
        $settingsChildrenCount = 4;
        $settingsSingleItem = null;
        $settingsSingleRoute = null;
        $settingsSingleIcon = null;
        
        if ($settingsChildrenCount == 1) {
            $settingsSingleItem = 'General Settings';
            $settingsSingleRoute = route('settings.edit');
            $settingsSingleIcon = 'fa-cog';
        }
    @endphp
    @if($settingsChildrenCount == 1 && $settingsSingleItem)
        <a 
            href="{{ $settingsSingleRoute }}" 
            class="d-flex align-items-center px-3 py-2 mb-1 text-decoration-none rounded sidebar-link {{ request()->routeIs('settings.*') || request()->routeIs('pi-layouts.*') || request()->routeIs('business-firms.*') || request()->routeIs('port-of-destinations.*') ? 'sidebar-active' : '' }}">
            <i class="fas {{ $settingsSingleIcon }} me-3 sidebar-icon"></i>
            <span class="fw-medium">{{ $settingsSingleItem }}</span>
        </a>
    @else
        <div x-data="{ open: {{ request()->routeIs('settings.edit') || request()->routeIs('settings.update') || request()->routeIs('settings.pi-layouts') || request()->routeIs('pi-layouts.*') || request()->routeIs('admin.*') || request()->routeIs('business-firms.*') || request()->routeIs('settings.port-of-destinations') || request()->routeIs('port-of-destinations.*') || request()->routeIs('settings.complain-types') || request()->routeIs('complain-types.*') ? 'true' : 'false' }} }">
            <button 
                @click="open = !open" 
                class="w-100 d-flex align-items-center justify-content-between px-3 py-2 mb-1 border-0 bg-transparent rounded sidebar-link {{ request()->routeIs('settings.edit') || request()->routeIs('settings.update') || request()->routeIs('settings.pi-layouts') || request()->routeIs('pi-layouts.*') || request()->routeIs('admin.*') || request()->routeIs('business-firms.*') || request()->routeIs('settings.port-of-destinations') || request()->routeIs('port-of-destinations.*') || request()->routeIs('settings.complain-types') || request()->routeIs('complain-types.*') ? 'sidebar-active' : '' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cog me-3 sidebar-icon"></i>
                    <span class="fw-medium">Settings</span>
                </div>
                <i class="fas fa-chevron-down small sidebar-chevron" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-collapse class="ms-4">
                <a href="{{ route('settings.edit') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('settings.edit') || (request()->routeIs('settings.update') && !request()->routeIs('settings.pi-layouts')) ? 'sidebar-active' : '' }}">
                    <i class="fas fa-cog me-2 sidebar-icon-small"></i>
                    <span>General Settings</span>
                </a>
                <a href="{{ route('settings.pi-layouts') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('settings.pi-layouts') || request()->routeIs('pi-layouts.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-file-invoice me-2 sidebar-icon-small"></i>
                    <span>Layout of Proforma</span>
                </a>
                <a href="{{ route('business-firms.index') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('business-firms.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-briefcase me-2 sidebar-icon-small"></i>
                    <span>Business Firm</span>
                </a>
                <a href="{{ route('settings.port-of-destinations') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('settings.port-of-destinations') || request()->routeIs('port-of-destinations.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-anchor me-2 sidebar-icon-small"></i>
                    <span>Port of Destination</span>
                </a>
                <a href="{{ route('settings.complain-types') }}" class="d-flex align-items-center px-3 py-2 mb-1 small text-decoration-none rounded sidebar-link sidebar-submenu {{ request()->routeIs('settings.complain-types') || request()->routeIs('complain-types.*') ? 'sidebar-active' : '' }}">
                    <i class="fas fa-exclamation-circle me-2 sidebar-icon-small"></i>
                    <span>Complain Type</span>
                </a>
            </div>
        </div>
    @endif
    @endcanany
</nav>
