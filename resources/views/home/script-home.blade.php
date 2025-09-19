{{-- Script JavaScript pour la page d'accueil --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle TTC/HT prices
    const toggleTTC = document.getElementById('toggleTTC');
    if (toggleTTC) {
        toggleTTC.addEventListener('change', function() {
            const prices = document.querySelectorAll('.js-price');
            const perPrices = document.querySelectorAll('.js-per');
            const suffixes = document.querySelectorAll('[data-suffix]');
            
            prices.forEach(function(element) {
                const htPrice = parseFloat(element.getAttribute('data-ht'));
                const ttcPrice = Math.round(htPrice * 1.2);
                element.textContent = toggleTTC.checked ? ttcPrice : htPrice;
            });
            
            perPrices.forEach(function(element) {
                const htPrice = parseFloat(element.getAttribute('data-ht'));
                const ttcPrice = Math.round(htPrice * 1.2);
                element.textContent = toggleTTC.checked ? ttcPrice : htPrice;
            });
            
            suffixes.forEach(function(element) {
                const suffix = element.getAttribute('data-suffix');
                if (suffix === 'ticket') {
                    element.textContent = toggleTTC.checked ? 'T.T.C.' : 'H.T.';
                } else if (suffix === 'pack') {
                    element.textContent = toggleTTC.checked ? 'T.T.C.' : 'H.T.';
                }
            });
        });
    }

    // Simulateur de prix
    const calcMinutes = document.getElementById('calcMinutes');
    const calcDeplacement = document.getElementById('calcDeplacement');
    const calcUrgence = document.getElementById('calcUrgence');
    const calcExpress = document.getElementById('calcExpress');
    const calcKm = document.getElementById('calcKm');
    const calcPack = document.getElementById('calcPack');
    const calcReset = document.getElementById('calcReset');

    function calculatePrice() {
        const minutes = parseInt(calcMinutes.value);
        const tickets = Math.ceil(minutes / 30);
        const packPrice = parseInt(calcPack.value);
        const km = parseInt(calcKm.value) || 0;
        const isUrgence = calcUrgence.checked;
        const isExpress = calcExpress.checked;
        const isDeplacement = calcDeplacement.checked;
        
        // Calcul base
        let basePrice = tickets * packPrice;
        
        // Suppléments
        let supplements = 0;
        if (isDeplacement) supplements += km * 0.6;
        if (isUrgence) basePrice *= 1.5;
        if (isExpress) basePrice *= 1.3;
        
        const totalHT = basePrice + supplements;
        const totalTTC = Math.round(totalHT * 1.2);
        
        // Mise à jour de l'affichage
        document.querySelectorAll('.totalMinutes').forEach(el => el.textContent = minutes);
        document.querySelectorAll('.totalTickets').forEach(el => el.textContent = tickets);
        document.querySelectorAll('.totalEnHeure').forEach(el => {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            el.textContent = hours > 0 ? `${hours}h${mins > 0 ? mins.toString().padStart(2, '0') : ''}` : `${minutes}min`;
        });
        document.querySelectorAll('.totalSupp').forEach(el => el.textContent = Math.round(supplements));
        document.querySelectorAll('.totalHT').forEach(el => el.textContent = Math.round(totalHT));
        document.querySelectorAll('.plurielTickets').forEach(el => el.style.display = tickets > 1 ? 'inline' : 'none');
        
        // Calculs pour tous les packs dans le tableau
        const packs = [
            {price: 68, base: '.calcBaseUnitaire', ht: '.calcTotalHTUnitaire', ttc: '.calcTotalTTCUnitaire'},
            {price: 57, base: '.calcBasePack10', ht: '.calcTotalHTPack10', ttc: '.calcTotalTTCPack10'},
            {price: 46, base: '.calcBasePack50', ht: '.calcTotalHTPack50', ttc: '.calcTotalTTCPack50'},
            {price: 37, base: '.calcBasePack100', ht: '.calcTotalHTPack100', ttc: '.calcTotalTTCPack100'},
            {price: 34, base: '.calcBasePack400', ht: '.calcTotalHTPack400', ttc: '.calcTotalTTCPack400'}
        ];
        
        packs.forEach(pack => {
            let packBase = tickets * pack.price;
            let packSupplements = supplements;
            if (isUrgence) packBase *= 1.5;
            if (isExpress) packBase *= 1.3;
            const packTotalHT = packBase + packSupplements;
            const packTotalTTC = Math.round(packTotalHT * 1.2);
            
            document.querySelectorAll(pack.base).forEach(el => el.textContent = Math.round(packBase) + '€');
            document.querySelectorAll(pack.ht).forEach(el => el.textContent = Math.round(packTotalHT) + '€');
            document.querySelectorAll(pack.ttc).forEach(el => el.textContent = packTotalTTC + '€');
        });
    }

    // Event listeners pour le simulateur
    if (calcMinutes) {
        calcMinutes.addEventListener('input', calculatePrice);
        calcDeplacement.addEventListener('change', function() {
            calcKm.disabled = !this.checked;
            if (!this.checked) calcKm.value = '';
            calculatePrice();
        });
        calcUrgence.addEventListener('change', calculatePrice);
        calcExpress.addEventListener('change', calculatePrice);
        calcKm.addEventListener('input', calculatePrice);
        calcPack.addEventListener('change', calculatePrice);
        
        calcReset.addEventListener('click', function() {
            calcMinutes.value = 30;
            calcDeplacement.checked = false;
            calcUrgence.checked = false;
            calcExpress.checked = false;
            calcKm.value = '';
            calcKm.disabled = true;
            calcPack.value = 37;
            calculatePrice();
        });
        
        // Calcul initial
        calculatePrice();
    }
});
</script>