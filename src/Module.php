<?php
/**
 * Created by PhpStorm.
 * User: Mix
 * Date: 30.10.2018
 * Time: 18:15
 */

namespace mix8872\includes;

use Yii;
use yii\base\InvalidConfigException;

/**
 * @property string $directory
 */
class Module extends \yii\base\Module
{
    public $directory;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();

        if (!$this->directory) {
            throw new InvalidConfigException('Property "directory" must defined!');
        }

        $this->directory = trim(strip_tags($this->directory));
        $this->directory = str_replace(['/', '\\'], '', $this->directory);
    }

    /**
     * Register translation for module
     */
    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['includes'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru-RU',
            'basePath' => '@vendor/mix8872/yii2-included-areas/src/messages',
        ];

    }
}