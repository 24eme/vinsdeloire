<?php
/**
 * BaseAnnuaire
 * 
 * Base model for Annuaire
 *
 * @property string $_id
 * @property string $_rev
 * @property AnnuaireRecoltant $recoltant
 * @property AnnuaireNegociant $negociant
 * @property AnnuaireCaveCooperative $cave_cooperative
 * @property string $identifiant
 * @property string $type

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method AnnuaireRecoltant getRecoltant()
 * @method AnnuaireRecoltant setRecoltant()
 * @method AnnuaireNegociant getNegociant()
 * @method AnnuaireNegociant setNegociant()
 * @method AnnuaireCaveCooperative getCaveCooperative()
 * @method AnnuaireCaveCooperative setCaveCooperative()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getType()
 * @method string setType()
 
 */
 
abstract class BaseAnnuaire extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Annuaire';
    }
    
}