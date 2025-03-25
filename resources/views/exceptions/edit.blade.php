<!-- Edit Exception Modal -->
<div class="modal fade" id="editExceptionModal" tabindex="-1" aria-labelledby="editExceptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-white">
                <h5 class="modal-title" id="editExceptionModalLabel">{{ __('edit_exception_modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('edit_exception_modal_close_button') }}"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Hibaüzenetek megjelenítése, ha vannak -->
                <div id="editModalErrors"></div>

                <!-- Edit Exception Form -->
                <form id="editExceptionForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- Hidden input az exception azonosítójához -->
                    <input type="hidden" name="exception_id" id="editExceptionId">

                    <!-- Store Selection -->
                    <div class="mb-3">
                        <label for="edit_store_id" class="form-label">{{ __('edit_exception_modal_store_label') }}</label>
                        <select name="store_id" id="edit_store_id" class="form-select" required>
                            @foreach ($storesWithPermission as $store)
                                <option value="{{ $store->store_id }}">{{ $store->domain }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">{{ __('edit_exception_modal_email_label') }}</label>
                        <input type="text" name="" id="edit_email" class="form-control" disabled>
                    </div>

                    <!-- Phone Input -->
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">{{ __('edit_exception_modal_phone_label') }}</label>
                        <input type="text" name="" id="edit_phone" class="form-control" disabled>
                    </div>

                    <!-- Type Selection -->
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">{{ __('edit_exception_modal_type_label') }}</label>
                        <select name="type" id="edit_type" class="form-select" required>
                            <option value="" disabled>{{ __('Válasszon...') }}</option>
                            <option value="allow">{{ __('edit_exception_modal_type_allow') }}</option>
                            <option value="deny">{{ __('edit_exception_modal_type_deny') }}</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">{{ __('edit_exception_modal_submit_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
