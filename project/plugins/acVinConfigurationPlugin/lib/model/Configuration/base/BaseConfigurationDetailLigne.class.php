<?php
/**
 * BaseConfigurationDetailLigne
 * 
 * Base model for ConfigurationDetailLigne

 * @property integer $readable
 * @property integer $writable
 * @property integer $details
 * @property integer $mouvement_coefficient
 * @property integer $vrac
 * @property integer $facturable

 * @method integer getReadable()
 * @method integer setReadable()
 * @method integer getWritable()
 * @method integer setWritable()
 * @method integer getDetails()
 * @method integer setDetails()
 * @method integer getMouvementCoefficient()
 * @method integer setMouvementCoefficient()
 * @method integer getVrac()
 * @method integer setVrac()
 * @method integer getFacturable()
 * @method integer setFacturable()
 
 */

abstract class BaseConfigurationDetailLigne extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationDetailLigne';
    }
                
}