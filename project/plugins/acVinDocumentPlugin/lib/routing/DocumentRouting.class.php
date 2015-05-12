<?php

class DocumentRouting {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {
        
        $r = $event->getSubject();
        $r->prependRoute('redirect_visualisation', new sfRoute('/redirectVisualisation/:id_doc', array('module' => 'document',
								  'action' => 'redirectVisualisation')));
        
    }

}