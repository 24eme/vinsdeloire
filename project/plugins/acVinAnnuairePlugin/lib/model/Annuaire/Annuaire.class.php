<?php
/**
 * Model for Annuaire
 *
 */

class Annuaire extends BaseAnnuaire 
{
    public function constructId() 
    {
        $this->set('_id', AnnuaireClient::getInstance()->buildId($this->identifiant));
    }

}