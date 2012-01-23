<?php use_helper('opUtil', 'Javascript') ?>
<?php $sf_response->removeStylesheet('/opSkinBasicPlugin/css/main.css'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php use_stylesheet('bootstrap') ?>
<?php use_stylesheet('smt_main') ?>
<?php include_stylesheets() ?>
<?php if (Doctrine::getTable('SnsConfig')->get('customizing_css')): ?>
<link rel="stylesheet" type="text/css" href="<?php echo url_for('@customizing_css') ?>" />
<?php endif; ?>
<meta name="viewport" content="width=320px,user-scalable=no" />
<?php
echo javascript_tag('
var openpne = {
  apiKey: "'.$sf_user->getMemberApiKey().'"
};
');
?>
<?php include_javascripts() ?>
<?php use_javascript('smt_menu'); ?>
</head>
<body id="<?php printf('page_%s_%s', $this->getModuleName(), $this->getActionName()) ?>" class="<?php echo opToolkit::isSecurePage() ? 'secure_page' : 'insecure_page' ?>">
<?php include_partial('global/tosaka') ?>
<div id="face" class="row">
  <?php $community = $sf_response->getDisplayCommunity() ?>
  <?php if ($community): ?>
  <div class="span2">
    <?php echo op_image_tag_sf_image($community->getImageFileName(), array('size' => '48x48')) ?>
  </div>
  <div class="span8">
    <hr class="toumei">
    <div class="row"><span class="face-name"><?php echo $community->getName() ?></span></div>
    <hr class="toumei">
  </div>
  <div class="span2 center"><a href="#menu" id="menubutton"><?php echo op_image_tag('HomeIcon.png', array('height' => '48')) ?></a></div>
  <?php endif ?>
</div>

<hr class="toumei">
<?php include_partial('default/smtMenu') ?>

<?php if ($sf_user->hasFlash('error')): ?>
<div id="global-error" class="row">
  <div class="alert-message block-message error">
    <?php echo __($sf_user->getFlash('error')); ?>
  </div>
</div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('notice')): ?>
<div id="global-error" class="row">
  <div class="alert-message block-message info">
    <?php echo __($sf_user->getFlash('notice')); ?>
  </div>
</div>
<?php endif; ?>

<?php echo $sf_content ?>
</body>
</html>
