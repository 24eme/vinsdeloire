<?php

class drm_editionActions extends sfActions
{
    
    public function executeIndex(sfWebRequest $request) {
        $this->init();
        $this->setTemplate('index');
    }
    
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
                $this->redirect('drm_edition', $this->config_lieu);
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
     				"content" => $this->getComponent('drm_edition', 'itemForm', array('config' => $this->config, 'detail' => $detail, 'active' => false)),
                    "produit" => array("old_hash" => $detail->getCepage()->getHash(), "hash" => $detail->getHash(), "libelle" => sprintf("%s (%s)", $detail->getLibelle("%g% %a% %m% %l% %co% %ce%"), $detail->getCepage()->getConfig()->getCodeProduit())),
     				"document" => array("id" => $this->drm->get('_id'),
                	"revision" => $this->drm->get('_rev'))
     			)));
     		} else {
     			$this->redirect('drm_edition', $this->drm);
     		}
     	}

     	if ($request->isXmlHttpRequest()) {
     		return $this->renderText(json_encode(array(
     				"success" => false,
     				"content" => ""
     		)));
        } else {
            $this->redirect('drm_edition', $this->drm);
        }
    }
    
    protected function init() {
        $this->form = null;
        $this->detail = null;
        $this->drm = $this->getRoute()->getDRM();
        $this->config = $this->drm->declaration->getConfig();
        $this->details = $this->drm->declaration->getProduitsDetails();
    }

    public function executeAddLabel(sfWebRequest $request) 
    {
      $this->detail = $this->getRoute()->getDRMDetail();
      $this->drm = $this->getRoute()->getDRM();
      
      $this->form = new DRMProduitLabelForm($this->detail);      
      if ($request->isMethod(sfRequest::POST)) {
          $this->form->bind($request->getParameter($this->form->getName()));
          if($this->form->isValid()) {              
                $this->form->save();
                if($request->isXmlHttpRequest())
                {
                    $this->getUser()->setFlash("notice", 'Les labels ont étés mis à jour avec success.'); 
                    //return $this->renderPartial('labelsList', array('form' => $this->form));
                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drm->get('_id'),"revision" => $this->drm->get('_rev')), 'content' => $this->form->getObject()->getLabelsLibelle())));                  
                }
            }
            if($request->isXmlHttpRequest())
            {
                $this->getUser()->setFlash("notice", 'Echec lors de la mis à jour des labels');
                return $this->renderText(json_encode(array('success' => false ,'document' => array("id" => $this->drm->get('_id'),"revision" => $this->drm->get('_rev')))));
            }
         }
    }
}
