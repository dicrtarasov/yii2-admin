<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:24:25
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы Breadcrumbs.
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
