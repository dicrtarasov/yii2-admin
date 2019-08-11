<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы Breadcrumbs.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class BreadcrumbsAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/breadcrumbs.css'
    ];

    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
    ];
}
