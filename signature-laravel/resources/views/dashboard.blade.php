<x-app-layout>
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <h3 class="h6 fw-semibold mb-3" style="color: #1f2937;">Quick Actions</h3>
                    <div class="row g-2 g-sm-3 quick-actions-row">
                        @can('create leads')
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('leads.create') }}" class="btn btn-primary quick-action-btn w-100">
                                    <i class="fas fa-user-plus quick-action-icon"></i><span class="quick-action-label">Create New Lead</span>
                                </a>
                            </div>
                        @endcan
                        @canany(['convert contract', 'view contract approvals'])
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('contracts.create') }}" class="btn btn-outline-primary quick-action-btn w-100">
                                    <i class="fas fa-file-contract quick-action-icon"></i><span class="quick-action-label">Create New Contract</span>
                                </a>
                            </div>
                        @endcanany
                        @can('create proforma invoices')
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('proforma-invoices.create') }}" class="btn btn-success quick-action-btn w-100">
                                    <i class="fas fa-file-invoice quick-action-icon"></i><span class="quick-action-label">Create Proforma Invoice</span>
                                </a>
                            </div>
                        @endcan
                        @canany(['view proforma invoices', 'view contract approvals'])
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-success quick-action-btn w-100">
                                    <i class="fas fa-file-signature quick-action-icon"></i><span class="quick-action-label">Create New Purchase Order</span>
                                </a>
                            </div>
                        @endcanany
                        @can('edit proforma invoices')
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('proforma-invoices.delivery-details-index') }}" class="btn btn-outline-info quick-action-btn w-100">
                                    <i class="fas fa-truck-loading quick-action-icon"></i><span class="quick-action-label">Add Detail In PI</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4">
                @can('view leads')
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-metric-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                            <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div class="d-flex flex-column">
                                            <p class="text-muted small mb-2 dashboard-metric-title">Total Leads</p>
                                            <div class="rounded-circle d-flex align-items-center justify-content-center dashboard-metric-icon-circle" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                                                <i class="fas fa-user-plus text-white"></i>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="dashboard-metric-number-badge">
                                                <h3 class="dashboard-metric-number mb-0">{{ $totalLeads }}</h3>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @canany(['convert contract', 'view contract approvals'])
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-metric-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex flex-column">
                                        <p class="text-muted small mb-2 dashboard-metric-title">Total Contracts</p>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center dashboard-metric-icon-circle" style="background: linear-gradient(135deg, #7c3aed, #5b21b6);">
                                            <i class="fas fa-file-contract text-white"></i>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="dashboard-metric-number-badge">
                                            <h3 class="dashboard-metric-number mb-0">{{ $totalContracts }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcanany

                @can('view customers')
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-metric-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex flex-column">
                                        <p class="text-muted small mb-2 dashboard-metric-title">Total Customers</p>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center dashboard-metric-icon-circle" style="background: linear-gradient(135deg, #059669, #047857);">
                                            <i class="fas fa-users text-white"></i>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="dashboard-metric-number-badge">
                                            <h3 class="dashboard-metric-number mb-0">{{ $totalCustomers }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @canany(['view proforma invoices', 'create proforma invoices', 'edit proforma invoices'])
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-metric-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex flex-column">
                                        <p class="text-muted small mb-2 dashboard-metric-title">Total PI</p>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center dashboard-metric-icon-circle" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                            <i class="fas fa-file-invoice text-white"></i>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="dashboard-metric-number-badge">
                                            <h3 class="dashboard-metric-number mb-0">{{ $totalPi }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcanany

                @canany(['view proforma invoices', 'view contract approvals'])
                    <div class="col-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-metric-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex flex-column">
                                        <p class="text-muted small mb-2 dashboard-metric-title">Total PO</p>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center dashboard-metric-icon-circle" style="background: linear-gradient(135deg, #0d9488, #0f766e);">
                                            <i class="fas fa-file-signature text-white"></i>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="dashboard-metric-number-badge">
                                            <h3 class="dashboard-metric-number mb-0">{{ $totalPo }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcanany
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="bg-white mb-6" style="border: 1px solid #60a5fa; border-radius: 6px; padding: 16px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h5 fw-semibold mb-0" style="color: #1f2937;">My Tasks Calendar</h3>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-info">
                <i class="fas fa-list me-2"></i>View All Tasks
            </a>
        </div>
        <div class="calendar-container" x-data="calendarView()" style="position: relative;">
            <!-- Calendar Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button @click="previousMonth()" class="btn btn-sm btn-link text-decoration-none p-1" style="color: #6b7280; border: none; background: none;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h4 class="h6 fw-medium mb-0" style="color: #1f2937;" x-text="monthYear"></h4>
                <button @click="nextMonth()" class="btn btn-sm btn-link text-decoration-none p-1" style="color: #6b7280; border: none; background: none;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Calendar Grid -->
            <div class="calendar-grid" style="border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden;">
                <!-- Day Headers -->
                <div class="row g-0" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Sun</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Mon</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Tue</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Wed</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Thu</span>
                    </div>
                    <div class="col text-center py-2" style="border-right: 1px solid #e5e7eb;">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Fri</span>
                    </div>
                    <div class="col text-center py-2">
                        <span class="small fw-medium" style="color: #6b7280; font-size: 0.8125rem;">Sat</span>
                    </div>
                </div>
                
                <!-- Calendar Days -->
                <div>
                    <template x-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
                        <div class="row g-0" style="border-bottom: 1px solid #e5e7eb;">
                            <template x-for="day in week" :key="day.date">
                                <div 
                                    class="col calendar-day-cell"
                                    :class="{
                                        'calendar-day-other-month': !day.isCurrentMonth,
                                        'calendar-day-today': day.isToday
                                    }">
                                    <!-- Date Number -->
                                    <div class="mb-2">
                                        <span 
                                            class="d-inline-block text-center"
                                            style="
                                                width: 28px;
                                                height: 28px;
                                                line-height: 28px;
                                                font-size: 0.875rem;
                                                font-weight: 500;
                                                border-radius: 4px;
                                            "
                                            :style="!day.isCurrentMonth ? 'color: #d1d5db; background-color: transparent;' : (day.isToday ? 'background-color: #9333ea; color: white; font-weight: 500;' : 'color: #2563eb; background-color: transparent;')"
                                            x-text="day.dayNumber">
                                        </span>
                                    </div>
                                    
                                    <!-- Tasks for this day -->
                                    <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 2px;">
                                        <template x-for="task in day.tasks" :key="task.id">
                                            <div 
                                                class="calendar-task-item rounded px-2 py-1"
                                                style="
                                                    font-size: 0.75rem;
                                                    font-weight: 500;
                                                    cursor: pointer;
                                                    transition: opacity 0.2s;
                                                    color: #374151;
                                                    border-radius: 6px;
                                                    position: relative;
                                                "
                                                :style="`background-color: ${getTaskColor(task.priority, task.status)};`"
                                                x-bind:data-task-id="task.id"
                                                x-bind:data-task-title="task.title"
                                                x-bind:data-task-event-type="task.event_type"
                                                x-bind:data-task-due-date="task.due_date_formatted"
                                                x-bind:data-task-scheduled-time="task.scheduled_time || ''"
                                                x-bind:data-task-description="task.description || ''"
                                                onmouseenter="showTaskPopup(this, event)"
                                                onmouseleave="hideTaskPopup()">
                                                <span x-text="task.title"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Task Detail Popup (Vanilla JS) -->
        <div id="task-detail-popup" class="task-detail-popup" style="display: none;"></div>
            </div>
        </div>
    </div>

    @if(
        auth()->user()->can('view leads')
        || auth()->user()->can('convert contract') || auth()->user()->can('view contract approvals')
        || auth()->user()->can('view customers')
        || auth()->user()->can('view proforma invoices') || auth()->user()->can('create proforma invoices') || auth()->user()->can('edit proforma invoices')
    )
        <div class="row g-4 mb-4">
            <div class="col-12">
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Analytics</h2>
                <p class="text-muted small mb-0">Use the filter on each chart (default: last 7 days). Data matches your permissions and team scope.</p>
            </div>

            @can('view leads')
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="leads">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-chart-line me-2 text-primary"></i>Leads trend</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <label class="visually-hidden" for="chartFilterLeads">Range</label>
                                    <select id="chartFilterLeads" class="form-select form-select-sm chart-range-preset" data-chart="leads">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="leads">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap"><canvas id="dashboardChartLeadsLine"></canvas></div>
                        </div>
                    </div>
                </div>
            @endcan

            @canany(['convert contract', 'view contract approvals'])
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="contracts">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-chart-bar me-2 text-primary"></i>Contracts</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <select class="form-select form-select-sm chart-range-preset" data-chart="contracts">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="contracts">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap"><canvas id="dashboardChartContractsBar"></canvas></div>
                        </div>
                    </div>
                </div>
            @endcanany

            @if(auth()->user()->can('view proforma invoices') || auth()->user()->can('create proforma invoices') || auth()->user()->can('edit proforma invoices') || auth()->user()->can('view contract approvals'))
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="pipo">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-chart-area me-2 text-primary"></i>PI &amp; PO trend</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <select class="form-select form-select-sm chart-range-preset" data-chart="pipo">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="pipo">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap"><canvas id="dashboardChartPiPoLine"></canvas></div>
                        </div>
                    </div>
                </div>
            @endif

            @canany(['convert contract', 'view contract approvals'])
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="contract_status">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-chart-pie me-2 text-primary"></i>Contract status</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <select class="form-select form-select-sm chart-range-preset" data-chart="contract_status">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="contract_status">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap"><canvas id="dashboardChartContractPie"></canvas></div>
                        </div>
                    </div>
                </div>
            @endcanany

            @can('view customers')
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="customers">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-user-check me-2 text-primary"></i>Customers (approved)</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <select class="form-select form-select-sm chart-range-preset" data-chart="customers">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="customers">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap dashboard-chart-wrap--horizontal"><canvas id="dashboardChartCustomersBar"></canvas></div>
                        </div>
                    </div>
                </div>
            @endcan

            @if(
                auth()->user()->can('view leads')
                || auth()->user()->can('convert contract') || auth()->user()->can('view contract approvals')
                || auth()->user()->can('view customers')
                || auth()->user()->can('view proforma invoices') || auth()->user()->can('create proforma invoices') || auth()->user()->can('edit proforma invoices')
                || auth()->user()->can('view proforma invoices') || auth()->user()->can('view contract approvals')
            )
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 dashboard-chart-card" style="border-radius: 12px;" data-chart-key="snapshot">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h6 fw-semibold mb-0 pt-1" style="color: #1f2937;"><i class="fas fa-circle-notch me-2 text-primary"></i>Totals overview</h3>
                                <div class="flex-shrink-0" style="width: 168px;">
                                    <select class="form-select form-select-sm chart-range-preset" data-chart="snapshot">
                                        <option value="7d" selected>Last 7 days</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="year">This year</option>
                                        <option value="custom">Custom range</option>
                                    </select>
                                    <div class="chart-custom-fields mt-2 d-none" data-for-chart="snapshot">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-start" aria-label="Start date">
                                        <input type="date" class="form-control form-control-sm mb-1 chart-custom-end" aria-label="End date">
                                        <button type="button" class="btn btn-sm btn-primary w-100 chart-custom-apply">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-chart-wrap"><canvas id="dashboardChartSnapshotDoughnut"></canvas></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

   

    <style>
        /* Quick Actions: full-width stack on mobile; 2-col from sm; wrap text (no overlap) */
        .quick-actions-row .quick-action-btn {
            min-height: 52px;
            padding: 0.65rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 10px;
            white-space: normal;
            text-align: center;
            line-height: 1.25;
            word-break: break-word;
            hyphens: auto;
        }
        .quick-actions-row .quick-action-icon {
            flex-shrink: 0;
        }
        .quick-actions-row .quick-action-label {
            flex: 1 1 auto;
            min-width: 0;
        }
        @media (min-width: 576px) {
            .quick-actions-row .quick-action-btn {
                min-height: 56px;
                font-size: 0.95rem;
            }
        }

        .dashboard-chart-wrap {
            position: relative;
            height: 280px;
            max-width: 100%;
        }
        .dashboard-chart-wrap--horizontal {
            height: 300px;
        }

        /* Dashboard top metric cards */
        .dashboard-metric-card{
            position: relative;
            overflow: hidden;
            border: 0;
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }
        .dashboard-metric-card::after{
            content: "";
            position: absolute;
            inset: -2px;
            background:
                radial-gradient(260px 120px at 10% 0%, rgba(124, 58, 237, 0.14), transparent 60%),
                radial-gradient(240px 120px at 90% 100%, rgba(5, 150, 105, 0.10), transparent 55%);
            pointer-events: none;
        }
        .dashboard-metric-card:hover{
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.10) !important;
        }
        .dashboard-metric-card > .card-body{
            position: relative;
            z-index: 1;
        }
        .dashboard-metric-title{
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }
        .dashboard-metric-icon-circle{
            width: 44px;
            height: 44px;
            border-radius: 999px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        .dashboard-metric-number-badge{
            padding: 10px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(229, 231, 235, 0.95);
            box-shadow: 0 14px 32px rgba(0,0,0,0.05);
        }
        .dashboard-metric-number{
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .calendar-day-cell {
            /* aspect-ratio: 1 / 1; */
            min-height: 80px;
            padding: 8px 6px;
            border-right: 1px solid #e5e7eb;
            position: relative;
            background-color: white;
        }
        
        .calendar-day-other-month {
            background-color: #fafafa !important;
        }
        
        .calendar-day-today {
            border: 2px solid #9333ea !important;
            background-color: white !important;
        }
        
        .task-detail-popup {
            position: fixed;
            z-index: 99999;
            width: 360px;
            background-color: #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            pointer-events: auto;
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        .task-detail-popup .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .task-detail-popup .popup-title {
            font-size: 1.125rem;
            font-weight: 500;
            color: #059669;
            margin: 0;
            flex: 1;
        }
        
        .task-detail-popup .popup-actions {
            display: flex;
            gap: 8px;
        }
        
        .task-detail-popup .popup-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            font-size: 0.875rem;
            padding: 4px;
            transition: color 0.2s;
        }
        
        .task-detail-popup .popup-actions button:hover {
            color: #1f2937;
        }
        
        .task-detail-popup .popup-content {
            background-color: #86efac;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .task-detail-popup .popup-detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .task-detail-popup .popup-detail-row:last-child {
            margin-bottom: 0;
        }
        
        .task-detail-popup .popup-detail-label {
            font-weight: 500;
            color: #065f46;
            min-width: 80px;
            font-size: 0.875rem;
        }
        
        .task-detail-popup .popup-detail-value {
            color: #064e3b;
            font-size: 0.875rem;
            flex: 1;
        }
        
        .task-detail-popup .popup-description {
            color: #064e3b;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(6, 78, 59, 0.2);
        }
    </style>

    <script>
        function calendarView() {
            return {
                currentDate: new Date({{ $selectedDate->year }}, {{ $selectedDate->month - 1 }}, 1),
                tasks: @json($tasksForJs),
                
                get monthYear() {
                    return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },
                
                get calendarDays() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    
                    // First day of the month
                    const firstDay = new Date(year, month, 1);
                    const startingDayOfWeek = firstDay.getDay();
                    
                    // Last day of the month
                    const lastDay = new Date(year, month + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    
                    // Previous month's days to fill the first week
                    const prevMonth = new Date(year, month, 0);
                    const daysInPrevMonth = prevMonth.getDate();
                    
                    const days = [];
                    
                    // Previous month's trailing days
                    for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                        const day = daysInPrevMonth - i;
                        const prevMonthNum = month === 0 ? 12 : month;
                        const prevYear = month === 0 ? year - 1 : year;
                        days.push({
                            dayNumber: day,
                            date: `${prevYear}-${String(prevMonthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                            isCurrentMonth: false,
                            isToday: false,
                            tasks: []
                        });
                    }
                    
                    // Current month's days
                    const today = new Date();
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        const isToday = year === today.getFullYear() && 
                                       month === today.getMonth() && 
                                       day === today.getDate();
                        
                        // Find tasks for this date
                        const dayTasks = this.tasks.find(t => t.date === dateStr);
                        
                        days.push({
                            dayNumber: day,
                            date: dateStr,
                            isCurrentMonth: true,
                            isToday: isToday,
                            tasks: dayTasks ? dayTasks.tasks : []
                        });
                    }
                    
                    // Next month's days to fill the last week (to make 6 weeks)
                    const totalDays = days.length;
                    const remainingDays = 42 - totalDays; // 6 weeks * 7 days
                    const nextMonthNum = month === 11 ? 1 : month + 2;
                    const nextYear = month === 11 ? year + 1 : year;
                    for (let day = 1; day <= remainingDays; day++) {
                        days.push({
                            dayNumber: day,
                            date: `${nextYear}-${String(nextMonthNum).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                            isCurrentMonth: false,
                            isToday: false,
                            tasks: []
                        });
                    }
                    
                    return days;
                },
                
                get calendarWeeks() {
                    const days = this.calendarDays;
                    const weeks = [];
                    for (let i = 0; i < days.length; i += 7) {
                        weeks.push(days.slice(i, i + 7));
                    }
                    return weeks;
                },
                
                getTaskColor(priority, status) {
                    if (status === 'completed') {
                        return '#e5e7eb'; // Light gray for completed
                    }
                    
                    // Soft pastel colors exactly matching the reference image
                    // Light backgrounds with dark text
                    const colors = [
                        '#bfdbfe', // Light blue (like "Ring Ceremony" - date 11)
                        '#fecdd3', // Light pink (like anniversaries - date 15, 17)
                        '#fed7aa', // Light orange/peach (like birthdays - date 16)
                        '#c7d2fe', // Light purple (like "Marriage Meeting" - date 24)
                        '#bbf7d0', // Light green (like Gujarati text - date 18)
                        '#fbcfe8'  // Light pink/magenta (alternative)
                    ];
                    
                    // Use priority to determine color, cycling for variety
                    switch(priority) {
                        case 2: return colors[2]; // Light orange for high priority
                        case 1: return colors[1]; // Light pink for medium priority
                        default: return colors[0]; // Light blue for low priority
                    }
                },
                
                previousMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                    this.loadTasksForMonth();
                },
                
                nextMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                    this.loadTasksForMonth();
                },
                
                loadTasksForMonth() {
                    // Reload tasks for the new month
                    const year = this.currentDate.getFullYear();
                    const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                    window.location.href = `/dashboard?month=${year}-${month}`;
                }
            }
        }
    </script>

    <!-- Vanilla JavaScript for Task Popup -->
    <script>
        let taskPopupTimer = null;
        const taskPopup = document.getElementById('task-detail-popup');
        
        function showTaskPopup(element, event) {
            // Clear any existing timer
            if (taskPopupTimer) {
                clearTimeout(taskPopupTimer);
                taskPopupTimer = null;
            }
            
            // Get task data from data attributes
            const taskId = element.getAttribute('data-task-id');
            const taskTitle = element.getAttribute('data-task-title');
            const taskEventType = element.getAttribute('data-task-event-type');
            const taskDueDate = element.getAttribute('data-task-due-date');
            const taskScheduledTime = element.getAttribute('data-task-scheduled-time');
            const taskDescription = element.getAttribute('data-task-description');
            
            // Calculate popup position
            const rect = element.getBoundingClientRect();
            let popupX = rect.left + (rect.width / 2) - 180;
            let popupY = rect.bottom + 10;
            
            // Adjust if popup would go off screen
            if (popupX < 10) popupX = 10;
            if (popupX + 360 > window.innerWidth) popupX = window.innerWidth - 370;
            if (popupY + 200 > window.innerHeight) {
                popupY = rect.top - 200;
            }
            
            // Build popup HTML
            let reminderHtml = '';
            if (taskEventType === 'Meeting' && taskScheduledTime) {
                reminderHtml = `
                    <div class="popup-detail-row">
                        <span class="popup-detail-label">Reminder:</span>
                        <span class="popup-detail-value">1 hour before (${taskScheduledTime})</span>
                    </div>
                `;
            }
            
            let descriptionHtml = '';
            if (taskDescription) {
                descriptionHtml = `<div class="popup-description">${taskDescription}</div>`;
            }
            
            taskPopup.innerHTML = `
                <div>
                    <!-- Popup Header -->
                    <div class="popup-header">
                        <h4 class="popup-title">${taskTitle}</h4>
                        <div class="popup-actions">
                            <a href="{{ url('tasks') }}/${taskId}/edit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Edit" style="color: #6b7280;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="deleteTask(${taskId})" class="btn btn-sm btn-link p-0 text-decoration-none" title="Delete" style="color: #6b7280;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="hideTaskPopup()" class="btn btn-sm btn-link p-0 text-decoration-none" title="Close" style="color: #6b7280;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Popup Content -->
                    <div class="popup-content">
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Event Type:</span>
                            <span class="popup-detail-value">${taskEventType}</span>
                        </div>
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Date:</span>
                            <span class="popup-detail-value">${taskDueDate}</span>
                        </div>
                        ${reminderHtml}
                    </div>
                    
                    <!-- Description -->
                    ${descriptionHtml}
                </div>
            `;
            
            // Position and show popup
            taskPopup.style.left = popupX + 'px';
            taskPopup.style.top = popupY + 'px';
            taskPopup.style.display = 'block';
            
            // Keep popup visible when hovering over it
            taskPopup.onmouseenter = function() {
                if (taskPopupTimer) {
                    clearTimeout(taskPopupTimer);
                    taskPopupTimer = null;
                }
            };
            
            taskPopup.onmouseleave = function() {
                hideTaskPopup();
            };
        }
        
        function hideTaskPopup() {
            taskPopupTimer = setTimeout(() => {
                if (taskPopup) {
                    taskPopup.style.display = 'none';
                }
            }, 200);
        }
        
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('tasks') }}/${taskId}`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                form.innerHTML = `
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="${csrfToken}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Vanilla JavaScript for Task Popup -->
    <script>
        let taskPopupTimer = null;
        let taskPopup = null;
        
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            taskPopup = document.getElementById('task-detail-popup');
        });
        
        function showTaskPopup(element, event) {
            if (!taskPopup) {
                taskPopup = document.getElementById('task-detail-popup');
            }
            
            // Clear any existing timer
            if (taskPopupTimer) {
                clearTimeout(taskPopupTimer);
                taskPopupTimer = null;
            }
            
            // Get task data from data attributes
            const taskId = element.getAttribute('data-task-id');
            const taskTitle = element.getAttribute('data-task-title');
            const taskEventType = element.getAttribute('data-task-event-type');
            const taskDueDate = element.getAttribute('data-task-due-date');
            const taskScheduledTime = element.getAttribute('data-task-scheduled-time');
            const taskDescription = element.getAttribute('data-task-description');
            
            // Calculate popup position
            const rect = element.getBoundingClientRect();
            let popupX = rect.left + (rect.width / 2) - 180;
            let popupY = rect.bottom + 10;
            
            // Adjust if popup would go off screen
            if (popupX < 10) popupX = 10;
            if (popupX + 360 > window.innerWidth) popupX = window.innerWidth - 370;
            if (popupY + 200 > window.innerHeight) {
                popupY = rect.top - 200;
            }
            
            // Build popup HTML
            let reminderHtml = '';
            if (taskEventType === 'Meeting' && taskScheduledTime) {
                reminderHtml = `
                    <div class="popup-detail-row">
                        <span class="popup-detail-label">Reminder:</span>
                        <span class="popup-detail-value">1 hour before (${taskScheduledTime})</span>
                    </div>
                `;
            }
            
            let descriptionHtml = '';
            if (taskDescription) {
                descriptionHtml = `<div class="popup-description">${escapeHtml(taskDescription)}</div>`;
            }
            
            taskPopup.innerHTML = `
                <div>
                    <!-- Popup Header -->
                    <div class="popup-header">
                        <h4 class="popup-title">${escapeHtml(taskTitle)}</h4>
                        <div class="popup-actions">
                            <a href="{{ url('tasks') }}/${taskId}/edit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Edit" style="color: #6b7280;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="deleteTaskPopup(${taskId})" class="btn btn-sm btn-link p-0 text-decoration-none" title="Delete" style="color: #6b7280;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="hideTaskPopup()" class="btn btn-sm btn-link p-0 text-decoration-none" title="Close" style="color: #6b7280;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Popup Content -->
                    <div class="popup-content">
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Event Type:</span>
                            <span class="popup-detail-value">${escapeHtml(taskEventType)}</span>
                        </div>
                        <div class="popup-detail-row">
                            <span class="popup-detail-label">Date:</span>
                            <span class="popup-detail-value">${escapeHtml(taskDueDate)}</span>
                        </div>
                        ${reminderHtml}
                    </div>
                    
                    <!-- Description -->
                    ${descriptionHtml}
                </div>
            `;
            
            // Position and show popup
            taskPopup.style.left = popupX + 'px';
            taskPopup.style.top = popupY + 'px';
            taskPopup.style.display = 'block';
            
            // Keep popup visible when hovering over it
            taskPopup.onmouseenter = function() {
                if (taskPopupTimer) {
                    clearTimeout(taskPopupTimer);
                    taskPopupTimer = null;
                }
            };
            
            taskPopup.onmouseleave = function() {
                hideTaskPopup();
            };
        }
        
        function hideTaskPopup() {
            taskPopupTimer = setTimeout(() => {
                if (taskPopup) {
                    taskPopup.style.display = 'none';
                }
            }, 200);
        }
        
        function deleteTaskPopup(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('tasks') }}/${taskId}`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                form.innerHTML = `
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="${csrfToken}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                return;
            }

            const chartDataUrl = @json(route('dashboard.chart-data'));
            const palette = ['#e74343', '#7c3aed', '#059669', '#2563eb', '#0d9488', '#d97706', '#db2777'];
            const chartOpts = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            };

            window._dashboardChartInstances = window._dashboardChartInstances || {};

            function destroyChart(canvasId) {
                if (window._dashboardChartInstances[canvasId]) {
                    window._dashboardChartInstances[canvasId].destroy();
                    delete window._dashboardChartInstances[canvasId];
                }
            }

            async function fetchChartJson(chart, preset, start, end) {
                const u = chartDataUrl.indexOf('http') === 0
                    ? new URL(chartDataUrl)
                    : new URL(chartDataUrl, window.location.origin);
                u.searchParams.set('chart', chart);
                u.searchParams.set('preset', preset);
                if (preset === 'custom') {
                    if (!start || !end) {
                        return null;
                    }
                    u.searchParams.set('start', start);
                    u.searchParams.set('end', end);
                }
                const res = await fetch(u.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.status === 403) {
                    return null;
                }
                if (!res.ok) {
                    const t = await res.text();
                    throw new Error(t || 'Chart request failed');
                }
                return res.json();
            }

            function renderLine(canvasId, labels, values, label, borderColor, fillColor) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                destroyChart(canvasId);
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            borderColor: borderColor,
                            backgroundColor: fillColor || 'rgba(231, 67, 67, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3
                        }]
                    },
                    options: chartOpts
                });
            }

            function renderBar(canvasId, labels, values, label) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                destroyChart(canvasId);
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            backgroundColor: 'rgba(124, 58, 237, 0.65)',
                            borderRadius: 6
                        }]
                    },
                    options: chartOpts
                });
            }

            function renderPiPo(canvasId, data) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                destroyChart(canvasId);
                const datasets = [];
                if (data.pi) {
                    datasets.push({
                        label: 'PI',
                        data: data.pi,
                        borderColor: palette[3],
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    });
                }
                if (data.po) {
                    datasets.push({
                        label: 'PO',
                        data: data.po,
                        borderColor: palette[4],
                        backgroundColor: 'rgba(13, 148, 136, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
                    });
                }
                if (!datasets.length) return;
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'line',
                    data: { labels: data.labels, datasets: datasets },
                    options: chartOpts
                });
            }

            function renderPie(canvasId, labels, counts) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                if (!labels || !labels.length) return;
                destroyChart(canvasId);
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: counts,
                            backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                            borderWidth: 1
                        }]
                    },
                    options: chartOpts
                });
            }

            function renderCustomersHBar(canvasId, labels, values) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                destroyChart(canvasId);
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Approved customers',
                            data: values,
                            backgroundColor: 'rgba(5, 150, 105, 0.7)',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        ...chartOpts,
                        indexAxis: 'y',
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }

            function renderDoughnut(canvasId, labels, counts) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                if (!labels || !labels.length) return;
                destroyChart(canvasId);
                window._dashboardChartInstances[canvasId] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: counts,
                            backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                            borderWidth: 1
                        }]
                    },
                    options: { ...chartOpts, cutout: '58%' }
                });
            }

            async function loadDashboardChart(chart, preset, start, end) {
                try {
                    const data = await fetchChartJson(chart, preset, start, end);
                    if (!data) return;

                    if (data.type === 'timeseries') {
                        if (chart === 'leads') {
                            renderLine('dashboardChartLeadsLine', data.labels, data.values, 'Leads', 'rgb(231, 67, 67)', 'rgba(231, 67, 67, 0.12)');
                        } else if (chart === 'contracts') {
                            renderBar('dashboardChartContractsBar', data.labels, data.values, 'Contracts');
                        } else if (chart === 'customers') {
                            renderCustomersHBar('dashboardChartCustomersBar', data.labels, data.values);
                        } else if (chart === 'pipo') {
                            renderPiPo('dashboardChartPiPoLine', data);
                        }
                    } else if (data.type === 'pie') {
                        renderPie('dashboardChartContractPie', data.labels, data.counts);
                    } else if (data.type === 'doughnut') {
                        renderDoughnut('dashboardChartSnapshotDoughnut', data.labels, data.counts);
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            function presetDefaultCustomDates(card) {
                const startEl = card.querySelector('.chart-custom-start');
                const endEl = card.querySelector('.chart-custom-end');
                if (!startEl || !endEl) return;
                const end = new Date();
                const start = new Date();
                start.setDate(start.getDate() - 6);
                const fmt = function (d) {
                    return d.toISOString().slice(0, 10);
                };
                endEl.value = fmt(end);
                startEl.value = fmt(start);
            }

            document.querySelectorAll('.chart-range-preset').forEach(function (sel) {
                const chart = sel.getAttribute('data-chart');
                const card = sel.closest('.dashboard-chart-card');
                const customBox = card ? card.querySelector('.chart-custom-fields[data-for-chart="' + chart + '"]') : null;

                sel.addEventListener('change', function () {
                    if (sel.value === 'custom') {
                        if (customBox) {
                            customBox.classList.remove('d-none');
                            presetDefaultCustomDates(card);
                        }
                    } else {
                        if (customBox) {
                            customBox.classList.add('d-none');
                        }
                        loadDashboardChart(chart, sel.value, null, null);
                    }
                });

                const applyBtn = card ? card.querySelector('.chart-custom-fields[data-for-chart="' + chart + '"] .chart-custom-apply') : null;
                if (applyBtn) {
                    applyBtn.addEventListener('click', function () {
                        const box = card.querySelector('.chart-custom-fields[data-for-chart="' + chart + '"]');
                        const s = box.querySelector('.chart-custom-start').value;
                        const e = box.querySelector('.chart-custom-end').value;
                        if (!s || !e) {
                            alert('Please select start and end date.');
                            return;
                        }
                        if (e < s) {
                            alert('End date must be on or after start date.');
                            return;
                        }
                        loadDashboardChart(chart, 'custom', s, e);
                    });
                }

                loadDashboardChart(chart, '7d', null, null);
            });
        });
    </script>
</x-app-layout>



