<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\web\Controller;
use Yii;

use meican\topology\services\DiscoveryService;
use meican\nsi\NSIParser;

/**
 * Classe que implementa o módulo Servidor do protocolo NSI Document Distribution Service (DDS), 
 * também conhecido como Discovery Service.
 * 
 * Recebe mensagens de provedores NSI para notificar de alterações da topologia. Ele chama o Synchronizer
 * para atualizar a topologia conhecida se assim estiver configurado.
 *
 * Esta classe NÃO deve extender o RbacControler, pois ela recebe respostas de provedores.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryController extends Controller {

    public $enableCsrfValidation = false;
    
    public function actionNotification() {
        Yii::trace("recebeu");
        $sync = $this->getSynchronizer(Yii::$app->request->getRawBody());
        if ($sync) {
            Yii::$app->response->setStatusCode(202);
            $sync->execute();
        }

        return "";
    }

    private function getSynchronizer($notificationXml) {
        $parser = new NSIParser; 
        $parser->loadXml($notificationXml);
        if($parser->isTD()) {
            $parser->parseNotifications();
            Yii::trace($parser->getData());
            foreach ($parser->getData()['nots'] as $subId => $notsData) {
                $sync = TopologySynchronizer::find()
                    ->where(['provider_nsa' => $notsData['providerId']])
                    ->andWhere(['subscription_id'=> $subId])->one();
                if ($sync) {
                    Yii::trace("achou sync ativo, sincronizando...");
                    $parser->parseTopology();
                    $sync->parser = $parser;
                    return $sync;
                }
                break; //VERIFICAR CASO EM QUE DUAS NOTIFICATIONS SAO RECEBIDAS NUMA MESMA MSG
            }
        }

        return null;
    }
}
