<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets\leaflet;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Map extends AssetBundle {
    
    public $sourcePath = '@npm/leaflet/dist';
    
    public $css = [
        'leaflet.css',
    ];
    
    public $js = [
        'leaflet.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>