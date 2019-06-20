<?php
namespace dicr\admin\widgets;

use dicr\admin\BaseAdminAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\Widget;

/**
 * Навигационная панель.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 *
 */
class NavBar extends Widget
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
        Html::addCssClass($this->options, [
            'widget' => 'dicr-admin-widgets-navbar',
            'navbar',
            'navbar-expand-md',
            'navbar-light'
        ]);

        $this->nav['optons'] = $this->nav['options'] ?? [];
        Html::addCssClass($this->nav['options'], 'navbar-nav');

        parent::init();
    }

    /**
     * {@inheritDoc}
     * @see \yii\bootstrap4\NavBar::run()
     */
    public function run()
    {
        BaseAdminAsset::registerConfig($this->view, [
            'css' => ['widgets/navbar.css'],
            'depends' => [BootstrapAsset::class]
        ]);

        ob_start();

        echo Html::beginTag('nav', $this->options);

        echo Html::beginTag('div', ['class' => 'container']);

        if (!empty($this->nav['options']['brandLabel']))

        echo Html::button(
            Html::tag('i', '', ['class' => 'navbar-toggler-icon']),
            [
                'class' => 'navbar-toggler',
                'data' => [
                    'toggle' => 'collapse',
                    'target' => '#admin-main-collapse'
                ]
            ]
        );

        echo Html::a('Admin', ['default/index'], ['class' => 'navbar-brand']);

        echo Html::beginTag('div', [
            'class' => 'collapse navbar-collapse',
            'id' => 'admin-main-collapse'
        ]);

		echo Nav::widget($this->nav);

		echo Html::endTag('div');

        echo ControlPanel::widget($this->controlPanel);

        echo Html::endTag('div');

        echo Html::endTag('nav');

        return ob_get_clean();
    }
}