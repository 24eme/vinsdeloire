<?php

class drm_recapActions extends sfActions
{
    
    public function executeIndex(sfWebRequest $request) {
        $this->init();
        $this->setTemplate('index');
    }

    
    /*public function executeRedirectIndex(sfWebRequest $request) {
    	$drm = $this->getRoute()->getDRM();
    	$first_certification = null;
        if(count($drm->declaration->certifications) > 0) {
            $first_certification = $drm->declaration->certifications->getFirst();
        }
        $this->redirect('drm_recap', $first_certification);
    }
    
    public function executeLieuAjoutAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->getResponse()->setContentType('text/json');
        $this->certification = $this->getRoute()->getObject();
        $this->drm = $this->getRoute()->getDRM();

        $this->form = new DRMLieuAjoutForm($this->drm, $this->certification->getConfig());
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $lieu = $this->form->addLieu();
                return $this->renderText(json_encode(array("success" => true,
                                                           "ajax" => true,
                                                           "url" => $this->generateUrl('drm_recap_ajout_ajax', $lieu))));
            }

            return $this->renderText(json_encode(array("success" => false, "content" => $this->getPartial('drm_recap/formLieuAjout', array('certification' => $this->certification, 'form' => $this->form)))));
        }
		
        return $this->renderText($this->getPartial('drm_recap/popupLieuAjout', array('certification' => $this->certification, 'form' => $this->form)));
    }
    
    public function executeLieu(sfWebRequest $request) {
        $this->init();
        $this->setTemplate('index');
    }

    public function executeAjoutAjax(sfWebRequest $request) 
    {
        $this->drm = $this->getRoute()->getDRM();
        $this->config_lieu = $this->getRoute()->getConfigLieu();
        $this->drm_lieu = $this->getRoute()->getDRMLieu();

        $this->forward404Unless($request->isXmlHttpRequest());

        $form = new DRMProduitAjoutForm($this->drm,
                                        $this->config_lieu);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->getResponse()->setContentType('text/json');
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                $form->addProduit();
                $this->drm->save();
                $this->getUser()->setFlash("notice", 'Le produit a été ajouté avec succès.');
                return $this->renderText(json_encode(array("success" => true, 
                                                           "url" => $this->generateUrl('drm_recap_lieu', $this->drm_lieu))));
            }
            return $this->renderText(json_encode(array("success" => false, 
                                                       "content" => $this->getPartial('formAjout', array('form' => $form, 'drm_lieu' => $this->drm_lieu)))));
        }
        return $this->renderPartial('popupAjout', array('form' => $form, 'drm_lieu' => $this->drm_lieu));
    }*/
    
    public function executeDetail(sfWebRequest $request) {
        $this->init();
        $this->detail = $this->getRoute()->getDRMDetail();
        $this->setTemplate('index');
    }
    
    public function executeUpdate(sfWebRequest $request) {
        $this->init();
  
        $this->form = new DRMDetailForm($this->getRoute()->getDRMDetail());
        $this->form->bind($request->getParameter($this->form->getName()));
        
        if($this->form->isValid()) {
        	$this->form->save();
            if ($request->isXmlHttpRequest()) {
				         		
                return $this->renderText(json_encode(array(
                	"success" => true,
                	"content" => "",
                	"document" => array("id" => $this->drm->get('_id'),
                	"revision" => $this->drm->get('_rev'))
                	)));
            } else {
                $this->redirect('drm_recap_lieu', $this->config_lieu);
            }
        }
        
        if ($request->isXmlHttpRequest()) {
            return $this->renderText(json_encode(array("success" => false, "content" => $this->getPartial('drm_recap/itemFormErrors', array('form' => $this->form)))));
        } else {
            $this->setTemplate('index');
        }
    }

    public function executeProduitAjout(sfWebRequest $request) {
    	$this->init();

    	$this->form = new DRMProduitForm($this->drm, $this->drm->declaration->getConfig());

     	$this->form->bind($request->getParameter($this->form->getName()));

     	if ($this->form->isValid()) {
     		$detail = $this->form->addProduit();	
     		$this->drm->save();
     		if ($request->isXmlHttpRequest()) {
     			return $this->renderText(json_encode(array(
     				"success" => true,
     				"content" => $this->getComponent('drm_recap', 'itemForm', array('config' => $this->config, 'detail' => $detail, 'active' => true)),
     				"document" => array("id" => $this->drm->get('_id'),
                	"revision" => $this->drm->get('_rev'))
     			)));
     		} else {
     			$this->redirect('drm_recap_lieu', $this->drm_lieu);
     		}
     	}

     	if ($request->isXmlHttpRequest()) {
     		return $this->renderText(json_encode(array(
     				"success" => false,
     				"content" => ""
     		)));
        } else {
            $this->redirect('drm_recap_lieu', $this->drm_lieu);
        }
    }
    
    protected function init() {
        $this->form = null;
        $this->detail = null;
        $this->drm = $this->getRoute()->getDRM();
        $this->config = $this->drm->declaration->getConfig();
        $this->produits = $this->drm->declaration->getProduitsDetails();
        /*$this->previous = $this->drm_lieu->getPreviousSisterWithMouvementCheck();
        $this->next = $this->drm_lieu->getNextSisterWithMouvementCheck();
    	$this->previous_certif = $this->drm_lieu->getCertification()->getPreviousSisterWithMouvementCheck();
    	$this->next_certif = $this->drm_lieu->getCertification()->getNextSisterWithMouvementCheck();

    	$this->redirectIfNoMouvementCheck();*/
    }

    protected function redirectIfNoMouvementCheck() {    	
    	if (!$this->drm_lieu->hasMouvementCheck()) {
	    	if ($this->next) {
	        	$this->redirect('drm_recap_lieu', $this->next);
	        } elseif (!$this->next && $this->next_certif) {
	        	$this->redirect('drm_recap', $this->next_certif);
	        } else  {
	        	$this->redirect('drm_vrac', $this->drm);
	        }
    	}
    }
}
