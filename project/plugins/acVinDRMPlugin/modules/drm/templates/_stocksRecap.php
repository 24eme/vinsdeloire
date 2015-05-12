<?php use_helper('Float'); ?>
<?php if(count($recaps) > 0): ?>
<table class="table_recap">
    <thead>
        <tr>
            <th>Produit</th>
   <th><?php echo ucfirst(preg_replace('/ /', '&nbsp;', $periode_debut)); ?><br/>(DS)</th>
            <th>Entrées</th>
            <th>dont Rev.<br/>(ODG)</th>
            <th>Sorties<br/>(Fact.)</th>
	    <th><?php echo ucfirst(preg_replace('/ /', '&nbsp;', $periode_fin)); ?><br/>(DS)</th>
            <th>dont Com.</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($recaps->getRawValue(ESC_RAW) as $recap): ?>
            <tr>
                <td><a class="lien_hamza_style" data-scrollto="#hamza_mouvement" href="#filtre=<?php echo str_replace(' ', '_', $recap['produit']); ?>"><?php echo $recap['produit'] ?></a></td>
                <td>
                    <?php echoFloat($recap['volume_stock_debut']) ?><br /><?php if($recap['volume_stock_debut_ds'] !== null): ?>(<?php echoFloat($recap['volume_stock_debut_ds']) ?>)<?php else: ?>(Abs.)<?php endif; ?>    
                </td>
                <td><?php echoFloat($recap['volume_entrees']) ?></td>
                <td>
                    <?php echoFloat($recap['volume_recolte']) ?><br /><?php if($recap['volume_revendique_drev'] !== null): ?>(<?php echoFloat($recap['volume_revendique_drev']) ?>)<?php else: ?>(Abs.)<?php endif; ?>
                </td>
                <td><?php echoFloat($recap['volume_sorties']) ?><br />(<?php echoFloat($recap['volume_facturable']) ?>)</td>
                <td>
                    <?php echoFloat($recap['volume_stock_fin']) ?><br /><?php if($recap['volume_stock_fin_ds'] !== null): ?>(<?php echoFloat($recap['volume_stock_fin_ds']) ?>)<?php else: ?>(Abs.)<?php endif; ?> 
                </td>
                <td><?php echoFloat($recap['volume_stock_commercialisable']) ?></td>
            </tr>
        <?php endforeach; ?>
</table>
<?php else: ?>
Pas de stock
<?php endif; ?>