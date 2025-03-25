@extends('layouts.master')
@section('title', 'Kézi lekérdező')
@section('css')
    <link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --text-color: #212529;
            --highlight-color: #0056b3;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Roboto', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--highlight-color);
            border-color: var(--highlight-color);
        }

        .table {
            border: 1px solid #dee2e6;
        }

        .table th {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #e9ecef;
        }

        .radio-group {
            width: 100%;
            gap: 10px; /* Térköz az elemek között */
        }

        .radio-item {
            position: relative;
        }

        .radio-item input[type="radio"] {
            display: none; /* Elrejtjük az alapértelmezett rádiógombot */
        }

        .radio-item label {
            display: inline-block;
            width: 100%;
            padding: 10px;
            border: 1.2px solid var(--primary-color);
            border-radius: 8px;
            background-color: var(--bs-card-bg);
            color: var(--primary-color);
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .radio-item input[type="radio"]:checked + label {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
        }


        /* Kártyák háttérszínei */
        .bg-success {
            background-color: #e6f4ea; /* Világos zöld */
        }

        .bg-danger {
            background-color: #fce8e6; /* Világos piros */
        }

        /* Szöveg színek */
        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Kártya stílus */
        .card-result {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

    .filter-results-text{
        color: #5b6166 !important;
    }

    </style>
@endsection
@section('content')
    <x-page-title title="{{ __('manual_query_title') }}" pagetitle="{{ __('manual_query_title') }}"/>
    @if($storesWithPermission->isEmpty())
        @include('partials.no_permission', ['message'=> __('manual_query_no_permission_message'), 'message_1' => __('manual_query_create_new_message'), 'title' => __('manual_query_title')])
    @else

        <!-- Űrlap -->
        <form method="POST" action="{{ route('manual-query.check-email') }}">
            @csrf
            <div class="row mb-4">
                <div class="col-md-12 p-0">
                    <label for="dataList" class="form-label fw-semibold">{{ __('manual_query_email_phone_label') }} <span class="text-danger">*</span></label>
                    <textarea id="dataList" name="data" rows="8" class="form-control @error('data') is-invalid @enderror" placeholder="{{ __('manual_query_email_phone_placeholder') }}">{{ old('data', session('form_data')) }}</textarea>
                    <small class="text-muted">{{ __('manual_query_email_phone_sub_label') }}</small>
                    @error('data')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Küszöbérték -->
            <div class="row mb-4">
                <div class="col-md-12 card p-2">
                    <label class="form-label fw-semibold">{{ __('manual_query_threshold_label') }}</label>
                    <p class="text-muted">{{ __('manual_query_threshold_description') }}</p>

                    <!-- Rádiógombok -->
                    <div class="radio-group d-flex justify-content-between">
                        @foreach (['Nincs szűrés', 'Elnéző', 'Engedékeny', 'Szigorú', 'Nagyon szigorú', 'Egyedi'] as $key => $option)
                            <div class="radio-item flex-grow-1 text-center">
                                <input
                                    type="radio"
                                    id="threshold_{{ $key }}"
                                    name="threshold"
                                    value="{{ strtolower($option) }}"
                                    class="@error('threshold') is-invalid @enderror"
                                    data-description="
                    @if ($option === 'Nincs szűrés')
                        <strong>{{ __('manual_query_no_filtering_description') }}</strong><br> {!! __('manual_query_no_filtering_detail') !!}
                    @elseif ($option === 'Elnéző')
                        <strong>{{ __('manual_query_lenient_description') }}</strong><br> {!! __('manual_query_lenient_detail') !!}
                    @elseif ($option === 'Engedékeny')
                        <strong>{{ __('manual_query_permissive_description') }}</strong><br> {!! __('manual_query_permissive_detail') !!}
                    @elseif ($option === 'Szigorú')
                        <strong>{{ __('manual_query_strict_description') }}</strong><br> {!! __('manual_query_strict_detail') !!}
                    @elseif ($option === 'Nagyon szigorú')
                        <strong>{{ __('manual_query_very_strict_description') }}</strong><br> {!! __('manual_query_very_strict_detail') !!}
                    @else
                        <strong>{{ __('manual_query_custom_description') }}</strong><br> {!! __('manual_query_custom_detail') !!}
                    @endif
                "
                                    {{ (strtolower($option) === (old('threshold')  ? old('threshold') : "")) ? 'checked' : ( session('threshold') === $option ? 'checked' : '') }}
                                    {{ (is_null(old('threshold')) && is_null(session('threshold')) && strtolower($option) === "szigorú") ? 'checked' : '' }}
                                > <label for="threshold_{{ $key }}" class="w-100">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('threshold')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <!-- Magyarázó szöveg -->
                    <div id="threshold-description" class="mt-3 p-3 bg-light text-primary rounded primary-border">
                        <strong>{{ __('manual_query_strict_description') }}</strong><br>
                        {{ __('manual_query_strict_detail') }}
                    </div>

                    <div id="custom-threshold-container" class="mt-3" style="display: none;">
                        <label for="custom-threshold" class="form-label fw-semibold">{{ __('manual_query_custom_threshold_label') }}</label>
                        <input type="number" id="custom-threshold" name="custom_threshold" class="form-control @error('custom_threshold') is-invalid @enderror" placeholder="{{ __('manual_query_custom_threshold_placeholder') }}" step="0.1" value="{{session('threshold') === 'Egyedi' ? session('thresholdValue') : ''}}">
                        @error('custom_threshold')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Gomb -->
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">{{ __('manual_query_start_check_button') }}</button>
                </div>
            </div>
        </form>

        <!-- Eredmények -->
        <div class="container my-5">
            @if (session('results'))
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2>{{ __('manual_query_results_title') }}</h2>
                        <p><strong>{{ __('manual_query_threshold_info') }}</strong> {{ session('threshold') }}</p>

                        <!-- Eredmények listája -->
                        @foreach (session('results') as $result)
                            <div class="d-flex justify-content-between align-items-center p-3 mb-3 rounded"
                                 style="background-color:
                                 {{ $result['error'] ? '#fce8e6' : (
                                        $result['isAboveThreshold'] ? '#e6f4ea' : (
                                            $result['reputation'] == 0.00 ? '#f4f4e6':'#fce8e6')
                                            )
                                         }};
                                 ">
                                <!-- E-mail cím és hash -->

                                @if($result['error'])
                                    <div>
                                        <p class="mb-1 filter-results-text"><strong>{{ __('manual_query_invalid_data') }}</strong></p>
                                        <p class="small filter-results-text">{{ $result['data'] }}</p>
                                    </div>
                                @elseif($result['reputation'] == 0)
                                    <div>
                                        <p class="mb-1 filter-results-text"><strong>{{ __('manual_query_no_data') }}</strong></p>
                                        <p class="small filter-results-text">{{ $result['data'] }}</p>
                                    </div>
                                @else
                                    <div>
                                        <p class="mb-1 filter-results-text"><strong>{{ $result['data'] }}</strong></p>
                                        <p class="small filter-results-text">{{ $result['hash'] }}</p>
                                    </div>
                                    <!-- Reputáció -->
                                    <div>
                                        <p class="mb-1 filter-results-text"><strong>{{ __('manual_query_reputation_label') }}</strong> {{ number_format($result['reputation'], 2) }}</p>
                                    </div>

                                    <!-- Átvételi arány -->
                                    <div class="text-end">
                                        <p class="mb-1" style="font-size: 1.2rem; font-weight: bold; color: {{ $result['isAboveThreshold'] ? '#28a745' : '#dc3545' }};">
                                            {{ $result['delivery_rate'] }}%
                                        </p>
                                        <p class="small filter-results-text">
                                            {{ __('manual_query_accepted_rejected_label', ['accepted' => $result['accepted'], 'rejected' => $result['rejected']]) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- Összesítés -->
                        <p class="text-muted text-end">{{ __('manual_query_summary_label', ['count' => count(session('results')), 'total' => session('totalEmails')]) }}</p>
                    </div>
                </div>
            @endif
        </div>



    @endif



@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('input[name="threshold"]');
            const descriptionBox = document.getElementById('threshold-description');
            const customThresholdContainer = document.getElementById('custom-threshold-container');

            // Funkció a magyarázó szöveg frissítésére
            function updateDescription(selectedRadio) {
                const description = selectedRadio.getAttribute('data-description').trim();
                descriptionBox.innerHTML = `${description}`;

                // Ha "Egyedi" van kiválasztva, mutassuk meg az input mezőt
                if (selectedRadio.value === 'egyedi') {
                    customThresholdContainer.style.display = 'block';
                } else {
                    customThresholdContainer.style.display = 'none';
                }
            }

            // Oldal betöltésekor frissítjük az alapértelmezett értéket
            const checkedRadio = document.querySelector('input[name="threshold"]:checked');
            if (checkedRadio) {
                updateDescription(checkedRadio);
            }

            // Eseménykezelő minden rádiógombra
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.checked) {
                        updateDescription(this);
                    }
                });
            });
        });


    </script>
@endsection
