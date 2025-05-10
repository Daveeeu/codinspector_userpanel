<div class="container mt-4">
    <style>
        .card {
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.9rem;
            font-weight: 600;
            color: #343a40;
        }

        .card-title.premium {
            color: white;
        }

        .card-text {
            font-size: 0.9rem;
        }

        .card-text.premium {
            color: white !important;
        }

        .list-group-item.premium{
            color: white !important;
        }

        .badge {
            font-size: 1rem;
        }

        .btn {
            border-radius: 8px;
        }
        .selected {
            border: #007bff solid 1px;
            background-color: #f8f9fa;
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .next-package {
            border: #ef8345 solid 1px;
            background-color: #f8f9fa;

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
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 1.05rem;
        }

        .pricing-card ul li i {
            color: #5865f2;
        }

        .pricing-card ul li i.premium {
            color: #fff;
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
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .list-group-item{
            background-color: transparent;
            border: 0;
        }

        .h-95{
            height: 95% !important;
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
            color: white !important;
        }

        button.btn-primary, button.btn-secondary, button.btn-danger {
            border-radius: 50px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button.btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        button.btn-danger:hover {
            background-color: red;
            transform: scale(1.05);
        }
    </style>
    <div class="row">
        @foreach($packages as $package)

            @if($package->package_id != 4)
                <div class="col-lg-4 col-md-6 col-sm-12" wire:key="package-{{ $package->package_id }}">
                    <div class="card pricing-card shadow-sm position-relative h-95
                    {{ $package->premium ? 'premium' : '' }}
                    {{ $selectedPackageId == $package->package_id ? 'selected' : '' }}
                    {{ $nextPackageId == $package->package_id ? 'next-package' : '' }}">

                        {{-- Indicators for selected and next package --}}
                        {{--                    @if($selectedPackageId == $package->package_id)--}}
                        {{--                        <span class="position-absolute top-0 start-50 translate-middle badge bg-primary ">--}}
                        {{--                            <div class="parent-icon">--}}
                        {{--                                <i class="material-icons-outlined">check</i>--}}
                        {{--                            </div>--}}
                        {{--                        </span>--}}
                        {{--                    @endif--}}
                        {{--                    @if($nextPackageId == $package->package_id)--}}
                        {{--                        <span class="position-absolute top-0 start-50 translate-middle badge bg-warning rounded-circle p-3">--}}
                        {{--                            <i class="fas fa-hourglass-half text-white"></i>--}}
                        {{--                        </span>--}}
                        {{--                    @endif--}}

                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title text-center {{ $package->premium ? 'premium' : '' }}">{{ $package->name }}</h1>
                            <p class="card-text text-muted text-center {{ $package->premium ? 'premium' : '' }}">{{ $package->description }}</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Lekérés limit:</strong> {{ $package->query_limit }}</li>
                                @if($package->cost_per_query)
                                    <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Ár/lekérdezés:</strong> {{ $package->cost_per_query }} Ft</li>
                                @endif
                                @if($package->cost)
                                    <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Ár:</strong> {{ $package->cost }} Ft</li>
                                @endif
                            </ul>

                            {{-- Buttons for package selection or actions --}}
                            <div class="mt-auto">
                                @if($nextPackageId == $package->package_id)
                                    <button     wire:click="$dispatch('showDeleteConfirmation', { type: 1 })"
                                                class="btn btn-danger w-100 mt-3">
                                        Lemondás
                                    </button>
                                    <small class="text-warning d-block mt-2 text-center">
                                        Ez a csomag az aktuális előfizetés lejárata után lép életbe.
                                    </small>
                                @elseif($selectedPackageId == $package->package_id && $subscription->auto_renewal == false)
                                    <button wire:click="reactivateSubscription()" wire:loading.attr="disabled" class="btn btn-success w-100 mt-3">
                                        Újraaktiválás
                                        <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>

                                    </button>
                                @else
                                    <button
                                        wire:click="selectSubscription({{ $package->package_id }})" wire:loading.attr="disabled"
                                        class="btn btn-trial btn-primary w-100 mt-3
                                        {{ $package->premium ? 'premium' : '' }}"
                                        {{ $selectedPackageId == $package->package_id ? 'disabled' : '' }}>
                                        {{ $selectedPackageId == $package->package_id ? 'Kiválasztva' : 'Kiválasztás' }}
                                        <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>

                                    </button>
                                @endif

                                @if($selectedPackageId == $package->package_id && $subscription->status === 'active')
                                    @if($subscription->auto_renewal)
                                        <button     wire:click="$dispatch('showDeleteConfirmation', { type: 0 })"
                                                    wire:loading.attr="disabled" class="btn btn-trial btn-danger w-100 mt-2">
                                            Lemondás
                                            <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                                        </button>
                                    @else
                                        <small class="text-warning d-block mt-2 text-center">
                                            Az előfizetés a jelenlegi időszak végén lejár.
                                        </small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-12 mb-4" wire:key="package-{{ $package->package_id }}">
                    <div class="card pricing-card shadow-sm position-relative h-95
                    {{ $package->premium ? 'premium' : '' }}
                    {{ $selectedPackageId == $package->package_id ? 'selected' : '' }}
                    {{ $nextPackageId == $package->package_id ? 'next-package' : '' }}">

                        {{-- Indicators for selected and next package --}}
                        {{--                    @if($selectedPackageId == $package->package_id)--}}
                        {{--                        <span class="position-absolute top-0 start-50 translate-middle badge bg-primary ">--}}
                        {{--                            <div class="parent-icon">--}}
                        {{--                                <i class="material-icons-outlined">check</i>--}}
                        {{--                            </div>--}}
                        {{--                        </span>--}}
                        {{--                    @endif--}}
                        {{--                    @if($nextPackageId == $package->package_id)--}}
                        {{--                        <span class="position-absolute top-0 start-50 translate-middle badge bg-warning rounded-circle p-3">--}}
                        {{--                            <i class="fas fa-hourglass-half text-white"></i>--}}
                        {{--                        </span>--}}
                        {{--                    @endif--}}

                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title text-center {{ $package->premium ? 'premium' : '' }}">{{ $package->name }}</h1>
                            <p class="card-text text-muted text-center {{ $package->premium ? 'premium' : '' }}">{{ $package->description }}</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Lekérés limit:</strong> {{ $package->query_limit }}</li>
                                @if($package->cost_per_query)
                                    <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Ár/lekérdezés:</strong> {{ $package->cost_per_query }} Ft</li>
                                @endif
                                @if($package->cost)
                                    <li class="list-group-item {{ $package->premium ? 'premium' : '' }}"><strong>Ár:</strong> {{ $package->cost }} Ft</li>
                                @endif
                            </ul>

                            {{-- Buttons for package selection or actions --}}
                            <div class="mt-auto">
                                @if($nextPackageId == $package->package_id)
                                    <button wire:click="$dispatch('showDeleteConfirmation', { type: 1 })"
                                                class="btn btn-danger w-100 mt-3">
                                        Lemondás
                                    </button>
                                    <small class="text-warning d-block mt-2 text-center">
                                        Ez a csomag az aktuális előfizetés lejárata után lép életbe.
                                    </small>
                                @elseif($selectedPackageId == $package->package_id && $subscription->auto_renewal == false)
                                    <button wire:click="reactivateSubscription()" wire:loading.attr="disabled" class="btn btn-success w-100 mt-3">
                                        Újraaktiválás
                                        <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>

                                    </button>
                                @else
                                    <button
                                        wire:click="selectSubscription({{ $package->package_id }})" wire:loading.attr="disabled"
                                        class="btn btn-trial btn-primary w-100 mt-3
                                        {{ $package->premium ? 'premium' : '' }}"
                                        {{ $selectedPackageId == $package->package_id ? 'disabled' : '' }}>
                                        {{ $selectedPackageId == $package->package_id ? 'Kiválasztva' : 'Kiválasztás' }}
                                        <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>

                                    </button>
                                @endif

                                @if($selectedPackageId == $package->package_id && $subscription->status === 'active')
                                    @if($subscription->auto_renewal)
                                        <button     wire:click="$dispatch('showDeleteConfirmation', { type: 0 })"
                                                    wire:loading.attr="disabled" class="btn btn-trial btn-danger w-100 mt-2">
                                            Lemondás
                                            <span wire:loading class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                                        </button>
                                    @else
                                        <small class="text-warning d-block mt-2 text-center">
                                            Az előfizetés a jelenlegi időszak végén lejár.
                                        </small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- User Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success mt-3">
            <i class="fas fa-check-circle"></i> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
</div>
