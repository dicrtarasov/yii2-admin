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
class EditLinkColumn extends DataColumn
{
    /**
     * {@inheritDoc}
     * @see \yii\grid\DataColumn::renderDataCellContent()
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            $value = $this->getDataCellValue($model, $key, $index);

            return Html::a($this->grid->formatter->format($value, $this->format), ['edit', 'id' => $model->id], [
                'data-pjax' => false
            ]);
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
