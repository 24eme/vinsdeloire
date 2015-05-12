<?php
/**
 * BaseFacture
 * 
 * Base model for Facture
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $identifiant
 * @property string $code_comptable_client
 * @property string $numero_facture
 * @property string $date_emission
 * @property string $date_facturation
 * @property string $campagne
 * @property string $numero_archive
 * @property string $statut
 * @property string $avoir
 * @property string $region
 * @property integer $versement_comptable
 * @property acCouchdbJson $emetteur
 * @property acCouchdbJson $declarant
 * @property float $total_ht
 * @property float $total_ttc
 * @property float $total_taxe
 * @property acCouchdbJson $lignes
 * @property acCouchdbJson $echeances
 * @property acCouchdbJson $origines

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getCodeComptableClient()
 * @method string setCodeComptableClient()
 * @method string getNumeroFacture()
 * @method string setNumeroFacture()
 * @method string getDateEmission()
 * @method string setDateEmission()
 * @method string getDateFacturation()
 * @method string setDateFacturation()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method string getStatut()
 * @method string setStatut()
 * @method string getAvoir()
 * @method string setAvoir()
 * @method string getRegion()
 * @method string setRegion()
 * @method integer getVersementComptable()
 * @method integer setVersementComptable()
 * @method acCouchdbJson getEmetteur()
 * @method acCouchdbJson setEmetteur()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method float getTotalHt()
 * @method float setTotalHt()
 * @method float getTotalTtc()
 * @method float setTotalTtc()
 * @method float getTotalTaxe()
 * @method float setTotalTaxe()
 * @method acCouchdbJson getLignes()
 * @method acCouchdbJson setLignes()
 * @method acCouchdbJson getEcheances()
 * @method acCouchdbJson setEcheances()
 * @method acCouchdbJson getOrigines()
 * @method acCouchdbJson setOrigines()
 
 */
 
abstract class BaseFacture extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Facture';
    }
    
}