(function () {
    'use strict';

    Array.prototype.forEach.call(
        document.querySelectorAll('.editor'),
        editor => {
            const textarea = editor.querySelector('textarea');
            const lineNumbers = editor.querySelector('.line-numbers');

            function updateEditorLineNumbers(textarea) {
                const numberOfLines = textarea.value.split('\n').length;

                lineNumbers.innerHTML = Array(numberOfLines)
                    .fill('<span></span>')
                    .join('');
            }

            textarea.addEventListener('input', event => {
                updateEditorLineNumbers(event.target);
            });

            updateEditorLineNumbers(textarea);
        }
    );
})();
