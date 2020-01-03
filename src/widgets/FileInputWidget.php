<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:29:12
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use dicr\file\FileInputWidgetTrait;
use yii\bootstrap4\InputWidget;

/**
 * Виджет редактирования файлов.
 */
class FileInputWidget extends InputWidget
{
    use FileInputWidgetTrait;
}
