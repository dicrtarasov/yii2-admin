<?php
namespace dicr\admin\models;

use yii\caching\TagDependency;

/**
 * Базовый элемент
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Очищает кэш моделей класса
     */
    public static function invalidateCache()
    {
        TagDependency::invalidate(\Yii::$app->cache, [static::class]);
    }

    /**
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterSave()
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // invalidate entity cache
        if ($insert || !empty($changedAttributes)) {
            static::invalidateCache();
        }
    }

    /**
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterDelete()
     */
    public function afterDelete()
    {
        // очищаем кеш после удаления модели
        static::invalidateCache();
    }

	/**
	 * Создает и загружает массив моделей из табулярных данных.
	 *
	 * Чтобы каждый раз при сохранении не удалять/пересоздавать все табулярные модели заново,
	 * можно использовать уже существующие в базе, для обновления при помощи save().
	 *
	 * В $current передается массив существующих в базе моделей для загрузки. Этот массив должен быть индексирован по такому же
	 * ключу как и данные формы.
	 *
	 * В $data[$formName] передается массив данных отправленных моделей, индексированный по ключу-идентификатору модели. Если модель
	 * с таким ключем отсутствует в массиве существующих ($models), то создается новая.
	 *
	 * Если $current не задан, то все модели будут созданы из данных.
	 *
	 * Модели из $current, ключ которых отсутствует в данных формы не возвращаются.
	 *
	 * @param \yii\base\Model[] $models существующие модели
	 *    требуется чтобы массив был проиндексирован по таким же ключам как в загружаемой форме
	 * @param array $data табулярные данные, например из $_POST
	 * @param string $formName
	 * @return static[]
	 */
	public static function loadAll(array $currentModels = [], array $data, string $formName = null)
	{
	    if (empty($currentModels)) {
	        $currentModels = [];
	    }

	    if (!isset($formName)) {
	        $formName = self::instance()->formName();
	    }

	    // коррекируем данные под форму
	    if ($formName !== '') {
	        $data = $data[$formName] ?? [];
	    }

	    $models = [];
	    foreach ($data as $key => $modelData) {
	        $model = $currentModels[$key] ?? new static();
	        $model->load($modelData, '');
	        $models[$key] = $model;
	    }

	    return $models;
	}
}
