<?php
use_helper('Etablissement');
use_helper('Date');
$typesLiaisons = EtablissementClient::getTypesLiaisons();
if (!isset($fromSociete))
    $fromSociete = false;
?>
<div id="etablissement_<?php echo $etablissement->identifiant; ?>" class="etablissement form_section ouvert">
    <h3><?php echo $etablissement->nom; ?></h3>
    <div class="form_contenu">
        <div class="form_modifier">
            <?php if ($fromSociete) : ?>
                <a id="btn_modifier" href="<?php echo url_for('etablissement_visualisation', $etablissement); ?>" class="btn_majeur btn_voir">Voir Etablissement</a>
            <?php endif; ?>
        </div>
        <div class="form_ligne">
            <label for="famille">
                Type établissement :
            </label>
            <?php echo EtablissementFamilles::getFamilleLibelle($etablissement->famille); ?> <?php echo ($etablissement->_get('famille') == EtablissementFamilles::FAMILLE_COOPERATIVE)? "(".EtablissementFamilles::FAMILLE_COOPERATIVE.')' : ''; ?>
        </div>
        <div class="form_ligne">
            <label for="nom">
                Nom du chai :
            </label>
            <?php echo $etablissement->nom; ?>
        </div>
        <div class="form_ligne">
            <label for="statut">
                Statut :</label>
            <?php echo $etablissement->statut; ?>
        </div>
        <?php if ($etablissement->recette_locale && $etablissement->recette_locale->nom) : ?>
            <div class="form_ligne">
                <label for="recette_locale">
                    Recette locale :
                </label>
                <a href="<?php echo url_for('societe_visualisation', SocieteClient::getInstance()->find($etablissement->recette_locale->id_douane)); ?>">
                    <?php echo $etablissement->recette_locale->nom; ?>
                </a>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->relance_ds) : ?>
            <div class="form_ligne">
                <label for="relance_ds">
                    Relance DS :
                </label>
                <?php echo $etablissement->relance_ds; ?>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->raisins_mouts) : ?>
            <div class="form_ligne">
                <label for="raisins_mouts">
                    Raisins et moûts :
                </label>
                <?php echo $etablissement->raisins_mouts; ?>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->exclusion_drm) : ?>
            <div class="form_ligne">
                <label for="exclusion_drm">
                    Exclusion DRM :
                </label>
                <?php echo $etablissement->exclusion_drm; ?>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->type_dr && !$etablissement->isCourtier()) : ?>
            <div class="form_ligne">
                <label for="type_dr">
                    Type DR :
                </label>
                <?php echo $etablissement->type_dr; ?>
            </div>
        <?php endif; ?>
        <?php if ($etablissement->cvi): ?>
            <div class="form_ligne">
                <label for="cvi">
                    CVI :</label>
                <?php echo $etablissement->cvi; ?>
            </div>
        <?php endif; ?>
        <?php if ($etablissement->exist('ppm') && $etablissement->ppm): ?>
            <div class="form_ligne">
                <label for="ppm">
                    PPM :</label>
                <?php echo $etablissement->ppm; ?>
            </div>
        <?php endif; ?>
        <?php if ($etablissement->no_accises): ?>
            <div class="form_ligne">
                <label for="no_accises">
                    Numéro d'accises :</label>
                <?php echo $etablissement->no_accises; ?>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->site_fiche): ?>
            <div class="form_ligne">
                <label for="site_fiche">
                    Site fiche :</label>
                <a href="<?php echo $etablissement->site_fiche; ?>"><?php echo $etablissement->site_fiche; ?></a>
            </div>
        <?php endif; ?>
        <?php if (!$fromSociete && $etablissement->carte_pro && $etablissement->isCourtier()) : ?>
            <div class="form_ligne">
                <label for="carte_pro">
                    Carte professionnelle :
                </label>
                <?php echo $etablissement->carte_pro; ?>
            </div>
        <?php endif; ?>
        <div class="form_ligne">
            <label for="region">
                Région :
            </label>
            <?php echo $etablissement->region; ?>
        </div>
        <div class="form_ligne">
            <label for="commune">
                Ville :
            </label>
            <?php echo $etablissement->siege->commune; ?>
        </div>
        <div class="form_ligne">
            <label for="adresse_societe">
                Adresse de la société :</label>
            <?php echo display_adresse_societe($etablissement); ?>
        </div>

            <div class="form_ligne">
                <form method="POST" action="<?php echo url_for('etablissement_resetcrd', $etablissement);?>">
                    <label for="crd_regime">
                        Régime de CRD :
                    </label>
                    <?php if ($etablissement->exist('crd_regime') && $etablissement->getCrdRegimeArray()) : ?>
                        <?php foreach ($etablissement->getCrdRegimeArray() as $crd_regime) :
                            echo EtablissementClient::$regimes_crds_libelles[$crd_regime].'&nbsp;&nbsp;';
                        endforeach; ?>
                        <br/>
                        <input type="submit" class="btn_majeur btn_contact" style="float:right;" value="Réinitialiser le regime CRD pour les DRM suspendues"/>
                    <?php else: ?>
                        En attente de saisie par l'utilisateur
                     <?php endif; ?>
                </form>
            </div>
            <div class="form_ligne">
                <label for="commentaire">
                    Caution :</label>
                <?php echo ($etablissement->exist('caution') && $etablissement->caution) ? "Caution" : (($etablissement->exist('caution') && !is_null($etablissement->caution)) ? "Dispensé" : "Non défini");
                    echo ($etablissement->exist('caution') && $etablissement->caution && $etablissement->exist('raison_sociale_cautionneur'))? " / ".$etablissement->raison_sociale_cautionneur : '';
                 ?>
            </div>
            <div class="form_ligne">
                <label>Email télédéclaration :</label>
                <?php echo ($etablissement->exist('teledeclaration_email')) ? $etablissement->teledeclaration_email : ""; ?>
            </div>
            <?php if($etablissement->exist('mois_stock_debut') && $etablissement->mois_stock_debut): ?>
                <div class="form_ligne">
                  <label for="mois_stock_debut">
                    Mois de saisie du stock :</label>
                    <?php echo ucfirst(format_date(date("Y")."-".$etablissement->mois_stock_debut."-01", 'MMMM', 'fr_FR')); ?>
                </div>
            <?php endif; ?>

        <?php if (!$fromSociete && $etablissement->commentaire): ?>
            <div class="form_ligne">
                <label for="commentaire">
                    Commentaire :</label>
                <?php echo $etablissement->commentaire; ?>
            </div>
        <?php endif; ?>

        <?php if (!$fromSociete && count($etablissement->liaisons_operateurs) > 0) : ?>
            <div class="form_ligne">
                <legend>
                    Liaisons opérateurs :
                </legend>
            </div>
            <?php foreach ($etablissement->liaisons_operateurs as $liaison_operateur): ?>
                <div class="form_ligne">
                    <div class="form_colonne">
                        <label for="libelle_etablissement"><?php echo $typesLiaisons[$liaison_operateur->type_liaison]; ?></label>
                        <a href="<?php echo url_for('etablissement_visualisation', EtablissementClient::getInstance()->find($liaison_operateur->id_etablissement)); ?>">
                            <?php echo $liaison_operateur->libelle_etablissement; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
