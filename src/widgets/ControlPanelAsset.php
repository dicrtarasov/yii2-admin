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
