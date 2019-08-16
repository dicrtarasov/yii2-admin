<?php
namespace dicr\admin\widgets;

use dicr\file\FileInputWidgetTrait;
use yii\bootstrap4\InputWidget;

/**
 * Виджет редактирования файлов.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class FileInputWidget extends InputWidget
{
    use FileInputWidgetTrait;
}