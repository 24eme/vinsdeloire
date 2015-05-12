<?php use_helper('Float'); ?>

<?php if(sizeof($list) > 0): ?>
<fieldset id="history_sv12">
    <legend>Déclaration SV12 en cours de Saisie</legend>
        <table class="table_recap">
        <thead>
        <tr>
            <th>Campagne</th>
            <th>Etat</th>
            <th>Volume total raisins</th>
            <th>Volume total moûts</th>
            <th>Total</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?> 
            <tr>
                <td>
                    <?php if(in_array($item->valide->statut, array(SV12Client::STATUT_VALIDE, SV12Client::STATUT_VALIDE_PARTIEL))): ?>
                        <a href="<?php echo url_for(array('sf_route' => 'sv12_visualisation', 'identifiant' => $item->identifiant, 'periode_version' => SV12Client::getInstance()->buildPeriodeAndVersion($item->periode, $item->version))) ?>"><?php echo sprintf("%s(-%s)", $item->campagne, ($item->version) ? $item->version : 'M00'); ?></a>
                    <?php else: ?>
                        <a href="<?php echo url_for(array('sf_route' => 'sv12_update', 'identifiant' => $item->identifiant, 'periode_version' => SV12Client::getInstance()->buildPeriodeAndVersion($item->periode, $item->version))) ?>"><?php echo sprintf("%s(-%s)", $item->campagne, ($item->version) ? $item->version : 'M00'); ?></a>
                    <?php endif; ?> 
                </td>
                <td>
                    <?php if($item->valide->statut == SV12Client::STATUT_VALIDE): ?>
                        <p><span class="statut_valide">Validé</span></p>
                    <?php elseif($item->valide->statut == SV12Client::STATUT_VALIDE_PARTIEL): ?>
                        <p><span class="statut_valide">Validé&nbsp;partiellement</span></p>
                    <?php else: ?>
                        <p><span class="statut_suspendu">Brouillon</span></p>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echoFloat($item->totaux->volume_raisins) ?>
                </td>
                <td>
                    <?php echoFloat($item->totaux->volume_mouts) ?>
                </td>
                <td>
                    <?php echoFloat($item->totaux->volume_raisins + $item->totaux->volume_mouts + $item->totaux->volume_ecarts) ?>
                </td>
                <td>
                    <?php if(in_array($item->valide->statut, array(SV12Client::STATUT_VALIDE, SV12Client::STATUT_VALIDE_PARTIEL))): ?>
                        <a class="btn_majeur btn_noir" href="<?php echo url_for(array('sf_route' => 'sv12_visualisation', 'identifiant' => $item->identifiant, 'periode_version' => SV12Client::getInstance()->buildPeriodeAndVersion($item->periode, $item->version))) ?>">Visualiser</a>
                    <?php else: ?>
                        <a class="btn_majeur btn_vert" href="<?php echo url_for(array('sf_route' => 'sv12_update', 'identifiant' => $item->identifiant, 'periode_version' => SV12Client::getInstance()->buildPeriodeAndVersion($item->periode, $item->version))) ?>">Continuer</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>   
        </tbody>
        </table>
</fieldset>
<?php endif; ?>