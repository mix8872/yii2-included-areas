<?php

namespace mix8872\includes\controllers;

use mix8872\includes\models\Includes;
use Yii;
use yii\base\Model;
use yii\web\Controller;

/**
 * DefaultController implements the CRUD actions for TextBlock model.
 */
class DefaultController extends Controller
{
    /**
     * Lists all TextBlock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $areas = Includes::find();

        if (($post = Yii::$app->request->post()) && Model::loadMultiple($areas, $post)) {
            $success = true;
            foreach ($areas as $area) {
                if (!$area->save()) {
                    $success = false;
                }
            }
            if ($success) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка сохранения');
            }
            return $this->redirect(['index']);
        }

        $result[0] = array();
        foreach ($areas as $key => $area) {
            if ($area->group) {
                $group = $area->group;
                $result[$group][$key] = $area;
            } else {
                $result[0][$key] = $area;
            }
        }
        return $this->render('index', [
            'areas' => $result
        ]);
    }
}
