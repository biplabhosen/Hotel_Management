<div class="dropdown">
    <button class="btn btn-sm btn-kebab" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
        &#x22EE;
    </button>

    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">

        {{-- Edit --}}
        <li>
            <a class="dropdown-item action-edit"
               href="{{ route('users.edit', $user->id) }}">
                Edit
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        {{-- Archive / Restore --}}
        <li>
            <form action="{{ $user->is_active
                    ? route('users.archive', $user->id)
                    : route('users.restore', $user->id) }}"
                  method="POST"
                  onsubmit="return confirm(
                      '{{ $user->is_active
                          ? 'Archive this user?'
                          : 'Restore this user?' }}'
                  )">
                @csrf
                @method('PATCH')

                <button type="submit"
                        class="dropdown-item {{ $user->is_active ? 'text-warning' : 'text-success' }}">
                    {{ $user->is_active ? 'Archive' : 'Restore' }}
                </button>
            </form>
        </li>

    </ul>
</div>
