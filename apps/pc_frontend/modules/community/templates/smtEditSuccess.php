<?php if ($communityForm->isNew()): ?>
<?php echo form_tag(url_for('@community_edit'), 'multipart=true') ?>
<?php else: ?>
<?php echo form_tag(url_for('@community_edit?id='.$community->getId()), 'multipart=true') ?>
<?php endif; ?>
<div class="row">
<?php if ($communityForm->isNew()): ?>
  <div class="gadget_header span12"> <?php echo __('Create a new %community%'); ?> </div>
<?php else: ?>
  <div class="gadget_header span12"> <?php echo __('Edit the %community%'); ?> </div>
<?php endif; ?>
</div>

<?php $errors = array(); ?>
<?php if ($communityForm->hasGlobalErrors()): ?>
<?php $errors[] = $communityForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($communityConfigForm->hasGlobalErrors()): ?>
<?php $errors[] = $communityConfigForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($communityFileForm->hasGlobalErrors()): ?>
<?php $errors[] = $communityFileForm->renderGlobalErrors(); ?>
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
<?php foreach ($communityForm as $cf): ?>
<?php if (!$cf->isHidden()): ?>
  <div class="span12">
  <?php echo $cf->renderLabel(); ?>
  </div>
  <?php if ($cf->hasError()): ?>
  <div class="span12"><div class="clearfix error"><span class="label important"><?php echo __($cf->getError()); ?></span><?php echo $cf->render(array('class' => 'span12 error')) ?><span class="help-block"><?php echo $cf->renderHelp(); ?></span></div></div>
  <?php else: ?>
  <div class="span12"><?php echo $cf->render(array('class' => 'span12')) ?><span class="help-block"><?php echo $cf->renderHelp(); ?></span></div>
  <?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<?php foreach ($communityConfigForm as $ccf): ?>
<?php if (!$ccf->isHidden()): ?>>
  <div class="span12"><?php echo $ccf->renderLabel(); ?></div>
  <?php if ($ccf->hasError()): ?>
  <div class="span12"><div class="clearfix error"><span class="label important"><?php echo __($ccf->getError()); ?></span><?php echo $ccf->render(array('class' => 'span12 error')) ?><span class="help-block"><?php echo $ccf->renderHelp(); ?></span></div></div>
  <?php else: ?>
  <div><?php echo $ccf->render(array('class' => 'span12')) ?><span class="help-block"><?php echo $ccf->renderHelp(); ?></span></div>
  <?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<?php foreach ($communityFileForm as $cff): ?>
<?php if (!$cff->isHidden()): ?>
  <div class="span12"><?php echo $cff->renderLabel(); ?></div>
  <?php if ($cff->hasError()): ?>
  <div class="span12"><div class="clearfix error"><span class="label important"><?php echo __($cff->getError()); ?></span><?php echo $cff->render(array('class' => 'span12 error')) ?><span class="help-block"><?php echo $cff->renderHelp(); ?></span></div></div>
  <?php else: ?>
  <div class="span12"><?php echo $cff->render(array('class' => 'span12')) ?><span class="help-block"><?php echo $cff->renderHelp(); ?></span></div>
  <?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<div class="span5">
<input type="submit" name="submit" value="編集する" class="btn primary" />
<?php echo $communityForm->renderHiddenFields(); ?>
<?php echo $communityConfigForm->renderHiddenFields(); ?>
<?php echo $communityFileForm->renderHiddenFields(); ?>
</form>
</div>

<div class="span5">
  <?php if (!$communityForm->isNew() && $isDeleteCommunity): ?>
  <?php echo form_tag(url_for('@community_delete?id='.$community->getId())) ?>
  <span class="label important">DANGER</span>: <input type="submit" name="submit" value="<?php echo __('Delete'); ?>" class="btn danger" />
  </form>
  <?php endif; ?>
</div>
</div>

</div>
