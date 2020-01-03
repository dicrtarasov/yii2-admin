<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.01.20 00:48:05
 */

declare(strict_types=1);

namespace dicr\admin\assets;

use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;
use function is_array;

/**
 * Ресурсы пакета.
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
     * @param View $view
     * @param array $config
     * @return static
     * @throws InvalidConfigException
     * @noinspection PhpUnused
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
