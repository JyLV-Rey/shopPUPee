// Theme switcher
(function () {
    const html = document.documentElement;
    const toggle = document.getElementById('theme-toggle');

    if (!toggle) return;

    const LIGHT = 'garden';
    const DARK = 'dim';

    // Initialize from localStorage — mirrors React's useState + JSON.parse
    const saved = localStorage.getItem('isdark');
    const isDark = saved !== null ? JSON.parse(saved) : false;

    if (isDark) {
        html.setAttribute('data-theme', DARK);
        toggle.checked = true;
    } else {
        html.setAttribute('data-theme', LIGHT);
        toggle.checked = false;
    }

    // Persist on toggle — mirrors React's useEffect watching isdark
    toggle.addEventListener('change', function () {
        const nowDark = this.checked;
        html.setAttribute('data-theme', nowDark ? DARK : LIGHT);
        localStorage.setItem('isdark', JSON.stringify(nowDark));
    });
})();
