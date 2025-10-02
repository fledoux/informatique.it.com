{{-- Bouton scroll to top --}}
<button id="scrollToTop" class="btn btn-orange position-fixed bottom-0 end-0 me-3 mb-5 shadow-lg"
    style="display: none; z-index: 1050; border-radius: 50%; width: 50px; height: 50px;">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<script>
    {{-- Script pour le bouton scroll to top --}}
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToTopBtn = document.getElementById('scrollToTop');

        {{-- Afficher/masquer le bouton selon le scroll --}}
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.display = 'block';
            } else {
                scrollToTopBtn.style.display = 'none';
            }
        });

        {{-- Action au clic : retour en haut avec animation --}}
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
