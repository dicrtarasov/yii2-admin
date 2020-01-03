<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:29:00
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use function is_string;

/**
 * Табы редактора.
 *
 * Расширяет класс \yii\bootstrap4\Nav, добавляя возможность указывать элементы в виде:
 * target => label
 *
 * Также добавляет tab-content в конце, поэтому можно использовать через begin/end
 *
 * ```php
 * <?php EditTabs::begin($config); ?>
 * <div class="tab-pane">...</div>
 * <div class="tab-pane">...</div>
 * <?php EditTabs::end() ?>
 * ```
 *
 * @noinspection PhpUnused
 */
class EditTabs extends Nav
{
    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Nav::init()
     */
    public function init()
    {
        // корректируем элементы
        $this->adjustItems();

        Html::addCssClass($this->options, ['nav-tabs', 'dicr-admin-widgets-edit-tabs']);

        parent::init();

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Nav::run()
     */
    public function run()
    {
        $content = trim(ob_get_clean());

        $this->view->registerAssetBundle(EditTabsAsset::class);
        $this->registerPlugin('dicrAdminWidgetsEditTabs');

        $html = '';

        // вкладки навигации
        ob_start();
        $html .= parent::run();
        $html .= ob_get_clean();

        // панель табов
        if ($content !== '') {
            $html .= self::beginTabContent() . $content . self::endTabContent();
        }

        return $html;
    }

    /**
     * Просматривает элементы и конвертирует короткий формат:
     * tab_id => label в формат Nav
     */
    protected function adjustItems()
    {
        if (empty($this->items)) {
            return;
        }

        /** @var bool имеется активный элемент */
        $hasActive = false;

        // просматриваем все элементы
        foreach ($this->items as $i => &$item) {
            // если ключ и значение заданы как target => label, то конвертируем в формат Nav
            if (is_string($i) && is_string($item)) {
                $item = [
                    'label' => $item,
                    'url' => 'javascript:',
                    'linkOptions' => [
                        'data' => [
                            'toggle' => 'tab',
                            'target' => '#' . $i
                        ]
                    ],
                ];
            }

            // проверяем активность
            if (!empty($item[$i]['active'])) {
                $hasActive = true;
            }
        }

        unset($item);

        // если не было активных элементов, то устанавливаем активным первый
        if (!$hasActive) {
            $keys = array_keys($this->items);
            $this->items[$keys[0]]['active'] = true;
        }
    }

    /**
     * Начало tab-content
     *
     * @param array $options
     * @return string
     */
    public static function beginTabContent(array $options = [])
    {
        Html::addCssClass($options, 'tab-content');
        return Html::beginTag('div', $options);
    }

    /**
     * Закрывающий тег tab-content
     *
     * @return string
     */
    public static function endTabContent()
    {
        return Html::endTag('div');
    }

    /**
     * Открывающий тег tab-pane
     *
     * @param string $id
     * @param bool $active
     * @param array $options
     * @return string
     * @noinspection PhpUnused
     */
    public static function beginTab(string $id, bool $active = false, array $options = [])
    {
        $options['id'] = $id;
        Html::addCssClass($options, 'tab-pane');
        if ($active) {
            Html::addCssClass($options, 'active');
        }

        ob_start();
        ob_implicit_flush(0);
        echo Html::beginTag('div', $options);
        return '';
    }

    /**
     * Закрывающий тег tab-pane
     *
     * @return string
     * @noinspection PhpUnused
     */
    public static function endTab()
    {
        echo ob_get_clean() . Html::endTag('div');
        return '';
    }
}
