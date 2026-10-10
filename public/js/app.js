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
});
