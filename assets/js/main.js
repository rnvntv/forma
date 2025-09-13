// Mobile menu toggle and smooth scroll
(function () {
  var toggle = document.querySelector('[data-mobile-toggle]');
  var nav = document.querySelector('[data-nav]');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('is-open');
    });
  }

  // Header scrolled state
  var header = document.querySelector('.header');
  var onScroll = function () {
    if (!header) return;
    if (window.scrollY > 6) header.classList.add('header--scrolled');
    else header.classList.remove('header--scrolled');
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  // Smooth anchor scroll
  document.addEventListener('click', function (e) {
    var target = e.target.closest('a[href^="#"]');
    if (!target) return;
    var id = target.getAttribute('href').slice(1);
    if (!id) return;
    var el = document.getElementById(id);
    if (!el) return;
    e.preventDefault();
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' });
  });

  // Simple reveal on scroll
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('[data-reveal]').forEach(function (el) { observer.observe(el); });
})();
