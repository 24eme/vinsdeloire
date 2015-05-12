    <h2>Engagements <a href="" class="msg_aide" data-msg="help_popup_validation_engagement" title="Message aide"></a></h2>
<?php if($form->hasErrors()){ ?>
<div class="error_list">Veuillez cocher toutes les cases.</div>
<?php } ?>
<ol>
    <?php foreach ($drmValidation->getEngagements() as $engagement): ?>
        <li>
            <?php echo $form['engagement_' . $engagement->getCode()]->renderLabel() ?>
            <?php echo $form['engagement_' . $engagement->getCode()]->render() ?>
        </li>
    <?php endforeach; ?>
</ol>
<br />