<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class PointForm extends Model {
    
    public $domain;
    public $network;
    public $port;
    public $vlan;
    public $vlan_text;
    public $urn;

    public function rules() {
        return [
            [['domain','network','port','vlan','urn','vlan_text'],'required'],
            [['urn'], 'match', 'pattern' => '/^urn:ogf:network:/'],
            [['urn'], 'match', 'not'=>true ,'pattern' => '/\?/'],
            [['vlan_text'],'match','pattern'=> '/^[0-9]+[-,][0-9]+|^[0-9]+$/'],
        ];
    }

    public function attributeLabels() {
        return [
            'domain' => Yii::t("circuits", 'Domain'),
            'network' =>  Yii::t("circuits", 'Network'),
            'port' =>  Yii::t("circuits", 'Port'),
            'vlan' =>  Yii::t("circuits", 'VLAN'),
            'vlan_text' =>  Yii::t("circuits", 'VLAN'),
            'urn' =>  Yii::t("circuits", 'URN'),
        ];
    }
}