<?php use_helper('Float'); ?>
<fieldset id="recapitulatif_sv12">
        <table class="table_recap">
        <thead>
        <tr>
            <th>Produits</th>
            <th>Volume de raisins</th>
            <th>Volume de moûts</th>
            <th>Total</th>                        
        </tr>
        </thead>
        <tbody>
            <?php foreach ($sv12->totaux->produits as $libelle => $produit) :  ?>
            <tr>
                <td>
                    <?php echo $libelle; ?>
                </td>
                <td>
                    <?php echoFloat($produit->volume_raisins).' hl'; ?>
                </td>

                <td>
                    <?php echoFloat($produit->volume_mouts).' hl'; ?>
                </td>

                <td>     
                    <?php echoFloat($produit->volume_raisins + $produit->volume_mouts + $produit->volume_ecarts).' hl'; ?>
                </td>
            </tr>
            <?php
            endforeach;
            ?>
            <tr>
                <td style="font-weight:bold;">Total</td>
                <td style="font-weight:bold;">
                    <?php echoFloat($sv12->totaux->volume_raisins).' hl'; ?>
                </td>
                <td style="font-weight:bold;">
                    <?php echoFloat($sv12->totaux->volume_mouts).' hl'; ?>
                </td>
                <td style="font-weight:bold;">
                    <?php echoFloat($sv12->volume_total).' hl'; ?>
                </td>
            </tr>
        </tbody>
        </table> 
</fieldset>