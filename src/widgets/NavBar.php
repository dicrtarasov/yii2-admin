<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Nav;

/**
 * Навигационная панель.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 *
 */
class NavBar extends \yii\bootstrap4\NavBar
{
    /** @var array опции навигации \yii\bootstrap4\Nav */
    public $nav = [];

    /** @var array опции панели \app\modules\admin\widgets\ControlPanel */
    public $controlPanel = [];

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\NavBar::init()
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (!isset($this->options['class']) || empty($this->options['class'])) {
            Html::addCssClass($this->options, ['navbar-expand-md', 'navbar-light', 'bg-light']);
        }

        Html::addCssClass($this->options, ['widget' => 'navbar', 'dicr-admin-widgets-navbar']);

        $navOptions = $this->options;
        $navTag = ArrayHelper::remove($navOptions, 'tag', 'nav');
        if (!isset($this->innerContainerOptions['class'])) {
            Html::addCssClass($this->innerContainerOptions, 'container');
        }

        if (!isset($this->collapseOptions['id'])) {
            $this->collapseOptions['id'] = "{$this->options['id']}-collapse";
        }

        $brand = '';

        if ($this->brandImage !== false) {
            $this->brandLabel = Html::img($this->brandImage);
        }

        if ($this->brandLabel !== false) {
            Html::addCssClass($this->brandOptions, ['widget' => 'navbar-brand']);
            if ($this->brandUrl === null) {
                $brand = Html::tag('span', $this->brandLabel, $this->brandOptions);
            } else {
                $brand = Html::a(
                    $this->brandLabel,
                    $this->brandUrl === false ? \Yii::$app->homeUrl : $this->brandUrl,
                    $this->brandOptions
                );
            }
        }

        Html::addCssClass($this->collapseOptions, ['collapse' => 'collapse', 'widget' => 'navbar-collapse']);
        $collapseOptions = $this->collapseOptions;
        $collapseTag = ArrayHelper::remove($collapseOptions, 'tag', 'div');

        $this->nav['optons'] = $this->nav['options'] ?? [];
        Html::addCssClass($this->nav['options'], 'navbar-nav');

        // начало вывода компонента
        echo Html::beginTag($navTag, $navOptions) . "\n";

        // начало container
        if ($this->renderInnerContainer) {
            echo Html::beginTag('div', $this->innerContainerOptions)."\n";
        }

        // кнопка раскрытия для мобильных
        echo $this->renderToggleButton() . "\n";

        // бренд
        echo $brand . "\n";

        // начало collapse
        echo Html::beginTag($collapseTag, $collapseOptions) . "\n";
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\NavBar::run()
     */
    public function run()
    {
        // выводим навигацию
        if (!empty($this->nav)) {
            echo Nav::widget($this->nav);
        }

		// закрываем collapse
        $tag = ArrayHelper::remove($this->collapseOptions, 'tag', 'div');
        echo Html::endTag($tag) . "\n";

        // выводим control-panel
        if (!empty($this->controlPanel)) {
            echo ControlPanel::widget($this->controlPanel);
        }

        // закрываем container
        if ($this->renderInnerContainer) {
            echo Html::endTag('div') . "\n";
        }

        // закрываем navbar
        $tag = ArrayHelper::remove($this->options, 'tag', 'nav');
        echo Html::endTag($tag);

        // загружаем стиль
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/navbar.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        // регистрируем плагин
        BootstrapPluginAsset::register($this->getView());
    }
}
