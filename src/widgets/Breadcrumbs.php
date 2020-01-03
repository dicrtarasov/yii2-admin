<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:24:10
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use yii\bootstrap4\Html;

/**
 * Хлебные крошки.
 *
 * @noinspection PhpUnused
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
