<?php
use_helper('Vrac');
if(isset($vrac)) $campagneDate = dateCampagneViticole($vrac->dateSignature);
if(isset($vracs)) $campagneDates = VracClient::getInstance()->listCampagneByEtablissementId($identifiant);

?>
<div id="campagne_viticole" class="bloc_col">
    <h2>Campagne viticole</h2>
    <div class="contenu">
            <div id="campagne_viticoleChoice">
                <?php if($visualisation): ?>
                    <span><?php echo $campagneDate; ?></span>
                <?php else: ?>
		    <form>
                    <select name="campagne" id="campagne_viticole_date">
                    <?php foreach ($campagneDates as $c => $c_libelle):?>
                        <option <?php echo ($c==$campagne)? 'selected="selected"' : ''; ?>><?php echo $c_libelle; ?></option>
                    <?php endforeach; ?>
                    </select>
		    <input type="submit" class="btn_vert btn_majeur" value="changer"/>
		    </form>
                <?php endif; ?>
            </div>
    </div>
</div>