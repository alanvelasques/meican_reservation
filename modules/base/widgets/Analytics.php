<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\widgets;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Analytics {

    static function build() {
            if (Yii::$app->params['google.analytics.enabled']) {
                return "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){".
                  "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),".
                  "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)".
                  "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');".
                  "ga('create', '".Yii::$app->params['google.analytics.key']."', 'auto');".
                  "ga('send', 'pageview');".
                  "</script>";
            } else return "";
    }

}

?>