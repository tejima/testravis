<form action="<?php echo url_for('@member_invite'); ?>" method="post">
<div class="row">
  <div class="gadget_header span12"> <?php echo __('Invite a friend to %1%', array('%1%' => $op_config['sns_name'])); ?> </div>
</div>
<?php $errors = array(); ?>
<?php if ($form->hasGlobalErrors()): ?>
<?php $errors[] = $form->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($errors): ?>
<div class="row">
<div class="alert-message block-message error">
<a class="close" href="#">x</a>
<?php foreach ($errors as $error): ?>
<p><?php echo __($error) ?></p>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<div class="row">

<?php foreach ($form as $f): ?>
<?php if (!$f->isHidden()): ?>
<div class="span12" style="text-align: left;">
<?php echo $f->renderLabel(); ?>
</div>
<?php if ($f->hasError()): ?>
<div class="span12"><div class="clearfix error"><span class="label important"><?php echo __($f->getError()); ?></span><br /><?php echo $f->render(array('class' => 'span12 error')) ?><br /><span class="help-block"><?php echo $f->renderHelp(); ?></span></div></div>
<?php else: ?>
<div class="span12"><?php echo $f->render(array('class' => 'span12')) ?><span class="help-block"><?php echo $f->renderHelp(); ?></span></div>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php echo $form->renderHiddenFields(); ?>
<input type="submit" name="submit" value="<?php echo __('Send'); ?>" class="btn primary" />
</form>
</div>
