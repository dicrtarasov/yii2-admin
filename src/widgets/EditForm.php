<?php
namespace dicr\admin\widgets;

use yii\base\Model;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use dicr\helper\ArrayHelper;

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
        $this->view->registerAssetBundle(EditFormAsset::class);

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
     * @param array $options для form-group (для самого input использоваь inputOptions)
     * @return \yii\bootstrap4\ActiveField
     */
    public function fieldStatic(Model $model, string $attribute, array $options=[])
    {
        $options['options'] = $options['options'] ?? [];
        Html::addCssClass($options['options'], ['form-group', 'form-group-static', 'row']);

        return $this->field($model, $attribute, $options)->staticControl();
    }

    /**
     * Поле ID
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $options
     * - string|bool $url - добавить URL к ID
     * @return string|\yii\bootstrap4\ActiveField
     */
    public function fieldId(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        $url = ArrayHelper::remove($options['url'], null);
        if (!empty($url)) {
            if ($url === true) {
                $url = $model->url;
            }

            $html = Html::a(Html::encode($model->id), $url, ['target' => '_blank']);
            return $this->fieldHtml($model, 'id', $html, $options);
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

        if (!isset($options['inputOptions']['value'])) {
            $options['inputOptions']['value'] = !empty($model->created) ? \Yii::$app->formatter->asDate($model->created, 'php:d.m.Y H:i:s') : null;
        }

        return $this->fieldStatic($model, 'created', $options);
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

        if (!isset($options['inputOptions']['value'])) {
            $options['inputOptions']['value'] = !empty($model->updated) ? \Yii::$app->formatter->asDate($model->updated, 'php:d.m.Y H:i:s') : null;
        }

        return $this->fieldStatic($model, 'updated', $options);
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
        if (!isset($options['inputOptions']['value'])) {
            $options['inputOptions']['value'] = $model->disabled ?: date('Y-m-d H:i:s');
        }

        return $this->field($model, 'disabled', $options)->checkbox();
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
        if (!isset($options['parts']['{input}'])) {
            $options['parts']['{input}'] = $html;
        }

        $options['options'] = $options['options'] ?? [];
        Html::addCssClass($options['options'], ['form-group', 'form-group-static', 'row']);

        return $this->field($model, $attribute, $options);
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

        $url = $model->url;
        $html = Html::a(Html::encode(Url::to($url, true)), $url, [
            'class' => 'form-control-plaintext',
            'target' => '_blank'
        ]);

        return $this->fieldHtml($model, 'url', $html, $options);
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
