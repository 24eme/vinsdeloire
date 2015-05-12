<?php
/**
 * BaseDRM
 * 
 * Base model for DRM
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $region
 * @property acCouchdbJson $editeurs
 * @property string $apurement_possible
 * @property string $raison_rectificative
 * @property string $etape
 * @property string $campagne
 * @property string $periode
 * @property string $precedente
 * @property string $version
 * @property string $numero_archive
 * @property acCouchdbJson $droits
 * @property DRMDeclaration $declaration
 * @property acCouchdbJson $declarant
 * @property DRMDeclaratif $declaratif
 * @property string $identifiant
 * @property string $mode_de_saisie
 * @property string $commentaire
 * @property acCouchdbJson $interpros
 * @property acCouchdbJson $valide
 * @property acCouchdbJson $douane
 * @property acCouchdbJson $mouvements

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getRegion()
 * @method string setRegion()
 * @method acCouchdbJson getEditeurs()
 * @method acCouchdbJson setEditeurs()
 * @method string getApurementPossible()
 * @method string setApurementPossible()
 * @method string getRaisonRectificative()
 * @method string setRaisonRectificative()
 * @method string getEtape()
 * @method string setEtape()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getPeriode()
 * @method string setPeriode()
 * @method string getPrecedente()
 * @method string setPrecedente()
 * @method string getVersion()
 * @method string setVersion()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method acCouchdbJson getDroits()
 * @method acCouchdbJson setDroits()
 * @method DRMDeclaration getDeclaration()
 * @method DRMDeclaration setDeclaration()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method DRMDeclaratif getDeclaratif()
 * @method DRMDeclaratif setDeclaratif()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getModeDeSaisie()
 * @method string setModeDeSaisie()
 * @method string getCommentaire()
 * @method string setCommentaire()
 * @method acCouchdbJson getInterpros()
 * @method acCouchdbJson setInterpros()
 * @method acCouchdbJson getValide()
 * @method acCouchdbJson setValide()
 * @method acCouchdbJson getDouane()
 * @method acCouchdbJson setDouane()
 * @method acCouchdbJson getMouvements()
 * @method acCouchdbJson setMouvements()
 
 */
 
abstract class BaseDRM extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'DRM';
    }
    
}