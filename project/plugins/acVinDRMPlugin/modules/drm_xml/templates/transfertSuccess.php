<section id="principal" class="drm">
    <div id="application_drm">
        <div id="contenu_etape">
		<h2>Transmission de votre DRM à la Douane</h2>
<?php if (!$drm->transmission_douane->success) :
  if (preg_match('/HTTP Error \d/', $cielResponse) || preg_match('/permission to access .authtoken.oauth2/', $cielResponse) || preg_match('/invalid certificate/', $cielResponse) || preg_match('/Access token/', $cielResponse)) {
    echo "<p><strong>Le service de reception des DRM de la Douane est indisponible pour le moment.</strong></p>";
  }else{
	   echo "<p>Une erreur s'est produite lors du transfert de votre DRM : ".html_entity_decode($cielResponse)."</p>";
  }
else: ?>
	<p>Votre DRM a été transmise avec succès sur le portail <a href="https://douane.gouv.fr/">douane.gouv.fr</a>.<br/><br/></p>
	<p>Pour terminer cette prodécure, vous devez vous rendre sur le site des douanes, une fois connecté sur l'espace DRM CIEL, vous pourrez valider votre DRM.<br/><br/></p>
    <br/>
	<p style="text-align: center;"><a class="btn_majeur" href="https://douane.gouv.fr/">Cliquez ici pour vous rendre sur proDouane</a>.</p>
    <br/><br/><br/><br/>
<?php endif; ?>
</div></div>
</section>
<div id="btn_etape_dr">
    <a href="<?php echo url_for('@drm_etablissement?identifiant='.$drm->identifiant); ?>" class="btn_etape_prec"><span>Retour à mon espace</span></a>
</div>
<?php
include_partial('drm/colonne_droite', array('drm' => $drm, 'isTeledeclarationMode' => true));
?>
