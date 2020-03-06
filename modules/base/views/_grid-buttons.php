<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Modal;

use meican\base\assets\grid\ButtonsAsset;

ButtonsAsset::register($this);

?>

<div class="grid-buttons">

    <div>
        <a class="btn <?= $size == 'small' ? 'btn-sm' : ''; ?> btn-primary add-grid-btn" href="<?= $addUrl; ?>">Add</a>
        <a id="delete-grid-btn" class="btn <?= $size == 'small' ? 'btn-sm' : ''; ?> btn-default">Delete</a>
    </div>

    <?php 

    Modal::begin([
        'id' => 'delete-grid-modal',
        'headerOptions' => ['hidden'=>'hidden'],
        'footer' => '<button id="confirm-grid-btn" class="btn btn-danger">Delete</button><button id="cancel-grid-btn" class="btn btn-default">Cancel</button>',
    ]);

    echo 'Do you want delete the selected items?';

    Modal::end(); 

    Modal::begin([
        'id' => 'error-grid-modal',
        'headerOptions' => ['hidden'=>'hidden'],
        'footer' => '<button id="close-grid-btn" class="btn btn-default">Close</button>',
    ]);

    echo 'Please, select a item.';

    Modal::end(); 

    ?>

</div>
