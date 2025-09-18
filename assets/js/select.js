function initSelect2() {
    $('select[multiple]').each(function() {
        // Détruire l'instance précédente si elle existe
        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).select2('destroy');
        }
        $(this).select2();
    });
}

// Initialisation au chargement complet classique
$(document).ready(initSelect2);

// Initialisation pour les navigations internes (Turbo, PJAX, etc.)
document.addEventListener('turbo:load', initSelect2);
