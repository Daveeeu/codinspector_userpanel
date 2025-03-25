@extends('layouts.master')
@section('title', 'Forrás megtekíntése')
@section('content')
    <x-page-title title="Források" pagetitle="Megtekintése" settings="store_helper" />
    <div class="container">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('store.update', $store->store_id) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Platform Selection -->
                    <div class="mb-4">
                        <label for="platform" class="form-label fw-bold">Webáruház motor típusa</label>
                        <select class="form-select" id="platform" name="platform">
                            @foreach($platforms as $platform)
                                <option value="{{ $platform['platform_id'] }}" {{ $store->platform['platform_id'] === $platform['platform_id'] ? 'selected' : '' }}>
                                    {{ $platform['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Store Name -->
                    <div class="mb-4">
                        <label for="store_name" class="form-label fw-bold">Webáruház neve</label>
                        <input type="text" id="store_name" name="store_name" class="form-control" value="{{ $store->store_name }}">
                    </div>

                    <!-- Domain -->
                    <div class="mb-4">
                        <label for="domain" class="form-label fw-bold">Domain címe</label>
                        <input type="text" id="domain" name="domain" class="form-control bg-light text-muted" value="{{ $store->domain }}" disabled>
                    </div>

                    <!-- Billing Info -->
                    <div class="mb-4">
                        <label for="billing_info" class="form-label fw-bold">Számlázási adatok</label>
                        <input type="text" id="billing_info" name="billing_info" class="form-control bg-light text-muted"
                               value="{{ $store->billingInfo->country }} {{ $store->billingInfo->city }} {{ $store->billingInfo->address }}" disabled>
                    </div>

                    <!-- Subscription Package -->
                    <div class="mb-4">
                        <label for="packages" class="form-label fw-bold">Előfizetési csomag</label>
                        <div class="d-flex align-items-center">
                            <select class="form-select me-2 bg-light text-muted" id="packages" name="packages" disabled>
                                @foreach($packages as $package)
                                    <option value="{{ $package['package_id'] }}" {{ $store->subscription->package['package_id'] === $package['package_id'] ? 'selected' : '' }}>
                                        {{ $package['name'] }} ({{ $package['cost'] }})
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('store.update.package', $id) }}" class="btn btn-outline-primary px-3 py-2">
                                <i id="editPackageIcon" class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Loss Unclaimed Package -->
                    <div class="mb-4">
                        <label for="loss_unclaimed_package" class="form-label fw-bold">Át nem vett csomag vesztesége</label>
                        <input type="text" id="loss_unclaimed_package" name="loss_unclaimed_package"
                               class="form-control"
                               value="{{ $store->lost_package_cost }}">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-2">Adatok Frissítése</button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Keys Modal -->
    <div class="modal fade" id="apiKeysModal" tabindex="-1" aria-labelledby="apiKeysModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="apiKeysModalLabel">API Kulcsok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Itt találhatók az API kulcsok:</p>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>API Key:</strong> {{ $store['api_key'] ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>API Secret:</strong> {{ $store['api_secret'] ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>URL:</strong> inspetorramburs.ro
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Video Modal -->
    <div class="modal fade" id="integrationStepModalLabel" tabindex="-1" aria-labelledby="integrationStepModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="integrationVideoModalLabel">Hogyan Integráld</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Steps Container -->
                    <div id="steps-container">
                        <!-- Step 1 -->
                        <div class="step" data-step="1">
                            <h5>Lépés 1: Jelentkezz be</h5>
                            <p>Jelentkezz be a fiókodba, majd navigálj az "Integrációk" menüpontra.</p>
                            <img src="step1.jpg" alt="Lépés 1 képe" class="img-fluid rounded">
                        </div>
                        <!-- Step 2 -->
                        <div class="step" data-step="2">
                            <h5>Lépés 2: Platform kiválasztása</h5>
                            <p>Válaszd ki a kívánt platformot az elérhető lehetőségek közül.</p>
                            <img src="step2.jpg" alt="Lépés 2 képe" class="img-fluid rounded">
                        </div>
                        <!-- Step 3 -->
                        <div class="step" data-step="3">
                            <h5>Lépés 3: API kulcs generálása</h5>
                            <p>Kattints az "API kulcs generálása" gombra, és másold ki a kapott kulcsot.</p>
                            <img src="step3.jpg" alt="Lépés 3 képe" class="img-fluid rounded">
                        </div>
                        <!-- Step 4 -->
                        <div class="step" data-step="4">
                            <h5>Lépés 4: API kulcs beillesztése</h5>
                            <p>Nyisd meg az integrációs platformot, és illeszd be az API kulcsot a megfelelő mezőbe.</p>
                            <img src="step4.jpg" alt="Lépés 4 képe" class="img-fluid rounded">
                        </div>
                        <!-- Step 5 -->
                        <div class="step" data-step="5">
                            <h5>Lépés 5: Tesztelés</h5>
                            <p>Mentsd el a beállításokat, és teszteld az integrációt a rendszer működésének ellenőrzéséhez.</p>
                            <img src="step5.jpg" alt="Lépés 5 képe" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" id="prev-btn" class="btn btn-secondary d-none">Előző</button>
                    <button type="button" id="next-btn" class="btn btn-primary">Következő</button>
                    <button type="button" id="close-btn" class="btn btn-danger d-none" data-bs-dismiss="modal">Bezárás</button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
    </style>

    <script>
        // JavaScript for Step Navigation
        document.addEventListener("DOMContentLoaded", function () {
            const steps = document.querySelectorAll(".step");
            const prevBtn = document.getElementById("prev-btn");
            const nextBtn = document.getElementById("next-btn");
            const closeBtn = document.getElementById("close-btn");

            let currentStep = 0;

            // Function to update step visibility
            function updateSteps() {
                steps.forEach((step, index) => {
                    step.classList.toggle("active", index === currentStep);
                });

                // Update button visibility
                prevBtn.classList.toggle("d-none", currentStep === 0);
                nextBtn.classList.toggle("d-none", currentStep === steps.length - 1);
                closeBtn.classList.toggle("d-none", currentStep !== steps.length - 1);
            }

            // Event listeners for buttons
            prevBtn.addEventListener("click", function () {
                if (currentStep > 0) {
                    currentStep--;
                    updateSteps();
                }
            });

            nextBtn.addEventListener("click", function () {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateSteps();
                }
            });

            // Initialize the first step
            updateSteps();
        });
    </script>
@endsection
