<div>
    <style>
        .step-indicator {
            text-align: center;
        }

        @media (min-width: 1750px) {
            .col-own-4{
                flex: 0 0 auto;
                width: 33.33333333%;
            }
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: #6c757d;
            transition: background-color 0.3s, color 0.3s, transform 0.3s;
        }

        .step-circle.active {
            background-color: #007bff;
            color: #fff;
            transform: scale(1.2);
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.6);
        }

        .step-circle.completed {
            background-color: #28a745;
            color: #fff;
            transform: scale(1.1);
        }


        .step-label {
            font-size: 12px;
            max-width: 80px;
        }

        .step-line {
            flex: 1;
            height: 4px;
            background-color: #e0e0e0;
            margin-top: 18px;
        }

        .step-line.completed {
            background-color: #28a745;
        }

        .platform-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .platform-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s, transform 0.3s, background-color 0.3s;
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .platform-option:hover {
            border-color: #007bff;
            background-color: var(--bs-card-bg);
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .platform-option.selected {
            border-color: #007bff;
            background-color: var(--bs-card-bg);
            transform: scale(1.05);
        }


        .platform-option input[type="radio"] {
            display: none;
        }

        .platform-option img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .platform-option span {
            font-size: 14px;
            font-weight: 500;
        }
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }



        .card .badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        button.btn-primary, button.btn-secondary {
            border-radius: 50px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button.btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        #card-element {
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        #card-element:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .card {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media only screen and (max-width: 600px) {
            .step-label {
                display: none;
            }
        }

        .centered-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Container styling */
        .toggle-container {
            display: flex;
            align-items: center;
            position: relative;
            width: 200px;
            height: 50px;
            background-color: #e0e0e0;
            border-radius: 25px;
            padding: 5px;
            margin-bottom: 20px;
        }

        /* Hide the default radio buttons */
        .toggle-container input[type="radio"] {
            display: none;
        }

        /* Label styling */
        .toggle-container label {
            flex: 1;
            text-align: center;
            line-height: 40px;
            font-size: 16px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
        }

        /* Slider styling */
        .toggle-slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 10px);
            height: calc(100% - 10px);
            background-color: #007bff;
            border-radius: 20px;
            transition: all 0.3s ease-in-out;
        }

        /* Change text color when selected */
        .toggle-container input[type="radio"]:checked + label {
            color: #fff; /* Text color for active option */
        }

        /* Move the slider based on selection */
        #yearly:checked ~ .toggle-slider {
            left: calc(50% + 5px);
        }

    </style>
    <div class="row">
        <div class="card col mx-auto rounded-4 overflow-hidden p-4">
            <div class="d-flex justify-content-between mb-4">
                @foreach([1 => 'Webáruház típusa', 2 => 'Webáruház információ', 3 => 'Előfizetés', 4 => 'Számlázási adatok'] as $index => $label)
                    <div class="step-indicator d-flex flex-column align-items-center">
                        <div class="step-circle {{ $step == $index ? 'active' : ($step > $index ? 'completed' : '') }}">
                            {{ $index }}
                        </div>
                        <div class="step-label text-center mt-2">{{ $label }}</div>
                    </div>
                    @if($index < 4)
                        <div class="step-line {{ $step > $index ? 'completed' : '' }}"></div>
                    @endif
                @endforeach
            </div>
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="alert alert-danger d-none" id="stripe-error">

            </div>
            <!-- Step 1 -->
            @if($step == 1)
                <div class="form-group">
                    <div class="platform-container">
                        @foreach($platforms as $platform)
                            <label class="platform-option {{ $platform_id == $platform->platform_id ? 'selected' : '' }}" for="platform_{{ $platform->platform_id }}">
                                <input type="radio" wire:model="platform_id" value="{{ $platform->platform_id }}" id="platform_{{ $platform->platform_id }}"
                                       onclick="setPlatformId({{ $platform->platform_id }})">
                                <img src="{{ asset($platform->logo) }}" alt="{{ $platform->name }} logo">
                                <span>{{ $platform->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('platform_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="button" wire:click="nextStep" wire:loading.attr="disabled" class="btn btn-primary mt-4">
                    Következő
                    <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                </button>
            @endif

            <!-- Step 2 -->
            @if($step == 2)
                <div class="form-group col-md-12">
                    <label for="domain">Domain cím</label>
                    <input type="text" wire:model="domain" class="form-control @error('domain') is-invalid @enderror" required>
                    @error('domain') <span class="text-danger">{{ $message }}</span> @enderror
                </div>


                <div class="form-group">
                    <label for="lost_package_cost">Át nem vett csomag vesztesége</label>
                    <input type="number" wire:model="lost_package_cost" step="0.01" class="form-control @error('lost_package_cost') is-invalid @enderror">
                    @error('lost_package_cost') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        <button type="button" wire:click="prevStep" class="btn btn-secondary mt-4">
                            <i class="bi bi-arrow-left"></i> <!-- Icon size adjustment -->
                        </button>
                    </div>
                    <div class="col-11">
                        <button type="button"  wire:loading.attr="disabled"  wire:click="nextStep" class="btn btn-primary mt-4 w-100">
                            Következő
                            <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>

                        </button>
                    </div>
                </div>


            @endif

            <!-- Step 3 -->
            @if($step == 4)
                <div class="form-group">
                    <label for="company_name">Cégnév</label>
                    <input type="text" wire:model="company_name" class="form-control @error('company_name') is-invalid @enderror" required>
                    @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="tax_id">Adószám</label>
                    <input type="text" wire:model="tax_id" class="form-control @error('tax_id') is-invalid @enderror" required>
                    @error('tax_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="country">Ország</label>
                    <input type="text" wire:model="country" class="form-control @error('country') is-invalid @enderror" required>
                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="postal_code">Irányítószám</label>
                    <input type="text" wire:model="postal_code" class="form-control @error('postal_code') is-invalid @enderror" required>
                    @error('postal_code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="city">Város</label>
                    <input type="text" wire:model="city" class="form-control @error('city') is-invalid @enderror" required>
                    @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="address">Cím</label>
                    <input type="text" wire:model="address" class="form-control @error('address') is-invalid @enderror" required>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>


                <style>
                    .stripe-payment-container {
                        display: flex;
                        flex-direction: column;
                        gap: 0.75rem;
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    }

                    .payment-method-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 0.5rem;
                    }

                    .payment-method-label {
                        font-weight: 600;
                        font-size: 1.1rem;
                        color: #1a1a1a;
                        margin: 0;
                    }

                    .stripe-badge {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.25rem 0.5rem;
                        background-color: #f8f9fa;
                        border: 1px solid #e9ecef;
                        border-radius: 4px;
                        font-size: 0.75rem;
                        color: #6c757d;
                    }

                    .stripe-logo {
                        height: 16px;
                        width: auto;
                        opacity: 0.8;
                    }

                    #card-element {
                        padding: 1rem;
                        border: 1px solid #e1e8ed;
                        border-radius: 8px;
                        background: #ffffff;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        transition: all 0.2s ease;
                    }

                    #card-element:focus-within {
                        border-color: #5167FC;
                        box-shadow: 0 0 0 3px rgba(81, 103, 252, 0.1);
                    }

                    .payment-security-note {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: #6c757d;
                        font-size: 0.875rem;
                        margin-top: 0.5rem;
                    }

                    .security-icon {
                        width: 16px;
                        height: 16px;
                        opacity: 0.6;
                    }
                </style>

                <div class="form-group mt-4 stripe-payment-container" wire:ignore>
                    <div class="payment-method-header">
                        <label for="payment_method" class="payment-method-label">Fizetési mód</label>
                        <div class="stripe-badge">
                            <svg class="stripe-logo" viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#5167FC" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.04.8-.06 1.48zm-5.92-5.62c-1.03 0-2.17.73-2.17 2.58h4.25c0-1.85-1.07-2.58-2.08-2.58zM40.95 20.3c-1.44 0-2.32-.6-2.9-1.04l-.02 4.63-4.12.87V5.57h3.76l.08 1.02a4.7 4.7 0 0 1 3.23-1.29c2.9 0 5.62 2.6 5.62 7.4 0 5.23-2.7 7.6-5.65 7.6zM40 8.95c-.95 0-1.54.34-1.97.81l.02 6.12c.4.44.98.78 1.95.78 1.52 0 2.54-1.65 2.54-3.87 0-2.15-1.04-3.84-2.54-3.84zM28.24 5.57h4.13v14.44h-4.13V5.57zm0-4.7L32.37 0v3.36l-4.13.88V.88zm-4.32 9.35v9.79H19.8V5.57h3.7l.12 1.22c1-1.77 3.07-1.41 3.62-1.22v3.79c-.52-.17-2.29-.43-3.32.86zm-8.55 4.72c0 2.43 2.6 1.68 3.12 1.46v3.36c-.55.3-1.54.54-2.89.54a4.15 4.15 0 0 1-4.27-4.24l.01-13.17 4.02-.86v3.54h3.14V9.1h-3.13v5.85zm-8.78.9c0 3.75-2.85 7.6-7.75 7.6C-4.8 23.44-7.5 19.6-7.5 15.76c0-3.84 2.8-7.6 7.75-7.6 4.8 0 7.5 3.85 7.5 7.6zm-4.51-.17c0-2.58-1.05-3.64-2.98-3.64-1.98 0-3.02 1.18-3.02 3.64 0 2.63 1.03 3.8 3.02 3.8 1.93 0 2.98-1.17 2.98-3.8z"/>
                            </svg>
                            <span>Powered by</span>
                        </div>
                    </div>

                    <div id="card-element"></div>

                    <div class="payment-security-note">
                        <svg class="stripe-logo" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" xml:space="preserve" y="0" x="0" id="Layer_1" version="1.1" viewBox="-54 -37.45 468 224.7"><style id="style16" type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#32325d}</style><g transform="translate(-54 -36)" id="g32"><path id="path18" d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5v-20c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-8.9zm-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16z" class="st0"/><path id="path20" d="M301.1 67.6c-9.8 0-16.1 4.6-19.6 7.8l-1.3-6.2h-22v116.6l25-5.3.1-28.3c3.6 2.6 8.9 6.3 17.7 6.3 17.9 0 34.2-14.4 34.2-46.1-.1-29-16.6-44.8-34.1-44.8zm-6 68.9c-5.9 0-9.4-2.1-11.8-4.7l-.1-37.1c2.6-2.9 6.2-4.9 11.9-4.9 9.1 0 15.4 10.2 15.4 23.3 0 13.4-6.2 23.4-15.4 23.4z" class="st0"/><path id="polygon22" class="st0" d="M248.9 36l-25.1 5.3v20.4l25.1-5.4z"/><path id="rect24" class="st0" d="M223.8 69.3h25.1v87.5h-25.1z"/><path id="path26" d="M196.9 76.7l-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7 15.9-6.3 19-5.2v-23c-3.2-1.2-14.9-3.4-20.8 7.4z" class="st0"/><path id="path28" d="M146.9 47.6l-24.4 5.2-.1 80.1c0 14.8 11.1 25.7 25.9 25.7 8.2 0 14.2-1.5 17.5-3.3V135c-3.2 1.3-19 5.9-19-8.9V90.6h19V69.3h-19z" class="st0"/><path id="path30" d="M79.3 94.7c0-3.9 3.2-5.4 8.5-5.4 7.6 0 17.2 2.3 24.8 6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6C67.5 67.6 54 78.2 54 95.9c0 27.6 38 23.2 38 35.1 0 4.6-4 6.1-9.6 6.1-8.3 0-18.9-3.4-27.3-8v23.8c9.3 4 18.7 5.7 27.3 5.7 20.8 0 35.1-10.3 35.1-28.2-.1-29.8-38.2-24.5-38.2-35.7z" class="st0"/></g></svg>
                        <span>A kártyaadatokat a Stripe biztonságosan kezeli.</span>
                    </div>

                    @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
                </div>



                <!-- Kuponkód mező -->
                <div class="form-group mt-4">
                    <label for="coupon_code">Kuponkód</label>
                    <input
                        type="text"
                        id="coupon_code"
                        wire:model="coupon_code"
                        class="form-control @error('coupon_code') is-invalid @enderror"
                        placeholder=""
                    >
                    @error('coupon_code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <!-- Gomb a kupon érvényesítésére -->
                <button
                    type="button"
                    wire:click="applyCoupon"
                    class="btn btn-outline-primary mt-2"
                >
                    Kupon érvényesítése
                </button>

                <!-- Kupon visszajelzés -->
                @if(session('coupon_success'))
                    <div class="alert alert-success mt-2">
                        {{ session('coupon_success') }}
                    </div>
                @endif

                @if(session('coupon_error'))
                    <div class="alert alert-danger mt-2">
                        {{ session('coupon_error') }}
                    </div>
                @endif



                <div class="row mt-4">
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        <button type="button" wire:click="prevStep" class="btn btn-secondary mt-2">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                    </div>
                    <div class="col-11">
                        <button
                            type="button"
                            onclick="f1()"
                            wire:loading.attr="disabled"
                            class="btn btn-primary mt-2 w-100 position-relative"
                        >
                            Forrás hozzáadása
                            <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            @endif

            @if($step == 3)
                <style>
                    .pricing-header {
                        text-align: center;
                        margin-bottom: 2rem;
                    }

                    .pricing-header h1 {
                        font-size: 2rem;
                        font-weight: 700;
                    }

                    .pricing-header p {
                        color: #6c757d;
                        margin-top: 0.5rem;
                    }

                    .toggle-switch {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 0.5rem;
                        margin-bottom: 2rem;
                    }

                    .toggle-switch span {
                        font-weight: 600;
                    }

                    .pricing-card {
                        border: none;
                        border-radius: 15px;
                        padding: 2rem;
                        text-align: center;
                        background-color: var(--bs-secondary-bg);
                        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
                    }
                    .pricing-card {
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        height: 100%; /* Egyforma magasság */
                    }

                    .pricing-card.premium {
                        background-color: #5865f2;
                        color: #fff;
                    }

                    .pricing-card h5 {
                        font-weight: 700;
                    }

                    .premium-color, .premium-color small{
                        color: #fff !important;
                    }

                    .price {
                        font-size: 2.5rem;
                        font-weight: 700;
                        margin: 1rem 0;
                    }

                    .price small {
                        font-size: 1rem;
                        color: #6c757d;
                    }

                    .pricing-card ul {
                        list-style: none;
                        padding: 0;
                        margin: 1rem 0;
                    }

                    .pricing-card ul li {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        margin-bottom: 0.5rem;
                        font-size: 0.95rem;
                    }

                    .pricing-card ul li i {
                        color: #5865f2;
                    }

                    .pricing-card ul li i.premium {
                        color: #fff;
                    }

                    .btn-trial {
                        margin-top: 1rem;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        font-weight: 600;
                    }

                    .btn-trial.premium {
                        background-color: #00b2ff;
                        border: none;
                    }
                    .toggle-switch {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                    }

                    .toggle-switch span {
                        font-weight: 600;
                    }

                    .switch {
                        position: relative;
                        display: inline-block;
                        width: 50px;
                        height: 25px;
                    }

                    .switch input {
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }

                    .slider {
                        position: absolute;
                        cursor: pointer;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: #ccc;
                        transition: 0.4s;
                        border-radius: 25px;
                    }

                    .slider::before {
                        position: absolute;
                        content: "";
                        height: 19px;
                        width: 19px;
                        left: 3px;
                        bottom: 3px;
                        background-color: white;
                        transition: 0.4s;
                        border-radius: 50%;
                    }

                    input:checked + .slider {
                        background-color: #5865f2; /* Kék szín */
                    }

                    input:checked + .slider::before {
                        transform: translateX(24px);
                    }

                    .save {
                        color: #5865f2; /* Kék szín */
                        font-weight: bold;
                    }
                    .pricing-card {
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        height: 100%; /* Azonos magasság minden kártyának */
                    }

                    .pricing-card .card-body {
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        height: 100%;
                    }

                    .pricing-card p {
                        min-height: 70px; /* Egységes magasság a leírásokhoz */
                        margin: 0; /* Eltávolítja az extra margókat */
                        text-align: left; /* Balra igazítás */
                    }

                </style>


                <!-- Toggle Switch -->
                <div class="toggle-switch">
                    <span>Bill Monthly</span>
                    <label class="switch">
                        <input type="checkbox" id="billingToggle" wire:click="togglePaymentFrequency"
                            {{ $paymentFrequency === 'annually' ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <span>Bill Annually</span>
                    <span class="save">Save 15%</span>
                </div>

                <!-- Pricing Cards -->
                <div class="row gy-4">
                    <!-- Standard Plan -->
                    @foreach($packages as $index => $package)
                        @if($index === 3)
                            <div class="{{ $index === 3 ? 'col-12' : 'col-md-4' }}">
                                <div class="card pricing-card {{ $selectedPackageId == $package->package_id ? 'border-primary shadow-lg' : '' }} {{ $package->premium ? 'premium' : '' }}">
                                    <div class="row">
                                        <!-- Bal oldal: Cím, Leírás, Ár -->
                                        <div class="col-md-6">
                                            <!-- Cím -->
                                            <h5 class="{{ $package->premium ? 'premium-color' : '' }} text-start">{{$package->name}}</h5>

                                            <!-- Leírás -->
                                            <p class="text-start">{{$package->description}}</p>

                                            <!-- Ár -->
                                            @if(floatval($package->cost_per_query) > 0)
                                                <p style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_per_query }} <small>Ár/lekérdezés</small></p>
                                            @endif
                                            @if(floatval($package->cost) > 0)
                                                @if($paymentFrequency === 'monthly')
                                                    <div style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost }} <small>per month</small></div>
                                                @else
                                                    <div style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_yearly }} <small>per year</small></div>
                                                @endif
                                            @endif

                                        </div>

                                        <!-- Jobb oldal: Lista -->
                                        <div class="col-md-6">
                                            <ul class="mt-3">
                                                @foreach($package->features as $feature)
                                                    <li><i class="bi bi-check-circle {{ $package->premium ? 'premium' : '' }}"></i>{{$feature['name']}}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Gomb -->
                                    <button
                                        wire:click="selectSubscription({{ $package->package_id }})"
                                        class="btn btn-primary btn-trial {{ $selectedPackageId == $package->package_id ? 'btn-primary' : 'btn-primary' }} w-100 mt-auto  {{ $package->premium ? 'premium' : '' }}"
                                        {{ $selectedPackageId == $package->package_id ? 'disabled' : '' }}>
                                        {{ $selectedPackageId == $package->package_id ? 'Kiválasztva' : 'Kiválasztás' }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="col-md-4">
                                <div class="card pricing-card {{ $selectedPackageId == $package->package_id ? 'border-primary shadow-lg' : '' }} {{ $package->premium ? 'premium' : '' }}">
                                    <!-- Cím -->
                                    <h5 class="{{ $package->premium ? 'premium-color' : '' }} text-start">{{$package->name}}</h5>

                                    <!-- Leírás -->
                                    <p class="text-start">{{$package->description}}</p>

                                    <!-- Ár -->
                                    @if(floatval($package->cost_per_query) > 0)
                                        <p class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_per_query }} <small>Ár/lekérdezés</small></p>
                                    @endif
                                    @if(floatval($package->cost) > 0)
                                        @if($paymentFrequency === 'monthly')
                                            <div class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost }} <small>per month</small></div>
                                        @else
                                            <div class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_yearly }} <small>per year</small></div>
                                        @endif
                                    @endif

                                    <!-- Lista -->
                                    <ul class="mt-3">
                                        @foreach($package->features as $feature)
                                            @if($feature->is_included)
                                                <li><i class="bi bi-check-circle {{ $package->premium ? 'premium' : '' }}"></i>{{$feature['name']}}</li>
                                            @else
                                                <li><i class="bi bi-x-circle {{ $package->premium ? 'premium' : '' }} text-danger"></i>{{$feature['name']}}</li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    <!-- Gomb -->
                                    <button
                                        wire:click="selectSubscription({{ $package->package_id }})"
                                        class="btn btn-primary btn-trial {{ $selectedPackageId == $package->package_id ? 'btn-primary' : 'btn-primary' }} w-100 mt-auto  {{ $package->premium ? 'premium' : '' }}"
                                        {{ $selectedPackageId == $package->package_id ? 'disabled' : '' }}>
                                        {{ $selectedPackageId == $package->package_id ? 'Kiválasztva' : 'Kiválasztás' }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
                <!-- Navigation Buttons -->
                <div class="row">
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        <button type="button" wire:click="prevStep" class="btn btn-secondary mt-4">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                    </div>
                    <div class="col-11">
                        <button type="button" wire:loading.attr="disabled" wire:click="nextStep" class="btn btn-primary mt-4 w-100">
                            Következő
                            <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            @endif






        </div>


        <div class="col-own-4 sidebar-card">
            <div class="card p-4">
                <div class="mb-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-shield-fill-check fs-3 text-success me-3"></i>
                        <div>
                            <h6 class="fw-bold">Biztonságos fizetés</h6>
                            <p class="mb-0">
                                A mentett kártyaadataidat mi nem, kizárólag a megjelölt fizetési szolgáltató tárolja.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-start">
                        <i class="bi bi-question-circle-fill fs-3 text-primary me-3"></i>
                        <div>
                            <h6 class="fw-bold">Segítségre van szükséged?</h6>
                            <p class="mb-1">
                                Írj nekünk a <a href="mailto:info@inspectorramburs.com">info@inspectorramburs.com</a>-ra, vagy keress minket a <a href="tel:+36209238883">+36 20 923 8883</a> telefonszámon!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function setPlatformId(platformId) {
            @this.set('platform_id', platformId);
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('stepUpdated', () => {
                    setTimeout(() =>{
                        if(document.getElementById('card-element')){
                                f()
                        }
                    }, 300)
            });
        })
    </script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        let stripe;
        let elements;
        let cardElement;
        let created = false;
       function f() {
           stripe = Stripe('{{ config('services.stripe.key') }}');
           elements = stripe.elements();
           cardElement = elements.create('card');
           cardElement.mount('#card-element');
       }

        async function f1() {
            const {paymentMethod, error} = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                const errorDiv = document.getElementById('stripe-error');
                errorDiv.innerText = error.message;
                errorDiv.classList.remove('d-none');
            } else {
                const errorDiv = document.getElementById('stripe-error');
                errorDiv.classList.add('d-none');
                created = true
                @this.call('storePaymentMethod', paymentMethod.id);
            }
        }

        window.addEventListener('init-stripe-payment', event => {
            setTimeout(() => {
                f();
            }, 300);
        })
        window.addEventListener('store-created', event => {
            Swal.fire({
                title: 'Mielőtt elkezdené használni szolgáltatásunkat, győződjön meg arról, hogy mindennek megfelel.',
                text: "Jogi követelmények tájékoztatása – A rendszer részletes útmutatást ad arról, hogy milyen módosításokat és kiegészítéseket kell elvégezni a webáruház ÁSZF és Adatvédelmi Tájékoztatójában, hogy megfeleljen a szolgáltatás használatának feltételeinek.",
                icon: 'warning',
                theme: 'auto',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('store.index') }}";
            });
        });



    </script>


</div>
