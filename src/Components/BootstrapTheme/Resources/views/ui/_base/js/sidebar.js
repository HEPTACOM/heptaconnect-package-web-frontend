(function () {
    'use strict';

    Array.prototype.forEach.call(
        document.querySelectorAll('[data-toggle="sidebar-collapse"]'),
        el => el.addEventListener(
            'click', event => event.target.closest('.sidebar').classList.toggle('sidebar-collapsed')
        )
    );
})();
