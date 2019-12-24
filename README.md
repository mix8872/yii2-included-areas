Yii2 included  areas module
---------------
This is module for creating included areas (like in the bitrix but without hermitage) in the yii2.

Management of included areas based on bs4, so you must use bs4... bs3 is dead, sorry 
(or you can fork this shit and do like you want).

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mix8872/included-areas
```

or add

```json
"mix8872/yii2-included-areas": "dev-master"
```

to the `require` section of your `composer.json`.

Usage
-----

Edit `modules` section of your application config file.

Common:

```php
'modules' => [
	'included-areas' => [
		'class' => 'mix8872\includes\Module',
		'directory' => 'includes',
		'as access' => [
			'class' => 'yii\filters\AccessControl',
			'rules' => [
				[
					'allow' => true,
					'roles' => ['admin'],
                    'matchCallback' => function () {
                        return Yii::$app->id === 'app-backend';
                    }
				],
			]
		],
	],
// next modules config	
],
```
In the config you must specify `directory` property. Directory named by this property 
will created in your web folder (and this folder must be common between frontend and backend web folders).
After first load of the page with widget, the included areas files will be created in this folder.

Also you can create file manually in folder defined in the config, but it should be noted that widget append _inc suffix for all files
so you must create files like `awesome_file_inc.php` or `sometext_inc.php`.

----

**IMPORTANT! You must specify `as access` for module!**

----

Then you must place widget in the your view file in this way:

```php
<?php
use mix8872\includes\widgets\IncludedArea;
?>

<?= IncludedArea::widget([
    'name' => 'your_unique_name_of_file', // required
    'directory' => 'subfolder', // not required
]) ?>
```
Where:
* `name` - unique name of file. Widget will add `_inc` suffix to your file, 
there way you will get the file like `your_unique_name_of_file_inc.php`.
* `directory` - subfolder name that will be creates in the main directory defined in the config.

----

After you load all pages with included areas and files will be created (or you will create files manually),
you can open url \included-areas\ and manage records.

Enjoy.
