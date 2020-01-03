/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 00:47:06
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
            $('.dropdown-item', $module).on('shown.bs.tab', function () {
                // ссылка родительского таба
                const $toggle = $(this).closest('.dropdown').find('.dropdown-toggle');

                // сохраняем оригинальную метку
                if (!$toggle.data('orig-label')) {
                    $toggle.data('orig-label', $toggle.text());
                }

                $toggle.text($(this).text());
            });

            // восстановление оригинального названия вкладки dropdown при уходе в другой таб
            $('.dropdown-toggle', $module).on('hidden.bs.tab', function () {
                const $origLabel = $(this).data('orig-label');
                if ($origLabel) {
                    $(this).text($origLabel);
                }
            });
        });
    };
})(jQuery);
