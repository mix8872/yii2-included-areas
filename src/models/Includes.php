<?php

namespace mix8872\includes\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class Includes extends Model
{
    public $title;
    public $group;
    public $type;
    public $content;
    public $name;
    public $path;
    public $meta;

    protected const COMMENT_END = 0;
    protected const COMMENT_STRING = 1;
    protected const COMMENT_AREA = 2;

    public const TYPE_HTML = 0;
    public const TYPE_PHP = 1;
    public const TYPE_PLAIN = 2;

    public static $types = [
        self::TYPE_HTML => 'html',
        self::TYPE_PHP => 'php',
        self::TYPE_PLAIN => 'plain',
    ];

    public function rules()
    {
        return [
            [['content'], 'string'],
            [['type'], 'integer'],
            [['meta'], 'each', 'rule' => ['safe']],
            [['title', 'group', 'name', 'path'], 'string', 'max' => 255],
            [['title', 'group'], 'default', 'value' => '']
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('includes', 'Заголовок'),
            'group' => Yii::t('includes', 'Группа'),
            'type' => Yii::t('includes', 'Тип'),
            'content' => Yii::t('includes', 'Контент'),
            'meta' => Yii::t('includes', 'Мета данные'),
        ];
    }

    public static function find()
    {
        static::_getAreas($areas);
        return $areas;
    }

    public function save()
    {
        $path = Yii::getAlias("@webroot/{$this->path}");
        if (!file_exists($path)) {
            touch($path);
        }
        if (!is_writable($path)) {
            $this->addError('path', 'File is readonly!');
        }
        if ($this->hasErrors('path')) {
            return false;
        }

        if (!$fp = fopen(Yii::getAlias("@webroot/{$this->path}"), 'w+')) {
            $this->addError('path', 'Can\'t open the file!');
            return false;
        }

        $meta = <<< META
        <?php
        /*
         * @title $this->title
         * @group $this->group
         * @type $this->type
        META;

        foreach ($this->meta as $i => $item) {
            $meta .= PHP_EOL . "* @$i $item";
        }
        $meta .= PHP_EOL . '**/' . PHP_EOL . '?>' . PHP_EOL;

        $isWriteError = false;
        if (!fwrite($fp, $meta . $this->content)) {
            $isWriteError = true;
        }
        fclose($fp);

        return !$isWriteError;
    }

    protected static function _getAreas(&$areas = null, $dir = null)
    {
        if (!$module = Yii::$app->getModule('included-areas')) {
            return;
        }
        if (!$module->hasProperty('directory')) {
            throw new InvalidConfigException('Wrong "includes" module or module hasn\'t contains "directory" attribute!');
        }
        if (!$dir) {
            $dir = $module->directory;
        }

        foreach (glob("$dir/*", GLOB_NOSORT) as $filename) {
            if (is_dir($filename)) {
                static::_getAreas($areas, (string)$filename);
            } else {
                $fp = fopen(Yii::getAlias("@webroot/$filename"), 'r') or error_log("Can't open the file: $filename");
                if ($fp) {
                    $title = '';
                    $group = '';
                    $type = self::TYPE_HTML;
                    $content = '';
                    $name = str_replace(['/', '.php'], ['-', ''], $filename);
                    if (!$meta = static::_getMeta($fp, $last)) {
                        $content .= $last;
                    }
                    $content .= fread($fp, filesize(Yii::getAlias("@webroot/$filename")));

                    if (isset($meta['title'])) {
                        $title = $meta['title'];
                        unset($meta['title']);
                    }
                    if (isset($meta['group'])) {
                        $group = $meta['group'];
                        unset($meta['group']);
                    }
                    if (isset($meta['type'])) {
                        $type = (int)$meta['type'];
                        unset($meta['type']);
                    }

                    $areas[$name] = new self([
                        'title' => $title,
                        'group' => $group,
                        'type' => $type,
                        'content' => $content,
                        'name' => $name,
                        'path' => $filename,
                        'meta' => $meta
                    ]);
                }
            }
        }
    }

    protected static function _getMeta($fp, &$last)
    {
        $last = null;
        $meta = array();
        $isComment = false;
        $isPhp = false;
        while (!feof($fp)) {
            $myText = fgets($fp);
            switch (true) {
                case strpos($myText, '<?') !== false:
                    $isPhp = true;
                    break;
                case strpos($myText, '/*') === 0:
                    $isComment = self::COMMENT_AREA;
                    break;
                case strpos($myText, '//') === 0:
                    $isComment = self::COMMENT_STRING;
                    break;
            }
            if (!$isPhp && ftell($fp) > 2) {
                $last = $myText;
                return $meta;
            }
            if (strpos($myText, '*/') !== false) {
                $isComment = self::COMMENT_END;
            }
            $pos = strpos($myText, '@');
            if ($isComment !== false && $pos !== false) {
                if ($isComment === self::COMMENT_END) {
                    $myText = str_replace('*/', '', $myText);
                }
                $myText = mb_substr($myText, $pos);
                [$key, $value] = explode(' ', trim($myText), 2);
                $meta[ltrim($key, '@')] = trim($value ?? '');
            }
            if ($isComment === self::COMMENT_STRING || $isComment === self::COMMENT_END) {
                $isComment = false;
            }
            if (strpos($myText, '?>') !== false) {
                break;
            }
        }
        return $meta;
    }
}