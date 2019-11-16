/*
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

(function ($) {
    'use strict';

    if (typeof $.fn.dicrAdminWidgetsEditTabs === 'function') {
        return;
    }

    $.fn.dicrAdminWidgetsEditTabs = function () {
        return this.each(function () {
            const $module = $(this);

            // смена названия вкладки dropdown-menu при переключении подменю
            $('.dropdown-item', $module).on('shown.bs.tab', function (e) {
                // ссылка родительского таба
                const $toggle = $(this).closest('.dropdown').find('.dropdown-toggle');

                // сохраняем оригинальную метку
                if (!$toggle.data('orig-label')) {
                    $toggle.data('orig-label', $toggle.text());
                }

                $toggle.text($(this).text());
            });

            // восстановление оригинального названия вкладки dropdown при уходе в другой таб
            $('.dropdown-toggle', $module).on('hidden.bs.tab', function (e) {
                const $origLabel = $(this).data('orig-label');
                if ($origLabel) {
                    $(this).text($origLabel);
                }
            });
        });
    };
})(jQuery);
