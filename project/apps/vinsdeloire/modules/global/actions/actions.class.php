<?php

class globalActions extends sfActions {

    public function executeError500(sfWebRequest $request) {
        $this->exception = $request->getParameter('exception');
        if (sfConfig::get('app_auth_mode') != 'HTTP_AD') {
            $this->setTemplate('error500Teledeclaration', 'global');
        }
        $this->getResponse()->setStatusCode(500);
    }

    public function executeError404(sfWebRequest $request) {
        $this->exception = $request->getParameter('exception');
    }

    public function executeHome(sfWebRequest $request) {

        if ($this->getUser()->hasCredential('transactions')) {
            return $this->redirect('vrac');
        }

	if ($this->getUser()->hasCredential('teledeclaration_drev_admin')) {
            return $this->redirect('/odg/declaration');
        }

        if ($this->getUser()->hasCredential('drm')) {
            return $this->redirect('drm');
        }

        if ($this->getUser()->hasObservatoire() && !$this->getUser()->hasTeledeclarationVrac() && !$this->getUser()->hasTeledeclarationDrm()) {
            $this->redirect(sfConfig::get('app_observatoire_url'));
        }


        if (!$this->getUser()->hasCredential('operateur')) {

            return $this->redirect('compte_teledeclarant_mon_espace', array("identifiant" => $this->getUser()->getCompte()->identifiant));
        }

        return $this->redirect('societe');
    }

    public function executeHeader(sfWebRequest $request) {
        if (strpos("::1,127.0.0.1,".sfConfig::get('app_header_ip_restriction', ''), $_SERVER['REMOTE_ADDR']) === false)
        {
            return $this->forwardSecure();
        }
        $compte = null;
        $droits = array();
        $compteDroits = array("admin");
        $societe = null;
        if($request->getParameter('compte_id') && $compte = CompteClient::getInstance()->find($request->getParameter('compte_id'))) {
	    if($compte && $compte->exist('droits')) {
            $compteDroits = $compte->droits->toArray(true ,false);
            $societe = $compte->getSociete();

		}
        }
        foreach ($compteDroits as $droit) {
            $droits = array_merge($droits, Roles::getRoles($droit));
        }

        $compteOrigine = null;
        if($request->getParameter('compteOrigine')) {
            $compteOrigine = CompteClient::getInstance()->findByLogin($request->getParameter('compteOrigine'));
        }

        $etablissement = null;
        if($request->getParameter('etablissement_id')) {
            $etablissement = EtablissementClient::getInstance()->find($request->getParameter('etablissement_id'));
        }

	$actif = $request->getParameter('actif', null);

        return $this->renderPartial("global/header", array("compte" => $compte, "droits" => $droits, "isAuthenticated" => true, "isUsurpation" => false, "etablissement" => $etablissement, "societe" => $societe, "actif" => $actif));
    }

    protected function forwardSecure() {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

}
