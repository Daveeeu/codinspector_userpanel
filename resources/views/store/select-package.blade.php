@extends('layouts.master')
@section('title', 'Forrás megtekíntése')
@section('content')
    <livewire:select-package :id="$id" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('showDeleteConfirmation', (data) => {
                Swal.fire({
                    title: 'Biztos benne?',
                    text: 'Biztosan le szeretné mondani az előfizetését?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Igen, lemondom',
                    cancelButtonText: 'Mégsem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Közvetlen metódushívás a komponensre
                        Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).cancelSubscription(data.type);
                    }
                });
            });
        });

    </script>

@endsection
