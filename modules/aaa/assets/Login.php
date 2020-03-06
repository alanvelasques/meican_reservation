<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Login extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/login';
    
    public $js = [
        'login.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
