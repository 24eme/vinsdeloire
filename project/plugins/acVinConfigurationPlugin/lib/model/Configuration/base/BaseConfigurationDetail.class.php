<?php
/**
 * BaseConfigurationDetail
 * 
 * Base model for ConfigurationDetail

 * @property acCouchdbJson $stocks_debut
 * @property acCouchdbJson $entrees
 * @property acCouchdbJson $sorties
 * @property acCouchdbJson $stocks_fin

 * @method acCouchdbJson getStocksDebut()
 * @method acCouchdbJson setStocksDebut()
 * @method acCouchdbJson getEntrees()
 * @method acCouchdbJson setEntrees()
 * @method acCouchdbJson getSorties()
 * @method acCouchdbJson setSorties()
 * @method acCouchdbJson getStocksFin()
 * @method acCouchdbJson setStocksFin()
 
 */

abstract class BaseConfigurationDetail extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationDetail';
    }
                
}