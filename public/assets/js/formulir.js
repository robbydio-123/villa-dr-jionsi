(function () {
    var inp = document.getElementById('guestInput');
    var lbl = document.getElementById('guestLabel');
    var minus = document.getElementById('guestMinus');
    var plus = document.getElementById('guestPlus');
    if (!inp || !lbl || !minus || !plus) return;

    function set(v) {
        v = Math.max(1, Math.min(6, v));
        inp.value = v;
        lbl.textContent = v + ' Orang Tamu';
    }
    minus.addEventListener('click', function () { set(parseInt(inp.value, 10) - 1); });
    plus.addEventListener('click', function () { set(parseInt(inp.value, 10) + 1); });
})();