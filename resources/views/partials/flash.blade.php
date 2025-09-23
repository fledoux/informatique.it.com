<div id="kt_docs_toast_stack_container" class="position-fixed top-0 start-0 p-2 w-50 mb-5" style="z-index: 1050;">
    <div class="alert border-0 shadow-sm p-4 d-flex align-items-center" role="alert" aria-live="assertive"
        aria-atomic="true" data-kt-docs-toast="stack">
        <i class="toast-picto me-2"></i>
        <span class="toast-message flex-grow-1 h6 mb-0"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>


<style>
    .alert[data-kt-docs-toast] {
        animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    const container = document.getElementById('kt_docs_toast_stack_container');
    const targetElement = document.querySelector('[data-kt-docs-toast="stack"]');
    targetElement.parentNode.removeChild(targetElement);

    function toastme(msg, color, picto) {
        const newToast = targetElement.cloneNode(true);
        container.append(newToast);
        $(newToast).find('.toast-message').html(msg);
        $(newToast).addClass('alert-' + color);
        $(newToast).find('.toast-picto').addClass('fa-regular ' + picto + ' text-' + color);
        
        // Auto-hide après 5 secondes
        setTimeout(() => {
            $(newToast).fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
</script>

@if (session('error'))
    <script>
        toastme('{!! addslashes(session('error')) !!}', 'danger', 'fa-ban');
    </script>
@endif

@if (session('warning'))
    <script>
        toastme('{!! addslashes(session('warning')) !!}', 'warning', 'fa-circle-exclamation');
    </script>
@endif

@if (session('info'))
    <script>
        toastme('{!! addslashes(session('info')) !!}', 'info', 'fa-circle-info');
    </script>
@endif

@if (session('success'))
    <script>
        toastme('{!! addslashes(session('success')) !!}', 'success', 'fa-circle-check');
    </script>
@endif
