<div style="display:none">
        <div id="signature_drm_popup_content" class="popup_contenu">
            <h2>Veuillez confirmer la validation de la DRM </h2>
            <br/>
            <p>
                Vous êtes sur le point de valider la partie économique de votre DRM.<br /><br />
            </p>
        <?php if($compte->hasDroit(Roles::TELEDECLARATION_DOUANE) && ! $drm->crds->exist('COLLECTIFACQUITTE')): ?>
    	    <p>Une fois validés, ces éléments peuvent être transmis automatiquement sur l'application CIEL des douanes.</p><br/>
          <p>N'oubliez pas alors de vous connecter au portail douane.gouv.fr pour finaliser votre DRM et sa partie fiscale.</p><br/>
        <?php endif; ?>
	    <p>Si vous décidez de transmettre le document par courrier postal ou par mail, décochez l'accord de transmission ci-dessous et n'oubliez pas que la DRM doit être signée manuellement pour être valable.</p>
        <?php if($compte->hasDroit("teledeclaration_douane")): ?>
          <p>
            <div class="ligne_form">
                <span>
                    <?php echo $validationForm['transmission_ciel']->renderLabel(null, array('id' => 'transmissionciellabel')); ?>
                    <?php echo $validationForm['transmission_ciel']->renderError(); ?>
                    <input id="drm_transmission_ciel_visible" type="checkbox"  value="1" checked="checked" />
                </span>
            </div>
          </p>
        <?php endif;?>
            <div class="ligne_btn">
                <a id="signature_drm_popup_close" class="btn_rouge btn_majeur annuler" style="float: left;" href="#" >Annuler</a>
                <a id="signature_drm_popup_confirm" class="btn_validation" href="javascript:void(0)" ><span>Valider la DRM</span></a>
                <a id="signature_drm_popup_confirm_progress" style="display: none; float: right;" class="btn_validation" href="javascript:void(0)" ><span>Valider la DRM</span></a>
            </div>
        </div>
</div>
