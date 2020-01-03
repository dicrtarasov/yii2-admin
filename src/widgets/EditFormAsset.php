<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:28:08
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use dicr\widgets\ToastsAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\widgets\ActiveFormAsset;

/**
 * Ресурсы EditForm.
 */
class EditFormAsset extends BaseAdminAsset
{
    /** @var string[] */
    public $css = [
        'widgets/edit-form.css'
    ];

    /** @var string[] */
    public $depends = [
        BootstrapAsset::class,
        ToastsAsset::class,
        ActiveFormAsset::class
    ];
}
