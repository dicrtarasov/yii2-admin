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
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\JqueryAsset;

/**
 * Ресурсы EditTabs.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EditTabsAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/edit-tabs.css'
    ];

    /** @var string[] */
    public $js = [
        'widgets/edit-tabs.js'
    ];

    /** @var string[] */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}
