<?php
use_helper('Date');
?>
<h2>Historique des factures</h2>
<?php
if(count($factures->getRawValue())==0) :
?>
<p>
    Il n'existe aucune facture générée pour cet établissement
</p>
<?php else : ?>
<fieldset>
    <table class="table_recap">
        <thead>
            <tr>
                <th>Date</th>
                <th>DRM liées</th>
                <th>Prix TTC</th>
                <th>Défacturer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures->getRawValue() as $facture) :
                ?>
                <tr>
                    <td><?php $fc = FactureClient::getInstance();
                        $d = format_date($facture->value[FactureSocieteView::VALUE_DATE_FACTURATION],'dd/MM/yyyy').' (créée le '.$fc->getDateCreation($facture->id).')';
                        echo link_to($d, array('sf_route' => 'facture_pdf', 'identifiant' => $facture->key[FactureSocieteView::KEYS_FACTURE_ID])); ?>
                    </td>
                    <td><?php foreach ($facture->value[FactureSocieteView::VALUE_ORIGINES] as $drmid => $drmlibelle) {

                        $drmIdFormat = (strstr($drmlibelle, 'DRM')!==FALSE)? DRMClient::getInstance()->getLibelleFromId($drmlibelle) :
                        SV12Client::getInstance()->getLibelleFromId($drmlibelle);
                echo link_to($drmIdFormat, 'facture_redirect_to_doc', array('iddocument' => $drmid)) . "<br/>";
            }; ?></td>
                    <td><?php echoFloat($facture->value[FactureSocieteView::VALUE_TOTAL_TTC]); ?>&nbsp;€</td>
                    <td><?php
if ($fc->isRedressee($facture)) {
  echo 'redressée';
}else if ($fc->isRedressable($facture)) {
  echo link_to('défacturer les mouvements', '@defacturer?identifiant='.str_replace('FACTURE-', '',$facture->key[FactureSocieteView::KEYS_FACTURE_ID])); 
}
                    ?></td>
                </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</fieldset>
<?php endif; ?>