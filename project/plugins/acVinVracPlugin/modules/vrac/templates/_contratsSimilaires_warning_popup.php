<div style="display: none">
    <div class="contrat_similaire_popup popup_contenu" id="contrat_similaire_popup">
        <div id="contrats_similaires_popup_message">
            <span class="msg">
                Le contrat que vous êtes en train de saisir comporte plusieurs similitudes avec le ou les contrats ci-dessous :
            </span>
        </div>
        <?php
        include_partial('contratsSimilaires', array('vrac' => $vrac));
        ?>
        <div id="ligne_btn">
                <div class="btnRetour">
                         <a href="<?php echo url_for('vrac_marche', $vrac); ?>" class="btn_etape_prec"><span>Retour à l'étape 2</span></a>
                </div>
                <div class="btnValidation">
                        <a href="#" class="btn_validation" id="popup_validation"><span>Terminer la saisie</span></a>                       
                </div>
               
        </div>  
    </div>
</div>
<a href="" class="btn_popup"
    data-popup-config="configDefaut"
    data-popup="#contrat_similaire_popup"
    data-popup-titre="Etes-vous sûr de vouloir modifier ces informations ?"
    style="display: none">Attention!</a>