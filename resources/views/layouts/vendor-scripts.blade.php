<script src="{{ URL::asset('build/js/bootstrap.bundle.min.js') }}"></script>

<!--plugins-->
<script src="{{ URL::asset('build/js/jquery.min.js') }}"></script>
<!--plugins-->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ URL::asset('build/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ URL::asset('build/plugins/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/js/main.js') }}"></script>
<script src="{{ URL::asset('build/js/notification.js') }}"></script>


<script>

    toastr.options = {
        "positionClass": "toast-bottom-right",
    };

    function showToastMessages() {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    }

    // Amikor a DOM betöltődik, meghívjuk a függvényt
    document.addEventListener('DOMContentLoaded', function() {
        showToastMessages();
    });


    var translations = {
        subscription_reminder: "{{ __('subscription_reminder') }}",
        subscription_reminder_description: "{{ __('subscription_reminder_description') }}",
    };
</script>
