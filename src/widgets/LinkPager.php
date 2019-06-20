<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\Html;

/**
 * LinkPager
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class LinkPager extends \yii\bootstrap4\LinkPager
{
    /** @var bool|string */
    public $firstPageLabel = '<i class="fas fa-angle-double-left"></i>';

    /** @var string|bool */
    public $prevPageLabel = '<i class="fas fa-angle-left"></i>';

    /** @var string|bool */
    public $nextPageLabel = '<i class="fas fa-angle-right"></i>';

    /** @var bool|string */
    public $lastPageLabel = '<i class="fas fa-angle-double-right"></i>';

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\LinkPager::init()
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dicr-admin-widgets-link-pager');

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\LinkPager::run()
     */
    public function run()
    {
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/link-pager.css']
        ]);

        return parent::run();
    }
}