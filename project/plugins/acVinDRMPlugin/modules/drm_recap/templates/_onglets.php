
<ul id="onglets_principal">
 <div id="msg_aide_drm">
        <a href="" class="msg_aide" data-msg="help_popup_drm_global" data-doc="notice.pdf" title="Message aide"></a>
 </div>
    <?php foreach ($items as $item): ?>
        <?php if ($item->hasMouvementCheck()): ?>
            <?php if ($item->getHash() == $drm_lieu->getHash()): ?>
                <li class="actif">
                    <strong>
                        <?php echo $item->getLibelle(ESC_RAW) ?> 
                        <span class="completion<?php if($item->isComplete()): ?> completion_validee<?php endif; ?>">
                            (<span class="appellation_produit_saisie"><?php echo $item->nbComplete() ?></span>/<span class="appellation_produit_total"><?php echo $item->nbToComplete() ?></span>)
                        </span>
                    </strong>
                </li>
            <?php else: ?>
                <li>
                    <a title="<?php echo $item->getLibelle(ESC_RAW) ?>" href="<?php echo url_for('drm_recap_lieu', $item) ?>">
                        <?php echo $item->getConfig()->getCodeFormat("%a%%l%") ?> 
                        <span class="completion<?php if($item->isComplete()): ?> completion_validee<?php endif; ?>">
                            (<span class="appellation_produit_saisie"><?php echo $item->nbComplete() ?></span>/<span class="appellation_produit_total"><?php echo $item->nbToComplete() ?></span>)
                        </span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <li class="ajouter">
        <a href="<?php echo url_for('drm_recap_lieu_ajout_ajax', $drm_lieu->getCertification()) ?> "class="btn_popup" data-popup="#popup_ajout_appelation" data-popup-ajax="true" data-popup-config="configForm">
            Ajouter une appellation
        </a></li>
</ul>