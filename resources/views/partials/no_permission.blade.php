@extends('layouts.master')

@section('title', $title)

@section('content')
    <x-page-title title={{$title}} pagetitle="{{ __('no_permission') }}"/>
    <div class="card">
        <div class="card-body">
            <div>
                {!! $message !!}
            </div>
            <div>
                {!! $message_1 !!}
            </div>
        </div>
    </div>

@endsection
@section('scripts')


@endsection
