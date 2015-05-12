<?php

class Etablissement extends BaseEtablissement {

    protected $_interpro = null;
    protected $droit = null;

    /**
     * @return _Compte
     */
    public function getInterproObject() {
        if (is_null($this->_interpro)) {
            $this->_interpro = InterproClient::getInstance()->find($this->interpro);
        }

        return $this->_interpro;
    }

    public function constructId() {
        $this->set('_id', 'ETABLISSEMENT-' . $this->identifiant);
        if ($this->isViticulteur()) {
            $this->raisins_mouts = is_null($this->raisins_mouts) ? EtablissementClient::RAISINS_MOUTS_NON : $this->raisins_mouts;
            $this->exclusion_drm = is_null($this->exclusion_drm) ? EtablissementClient::EXCLUSION_DRM_NON : $this->exclusion_drm;
            $this->type_dr = is_null($this->type_dr) ? EtablissementClient::TYPE_DR_DRM : $this->type_dr;
        }

        if ($this->isViticulteur() || $this->isNegociant()) {
            $this->relance_ds = is_null($this->relance_ds) ? EtablissementClient::RELANCE_DS_OUI : $this->relance_ds;
        }

        $this->statut = is_null($this->statut) ? EtablissementClient::STATUT_ACTIF : $this->statut;
    }

    public function setRelanceDS($value) {
        if (!($this->isViticulteur() || $this->isNegociant())) {
            throw new sfException("Le champs 'relance_ds' n'est valable que pour les viticulteurs ou les négociants");
        }

        $this->_set('relance_ds', $value);
    }

    public function setExclusionDRM($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'exclusion_drm' n'est valable que pour les viticulteurs");
        }

