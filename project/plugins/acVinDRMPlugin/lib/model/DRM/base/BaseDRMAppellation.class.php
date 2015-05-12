<?php
/**
 * BaseDRMAppellation
 * 
 * Base model for DRMAppellation

 * @property float $total_debut_mois
 * @property float $total_entrees
 * @property float $total_recolte
 * @property float $total_sorties
 * @property float $total_facturable
 * @property float $total
 * @property acCouchdbJson $mentions

 * @method float getTotalDebutMois()
 * @method float setTotalDebutMois()
 * @method float getTotalEntrees()
 * @method float setTotalEntrees()
 * @method float getTotalRecolte()
 * @method float setTotalRecolte()
 * @method float getTotalSorties()
 * @method float setTotalSorties()
 * @method float getTotalFacturable()
 * @method float setTotalFacturable()
 * @method float getTotal()
 * @method float setTotal()
 * @method acCouchdbJson getMentions()
 * @method acCouchdbJson setMentions()
 
 */

abstract class BaseDRMAppellation extends _DRMTotal {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMAppellation';
    }
                
}