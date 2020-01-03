<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:29:08
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\JqueryAsset;

/**
 * Ресурсы EditTabs.
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
