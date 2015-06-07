/**
 * Общие скрипты
 */
$(document).ready(function() {
    /**
     * Кастомный селект
     */
    $('select').each(function() {
        var options = {};
        if ($(this).attr('placeholder')) {
            options.noneSelectedText = $(this).attr('placeholder');
        }
        $(this).selectpicker(options);
    });

    /**
     * Переключение верхнего поиска
     */
    $('#toggleTopSearch').click(function(e) {
        e.preventDefault();
        $('#topSearchWrap').slideToggle();
        $(this).parents('li:first').toggleClass('active');
    });

    /**
     * Подгрузка модальных бутстрап-окон в AJAX
     */
    $('.load-modal-ajax').on('show.bs.modal', function(e) {
        var href = $(this).data('ajax-url');
        $(this).find('.modal-body').load(href);
    });
    $('.load-modal-ajax').on('hide.bs.modal', function(e) {
        $(this).find('.modal-body').html('');
    });
});
