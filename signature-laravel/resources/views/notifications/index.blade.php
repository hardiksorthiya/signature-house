<x-app-layout>
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Notifications</h1>
                <p class="text-muted mb-0 small">All your notifications in one place</p>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end align-items-center">
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline mb-0">
                            @csrf
                            <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                                <i class="fas fa-check-double me-1 me-sm-2"></i><span class="d-none d-sm-inline">Mark all as read</span><span class="d-inline d-sm-none">Mark read</span>
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1 me-sm-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 px-md-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-bell text-white small"></i>
                        </div>
                        <h2 class="h5 mb-0 fw-semibold" style="color: #1f2937;">All Notifications</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $notifications->total() }} Total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $message = $data['message'] ?? 'Notification';
                            $isUnread = is_null($notification->read_at);
                            $readUrl = route('notifications.read', $notification->id);
                        @endphp
                        <a href="{{ $readUrl }}" class="d-block text-decoration-none text-dark border-bottom notification-row {{ $isUnread ? 'bg-light bg-opacity-50' : '' }}" style="transition: background-color 0.15s ease;">
                            <div class="d-flex align-items-start gap-3 px-3 px-md-4 py-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $isUnread ? 'bg-primary bg-opacity-10' : 'bg-light' }}" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-bell {{ $isUnread ? 'text-primary' : 'text-muted' }}" style="font-size: 0.875rem;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="small fw-medium" style="color: #1f2937;">{{ $message }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ $notification->created_at->format('M d, Y \a\t h:i A') }} ({{ $notification->created_at->diffForHumans() }})</div>
                                </div>
                                @if($isUnread)
                                    <span class="badge rounded-pill bg-danger flex-shrink-0" style="font-size: 0.65rem;">New</span>
                                @endif
                                <i class="fas fa-chevron-right text-muted flex-shrink-0" style="font-size: 0.75rem;"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 px-3">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-bell-slash text-muted" style="font-size: 1.5rem;"></i>
                            </div>
                            <p class="text-muted mb-0">No notifications yet.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mt-3" style="border-radius: 8px;">Go to Dashboard</a>
                        </div>
                    @endforelse
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer border-0 bg-transparent py-3 px-3 px-md-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
