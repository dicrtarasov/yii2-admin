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
     * Upsert (INSERT on duplicate keys UPDATE)
     *
     * @param boolean $runValidation
     * @param array $attributes
     * @return boolean
     */
    public function upsert($runValidation = true, $attributes = null)
    {
        if ($runValidation) {
            // reset isNewRecord to pass "unique" attribute validator because of upsert
            $this->setIsNewRecord(false);
            if (!$this->validate($attributes)) {
                \Yii::info('Model not inserted due to validation error.', __METHOD__);
                return false;
            }
        }

        if (!$this->isTransactional(self::OP_INSERT)) {
            return $this->upsertInternal($attributes);
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->upsertInternal($attributes);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Insert or update record.
     *
     * @param array $attributes
     * @return boolean
     */
    protected function upsertInternal($attributes = null)
    {
        if (!$this->beforeSave(true)) {
            return false;
        }

        // attributes for INSERT
        $insertValues = $this->getAttributes($attributes);

        // attributes for UPDATE exclude primaryKey
        $updateValues = array_slice($insertValues, 0);
        foreach (static::getDb()->getTableSchema(static::tableName())->primaryKey as $key) {
            unset($updateValues[$key]);
        }

        // process update/insert
        if (static::getDb()->createCommand()->upsert(static::tableName(), $insertValues, $updateValues ?: false)->execute() === false) {
            return false;
        }

        // reset isNewRecord after save
        $this->isNewRecord = false;

        // call handlers
        $this->afterSave(true, array_fill_keys(array_keys($insertValues), null));

        return true;
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
	        $formName = static::instance()->formName();
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
