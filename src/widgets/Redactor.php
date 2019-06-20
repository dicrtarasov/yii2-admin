<?php
namespace dicr\admin\widgets;

/**
 * HTML-редактор.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 * @link https://imperavi.com/redactor/docs/settings
 */
class Redactor extends \yii\redactor\widgets\Redactor
{
    /*
     * @var array The options underlying for setting up Redactor plugin.
     * @see http://imperavi.com/redactor/docs/settings
     */
    public $clientOptions = [
        'plugins' => [
            'fullscreen',
            'fontcolor', 'fontfamily', 'fontsize',
            'aligment', 'table', 'imagemanager', 'filemanager', 'video',
            'properties',

        ],

        'buttons' => [
            'fullscreen', 'format', 'fontcolor', 'fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'ul', 'ol',
            'link', 'image', 'file', 'video', 'html',
        ],

        'buttonsAddAfter' => false,

        'imageResizable' => true,
        'imagePosition' => true,
        'multipleUpload' => false,
        'maxHeight' => '15rem'
    ];
}
