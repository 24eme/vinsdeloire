<?php
/**
 * BaseSV12Mouvement
 * 
 * Base model for SV12Mouvement

 * @property string $categorie
 * @property string $produit_hash
 * @property string $produit_libelle
 * @property string $type_hash
 * @property string $type_libelle
 * @property string $vrac_numero
 * @property string $vrac_destinataire
 * @property string $detail_identifiant
 * @property string $detail_libelle
 * @property float $volume
 * @property float $cvo
 * @property integer $facture
 * @property integer $facturable
 * @property string $date
 * @property string $date_version
 * @property string $version

 * @method string getCategorie()
 * @method string setCategorie()
 * @method string getProduitHash()
 * @method string setProduitHash()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method string getTypeHash()
 * @method string setTypeHash()
 * @method string getTypeLibelle()
 * @method string setTypeLibelle()
 * @method string getVracNumero()
 * @method string setVracNumero()
 * @method string getVracDestinataire()
 * @method string setVracDestinataire()
 * @method string getDetailIdentifiant()
 * @method string setDetailIdentifiant()
 * @method string getDetailLibelle()
 * @method string setDetailLibelle()
 * @method float getVolume()
 * @method float setVolume()
 * @method float getCvo()
 * @method float setCvo()
 * @method integer getFacture()
 * @method integer setFacture()
 * @method integer getFacturable()
 * @method integer setFacturable()
 * @method string getDate()
 * @method string setDate()
 * @method string getDateVersion()
 * @method string setDateVersion()
 * @method string getVersion()
 * @method string setVersion()
 
 */

abstract class BaseSV12Mouvement extends Mouvement {
                
    public function configureTree() {
       $this->_root_class_name = 'SV12';
       $this->_tree_class_name = 'SV12Mouvement';
    }
                
}