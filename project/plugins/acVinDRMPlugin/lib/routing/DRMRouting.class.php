<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DRMRouting configuration.
 * 
 * @package    DRMRouting
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class DRMRouting {

    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    static public function listenToRoutingLoadConfigurationEvent(sfEvent $event) {
        $r = $event->getSubject();
        
        $r->prependRoute('drm', new sfRoute('/drm', array('module' => 'drm', 
                                                          'action' => 'chooseEtablissement')));
		
        $r->prependRoute('drm_etablissement', new EtablissementRoute('/drm/:identifiant/:campagne', array('module' => 'drm', 
                                                                                'action' => 'monEspace', 'campagne' => null),
                                                         array('sf_method' => array('get','post')),
                                                          array('model' => 'Etablissement',
                                                                'type' => 'object')
								));
        
        $r->prependRoute('drm_inProcess', new EtablissementRoute('/drm/:identifiant/drm-conflit', array('module' => 'drm', 
                                                                                'action' => 'inProcess', 'campagne' => null),
                                                         array('sf_method' => array('get','post')),
                                                          array('model' => 'Etablissement',
                                                                'type' => 'object')
								));
        

        $r->prependRoute('drm_etablissement_stocks', new EtablissementRoute('/drm/:identifiant/stocks/:campagne', array('module' => 'drm', 
                                                                                'action' => 'stocks',
                                                                                'campagne' => null),
                                                                     array('sf_method' => array('get','post')),
                                                                      array('model' => 'Etablissement',
                                                                            'type' => 'object')
                ));

        $r->prependRoute('drm_historique', new EtablissementRoute('/drm/:identifiant/historique/:campagne', array('module' => 'drm', 
                                                                                       'action' => 'historique', 
                                                                                       'campagne' => null),
                                                         array('sf_method' => array('get','post')),
                                                          array('model' => 'Etablissement',
                                                                'type' => 'object')
								));

        $r->prependRoute('drm_nouvelle', new EtablissementRoute('/drm/:identifiant/nouvelle/:periode', 
                                                array('module' => 'drm', 
                                                      'action' => 'nouvelle',
                                                	    'periode' => null),
                                                array('sf_method' => array('get')),
                                                array('model' => 'Etablissement',
                                                      'type' => 'object')));

        $r->prependRoute('drm_delete', new DRMRoute('/drm/:identifiant/delete/:periode_version', 
                                                array('module' => 'drm', 
                                                      'action' => 'delete'),
							 array('sf_method' => array('get', 'post')),
                                                array('model' => 'DRM',
                                                      'type' => 'object',
                                                      'control' => array('edition'),)));

        $r->prependRoute('drm_init', new DRMRoute('/drm/:identifiant/initialiser/:periode_version', 
                                                array('module' => 'drm', 
                                                      'action' => 'init'),
                                                array('sf_method' => array('get')),
                                                array('model' => 'DRM',
                                                      'type' => 'object',
                                                      'control' => array('edition'),
                                                     )));

        $r->prependRoute('drm_rectificative', new DRMRoute('/drm/:identifiant/rectifier/:periode_version', 
                                                          array('module' => 'drm', 
                                                                'action' => 'rectificative'),
                                                          array(),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                                                                'control' => array('valid'),
                                                                )));

        $r->prependRoute('drm_modificative', new DRMRoute('/drm/:identifiant/modificative/:periode_version', 
                                                          array('module' => 'drm', 
                                                                'action' => 'modificative'),
                                                          array(),
                                                		      array('model' => 'DRM',
                                                                'type' => 'object',
                                                                'control' => array('valid'),
                                                                )));

        /*$r->prependRoute('drm_informations', new DRMRoute('/drm/:identifiant/edition/:periode_version/informations', 
                                                          array('module' => 'drm', 
                                                                'action' => 'informations'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									                'must_be_valid' => false,
                              									                'must_be_not_valid' => true)));*/
        
        /*$r->prependRoute('drm_modif_infos', new DRMRoute('/drm/:identifiant/edition/:periode_version/modification-informations', 
                                                          array('module' => 'drm', 
                                                                'action' => 'modificationInfos'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									'must_be_valid' => false,
                              									'must_be_not_valid' => true)));*/

        /*$r->prependRoute('drm_stock_debut_mois', new DRMRoute('/drm/:identifiant/edition/:periode_version/stock', 
                                                          array('module' => 'drm', 
                                                                'action' => 'stock'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									'must_be_valid' => false,
                              									'must_be_not_valid' => true)));*/

        /*$r->prependRoute('drm_declaratif', new DRMRoute('/drm/:identifiant/edition/:periode_version/declaratif', 
                                                          array('module' => 'drm', 
                                                                'action' => 'declaratif'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									'must_be_valid' => false,
                              									'must_be_not_valid' => true)));
        

        $r->prependRoute('drm_declaratif_frequence_form', new DRMRoute('/drm/:identifiant/edition/:periode_version/declaratif/frequence-paiement',
                                                          array('module' => 'drm', 
                                                                'action' => 'paiementFrequenceFormAjax'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                                                                'must_be_valid' => false,
                                                                'must_be_not_valid' => true)));*/

        $r->prependRoute('drm_validation', new DRMRoute('/drm/:identifiant/edition/:periode_version/validation', 
                                                          array('module' => 'drm', 
                                                                'action' => 'validation'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									                'control' => array('edition'))));

        // $r->prependRoute('drm_show_error', new DRMRoute('/drm/:identifiant/edition/:periode_version/voir-erreur/:type/:identifiant', 
        //                                                   array('module' => 'drm', 
        //                                                         'action' => 'showError'),
        //                                                   array('sf_method' => array('get')),
        //                                                   array('model' => 'DRM',
        //                                                         'type' => 'object',
        //                       									'must_be_valid' => false,
        //                       									'must_be_not_valid' => true)));

	$r->prependRoute('drm_redirect_to_visualisation', new sfRoute('/drm/redirect/:identifiant_drm', 
								      array('module' => 'drm', 'action' => 'redirect'),  
								      array('sf_method' => array('get'))
								      ));
	

        $r->prependRoute('drm_visualisation', new DRMRoute('/drm/:identifiant/visualisation/:periode_version/:hide_rectificative', 
                                                          array('module' => 'drm', 
                                                                'action' => 'visualisation',
                                                          		  'hide_rectificative' => null),
                                                          array('sf_method' => array('get')),
                                            						  array('model' => 'DRM', 
                                                                'type' => 'object', 
                                            							      'control' => array('valid'))));

        /*$r->prependRoute('drm_pdf', new DRMRoute('/drm/:identifiant/pdf/:periode_version.:format', 
                                                          array('module' => 'drm', 
                                                                'action' => 'pdf',
                                                                'format' => 'pdf'),
                                                          array('sf_method' => array('get'), 'format' => '(html|pdf)'),
                                                          array('must_be_valid' => false,
                              									'must_be_not_valid' => false)));*/

        /*$r->prependRoute('drm_mouvements_generaux', new DRMRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux', 
                                                          array('module' => 'drm_mouvements_generaux', 
                                                                'action' => 'index'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                              									'must_be_valid' => false,
                              									'must_be_not_valid' => true)));

        $r->prependRoute('drm_mouvements_generaux_produit_update', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail/update',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'updateAjax'),
                        array('sf_method' => array('post')),
                        array('model' => 'DRMProduit',
                              'type' => 'object',
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));
                        
        $r->prependRoute('drm_mouvements_generaux_produit_addlabel', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail/addlabel',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'addLabel'),
                        array('sf_method' => array('post')),
                        array('model' => 'DRMProduit',
                              'type' => 'object',
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));
                        
        $r->prependRoute('drm_mouvements_generaux_stock_epuise', new DRMRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/stock-epuise',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'stockEpuise'),
                        array('sf_method' => array('post', 'get')),
                        array('model' => 'DRM',
                              'type' => 'object',
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));

        $r->prependRoute('drm_mouvements_generaux_produits_update', new DRMRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/update_produits',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'updateProduitsAjax'),
                        array('sf_method' => array('post')),
                        array('model' => 'DRM',
                              'type' => 'object',
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));

        $r->prependRoute('drm_mouvements_generaux_produit_delete', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail/delete',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'deleteAjax'),
                        array('sf_method' => array('post')),
                        array('model' => 'DRMProduit',
                              'type' => 'object',
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));  

        $r->prependRoute('drm_mouvements_generaux_product_ajout', new DRMCertificationRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/ajout/:certification',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'ajoutAjax'),
                        array('sf_method' => array('get','post')),
                        array('model' => 'DRMCertification',
                              'type' => 'object',
                              'add_noeud' => true,
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));

        $r->prependRoute('drm_mouvements_generaux_product_add', new DRMCertificationRoute('/drm/:identifiant/edition/:periode_version/mouvements-generaux/add/:certification',
                        array('module' => 'drm_mouvements_generaux',
                            'action' => 'add'),
                        array('sf_method' => array('get','post')),
                        array('model' => 'DRMCertification',
                              'type' => 'object',
                              'add_noeud' => true,
                              'must_be_valid' => false,
                              'must_be_not_valid' => true)));
		*/

        /*$r->prependRoute('drm_recap', new DRMRoute('/drm/:identifiant/edition/:periode_version/recapitulatif',
                        array('module' => 'drm_recap',
                            'action' => 'index'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DRM',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));
        
        $r->prependRoute('drm_recap_lieu_ajout_ajax', new DRMCertificationRoute('/drm/:identifiant/edition/:periode_version/recapitulatif-appellation-ajout/:certification',
                        array('module' => 'drm_recap',
                            'action' => 'lieuAjoutAjax'),
                        array('sf_method' => array('get','post')),
                        array('model' => 'DRMCertification',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));

        $r->prependRoute('drm_recap_lieu', new DRMLieuRoute('/drm/:identifiant/edition/:periode_version/recapitulatif/:certification/:genre/:appellation/:mention/:lieu',
                        array('module' => 'drm_recap',
                            'action' => 'lieu'),
                        array('sf_method' => array('get')),
                        array('model' => 'DRMAppellation',
                             'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));


		$r->prependRoute('drm_recap_detail', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/recapitulatif/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail',
                        array('module' => 'drm_recap',
                            'action' => 'detail'),
                        array('sf_method' => array('get')),
                        array('model' => 'DRMDetail',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
		
        $r->prependRoute('drm_recap_ajout_ajax', new DRMLieuRoute('/drm/:identifiant/edition/:periode_version/recapitulatif/:certification/:genre/:appellation/:mention/:lieu/ajout-ajax',
                        array('module' => 'drm_recap',
                            'action' => 'ajoutAjax'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DRMAppellation',
                            'type' => 'object',
                            'add_noeud' => true,
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));

		$r->prependRoute('drm_recap_update', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/recapitulatif/update/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail',
                                                          array('module' => 'drm_recap',
                                                                'action' => 'update'),
                                                          array('sf_method' => array('post')),
                                                          array('model' => 'DRMDetail',
                                                                'type' => 'object',
                                                                'must_be_valid' => false,
                                                                'must_be_not_valid' => true
                                                                    )));*/

        /*$r->prependRoute('drm_vrac', new DRMRoute('/drm/:identifiant/edition/:periode_version/vrac', 
                                                          array('module' => 'drm_vrac', 
                                                                'action' => 'index'),
                                                          array('sf_method' => array('get','post')),
                                                          array('model' => 'DRM',
                                                                'type' => 'object',
                                                                'must_be_valid' => false,
                                                                'must_be_not_valid' => true)));

         */


	$r->prependRoute('drm_edition', new DRMRoute('/drm/:identifiant/edition/:periode_version/edition',

                        array('module' => 'drm_edition',
                            'action' => 'index'),
                        array('sf_method' => array('get', 'post')),
                        array('model' => 'DRM',
                            'type' => 'object',
                            'control' => array('edition'),
                )));
        
        
        /*$r->prependRoute('drm_pdf_facture', new DRMRoute('/drm/:identifiant/facture/:periode_version/pdf', 
                                    array('module' => 'drm_pdf', 
                                        'action' => 'generatePdfFacture'),
                                    array('sf_method' => array('get','post')),
                                    array('model' => 'DRM',
                                        'type' => 'object',
                                        'must_be_valid' => false,
                                        'must_be_not_valid' => true
                                        ))); */

        $r->prependRoute('drm_edition_detail', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/edition/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail',
                        array('module' => 'drm_edition',
                            'action' => 'detail'),
                        array('sf_method' => array('get')),
                        array('model' => 'DRMDetail',
                            'type' => 'object',
                            'control' => array('edition'),
                )));

        $r->prependRoute('drm_edition_produit_ajout', new DRMRoute('/drm/:identifiant/edition/:periode_version/edition/produit-ajout',
                array('module' => 'drm_edition',
                    'action' => 'produitAjout'),
                array('sf_method' => array('get', 'post')),
                array('model' => 'DRMAppellation',
                    'type' => 'object',
                    'add_noeud' => true,
                    'control' => array('edition'),
        )));

        $r->prependRoute('drm_edition_update', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/edition/update/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail',
                                                          array('module' => 'drm_edition',
                                                                'action' => 'update'),
                                                          array('sf_method' => array('post')),
                                                          array('model' => 'DRMDetail',
                                                                'type' => 'object',
                                                                'control' => array('edition'),
                                                                    )));
        
        /*
        $r->prependRoute('drm_vrac_ajout_contrat', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/vrac/contrat/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/ajout/:detail',
                        array('module' => 'drm_vrac',
                            'action' => 'nouveauContrat',
                            'detail' => null),
                        array('sf_method' => array('post', 'get')),
                        array('model' => 'DRMDetail',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));
        $r->prependRoute('drm_vrac_update_volume', new DRMVracDetailRoute('/drm/:identifiant/edition/:periode_version/vrac/update/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail/volume/:contrat',
                        array('module' => 'drm_vrac',
                            'action' => 'updateVolume'),
                        array('sf_method' => array('post')),
                        array('model' => 'acCouchdbJson',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));

        $r->prependRoute('drm_delete_vrac', new DRMVracDetailRoute('/drm/:identifiant/edition/:periode_version/vrac/update/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail/delete/:contrat',
                        array('module' => 'drm_vrac',
                            'action' => 'deleteVrac'),
                        array('sf_method' => array('post', 'get')),
                        array('model' => 'acCouchdbJson',
                            'type' => 'object',
                            'must_be_valid' => false,
                            'must_be_not_valid' => true
                )));*/
        
        $r->prependRoute('drm_vrac_details', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/details-vrac/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail', 
                                                    array('module' => 'drm_vrac_details', 
                                                        'action' => 'produit'),
                                                    array('sf_method' => array('get','post')),
                                                    array('model' => 'DRM',
                                                        'type' => 'object',
                                                        'control' => array('edition'))));
  
        
        $r->prependRoute('drm_export_details', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/details-export/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail', 
                                                    array('module' => 'drm_export_details', 
                                                        'action' => 'produit'),
                                                    array('sf_method' => array('get','post')),
                                                    array('model' => 'DRM',
                                                        'type' => 'object',
                                                        'control' => array('edition'))));
        
        $r->prependRoute('drm_cooperative_details', new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/details-cooperative/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail', 
                                                    array('module' => 'drm_cooperative_details', 
                                                        'action' => 'produit'),
                                                    array('sf_method' => array('get','post')),
                                                    array('model' => 'DRM',
                                                        'type' => 'object',
                                                        'control' => array('edition'))));

        $r->prependRoute('drm_edition_produit_addlabel', 
			 new DRMDetailRoute('/drm/:identifiant/edition/:periode_version/addlabel/:certification/:genre/:appellation/:mention/:lieu/:couleur/:cepage/:detail',
					    array('module' => 'drm_edition',
						  'action' => 'addLabel'),
					    array('sf_method' => array('get','post')),
					    array('model' => 'DRM',
						  'type' => 'object',
						  'control' => array('edition'))));
	
    }

}
