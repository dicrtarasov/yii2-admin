<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:30:55
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;

/**
 * Ресурсы LinkPager.
 */
class LinkPagerAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/link-pager.css'
    ];

    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
    ];
}
