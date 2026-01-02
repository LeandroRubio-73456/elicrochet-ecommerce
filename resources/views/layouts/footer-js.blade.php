<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}" defer></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}" defer></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}" defer></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

<!-- jQuery and DataTables -->
<script src="{{ asset('assets/js/libs/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('assets/js/libs/sweetalert2.all.min.js') }}"></script>



<!-- WOW.js for scrolling animations -->
<script src="{{ asset('assets/js/libs/wow.min.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new WOW().init();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof layout_change === 'function') layout_change('light');
        if (typeof change_box_container === 'function') change_box_container('false');
        if (typeof layout_rtl_change === 'function') layout_rtl_change('false');
        if (typeof preset_change === 'function') preset_change("preset-1");
        if (typeof font_change === 'function') font_change("Public-Sans");
    });
</script>

    <script>
        // Global Toast Notification System
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{{ session('warning') }}"
                });
            @endif

            @if(session('info'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('info') }}"
                });
            @endif
        });
    </script>
