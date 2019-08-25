<?php
namespace dicr\admin\behaviors;

use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * Добавляет ActiveRecord метод upsert.
 * upsert($runValidation = true, $attributes = null)
 *
 * ВНИМАНИЕ!!! Необходимо использовать там где нет autogegerated id.
 *
 * @property \yii\db\ActiveRecord $owner
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class UpsertBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     * @see \yii\base\Behavior::attach()
     */
    public function attach($owner)
    {
        if (!is_a($owner, \yii\db\ActiveRecord::class)) {
            throw new InvalidArgumentException('owner должен быть типа ActiveRecord');
        }

        parent::attach($owner);
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
            $this->owner->setIsNewRecord(false);

            if (!$this->owner->validate($attributes)) {
                \Yii::info('Model not inserted due to validation error.', __METHOD__);
                return false;
            }
        }

        if (!$this->owner->isTransactional(ActiveRecord::OP_INSERT)) {
            return $this->upsertInternal($attributes);
        }

        $transaction = $this->owner->getDb()->beginTransaction();
        try {
            $result = $this->upsertInternal($attributes);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
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
        if (!$this->owner->beforeSave(true)) {
            return false;
        }

        // attributes for INSERT
        $insertValues = $this->owner->getAttributes($attributes);
        $db = $this->owner->getDb();
        $tableName = $this->owner->tableName();

        // attributes for UPDATE exclude primaryKey
        $updateValues = array_slice($insertValues, 0);
        foreach ($db->getTableSchema($tableName)->primaryKey as $key) {
            unset($updateValues[$key]);
        }

        // process update/insert
        if ($db->createCommand()->upsert($tableName, $insertValues, $updateValues ?: false)->execute() === false) {
            return false;
        }

        // reset isNewRecord after save
        $this->owner->isNewRecord = false;

        // call handlers
        $this->owner->afterSave(true, array_fill_keys(array_keys($insertValues), null));

        return true;
    }
}