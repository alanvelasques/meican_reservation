<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\base\utils\DateUtils;
use meican\circuits\models\CircuitsPreference;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ConfigurationForm extends Model {
    
    public $meicanNsa;
    public $uniportsEnabled;
    public $defaultProviderNsa;
    public $defaultCSUrl;
    public $protocol;
    
    public function rules() {
        return [
            [['meicanNsa','defaultProviderNsa'], 'validateUrn'],
            [['meicanNsa','protocol','uniportsEnabled', 'defaultProviderNsa', 'defaultCSUrl'], 'required'],
            [['meicanNsa','protocol','uniportsEnabled','defaultProviderNsa','defaultCSUrl'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'meicanNsa' => Yii::t('circuits', 'MEICAN NSA ID'),
            'uniportsEnabled' => Yii::t('circuits', 'Unidirectional Ports'),
            'defaultProviderNsa' => Yii::t('circuits', 'Provider NSA ID'),
            'defaultCSUrl' => Yii::t('circuits', 'Connection Service URL'),
            'protocol' => Yii::t('circuits', 'Protocol'),
        ];
    }

    public function validateUrn($attr, $param) {
        $this->$attr = trim(str_replace("urn:ogf:network:","",$this->$attr));
    } 

    public function setPreferences($prefs) {
        foreach ($prefs as $pref) {
            switch ($pref->name) {
                case CircuitsPreference::MEICAN_NSA:
                    $this->meicanNsa = $pref->value;                    
                    break;
                case CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA:
                    $this->defaultProviderNsa = $pref->value;                    
                    break;
                case CircuitsPreference::CIRCUITS_DEFAULT_CS_URL:
                    $this->defaultCSUrl = $pref->value;                    
                    break;
                case CircuitsPreference::CIRCUITS_UNIPORT_ENABLED:
                    $this->uniportsEnabled = $pref->value;                    
                    break;
                case CircuitsPreference::CIRCUITS_PROTOCOL:
                    $this->protocol = $pref->value;                    
                    break;
                
                default:
                    break;
            }
        }
    }

    public function save() {
        $pref = CircuitsPreference::findOne(CircuitsPreference::MEICAN_NSA);
        $pref->value = $this->meicanNsa;
        if(!$pref->save()) return false;

        $pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA);
        $pref->value = $this->defaultProviderNsa;
        if(!$pref->save()) return false;

        $pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_DEFAULT_CS_URL);
        $pref->value = $this->defaultCSUrl;
        if(!$pref->save()) return false;

        $pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_UNIPORT_ENABLED);
        $pref->value = $this->uniportsEnabled;
        if(!$pref->save()) return false;

        $pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_PROTOCOL);
        $pref->value = $this->protocol;
        if(!$pref->save()) return false;

        return true;
    }
}

?>