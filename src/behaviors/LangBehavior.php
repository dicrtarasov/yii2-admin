<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

declare(strict_types = 1);
namespace dicr\admin\behaviors;

use dicr\helper\ArrayHelper;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use function call_user_func;
use function is_array;
use function is_string;

/**
 * Связь обьекта с описаниями на разных языках.
 *
 * Например, имеется модель товара Prod с полем id.
 *
 * Имеется языковая модель с описание товара на разных языках ProdLang с полями:
 * - prod_id (ссылка на товар по id),
 * - lang (код или id языка) и
 * - name (название товара для этого языка)
 *
 * Для связи товара с языковыми моделями определяем товару behavior таким образом:
 *
 * class Prod extends ActiveRecord
 * {
 *     public function behaviors()
 *     {
 *         return array_merge(parent::behaviors(), [
 *             'lang' => LangBehavior::class,
 *             'relationClass' => ProdLang::class,
 *             'relationLink' => ['prod_id' => 'id'],
 *             'langAttribute' => 'lang',
 *             // если в ProdLang определена связм с 'prod'
 *             'inverseOf' => 'prod'
 *        ]);
 *    }
 * }
 *
 * Также можно определить обратную связь в ProdLang
 *
 * class ProdLang extends ActiveRecord
 * {
 *     public function getProd()
 *     {
 *         return $this->hasOne(['id' => 'prod_id']);
 *     }
 * }
 *
 * Добавляет модели свойства $langs и $lang
 *
 * @property \dicr\admin\models\ActiveRecord $lang языковая модель для текущего языка
 * @property \dicr\admin\models\ActiveRecord[] $langs все языковые модели
 *
 * @property \yii\db\ActiveRecord $owner
 */
class LangBehavior extends Behavior
{
    /**
     * @var string класс языковой модели с данными языка для связи с родительским обьектом.
     * Должен быть подклассом ActiveRecord.
     */
    public $relationClass;

    /**
     * @var array описание связи hasMany языковой модели с родительской, например ['brand_id' => 'id']
     */
    public $relationLink;

    /**
     * @var string поле со значением языка в языковой модели для индексации связей ActiveQuery::indexBy
     */
    public $langAttribute = 'lang';

    /** @var string|null обратная связь ActiveQuery::inverseOf */
    public $inverseOf;

    /**
     * {@inheritDoc}
     * @throws \yii\base\InvalidConfigException
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        parent::init();

        if (! is_string($this->relationClass) || !is_a($this->relationClass, ActiveRecord::class, true)) {
            throw new InvalidConfigException('relationClass должен быть экземпляром ActiveRecord');
        }

        if (empty($this->relationLink) || ! is_array($this->relationLink)) {
            throw new InvalidConfigException('relationLink должен быть масивом с описанием связи hasMany');
        }

        if (empty($this->langAttribute)) {
            throw new InvalidConfigException('langAttr пустое значение аттрибута языка в модели');
        }
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\Behavior::attach()
     */
    public function attach($owner)
    {
        if (!is_a($owner, ActiveRecord::class)) {
            throw new InvalidArgumentException('owner должен быть типа ActiveRecord');
        }

        parent::attach($owner);
    }

    /**
     * Возвращает код текущего языка.
     *
     * @param string $lang
     * @return string
     */
    public static function currentLanguage(string $lang = null)
    {
        if (!isset($lang)) {
            $lang = Yii::$app->language;
        }

        $matches = null;
        if (preg_match('~^(\w+)\W~um', $lang)) {
            $lang = $matches[1];
        }

        return $lang;
    }

    /**
     * Возвращает связь с языковыми описаниями.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLangs()
    {
        $link = $this->owner->hasMany($this->relationClass, $this->relationLink)
            ->indexBy($this->langAttribute);

        if (isset($this->inverseOf)) {
            $link->inverseOf($this->inverseOf);
        }

        return $link;
    }

    /**
     * Устанавливает связи с языками
     *
     * @param \yii\db\ActiveRecord[] $langs
     * @return string[] errors
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function setLangs(array $langs)
    {
        /** @var \yii\db\ActiveRecord[] $langs */
        $langs = ArrayHelper::index($langs, $this->langAttribute);

        /** @var string[] ошибки */
        $errors = [];

        // сохраняем данные в базу
        foreach ($langs as $lang) {
            // устанавливаем родительскую связь чтобы прошла проверка validate
            foreach ($this->relationLink as $langAttr => $ownerAttr) {
                $lang->setAttribute($langAttr, $this->owner->getAttribute($ownerAttr));
            }

            // новые записи вставляем методом upsert для избежания конфликтов с существующими
            if ($lang->isNewRecord && $lang->hasMethod('upsert', true)) {
                // сохраняем
                if ($lang->upsert(true) === false) {
                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    $errors = array_merge($errors, $lang->firstErrors);
                }
            } elseif ($lang->update(true) === false) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $errors = array_merge($errors, $lang->firstErrors);
            }
        }

        // готовим критерии и удаляем лишние языки
        $conds = ['and'];
        foreach ($this->relationLink as $langAttr => $ownerAttr) {
            $conds[] = [$langAttr => $this->owner->getAttribute($ownerAttr)];
        }

        if (!empty($langs)) {
            $conds[] = ['not in', $this->langAttribute, array_keys($langs)];
        }

        // удаляем лишние
        call_user_func([$this->relationClass, 'deleteAll', $conds]);

        // очистка кэша
        TagDependency::invalidate(Yii::$app->cache, $this->relationClass);

        // обновляем кэш связей с языковыми моделями
        $this->owner->populateRelation('langs', $langs);

        // обновляем кэш связи с языковой моделью для текущего языка
        $currentLang = static::currentLanguage();
        if (isset($langs[$currentLang])) {
            $this->owner->populateRelation('lang', $langs[$currentLang]);
        } else {
            /** @noinspection PhpUndefinedFieldInspection */
            unset($this->owner->lang);
        }

        // возвращаем ошибки
        return $errors;
    }

    /**
     * Возвращает связь модели с языковой моделью для теущего языка.
     *
     * @param string|null $lang код языка, если не задан, то берется текущий из $app->language
     * @return \yii\db\ActiveQuery
     */
    public function getLang(string $lang = null)
    {
        // баг в yii - не дбавляется имя таблицы к полю onCondition,
        $fullName = sprintf('%s.[[%s]]', call_user_func([$this->relationClass, 'tableName']), $this->langAttribute);

        // описываем связь модели с языковой моделью для текущего языка
        $link = $this->owner->hasOne($this->relationClass, $this->relationLink)
            ->andOnCondition([$fullName => static::currentLanguage($lang)]);

        if (isset($this->inverseOf)) {
            $link->inverseOf($this->inverseOf);
        }

        return $link;
    }

    /**
     * Устанавливает языковую модель для текущего языка.
     *
     * @param \yii\db\ActiveRecord $lang
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function setLang(ActiveRecord $lang)
    {
        // получаем список всех языков их кэша связи
        /** @noinspection PhpUndefinedFieldInspection */
        $langs = $this->owner->langs;

        // код текущего языка
        $langCode = static::currentLanguage();

        // обновляем языковую модель для текущего языка
        $lang->setAttribute($this->langAttribute, $langCode);
        $langs[$langCode] = $lang;

        // сохраняем (связь обновится при сохранении всех)
        return empty($this->setLangs($langs));
    }
}
