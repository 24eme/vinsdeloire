<?php
/**
 * BaseConfigurationAppellation
 * 
 * Base model for ConfigurationAppellation

 * @property string $libelle
 * @property string $code
 * @property string $code_produit
 * @property string $code_comptable
 * @property string $code_douane
 * @property string $densite
 * @property ConfigurationDetail $detail
 * @property acCouchdbJson $interpro
 * @property acCouchdbJson $departements
 * @property acCouchdbJson $mentions

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getCode()
 * @method string setCode()
 * @method string getCodeProduit()
 * @method string setCodeProduit()
 * @method string getCodeComptable()
 * @method string setCodeComptable()
 * @method string getCodeDouane()
 * @method string setCodeDouane()
 * @method string getDensite()
 * @method string setDensite()
 * @method ConfigurationDetail getDetail()
 * @method ConfigurationDetail setDetail()
 * @method acCouchdbJson getInterpro()
 * @method acCouchdbJson setInterpro()
 * @method acCouchdbJson getDepartements()
 * @method acCouchdbJson setDepartements()
 * @method acCouchdbJson getMentions()
 * @method acCouchdbJson setMentions()
 
 */

abstract class BaseConfigurationAppellation extends _ConfigurationDeclaration {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationAppellation';
    }
                
}