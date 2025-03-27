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


                <div>
                    <!-- Fizetési mód -->
                    <div class="form-group mt-4" wire:ignore>
                        <label for="payment_method">Fizetési mód</label>
                        <div id="card-element"></div>
                        <small class="text-muted">A kártyaadatokat a Stripe biztonságosan kezeli.</small>
                        @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>




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
                                            @if($package->cost_per_query)
                                                <p style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_per_query }} <small>Ár/lekérdezés</small></p>
                                            @endif
                                            @if($package->cost)
                                                @if($paymentFrequency === 'monthly')
                                                    <div style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost }} <small>per month</small></div>
                                                @else
                                                    <div  style="padding:0;margin:0;" class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_yearly }} <small>per year</small></div>
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
                                    @if($package->cost_per_query)
                                        <p class="price {{ $package->premium ? 'premium-color' : '' }} text-start">{{ $package->cost_per_query }} <small>Ár/lekérdezés</small></p>
                                    @endif
                                    @if($package->cost)
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
