<?php
namespace dicr\admin\widgets;

use dicr\admin\assets\BaseAdminAsset;
use dicr\widgets\ToastsAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\widgets\ActiveFormAsset;

/**
 * Ресурсы EditForm.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
