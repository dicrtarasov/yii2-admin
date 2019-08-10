<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
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
        'edit-tabs.css'
    ];

    /** @var string[] */
    public $js = [
        'edit-tabs.js'
    ];

    /** @var string[] */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class
    ];
}