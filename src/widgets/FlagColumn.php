<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

declare(strict_types = 1);
namespace dicr\admin\widgets;

use yii\bootstrap4\Html;
use yii\grid\DataColumn;

/**
 * Булевая колонка таблицы.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
