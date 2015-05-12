<?php
/**
 * BaseFactureLigne
 * 
 * Base model for FactureLigne

 * @property string $origine_type
 * @property string $origine_identifiant
 * @property string $origine_libelle
 * @property string $origine_date
 * @property string $id_origine
 * @property string $produit_type
 * @property string $produit_libelle
 * @property string $produit_hash
 * @property string $produit_identifiant_analytique
 * @property string $contrat_identifiant
 * @property string $echeance_code
 * @property float $volume
 * @property float $cotisation_taux
 * @property float $montant_ht
 * @property acCouchdbJson $origine_mouvements

 * @method string getOrigineType()
 * @method string setOrigineType()
 * @method string getOrigineIdentifiant()
 * @method string setOrigineIdentifiant()
 * @method string getOrigineLibelle()
 * @method string setOrigineLibelle()
 * @method string getOrigineDate()
 * @method string setOrigineDate()
 * @method string getIdOrigine()
 * @method string setIdOrigine()
 * @method string getProduitType()
 * @method string setProduitType()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method string getProduitHash()
 * @method string setProduitHash()
 * @method string getProduitIdentifiantAnalytique()
 * @method string setProduitIdentifiantAnalytique()
 * @method string getContratIdentifiant()
 * @method string setContratIdentifiant()
 * @method string getEcheanceCode()
 * @method string setEcheanceCode()
 * @method float getVolume()
 * @method float setVolume()
 * @method float getCotisationTaux()
 * @method float setCotisationTaux()
 * @method float getMontantHt()
 * @method float setMontantHt()
 * @method acCouchdbJson getOrigineMouvements()
 * @method acCouchdbJson setOrigineMouvements()
 
 */

abstract class BaseFactureLigne extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Facture';
       $this->_tree_class_name = 'FactureLigne';
    }
                
}