<?php if ($navs): ?>
<?php foreach ($navs as $nav): ?>
<?php if (op_is_accessible_url($nav->uri)): ?>
<?php echo link_to($nav->caption, $nav->uri, array('class' => 'btn', 'id' => sprintf('smtGlobalNav_%1', op_url_to_id($nav->uri, true)), )) ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
