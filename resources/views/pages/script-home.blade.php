{{-- Home page JavaScript --}}
<script>
// Toggle HT/TTC (TTC = HT * 1.20)
(function () {
    const toggle = document.getElementById('toggleTTC');
    const prices = () => document.querySelectorAll('#tarifs .js-price');
    const perTicket = () => document.querySelectorAll('#tarifs .js-per');
    const suffixes = () => document.querySelectorAll('#tarifs [data-suffix]');

    function formatFR(n) {
        const isInt = Math.abs(n - Math.round(n)) < 1e-9;
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: isInt ? 0 : 2,
            maximumFractionDigits: isInt ? 0 : 2
        }).format(n);
    }

    function refresh() {
        const isTTC = !!toggle?.checked;

        prices().forEach(node => {
            const ht = parseFloat(node.getAttribute('data-ht'));
            if (Number.isNaN(ht)) return;

            const value = isTTC ? (ht * 1.20) : ht;
            node.textContent = formatFR(value);
        });

        perTicket().forEach(node => {
            const ht = parseFloat(node.getAttribute('data-ht'));
            if (Number.isNaN(ht)) return;

            const value = isTTC ? (ht * 1.20) : ht;
            node.textContent = formatFR(value);
        });

        suffixes().forEach(el => {
            const kind = el.getAttribute('data-suffix');
            if (kind === 'ticket') {
                el.innerHTML = isTTC ? '/ ticket T.T.C. (30 min), valable 1 an.' : '/ ticket H.T. (30 min), valable 1 an.';
            } else if (kind === 'pack') {
                el.innerHTML = isTTC ? '/ pack T.T.C.' : '/ pack H.T.';
            }
        });
    }

    if (toggle) {
        toggle.addEventListener('change', refresh);
    }
    refresh();

    // --- Simulator ---
    const $ = (sel) => document.querySelector(sel);
    const fmt = (n) => new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: (Math.abs(n - Math.round(n)) < 1e-9) ? 0 : 2,
        maximumFractionDigits: (Math.abs(n - Math.round(n)) < 1e-9) ? 0 : 2
    }).format(n) + '€';

    function minutesToHeureLabel(m) {
        const min = Math.max(0, parseInt(m, 10) || 0);
        const h = Math.floor(min / 60);
        const r = min % 60;
        if (h > 0 && r > 0) return `${h}h${r}`;
        if (h > 0 && r === 0) return `${h}h`;
        return `${r}min`;
    }

    function refreshHeure() {
        const minutesEl = document.getElementById('calcMinutes');
        const label = minutesToHeureLabel(minutesEl ? minutesEl.value : 0);
        document.querySelectorAll('.totalEnHeure').forEach(span => {
            span.textContent = label;
        });
    }

    const PRICES = {
        unit: 68,
        p10: 57,
        p50: 46,
        p100: 37,
        p400: 34
    };
    // HT per ticket

    // Surcharges (additional tickets)
    const EXTRA = {
        night: 1,    // Night → +1 ticket
        weekend: 1,  // Weekend → +1 ticket
        holiday: 2,  // Holiday → +2 tickets
        urgent: 3,   // Urgent → +3 tickets
        travel: 3    // Travel Paris/RP → +3 tickets
    };

    function setExtrasUI() { // Update visual badges from config
        document.querySelectorAll('.js-extra').forEach(badge => {
            const key = badge.getAttribute('data-key');
            const val = EXTRA[key] ?? 0;
            const label = val + ' ticket' + (val > 1 ? 's' : '');
            badge.textContent = '+' + label;
        });
    }

    const ids = {
        // HT totals
        tot_ht_unit: '#tot_ht_unit',
        tot_ht_p10: '#tot_ht_p10',
        tot_ht_p50: '#tot_ht_p50',
        tot_ht_p100: '#tot_ht_p100',
        tot_ht_p400: '#tot_ht_p400',
        // TTC totals
        tot_ttc_unit: '#tot_ttc_unit',
        tot_ttc_p10: '#tot_ttc_p10',
        tot_ttc_p50: '#tot_ttc_p50',
        tot_ttc_p100: '#tot_ttc_p100',
        tot_ttc_p400: '#tot_ttc_p400',
        // HT unit price
        t_ht_unit: '#t_ht_unit',
        t_ht_p10: '#t_ht_p10',
        t_ht_p50: '#t_ht_p50',
        t_ht_p100: '#t_ht_p100',
        t_ht_p400: '#t_ht_p400',
        // TTC unit price
        t_ttc_unit: '#t_ttc_unit',
        t_ttc_p10: '#t_ttc_p10',
        t_ttc_p50: '#t_ttc_p50',
        t_ttc_p100: '#t_ttc_p100',
        t_ttc_p400: '#t_ttc_p400'
    };

    function computeTickets() {
        const minutes = Math.max(parseInt($('#calcMinutes')?.value || '0', 10), 0);
        const base = Math.max(1, Math.ceil(minutes / 30));
        let extra = 0;

        if ($('#calcNight')?.checked) extra += (EXTRA.night ?? 0);
        if ($('#calcWeekend')?.checked) extra += (EXTRA.weekend ?? 0);
        if ($('#calcHoliday')?.checked) extra += (EXTRA.holiday ?? 0);
        if ($('#calcUrgent')?.checked) extra += (EXTRA.urgent ?? 0);
        if ($('#calcTravel')?.checked) extra += (EXTRA.travel ?? 0);

        const total = base + extra;
        $('#outBase') && ($('#outBase').textContent = base);
        $('#outExtra') && ($('#outExtra').textContent = extra);
        $('#outTotal') && ($('#outTotal').textContent = total);
        return {base, extra, total};
    }

    function refreshSimulator() {
        const {total} = computeTickets();
        refreshHeure();

        // Fill HT/TTC unit prices
        Object.entries(PRICES).forEach(([key, priceHT]) => {
            const uHT = priceHT;
            const uTTC = priceHT * 1.2;
            const t_ht = document.querySelector(ids['t_ht_' + key]);
            const t_ttc = document.querySelector(ids['t_ttc_' + key]);
            
            if (t_ht) t_ht.textContent = fmt(uHT);
            if (t_ttc) t_ttc.textContent = fmt(uTTC);

            // Calculate totals
            const totalHT = total * uHT;
            const totalTTC = total * uTTC;
            const tot_ht = document.querySelector(ids['tot_ht_' + key]);
            const tot_ttc = document.querySelector(ids['tot_ttc_' + key]);
            
            if (tot_ht) tot_ht.textContent = fmt(totalHT);
            if (tot_ttc) tot_ttc.textContent = fmt(totalTTC);
        });
    }

    // Initialize
    setExtrasUI();

    // Event listeners
    document.getElementById('calcMinutes')?.addEventListener('input', refreshSimulator);
    document.getElementById('calcNight')?.addEventListener('change', refreshSimulator);
    document.getElementById('calcWeekend')?.addEventListener('change', refreshSimulator);
    document.getElementById('calcHoliday')?.addEventListener('change', refreshSimulator);
    document.getElementById('calcUrgent')?.addEventListener('change', refreshSimulator);
    document.getElementById('calcTravel')?.addEventListener('change', refreshSimulator);

    // Initial calculation
    refreshSimulator();
})();
</script>