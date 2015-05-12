<!-- #principal -->
<section id="principal" class="sv12">
    <p id="fil_ariane"><a href="<?php echo url_for('sv12') ?>">Page d'accueil</a> &gt; <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>"><?php echo $sv12->declarant->nom ?></a> &gt; <strong><?php echo $sv12 ?></strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <h2>Déclaration SV12</h2>
        <?php include_partial('document_validation/validation', array('validation' => $validation)); ?>
        <?php include_partial('negociant_infos', array('sv12' => $sv12)); ?>

        <form name="sv12_recapitulatif" method="POST" action="<?php echo url_for('sv12_recapitulatif', $sv12); ?>" >

            <h2>Récapitulatif</h2>            
            <?php include_partial('totaux', array('sv12' => $sv12)); ?>

            <h2>Mouvements</h2>
            <?php include_partial('mouvements', array('mouvements' => $mouvements)); ?>

            <div class="btn_etape">
                <a href="<?php echo url_for('sv12_update', $sv12); ?>" class="btn_etape_prec"><span>Précedent</span></a>                
                <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()); ?>" class="btn_majeur btnModification">Enregistrer le brouillon</a>
                <button type="submit" class="btn_majeur btn_terminer_saisie btnValidation">Valider</button>
            </div>
        </form>
    </section>
</section>
    <!-- fin #contenu_etape -->
<?php
slot('colButtons');
?>
<div id="action" class="bloc_col">
    <h2>Action</h2>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('sv12'); ?>" class="btn_majeur btn_acces"><span>Retour à l'accueil</span></a>
        </div>
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>" class="btn_majeur btn_acces"><span>Historique opérateur</span></a>
        </div>
      </div>
</div>
<?php
end_slot();
?>
