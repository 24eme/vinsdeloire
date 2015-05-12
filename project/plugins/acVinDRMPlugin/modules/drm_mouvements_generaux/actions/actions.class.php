<?php

class drm_mouvements_generauxActions extends sfActions
{
	public function executeIndex(sfWebRequest $request) 
	{
		$this->drm = $this->getRoute()->getDRM();
        $this->form = new DRMMouvementsGenerauxProduitsForm($this->drm);
		$this->forms = array();
		$this->certifs = array();
		$this->certificationLibelle = array();
		$configuration = ConfigurationClient::getCurrent();
		foreach ($configuration->declaration->certifications as $certification_key => $certification_config) {
			if ($certification_config->hasProduit($this->drm->getInterpro()->get('_id'), $this->drm->getDepartement())) {
	            if (!isset($this->certifs[$certification_key])) {
					$this->certifs[$certification_key] = $certification_config->hasUniqProduit($this->drm->getInterpro()->get('_id'));
				}
	            if (!isset($this->forms[$certification_key])) {
					$this->forms[$certification_key] = array();
					$this->certificationLibelle[$certification_key] = $certification_config->libelle;
				}
				if ($this->drm->declaration->certifications->exist($certification_key)) {
	                $details = $this->drm->declaration->certifications->get($certification_key)->getProduitsDetails();
					foreach ($details as $detail) {
						$this->forms[$certification_key][] = new DRMMouvementsGenerauxProduitForm($detail);
					}
				}
			}
		}
        $this->first_certification = null;
        if(count($this->drm->declaration->certifications) > 0) {
            $this->first_certification = $this->drm->declaration->certifications->getFirst();
        }
        if ($request->isMethod(sfWebRequest::POST)) {

            if($this->drm->declaration->hasMouvementCheck()) {
        	   $this->drm->setCurrentEtapeRouting('recapitulatif');
        	   $this->redirect('drm_recap', $this->first_certification);
            } else {
               $this->drm->setCurrentEtapeRouting('declaratif');
               $this->redirect('drm_declaratif', $this->drm);
            }

        }
    }
    
    public function executeUpdateAjax(sfWebRequest $request) 
    {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->getResponse()->setContentType('text/json');
        $drm = $this->getRoute()->getDRM();
        $detail = $this->getRoute()->getObject();
        $this->forward404Unless(!$detail->hasMouvement());
		$form = new DRMMouvementsGenerauxProduitForm($this->getRoute()->getObject());
    	$form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
       		$form->save();
       		return $this->renderText(json_encode(array("success" => true,
            										   "document" => array("id" => $drm->get('_id'),
                										   				   "revision" => $drm->get('_rev'))
                									  )));
        }
        
        return $this->renderText(json_encode(array("success" => false)));
    }

    public function executeUpdateProduitsAjax(sfWebRequest $request) 
    {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->getResponse()->setContentType('text/json');
        $drm = $this->getRoute()->getDRM();
        $form = new DRMMouvementsGenerauxProduitsForm($drm);
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
            foreach($drm->declaration->getProduitsDetails() as $produit) {
            	$produit->pas_de_mouvement_check = ($form->getValue('pas_de_mouvement'))? 1 : 0;
            }
            $drm->save();
            return $this->renderText(json_encode(array("success" => true,
            										   "document" => array("id" => $drm->get('_id'),
                										   				   "revision" => $drm->get('_rev'))
                									  )));
        } 
        return $this->renderText(json_encode(array("success" => false)));
    }

    public function executeStockEpuise(sfWebRequest $request) 
    {
    	$drm = $this->getRoute()->getDRM();
    	$this->forward404Unless($drm->declaration->hasStockEpuise());
        foreach($drm->declaration->getProduitsDetails() as $produit) {
        	$produit->pas_de_mouvement_check = 1;
        }
        $drm->setCurrentEtapeRouting('declaratif');
        $this->redirect('drm_declaratif', $drm);
    }
    
    public function executeDeleteAjax(sfWebRequest $request) 
    {
        $this->forward404Unless($request->isXmlHttpRequest());
		$objectToDelete = $this->getRoute()->getObject()->cascadingDelete();
		$objectToDelete->delete();
		$this->getRoute()->getObject()->getDocument()->update();
		$this->getRoute()->getObject()->getDocument()->save();
        return sfView::NONE;
    }
    
    public function executeAjoutAjax(sfWebRequest $request) 
    {
        $this->forward404Unless($request->isXmlHttpRequest());
    	$drm = $this->getRoute()->getDRM();
        $certification_config = $this->getRoute()->getConfigCertification();

        $form = new DRMProduitAjoutForm($drm, $certification_config);
        if ($request->isMethod(sfWebRequest::POST)) {
    		$this->getResponse()->setContentType('text/json');
            $form->bind($request->getParameter($form->getName()));
			if ($form->isValid()) {
                $form->addProduit();
				$drm->save();
				$this->getUser()->setFlash("notice", 'Le produit a été ajouté avec succès.');
				return $this->renderText(json_encode(array("success" => true, "url" => $this->generateUrl('drm_mouvements_generaux', $drm))));
			} else {
				return $this->renderText(json_encode(array("success" => false, "content" => $this->getPartial('form', array('form' => $form, 'certification_config' => $certification_config)))));
			}
        }

        return $this->renderText($this->getPartial('popupAjout', array('form' => $form, 'certification_config' => $certification_config)));
    }
    
    public function executeAdd(sfWebRequest $request) 
    {
    	$drm = $this->getRoute()->getDRM();
    	$certification_config = $this->getRoute()->getConfigCertification();
    	$hash = $certification_config->hasUniqProduit($drm->getInterpro()->get('_id'));
    	$drm->addProduit($hash);
		$drm->save();
		$this->getUser()->setFlash("notice", 'Le produit a été ajouté avec succès.');
		$this->redirect('drm_mouvements_generaux', $drm);

    }
}
