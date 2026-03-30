@if (session('success') || session('status'))
    <div class="alert alert-success alert-sm py-2 px-3 mb-2">
        <small>{{ session('success') ?? session('status') }}</small>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-sm py-2 px-3 mb-2">
        <small>{{ session('error') }}</small>
    </div>
@endif
