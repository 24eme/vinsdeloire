<?php
/**
 * BaseConfigurationCertification
 * 
 * Base model for ConfigurationCertification

 * @property string $libelle
 * @property string $code
 * @property string $code_produit
 * @property string $code_comptable
 * @property string $code_douane
 * @property acCouchdbJson $departements
 * @property acCouchdbJson $interpro
 * @property acCouchdbJson $genres
 * @property ConfigurationDetail $detail

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
 * @method acCouchdbJson getDepartements()
 * @method acCouchdbJson setDepartements()
 * @method acCouchdbJson getInterpro()
 * @method acCouchdbJson setInterpro()
 * @method acCouchdbJson getGenres()
 * @method acCouchdbJson setGenres()
 * @method ConfigurationDetail getDetail()
 * @method ConfigurationDetail setDetail()
 
 */

abstract class BaseConfigurationCertification extends _ConfigurationDeclaration {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationCertification';
    }
                
}