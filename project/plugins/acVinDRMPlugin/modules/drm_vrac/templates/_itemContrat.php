<?php
$contrat = VracClient::getInstance()->retrieveById($form->getObject()->getKey());

?><tr>
        <td></td>
        <td align="center">
    <span><?php echo $form->getObject()->getKey() ?> (<?php echo $contrat->acheteur->nom; ?> - <?php echo $contrat->volume_propose; ?>&nbsp;hl de <?php echo ConfigurationClient::getCurrent()->get(substr($contrat->produit, 1))->getLibelleFormat() ?> à <?php echo $contrat->prix_total; ?>&nbsp;€)</span>
        </td>
        <td align="center">
                <form action="<?php echo url_for('drm_vrac_update_volume', $form->getObject()) ?>" method="post">
                        <?php echo $form->renderGlobalErrors() ?>
                        <?php echo $form->renderHiddenFields() ?>
                        <div class="ligne_form">
                                <?php echo $form['volume']->render() ?>
                                <span class="unite"> (en hl) </span>
                                <span class="error"><?php echo $form['volume']->renderError() ?></span>
                        </div>
                        <div class="ligne_form_btn">
                                <button name="valider" class="btn_valider" type="submit">OK</button>
                        </div>
                </form>
        </td>
        <td>
                <a href="<?php echo url_for('drm_delete_vrac', $form->getObject()) ?>">X</a>
        </td>
</tr>