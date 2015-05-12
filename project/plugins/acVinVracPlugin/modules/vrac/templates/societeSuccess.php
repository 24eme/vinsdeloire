<?php
use_helper('Vrac');
use_helper('Float');
?>
<section id="principal">

    <h2 class="titre_societe titre_societe_teledeclaration">
        Espace de <?php echo $societe->raison_sociale; ?>
    </h2>
    <div class="clearfix">
        <ul class="stats_contrats">
            <li class="stats_contrats_brouillon"> 
                 <div class="action <?php echo ($contratsSocietesWithInfos->infos->brouillon)? "actif" : ""; ?>">
                   <h2>  Brouillon </h2>
                <a href="#">
                    <?php echo $contratsSocietesWithInfos->infos->brouillon; ?> contrat(s) en brouillon</a>
                 </div>
            </li>
            <li class="stats_contrats_a_signer <?php echo ($contratsSocietesWithInfos->infos->attente_signature)? "actif" : ""; ?>">
                <div class="action <?php echo ($contratsSocietesWithInfos->infos->attente_signature)? "actif" : ""; ?>">
                 <h2>  A Signer </h2>
                <a href="#">
                    <?php echo $contratsSocietesWithInfos->infos->attente_signature; ?> contrat(s) à signer</a>
                </div>
            </li>
            <li class="stats_contrats_en_attente">
                <div class="action <?php echo ($contratsSocietesWithInfos->infos->valide)? "actif" : ""; ?>">
                     <h2>  En Attente </h2>
                    <a href="#">
                        <?php echo $contratsSocietesWithInfos->infos->valide; ?> contrat(s) en attente
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <div class="ligne_btn txt_droite">
        <?php if ($etablissementPrincipal->isCourtier() || $etablissementPrincipal->isNegociant()): ?>      
            <a class="btn_orange btn_majeur" href="<?php echo url_for('vrac_nouveau', array('etablissement' => $etablissementPrincipal->identifiant)); ?>">
                Saisir Un Nouveau contrat
            </a>
        <?php endif; ?>
    </div>

    <?php include_partial('contratsTable', array('contrats' => $contratsSocietesWithInfos->contrats, 'societe' => $societe)); ?>

    <div class="ligne_btn txt_droite">     
        <a class="btn_majeur" href="<?php echo url_for('vrac_history', array('identifiant' => $etablissementPrincipal->identifiant, 'campagne' => ConfigurationClient::getInstance()->getCurrentCampagne(), 'etablissement' => 'all')); ?>">
            Voir tout l'historique
        </a>
    </div>


</section>

<?php
include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));
?>