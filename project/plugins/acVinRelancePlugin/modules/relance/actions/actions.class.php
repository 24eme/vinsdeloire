<?php
class relanceActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->form = new RelanceEtablissementChoiceForm('INTERPRO-inter-loire');
        $this->generationForm = new RelanceGenerationMasseForm();
        $this->generations = GenerationClient::getInstance()->findHistoryWithType(GenerationClient::TYPE_DOCUMENT_RELANCE,10);
         if ($request->isMethod(sfWebRequest::POST)) {
	 $this->form->bind($request->getParameter($this->form->getName()));
	 if ($this->form->isValid()) {
	  return $this->redirect('relance_etablissement', $this->form->getEtablissement());
          }
         }
    }

    public function executeMonEspace(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->relances = RelanceEtablissementView::getInstance()->findByEtablissement($this->etablissement); 
        $this->alertes = AlerteRelanceView::getInstance()->getRechercheByEtablissementAndStatut($this->etablissement->identifiant, AlerteClient::STATUT_A_RELANCER);
    }
    
    public function executeGenererEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->alertes_relance = AlerteRelanceView::getInstance()->getRechercheByEtablissementAndStatutSorted($this->etablissement->identifiant, AlerteClient::STATUT_A_RELANCER);
        if(count($this->alertes_relance)){
            $generation = RelanceClient::getInstance()->createRelancesByEtb($this->alertes_relance, $this->etablissement);
            $generation->save();
        }
         $this->redirect('relance_etablissement', $this->etablissement);
    }
    
    
   public function executeLatex(sfWebRequest $request) {
        $this->relance = $this->getRoute()->getRelance();
        $this->forward404Unless($this->relance);
	$latex = new RelanceLatex($this->relance);
	$latex->echoWithHTTPHeader($request->getParameter('type'));
    }

    
    public function executeGeneration(sfWebRequest $request) {
       $this->generationForm = new RelanceGenerationMasseForm();
       if ($request->isMethod(sfWebRequest::POST)) {
	 $this->generationForm->bind($request->getParameter($this->generationForm->getName()));
         $values = $this->generationForm->getValues();
         if ($this->generationForm->isValid()) {
	  $generation = new Generation();
           
          $date_relance = DATE::getIsoDateFromFrenchDate($values['date_relance']);
          $generation->arguments->add('types_relance', implode(',', array_values($values['types_relance'])));          
          $generation->arguments->add('date_relance', $date_relance);
          $generation->type_document = GenerationClient::TYPE_DOCUMENT_RELANCE;
          $generation->save();
	 }
       }
       return $this->redirect('generation_view', array('type_document' => $generation->type_document,'date_emission' => $generation->date_emission));

    }

}
