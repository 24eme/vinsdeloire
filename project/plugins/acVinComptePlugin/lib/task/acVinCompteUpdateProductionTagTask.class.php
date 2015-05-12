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
 * acVinComptePlugin task.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCompteUpdateProductionTagTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_OPTIONAL, 'Campagne', null),
            new sfCommandOption('reinitialisation_tags_produit', null, sfCommandOption::PARAMETER_REQUIRED, 'Reset tags', false),
            new sfCommandOption('reinitialisation_tags_export', null, sfCommandOption::PARAMETER_REQUIRED, 'Reset tags', false),
        ));

        $this->namespace        = 'compte';
        $this->name             = 'updateProductionTag';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $context = sfContext::createInstance($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if(!isset($options['campagne']) || !$options['campagne']) {
            $campagne = ConfigurationClient::getInstance()->getCurrentCampagne();
        } else {
            $campagne = $options['campagne'];
        }

        $this->logSection("campagne use", $campagne);

        foreach(EtablissementAllView::getInstance()->findByInterproStatutAndFamilleVIEW('INTERPRO-inter-loire', EtablissementClient::STATUT_ACTIF,null) as $e) {
            $id = $e->key[EtablissementAllView::KEY_ETABLISSEMENT_ID];
            $tags = array('export' => array(), 'produit' => array());
            
            $mvts = SV12MouvementsConsultationView::getInstance()->getByIdentifiantAndCampagne($id, $campagne);
            foreach($mvts as $m) {
                $produit_libelle = $this->getProduitLibelle($m->produit_hash);
      	        $tags['produit'][$produit_libelle] = 1;
            }
            $mvts = DRMMouvementsConsultationView::getInstance()->getByIdentifiantAndCampagne($id, $campagne);
            foreach($mvts as $m) {
                $produit_libelle = $this->getProduitLibelle($m->produit_hash);
              	$tags['produit'][$produit_libelle] = 1;
              	if ($m->detail_libelle && $m->type_libelle == 'Export') {
              	  $tags['export'][$m->detail_libelle] = 1;
              	}
            }   
            $etablissement = EtablissementClient::getInstance()->findByIdentifiant(str_replace('ETABLISSEMENT-', '' ,$id));
            if (!$etablissement) {
      	        throw new sfException("etablissement $id non trouvé");
            }
            $compte = $etablissement->getContact();
            if($options['reinitialisation_tags_export']) {
                $compte->tags->remove('export');
                $this->logSection("reset tags export", $compte->identifiant);
            }
            if($options['reinitialisation_tags_produit']) {
                $compte->tags->remove('produit');
                $this->logSection("reset tags produit", $compte->identifiant);
            }
            if (!count($tags)) {
      	        continue;
            }
            foreach ($tags as $type => $ttags) {
              	foreach ($ttags as $t => $null) {
              	  $compte->addTag($type, $t);
              	}
            }
            $compte->save();
            $this->logSection("done", $compte->identifiant);
        }
    }

    public function getProduitLibelle($hash) {
        $configuration =  ConfigurationClient::getInstance()->getCurrent();

        return $configuration->get($hash)->getLibelleFormat(array(), "%format_libelle%");
    }
}
