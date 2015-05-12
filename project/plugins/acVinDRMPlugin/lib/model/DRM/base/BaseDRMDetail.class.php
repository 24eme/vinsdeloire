<?php
/**
 * BaseDRMDetail
 * 
 * Base model for DRMDetail

 * @property integer $pas_de_mouvement_check
 * @property float $total_debut_mois
 * @property float $total_entrees
 * @property float $total_recolte
 * @property float $total_sorties
 * @property float $total_facturable
 * @property float $total
 * @property string $produit_libelle
 * @property DRMDetailDroit $cvo
 * @property acCouchdbJson $labels
 * @property string $label_supplementaire
 * @property acCouchdbJson $millesimes
 * @property DRMDetailNoeud $stocks_debut
 * @property DRMDetailNoeud $entrees
 * @property DRMDetailNoeud $sorties
 * @property DRMDetailNoeud $stocks_fin

 * @method integer getPasDeMouvementCheck()
 * @method integer setPasDeMouvementCheck()
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
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method DRMDetailDroit getCvo()
 * @method DRMDetailDroit setCvo()
 * @method acCouchdbJson getLabels()
 * @method acCouchdbJson setLabels()
 * @method string getLabelSupplementaire()
 * @method string setLabelSupplementaire()
 * @method acCouchdbJson getMillesimes()
 * @method acCouchdbJson setMillesimes()
 * @method DRMDetailNoeud getStocksDebut()
 * @method DRMDetailNoeud setStocksDebut()
 * @method DRMDetailNoeud getEntrees()
 * @method DRMDetailNoeud setEntrees()
 * @method DRMDetailNoeud getSorties()
 * @method DRMDetailNoeud setSorties()
 * @method DRMDetailNoeud getStocksFin()
 * @method DRMDetailNoeud setStocksFin()
 
 */

abstract class BaseDRMDetail extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetail';
    }
                
}