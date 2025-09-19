{{-- Flash messages --}}
@php
$types = ['success', 'info', 'warning', 'danger'];
$icons = [
    'success' => 'fa-check',
    'info' => 'fa-circle-info',
    'warning' => 'fa-triangle-exclamation',
    'danger' => 'fa-circle-xmark'
];
@endphp

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    @foreach($types as $type)
        @if(session()->has($type))
            @php
                $messages = session($type);
                // Ensure $messages is always an array
                if (!is_array($messages)) {
                    $messages = [$messages];
                }
            @endphp
            @foreach($messages as $message)
                <div class="toast align-items-center text-bg-{{ $type }} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="toast-picto fa-solid fa-fw me-2 {{ $icons[$type] ?? 'fa-bell' }}"></i>
                            <span class="toast-message">{{ $message }}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach
</div>

{{-- Template for JavaScript toasts --}}
<div id="toast-template" style="display: none;">
    <div class="toast align-items-center text-bg-primary border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="toast-picto fa-solid fa-fw me-2 fa-circle-info text-white"></i>
                <span class="toast-message"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
// Execute immediately: NO addEventListener
(function () {
    const toastElements = document.querySelectorAll('#toast-container .toast');
    for (const el of toastElements) {
        bootstrap.Toast.getOrCreateInstance(el).show();
    }

    // Define global function to trigger JS toast if needed
    window.toastme = function (msg, color = 'info', picto = 'fa-circle-info') {
        const container = document.getElementById('toast-container');
        const template = document.getElementById('toast-template').firstElementChild;
        const newToast = template.cloneNode(true);

        newToast.classList.remove('text-bg-primary');
        newToast.classList.add('text-bg-' + color);

        newToast.querySelector('.toast-message').textContent = msg;
        newToast.querySelector('.toast-picto').className = 'toast-picto fa-solid fa-fw me-2 ' + picto + ' text-white';

        container.appendChild(newToast);
        bootstrap.Toast.getOrCreateInstance(newToast).show();
    };
})();
</script>