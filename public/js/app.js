// Обратный отсчёт до окончания аукциона.
// Это только визуальный таймер: решение о завершении принимает сервер.
document.addEventListener('DOMContentLoaded', function () {
    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function tick() {
        document.querySelectorAll('.countdown').forEach(function (el) {
            var end = new Date(el.dataset.end).getTime();
            var diff = Math.floor((end - Date.now()) / 1000);

            if (isNaN(end)) {
                el.textContent = '--:--:--';
                return;
            }

            if (diff <= 0) {
                el.textContent = 'завершён';
                return;
            }

            var h = Math.floor(diff / 3600);
            var m = Math.floor((diff % 3600) / 60);
            var s = diff % 60;

            el.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        });
    }

    tick();
    setInterval(tick, 1000);

    // Сообщения об успехе прячем сами
    document.querySelectorAll('.alert--success').forEach(function (el) {
        setTimeout(function () {
            el.style.display = 'none';
        }, 6000);
    });

    // Кнопка ставки блокируется при отправке
    var bidForm = document.getElementById('bid-form');
    if (bidForm) {
        bidForm.addEventListener('submit', function () {
            var btn = document.getElementById('bid-submit');
            if (btn && btn.textContent.trim() !== '') {
                btn.disabled = true;
                btn.textContent = btn.dataset.loadingText || '...';
            }
        });
    }
});
