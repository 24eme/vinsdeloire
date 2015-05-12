<?php foreach($form_field_schema as $key => $item): ?>
    <?php if ($item instanceof sfFormFieldSchema): ?>
        <?php include_partial('drm/errorMessagesFromFormFieldSchema', array('form_field_schema' => $item)) ?>
    <?php elseif($item instanceof sfFormField && $item->hasError()): ?>
<li><label for="<?php echo $item->renderId() ?>"><strong><?php echo ($item->getWidget()->getLabel()) ? $item->getWidget()->getLabel() : ucfirst($key) ?></strong> : <?php echo $item->getError()->getMessage() ?></label></li>
    <?php endif; ?>
<?php endforeach; ?>

