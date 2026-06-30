<header class="bg-white border-bottom shadow-sm sticky-top z-30">
    <div class="container-fluid px-3 px-lg-4 py-3">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Hamburger Menu Button -->
            <div class="d-flex align-items-center">
                <button 
                    type="button"
                    @click="sidebarOpen = !sidebarOpen" 
                    class="btn btn-link text-dark p-2"
                    aria-label="Toggle sidebar">
                    <i class="fas fa-bars fs-5"></i>
                </button>

                <div class="d-lg-none d-flex align-items-center ms-1">
                    @if(!empty($logoPath))
                        <img src="{{ $logoPath }}" alt="{{ config('app.name', 'Signature ERP') }}" class="header-mobile-logo">
                    @else
                        <div class="header-mobile-logo-placeholder">
                            <i class="fas fa-tshirt text-white"></i>
                        </div>
                    @endif
                </div>

                <div class="d-none d-md-block text-start ms-3">
                    <div class="fw-medium text-dark small mb-1">{{ Auth::user()->name }}</div>
                    <div class="user-role-badge">
                        @if(Auth::user()->roles->first())
                            <i class="fas fa-user-tag me-1"></i>
                            <span>{{ Auth::user()->roles->first()->name }}</span>
                        @else
                            <i class="fas fa-user me-1"></i>
                            <span>User</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side Actions -->
            <div class="d-flex align-items-center gap-2 position-relative">
                <!-- Notifications Dropdown -->
                @php
                    $userNotifications = Auth::user()->notifications()->latest()->take(15)->get();
                    $unreadNotificationCount = Auth::user()->unreadNotifications()->count();
                @endphp
                <div class="position-relative" x-data="{ notificationOpen: false }" @click.away="notificationOpen = false">
                    <button 
                        type="button"
                        @click="notificationOpen = !notificationOpen" 
                        class="btn btn-link text-muted position-relative p-2 text-decoration-none"
                        aria-label="Notifications"
                        aria-expanded="false"
                        :aria-expanded="notificationOpen">
                        <i class="fas fa-bell fs-5"></i>
                        @if($unreadNotificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.2rem 0.35rem; min-width: 1.1rem;">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <div 
                        x-show="notificationOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-1"
                        x-cloak
                        class="position-absolute end-0 mt-2 bg-white border rounded shadow-lg notification-dropdown"
                        style="min-width: 20rem; max-width: 22rem; max-height: 22rem; z-index: 1080; top: 100%; overflow: hidden;">
                        <div class="d-flex flex-column" style="max-height: 22rem;">
                            <div class="px-3 py-2 border-bottom bg-light d-flex align-items-center justify-content-between">
                                <span class="fw-medium text-dark small">Notifications</span>
                                @if($unreadNotificationCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-primary p-0 text-decoration-none">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="overflow-auto flex-grow-1" style="max-height: 16rem;">
                                @forelse($userNotifications as $notification)
                                    @php
                                        $data = $notification->data ?? [];
                                        $message = $data['message'] ?? 'Notification';
                                        $isUnread = is_null($notification->read_at);
                                        $taskId = $data['task_id'] ?? null;
                                        $leadId = $data['lead_id'] ?? null;
                                        $contractId = $data['contract_id'] ?? null;
                                        $notificationUrl = $contractId ? route('contracts.pending-approval') : ($taskId ? route('tasks.show', $taskId) : ($leadId ? route('leads.show', $leadId) : '#'));
                                    @endphp
                                    <a href="{{ $notificationUrl }}" 
                                       class="notification-item d-block px-3 py-2 border-bottom text-decoration-none text-dark {{ $isUnread ? 'bg-light bg-opacity-50' : '' }}"
                                       style="transition: background-color 0.15s ease;">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="fas fa-bell text-muted mt-1" style="font-size: 0.75rem;"></i>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="small">{{ $message }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                            @if($isUnread)
                                                <span class="badge rounded-pill bg-danger" style="font-size: 0.5rem;"></span>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-3 py-4 text-center text-muted small">No notifications yet.</div>
                                @endforelse
                            </div>
                            @if($userNotifications->isNotEmpty())
                                <div class="px-3 py-2 border-top bg-light text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-primary small text-decoration-none">View all</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="position-relative" x-data="{ open: false }" @click.away="open = false">
                    <button 
                        @click="open = !open" 
                        class="d-flex align-items-center gap-2 btn btn-link text-decoration-none p-1"
                        type="button">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-start ms-2">
                            <div class="fw-medium text-dark small mb-0">{{ Auth::user()->name }}</div>
                            <div class="user-role-text">
                                @if(Auth::user()->roles->first())
                                    {{ Auth::user()->roles->first()->name }}
                                @else
                                    User
                                @endif
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-muted small d-none d-md-block" :class="{ 'rotate-180': open }" style="transition: transform 0.2s ease;"></i>
                    </button>

                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-1"
                        x-cloak
                        class="position-absolute end-0 mt-2 bg-white border rounded shadow-lg"
                        style="min-width: 14rem; z-index: 1050; top: 100%;">
                        <div class="py-2">
                            <!-- User Info Section -->
                            <div class="px-4 py-3 border-bottom">
                                <div class="fw-medium text-dark small mb-1">{{ Auth::user()->name }}</div>
                                <div class="user-role-badge-dropdown">
                                    <i class="fas fa-user-tag me-1"></i>
                                    <span>
                                        @if(Auth::user()->roles->first())
                                            {{ Auth::user()->roles->first()->name }}
                                        @else
                                            User
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Menu Items -->
                            <div class="pt-1">
                            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center px-4 py-2 text-decoration-none text-dark hover-bg-light" style="transition: background-color 0.15s ease;">
                                <i class="fas fa-user me-2 text-muted"></i>
                                {{ __('Profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button 
                                    type="submit"
                                    class="w-100 d-flex align-items-center px-4 py-2 border-0 bg-transparent text-start text-danger text-decoration-none hover-bg-light"
                                    style="transition: background-color 0.15s ease;">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
