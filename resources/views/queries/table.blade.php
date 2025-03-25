<table class="table mb-0 table-hover">
    <thead>
    <tr>
        <th scope="col" class="h5">{{ __('queries_table_email_header') }}</th>
        <th scope="col" class="h5">{{ __('queries_table_phone_header') }}</th>
        <th scope="col" class="h5">{{ __('queries_table_store_header') }}</th>
        <th scope="col" class="h5">{{ __('queries_table_status_header') }}</th>
        <th scope="col" class="h5">{{ __('queries_table_query_time_header') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($queries as $query)
        <tr>
            <td>{{ $query['email'] }}</td>
            <td>{{ $query['phone']}}</td>
            <td>{{ $query['store_domain']}}</td>
            <td>{{ $query['status']? __('queries_table_status_approved') : __('queries_table_status_blocked') }}</td>
            <td>{{ $query['created_at']}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">{{ __('queries_table_no_queries_found') }}</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $queries->links() }}
</div>
