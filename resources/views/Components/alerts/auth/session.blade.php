{{-- ✅ Session Alerts: success / status / error --}}
@if (session('success') || session('status'))
    <div class="alert alert-success mt-2">
        {{ session('success') ?? session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-2">
        {{ session('error') }}
    </div>
@endif

