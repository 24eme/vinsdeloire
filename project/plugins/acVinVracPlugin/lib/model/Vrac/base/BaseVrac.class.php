<?php
/**
 * BaseVrac
 * 
 * Base model for Vrac
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $campagne
 * @property string $numero_contrat
 * @property string $numero_archive
 * @property integer $etape
 * @property string $interne
 * @property string $vendeur_identifiant
 * @property acCouchdbJson $vendeur
 * @property string $acheteur_identifiant
 * @property acCouchdbJson $acheteur
 * @property string $mandataire_exist
 * @property acCouchdbJson $mandatant
 * @property string $mandataire_identifiant
 * @property acCouchdbJson $mandataire
 * @property integer $original
 * @property string $type_transaction
 * @property string $produit
 * @property string $produit_libelle
 * @property integer $millesime
 * @property string $categorie_vin
 * @property string $domaine
 * @property acCouchdbJson $label
 * @property float $raisin_quantite
 * @property float $jus_quantite
 * @property integer $bouteilles_quantite
 * @property float $bouteilles_contenance_volume
 * @property string $bouteilles_contenance_libelle
 * @property float $prix_unitaire
 * @property float $prix_total
 * @property float $prix_hl
 * @property string $type_contrat
 * @property integer $prix_variable
 * @property float $part_variable
 * @property float $prix_definitif_unitaire
 * @property float $prix_definitif_hl
 * @property float $prix_total_definitif
 * @property string $cvo_nature
 * @property string $cvo_repartition
 * @property string $commentaire
 * @property string $date_campagne
 * @property string $date_signature
 * @property float $volume_propose
 * @property float $volume_enleve
 * @property acCouchdbJson $valide

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getNumeroContrat()
 * @method string setNumeroContrat()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method integer getEtape()
 * @method integer setEtape()
 * @method string getInterne()
 * @method string setInterne()
 * @method string getVendeurIdentifiant()
 * @method string setVendeurIdentifiant()
 * @method acCouchdbJson getVendeur()
 * @method acCouchdbJson setVendeur()
 * @method string getAcheteurIdentifiant()
 * @method string setAcheteurIdentifiant()
 * @method acCouchdbJson getAcheteur()
 * @method acCouchdbJson setAcheteur()
 * @method string getMandataireExist()
 * @method string setMandataireExist()
 * @method acCouchdbJson getMandatant()
 * @method acCouchdbJson setMandatant()
 * @method string getMandataireIdentifiant()
 * @method string setMandataireIdentifiant()
 * @method acCouchdbJson getMandataire()
 * @method acCouchdbJson setMandataire()
 * @method integer getOriginal()
 * @method integer setOriginal()
 * @method string getTypeTransaction()
 * @method string setTypeTransaction()
 * @method string getProduit()
 * @method string setProduit()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method integer getMillesime()
 * @method integer setMillesime()
 * @method string getContientDomaine()
 * @method string setContientDomaine()
 * @method string getDomaine()
 * @method string setDomaine()
 * @method acCouchdbJson getLabel()
 * @method acCouchdbJson setLabel()
 * @method float getRaisinQuantite()
 * @method float setRaisinQuantite()
 * @method float getJusQuantite()
 * @method float setJusQuantite()
 * @method integer getBouteillesQuantite()
 * @method integer setBouteillesQuantite()
 * @method float getBouteillesContenanceVolume()
 * @method float setBouteillesContenanceVolume()
 * @method string getBouteillesContenanceLibelle()
 * @method string setBouteillesContenanceLibelle()
 * @method float getPrixUnitaire()
 * @method float setPrixUnitaire()
 * @method float getPrixTotal()
 * @method float setPrixTotal()
 * @method float getPrixHl()
 * @method float setPrixHl()
 * @method string getTypeContrat()
 * @method string setTypeContrat()
 * @method integer getPrixVariable()
 * @method integer setPrixVariable()
 * @method float getPartVariable()
 * @method float setPartVariable()
 * @method float getPrixDefinitifUnitaire()
 * @method float setPrixDefinitifUnitaire()
 * @method float getPrixDefinitifHl()
 * @method float setPrixDefinitifHl()
 * @method float getPrixTotalDefinitif()
 * @method float setPrixTotalDefinitif()
 * @method string getCvoNature()
 * @method string setCvoNature()
 * @method string getCvoRepartition()
 * @method string setCvoRepartition()
 * @method string getCommentaires()
 * @method string setCommentaires()
 * @method string getDateCampagne()
 * @method string setDateCampagne()
 * @method string getDateSignature()
 * @method string setDateSignature()
 * @method float getVolumePropose()
 * @method float setVolumePropose()
 * @method float getVolumeEnleve()
 * @method float setVolumeEnleve()
 * @method acCouchdbJson getValide()
 * @method acCouchdbJson setValide()
 
 */
 
abstract class BaseVrac extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Vrac';
    }
    
}
