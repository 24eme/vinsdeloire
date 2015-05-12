<?php if($validation->hasErreurs()): ?>
<div id="points_bloquants">
    <p>Points bloquants :</p>
    <?php include_partial('document_validation/validationType', array('points' => $validation->getPoints('erreur'), 'css_class' => 'error')) ?>
</div>
<?php endif; ?>

<?php if($validation->hasVigilances()): ?>
<div id="points_vigilance">
    <p>Points de vigilance :</p>
     <?php include_partial('document_validation/validationType', array('points' => $validation->getPoints('vigilance'), 'css_class' => 'warning')) ?>
</div>
<?php endif; ?>
