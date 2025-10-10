<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ versioned_asset('https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ versioned_asset('https://cdn.datatables.net/select/1.6.2/css/select.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ versioned_asset('https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css') }}">

<!-- DataTables JS -->
<script src="{{ versioned_asset('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdn.datatables.net/select/1.6.2/js/dataTables.select.min.js') }}"></script>

<!-- DataTables Buttons -->
<script src="{{ versioned_asset('https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js') }}"></script>
<script src="{{ versioned_asset('https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $.extend(true, $.fn.dataTable.defaults, {
            rowId: "id",
            pagingType: "full_numbers",
            responsive: false,
            ordering: true,
            autoWidth: false,
            pageLength: -1,
            lengthMenu: [
                [10, 15, 25, 50, 100, -1],
                [10, 15, 25, 50, 100, "Tous"]
            ],
            dom: "<'row mb-3'" +
                "<'col-xl-4 d-sm-flex align-items-center'<'w-100'f>>" +
                "<'col-xl-4 d-sm-flex align-items-center justify-content-center'p>" +
                "<'col-xl-4 d-sm-flex align-items-center justify-content-end gap-3 d-none d-sm-flex'iBl>" +
                ">" +
                "<'row'" +
                "<'col-xl-12'<'table-responsive'tr>>" +
                ">",

            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-regular fa-file-xls"></i> Export Excel',
                className: 'btn btn-primary btn-sm', // Change la couleur ici
                title: function() {
                    let now = new Date();
                    let dateStr = now.toISOString().slice(0, 10); // Format YYYY-MM-DD
                    let timeStr = now.toTimeString().slice(0, 5).replace(":",
                    "h"); // Format HHhMM
                    return dateStr + "_" + timeStr + "_AAS"; // Ex: "2025-01-29_14h30_VPL"
                },
                exportOptions: {
                    columns: ':not(.no-export)' // Exporter uniquement les colonnes visibles
                }
            }],
            // Exemples de paramétrages possibles :
            language: {
                //url: "/assets/json/fr-FR.json",
                zeroRecords: "<div class=\"text-center m-4\"><h5 class=\"text-danger\"><i class=\"fa-regular text-danger fa-magnifying-glass\"></i> Aucun résultat</h5></div>",
                buttons: {
                    copy: "Copier",
                    selectAll: '<i class="fa-regular fa-square-check"></i>',
                    selectNone: '<i class="fa-regular fa-square"></i>',
                },
                select: {
                    rows: {
                        "_": ' - %d Sélections',
                        "0": ' - 0 Sélection',
                        "1": ' - 1 Sélection'
                    }
                },
                paginate: {
                    first: '<i class="fa-light fa-arrow-left-long-to-line"></i>',
                    last: '<i class="fa-light fa-arrow-right-long-to-line"></i>',
                    next: '<i class="fa-light fa-arrow-right-long"></i>',
                    previous: '<i class="fa-light fa-arrow-left-long"></i>'
                },
                emptyTable: "<h2 class=\"text-danger ms-3\">Aucune donnée disponible dans le tableau</h2>",
                loadingRecords: "Chargement...",
                processing: "Traitement...",
                info: "TOTAL : _TOTAL_",
                infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                infoFiltered: "/_MAX_",
                lengthMenu: "_MENU_",
                search: " ",
                searchPlaceholder: "Recherche...",
                lenght: "No matching records found"
            },

            // Exemple de callback pour personnaliser le rendu
            drawCallback: function() {
                //console.log("dataTable OK")
            },
            initComplete: function() {
                // Forcer la classe form-control (taille normale) au lieu de form-control-sm
                $('.dataTables_filter label').addClass('w-100');
                $('.dataTables_filter input[type="search"]')
                    .removeClass('form-control-sm')
                    .addClass('form-control w-100');
                // Enlever les marges de la pagination pour l'alignement vertical
                $('.dataTables_paginate .pagination').css('margin-bottom', '0');
            }
        });

        // Initialiser automatiquement toutes les tables avec la classe .datatable
        @if(!isset($datatableOptions))
            // Configuration par défaut si aucune option spécifique n'est fournie
            $('.datatable').DataTable({
                order: [[0, 'desc']], // Tri par défaut : première colonne décroissante
                columnDefs: [
                    { orderable: false, targets: -1 } // Désactiver le tri sur la dernière colonne (Actions)
                ]
            });
        @else
            // Configuration personnalisée fournie par la vue
            $('.datatable').DataTable({!! json_encode($datatableOptions) !!});
        @endif
    });
</script>

{{-- 
Exemple d'utilisation avec options personnalisées :

@push('javascripts')
    @include('partials._datatable', [
        'datatableOptions' => [
            'order' => [[2, 'asc']], // Tri par 3ème colonne croissant
            'pageLength' => 25
        ]
    ])
@endpush
--}}

