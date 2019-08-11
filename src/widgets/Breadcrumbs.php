<?php
namespace dicr\admin\widgets;

use yii\bootstrap4\Html;

/**
 * Хлебные крошки.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class Breadcrumbs extends \yii\bootstrap4\Breadcrumbs
{
    /** @var array */
    public $homeLink = [
        'label' => 'Главная',
        'url' => ['/admin/default/index'],
    ];

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Breadcrumbs::init()
     */
    public function init()
    {
        Html::addCssClass($this->navOptions, 'dicr-admin-widgets-breadcrumbs');
        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\Breadcrumbs::run()
     */
    public function run()
    {
        $this->view->registerAssetBundle(BreadcrumbsAsset::class);

        return parent::run();
    }
}
