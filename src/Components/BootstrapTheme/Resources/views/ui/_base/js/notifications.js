(function () {
    'use strict';

    Array.prototype.forEach.call(
        document.getElementsByClassName('toast'),
        toast => (new bootstrap.Toast(toast)).show()
    );
})();
