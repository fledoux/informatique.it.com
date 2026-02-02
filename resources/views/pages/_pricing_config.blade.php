{{-- Configuration JavaScript pour les prix et validités des tickets --}}
<script>
    window.ticketConfig = {
        prices: @json(\App\Helpers\Helper::getAllTicketPrices()),
        validities: @json(\App\Helpers\Helper::getAllTicketValidities()),
        discounts: @json(\App\Helpers\Helper::getAllTicketDiscounts()),
        tvaRate: {{ config('pricing.tva_rate', 0.20) }}
    };
</script>
