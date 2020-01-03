<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 00:47:36
 */

declare(strict_types=1);

namespace dicr\admin\assets;

use dicr\asset\FontAwesomeAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\JqueryAsset;

/**
 * Ресурсы админки.
 *
 * @noinspection PhpUnused
 */
class AdminAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'main/style.css'
    ];

    /** @var string[] */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        FontAwesomeAsset::class
    ];
}
