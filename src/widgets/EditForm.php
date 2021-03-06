<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.04.20 18:36:13
 */

declare(strict_types = 1);

namespace dicr\admin\widgets;

use dicr\file\FileInputWidget;
use dicr\helper\ArrayHelper;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Форма редактирования.
 *
 * @noinspection PhpUnused
 */
class EditForm extends ActiveForm
{
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

        if (! isset($this->options['enctype'])) {
            $this->options['enctype'] = 'multipart/form-data';
        }

        parent::init();

        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
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
     * Статическое поле
     *
     * @param Model $model
     * @param string $attribute
     * @param array $options для form-group (для самого input использовать inputOptions)
     * @return \yii\bootstrap4\ActiveField
     */
    public function fieldStatic(Model $model, string $attribute, array $options = [])
    {
        $options['options'] = $options['options'] ?? [];
        Html::addCssClass($options['options'], ['form-group', 'form-group-static', 'row']);

        // баг в bootstrap4 (staticControl не берет inputOptions, сука).
        $inputOptions = ArrayHelper::remove($options, 'inputOptions', []);

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        return $this->field($model, $attribute, $options)->staticControl($inputOptions);
    }

    /**
     * Поле ID
     *
     * @param ActiveRecord $model
     * @param array $options
     * - string|bool $url - добавить URL к ID
     * @return string|\yii\bootstrap4\ActiveField
     * @noinspection PhpUnused
     */
    public function fieldId(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        $url = ArrayHelper::remove($options, 'url');
        if (! empty($url)) {
            if ($url === true) {
                $url = $model->{'url'};
            }

            $options['inputOptions'] = array_merge([
                'target' => '_blank'
            ], $options['inputOptions'] ?? []);

            $html = Html::a(Html::encode($model->{'id'}), $url, $options['inputOptions']);

            return $this->fieldHtml($model, 'id', $html, $options);
        }

        return $this->fieldStatic($model, 'id', $options);
    }

    /**
     * Поле Created
     *
     * @param ActiveRecord $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     * @throws InvalidConfigException
     * @noinspection PhpUnused
     */
    public function fieldCreated(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        if (! isset($options['inputOptions']['value'])) {
            $options['inputOptions']['value'] =
                ! empty($model->created) ? Yii::$app->formatter->asDate($model->created, 'php:d.m.Y H:i:s') : null;
        }

        return $this->fieldStatic($model, 'created', $options);
    }

    /**
     * Поле Updated
     *
     * @param ActiveRecord $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     * @throws InvalidConfigException
     * @noinspection PhpUnused
     */
    public function fieldUpdated(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        if (! isset($options['inputOptions']['value'])) {
            $options['inputOptions']['value'] =
                ! empty($model->updated) ? Yii::$app->formatter->asDate($model->updated, 'php:d.m.Y H:i:s') : null;
        }

        return $this->fieldStatic($model, 'updated', $options);
    }

    /**
     * Поле Disabled
     *
     * @param Model $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     * @noinspection PhpUnused
     */
    public function fieldDisabled(Model $model, array $options = [])
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->field($model, 'disabled', $options)->checkbox([
            'value' => $model->disabled ?: date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Поле Enabled.
     *
     * @param Model $model
     * @param array $options
     * @return \yii\bootstrap4\ActiveField
     * @noinspection PhpUnused
     */
    public function fieldEnabled(Model $model, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->field($model, 'enabled', $options)->checkbox();
    }

    /**
     * Поле с Html-контентом.
     *
     * @param Model $model
     * @param string $attribute
     * @param string $html
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     */
    public function fieldHtml(Model $model, string $attribute, string $html, array $options = [])
    {
        if (! isset($options['parts']['{input}'])) {
            $options['parts']['{input}'] = $html;
        }

        $options['options'] = $options['options'] ?? [];
        Html::addCssClass($options['options'], ['form-group', 'form-group-static', 'row']);

        return $this->field($model, $attribute, $options);
    }

    /**
     * Поле URL.
     *
     * @param ActiveRecord $model
     * @param array $options
     * @return string|\yii\bootstrap4\ActiveField
     * @noinspection PhpUnused
     */
    public function fieldUrl(ActiveRecord $model, array $options = [])
    {
        if ($model->isNewRecord) {
            return '';
        }

        $options['inputOptions'] = $options['inputOptions'] ?? [];
        Html::addCssClass($options['inputOptions'], 'form-control-plaintext');

        if (! isset($options['inputOptions']['target'])) {
            $options['inputOptions']['target'] = '_blank';
        }

        /** @noinspection PhpUndefinedFieldInspection */
        $url = $model->url;

        $html = Html::a(Html::encode(Url::to($url, true)), $url, $options['inputOptions']);

        return $this->fieldHtml($model, 'url', $html, $options);
    }

    /**
     * Редактор текста.
     *
     * @param Model $model
     * @param string $attribute
     * @param array $options field options
     * @return \yii\bootstrap4\ActiveField
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function fieldText(Model $model, string $attribute, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->field($model, $attribute, $options)->widget(RedactorWidget::class);
    }

    /**
     * Поле ввода картинок.
     *
     * @param Model $model
     * @param string $attribute
     * @param int $limit
     * @param array $options
     * @return \yii\bootstrap4\ActiveField
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function fieldImages(Model $model, string $attribute, int $limit = 0, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
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
     * @param Model $model
     * @param string $attribute
     * @param int $limit
     * @param array $options
     * @return \yii\bootstrap4\ActiveField
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function fieldFiles(Model $model, string $attribute, int $limit = 0, array $options = [])
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->field($model, $attribute, $options)->widget(FileInputWidget::class, [
            'layout' => 'files',
            'limit' => $limit,
            'removeExt' => true
        ]);
    }
}
