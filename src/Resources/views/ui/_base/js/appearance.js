(function () {
    'use strict';

    const darkModeToggle = document.getElementById('toggle--dark-mode');

    if (darkModeToggle !== null) {
        function updateDarkMode(enabled) {
            document.body.dataset.bsTheme = enabled ? 'dark' : 'light';
            localStorage.setItem('bsTheme', document.body.dataset.bsTheme);

            Array.prototype.forEach.call(
                document.querySelectorAll('link[rel="stylesheet"][data-style-theme-aware="true"]'),
                function (link) {
                    if (document.body.dataset.bsTheme === 'light') {
                        link.href = link.dataset.hrefLight;
                    } else if (document.body.dataset.bsTheme === 'dark') {
                        link.href = link.dataset.hrefDark;
                    }
                }
            );
        }

        const selectedTheme = localStorage.getItem('bsTheme');

        if (['light', 'dark'].indexOf(selectedTheme) > -1) {
            updateDarkMode(selectedTheme === 'dark')
            darkModeToggle.checked = document.body.dataset.bsTheme === 'dark';
        } else {
            darkModeToggle.checked = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            updateDarkMode(darkModeToggle.checked);
        }

        darkModeToggle.addEventListener('change', event => updateDarkMode(event.target.checked));
    }
})();
