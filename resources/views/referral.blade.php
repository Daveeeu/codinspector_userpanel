@extends('layouts.master')

@section('content')
    <div class="container">
        <x-page-title title="Ajánlói rendszer" pagetitle="Ajánlói rendszer"/>


    @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h3>Saját ajánlói kódod</h3>

                @if($user->referral_code)
                    <p><strong>Kód:</strong> {{ $user->referral_code }}</p>
                    <p>Oszd meg ezt a kódot másokkal, hogy jutalékot szerezhess!</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="{{ route('register') }}?ref={{ $user->referral_code }}" readonly>
                        <button class="btn btn-primary" onclick="copyToClipboard()">Másolás</button>
                    </div>
                @else
                    <form action="{{ route('referral.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Ajánlói kód igénylése</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const input = document.querySelector('.input-group input');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("A link másolva: " + input.value);
        }
    </script>
@endsection
