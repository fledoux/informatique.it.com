{{-- JavaScript files avec cache busting automatique --}}
<script src="{{ versioned_asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js') }}" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="{{ versioned_asset('https://kit.fontawesome.com/60fb8bbc91.js') }}" crossorigin="anonymous"></script>

{{-- TinyMCE Rich Text Editor --}}
<script src="https://cdn.tiny.cloud/1/0tl4f13gkahrgquxa8yryptzci68gyrbwv1qqlda1h9xz1ey/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.tinymce')) {
        tinymce.init({
            selector: '.tinymce',
            height: 300,
            menubar: false,
            language: 'fr-FR',
            plugins: 'anchor autolink charmap codesample emoticons link lists searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | link table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
            branding: false,
            promotion: false
        });
    }
});
</script>