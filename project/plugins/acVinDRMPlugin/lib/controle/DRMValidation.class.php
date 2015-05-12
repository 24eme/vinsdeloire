<?php
class DRMValidation extends DocumentValidation
{
	public function configure() {
		$this->addControle('erreur', 'repli', "La somme des replis en entrée et en sortie n'est pas la même");
		$this->addControle('erreur', 'vrac_detail_nonsolde', "Le contrat est soldé (ou annulé)");
		$this->addControle('erreur', 'vrac_detail_exist', "Le contrat n'existe plus");
		
		$this->addControle('vigilance', 'total_negatif', "Le stock revendiqué théorique fin de mois est négatif");
		$this->addControle('vigilance', 'vrac_detail_negatif', "Le volume qui sera enlevé sur le contrat est supérieur au volume restant");
	}

	public function controle()
	{
		$total_entrees_replis = 0;
		$total_sorties_replis = 0;
	
		foreach ($this->document->getProduitsDetails() as $detail) {
			$total_entrees_replis += $detail->entrees->repli;
			$total_sorties_replis += $detail->sorties->repli;

			if($detail->total < 0) {
				$this->addPoint('vigilance', 'total_negatif', $detail->getLibelle(), $this->generateUrl('drm_edition_detail', $detail));
			}
		}

		$volumes_restant = array();
		foreach ($this->document->getMouvementsCalculeByIdentifiant($this->document->identifiant) as $mouvement) {
		  if ($mouvement->isVrac()) {
		    $vrac = $mouvement->getVrac();
		      if(!$vrac) {
			$this->addPoint('erreur', 'vrac_detail_exist', sprintf("%s, Contrat n°%s avec %s", $mouvement->produit_libelle, $mouvement->detail_libelle, $mouvement->vrac_destinataire), $this->generateUrl('drm_edition_detail', $detail));
			continue;
		      }

		    if ($vrac->valide->statut != VracClient::STATUS_CONTRAT_NONSOLDE) {
		      $this->addPoint('erreur', 'vrac_detail_nonsolde', sprintf("Contrat %s", $mouvement->produit_libelle, $vrac->__toString()), $this->generateUrl('vrac_visualisation', $vrac));
		      continue;
		    }
		    $id_volume_restant = $mouvement->produit_hash.$mouvement->vrac_numero;
		    if (!isset($volumes_restant[$id_volume_restant])) {
		      $volumes_restant[$id_volume_restant]['volume'] = $vrac->volume_propose - $vrac->volume_enleve;
		      $volumes_restant[$id_volume_restant]['vrac'] = $vrac;
		    }
		    $volumes_restant[$id_volume_restant]['volume'] +=  $mouvement->volume;
		  }
		}

		foreach ($volumes_restant as $is => $restant) {
		    if($restant['volume'] < 0) {
		      $vrac = $restant['vrac'];
		      $this->addPoint('vigilance', 
				      'vrac_detail_negatif', 
				      sprintf("%s, Contrat %s (%01.02f hl enlevé / %01.02f hl proposé)", 
					      $volumes_restant[$id_volume_restant]['vrac']->produit_libelle, 
					      $vrac->__toString(), 
					      $vrac->volume_propose - $restant['volume'],
					      $vrac->volume_propose), 
				      $this->generateUrl('drm_edition', $this->document));
		    }
		}

		if (round($total_entrees_replis, 2) != round($total_sorties_replis, 2)) {
			$this->addPoint('erreur', 'repli', $detail->getLibelle(), $this->generateUrl('drm_edition', $this->document));
		}
	}
}