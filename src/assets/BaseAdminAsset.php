<?php
namespace dicr\admin\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;

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
	 */
	public static function registerConfig(View $view, array $config)
	{
	    $am = $view->getAssetManager();
	    $asset = new static($config);
	    $asset->publish($am);

	    $key = static::class . '-' . md5(Json::encode($config));
	    $am->bundles[$key] = $asset;
        $view->registerAssetBundle($key);

        return $asset;
	}
}
