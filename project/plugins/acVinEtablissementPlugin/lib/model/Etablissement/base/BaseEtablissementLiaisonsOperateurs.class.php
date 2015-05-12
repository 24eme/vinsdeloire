<?php
/**
 * BaseEtablissementLiaisonsOperateurs
 * 
 * Base model for EtablissementLiaisonsOperateurs

 * @property string $id_etablissement
 * @property string $libelle_etablissement
 * @property string $type_liaison
 * @property acCouchdbJson $aliases

 * @method string getIdEtablissement()
 * @method string setIdEtablissement()
 * @method string getLibelleEtablissement()
 * @method string setLibelleEtablissement()
 * @method string getTypeLiaison()
 * @method string setTypeLiaison()
 * @method acCouchdbJson getAliases()
 * @method acCouchdbJson setAliases()
 
 */

abstract class BaseEtablissementLiaisonsOperateurs extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Etablissement';
       $this->_tree_class_name = 'EtablissementLiaisonsOperateurs';
    }
                
}