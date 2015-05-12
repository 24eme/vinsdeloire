<?php

class drm_vrac_detailsActions extends sfActions
{
    
    public function executeProduit(sfWebRequest $request) {
        
        $this->detail = $this->getRoute()->getDRMDetail();
        $this->drm = $this->detail->getDocument();
        $this->form = new DRMDetailVracForm($this->detail->sorties->vrac_details);
        if ($request->isMethod(sfRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            
            if($this->form->isValid()) {
                $this->form->update();
                $this->drm->update();
                $this->drm->save();
                
                if($request->isXmlHttpRequest())
                {
                    $this->getUser()->setFlash("notice", 'Le détail des vracs a été mis à jour avec success.');                    
                    return $this->renderText(json_encode(array("success" => true, "type" => "sortie_vrac", "volume" => $this->detail->sorties->vrac, "document" => array("id" => $this->drm->get('_id'),"revision" => $this->drm->get('_rev')))));                  
                }

                return $this->redirect('drm_edition_detail', $this->detail);
            }
            if($request->isXmlHttpRequest())
            {
                return $this->renderText(json_encode(array('success' => false ,'content' => $this->getPartial('formContent', array('form' => $this->form, 'detail' => $this->detail)))));
                
            }
        }
    }
}