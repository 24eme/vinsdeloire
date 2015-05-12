<div id="popup_ajout_produit_<?php echo $certification_config->getKey() ?>" class="popup_contenu">
        <p><?php echo acCouchdbManager::getClient('Messages')->getMessage('msg_popup_ajout'); ?></p>
	<?php include_partial('form', array('form' => $form, 'certification_config' => $certification_config)) ?>
</div>