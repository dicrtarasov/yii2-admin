'use strict';

(function($) {
    if (typeof $.fn.dicrAdminWidgetsEditTabs == 'function') {
        return;
    }

    $.fn.dicrAdminWidgetsEditTabs = function() {
        return this.each(function() {
            const $module = $(this);

            // смена названия вкладки dropdown-menu
            $('.dropdown-item', $module).on('shown.bs.tab', function(e) {
                $(this).closest('.dropdown').find('.dropdown-toggle').text($(this).text());
            });
        });
    };
})(jQuery);