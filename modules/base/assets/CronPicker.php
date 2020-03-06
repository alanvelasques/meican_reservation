<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class CronPicker extends AssetBundle {
    
    public $sourcePath = '@meican/base/assets/cron';

    public $js = [
        'jquery-cron.js',
        'jquery-cron-i18n.js',
    ];
    
    public $css = [
        'cron.css'
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'meican\base\assets\I18N',
    ];
}
