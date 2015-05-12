  
    <!-- #principal -->
    <section id="principal" class="sv12">
        <p id="fil_ariane"><a href="<?php echo url_for('sv12') ?>">Page d'accueil</a> &gt; <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>"><?php echo $sv12->declarant->nom ?></a> &gt; <strong><?php echo $sv12 ?></strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <h2>Déclaration SV12</h2>
            <?php include_partial('negociant_infos',array('sv12' => $sv12)); ?>
       
            <?php if ($sv12->isModifiable()): ?>
            <a class="btn_majeur btn_modifier" href="<?php echo url_for('sv12_modificative', $sv12) ?>" id="btn_modifier_sv12">Modifier la SV12</a>
            <?php endif; ?>

            <?php if(!$sv12->isMaster()): ?>
            <div id="points_vigilance">
                <ul>
                    <li class="warning">Ce n'est pas la <a href="<?php echo url_for('drm_visualisation', $sv12->getMaster()) ?>">dernière version</a> de la SV12, le tableau récapitulatif n'est donc pas à jour.</a></li>
                </ul>
            </div>
            <?php endif; ?>

            <?php if(count($contrats_non_saisis) > 0): ?>
                <h2>Contrats sans volume saisie</h2>
                <?php include_partial('contrats', array('contrats' => $contrats_non_saisis)); ?>
            <?php endif; ?>

            <h2>Récapitulatif</h2>
            <?php include_partial('totaux', array('sv12' => $sv12)); ?>

            <h2>Mouvements</h2>
            <?php include_partial('mouvements',array('mouvements' => $mouvements, 'hamza_style' => true)); ?>
            
			<div class="btn_etape">
				<a class="btn_etape_prec" href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>">
					<span>Retour à mon espace</span>
				</a> 
			</div>   
        </section>
        <!-- fin #contenu_etape -->
    </section>
    
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

    