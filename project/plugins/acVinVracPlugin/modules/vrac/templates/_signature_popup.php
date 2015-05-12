<div style="display:none">
    <div id="signature_popup_content">
    Êtes-vous sûr de vouloir confirmer la signature du contrat : <?php echo $vrac->numero_contrat; ?> (<?php echo $vrac->campagne; ?>) ?
    <br/>
    <br/>
    <a id="signature_popup_close" href="<?php echo url_for('vrac_signature', $vrac) ?>" class="btn_majeur btn_vert f_right">Signer le contrat</a>     
    
        
    </div>
</div>