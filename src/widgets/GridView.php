<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\Html;

/**
 * GridView.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class GridView extends \yii\grid\GridView
{
    /** @var array */
    public $tableOptions = [
        'class' => 'table table-sm table-striped'
    ];

    /** @var string */
    public $layout = '{summary}<div class="table-responsive">{items}</div>{pager}';

    /** @var string аттрибут обозначающий удаленую */
    public $disabledAttr = 'disabled';

    /** @var string атрибут для выделения */
    public $featuredAttr;

    /**
     * {@inheritDoc}
     * @see \yii\grid\GridView::init()
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dicr-admin-widgets-grid-view');

        if (empty($this->pager['class'])) {
            $this->pager['class'] = LinkPager::class;
        }

        $this->rowOptions = function($model, $key, $index, $grid) {
            return $this->getRowOptions($model, $key, $index, $grid);
        };

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\grid\GridView::run()
     */
    public function run()
    {
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/grid-view.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        return parent::run();
    }

    /**
     * Возвращает опции строки.
     *
     * @param \yii\base\Model $model
     * @param string $key
     * @param int $index
     * @param GridView $grid
     * @return array
     */
    protected function getRowOptions($model, $key, $index, $grid)
    {
        $options = [];

        if (!empty($this->disabledAttr) && $model->hasProperty($this->disabledAttr) && !empty($model->{$this->disabledAttr})) {
            Html::addCssStyle($options, [
                'text-decoration' => 'line-through'
            ]);
        }

        if (!empty($this->featuredAttr) && $model->hasProperty($this->featuredAttr) && !empty($model->{$this->featuredAttr})) {
            Html::addCssStyle($options, [
                'font-weight' => 'bold'
            ]);
        }

        return $options;
    }
}