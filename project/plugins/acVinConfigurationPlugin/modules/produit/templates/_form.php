<form id="form_ajout" action="<?php echo url_for('produit_modification', array('noeud' => $form->getObject()->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>" method="post">
	<?php echo $form->renderGlobalErrors() ?>
	<?php echo $form->renderHiddenFields() ?>
    <div class="ligne_form">
        <?php if($form['libelle']->hasError()) {?><span class="error"><?php echo $form['libelle']->renderError() ?></span><?php } ?>
        <?php echo $form['libelle']->renderLabel() ?>
        <?php echo $form['libelle']->render() ?>
    </div>
    <div class="ligne_form">
        <?php if($form['format_libelle']->hasError()) {?><span class="error"><?php echo $form['format_libelle']->renderError() ?></span><?php } ?>
        <?php echo $form['format_libelle']->renderLabel() ?>
        <?php echo $form['format_libelle']->render() ?>
    </div>
    <div class="ligne_form">
        <label>Clé :</label>
        <span><?php echo $form->getObject()->getKey(); ?></span><br />
        <span style="color: #999999">Cette clé est utilisée pour construire l'arbre, elle est constituante du hash produit</span>
    </div>
    <div class="ligne_form">
        <?php if($form['code']->hasError()){ ?><span class="error"><?php echo $form['code']->renderError() ?></span><?php } ?>
        <?php echo $form['code']->renderLabel() ?>
        <?php echo $form['code']->render() ?>
        <span style="color: #999999"><?php echo $form['code']->renderHelp() ?></span>
    </div>
    <?php if ($form->getObject()->exist('densite')): ?>
        <?php if($form['densite']->hasError()){ ?><span class="error"><?php echo $form['densite']->renderError() ?></span><?php } ?>
        <?php echo $form['densite']->renderLabel() ?>
        <?php echo $form['densite']->render() ?>
        <span style="color: #999999"><?php echo $form['densite']->renderHelp() ?></span>
    <?php endif; ?>
    <?php if ($form->getObject()->hasCodes()): ?>
        <div class="ligne_form">
            <?php if($form['code_produit']->hasError()) {?><span class="error"><?php echo $form['code_produit']->renderError() ?></span><?php } ?>
            <?php echo $form['code_produit']->renderLabel() ?>
            <?php echo $form['code_produit']->render() ?>
        </div>
        <div class="ligne_form">
            <?php if($form['code_douane']->hasError()){ ?><span class="error"><?php echo $form['code_douane']->renderError() ?></span><?php } ?>
            <?php echo $form['code_douane']->renderLabel() ?>
            <?php echo $form['code_douane']->render() ?>
        </div>
        <div class="ligne_form">
            <?php if($form['code_comptable']->hasError()){ ?><span class="error"><?php echo $form['code_comptable']->renderError() ?></span><?php } ?>
            <?php echo $form['code_comptable']->renderLabel() ?>
            <?php echo $form['code_comptable']->render() ?>
        </div>
    <?php endif; ?>
	<?php if ($form->getObject()->hasDepartements()): ?>
        <h2>Départements</h2>
        <div class="subForm contenu_onglet" id="formsDepartement">
        <p>Liste des départements :</p><br />
		<?php foreach ($form['secteurs'] as $subform): ?>
		  <?php include_partial('produit/subformDepartement', array('form' => $subform))?><br />
		<?php endforeach; ?>
            <a href="javascript:void(0)" class="btn_majeur btn_orange">Ajouter une ligne</a>
		</div>
		<input class="counteur" type="hidden" name="nb_departement" value="<?php echo count($form['secteurs']) ?>" />
	<?php endif; ?>
    <?php if ($form->getObject()->hasDroit(ConfigurationDroits::DROIT_DOUANE)): ?>
        <h2>Droits circulation</h2>
		<div id="formsDouane">
		<?php foreach ($form['droit_douane'] as $subform): ?>
		  <?php include_partial('produit/subformDroits', array('form' => $subform))?>
		<?php endforeach; ?>
            <a href="javascript:void(0)" class="btn_majeur btn_orange">Ajouter une ligne</a></strong>
		</div>
		<input class="counteur" type="hidden" name="nb_douane" value="<?php echo count($form['droit_douane']) ?>" />
	<?php endif; ?>
    <?php if ($form->getObject()->hasDroit(ConfigurationDroits::DROIT_CVO)): ?>
        <h2>Cotisations interprofessionnelles&nbsp;&nbsp;</h2>
        <div id="formsCvo">
		<?php foreach ($form['droit_cvo'] as $subform): ?>
		  <?php include_partial('produit/subformDroits', array('form' => $subform))?>
		<?php endforeach; ?>
	    </div>
        <input class="counteur" type="hidden" name="nb_cvo" value="<?php echo count($form['droit_cvo']) ?>" />
    <?php endif; ?>
	<?php if ($form->getObject()->hasLabels()): ?>
        <h2>Labels&nbsp;&nbsp;</h2>
		<div id="formsLabel">
    		<?php foreach ($form['labels'] as $subform): ?>
    		  <?php include_partial('produit/subformLabel', array('form' => $subform))?>
    		<?php endforeach; ?>
            <a href="javascript:void(0)" class="btn_majeur btn_orange"></a>
		</div>
		<input class="counteur" type="hidden" name="nb_label" value="<?php echo count($form['labels']) ?>" />
	<?php endif; ?>
	<?php if ($form->getObject()->hasDetails()): ?>
		<h2>Activation des lignes</h2>
		<div id="formsDetails">
			<?php foreach ($form['detail'] as $detail): ?>
			<?php foreach ($detail as $type): ?>
			<div class="ligne_form">
				<?php if($type['readable']->hasError()){ ?><span class="error"><?php echo $type['readable']->renderError() ?></span><?php } ?>				<?php echo $type['readable']->renderLabel() ?>
				<?php echo $type['readable']->render() ?>
				<?php echo $type['writable']->render() ?>
			</div>
                        <br />
			<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="form_ligne_btn" style="margin-top:20px;">
		<a name="annuler" class="btn_majeur btn_annuler" href="<?php echo url_for('produits') ?>">Annuler</a>
		<button style="float: right;" name="valider" class="btn_majeur btn_valider" type="submit">Valider</button>
	</div>
</form>
<?php 
	include_partial('templateformsDepartement');
	include_partial('templateformsDouane');
	include_partial('templateformsCvo');
	include_partial('templateformsLabel');
?>