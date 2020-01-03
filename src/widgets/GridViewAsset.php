<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:30:35
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы GridView.
 */
class GridViewAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/grid-view.css'
    ];

    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
        \yii\grid\GridViewAsset::class,
    ];
}
