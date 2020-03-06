<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\grid;

use yii\grid\GridView;

/**
 * GridView customized for MEICAN.
 *
 * Features:
 * - standard layout
 * - responsive template
 *
 * @author Mauricio Quatrin Guerreiro
 */
class Grid extends GridView {

    public $layout = "{items}{summary}{pager}";
    public $tableOptions = ['class' => 'table table-striped'];

    public function init() {
        parent::init();
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItems()
    {
        return '<div class="table-responsive">'.parent::renderItems().'</div>';
    }
}

?>