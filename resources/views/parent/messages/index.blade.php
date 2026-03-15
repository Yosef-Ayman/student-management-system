@extends('layouts.app')
@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')

<div class="row g-3">
    {{-- Compose --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-pen me-2 text-warning"></i>New Message</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('parent.messages.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.83rem;font-weight:500;">To (Teacher)</label>
                        <select name="receiver_id" class="form-select form-select-sm" required>
                            <option value="">Select teacher</option>
                            @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.83rem;font-weight:500;">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-sm"
                               placeholder="Optional subject line">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.83rem;font-weight:500;">Message *</label>
                        <textarea name="body" rows="5" class="form-control form-control-sm"
                                  placeholder="Write your message..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Inbox --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header-clean">
                <h6>
                    <i class="fas fa-inbox me-2 text-warning"></i>Inbox
                    @if($unreadCount > 0)
                    <span class="badge bg-danger ms-1">{{ $unreadCount }} new</span>
                    @endif
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>From / To</th><th>Subject</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                        @php $isMine = $msg->sender_id === auth()->id(); @endphp
                        <tr style="{{ !$isMine && !$msg->is_read ? 'background:#f8f7ff;' : '' }}">
                            <td>
                                <div style="font-size:.875rem;font-weight:500;">
                                    @if($isMine)
                                        <i class="fas fa-arrow-right me-1" style="color:#059669;font-size:.7rem;"></i>
                                        {{ $msg->receiver?->name ?? '—' }}
                                    @else
                                        <i class="fas fa-arrow-left me-1" style="color:#4f46e5;font-size:.7rem;"></i>
                                        {{ $msg->sender?->name ?? '—' }}
                                    @endif
                                </div>
                                <div style="font-size:.72rem;color:#9ca3af;">
                                    {{ ucfirst($isMine ? $msg->receiver?->role : $msg->sender?->role) }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size:.875rem;">{{ $msg->subject ?: Str::limit($msg->body, 40) }}</div>
                            </td>
                            <td style="font-size:.78rem;color:#9ca3af;white-space:nowrap;">
                                {{ $msg->created_at->diffForHumans() }}
                            </td>
                            <td>
                                @if($isMine)
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:.72rem;">Sent</span>
                                @elseif(!$msg->is_read)
                                    <span class="badge bg-primary-subtle text-primary" style="font-size:.72rem;">New</span>
                                @else
                                    <span class="badge bg-light text-muted" style="font-size:.72rem;">Read</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
                                No messages yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
            <div class="p-2">{{ $messages->links() }}</div>
            @endif
        </div>
    </div>
</div>

@endsection
