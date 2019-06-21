<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;

/**
 * Табы редактора.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EditTabs extends Nav
{
    /** @var string[]|array[] ассоциативный массив href_id => label либо массив Nav::items */
    public $items = [];

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Nav::init()
     */
    public function init()
    {
        // если задан ассоциативный массив то конверируем в формат Nav
        if (!empty($this->items) && !isset($this->items[0])) {
            $items = [];
            foreach ($this->items as $id => $label) {
                $items[] = [
                    'label' => $label,
                    'url' => 'javascript:',
                    'linkOptions' => [
                        'data' => [
                            'toggle' => 'tab',
                            'target' => '#' . $id
                        ]
                    ],
                    'active' => empty($items)
                ];
            }

            $this->items = $items;
        }

        Html::addCssClass($this->options, ['nav-tabs', 'dicr-admin-widgets-edit-tabs']);

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Nav::run()
     */
    public function run()
    {
        if (empty($this->items)) {
            return '';
        }

        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/edit-tabs.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        return parent::run();
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

        return Html::beginTag('div', $options);
    }

    /**
     * Закрывающий тег tab-pane
     *
     * @return string
     */
    public static function endTab()
    {
        return Html::endTag('div');
    }
}
