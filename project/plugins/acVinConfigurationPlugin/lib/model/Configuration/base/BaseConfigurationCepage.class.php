<?php
/**
 * BaseConfigurationCepage
 * 
 * Base model for ConfigurationCepage

 * @property string $libelle
 * @property string $code
 * @property string $code_produit
 * @property string $code_comptable
 * @property string $code_douane

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
 
 */

abstract class BaseConfigurationCepage extends _ConfigurationDeclaration {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationCepage';
    }
                
}