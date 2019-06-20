<?php
namespace dicr\admin\widgets;

use yii\base\Model;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * Форма фильтра данных.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
            $this->action = ['/' . \Yii::$app->requestedRoute];
        }

        if (!isset($this->fieldConfig['template'])) {
            $this->fieldConfig['template'] = '{beginWrapper}{input}{hint}{error}{endWrapper}';
        }

        if (!isset($this->options['data-pjax']) && !isset($this->options['data']['pjax'])) {
            $this->options['data']['pjax'] = 1;
        }

        Html::addCssClass($this->options, 'dicr-admin-widgets-filter-form');

        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * {@inheritDoc}
     * @see \yii\widgets\ActiveForm::run()
     */
    public function run()
    {
        $this->view->registerJs(
            "$('#{$this->options['id']}').on('change', ':input', function() {
                $(this).closest('form').submit()
            })"
        );

        return parent::run();
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\ActiveForm::field()
     */
    public function field($model, $attribute, $options = [])
    {
        return parent::field($model, $attribute, array_merge([
            'inputOptions' => [
                'placeholder' => $model->getAttributeLabel($attribute),
                'prompt' => '- ' . mb_strtolower($model->getAttributeLabel($attribute)) . ' -'
            ]
        ], $options));
    }

    /**
     * Поле фильтра по disabled.
     *
     * @param \yii\base\Model $model
     * @param array $options
     * @return \yii\bootstrap4\ActiveField
     */
    public function fieldDisabled(Model $model, array $options=[])
    {
        return $this->field($model, 'disabled')->dropDownList([
            0 => 'включено',
            1 => 'отключено'
        ], $options);
    }

    /**
     * Булево поле фильтра
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return \yii\bootstrap4\ActiveField
     */
    public function fieldBoolean(Model $model, string $attribute, array $options = [])
    {
        return $this->field($model, $attribute)->dropDownList([
            0 => 'нет',
            1 => 'да'
        ], $options);
    }
}