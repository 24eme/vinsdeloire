<div class="form_contenu">                  
    <?php
    $compte = $compteForm->getObject();
    
    echo $compteForm->renderHiddenFields();
    echo $compteForm->renderGlobalErrors();
    
    $countries = $compteForm->getCountryList();
    ?>


    <fieldset>
        <div class="form_ligne">
            <legend>Adresse</legend>
        </div>
        <div class="form_ligne">
            <label for="adresse">
                <label for="compte_modification_adresse">N° et nom de rue *</label> 
            </label>
            <input type="text" id="compte_modification_adresse" class="champ_long" value="<?php echo $compte->adresse ; ?>" disabled="disabled" >   
        </div>
        <div class="form_ligne">
            <label for="adresse_complementaire">
                <label for="compte_modification_adresse_complementaire">Adresse complémentaire</label>            </label>
            <input type="text" id="compte_modification_adresse_complementaire" class="champ_long" value="<?php echo $compte->adresse_complementaire ; ?>" disabled="disabled" >      
        </div>
        <div class="form_ligne">
            <label for="code_postal">
                <label for="compte_modification_code_postal">CP *</label>      
            </label>
            <input type="text" id="compte_modification_code_postal" value="<?php echo $compte->code_postal ; ?>" disabled="disabled" >       
        </div>
        <div class="form_ligne">
            <label for="commune">
                <label for="compte_modification_commune">Ville *</label>         
            </label>
            <input type="text" id="compte_modification_commune" class="champ_long" value="<?php echo $compte->commune ; ?>" disabled="disabled" >  
        </div>                
        <div class="form_ligne">
            <label for="cedex">
                <label for="compte_modification_cedex">Cedex</label>           
            </label>
            <input type="text" id="compte_modification_cedex" value="<?php echo $compte->cedex ; ?>" disabled="disabled" >     
        </div>                 
        <div class="form_ligne">
            <label for="pays">
                <label for="compte_modification_pays">Pays *</label>            
            </label>
            <select id="compte_modification_pays" class="autocomplete" name="compte_modification[pays]" disabled="disabled"  >
                <?php foreach ($countries as $key => $country): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($compte->pays == $key)? 'selected="selected"' : '';?> ><?php echo $country; ?></option>
                <?php endforeach; ?>
            </select>
        </div>   
    </fieldset>

    <fieldset>
        <div class="form_ligne">
            <legend>E-mail / téléphone / fax</legend>
        </div>
        <div class="form_ligne">
            <label for="email">
                <?php echo $compteForm['email']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['email']->render(); ?>
            <?php echo $compteForm['email']->renderError(); ?>
        </div>
        <div class="form_ligne">
            <label for="telephone_perso">
                <?php echo $compteForm['telephone_perso']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['telephone_perso']->render(); ?>
            <?php echo $compteForm['telephone_perso']->renderError(); ?>
        </div>
        <div class="form_ligne">
            <label for="telephone_bureau">
                <?php echo $compteForm['telephone_bureau']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['telephone_bureau']->render(); ?>
            <?php echo $compteForm['telephone_bureau']->renderError(); ?>
        </div>
        <div class="form_ligne">
            <label for="telephone_mobile">
                <?php echo $compteForm['telephone_mobile']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['telephone_mobile']->render(); ?>
            <?php echo $compteForm['telephone_mobile']->renderError(); ?>
        </div>
        <div class="form_ligne">
            <label for="fax">
                <?php echo $compteForm['fax']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['fax']->render(); ?>
            <?php echo $compteForm['fax']->renderError(); ?>
        </div>
        <?php if(isset($compteSociete) && $compteSociete):  ?>
        <div class="form_ligne">
            <label for="site">
                <?php echo $compteForm['site_internet']->renderLabel(); ?>
            </label>
            <?php echo $compteForm['site_internet']->render(); ?>
            <?php echo $compteForm['site_internet']->renderError(); ?>
        </div>
        <?php endif; ?>
    </fieldset>
</div>