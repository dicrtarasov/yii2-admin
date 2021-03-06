<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:36:35
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use yii\bootstrap4\Html;
use yii\grid\DataColumn;

/**
 * Булева колонка таблицы.
 *
 * @noinspection PhpUnused
 */
class FlagColumn extends DataColumn
{
    /** @var array */
    public $headerOptions = [
        'class' => 'text-center'
    ];

    /** @var array */
    public $contentOptions = [
        'class' => 'text-center'
    ];

    /**
     * {@inheritDoc}
     * @see \yii\grid\DataColumn::renderDataCellContent()
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            return Html::tag('i', '', [
                'class' => [$this->getDataCellValue($model, $key, $index) ? 'fas' : 'far', 'fa-star']
            ]);
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
