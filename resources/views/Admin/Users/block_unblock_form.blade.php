@php
    $isSelf = auth()->id() === $user->id;

    $isBlocked = (bool) $user->is_blocked;

    $action = $isBlocked
        ? route('admin.users.unblock', $user->id)
        : route('admin.users.block', $user->id);

    $btnClass = $isBlocked
    ? 'btn-gradient-success'
    : 'btn-gradient-danger';


    $label = $isBlocked ? 'Unblock' : 'Block';

@endphp

@if ($isSelf)
    <button type="button"
            class="btn btn-sm btn-outline-secondary"
            disabled
            title="You cannot block your own account">
        You
    </button>
@else
    <form action="{{ $action }}"
          method="POST"
          class="d-inline-block mb-0"
          >
        @csrf
        @method('PUT')

        <button type="submit" class="btn btn-sm {{ $btnClass }}">
            {{ $label }}
        </button>
    </form>
@endif
