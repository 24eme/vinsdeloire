<?php
/**
 * BaseCurrent
 * 
 * Base model for Current
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $campagne

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCampagne()
 * @method string setCampagne()
 
 */
 
abstract class BaseCurrent extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        
        return 'Current';
    }
    
}