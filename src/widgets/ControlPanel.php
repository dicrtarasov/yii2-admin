<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:35:38
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\helpers\ArrayHelper;

/**
 * Панель управления для панели навигации.
 */
class ControlPanel extends Widget
{
    /** @var array url для кнопки создания */
    public $create;

    /** @var array url для кнопки удаления */
    public $remove;

    /** @var array опции кнопки сохранить (form) */
    public $submit;

    /** @var array url кнопки скачивания, ex. Url::current([export => 1]) */
    public $download;

    /** @var string[] дополнительные кнопки */
    public $buttons;

    /**
     * {@inheritDoc}
     * @see \yii\base\Widget::init()
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dicr-admin-widgets-control-panel');

        parent::init();
    }

    /**
     * Создает кнопки
     *
     * @return string[]
     */
    protected function createButtons()
    {
        $buttons = [];

        if (!empty($this->create)) {
            $buttons['create'] = Html::a('<i class="fas fa-plus-square"></i>', $this->create, [
                'class' => 'btn btn-sm btn-success',
                'encode' => false,
                'title' => 'Создать'
            ]);
        }

        if (!empty($this->remove)) {
            $buttons['remove'] = Html::a('<i class="fas fa-trash-alt"></i>', $this->remove, [
                'class' => 'btn btn-sm btn-danger',
                'encode' => false,
                'title' => 'Удалить',
                'onclick' => 'return confirm(\'Удалить?\')'
            ]);
        }

        if (!empty($this->submit)) {
            $options = ArrayHelper::merge(['title' => 'Сохранить'], $this->submit);
            Html::addCssClass($options, ['btn btn-sm btn-primary']);
            $buttons['submit'] = Html::submitButton('<i class="fas fa-save"></i>', $options);
        }

        if (!empty($this->download)) {
            $buttons['download'] = Html::a('<i class="fas fa-download"></i>', $this->download, [
                'class' => 'btn btn-sm btn-secondary',
                'encode' => false,
                'title' => 'Скачать'
            ]);
        }

        if (!empty($this->buttons)) {
            $buttons = array_merge($buttons, $this->buttons);
        }

        return $buttons;
    }

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     * @see \yii\base\Widget::run()
     */
    public function run()
    {
        $buttons = $this->createButtons();
        if (empty($buttons)) {
            return '';
        }

        $this->view->registerAssetBundle(ControlPanelAsset::class);

        ob_start();
        echo Html::beginTag('section', $this->options);

        foreach ($buttons as $button) {
            echo $button;
        }

        echo Html::endTag('section');
        return ob_get_clean();
    }
}
