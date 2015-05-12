<?php

class documentActions extends sfActions {

    public function executeRedirectVisualisation(sfWebRequest $request) {
        $id_doc = $request->getParameter('id_doc');
        $this->forward404Unless($id_doc);
        if(preg_match('/^SV12/', $id_doc)){
            $this->redirect('sv12_redirect_to_visualisation', array('identifiant_sv12' =>$id_doc));
        }
        if(preg_match('/^DRM/', $id_doc)){
            $this->redirect('drm_redirect_to_visualisation', array('identifiant_drm' =>$id_doc));
        }
        if(preg_match('/^VRAC/', $id_doc)){
            $this->redirect('vrac_redirect_to_visualisation', array('identifiant_vrac' =>$id_doc));
        }
        throw new sfException("Le document d'id $id_doc n'est pas visualisable, il n'existe pas.");
    }

}

