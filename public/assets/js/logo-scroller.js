// public/assets/js/logo-scroller.js
(() => {
  const initScroller = (root) => {
    const originalTrack = root.querySelector('.logo-track');
    if (!originalTrack) return;
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const speed = parseFloat(root.getAttribute('data-speed') || '38'); // px/sec

    // Crée une ceinture: <div class="logo-belt"><div class="logo-track">...</div><div class="logo-track clone">...</div></div>
    let belt = root.querySelector('.logo-belt');
    if (!belt) {
      belt = document.createElement('div');
      belt.className = 'logo-belt';
      originalTrack.parentNode.insertBefore(belt, originalTrack);
      belt.appendChild(originalTrack);
      const clone = originalTrack.cloneNode(true);
      clone.classList.add('clone');
      belt.appendChild(clone);
    }

    let trackWidth = 0;
    const measure = () => {
      // Mesurer la largeur d'une piste (une seule fois par resize)
      trackWidth = belt.children[0].scrollWidth; // width de la piste d'origine
      // S'assurer que la ceinture a la largeur de 2 pistes
      belt.style.width = `${trackWidth * 2}px`;
    };
    measure();

    let x = 0;
    let last = performance.now();
    let paused = false;
    let raf;

    const step = (now) => {
      if (paused || prefersReduced) { last = now; raf = requestAnimationFrame(step); return; }
      const dt = (now - last) / 1000; last = now;
      x -= speed * dt;

      // Quand on a parcouru une piste entière, on reset proprement à 0
      if (-x >= trackWidth) x += trackWidth;

      belt.style.transform = `translateX(${x}px)`;
      raf = requestAnimationFrame(step);
    };
    raf = requestAnimationFrame(step);

    // Accessibilité / contrôles
    const pause = () => paused = true;
    const play  = () => paused = false;
    root.addEventListener('mouseenter', pause);
    root.addEventListener('mouseleave', play);
    root.addEventListener('focusin', pause);
    root.addEventListener('focusout', play);

    // Recalcul sur resize (debounce)
    let to;
    window.addEventListener('resize', () => {
      clearTimeout(to);
      to = setTimeout(() => {
        belt.style.transform = 'translateX(0px)';
        x = 0;
        measure();
      }, 150);
    });

    // Cleanup (si besoin SPA)
    root._destroyLogoScroller = () => {
      cancelAnimationFrame(raf);
      root.removeEventListener('mouseenter', pause);
      root.removeEventListener('mouseleave', play);
      root.removeEventListener('focusin', pause);
      root.removeEventListener('focusout', play);
      window.removeEventListener('resize', () => {});
    };
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.logo-scroller').forEach(initScroller);
  });
})();