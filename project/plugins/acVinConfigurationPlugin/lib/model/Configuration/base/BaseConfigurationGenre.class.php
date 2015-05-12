<?php
/**
 * BaseConfigurationGenre
 * 
 * Base model for ConfigurationGenre

 * @property string $libelle
 * @property string $code
 * @property string $code_produit
 * @property string $code_comptable
 * @property string $code_douane
 * @property ConfigurationDetail $detail
 * @property acCouchdbJson $interpro
 * @property acCouchdbJson $departements
 * @property acCouchdbJson $appellations

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
 * @method ConfigurationDetail getDetail()
 * @method ConfigurationDetail setDetail()
 * @method acCouchdbJson getInterpro()
 * @method acCouchdbJson setInterpro()
 * @method acCouchdbJson getDepartements()
 * @method acCouchdbJson setDepartements()
 * @method acCouchdbJson getAppellations()
 * @method acCouchdbJson setAppellations()
 
 */

abstract class BaseConfigurationGenre extends _ConfigurationDeclaration {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationGenre';
    }
                
}