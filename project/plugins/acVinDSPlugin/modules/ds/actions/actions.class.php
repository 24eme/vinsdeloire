<?php
class dsActions extends sfActions {
    
  public function executeIndex(sfWebRequest $request) {
      
    $this->form = new DSEtablissementChoiceForm('INTERPRO-inter-loire');
    $this->generations = GenerationClient::getInstance()->findHistoryWithType(GenerationClient::TYPE_DOCUMENT_DS,10);
      $this->generationForm = new DSGenerationForm();
       if ($request->isMethod(sfWebRequest::POST)) {
	 $this->form->bind($request->getParameter($this->form->getName()));
	 if ($this->form->isValid()) {
	   return $this->redirect('ds_etablissement', $this->form->getEtablissement());
	 }
       }
    }
    
     public function executeGeneration(sfWebRequest $request) {
       $this->generationForm = new DSGenerationForm();
       if ($request->isMethod(sfWebRequest::POST)) {
	 $this->generationForm->bind($request->getParameter($this->generationForm->getName()));
	 if ($this->generationForm->isValid()) {
	   $values = $this->generationForm->getValues();
	   $generation = new Generation();
	   $generation->arguments->add('regions', implode(',', array_values($values['regions'])));
	   $generation->arguments->add('operateur_types', implode(',', array_values($values['operateur_types'])));
	   $generation->arguments->add('date_declaration', $values['date_declaration']);
	   $generation->type_document = GenerationClient::TYPE_DOCUMENT_DS;
	   $generation->save();
	   return $this->redirect('generation_view', array('type_document' => $generation->type_document, 'date_emission' => $generation->date_emission));
	 }
       }
       $this->setTemplate('index');
    }
    
     public function executeMonEspace(sfWebRequest $request) {    
         
        $this->etablissement = $this->getRoute()->getEtablissement();        
        $this->dsHistorique = DSClient::getInstance()->getHistoryByOperateur($this->etablissement);
        $this->generationOperateurForm = new DSGenerationOperateurForm();
        
        if ($request->isMethod(sfWebRequest::POST)) {
	 $this->generationOperateurForm->bind($request->getParameter($this->generationOperateurForm->getName()));
	 if ($this->generationOperateurForm->isValid()) {
           $values = $this->generationOperateurForm->getValues();
           $date = $values["date_declaration"];
	   try {
	     $declarationDs = DSClient::getInstance()->findOrCreateDsByEtbId($this->etablissement->identifiant, $date);     
	     $declarationDs->save();
	   }catch(sfException $e) {
	     $this->getUser()->setFlash('global_error', $e->getMessage());
	     $this->redirect('ds_etablissement', $this->etablissement);
	   }
           $this->redirect('ds_generation_operateur', array('identifiant' => $declarationDs->identifiant, 'periode' => $declarationDs->periode));
	 }
       }
    }
    
    
     public function executeGenerationOperateur(sfWebRequest $request) { 
        $this->ds = $this->getRoute()->getDS();
        $this->etablissement = EtablissementClient::getInstance()->retrieveById($this->ds->identifiant);
        $generation = new Generation();
	$generation->arguments->add('date_declaration', $this->ds->date_stock);
	$generation->type_document = 'DS';
        $generation->add('documents')->add(0, $this->ds->_id);
	$generation->save();
        return $this->redirect('generation_view', array('type_document' => $generation->type_document,'date_emission' => $generation->date_emission,'identifiant' => $this->etablissement->identifiant));
      
    }
    
     public function executeEditionDS(sfWebRequest $request) {        
         $this->ds = $this->getRoute()->getDS();
	 $this->ds->updateProduits();
         $this->form = new DSEditionForm($this->ds);
         if ($request->isMethod(sfWebRequest::POST)) {
             $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doUpdateObject();
                $this->ds->save();
		if ($request->getParameter('addproduit'))
		  return $this->redirect('ds_edition_operateur_addProduit', $this->ds);
                return $this->redirect('ds_edition_operateur_validation_visualisation', $this->ds);
            }
       }
    } 
    
    public function executeEditionDSAddProduit(sfWebRequest $request)
    {
        $this->ds = $this->getRoute()->getDS();
        $this->form = new DSEditionAddProduitForm($this->ds);
         if ($request->isMethod(sfWebRequest::POST)) {
             $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $produit = $this->form->addProduit();
                $this->ds->save();
                return $this->redirect('ds_edition_operateur', $this->ds);
            }
       }
    }
    
    public function executeEditionDSValidationVisualisation(sfWebRequest $request) {
        $this->ds = $this->getRoute()->getDS();
	$this->ds->updateProduits(); 
	$this->validation = new DSValidation($this->ds);
	if ($request->isMethod(sfWebRequest::POST)) {
	  $this->ds->updateStatut();
	  $this->ds->updateProduits();
	  $this->ds->save();
	  return $this->redirect('ds_etablissement', array('identifiant' => $this->ds->identifiant));
	}
    }

    public function executeLatex(sfWebRequest $request) {
        
        $this->setLayout(false);
        $this->ds = $this->getRoute()->getDS();
        $this->forward404Unless($this->ds);
	$latex = new DSLatex($this->ds);
        //$latex->echoFactureWithHTTPHeader(DSLatex::DS_OUTPUT_TYPE_LATEX);
	$latex->echoWithHTTPHeader($request->getParameter('type'));
        exit;
    }
    
    public function executeHistoriqueGeneration(sfWebRequest $request) {
        return $this->redirect('ds_edition_operateur_validation_visualisation', $this->ds);
    }
    
}
