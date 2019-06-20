<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\helpers\ArrayHelper;

/**
 * Панель управления для панели навигации.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
     * {@inheritDoc}
     * @see \yii\base\Widget::run()
     */
    public function run()
    {
        if (empty($this->create) &&
            empty($this->remove) &&
            empty($this->submit) &&
            empty($this->download) &&
            empty($this->buttons)) {
            return '';
        }

        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/control-panel.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        ob_start();

        echo Html::beginTag('section', $this->options);

        if (!empty($this->create)) {
            echo Html::a('<i class="fas fa-plus-square"></i>', $this->create, [
                'class' => 'btn btn-sm btn-success',
                'encode' => false,
                'title' => 'Создать'
            ]);
        }

        if (!empty($this->remove)) {
            echo Html::a('<i class="fas fa-trash-alt"></i>', $this->remove, [
                'class' => 'btn btn-sm btn-danger',
                'encode' => false,
                'title' => 'Удалить',
                'onclick' => 'return confirm(\'Удалить?\')'
            ]);
        }

        if (!empty($this->submit)) {
            $options = ArrayHelper::merge([
                'title' => 'Сохранить'
            ], $this->submit);

            Html::addCssClass($options, ['btn btn-sm btn-primary']);

            echo Html::submitButton('<i class="fas fa-save"></i>', $options);
        }

        if (!empty($this->download)) {
            echo Html::a('<i class="fas fa-download"></i>', $this->download, [
                'class' => 'btn btn-sm btn-secondary',
                'encode' => false,
                'title' => 'Скачать'
            ]);
        }

        if (!empty($this->buttons)) {
            foreach ($this->buttons as $button) {
                echo $button;
            }
        }

        echo Html::endTag('section');

        return ob_get_clean();
    }
}
