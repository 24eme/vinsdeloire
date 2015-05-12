<?php
/**
 * BaseDSProduit
 * 
 * Base model for DSProduit

 * @property string $code_douane
 * @property string $produit_libelle
 * @property string $produit_hash
 * @property float $stock_initial
 * @property float $stock_declare
 * @property string $vci
 * @property string $reserve_qualitative

 * @method string getCodeDouane()
 * @method string setCodeDouane()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method string getProduitHash()
 * @method string setProduitHash()
 * @method float getStockInitial()
 * @method float setStockInitial()
 * @method float getStockRevendique()
 * @method float setStockRevendique()
 * @method string getVci()
 * @method string setVci()
 * @method string getReserveQualitative()
 * @method string setReserveQualitative()
 
 */

abstract class BaseDSProduit extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSProduit';
    }
                
}
