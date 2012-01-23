<form action="<?php echo url_for('@member_editProfile'); ?>" method="post">
<div class="row">
  <div class="gadget_header span12"> <?php echo __('Edit Profile'); ?> </div>
</div>
<?php $errors = array(); ?>
<?php if ($memberForm->hasGlobalErrors()): ?>
<?php $errors[] = $memberForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($profileForm->hasGlobalErrors()): ?>
<?php $errors[] = $profileForm->renderGlobalErrors(); ?>
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
<table class="zebra-striped">
<?php foreach ($memberForm as $mf): ?>
<?php if (!$mf->isHidden()): ?>
<tr>
  <th><?php echo $mf->renderLabel(); ?></th>
  <?php if ($mf->hasError()): ?>
  <td><div class="clearfix error"><span class="label important"><?php echo __($mf->getError()); ?></span><?php echo $mf->render(array('class' => 'span16 error')) ?><span class="help-block"><?php echo $mf->renderHelp(); ?></span></div></td>
  <?php else: ?>
  <td><?php echo $mf->render(array('class' => 'span16')) ?><span class="help-block"><?php echo $mf->renderHelp(); ?></span></td>
  <?php endif; ?>
</tr>
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($profileForm as $pf): ?>
<?php if (!$pf->isHidden()): ?>
<?php if ($pf->getName()!=='op_preset_birthday'): ?>
<tr>
  <th><?php echo $pf->renderLabel(); ?></th>
  <?php if ($mf->hasError()): ?>
  <td><div class="clearfix error"><span class="label important"><?php echo __($pf->getError()); ?></span><?php echo $pf->render(array('class' => 'span16 error')) ?><span class="help-block"><?php echo $pf->renderHelp(); ?></span></div></td>
  <?php else: ?>
  <td><?php echo $pf->render(array('class' => 'span16')); ?><span class="help-block"><?php echo $pf->renderHelp(); ?></span></td>
  <?php endif; ?>
</tr>
<?php else: ?>
<tr>
  <th><?php echo $pf->renderLabel(); ?></th>
  <?php if ($mf->hasError()): ?>
  <td><div class="clearfix error"><span class="label important"><?php echo __($pf->getError()); ?></span><?php echo $pf->render(array('class' => 'span8 error')) ?><span class="help-block"><?php echo $pf->renderHelp(); ?></span></div></td>
  <?php else: ?>
  <td><?php echo $pf->render(array('class' => 'span8')); ?><span class="help-block"><?php echo $pf->renderHelp(); ?></span></td>
  <?php endif; ?>
</tr>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php echo $memberForm->renderHiddenFields(); ?>
<?php echo $profileForm->renderHiddenFields(); ?>
</table>
<input type="submit" name="subtmi" value="編集する" class="btn primary" /> 
</form>
</div>
