<?php
namespace dicr\admin\widgets;

use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;

/**
 * Табы редактора.
 *
 * Расширяет класс \yii\bootstrap4\Nav, добавляя возможность указывать элементы в виде:
 * target => label
 *
 * Также добавляет tab-content в конце, поэтому можно использовать через begin/end
 *
 * <?php EditTabs::begin($config); ?>
 * <div class="tab-pane">...</div>
 * <div class="tab-pane">...</div>
 * <?php EditTabs::end() ?>
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
        ob_implicit_flush(false);
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
        foreach ($this->items as $i => $item) {
            // если ключ и значение заданы как target => label, то конвертируем в формат Nav
            if (is_string($i) && is_string($item)) {
                $this->items[$i] = [
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
            if (!empty($this->items[$i]['active'])) {
                $hasActive = true;
            }
        }

        // если не было активных элементов, то устанавливаем активным первый
        if (!$hasActive) {
            $keys = array_keys($this->items);
            $this->items[$keys[0]]['active'] = true;
        }
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
     * Начало tab-content
     *
     * @param array $options
     * @return string
     */
    public static function beginTabContent(array $options=[])
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
     */
    public static function beginTab(string $id, bool $active=false, array $options=[])
    {
        $options['id'] = $id;
        Html::addCssClass($options, 'tab-pane');
        if ($active) {
            Html::addCssClass($options, 'active');
        }

        ob_start();
        ob_implicit_flush(false);
        echo Html::beginTag('div', $options);
        return '';
    }

    /**
     * Закрывающий тег tab-pane
     *
     * @return string
     */
    public static function endTab()
    {
        echo ob_get_clean() . Html::endTag('div');
        return '';
    }
}
