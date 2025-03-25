@extends('layouts.master')

@section('title', __('store_index_title'))


@section('content')
    <style>
        .bg-gradient-card {
            background: -webkit-linear-gradient(to right, #0a58ca, #0a6dca);
            background: -moz-linear-gradient(to right, #0a58ca, #0a6dca);
            background: -ms-linear-gradient(to right, #0a58ca, #0a6dca);
            background: -o-linear-gradient(to right, #0a58ca, #0a6dca);
            background: linear-gradient(to right, #0a58ca, #0a6dca);
        }
    </style>

    <x-page-title title="{{ __('store_index_title') }}" pagetitle="{{ __('store_index_sub_title') }}" settings="new_store" />
    <div class="row row-cols-1 row-cols-lg-2 g-3">
        @foreach($user->stores as $store)
            <a href="{{ route('store.details', $store['store_id']) }}">
                <div class="col">
                    <div class="card shadow-none bg-gradient-card mb-0" style="height: 160px;">
                        <div class="card-body">
                            <h5 class="mb-0 text-white">{{ $store['domain'] }}</h5>
                            <img src="{{ $store->platform['logo'] }}" class="position-absolute end-0 bottom-0 p-2" width="140" alt="{{ $store->platform['name'] }}">
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection

@section('scripts')


@endsection
