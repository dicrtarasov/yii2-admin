<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

declare(strict_types = 1);
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
