<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:24:07
 */

declare(strict_types=1);

namespace dicr\admin\widgets;

use Yii;
use yii\base\Model;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * Форма фильтра данных.
 *
 * @noinspection PhpUnused
 */
class FilterForm extends ActiveForm
{
    /** @var string */
    public $method = 'get';

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\ActiveForm::init()
     */
    public function init()
    {
        if (empty($this->action)) {
            $this->action = ['/' . Yii::$app->requestedRoute];
        }

        if (!isset($this->fieldConfig['template'])) {
            $this->fieldConfig['template'] = '{beginWrapper}{input}{hint}{error}{endWrapper}';
        }

        if (!isset($this->options['data-pjax']) && !isset($this->options['data']['pjax'])) {
            $this->options['data']['pjax'] = 1;
        }

        Html::addCssClass($this->options, 'dicr-admin-widgets-filter-form');

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\widgets\ActiveForm::run()
     */
    public function run()
    {
        $this->view->registerJs("$('#{$this->options['id']}').on('change', ':input', function() {
                $(this).closest('form').submit()
            })");

        return parent::run();
    }

    /**
     * {@inheritDoc}
     * @param Model $model
     * @return ActiveField
     * @see \yii\bootstrap4\ActiveForm::field()
     */
    public function field($model, $attribute, $options = [])
    {
        $attrName = Html::getAttributeName($attribute);
        $prompt = '- ' . mb_strtolower($model->getAttributeLabel($attrName)) . ' -';

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::field($model, $attribute, array_merge([
            'inputOptions' => [
                'placeholder' => $model->getAttributeLabel($attribute),
                'prompt' => $prompt
            ]
        ], $options))->input('search'); // по-умолчанию форматируем в тип search
    }

    /**
     * Булево поле фильтра
     *
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return ActiveField
     */
    public function fieldBoolean(Model $model, string $attribute, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
        return $this->field($model, $attribute)->dropDownList([
            0 => 'нет',
            1 => 'да'
        ], $options);
    }

    /**
     * Поле Enabled
     *
     * @param Model $model
     * @param array $options
     * @return ActiveField
     * @noinspection PhpUnused
     */
    public function fieldEnabled(Model $model, array $options = [])
    {
        return $this->fieldBoolean($model, 'enabled', $options);
    }

    /**
     * Поле фильтра по disabled.
     *
     * @param Model $model
     * @param array $options
     * @return ActiveField
     * @noinspection PhpUnused
     */
    public function fieldDisabled(Model $model, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
        return $this->field($model, 'disabled')->dropDownList([
            0 => 'включено',
            1 => 'отключено'
        ], $options);
    }
}
