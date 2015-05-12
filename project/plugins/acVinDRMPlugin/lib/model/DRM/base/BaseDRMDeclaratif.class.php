<?php
/**
 * BaseDRMDeclaratif
 * 
 * Base model for DRMDeclaratif

 * @property integer $defaut_apurement
 * @property acCouchdbJson $daa
 * @property acCouchdbJson $dsa
 * @property integer $adhesion_emcs_gamma
 * @property acCouchdbJson $paiement
 * @property acCouchdbJson $caution

 * @method integer getDefautApurement()
 * @method integer setDefautApurement()
 * @method acCouchdbJson getDaa()
 * @method acCouchdbJson setDaa()
 * @method acCouchdbJson getDsa()
 * @method acCouchdbJson setDsa()
 * @method integer getAdhesionEmcsGamma()
 * @method integer setAdhesionEmcsGamma()
 * @method acCouchdbJson getPaiement()
 * @method acCouchdbJson setPaiement()
 * @method acCouchdbJson getCaution()
 * @method acCouchdbJson setCaution()
 
 */

abstract class BaseDRMDeclaratif extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDeclaratif';
    }
                
}