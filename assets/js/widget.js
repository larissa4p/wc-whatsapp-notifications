(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.querySelector('.ewa-btn');
        if (!btn || !window.ewaConfig) return;

        btn.addEventListener('click', function () {
            const phone   = ewaConfig.phone.replace(/\D/g, '');
            const message = encodeURIComponent(ewaConfig.message);
            const url     = 'https://wa.me/' + phone + '?text=' + message;

            window.open(url, '_blank', 'noopener,noreferrer');
        });
    });
})();
