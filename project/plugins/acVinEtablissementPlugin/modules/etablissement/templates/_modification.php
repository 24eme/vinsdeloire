<div class="form_contenu">
    <?php
    echo $etablissementForm->renderHiddenFields();
    echo $etablissementForm->renderGlobalErrors();
    ?>
    <div class="form_ligne">
        <?php echo $etablissementForm['nom']->renderLabel(); ?>
        <?php echo $etablissementForm['nom']->render(array('class' => 'champ_long')); ?>
        <?php echo $etablissementForm['nom']->renderError(); ?>
    </div>
    <div class="form_ligne">
            <?php echo $etablissementForm['statut']->renderLabel('Statut *',array('class' => 'label_liste')); ?>
            <?php echo $etablissementForm['statut']->render(); ?>
            <?php echo $etablissementForm['statut']->renderError(); ?>
    </div>
    <?php if (!$etablissement->isNegociant() && !$etablissement->isCourtier()) : ?>
        <div class="form_ligne">
            <div class="form_colonne">
                <?php echo $etablissementForm['raisins_mouts']->renderLabel(); ?>                
                <?php echo $etablissementForm['raisins_mouts']->render(); ?>
                <?php echo $etablissementForm['raisins_mouts']->renderError(); ?>
            </div>
            <div class="form_colonne">
                <?php echo $etablissementForm['exclusion_drm']->renderLabel(); ?>
                <?php echo $etablissementForm['exclusion_drm']->render(); ?>
                <?php echo $etablissementForm['exclusion_drm']->renderError(); ?>
            </div>
        </div>
    <?php
    endif;
    if (!$etablissement->isCourtier()) :
        ?>
            <div class="form_ligne">
                <?php echo $etablissementForm['recette_locale_choice']->renderLabel(); ?>
                <?php echo $etablissementForm['recette_locale_choice']->render(array('class' => 'champ_long')); ?>
    <?php echo $etablissementForm['recette_locale_choice']->renderError(); ?>
            </div> 
    
        <div class="form_ligne">
            <div class="form_colonne">
                <?php echo $etablissementForm['relance_ds']->renderLabel(); ?>
                <?php echo $etablissementForm['relance_ds']->render(); ?>
    <?php echo $etablissementForm['relance_ds']->renderError(); ?>
            </div>
            <div class="form_colonne">
            </div>
        </div>    
<?php endif; ?>
    <div class="form_ligne">
        <div class="form_colonne">
            <?php echo $etablissementForm['region']->renderLabel(); ?>
            <?php echo $etablissementForm['region']->render(); ?>
        <?php echo $etablissementForm['region']->renderError(); ?>
        </div>
            <?php if (!$etablissement->isNegociant() && !$etablissement->isCourtier()) : ?>
            <div class="form_colonne">
                <?php echo $etablissementForm['type_dr']->renderLabel(); ?>
                <?php echo $etablissementForm['type_dr']->render(); ?>
            <?php echo $etablissementForm['type_dr']->renderError(); ?>
            </div>
<?php endif; ?>
    </div>
             <div id="liaisons_list">
            <?php
            foreach ($etablissementForm['liaisons_operateurs'] as $liaisonForm) {
                include_partial('itemLiaison', array('form' => $liaisonForm));
            }
            ?>
            <div class="form_ligne">
                <a class="btn_ajouter_ligne_template" data-container="#liaisons_list" data-template="#template_liaison" href="#">Ajouter une liaison</a>
            </div>
        </div>
<?php
    if (!$etablissement->isCourtier()):
        ?>
        <div class="form_ligne">
            <?php echo $etablissementForm['cvi']->renderLabel(); ?>
        <?php echo $etablissementForm['cvi']->render(); ?>
        <?php echo $etablissementForm['cvi']->renderError(); ?>
        </div> 
        <?php endif; ?>
    <div class="form_ligne">
        <?php echo $etablissementForm['site_fiche']->renderLabel(); ?>
<?php echo $etablissementForm['site_fiche']->render(); ?>
        <?php echo $etablissementForm['site_fiche']->renderError(); ?>
    </div>
     <?php if ($etablissement->isCourtier()): ?>
    <div class="form_ligne">
        <?php echo $etablissementForm['carte_pro']->renderLabel(); ?>
<?php echo $etablissementForm['carte_pro']->render(); ?>
        <?php echo $etablissementForm['carte_pro']->renderError(); ?>
    </div>
    <?php endif; ?>
    <div class="form_ligne">
        <?php echo $etablissementForm['no_accises']->renderLabel(); ?>
<?php echo $etablissementForm['no_accises']->render(); ?>
        <?php echo $etablissementForm['no_accises']->renderError(); ?>
    </div>
    <div class="form_ligne">
        <?php echo $etablissementForm['commentaire']->renderLabel(); ?>
<?php echo $etablissementForm['commentaire']->render(); ?>
<?php echo $etablissementForm['commentaire']->renderError(); ?>
    </div>

</div>
<?php include_partial('templateLiaisonItem', array('form' => $etablissementForm->getFormTemplate()));
?>
<script type="text/javascript">    
    (function($)
    {
        $(document).ready(function()
        {
            initCollectionAddTemplate('.btn_ajouter_ligne_template', /var---nbItem---/g, callbackAddTemplate);
            initCollectionDeleteTemplate();
        });

        var callbackAddTemplate = function(bloc)
        {
          
        }

       
        var initCollectionAddTemplate = function(element, regexp_replace, callback)
        {
       
            $(element).live('click', function()
            {
                
        console.log($($(this).attr('data-template')).html());
                var bloc_html = $($(this).attr('data-template')).html().replace(regexp_replace, UUID.generate());

                try {
                    var params = jQuery.parseJSON($(this).attr('data-template-params'));
                } catch (err) {

                }

                for(key in params) {
                    bloc_html = bloc_html.replace(new RegExp(key, "g"), params[key]);
                }

                var bloc = $($(this).attr('data-container')).before(bloc_html);

                if(callback) {
                    callback(bloc);
                }
                return false;
            });
        }
   
        var initCollectionDeleteTemplate = function()
        {
            $('.btn_supprimer_ligne_template').live('click',function()
            {
                var element = $(this).attr('data-container');
                $(this).parent(element).remove();
   
                return false;
            });
        }
    })(jQuery);
    
</script>
