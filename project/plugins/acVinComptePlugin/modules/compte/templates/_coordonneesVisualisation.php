<fieldset>
    <div class="form_ligne">
        <legend>Adresse</legend>
    </div>
    <div class="form_ligne">
        <label for="adresse">
            Adresse :
        </label>
        <?php echo $compte->adresse; ?>
    </div>
    <?php if ($compte->adresse_complementaire) : ?>
        <div class="form_ligne">
            <label for="adresse_complementaire">
                Adresse complémentaire :
            </label>
            <?php echo $compte->adresse_complementaire; ?>
        </div>
    <?php endif; ?>
    <div class="form_ligne">
        <label for="code_postal">
            Code postal :
        </label>
        <?php echo $compte->code_postal; ?>
    </div>
    <div class="form_ligne">
        <label for="commune">
            Commune :
        </label>
        <?php echo $compte->commune; ?>
    </div>   
    <?php if ($compte->cedex) : ?>
        <div class="form_ligne">
            <label for="cedex">
                Cedex :
            </label>
            <?php echo $compte->cedex; ?>
        </div>       
    <?php endif; ?>
    <div class="form_ligne">
        <label for="pays">
            Pays :
        </label>
        <?php echo $compte->pays; ?>
    </div>
</fieldset>
<fieldset>
    <div class="form_ligne">
        <legend>E-mail / téléphone / fax</legend>
    </div>
    <?php if ($compte->email) : ?>
        <div class="form_ligne">
            <label for="email">
                E-mail : 
            </label>
            <?php echo $compte->email; ?>
        </div>
    <?php endif; ?>
    <?php if ($compte->telephone_perso) : ?>
        <div class="form_ligne">
            <label for="telephone_perso">
                Téléphone perso :
            </label>
            <?php echo $compte->telephone_perso; ?>
        </div>
    <?php endif; ?>
    <?php if ($compte->telephone_bureau) : ?>
        <div class="form_ligne">
            <label for="telephone_bureau">
                Téléphone bureau :
            </label>
            <?php echo $compte->telephone_bureau; ?>
        </div>
    <?php endif; ?>
    <?php if ($compte->telephone_mobile) : ?>
        <div class="form_ligne">
            <label for="telephone_mobile">
                Téléphone mobile :
            </label>
            <?php echo $compte->telephone_mobile; ?>
        </div>
    <?php endif; ?>
    <?php if ($compte->fax) : ?>
        <div class="form_ligne">
            <label for="fax">
                Fax :
            </label>
            <?php echo $compte->fax; ?>
        </div>
    <?php endif; ?>
    <?php if ($compte->exist('site_internet')) : ?>
        <div class="form_ligne">
            <label for="site_internet">
                Site Internet :
            </label>
            <a href="<?php echo $compte->site_internet; ?>"><?php echo $compte->site_internet; ?></a>
        </div>
    <?php endif; ?>
</fieldset>
<fieldset>
        <div class="form_ligne">
            <legend>Droits</legend>
        </div>
        <div class="form_ligne">
            <ul>
                <?php foreach ($compte->getDroits() as $droit) : ?>
                    <li><?php echo $droit; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
</fieldset>
<fieldset>
    <div class="form_ligne">
        <legend>Tags - étiquettes </legend>
    </div>
        <?php foreach ($compte->tags as $type_tag => $selected_tags) : ?>
            <div class="form_ligne">
            <label for="tags" class="label_liste"><?php echo $type_tag; ?></label>
            <ul>
                <?php
                foreach ($selected_tags as $t) {
                    $targs['tags'] = implode(',',array($type_tag . ':' . $t));
                    echo '<li><a href="' . url_for('compte_search', $targs) . '">' . str_replace('_', ' ', $t) . '</a>&nbsp;';
                    $targs['tag'] = $t;
                    $targs['q'] = $compte->identifiant;
                    if ($type_tag == 'manuel') {
                        echo '(<a class="removetag" href="' . url_for('compte_removetag', $targs) . '">X</a>)';
                    }
                    echo '</li>';
                }
                ?>
            </ul>
    </div>
<?php endforeach; ?>
</fieldset>