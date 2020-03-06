<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;

use meican\topology\models\Port;

/**
 * Classe que representa o Path requisitado em uma
 * solicitação de reserva. Uma reserva apenas terá uma
 * ReservationPath se for criada a partir do MEICAN.
 *
 * Reservas de outros requesters consultadas a partir
 * de provedores serão associadas apenas às suas respectivas
 * Connections e ConnectionsPaths.
 *
 * @property integer $reservation_id
 * @property integer $path_order
 * 
 * @property string $port_urn
 *  
 * @property string $vlan
 *
 * @property Reservation $reservation
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationPath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reservation_path}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reservation_id', 'path_order', 'port_urn', 'vlan'], 'required'],
            [['reservation_id', 'path_order'], 'integer'],
            [['port_urn'], 'string', 'max' => 250],
            [['vlan'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reservation_id' => Yii::t('circuits', 'Reservation ID'),
            'path_order' => Yii::t('circuits', 'Path Order'),
            'port_urn' => Yii::t('circuits', 'Port'),
            'vlan' => Yii::t('circuits', 'Vlan'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation() {
        return $this->hasOne(Reservation::className(), ['id' => 'reservation_id']);
    }
    
    public function getPort() {
    	return Port::findByUrn($this->port_urn);
    }

    public function getFullPortUrn() {
        return "urn:ogf:network:".$this->port_urn;
    }
}
