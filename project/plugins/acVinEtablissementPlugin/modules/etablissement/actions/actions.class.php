<?php

class etablissementActions extends sfCredentialActions {

 public function executeAjout(sfWebRequest $request) {
        $this->societe = $this->getRoute()->getSociete();
        $this->applyRights();
        if(!$this->modification){
            $this->forward('acVinCompte','forbidden');
        }
        $this->etablissement = EtablissementClient::getInstance()->createEtablissementFromSociete($this->societe);
        $this->processFormEtablissement($request);        
        $this->setTemplate('modification');    
    }    
    
    public function executeModification(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->societe = $this->etablissement->getSociete();
        $this->applyRights();
        if(!$this->modification){
          $this->forward('acVinCompte','forbidden');
        }
        $this->processFormEtablissement($request);
    }
    
     protected function processFormEtablissement(sfWebRequest $request) {
        $this->etablissementModificationForm = new EtablissementModificationForm($this->etablissement);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->etablissementModificationForm->bind($request->getParameter($this->etablissementModificationForm->getName()));
            if ($this->etablissementModificationForm->isValid() ){
                $this->etablissementModificationForm->save();
                if($this->etablissementModificationForm->getValue('adresse_societe')){
                     $this->redirect('etablissement_visualisation', array('identifiant' => $this->etablissement->identifiant));
                }
                else{
                    $this->redirect('compte_coordonnee_modification', $this->etablissement->getMasterCompte());
                }
            }
        }
    }
    
    public function executeVisualisation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->societe = $this->etablissement->getSociete();
        $this->contact = $this->etablissement->getContact(); 
        $this->applyRights();
    }

}