        $this->_set('exclusion_drm', $value);
    }

    public function setRaisinsMouts($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'raisins_mouts' n'est valable que pour les viticulteurs");
        }

        $this->_set('raisins_mouts', $value);
    }

    public function setTypeDR($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'type_dr' n'est valable que pour les viticulteurs");
        }

        $this->_set('type_dr', $value);
    }

    public function getAllDRM() {
        return acCouchdbManager::getClient()->startkey(array($this->identifiant, null))
                        ->endkey(array($this->identifiant, null))
                        ->getView("drm", "all");
    }

    private function cleanPhone($phone) {
        $phone = preg_replace('/[^0-9\+]+/', '', $phone);
        $phone = preg_replace('/^00/', '+', $phone);
        $phone = preg_replace('/^0/', '+33', $phone);

        if (strlen($phone) == 9 && preg_match('/^[64]/', $phone))
            $phone = '+33' . $phone;

        if (!preg_match('/^\+/', $phone) || (strlen($phone) != 12 && preg_match('/^\+33/', $phone)))
            echo("$phone n'est pas un téléphone correct pour " . $this->_id . "\n");

        return $phone;
    }

    public function getMasterCompte() {
        if ($this->compte)
            return CompteClient::getInstance()->find($this->compte);
        return CompteClient::getInstance()->find($this->getSociete()->compte_societe);
    }

    public function getContact() {

        return $this->getMasterCompte();
    }

    public function getSociete() {
        return SocieteClient::getInstance()->find($this->id_societe);
    }

    public function isSameCoordonneeThanSociete() {

        return $this->isSameContactThanSociete();
    }

    public function isSameContactThanSociete() {
        return ($this->compte == $this->getSociete()->compte_societe);
    }

    public function getNumCompteEtablissement() {
        if (!$this->compte)
            return null;
        if ($this->compte != $this->getSociete()->compte_societe)
            return $this->compte;
        return null;
    }

    public function getNoTvaIntraCommunautaire() {
        $societe = $this->getSociete();

        if (!$societe) {

            return null;
        }

        return $societe->no_tva_intracommunautaire;
    }

    public function setFax($fax) {
        if ($fax)
            $this->_set('fax', $this->cleanPhone($fax));
    }

    public function setTelephone($phone, $idcompte = null) {
        if ($phone)
            $this->_set('telephone', $this->cleanPhone($phone));
    }

    public function getDenomination() {

        return ($this->nom) ? $this->nom : $this->raison_sociale;
    }

    public function addLiaison($type, $etablissement) {
        if (!in_array($type, EtablissementClient::listTypeLiaisons()))
            throw new sfException("liaison type \"$type\" unknown");
        $liaison = $this->liaisons_operateurs->add($type . '_' . $etablissement->_id);
        $liaison->type_liaison = $type;
        $liaison->id_etablissement = $etablissement->_id;
        $liaison->libelle_etablissement = $etablissement->nom;
        return $liaison;
    }

    public function isNegociant() {
        return ($this->famille == EtablissementFamilles::FAMILLE_NEGOCIANT);
    }

    public function isViticulteur() {
        return ($this->famille == EtablissementFamilles::FAMILLE_PRODUCTEUR);
    }

    public function isCourtier() {
        return ($this->famille == EtablissementFamilles::FAMILLE_COURTIER);
    }

    public function getFamilleType() {
        $familleType = array(EtablissementFamilles::FAMILLE_PRODUCTEUR => 'vendeur',
            EtablissementFamilles::FAMILLE_NEGOCIANT => 'acheteur',
            EtablissementFamilles::FAMILLE_COURTIER => 'mandataire');
        return $familleType[$this->famille];
    }

    public function getDepartement() {
        if ($this->siege->code_postal) {
            return substr($this->siege->code_postal, 0, 2);
        }
        return null;
    }

    public function getDroit() {
        if (is_null($this->droit)) {

            $this->droit = new EtablissementDroit($this);
        }

        return $this->droit;
    }

    public function hasDroit($droit) {

        return $this->getDroit()->has($droit);
    }

    public function getDroits() {
        return EtablissementFamilles::getDroitsByFamilleAndSousFamille($this->famille, $this->sous_famille);
    }

    public function isInterLoire() {
        return ($this->region != EtablissementClient::REGION_HORSINTERLOIRE);
    }

    protected function synchroRecetteLocale() {
        if ($this->recette_locale->id_douane) {
            $soc = SocieteClient::getInstance()->find($this->recette_locale->id_douane);
            if ($soc && $this->recette_locale->nom != $soc->raison_sociale) {
                $this->recette_locale->nom = $soc->raison_sociale;
                $this->recette_locale->ville = $soc->siege->commune;
            }
        }
    }

    protected function initFamille() {
        if (!$this->famille) {
            $this->famille = EtablissementFamilles::FAMILLE_PRODUCTEUR;
        }
        if (!$this->sous_famille) {
            $this->sous_famille = EtablissementFamilles::SOUS_FAMILLE_CAVE_PARTICULIERE;
        }
    }

    protected function synchroFromSociete() {
        $soc = SocieteClient::getInstance()->find($this->id_societe);
        if (!$soc)
            throw new sfException("$id n'est pas une société connue");
        $this->cooperative = $soc->cooperative;
    }

    protected function synchroAndSaveSociete() {
        $soc = $this->getSociete();
        $soc->addEtablissement($this);
        $soc->save(true);
    }

    protected function synchroAndSaveCompte() {
        $compte_master = $this->getMasterCompte();
        if ($this->isSameContactThanSociete()) {
            $compte_master->addOrigine($this->_id);
            if (($this->statut != EtablissementClient::STATUT_SUSPENDU)){
                $compte_master->statut = $this->statut;
            }
        } else {
            $compte_master->statut = $this->statut;
        }
        $compte_master->save(false, true);
    }
    
    public function switchOrigineAndSaveCompte($old_id) {

        $this->synchroFromCompte();
        
        if (!$old_id) {
            return;
        }        

        if ($this->isSameContactThanSociete()) {
            CompteClient::getInstance()->findAndDelete($old_id, true);
            $compte = $this->getContact();
            $compte->addOrigine($this->_id);
        } else {
            $compte = CompteClient::getInstance()->find($old_id);
            $compte->removeOrigine($this->_id);
            $compte->statut = $this->statut;
        }
        $compte->save(false, true);
    }

    public function save($fromsociete = false, $fromclient = false, $fromcompte = false) {
        $this->constructId();
        $this->synchroRecetteLocale();
        $this->initFamille();
        $this->synchroFromSociete();

        if (!$fromclient) {
            if (!$this->compte) {
                $compte = CompteClient::getInstance()->createCompteFromEtablissement($this);
                $compte->constructId();
                $compte->statut = $this->statut;
                $this->compte = $compte->_id;
                parent::save();
                $compte->save(true, true);
            }
        }

        if ($this->isViticulteur() && $this->type_dr == EtablissementClient::TYPE_DR_DRA) {
            $this->exclusion_drm = EtablissementClient::EXCLUSION_DRM_OUI;
        }

        parent::save();

        if (!$fromsociete) {
            $this->synchroAndSaveSociete();
            if (!$fromcompte) {
                $this->synchroAndSaveCompte();
            }
        }
    }

    public function isActif() {
        return ($this->statut == EtablissementClient::STATUT_ACTIF);
    }

    public function setIdSociete($id) {
        $soc = SocieteClient::getInstance()->find($id);
        if (!$soc)
            throw new sfException("$id n'est pas une société connue");
        $this->_set("id_societe", $id);
    }

    public function __toString() {

        return sprintf('%s (%s)', $this->nom, $this->identifiant);
    }

    public function getBailleurs() {
        $bailleurs = array();
        if (!(count($this->liaisons_operateurs)))
            return $bailleurs;
        $liaisons = $this->liaisons_operateurs;
        foreach ($liaisons as $key => $liaison) {
            if ($liaison->type_liaison == EtablissementClient::TYPE_LIAISON_BAILLEUR)
                $bailleurs[$key] = $liaison;
        }
        return $bailleurs;
    }

    public function findBailleurByNom($nom) {
        $bailleurs = $this->getBailleurs();
        foreach ($bailleurs as $key => $liaison) {
            if ($liaison->libelle_etablissement == $nom)
                return EtablissementClient::getInstance()->find($liaison->id_etablissement);
            if ($liaison->exist('aliases'))
                foreach ($liaison->aliases as $alias) {
                    if (strtoupper($alias) == strtoupper($nom))
                        return EtablissementClient::getInstance()->find($liaison->id_etablissement);
                }
        }
        return null;
    }

    public function addAliasForBailleur($identifiant_bailleur, $alias) {
        $bailleurNameNode = EtablissementClient::TYPE_LIAISON_BAILLEUR . '_' . $identifiant_bailleur;
        if (!$this->liaisons_operateurs->exist($bailleurNameNode))
            throw new sfException("La liaison avec le bailleur $identifiant_bailleur n'existe pas");
        $node = $this->liaisons_operateurs->$bailleurNameNode;
        if (!$node->exist('aliases'))
            $node->add('aliases');
        $node->aliases->add($alias, $alias);
    }

    public function synchroFromCompte() {
        $compte = $this->getMasterCompte();

        if (!$compte) {

            return null;
        }

        $this->siege->adresse = $compte->adresse;
        if($compte->exist('adresse_complementaire'))
             $this->siege->add('adresse_complementaire',$compte->adresse_complementaire);
        $this->siege->code_postal = $compte->code_postal;
        $this->siege->commune = $compte->commune;
        $this->email = $compte->email;
        $this->fax = $compte->fax;
        $this->telephone = ($compte->telephone_bureau) ? $compte->telephone_bureau : $compte->telephone_mobile;

        return $this;
    }
    
    public function getSiegeAdresses() {
      $a = $this->siege->adresse;
      if ($this->siege->exist("adresse_complementaire")) {
	$a .= ' ; '.$this->siege->adresse_complementaire;
      }
      return $a;
    }
    
    public function getTeledeclarationMail() {
        $etablissementPrincipal = $this->getSociete()->getEtablissementPrincipal();
        if(!$etablissementPrincipal->exist('email') || !$etablissementPrincipal->email){
            return false;
        }
        return $etablissementPrincipal->email;
    }
    
    public function getEtablissementPrincipal() {
        return SocieteClient::getInstance()->find($this->id_societe)->getEtablissementPrincipal();
    }

}
