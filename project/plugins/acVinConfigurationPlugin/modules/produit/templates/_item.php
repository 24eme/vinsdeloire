<tr>
	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getFormatLibelleDefinitionNoeud()->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo $produit->getLibelleFormat() ?>
		</a>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getCertification(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getGenre(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getAppellation(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getMention(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getLieu(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getCouleur(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('produit' => $produit, 'noeud' => $produit->getCepage(), 'cvo' => $cvo)) ?>
	</td>
   	<td class="center">
		<strong title="<?php echo $cvo->date ?>"><?php echo (!is_null($cvo)) ? $cvo->taux : null ?></strong>
	</td>
	<td class="center">
		<strong><?php echo (!is_null($douane)) ? $douane->taux : null ?></strong>
	</td>
	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo ($produit->getCodeProduit()) ? sprintf("%04d", $produit->getCodeProduit()) : "(Aucun)" ?>
		</a>
	</td>

	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo ($produit->getCodeDouane()) ? str_replace('_', ' ', $produit->getCodeDouane()) : "(Aucun)" ?>
		</a>
	</td>
	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo ($produit->getCodeComptable()) ? $produit->getCodeComptable() : "(Aucun)" ?>
		</a>
	</td>
</tr>