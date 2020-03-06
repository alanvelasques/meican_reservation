<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;
use yii\data\ActiveDataProvider;

use meican\base\utils\DateUtils;


/**
 * A Reservation element is created when a user
 * request a scheduled circuit.
 *
 * @property integer $id
 * 
 * Indica o tipo da reserva, TEST ou NORMAL.
 * Se TEST, será gerida pelo Meican, sendo recriada
 * com a frequencia indicada pelo Cron associado.
 * Reservas desse tipo não são efetivamente provisionadas.
 * Dessa forma não há alocamento de recursos.
 * Se NORMAL, a reserva será efetiva, dessa forma serão
 * executados os Workflows de Autorização de todos os 
 * Domains que o circuito passar.
 *  
 * @property string $type
 * @property string $name
 * @property integer $bandwidth
 * @property string $start
 * @property string $finish
 * 
 * Requester NSA ID que enviou a solicitação
 * 
 * @property string $requester_nsa
 *
 * Provider NSA ID que recebeu a solicitação
 * 
 * @property string $provider_nsa
 *
 * @property integer $request_user_id
 * 
 * @property Connection[] $connections
 * @property Provider $provider
 * @property User $requesterUser
 * @property ReservationRecurrence $reservationRecurrence
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Reservation extends \yii\db\ActiveRecord
{
	const TYPE_NORMAL 	= "NORMAL";
	const TYPE_TEST 	= "TEST";
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reservation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'date', 'bandwidth', 'provider_nsa','requester_nsa'], 'required'],
            [['type'], 'string'],
            [['bandwidth', 'request_user_id'], 'integer'],
            [['start', 'finish', 'date'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['provider_nsa', 'requester_nsa'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Reservation ID',
            'type' => Yii::t('circuits', 'Type'),
            'name' => Yii::t('circuits', 'Name'),
            'date' => Yii::t('circuits', 'Requested at'),
            'bandwidth' => Yii::t('circuits', 'Bandwidth (Mbps)'),
            'start' => Yii::t('circuits', 'Start'),
            'finish' => Yii::t('circuits', 'Finish'),
            'request_user_id' => Yii::t('circuits', 'Requested by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBpmFlowControls()
    {
        return $this->hasMany(BpmFlowControl::className(), ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnections()
    {
        return $this->hasMany(Connection::className(), ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['nsa' => 'provider_nsa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequesterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'request_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecurrence()
    {
        return $this->hasOne(ReservationRecurrence::className(), ['id' => 'id']);
    }
    
    public function getPath($order) {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id, 'path_order'=>$order]);
    }
    
    public function getPaths() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id])->orderBy(['path_order'=> "SORT ASC"]);
    }
    
    public function getFirstPath() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id,
    			'path_order'=> 0]);
    }
    
    public function getLastPath() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id,
    			'path_order'=> ReservationPath::find()->where(['reservation_id'=>$this->id])->max('path_order')]);
    }
    
    public function getDestinationUrn() {
    	$path = $this->getLastPath()->one();
    	return $path ? $path->port_urn : null;
    }
    
    public function getSourceUrn() {
    	$path = $this->getFirstPath()->one();
    	return $path ? $path->port_urn : null;
    }
    
    public function getSourceDomain() {
		$connection = Connection::find()->where(['reservation_id' => '1'])->one();
		if(!$connection) return null;
		$path = $connection->getFirstPath()->one();
    	if(!$path) return null;
		return $path->domain;
    }
    
    public function getDestinationDomain() {
    	$connection = Connection::find()->where(['reservation_id' => $this->id])->one();
		if(!$connection) return null;
		$path = $connection->getLastPath()->one();
    	if(!$path) return null;
		return $path->domain;
    }
    
    public function createConnections($events) {
    	$paths = $this->getPaths()->all();
    	Yii::trace($events);
    	for ($i=0; $i < count($events['start']) ; $i++) { 
    		$conn = new Connection;

    		$date = new \DateTime($events['start'][$i]);    
    		$conn->start = $date->format('Y-m-d H:i');
    
            $date = new \DateTime($events['finish'][$i]);    
    		$conn->finish = $date->format('Y-m-d H:i');
    
    		$conn->reservation_id = $this->id;
    		$conn->status = Connection::STATUS_PENDING;
    		$conn->dataplane_status = Connection::DATA_STATUS_INACTIVE;
    		$conn->auth_status = Connection::AUTH_STATUS_UNEXECUTED;
            $conn->resources_status = Connection::RES_STATUS_RELEASED;
            $conn->version = -1;
            $conn->bandwidth = $this->bandwidth;
            $conn->type = Connection::TYPE_NSI;

            if($conn->save()) {
                $k = 0;
                foreach ($paths as $resPath) {
                    $connPath = new ConnectionPath;
                    $connPath->path_order = $k;
                    $connPath->conn_id = $conn->id;
                    $connPath->domain = explode(":",$resPath->port_urn)[0];
                    $k++;
                    $connPath->port_urn = $resPath->port_urn;
                    $connPath->vlan = $resPath->vlan;
                    
                    $connPath->save();
                }
            } else {
                Yii::trace($conn->getErrors());
            }
    	}
    }
    
    public function confirm() {
    	foreach ($this->getConnections()->all() as $conn) {
    		if ($conn->external_id == null) {
    			$conn->requestCreate();
    		}
    	}
    }
}
