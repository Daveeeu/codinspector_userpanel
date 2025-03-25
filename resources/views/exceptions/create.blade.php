<div class="modal fade @if ($errors->any()) show @endif" id="addExceptionModal" tabindex="-1" aria-labelledby="addExceptionModalLabel" aria-hidden="{{ $errors->any() ? 'false' : 'true' }}" style="{{ $errors->any() ? 'display: block;' : '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-white">
                <h5 class="modal-title" id="addExceptionModalLabel">{{ __('add_exception_modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5>{{ __('add_exception_modal_error_header') }}</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add Exception Form -->
                <form action="{{ route('exceptions.store') }}" method="POST">
                    @csrf
                    <!-- Store Selection -->
                    <div class="mb-3">
                        <label for="store_id" class="form-label">{{ __('add_exception_modal_store_label') }}</label>
                        <select name="store_id" id="store_id" class="form-select" required>
                            @foreach ($storesWithPermission as $store)
                                <option value="{{ $store->store_id }}" {{ old('store_id') == $store->store_id ? 'selected' : '' }}>
                                    {{ $store->domain }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('add_exception_modal_email_label') }}</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="exception@domain.com"
                               value="{{ old('email') }}" />
                    </div>

                    <!-- Phone Input -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">{{ __('add_exception_modal_phone_label') }}</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                               placeholder="+36 20 123 4567" value="{{ old('phone') }}" />
                    </div>

                    <!-- Type Selection -->
                    <div class="mb-3">
                        <label for="type" class="form-label">{{ __('add_exception_modal_type_label') }}</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="" disabled selected>{{ __('Válasszon...') }}</option>
                            <option value="allow" {{ old('type') == 'allow' ? 'selected' : '' }}>{{ __('add_exception_modal_type_allow') }}</option>
                            <option value="deny" {{ old('type') == 'deny' ? 'selected' : '' }}>{{ __('add_exception_modal_type_deny') }}</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">{{ __('add_exception_modal_submit_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
