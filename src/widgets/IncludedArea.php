<?php

namespace mix8872\includes\widgets;

use Yii;
use yii\base\Widget;

class IncludedArea extends Widget
{
    public $directory;
    public $name;

    public function init()
    {
        parent::init();
        $module = Yii::$app->getModule('included-areas');
        $rootDir = $module->directory;
        $this->directory = trim("$rootDir/{$this->directory}", '/');
    }

    public function run()
    {
        $directoryPath = Yii::getAlias("@webroot/{$this->directory}");
        if (!is_dir($directoryPath) && !mkdir($directoryPath, 0755, true) && !is_dir($directoryPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directoryPath));
        }

        $name = preg_replace('/[^\w_\-]/u', '_', $this->name);
        $filePath = "$directoryPath/{$name}_inc.php";

        if (!file_exists($filePath)) {
            touch($filePath);
        }

        return $this->renderFile($filePath);
    }
}