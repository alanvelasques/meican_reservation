<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\assets\group;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class CreateEdit extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/group/public';

    public $js = [
        'groupCreateEdit.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
