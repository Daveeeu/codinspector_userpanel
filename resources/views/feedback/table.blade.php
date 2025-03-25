<table class="table mb-0 table-hover">
    <thead>
    <tr>
        <th scope="col" class="h5">{{ __('feedbacks_table_email_header') }}</th>
        <th scope="col" class="h5">{{ __('feedbacks_table_phone_header') }}</th>
        <th scope="col" class="h5">{{ __('feedbacks_table_order_identifier_header') }}</th>
        <th scope="col" class="h5">{{ __('feedbacks_table_store_header') }}</th>
        <th scope="col" class="h5">{{ __('feedbacks_table_outcome_header') }}</th>
        <th scope="col" class="h5">{{ __('feedbacks_table_submission_time_header') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($feedbacks as $feedback)
        <tr>
            <td>{{ $feedback['email'] }}</td>
            <td>{{ $feedback['phone']}}</td>
            <td>{{ $feedback['order_identifier']}}</td>
            <td>{{ $feedback['store_domain']}}</td>
            <td>{{ $feedback['is_received']? __('feedbacks_table_received_status') : __('feedbacks_table_not_received_status') }}</td>
            <td>{{ $feedback['created_at']}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">{{ __('feedbacks_table_no_data_message') }}</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $feedbacks->links() }}
</div>
