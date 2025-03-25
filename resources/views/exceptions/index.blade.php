@extends('layouts.master')
@section('title', 'Kivételek')
@section('css')
    <link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        /* General styles */
        .input-group .btn i {
            margin-right: 5px; /* Space between icon and text */
        }

        /* Mobile-specific styles */
        @media (max-width: 768px) {
            #searchButton i {
                margin-right: 0; /* Remove margin for mobile */
            }

            .btn-success .desktop-text {
                display: none; /* Hide "Hozzáadás" text on mobile */
            }

            .btn-success .mobile-icon {
                display: inline-block !important; /* Show "+" icon on mobile */
            }
        }
    </style>
@endsection
@section('content')
    <x-page-title title="Kivétel kezelő" pagetitle="Kivétel kezelő"/>
    @if($storesWithPermission->isEmpty())
        @include('partials.no_permission', ['message'=> 'Nincs olyan forrásod a rendszerben, amely a megfelelő jogosultságokkal rendelkezik ennek az oldalnak a megtekintésére.', 'message_1' => '                Hozz létre új forrást, vagy válts csomagot a már meglévő forrásodnál a kivételek kezelő használatához!', 'title' => 'Kivétel kezelő'])
    @else
        <div class="card">
            <div class="card-body">

<div class="mb-3">
    <form id="filterForm">
        <div class="row">
            <div class="col-md-2 mb-3 mb-sm-0">
                <input class="form-control" type="text" name="data" id="searchInput" placeholder="Email vagy telefon">
            </div>
            <div class="col-md-2 mb-3 mb-sm-0">
                <select name="store_id" class="form-select" id="storeFilter">
                    <option value="">Összes forrás</option>
                    @foreach ($storesWithPermission as $store)
                        <option value="{{$store->store_id}}">{{$store->domain}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3 mb-sm-0">
                <select name="is_received" class="form-select" id="receivedFilter">
                    <option value="">Összes típus</option>
                    <option value="allow">Engedélyezve</option>
                    <option value="deny">Letiltva</option>
                </select>
            </div>
            <div class="col-auto" id="searchButtonContainer">
                <button type="button" class="btn btn-outline-primary px-4 d-flex gap-2" id="searchButton">
                    <i class="material-icons-outlined">search</i>Keresés
                </button>
            </div>
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExceptionModal">
                    <span class="desktop-text">Hozzáadás</span>
                    <span class="mobile-icon d-none">
                                +
                            </span> <!-- Plus icon -->
                </button>
            </div>
        </div>
    </form>
</div>
                    <div id="loadingSpinner" class="spinner-container" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">
                        <div class="spinner-border text-primary custom-spinner" role="status">
                            <span class="visually-hidden">Betöltés...</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="exceptionTable">
                            @include('exceptions.table', ['exceptions' => $exceptions])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Exception Modal -->
            @include('exceptions.create')

            <!-- Edit Exception Modal -->
            @include('exceptions.edit')

        </div>
    @endif



    @if ($errors->any())
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                var addExceptionModal = new bootstrap.Modal(document.getElementById('addExceptionModal'));
                addExceptionModal.show();
            });
        </script>
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
                            document.getElementById('exceptionTable').innerHTML = html;
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
            const search_input = document.getElementById('searchInput').value;
            const store_id = document.getElementById('storeFilter').value;
            const type = document.getElementById('receivedFilter').value;

            const url = "{{ route('exceptions.filter') }}";

            const params = new URLSearchParams({ search_input, store_id, type }).toString();

            const spinner = document.getElementById('loadingSpinner');
            spinner.style.display = 'flex';
            fetch(`${url}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('exceptionTable').innerHTML = html;
                    setupPaginationLinks();
                })
                .catch(error => console.error('Hiba történt:', error))
                .finally(() => {
                    spinner.style.display = 'none';
                });
        });


    </script>

    <script>

        function confirmDelete(id) {
            Swal.fire({
                title: "Biztosan törölni szeretnéd?",
                text: "Ez a művelet nem vonható vissza!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Igen, törlöm!",
                cancelButtonText: "Mégsem"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>

    <script>
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                // Lekérjük az adatokat a data attribútumokból
                const exceptionId = this.getAttribute('data-id');
                const storeId = this.getAttribute('data-store_id');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const type = this.getAttribute('data-type');

                // Kitöltjük a modal mezőit
                document.getElementById('editExceptionId').value = exceptionId;
                document.getElementById('edit_store_id').value = storeId;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_phone').value = phone;
                document.getElementById('edit_type').value = type;

                // Beállítjuk a form action URL-jét az update route-hoz
                const form = document.getElementById('editExceptionForm');
                form.action = `/exceptions/${exceptionId}`; // Győződj meg róla, hogy ez az URL megfelel a routes/web.php beállításaidnak
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: "Biztosan törölni szeretnéd?",
                text: "Ez a művelet nem vonható vissza!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Igen, törlöm!",
                cancelButtonText: "Mégsem"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
@endsection
