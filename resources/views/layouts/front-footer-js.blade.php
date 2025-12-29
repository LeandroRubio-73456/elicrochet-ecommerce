<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}" defer></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}" defer></script>

<!-- jQuery (Required for many plugins) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>

<!-- SweetAlert2 (Notifications) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<!-- WOW.js (Animations) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new WOW().init();
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
