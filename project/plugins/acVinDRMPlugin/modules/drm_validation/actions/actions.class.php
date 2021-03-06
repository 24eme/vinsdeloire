
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of actions
 *
 * @author mathurin
 */
class drm_validationActions extends drmGeneriqueActions {

  public function executeValidation(sfWebRequest $request) {
      set_time_limit(180);
      $this->drm = $this->getRoute()->getDRM();
      $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
      $this->initSocieteAndEtablissementPrincipal();
      $this->mouvements = $this->drm->getMouvementsCalculeByIdentifiant($this->drm->identifiant);
      $this->mouvementsByProduit = DRMClient::getInstance()->sortMouvementsForDRM($this->mouvements);
      $this->recapCvo = DRMClient::recapCvo($this->mouvements);

      $this->drm->cleanDeclaration();
      $this->initDeleteForm();
      if ($this->isTeledeclarationMode) {
          $this->validationCoordonneesSocieteForm = new DRMValidationCoordonneesSocieteForm($this->drm);
          $this->validationCoordonneesEtablissementForm = new DRMValidationCoordonneesEtablissementForm($this->drm);
          $this->drm->generateDroitsDouanes();

      } else {
          $this->formCampagne = new DRMEtablissementCampagneForm($this->drm->identifiant, $this->drm->campagne,$this->isTeledeclarationMode);
      }
      $this->no_link = false;
      if ($this->getUser()->hasOnlyCredentialDRM()) {
          $this->no_link = true;
      }

      $this->validation = new DRMValidation($this->drm, $this->isTeledeclarationMode);
      $this->produits = array();
      foreach ($this->drm->getProduits() as $produit) {
          $d = new stdClass();
          $d->version = $this->drm->version;
          $d->periode = $this->drm->periode;
          $d->produit_hash = $produit->getHash();
          $d->produit_libelle = $produit->getLibelle();
          $d->total_debut_mois = $produit->total_debut_mois;
          $d->total_entrees = $produit->total_entrees;
          $d->total_sorties = $produit->total_sorties;
          $d->total = $produit->total;
          $d->total_facturable = $produit->total_facturable;
          $this->produits[] = $d;
      }

      $this->isUsurpationMode = $this->isUsurpationMode();
      $this->form = new DRMValidationCommentaireForm($this->drm);

      if (!$request->isMethod(sfWebRequest::POST)) {

          return sfView::SUCCESS;
      }

      $this->form->bind($request->getParameter($this->form->getName()));
      if ($request->getParameter('brouillon')) {
          $this->form->save();
          return $this->redirect('drm_etablissement', $this->drm->getEtablissement());
      }

      if (!$this->validation->isValide()) {
          return sfView::SUCCESS;
      }
      $this->form->save();
      $this->drm->validate(array('isTeledeclarationMode' => $this->isTeledeclarationMode));
      $this->drm->save();

      $this->drm->updateVracs();

      if(!$this->isUsurpationMode() && $this->isTeledeclarationMode){
          $mailManager = new DRMEmailManager($this->getMailer(), $this->getUser());
          $mailManager->setDRM($this->drm);
          $mailManager->sendMailValidation();
      }

      DRMClient::getInstance()->generateVersionCascade($this->drm);
      if(!$this->isUsurpationMode() && $this->isTeledeclarationMode){
          if ($this->drm->hasFactureEmail()) {
              $this->transmissionFactureMail();
          }
      }

      if ($this->form->getValue('transmission_ciel')) {
          $this->redirect('drm_transmission', array('identifiant' => $this->drm->identifiant,'periode_version' => $this->drm->getPeriodeAndVersion()));
      }

      if(!$this->isUsurpationMode() && $this->isTeledeclarationMode){
          if (!$this->drm->hasFactureEmail()) {
              $this->redirect('drm_confirmation', array('identifiant' => $this->drm->identifiant, 'periode_version' => $this->drm->getPeriodeAndVersion()));
          }
      }

      $this->redirect('drm_visualisation', array('identifiant' => $this->drm->identifiant, 'periode_version' => $this->drm->getPeriodeAndVersion()));
  }

    public function executeConfirmation(sfWebRequest $request)
    {
        $this->drm = $this->getRoute()->getDRM();
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        if ($this->getUser()->hasTeledeclarationFactureEmail()) {
            $this->redirect('drm_visualisation', array('identifiant' => $this->drm->identifiant, 'periode_version' => $this->drm->getPeriodeAndVersion()));
        }
        if (!$this->getUser()->hasTeledeclarationFacture()) {
            $this->redirect('drm_visualisation', array('identifiant' => $this->drm->identifiant, 'periode_version' => $this->drm->getPeriodeAndVersion()));
        }

        $this->form = new DRMFactureEmailForm($this->drm);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->transmissionFactureMail();
                $this->redirect('drm_visualisation', $this->drm);
            }
        }
    }

    public function executeUpdateEtablissement(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->initSocieteAndEtablissementPrincipal();
        $this->form = new DRMValidationCoordonneesEtablissementForm($this->drm);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $diff = $this->form->getDiff();
                $this->form->save();
                $mailManager = new DRMEmailManager($this->getMailer(),$this->getUser());
                $mailManager->setDRM($this->drm);
                $mailManager->sendMailCoordonneesOperateurChanged(CompteClient::TYPE_COMPTE_ETABLISSEMENT, $diff);
                $this->redirect('drm_validation', $this->drm);
            }
        }
    }

    public function executeUpdateSociete(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->initSocieteAndEtablissementPrincipal();
        $this->form = new DRMValidationCoordonneesSocieteForm($this->drm);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $diff = $this->form->getDiff();
                $this->form->save();
                $mailManager = new DRMEmailManager($this->getMailer(), $this->getUser());
                $mailManager->setDRM($this->drm);
                $mailManager->sendMailCoordonneesOperateurChanged(CompteClient::TYPE_COMPTE_SOCIETE, $diff);
                $this->redirect('drm_validation', $this->drm);
            }
        }
    }

    private function transmissionFactureMail(){
      $date_facturation = date('Y-m-d');
      $mouvementsBySoc = array();
      $etablissementDRM = $this->drm->getEtablissement();
      $generation = FactureClient::getInstance()->createFacturesBySoc($mouvementsBySoc, $date_facturation, $message_communication);
      $generation->type_document = GenerationClient::TYPE_DOCUMENT_FACTURES_DRM;
      $generation->add('arguments')->add('regions', $etablissementDRM->region);
      $generation->add('arguments')->add('drmid', $this->drm->_id);
      if($seuil = sfConfig::get('app_facture_seuil_facturation',null)){
        $generation->add('arguments')->add('seuil', $seuil);
      }
      $generation->add('arguments')->add('date_facturation',  $this->drm->valide->date_saisie);
      $generation->save();
    }
}
