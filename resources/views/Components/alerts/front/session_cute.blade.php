
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- 💗 رسالة اللوج إن الكيوت --}}
@if (session('error'))
    <div
        style="
            background: #ffe4f4;
            border: 1px solid #ffb6e1;
            color: #b3005f;
            padding: 10px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        ">
        <span>{{ session('error') }}</span>
    </div>
@endif
