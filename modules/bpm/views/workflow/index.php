<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\grid\CheckboxColumn;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use meican\base\components\LinkColumn;
use meican\bpm\models\BpmWorkflow;
use meican\base\grid\Grid;

\meican\bpm\assets\Index::register($this);

$this->params['header'] = ["Workflows", ['Home', 'Workflows']];

?>

<div class="box box-default">
    <div class="box-body">               
	<?=
		Grid::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
			'filterModel' => $searchModel,
			'id' => 'gridDevices',
			'layout' => "{items}{summary}{pager}",
			'columns' => array(
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{delete}',
						'contentOptions' => function($model){
							return ['class'=>'btn-delete', 'id' => $model->id];
						},
						'buttons' => [
								'delete' => function ($url, $model) {
									return Html::a('<span class="fa fa-trash"></span>', '#');
								}
						],
                        'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{update}',
						'buttons' => [
								'update' => function ($url, $model) {
									return Html::a('<span class="fa fa-pencil"></span>', $url);
								}
						],
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{copy}',
						'buttons' => [
								'copy' => function ($url, $model) {
									return Html::a('<span class="fa fa-copy"></span>', $url);
								}
						],
						'headerOptions'=>['style'=>'width: 2%;'],
					],
			        [
						'label' => Yii::t("bpm", 'Name'),
						'value' => 'name',
			        	'headerOptions'=>['style'=>'width: 35%;'],
			        ],
					[
						'label' => Yii::t("bpm", 'Domain'),
						'value' => 'domain',
						'filter' => Html::activeDropDownList($searchModel, 'domain',
								ArrayHelper::map(
									BpmWorkflow::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
								['class'=>'form-control','prompt' => Yii::t("bpm", 'any')]
						),
						'headerOptions'=>['style'=>'width: 35%;'],
					],
					[
						'format' => 'raw',
						'label' => 'Status',
						'value' => function ($model){
							return Html::input('checkbox', '', $model->id, ['id'=>'toggle-'.$model->id, 'class'=>'toggle-event-class', 'checked'=>!$model->isDisabled(), 'data-toggle'=>"toggle", 'data-size'=> "mini", "data-on" => Yii::t("bpm", "Enabled"), "data-off"=> Yii::t("bpm", "Disabled"), "data-width"=>"100", "data-onstyle"=>"success", "data-offstyle"=>"warning"]);
						},
						'filter' => Html::activeDropDownList($searchModel, 'status',
								["enabled" => Yii::t("bpm", "Enabled"), "disabled"=> Yii::t("bpm", "Disabled")],
								['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
						),
						'headerOptions'=>['style'=>'width: 18%;'],
					],
					
				),
		]);
	?>
	</div>
</div>

<?php 

Modal::begin([
    'id' => 'delete-workflow-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="delete-btn" class="btn btn-danger">'.Yii::t("bpm", "Delete").'</button><button id="cancel-btn" class="btn btn-default">'.Yii::t("bpm", "Cancel").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;">'.Yii::t("bpm", "Delete this workflows?").'</p>';

Modal::end();

Modal::begin([
		'id' => 'disable-workflow-modal',
		'headerOptions' => ['hidden'=>'hidden'],
		'footer' => '<button id="confirm-btn" class="btn btn-danger">'.Yii::t("bpm", "Yes").'</button><button id="cancel-btn" class="btn btn-default">'.Yii::t("bpm", "Cancel").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;" id="disable-message"></p>';

Modal::end();

?>