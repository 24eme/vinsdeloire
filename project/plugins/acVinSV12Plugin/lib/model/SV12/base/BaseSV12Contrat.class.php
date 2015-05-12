<?php
/**
 * BaseSV12Contrat
 * 
 * Base model for SV12Contrat

 * @property string $contrat_numero
 * @property string $contrat_type
 * @property string $produit_libelle
 * @property string $produit_hash
 * @property string $vendeur_identifiant
 * @property string $vendeur_nom
 * @property float $volume_prop
 * @property float $volume

 * @method string getContratNumero()
 * @method string setContratNumero()
 * @method string getContratType()
 * @method string setContratType()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method string getProduitHash()
 * @method string setProduitHash()
 * @method string getVendeurIdentifiant()
 * @method string setVendeurIdentifiant()
 * @method string getVendeurNom()
 * @method string setVendeurNom()
 * @method float getVolumeProp()
 * @method float setVolumeProp()
 * @method float getVolume()
 * @method float setVolume()
 
 */

abstract class BaseSV12Contrat extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'SV12';
       $this->_tree_class_name = 'SV12Contrat';
    }
                
}