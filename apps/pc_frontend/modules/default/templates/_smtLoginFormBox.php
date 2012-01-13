<?php foreach ($forms as $form) : ?>

<?php echo form_tag(url_for(sprintf('@login'.'?%s=%s', opAuthForm::AUTH_MODE_FIELD_NAME, $form->getAuthMode()))) ?>

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
<?php foreach ($form as $field): ?>
<?php if (!$field->isHidden()): ?>
  <div class="span12">
  <?php echo $field->renderLabel(); ?>
  </div>
  <?php if ($field->hasError()): ?>
  <div class="span12">
    <div class="clearfix error"><span class="label important"><?php echo __($field->getError()); ?></span>
    <?php if ($field->getWidget() instanceof sfWidgetFormInput && ("text" === $field->getWidget()->getOption('type') || "password" === $field->getWidget()->getOption('type'))): ?>
    <?php echo $field->render(array('class' => 'span12')); ?>
    <?php else: ?>
    <?php echo $field->render(); ?>
    <?php endif; ?>
    <span class="help-block"><?php echo $field->renderHelp(); ?></span>
    </div>
  </div>
  <?php else: ?>
  <div class="span12">
    <?php if ($field->getWidget() instanceof sfWidgetFormInput && ("text" === $field->getWidget()->getOption('type') || "password" === $field->getWidget()->getOption('type'))): ?>
    <?php echo $field->render(array('class' => 'span12')); ?>
    <?php else: ?>
    <?php echo $field->render(); ?>
    <?php endif; ?>
    <span class="help-block"><?php echo $field->renderHelp(); ?></span>
  </div>
  <?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<div class="span12">
<input type="submit" name="submit" value="<?php echo __('Login'); ?>" class="btn primary" />
<?php echo $form->renderHiddenFields(); ?>
</form>
</div>

<?php endforeach; ?>
