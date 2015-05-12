<?php if ($form instanceof sfForm && ($form->hasErrors() || $form->hasGlobalErrors())): ?>
<ul class="error_list">
    <?php foreach($form->getGlobalErrors() as $item): ?>
        <li><?php echo $item->getMessage(); ?></li>
    <?php endforeach; ?>
    <?php include_partial('drm/errorMessagesFromFormFieldSchema', array('form_field_schema' => $form->getFormFieldSchema())) ?>
</ul>
<?php endif; ?>