<div id="colonne_intitules" style="width: 210px">
    <p class="couleur">Produit</p>
    <p class="label">Labels</p>

    <div class="groupe groupe_ouvert groupe_bloque" data-groupe-id="1">
        <p class="itemcache">Stock théorique dbt de mois</p>
        <ul>
            <?php foreach ($config->detail->getStocksDebut() as $key => $item): ?>
                <li class="<?php if ($key != 'revendique') echo ' itemcache'; else echo ' li_gris' ?>"><?php echo $item->getLibelle() ?>&nbsp;(<span class="unite">hl</span>)&nbsp;<a href="" class="msg_aide" data-msg="help_popup_drm_stockdebut_<?php echo $key; ?>" title="Message aide"></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="groupe" data-groupe-id="2">
        <p>Mouvements d'entrées</p>
        <ul>
            <?php foreach ($config->detail->getEntrees() as $key => $item): ?>
                <li<?php if ($item->getFacturable()) {echo ' class="facturable"';}?>><?php echo $item->getLibelle() ?>&nbsp;(<span class="unite">hl</span>)&nbsp;<a href="" class="msg_aide" data-msg="help_popup_drm_entrees_<?php echo $key; ?>" title="Message aide"></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="groupe groupe_ouvert" data-groupe-id="3">
        <p>Mouvements de sorties</p>
        <ul>
            <?php foreach ($config->detail->getSorties() as $key => $item): ?>
                <li<?php if ($item->getFacturable()) {echo ' class="facturable"';}?>><?php echo $item->getLibelle() ?>&nbsp;(<span class="unite">hl</span>)&nbsp;<a href="" class="msg_aide" data-msg="help_popup_drm_sorties_<?php echo $key; ?>" title="Message aide"></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="groupe groupe_ouvert groupe_bloque" data-groupe-id="4">
        <p class="itemcache">Stock théorique fin de mois</p>
        <ul>
            <?php foreach ($config->detail->getStocksFin() as $key => $item): ?>
                <li class="<?php if ($key != 'revendique') echo ' itemcache'; else echo ' li_gris' ?>"><?php echo $item->getLibelle() ?>&nbsp;(<span class="unite">hl</span>)&nbsp;<a href="" class="msg_aide" data-msg="help_popup_drm_stockfin_<?php echo $key; ?>" title="Message aide"></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
