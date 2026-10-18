/**
 * Countdown timer for Villa D-R Jionsi Keep Window (15 Minutes)
 * SRS FR-03, FR-04, FR-11
 */

function initKeepCountdown(elementId, expiresTimestamp, onExpireCallback) {
    const el = document.getElementById(elementId);
    if (!el) return;

    function update() {
        const now = Math.floor(Date.now() / 1000);
        const remaining = expiresTimestamp - now;

        if (remaining <= 0) {
            el.innerHTML = "00:00";
            el.classList.add("text-danger");
            if (typeof onExpireCallback === "function") {
                onExpireCallback();
            }
            return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        const mStr = String(minutes).padStart(2, '0');
        const sStr = String(seconds).padStart(2, '0');

        el.innerHTML = `${mStr}:${sStr}`;
    }

    update();
    const interval = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        if (expiresTimestamp - now <= 0) {
            clearInterval(interval);
            update();
        } else {
            update();
        }
    }, 1000);
}
