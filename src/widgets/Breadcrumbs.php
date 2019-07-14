<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
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
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/breadcrumbs.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        return parent::run();
    }
}