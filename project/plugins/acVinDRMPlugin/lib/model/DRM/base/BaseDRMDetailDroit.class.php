<?php
/**
 * BaseDRMDetailDroit
 * 
 * Base model for DRMDetailDroit

 * @property float $taux
 * @property float $volume_taxable

 * @method float getTaux()
 * @method float setTaux()
 * @method float getVolumeTaxable()
 * @method float setVolumeTaxable()
 
 */

abstract class BaseDRMDetailDroit extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetailDroit';
    }
                
}