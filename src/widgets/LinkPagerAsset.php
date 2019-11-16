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
 * Ресурсы LinkPager.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
