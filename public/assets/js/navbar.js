(function () {
    const nav = document.querySelector('.navbar-custom');
    const onScroll = () => {
        const y = window.scrollY;
        const h = document.documentElement.scrollHeight - window.innerHeight;
        if (nav) {
            nav.classList.toggle('scrolled', y > 10);
            nav.style.setProperty('--scroll-progress', (h > 0 ? (y / h) * 100 : 0) + '%');
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    const page = location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.navbar-custom .nav-link').forEach(a => {
        if (a.getAttribute('href') === page) a.classList.add('active');
    });

    const els = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    } else { els.forEach(el => el.classList.add('in')); }
})();