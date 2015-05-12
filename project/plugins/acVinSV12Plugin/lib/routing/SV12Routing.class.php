<?php

class SV12Routing {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {
        
        $r = $event->getSubject();
        $r->prependRoute('sv12', new sfRoute('/sv12', array('module' => 'sv12',
								  'action' => 'chooseEtablissement')));
        
        $r->prependRoute('sv12_redirect_to_visualisation', new sfRoute('/sv12/redirect/:identifiant_sv12', 
								      array('module' => 'sv12', 'action' => 'redirect'),  
								      array('sf_method' => array('get'))));
        
        $r->prependRoute('sv12_etablissement', new EtablissementRoute('/sv12/:identifiant', array('module' => 'sv12', 
                                                                        'action' => 'monEspace'),
                                                    array('sf_method' => array('get','post')),
                                                    array('model' => 'Etablissement',
                                                        'type' => 'object')
                                                        ));
        
        $r->prependRoute('sv12_nouvelle', new sfRoute('/sv12/:identifiant/nouvelle/:periode', 
                                                array('module' => 'sv12', 
                                                      'action' => 'nouvelle'),
                                                array('sf_method' => array('get'))
                                                ));
        
        $r->prependRoute('sv12_update', new SV12Route('/sv12/:identifiant/edition/:periode_version/update',
                        array('module' => 'sv12',
                            'action' => 'update'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'SV12',
                             'type' => 'object',
                             'control' => array('edition'),
                )));
        
        
        $r->prependRoute('sv12_update_addProduit', new SV12Route('/sv12/:identifiant/edition/:periode_version/update/addProduit', array('module' => 'sv12',
                    'action' => 'updateAddProduit'),
                    array('sf_method' => array('get', 'post')),
                    array('model' => 'SV12',
                        'type' => 'object',
                        'control' => array('edition')
           		)));

        $r->prependRoute('sv12_modificative', new SV12Route('/sv12/:identifiant/modificative/:periode_version', 
                                                  array('module' => 'sv12', 
                                                        'action' => 'modificative'),
                                                  array(),
                                                  array('model' => 'SV12',
                                                        'type' => 'object',
                                                        'control' => array('valid'))));
        
        $r->prependRoute('sv12_recapitulatif', new SV12Route('/sv12/:identifiant/edition/:periode_version/recapitulatif',
                array('module' => 'sv12',
                    'action' => 'recapitulatif'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'SV12',
                      'type' => 'object',
                      'control' => array('edition')
        )));
        
         $r->prependRoute('sv12_visualisation', new SV12Route('/sv12/:identifiant/visualisation/:periode_version',
                array('module' => 'sv12',
                    'action' => 'visualisation'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'SV12',
                      'type' => 'object',
                      'control' => array('valid')
        )));
        
    }

}