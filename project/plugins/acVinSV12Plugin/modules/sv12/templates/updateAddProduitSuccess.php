<!-- #principal -->
<section id="principal" class="sv12">
    <p id="fil_ariane"><a href="<?php echo url_for('sv12') ?>">Page d'accueil</a> &gt; <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>"><?php echo $sv12->declarant->nom ?></a> &gt; <a href="<?php echo url_for('sv12_update', $sv12) ?>"><?php echo $sv12 ?></a> &gt; <strong>Ajouter un produit</strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <h2>Déclaration SV12</h2>
        <?php include_partial('produitForm', array('sv12' => $sv12, 'form' => $form)); ?>
    </section>
    <!-- fin #contenu_etape -->
</section>

<script type="text/javascript">
    $(document).ready(function () {
        $( "#<?php echo $form['hashref']->renderId() ?>" ).combobox();
    });
</script>

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
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('sv12_update', $sv12); ?>" class="btn_majeur btn_acces"><span>Edition SV12</span></a>
        </div>
    </div>
</div>
<?php
end_slot();
?>



