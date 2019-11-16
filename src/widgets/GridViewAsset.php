<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

declare(strict_types = 1);
namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы GridView.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
