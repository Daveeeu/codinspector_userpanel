@extends('layouts.master')
@section('title', __('queries_results_title'))
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endsection
@section('content')
    <x-page-title title="{{ __('queries_results_title') }}" pagetitle="Lista"/>
    @if($storesWithPermission->isEmpty())
        @include('partials.no_permission', ['message'=> __('queries_results_no_permission_message'), 'message_1' => __('queries_results_create_new_message'), 'title' => __('queries_results_title')])
    @else
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-2 mb-3 mb-sm-0">
                                <input class="form-control" type="text" name="data" id="dataFilter" placeholder="{{ __('queries_results_email_phone_label') }}">
                            </div>
                            <div class="col-md-2 mb-3 mb-sm-0">
                                <select name="store_id" class="form-select" id="storeFilter">
                                    <option value="">{{ __('queries_results_store_label') }}</option>
                                    @foreach ($storesWithPermission as $store)
                                        <option value="{{$store->store_id}}">{{$store->domain}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-sm-0">
                                <select name="status" class="form-select" id="statusFilter">
                                    <option value="">{{ __('queries_results_status_label') }}</option>
                                    <option value="1">{{ __('queries_results_status_approved') }}</option>
                                    <option value="0">{{ __('queries_results_status_blocked') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-sm-0">
                                <input type="text" class="form-control date-range" placeholder="{{ __('queries_results_date_range_label') }}">
                            </div>
                            <div class="col-auto" id="searchButtonContainer">
                                <button type="button" class="btn btn-outline-primary px-4 d-flex gap-2" id="searchButton">
                                    <i class="material-icons-outlined">search</i>{{ __('queries_results_search_button') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="loadingSpinner" class="spinner-container" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                    <div class="spinner-border text-primary custom-spinner" role="status">
                        <span class="visually-hidden">{{ __('queries_results_loading_message') }}</span>
                    </div>
                </div>
                <div id="queryTable">
                    @include('queries.table', ['queries' => $queries])
                </div>
            </div>
        </div>
    @endif

@endsection
@section('scripts')

    <script type="text/javascript">

        function setupPaginationLinks() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const url = this.href;

                    const spinner = document.getElementById('loadingSpinner');
                    spinner.style.display = 'flex';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('queryTable').innerHTML = html;
                            setupPaginationLinks();
                        })
                        .catch(error => console.error('Hiba történt:', error))
                        .finally(() => {
                            spinner.style.display = 'none';
                        });
                });
            });
        }

        document.getElementById('searchButton').addEventListener('click', function () {
            const store_id = document.getElementById('storeFilter').value;
            const status = document.getElementById('statusFilter').value;
            const data = document.getElementById('dataFilter').value;
            const date_range = document.querySelector('.date-range').value;
            const url = "{{ route('queries.filter') }}";

            const params = new URLSearchParams({ store_id, status, data, date_range }).toString();

            const spinner = document.getElementById('loadingSpinner');
            spinner.style.display = 'flex';

            fetch(`${url}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('queryTable').innerHTML = html;
                    setupPaginationLinks();
                })
                .catch(error => console.error('Hiba történt:', error))
                .finally(() => {
                    spinner.style.display = 'none';
                });
        });

        document.getElementById('filterForm').addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>

        const today = new Date();
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);

        $(".date-range").flatpickr({
            mode: "range",
            altInput: true,
            altFormat: "Y. M j.",
            maxDate: "today",
            dateFormat: "Y-m-d",
            defaultDate: [
                oneMonthAgo.toISOString().split("T")[0],
                today.toISOString().split("T")[0]
            ],
            rangeSeparator: " - ",
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ["V", "H", "K", "Sze", "Cs", "P", "Szo"],
                    longhand: ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"]
                },
                months: {
                    shorthand: [
                        "jan", "feb", "már", "ápr", "máj", "jún",
                        "júl", "aug", "szep", "okt", "nov", "dec"
                    ],
                    longhand: [
                        "január", "február", "március", "április", "május", "június",
                        "július", "augusztus", "szeptember", "október", "november", "december"
                    ]
                },
            },
            onReady: function(selectedDates, dateStr, instance) {
                if (instance.altInput && selectedDates.length === 2) {
                    const formattedDates = selectedDates.map(date =>
                        instance.formatDate(date, instance.config.altFormat)
                    );
                    instance.altInput.value = formattedDates.join(instance.config.rangeSeparator);
                }
            },
            onValueUpdate: function(selectedDates, dateStr, instance) {
                if (instance.altInput && selectedDates.length === 2) {
                    const formattedDates = selectedDates.map(date =>
                        instance.formatDate(date, instance.config.altFormat)
                    );
                    instance.altInput.value = formattedDates.join(instance.config.rangeSeparator);
                }
            }
        });

    </script>

@endsection
