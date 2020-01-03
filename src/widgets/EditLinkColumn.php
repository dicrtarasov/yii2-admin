<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 01:28:02
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use yii\bootstrap4\Html;
use yii\grid\DataColumn;

/**
 * Булевая колонка таблицы.
 *
 * @noinspection PhpUnused
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
