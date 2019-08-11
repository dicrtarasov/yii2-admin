<?php
namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы ControlPanel.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class ControlPanelAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/control-panel.css'
    ];

    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
    ];
}