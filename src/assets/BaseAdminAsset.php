<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor A Tarasov <develop@dicr.org>
 */

declare(strict_types = 1);
namespace dicr\admin\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;
use function is_array;

/**
 * Ресурсы пакета.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class BaseAdminAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@dicr/admin/res';

    /**
     * {@inheritDoc}
     * @see \yii\web\AssetBundle::init()
     */
    public function init()
    {
        $this->publishOptions = array_merge([
            'forceCopy' => YII_DEBUG
        ], $this->publishOptions);

        parent::init();
    }

    /**
     * Комбинированный метод для создания и регистрации
     *
     * @param \yii\web\View $view
     * @param array $config
     * @return static
     * @throws \yii\base\InvalidConfigException
     */
    public static function registerConfig(View $view, array $config)
    {
        $am = $view->getAssetManager();
        $asset = new static($config);
        $asset->publish($am);

        $key = static::class . '-' . md5(Json::encode($config));
        if (is_array($am->bundles)) {
            /** @noinspection OffsetOperationsInspection */
            $am->bundles[$key] = $asset;
        }

        $view->registerAssetBundle($key);

        return $asset;
    }
}
