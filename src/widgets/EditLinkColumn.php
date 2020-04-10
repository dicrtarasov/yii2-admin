<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:38:25
 */

declare(strict_types = 1);

namespace dicr\admin\widgets;

use yii\base\Model;
use yii\bootstrap4\Html;
use yii\grid\DataColumn;

/**
 * Колонка-ссылка на редактирование объекта.
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
        if (($this->content === null) && ($model instanceof Model) && $model->canGetProperty('id')) {
            $value = $this->getDataCellValue($model, $key, $index);

            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            return Html::a($this->grid->formatter->format($value, $this->format),
                ['edit', 'id' => $model->id],
                ['data-pjax' => false]
            );
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
