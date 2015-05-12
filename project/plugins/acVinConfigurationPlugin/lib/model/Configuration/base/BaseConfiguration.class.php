<?php
/**
 * BaseConfiguration
 * 
 * Base model for Configuration
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $campagne
 * @property acCouchdbJson $labels
 * @property acCouchdbJson $contenances
 * @property acCouchdbJson $droits
 * @property acCouchdbJson $libelle_detail_ligne
 * @property acCouchdbJson $alias
 * @property ConfigurationDeclaration $declaration

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method acCouchdbJson getLabels()
 * @method acCouchdbJson setLabels()
 * @method acCouchdbJson getContenances()
 * @method acCouchdbJson setContenances()
 * @method acCouchdbJson getDroits()
 * @method acCouchdbJson setDroits()
 * @method acCouchdbJson getLibelleDetailLigne()
 * @method acCouchdbJson setLibelleDetailLigne()
 * @method acCouchdbJson getAlias()
 * @method acCouchdbJson setAlias()
 * @method ConfigurationDeclaration getDeclaration()
 * @method ConfigurationDeclaration setDeclaration()
 
 */
 
abstract class BaseConfiguration extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Configuration';
    }
    
}