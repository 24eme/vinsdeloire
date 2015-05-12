<?php
/**
 * BaseDS
 * 
 * Base model for DS
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $identifiant
 * @property string $date_emission
 * @property string $date_echeance
 * @property string $date_stock
 * @property string $campagne
 * @property string $periode
 * @property string $numero_archive
 * @property string $statut
 * @property string $commentaire
 * @property acCouchdbJson $declarant
 * @property acCouchdbJson $declarations

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getDateEmission()
 * @method string setDateEmission()
 * @method string getDateEcheance()
 * @method string setDateEcheance()
 * @method string getDateStock()
 * @method string setDateStock()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getPeriode()
 * @method string setPeriode()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method string getStatut()
 * @method string setStatut()
 * @method string getCommentaires()
 * @method string setCommentaires()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method acCouchdbJson getDeclarations()
 * @method acCouchdbJson setDeclarations()
 
 */
 
abstract class BaseDS extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'DS';
    }
    
}
