<?php

use conquer\codemirror\CodemirrorWidget;
use sadovojav\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\ContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\mix8872\includes\assets\IncludesAsset::register($this);

$this->title = Yii::t('includes', 'Включаемые области');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-12">
        <div class="panel-heading">
            <h2 class="pull-left"><?= Html::encode($this->title) ?></h2>
        </div>
    </div>
</div>
<?php $form = ActiveForm::begin() ?>
<?php foreach ($areas as $group => $items) : ?>
    <?php if ($items): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <?php if ($group): ?>
                            <h5 class="float-left text-white"><?= $group ?></h5>
                        <?php endif; ?>
                        <div class="card-widgets text-white float-right">
                            <a data-toggle="collapse" href="#group-<?= $group ?>"
                               class="btn btn-outline-light btn-sm ml-2" role="button" aria-expanded="true"
                               aria-controls="group-<?= $group ?>"><i class="mdi mdi-minus"></i></a>
                        </div>
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-sm width-sm float-right', 'title' => 'Сохранятся изменения во всех блоках']) ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="card-body collapse show" id="group-<?= $group ?>">
                        <div class="row">
                            <?php foreach ($items as $path => $item): ?>
                                <div class="col-lg-6 col-12 border-left">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5><?= preg_replace('/(^includes-)|(_inc$)/ui', '', $item->name) ?></h5>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <?= $form->field($item, "[{$item->name}]title") ?>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <?= $form->field($item, "[{$item->name}]group") ?>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <?= $form->field($item, "[{$item->name}]type")->dropDownList($item::$types) ?>
                                        </div>
                                        <div class="col-12">
                                            <?php if ($item->type === $item::TYPE_HTML): ?>
                                                <?= $form->field($item, "[{$item->name}]content")->widget(CKEditor::class, [
                                                    'name' => $item->name,
                                                    'value' => $item->content,
                                                    'editorOptions' => [
                                                        'toolbar' => [
                                                            ['Source'],
                                                            ['PasteText', '-', 'Undo', 'Redo'],
                                                            ['Replace', 'SelectAll'],
                                                            ['Format', 'FontSize'],
                                                            ['Bold', 'Italic', 'Underline', 'TextColor', 'StrikeThrough', '-', 'Outdent', 'Indent', 'RemoveFormat',
                                                                'Blockquote', 'HorizontalRule'],
                                                            ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                                                            ['Link', 'Unlink'],
                                                            ['Maximize', 'ShowBlocks']
                                                        ],
                                                        'allowedContent' => true,
                                                        'forcePasteAsPlainText' => true,
                                                        'height' => 100
                                                    ],
                                                ])->label($item->title ?: $item->name) ?>
                                            <?php elseif ($item->type === $item::TYPE_PLAIN): ?>
                                                <?= $form->field($item, "[{$item->name}]content")->textarea(['rows' => 8])->label($item->title ?: $item->name) ?>
                                            <?php else: ?>
                                                <?= $form->field($item, "[{$item->name}]content")->widget(CodemirrorWidget::class, [
                                                    'preset' => $item->type === $item::TYPE_PHP ? 'php' : ($item->type === $item::TYPE_JS ? 'javascript' : 'text'),
//                                                'options' => ['height' => '100px'],
                                                    'settings' => ['height' => 100]
                                                ])->label($item->title ?: $item->name) ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($item->meta): ?>
                                            <?php foreach ($item->meta as $metaKey => $metaValue): ?>
                                                <div class="col-md-12">
                                                    <?= $form->field($item, "[{$item->name}]meta[{$metaKey}]")->label($metaKey) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<?php ActiveForm::end(); ?>
