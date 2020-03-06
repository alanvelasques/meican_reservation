<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Reservation;
use meican\circuits\models\ConnectionPath;
use meican\aaa\models\User;

class AuthorizationDetailed extends Model {
	
	public $reservation_id;
	public $reservation_name;
	public $source_domain;
	public $destination_domain;
	public $requester;
	public $bandwidth;
	public $port_in;
	public $port_out;
	
	public function rules()
	{
		return [
				[['reservation_id, reservation_name, source_domain, destination_domain, requester, bandwidth, port_in, port_out'], 'string'],
		];
	}
	
	public function attributeLabels()
	{
		return [
				'reservation_name' => Yii::t('circuits', 'Reservation name'),
				'source_domain' => Yii::t('circuits', 'Source Domain'),
				'destination_domain' => Yii::t('circuits', 'Destination Domain'),
				'requester' => Yii::t('circuits', 'Requester'),
				'bandwidth' => Yii::t('circuits', 'Bandwidth'),
				'port_in' => Yii::t('circuits', 'Port In'),
				'port_out' => Yii::t('circuits', 'Port Out'),
		];
	}
	
	public function __construct($reservation, $connection_id, $domain){
		$this->reservation_id = $reservation->id;
		
		$this->reservation_name = $reservation->name;
		
		$path = ConnectionPath::findOne(['conn_id' => $connection_id, 'path_order' => 0]);
		if($path) $this->source_domain = $path->domain;
		else $this->source_domain = Yii::t('circuits', 'deleted');
		
		$path = ConnectionPath::find()->where(['conn_id' => $connection_id])->orderBy("path_order DESC")->one();
		if($path) $this->destination_domain = $path->domain;
		else $this->destination_domain = Yii::t('circuits', 'deleted');
		
		$user = User::findOne(['id' => $reservation->request_user_id]);
		if($user) $this->requester = $user->name;
		
		$this->bandwidth = $reservation->bandwidth." Mbps";

		$paths_domain = ConnectionPath::find()->where(['conn_id' => $connection_id, 'domain' => $domain])->orderBy("path_order ASC")->all();
		
		$this->port_in = $paths_domain[0]->port_urn;
		$this->port_out = $paths_domain[count($paths_domain)-1]->port_urn;
	}
	
}