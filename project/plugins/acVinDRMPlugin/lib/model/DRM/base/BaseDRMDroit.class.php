<?php
/**
 * BaseDRMDroit
 * 
 * Base model for DRMDroit

 * @property integer $volume_taxe
 * @property integer $volume_reintegre
 * @property float $taux
 * @property string $code
 * @property string $libelle
 * @property float $total
 * @property float $report
 * @property float $cumul

 * @method integer getVolumeTaxe()
 * @method integer setVolumeTaxe()
 * @method integer getVolumeReintegre()
 * @method integer setVolumeReintegre()
 * @method float getTaux()
 * @method float setTaux()
 * @method string getCode()
 * @method string setCode()
 * @method string getLibelle()
 * @method string setLibelle()
 * @method float getTotal()
 * @method float setTotal()
 * @method float getReport()
 * @method float setReport()
 * @method float getCumul()
 * @method float setCumul()
 
 */

abstract class BaseDRMDroit extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDroit';
    }
                
}