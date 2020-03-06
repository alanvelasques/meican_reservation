<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;

\meican\bpm\assets\Update::register($this);

$this->params['header'] = [Yii::t("bpm", 'Edit workflow'), ['Home', 'Workflows', '#'.$id]];

?>

<script>
	var id = <?php echo json_encode($id); ?>;
</script>

<div class="box box-default">
    <h5 style="margin-bottom: 0px; margin-left: 10px;"><?= Yii::t("bpm", 'Owner Domain:')." ".$domainName ?></h5>

    <div  id="frame" class="box-body">  
		<iframe class="embed-responsive-item" style="width: 100%; height: 670px; border: none;" name="workflow_editor" id="workflow_editor" src="<?php echo Yii::$app->urlManager->createUrl(['bpm/workflow/editor-update', 'id' => $_GET['id'], 'lang' => Yii::$app->language]);?>"></iframe>
	</div>
	
	<div class="box-footer with-border">
        <input type="button" id="button_save" class="btn btn-primary" value=<?= Yii::t("bpm", 'Save'); ?>>
	    <input type="button" id="button_cancel" class="btn btn-default" value=<?= Yii::t("bpm", 'Cancel'); ?>>
    </div>
</div>

<?php

Modal::begin([
    'id' => 'dialog',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default" data-dismiss="modal">Ok</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';

Modal::end(); 

?>