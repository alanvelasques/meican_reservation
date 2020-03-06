<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

use meican\base\widgets\Analytics;

\meican\base\assets\Theme::register($this);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= Html::encode(Yii::$app->name); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?= Html::csrfMetaTags() ?>
  <link rel="shortcut icon" href="<?= Url::base(); ?>/images/favicon.ico" type="image/x-icon" />
  <script type="text/javascript">
    var disabledNotify = true;
    window.baseUrl = "<?= Url::base(); ?>";
    var language = "<?= Yii::$app->language; ?>";
  </script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <?php $this->head() ?>
</head>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<?= $content; ?>
<!-- /.login-box -->

<?php $this->endBody() ?>
<?= Analytics::build(); ?>
</body>
<?php $this->endPage() ?>
</html>
