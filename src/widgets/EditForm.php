<?php
namespace dicr\admin\widgets;

use app\widgets\FileInputWidget;
use dicr\admin\BaseAdminAsset;
use dicr\widgets\ToastsAsset;
use yii\base\Model;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Форма редактирования.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EditForm extends ActiveForm
{
    /** @var string */
    public $method = 'post';

    /** @var string */
    public $action = '';

    /** @var string */
    public $layout = 'horizontal';

    /** @var bool */
    public $enableAjaxValidation = true;

    /** @var array */
    public $fieldConfig = [
        'horizontalCssClasses' => [
            'label' => ['col-sm-3', 'col-xl-2'],
            'offset' => 'offset-sm-3 offset-xl-2',
            'wrapper' => 'col-sm-9 col-xl-10',
            'hint' => '',
            'error' => '',
        ]
    ];

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\ActiveForm::init()
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dicr-admin-widgets-edit-form');

        if (!isset($this->options['enctype'])) {
            $this->options['enctype'] = 'multipart/form-data';
        }

        parent::init();

        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId(true);
        }
    }

    /**
     * {@inheritDoc}
     * @see \yii\widgets\ActiveForm::run()
     */
    public function run()
    {
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/edit-form.css'],
            'depends' => [
                BootstrapAsset::class,
                ToastsAsset::class
            ]
        ]);

        $this->view->registerJs("
            $('#{$this->options['id']}').on('afterValidate', function (event, messages, errorAttributes) {
                if (messages) {
                    $.each(messages, function(field, messages) {
                        if (messages && messages[0]) {
                            window.dicr.widgets.toasts.error(messages[0]);
                        }
                    });
                }
            });
        ");

        return parent::run();
    }

    /**
     * Сатическое поле
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param string $value
     * @return \yii\bootstrap4\ActiveField
     */
    public function fieldStatic(Model $model, string $attribute, array $options=[])
    {
        return $this->field($model, $attribute, array_merge([
            'options' => [
                'class' => ['form-group', 'row', 'mb-0']
            ]
        ], $options))->staticControl();
    }

    /**
     * Поле ID
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     */
    public function fieldId(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        return $this->fieldStatic($model, 'id', $options);
    }

    /**
     * Поле Created
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $options
     * @return string|\yii\widgets\ActiveField
     */
    public function fieldCreated(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        return $this->fieldStatic($model, 'created', array_merge([
            'inputOptions' => [
                'value' => \Yii::$app->formatter->asDate($model->created, 'php:d.m.Y H:i:s')
            ]
        ], $options));
    }

    /**
     * Поле Updated
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     */
    public function fieldUpdated(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        return $this->fieldStatic($model, 'updated', array_merge([
            'inputOptions' => [
                'value' => \Yii::$app->formatter->asDate($model->updated, 'php:d.m.Y H:i:s')
            ]
        ], $options));
    }

    /**
     * Поле Disabled
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $options
     * @return string|\yii\widgets\ActiveField
     */
    public function fieldDisabled(Model $model, array $options = [])
    {
        return $this->field($model, 'disabled', $options)->checkbox([
            'value' => $model->disabled ?: date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Поле Enabled.
     *
     * @param \yii\base\Model $model
     * @param array $options
     * @return \yii\widgets\ActiveField
     */
    public function fieldEnabled(Model $model, array $options = [])
    {
        return $this->field($model, 'enabled', $options)->checkbox();
    }

    /**
     * Поле с Html-кнтентом.
     *
     * @param \yii\db\ActiveRecord $model
     * @param string $html
     * @param array $options
     * @return string|\yii\widgets\ActiveField
     */
    public function fieldHtml(Model $model, string $attribute, string $html, array $options = [])
    {
        return $this->field($model, $attribute, [
            'options' => [
                'class' => ['form-group', 'row', 'mb-0']
            ],
            'parts' => [
                '{input}' => $html
            ]
        ]);
    }

    /**
     * Поле URL
     *
     * @param \yii\base\Model $model
     * @param array $options
     * @return string|\yii\widgets\ActiveField
     */
    public function fieldUrl(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        return $this->fieldHtml($model, 'url', Html::a(Html::encode(Url::to($model->url)), $model->url, [
            'class' => 'form-control-plaintext field-url',
            'target' => '_blank'
        ]));
    }

    /**
     * Редактор текста.
     *
     * @param \yii\db\ActiveRecord $model
     * @param string $attribute
     * @param array $options field options
     * @return \yii\widgets\ActiveField
     */
    public function fieldText(ActiveRecord $model, string $attribute, array $options = [])
    {
        return $this->field($model, $attribute, $options)->widget(Redactor::class);
    }

    /**
     * Поле ввода картинок.
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param int $limit
     * @param array $options
     * @return \yii\widgets\ActiveField
     */
    public function fieldImages(Model $model, string $attribute, int $limit = 0, array $options = [])
    {
        return $this->field($model, $attribute, $options)->widget(FileInputWidget::class, [
            'layout' => 'images',
            'limit' => $limit,
            'accept' => 'image/*',
            'removeExt' => true
        ]);
    }

    /**
     * Поле ввода файлов.
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param int $limit
     * @param array $options
     * @return \yii\widgets\ActiveField
     */
    public function fieldFiles(Model $model, string $attribute, int $limit = 0, array $options = [])
    {
        return $this->field($model, $attribute, $options)->widget(FileInputWidget::class, [
            'layout' => 'files',
            'limit' => $limit,
            'removeExt' => true
        ]);
    }
}
