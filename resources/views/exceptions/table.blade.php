<table class="table mb-0 table-hover">
    <thead>
    <tr>
        <th scope="col" class="h5">{{ __('exceptions_table_email_header') }}</th>
        <th scope="col" class="h5">{{ __('exceptions_table_phone_header') }}</th>
        <th scope="col" class="h5">{{ __('exceptions_table_store_header') }}</th>
        <th scope="col" class="h5">{{ __('exceptions_table_type_header') }}</th>
        <th scope="col" class="h5">{{ __('exceptions_table_submission_time_header') }}</th>
        <th scope="col" class="h5">{{ __('exceptions_table_actions_header') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($exceptions as $exception)
        <tr>
            <td>{{ $exception['email'] }}</td>
            <td>{{ $exception['phone']}}</td>
            <td>{{ $exception['store_domain']}}</td>
            <td>{{ $exception['status']? 'Engedélyezve' : 'Letiltva' }}</td>
            <td>{{ $exception['created_date']}}</td>
            <td>
                <button class="btn btn-sm btn-primary edit-button"
                        data-id="{{ $exception['id'] }}"
                        data-store_id="{{ $exception['store_id'] }}"
                        data-email="{{ $exception['email'] }}"
                        data-phone="{{ $exception['phone'] }}"
                        data-type="{{ $exception['status'] ? 'allow' : 'deny' }}"
                        data-bs-toggle="modal" data-bs-target="#editExceptionModal">
                    {{ __('exceptions_table_edit_button') }}
                </button>
                <form id="delete-form-{{ $exception['id'] }}" action="{{ route('exceptions.destroy', $exception['id']) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $exception['id'] }}')">
                        {{ __('exceptions_table_delete_button') }}
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">{{ __('exceptions_table_no_data_message') }}</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $exceptions->links() }}
</div>
