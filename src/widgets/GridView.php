<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:35:23
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use Closure;
use yii\base\Arrayable;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\bootstrap4\Html;
use function array_key_exists;

/**
 * GridView.
 *
 * @noinspection PhpUnused
 */
class GridView extends \yii\grid\GridView
{
    /** @var array */
    public $tableOptions = [
        'class' => 'table table-sm table-striped'
    ];

    /** @var string */
    public $layout = '{summary}<div class="table-responsive">{items}</div>{pager}';

    /** @var string аттрибут обозначающий отключенную запись */
    public $disabledAttr = 'disabled';

    /** @var string аттрибут обозначающий включенную запись */
    public $enabledAttr = 'enabled';

    /** @var string атрибут для выделения */
    public $featuredAttr;

    /** @var array */
    private $_origRowOptions;

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     * @see \yii\grid\GridView::init()
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dicr-admin-widgets-grid-view');

        if (empty($this->pager['class'])) {
            $this->pager['class'] = LinkPager::class;
        }

        $this->_origRowOptions = $this->rowOptions ?: [];

        $this->rowOptions = function ($model, $key, $index, $grid) {
            return $this->getRowOptions($model, $key, $index, $grid);
        };

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     * @see \yii\grid\GridView::run()
     */
    public function run()
    {
        $this->view->registerAssetBundle(GridViewAsset::class);

        return parent::run();
    }

    /**
     * Возвращает опции строки.
     *
     * @param Model|array $model
     * @param string $key
     * @param int $index
     * @param GridView $grid
     * @return array
     */
    protected function getRowOptions($model, $key, $index, $grid)
    {
        // оригинальные опции
        $options = $this->_origRowOptions;

        // если опции в виде Closure, то получаем значение
        if ($options instanceof Closure) {
            $options = $options($model, $key, $index, $grid);
        }

        if (!empty($model)) {
            if ($model instanceof Model) {
                $model = $model->attributes;
            } elseif ($model instanceof Arrayable) {
                $model = $model->toArray();
            } else {
                $model = (array)$model;
            }

            $disabled = false;
            $featured = false;

            if (!empty($this->disabledAttr) && array_key_exists($this->disabledAttr, $model)) {
                $disabled = !empty($model[$this->disabledAttr]);
            } elseif (!empty($this->enabledAttr) && array_key_exists($this->enabledAttr, $model)) {
                $disabled = empty($model[$this->enabledAttr]);
            }

            if (!empty($this->featuredAttr) && array_key_exists($this->featuredAttr, $model)) {
                $featured = !empty($model[$this->featuredAttr]);
            }

            if ($disabled) {
                Html::addCssStyle($options, ['text-decoration' => 'line-through']);
            }

            if ($featured) {
                Html::addCssStyle($options, ['font-weight' => 'bold']);
            }
        }

        return $options;
    }
}
