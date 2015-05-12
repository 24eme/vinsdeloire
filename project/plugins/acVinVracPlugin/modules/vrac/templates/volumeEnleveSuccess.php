<?php
?>

<div id="contenu">
    <div id="rub_contrats">
        <section id="principal">      
            <section id="contenu_etape"> 
                <form id="volume_propose" method="post" action="<?php echo url_for('vrac_volumeEnleve',$vrac); ?>">
                <?php echo $form->renderHiddenFields() ?>
                <?php echo $form->renderGlobalErrors() ?>    
                <h2>N° d'enregistrement du contrat : <span><?php echo $vrac['numero_contrat']; ?></span></h2>
                
                <div id="volume_enleve" class="section_label_maj">
                <?php echo $form['volume_enleve']->renderLabel(); ?>
                <?php echo $form['volume_enleve']->render(); ?> (en hl) 
                <span style="text-align: right;"><?php echo $form['volume_enleve']->renderError(); ?></span>
                </div>
                        <div id="btn_etape_dr">
                            <div class="btnValidation">
                            <span>&nbsp;</span>
                            <button class="btn_majeur btn_gris" type="submit"> Valider</button>
                            </div>       
                        </div>
                </form>
            </section>
        </section>
    </div>
</div>