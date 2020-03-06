<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\helpers\Json;
use Yii;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\Domain;
use meican\topology\models\Service;
use meican\topology\models\Provider;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ServiceController extends RbacController {
    
    public function actionCreate($id) {
        $prov = Provider::findOne($id);
        if ($prov) {
            $model = new Service; 
            $model->provider_id = $prov->id;
        } else return $this->redirect(array('index'));

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Service {type} added successfully", ['type'=>$model->getType()]));
                return $this->redirect(array('/topology/provider/view', 'id'=>$model->provider_id));
            } 
        }

        return $this->render('/provider/service/create', array(
            'provider' => $prov,    
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
        $model = Service::findOne($id);

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Service {type} updated successfully", ['type'=>$model->getType()]));
                return $this->redirect(array('/topology/provider/view', 'id'=>$model->provider_id));
            } 
        }

        return $this->render('/provider/service/update', array(
            'provider' => $model->getProvider()->one(), 
            'model' => $model,
        ));
    }

    public function actionDelete() {
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $id) {
                $service = Service::findOne($id);
                if ($service->delete()) {
                    Yii::$app->getSession()->addFlash('success', Yii::t("topology", "Service {type} deleted successfully", ['type'=>$service->getType()]));
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Error deleting service '.$service->getType());
                }
            }
        }
    
        return $this->redirect(array('/topology/provider/view', 'id'=>$service->provider_id));
    }

    /////////////////

    public function actionGetCsByProviderNsa($nsa, $cols=null) {
        $provider = Provider::findByNsa($nsa)->one();
        if (!$provider) {
            return [];
        }

        $query = $provider->getConnectionService()->asArray();

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
        
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}
