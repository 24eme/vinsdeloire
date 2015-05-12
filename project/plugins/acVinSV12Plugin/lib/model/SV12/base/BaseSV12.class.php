<?php
/**
 * BaseSV12
 * 
 * Base model for SV12
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $identifiant
 * @property string $campagne
 * @property string $periode
 * @property string $version
 * @property string $region
 * @property string $numero_archive
 * @property acCouchdbJson $declarant
 * @property acCouchdbJson $totaux
 * @property acCouchdbJson $contrats
 * @property acCouchdbJson $mouvements
 * @property acCouchdbJson $valide

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getPeriode()
 * @method string setPeriode()
 * @method string getVersion()
 * @method string setVersion()
 * @method string getRegion()
 * @method string setRegion()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method acCouchdbJson getTotaux()
 * @method acCouchdbJson setTotaux()
 * @method acCouchdbJson getContrats()
 * @method acCouchdbJson setContrats()
 * @method acCouchdbJson getMouvements()
 * @method acCouchdbJson setMouvements()
 * @method acCouchdbJson getValide()
 * @method acCouchdbJson setValide()
 
 */
 
abstract class BaseSV12 extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'SV12';
    }
    
}