<?php

class AnnuaireRouting {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) 
    {
        $r = $event->getSubject();
        $r->prependRoute('annuaire', new sfRoute('/annuaire/:identifiant', array('module' => 'annuaire', 'action' => 'index')));
        $r->prependRoute('annuaire_retour', new sfRoute('/annuaire/:identifiant/retour', array('module' => 'annuaire', 'action' => 'retour')));
        $r->prependRoute('annuaire_selectionner', new sfRoute('/annuaire/:identifiant/selectionner/:type', array('module' => 'annuaire', 'action' => 'selectionner', 'type' => null)));
        $r->prependRoute('annuaire_commercial_ajouter', new sfRoute('/annuaire/:identifiant/ajouter/commercial', array('module' => 'annuaire', 'action' => 'ajouterCommercial')));
        $r->prependRoute('annuaire_ajouter', new sfRoute('/annuaire/:identifiant/ajouter/:type/:tiers', array('module' => 'annuaire', 'action' => 'ajouter')));
        $r->prependRoute('annuaire_supprimer', new sfRoute('/annuaire/:identifiant/supprimer/:type/:id', array('module' => 'annuaire', 'action' => 'supprimer')));
        $r->prependRoute('annuaire_relais', new sfRoute('/annuaire/:identifiant/relais/:type/:tiers', array('module' => 'annuaire', 'action' => 'relais')));
        $r->prependRoute('annuaire_choix_etablissement', new sfRoute('/annuaire/:identifiant/choix/:type/:tiers', array('module' => 'annuaire', 'action' => 'choix')));
    }

}