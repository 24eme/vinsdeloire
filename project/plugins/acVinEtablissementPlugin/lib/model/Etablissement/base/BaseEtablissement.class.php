<?php
/**
 * BaseEtablissement
 * 
 * Base model for Etablissement
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $cooperative
 * @property string $interpro
 * @property string $identifiant
 * @property string $id_societe
 * @property string $nom
 * @property string $statut
 * @property string $raisins_mouts
 * @property string $exclusion_drm
 * @property string $relance_ds
 * @property acCouchdbJson $recette_locale
 * @property string $region
 * @property string $type_dr
 * @property acCouchdbJson $liaisons_operateurs
 * @property string $site_fiche
 * @property string $compte
 * @property string $num_interne
 * @property string $siret
 * @property string $raison_sociale
 * @property string $cni
 * @property string $cvi
 * @property string $no_accises
 * @property string $carte_pro
 * @property string $no_tva_intracommunautaire
 * @property string $famille
 * @property string $sous_famille
 * @property string $email
 * @property string $telephone
 * @property string $fax
 * @property string $commentaire
 * @property acCouchdbJson $siege
 * @property acCouchdbJson $comptabilite

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCooperative()
 * @method string setCooperative()
 * @method string getInterpro()
 * @method string setInterpro()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getIdSociete()
 * @method string setIdSociete()
 * @method string getNom()
 * @method string setNom()
 * @method string getStatut()
 * @method string setStatut()
 * @method string getRaisinsMouts()
 * @method string setRaisinsMouts()
 * @method string getExclusionDrm()
 * @method string setExclusionDrm()
 * @method string getRelanceDs()
 * @method string setRelanceDs()
 * @method acCouchdbJson getRecetteLocale()
 * @method acCouchdbJson setRecetteLocale()
 * @method string getRegion()
 * @method string setRegion()
 * @method string getTypeDr()
 * @method string setTypeDr()
 * @method acCouchdbJson getLiaisonsOperateurs()
 * @method acCouchdbJson setLiaisonsOperateurs()
 * @method string getSiteFiche()
 * @method string setSiteFiche()
 * @method string getCompte()
 * @method string setCompte()
 * @method string getNumInterne()
 * @method string setNumInterne()
 * @method string getSiret()
 * @method string setSiret()
 * @method string getRaisonSociale()
 * @method string setRaisonSociale()
 * @method string getCni()
 * @method string setCni()
 * @method string getCvi()
 * @method string setCvi()
 * @method string getNoAccises()
 * @method string setNoAccises()
 * @method string getCartePro()
 * @method string setCartePro()
 * @method string getNoTvaIntracommunautaire()
 * @method string setNoTvaIntracommunautaire()
 * @method string getFamille()
 * @method string setFamille()
 * @method string getSousFamille()
 * @method string setSousFamille()
 * @method string getEmail()
 * @method string setEmail()
 * @method string getTelephone()
 * @method string setTelephone()
 * @method string getFax()
 * @method string setFax()
 * @method string getCommentaire()
 * @method string setCommentaire()
 * @method acCouchdbJson getSiege()
 * @method acCouchdbJson setSiege()
 * @method acCouchdbJson getComptabilite()
 * @method acCouchdbJson setComptabilite()
 
 */
 
abstract class BaseEtablissement extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Etablissement';
    }
    
}