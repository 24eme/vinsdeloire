<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DRMImportCsvEdi
 *
 * @author mathurin
 */
class DRMImportCsvEdi extends DRMCsvEdi {

    protected $configuration = null;
    protected $mouvements = array();
    protected $csvDoc = null;
    protected $fromEdi = false;
    protected $noSave = false;
    protected $drmPrecedente = null;
    protected $etablissement = null;

      public function __construct($file, DRM $drm = null, $fromEdi = false) {
            $this->fromEdi = $fromEdi;
            $this->external_id = false;
            if($this->fromEdi){
              parent::__construct($file, $drm);
              $drmInfos = $this->getDRMInfosFromFile();
              if(!$drmInfos){
                  throw new sfException("La DRM n'a pu être initialisée depuis le fichier csv : l'identifiant ou/et la periode n'ont pas été trouvés");
              }
              try{
                $drm = DRMClient::getInstance()->findOrCreateFromEdiByIdentifiantAndPeriode($drmInfos['identifiant'],$drmInfos['periode'], true);
              }catch(sfException $e){
                echo "\"#Niveau erreur\";\"Numéro ligne de l'erreur\";\"Parametre en erreur \";\"Diagnostic\"\n";
                echo "Error;1;".$drmInfos['identifiant'].";Le numéro de compte n'est pas connu\n";
                return;
              }
            }

            $this->etablissement = $drm->getEtablissementObject();
            $this->societe = $this->etablissement->getSociete();

            $this->initConf($drm);
            if(is_null($this->csvDoc)) {
                $this->csvDoc = CSVClient::getInstance()->createOrFindDocFromDRM($file, $drm);
            }
            $this->drmPrecedente = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($drm->identifiant, DRMClient::getInstance()->getPeriodePrecedente($drm->periode));
            parent::__construct($file, $drm);
        }

        private function getDRMInfosFromFile(){
          if($this->getCsv()){
            foreach ($this->getCsv() as $keyRow => $csvRow) {
              if((KeyInflector::slugify($csvRow[self::CSV_TYPE]) == self::TYPE_CAVE)
              || (KeyInflector::slugify($csvRow[self::CSV_TYPE]) == self::TYPE_CRD)
              || (KeyInflector::slugify($csvRow[self::CSV_TYPE]) == self::TYPE_ANNEXE)){
                if (!preg_match('/^[0-9]{8}$/', KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]))) {
                  continue;
                }
                if (!preg_match('/^[0-9]{6}$/', KeyInflector::slugify($csvRow[self::CSV_PERIODE]))) {
                    continue;
                }
                $identifiantCsv = KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]);
                if(preg_match('/^[0-9]{6}$/',$identifiantCsv)){
                  $identifiantCsv=intval(sprintf("%06d",$identifiantCsv)."01");
                }
                return array('identifiant' => sprintf("%08d",$identifiantCsv), 'periode' => KeyInflector::slugify($csvRow[self::CSV_PERIODE]));
              }
            }
          }
          return null;
        }

        public function getDrm(){
          return $this->drm;
        }

        public function getCsvDoc() {

            return $this->csvDoc;
        }

        public function getCsvArrayErreurs(){
          $csvErreurs = array();
          $csvErreurs[] = array("#Niveau erreur","Numéro ligne de l'erreur","Parametre en erreur ","Diagnostic");
          if($this->getCsvDoc()->hasErreurs()){
            $erreursRows = $this->getCsvDoc()->getErreurs();
            foreach ($erreursRows as $erreur) {
              $erreurLevel = ($erreur->exist('level'))? CSVClient::$levelErrorsLibelle[$erreur->level] : "";
              $csvErreurs[] = array($erreurLevel,"".$erreur->num_ligne,"".$erreur->csv_erreur,$erreur->diagnostic);
            }
          }
          return $csvErreurs;
        }

        protected function initConf($drm) {
            $this->configuration = ConfigurationClient::getCurrent();
            if($drm) {
                $this->configuration = $drm->getConfig();
            }
            $this->mouvements = $this->buildAllMouvements();
        }

        public function getDocRows() {
            return $this->getCsv($this->csvDoc->getFileContent());
        }

        /**
         * CHECK DU CSV
         */
        public function checkCSV() {
            $this->createCacheProduits();
            $this->csvDoc->clearErreurs();
            $this->checkCSVIntegrity();
            if ($this->csvDoc->hasErreurs(self::STATUT_ERROR)) {
                $this->csvDoc->setStatut(self::STATUT_ERROR);
                $this->csvDoc->save();
                return;
            }
            // Check annexes
            $this->checkImportAnnexesFromCSV();
            // Check mouvements
            $this->checkImportMouvementsFromCSV();
            // Check Crds
            $this->checkImportCrdsFromCSV();
            if ($this->csvDoc->hasErreurs(self::STATUT_ERROR)) {
                $this->csvDoc->setStatut(self::STATUT_ERROR);
                $this->csvDoc->save();
                return;
            }
            if ($this->csvDoc->hasErreurs(self::STATUT_WARNING)) {
                $this->csvDoc->setStatut(self::STATUT_WARNING);
                $this->csvDoc->save();
                return;
            }
            $this->csvDoc->setStatut(self::STATUT_VALIDE);
            $this->csvDoc->save();
        }
        private function getCacheKeyFromData($datas) {
            return $datas[self::CSV_CAVE_CERTIFICATION].'-'.
                	$datas[self::CSV_CAVE_GENRE].'-'.
                	$datas[self::CSV_CAVE_APPELLATION].'-'.
                	$datas[self::CSV_CAVE_MENTION].'-'.
                	$datas[self::CSV_CAVE_LIEU].'-'.
                	$datas[self::CSV_CAVE_COULEUR].'-'.
                	$datas[self::CSV_CAVE_CEPAGE].'-'.
                    $datas[self::CSV_CAVE_LIBELLE_COMPLEMENTAIRE].'-'.
                    $datas[self::CSV_CAVE_LIBELLE_COMPLET].'-'.
                    $datas[self::CSV_CAVE_TYPE_DRM];
        }

        public function createCacheProduits() {
            $this->cache = array();
            $this->cache2datas = array();

            if ($this->drm->canSetStockDebutMois()) {
                $this->drm->remove('declaration');
                $this->drm->add('declaration');
            }


            foreach ($this->getDocRows() as $datas) {
              if (KeyInflector::slugify(trim($datas[self::CSV_TYPE])) != self::TYPE_CAVE) {
                  continue;
              }
              if (strtolower($datas[self::CSV_CAVE_CATEGORIE_MOUVEMENT] != 'stocks_debut')) {
                  continue;
              }
              $cacheid = $this->getCacheKeyFromData($datas);
              if (isset($this->cache2datas[$cacheid])) {
                  continue;
              }
              if($datas[self::CSV_CAVE_VOLUME] === "") {
                  continue;
              }
              $this->cache2datas[$cacheid] = $datas;
              $this->cache2datas[$cacheid][self::CSV_CAVE_VOLUME] = $this->convertNumber($this->cache2datas[$this->getCacheKeyFromData($datas)][self::CSV_CAVE_VOLUME]);
            }

            $cacheProduitTav = array();
            # Premier parcours des lignes du csv pour créer un tableau de hashage : produit / tav
            foreach ($this->getDocRows() as $datas) {
              if (KeyInflector::slugify(trim($datas[self::CSV_TYPE])) != self::TYPE_CAVE) {
                  continue;
              }
              if(strtoupper(KeyInflector::slugify($datas[self::CSV_CAVE_CATEGORIE_MOUVEMENT])) != self::COMPLEMENT){
                  continue;
              }
              if(strtoupper(KeyInflector::slugify($datas[self::CSV_CAVE_TYPE_COMPLEMENT_PRODUIT])) != self::COMPLEMENT_TAV) {
                  continue;
              }
              $tav = $this->convertNumber($datas[self::CSV_CAVE_VALEUR_COMPLEMENT_PRODUIT]);
              if(!$tav) {
                  continue;
              }
              $cacheProduitTav[$this->getCacheKeyFromData($datas)] = $tav;
            }

            foreach ($this->getDocRows() as $datas) {
                if (!isset($all_produits)) {
                    $all_produits = $this->configuration->declaration->getProduits($datas[self::CSV_PERIODE]);
                }
                if (preg_match('/^(...)?#/', $datas[self::CSV_TYPE])) {
                    continue;
                }
                if (strtoupper($datas[self::CSV_TYPE]) != self::TYPE_CAVE) {
                    continue;
                }
                if (isset($this->cache[$this->getCacheKeyFromData($datas)])) {
                    continue;
                }

                /// RECHECHE DE PRODUIT
                $csvLibelleProductArray = $this->buildLibellesArrayWithRow($datas, true);
                $csvLibelleProductComplet = $this->slugifyProduitArrayOrString($csvLibelleProductArray);
                $founded_produit = false;
                $is_default_produit = false;

                $tav = isset($cacheProduitTav[$this->getCacheKeyFromData($datas)]) ? $cacheProduitTav[$this->getCacheKeyFromData($datas)] : null;

                if ($idDouane = $this->getIdDouane($datas)) {
                    $produits = $this->configuration->identifyProductByCodeDouane($idDouane);
                    if (count($produits) == 1) {
                        $founded_produit = $produits[0];
                    } else {
                        $libelle = preg_replace('/([a-zA-Z0-9\ \-\_]*)\(([a-zA-Z0-9\ \-\_]*)\)/', '${1}', trim($datas[self::CSV_CAVE_LIBELLE_COMPLET]));
                        foreach($produits as $p) {
                            if (!$founded_produit) {
                                $founded_produit = $p;
                            }
                            if (KeyInflector::slugify(str_replace(" ", "", $p->getLibelleFormat())) == KeyInflector::slugify(str_replace(" ", "", $libelle))) {
                                $founded_produit = $p;
                                break;
                            }
                        }
                    }
                }

                if (!$founded_produit) {
                    foreach ($all_produits as $produit) {
                        if ($founded_produit) {
                            break;
                        }
                        $produitConfLibelleAOC = $this->slugifyProduitConf($produit);
                        $produitConfLibelleAOP = $this->slugifyProduitConf($produit,true);
                        $libelleCompletConfAOC = $this->slugifyProduitArrayOrString($produitConfLibelleAOC);
                        $libelleCompletConfAOP = $this->slugifyProduitArrayOrString($produitConfLibelleAOP);
                        $libelleCompletEnCsv = $this->slugifyProduitArrayOrString($datas[self::CSV_CAVE_LIBELLE_COMPLET]);

                        $isEmptyArray = $this->isEmptyArray($csvLibelleProductArray);
                        if ($isEmptyArray){
                          if(($libelleCompletConfAOC != $csvLibelleProductComplet) && ($libelleCompletConfAOP != $csvLibelleProductComplet)
                          && ($libelleCompletConfAOC != $libelleCompletEnCsv) && ($libelleCompletConfAOP != $libelleCompletEnCsv)
                          && ($this->slugifyProduitArrayOrString($produit->getLibelleFormat()) != $libelleCompletEnCsv)) {
                            continue;
                          }
                        }elseif((count(array_diff($csvLibelleProductArray, $produitConfLibelleAOC))) && (count(array_diff($csvLibelleProductArray, $produitConfLibelleAOP)))

                            && ($libelleCompletConfAOC != $csvLibelleProductComplet) && ($libelleCompletConfAOP != $csvLibelleProductComplet)
                            && ($libelleCompletConfAOC != $libelleCompletEnCsv) && ($libelleCompletConfAOP != $libelleCompletEnCsv)
                            && ($this->slugifyProduitArrayOrString($produit->getLibelleFormat()) != $libelleCompletEnCsv)) {
                            continue;
                        }elseif((count(array_diff($produitConfLibelleAOC, $csvLibelleProductArray))) && (count(array_diff($produitConfLibelleAOP, $csvLibelleProductArray)))
                            && ($libelleCompletConfAOC != $csvLibelleProductComplet) && ($libelleCompletConfAOP != $csvLibelleProductComplet)
                            && ($libelleCompletConfAOC != $libelleCompletEnCsv) && ($libelleCompletConfAOP != $libelleCompletEnCsv)
                            && ($this->slugifyProduitArrayOrString($produit->getLibelleFormat()) != $libelleCompletEnCsv)) {
                            continue;
                        }
                        $founded_produit = $produit;
                    }
                }

                //Gestion du produit non connu
                if((!$founded_produit)  && ($default_produit_inao = $this->getIdDouane($datas))) {
                    $is_default_produit = true;
                    if (preg_match('/(.*[^ ]) *\(([^\)]+)\)/', $datas[self::CSV_CAVE_LIBELLE_COMPLET], $m)) {
                        $default_produit_libelle = $m[1];
                    }else{
                        $default_produit_libelle = $datas[self::CSV_CAVE_LIBELLE_COMPLET];
                    }
                    $default_produit_hash = self::getEdiDefaultFromInao($default_produit_inao);
                    $founded_produit = $this->configuration->get($default_produit_hash);
                }

                $denomination_complementaire = (trim($datas[self::CSV_CAVE_LIBELLE_COMPLEMENTAIRE]))? trim($datas[self::CSV_CAVE_LIBELLE_COMPLEMENTAIRE]) : false;
                if ($is_default_produit) {
                    $denomination_complementaire = ($denomination_complementaire) ? $default_produit_libelle." ".$denomination_complementaire : $default_produit_libelle;
                }
                if (!$founded_produit) {
                    continue;
                }
                /// CREATION DU DETAILS
                $produit =  $this->drm->addProduit($founded_produit->getHash(),DRMClient::$types_node_from_libelles[KeyInflector::slugify(strtoupper($datas[self::CSV_CAVE_TYPE_DRM]))], $denomination_complementaire, $tav);


                //Gestion du produit non connu
                if ($is_default_produit) {
                    $produit->code_inao = $default_produit_inao;
                    $produit->produit_libelle = $default_produit_libelle;
                }

                $cacheid = $this->getCacheKeyFromData($datas);
                $this->cache[$cacheid] = $produit;
                if (!isset($this->cache[$cacheid])) {
                    $this->cache2datas[$cacheid] = $datas;
                }
                $this->cache2datas[$cacheid]['hash'] = $founded_produit->getHash();
                $this->cache2datas[$cacheid]['hash_detail'] = $produit->getHash();
                $this->cache2datas[$cacheid]['details_type'] = DRMClient::$types_node_from_libelles[KeyInflector::slugify(strtoupper($datas[self::CSV_CAVE_TYPE_DRM]))];
                $this->cache2datas[$cacheid]['denomination_complementaire'] = $denomination_complementaire;
                $this->cache2datas[$cacheid]['libelle'] = $datas[self::CSV_CAVE_LIBELLE_COMPLET];
                $this->cache2datas[$cacheid]['tav'] = $tav;
            }
            //avec le reorder, les référence vers les details sautent, on les re-récupère donc ici :
            // (il est possible que le produit ait du tav ou du volume mais ne soit pas reconnu, donc il faut le supprimer du cache => $delete)
            $delete = array();
            foreach($this->cache2datas as $cacheid => $params) {
                if (isset($params['hash_detail']) && $params['hash_detail']) {
                    $this->cache[$cacheid] = $this->drm->get($params['hash_detail']);
                }else{
                    $delete[] = $cacheid;
                }
            }
            foreach($delete as $cacheid){
                unset($this->cache2datas[$cacheid]);
                unset($this->cache[$cacheid]);

            }

            if($this->drm->isMoisOuvert()) {
                return;
            }

            //on prépare les vérifications
            $check = array();
            foreach ($this->cache as $cacheid => $produit) {
                if (!isset($check[$produit->getHash()])) {
                    $check[$produit->getHash()] = array();
                }
                $check[$produit->getHash()][$cacheid] = 1;
            }
            // Cas d'un nouveau produit avec label ou complement et où un produit DEFAUT existe
            foreach ($check as $hash => $array) {
                if (count($array) <= 1) {
                    continue;
                }
                ksort($array);
                $isfirst = true;
                foreach($array as $cacheid => $null) {
                    if ($isfirst) {
                        $isfirst = false;
                        continue;
                    }
                    $p = $this->drm->addProduit($this->cache2datas[$cacheid]['hash'], $this->cache2datas[$cacheid]['details_type'], $this->cache2datas[$cacheid]['denomination_complementaire'], $this->cache2datas[$cacheid]['tav']);
                    $p->produit_libelle = $this->cache2datas[$cacheid]['libelle'];
                    $this->cache[$cacheid] = $p;
                }
            }
            $couleurs = array();
            foreach ($this->cache as $cacheid => $produit) {
                if (!isset($couleurs[$produit->getCouleur()->getHash()])) {
                    $couleurs[$produit->getCouleur()->getHash()] = array();
                }
                $couleurs[$produit->getCouleur()->getHash()][$cacheid] = 1;
            }
            //avec le reorder, les référence vers les details sautent, on les re-récupère donc ici :
            foreach($this->cache2datas as $cacheid => $params) {
                $this->cache[$cacheid] = $this->drm->get($params['hash_detail']);
            }
            //gestion des multidetails sur la base stock final de la DRM précédente
            //+ preparation de la comparaison tav + denom
            $cepagedenomtav = array();
            foreach($couleurs as $hash => $array_cache) {
                $volume2hash = array();
                    if($this->drmPrecedente && $this->drmPrecedente->exist($hash)) {
                    foreach($this->drmPrecedente->get($hash)->getProduits() as $k => $p) {
                        if(!$this->drm->getConfig()->exist($p->getHash()) || !$this->drm->getConfig()->get($p->getHash())->isActif($this->drm->getDate())) {
                            continue;
                        }
                        foreach($p->getProduitsDetails(true) as $kd => $d) {
                            //préparation de l'étape suivante sur la comparaison sur la base du tav et de la denom
                            if ($d->denomination_complementaire || ($d->exist('tav') && $d->tav) ) {
                                $cepagedenomtav[$d->getCepage()->getHash().'-'.$d->getParent()->getKey().'-'.$d->denomination_complementaire.'-'.$d->tav] = $d->getHash();
                            }

                            $total_fin_mois = $d->stocks_fin->revendique * 1;
                            if (!$total_fin_mois) {
                                continue;
                            }
                            if (!isset($volume2hash["$total_fin_mois"])) {
                                $volume2hash["$total_fin_mois"] = array();
                            }
                            $volume2hash["$total_fin_mois"][$d->getHash()] = 1;
                        }
                    }
                }
                foreach($array_cache as $cacheid => $null)  {
                    $total_debut_mois = 0;
                    if (isset($this->cache2datas[$cacheid][self::CSV_CAVE_VOLUME])) {
                        $total_debut_mois = $this->cache2datas[$cacheid][self::CSV_CAVE_VOLUME] * 1;
                    }
                    if (!$total_debut_mois) {
                        continue;
                    }
                    if (!isset($volume2hash["$total_debut_mois"]))  {
                        continue;
                    }
                    if (isset($volume2hash["$total_debut_mois"][$this->cache[$cacheid]->getHash()])) {
                        continue;
                    }
                    if (count(array_keys($volume2hash["$total_debut_mois"])) > 1) {
                        $current_cepage_hash = $this->cache[$cacheid]->getCepage()->getHash();
                        $nb = 0;
                        foreach(array_keys($volume2hash["$total_debut_mois"]) as $a_hash_volume) {
                            if (preg_replace('/.details.[^\/]*$/', '', $a_hash_volume) == $current_cepage_hash) {
                                $nb++;
                                $new_hash = $a_hash_volume;
                            }
                        }
                        if ($nb === 1) {
                            unset($volume2hash["$total_debut_mois"][$new_hash]);
                        }else{
                            continue;
                        }
                    }else{
                        $new_hashes = array_keys($volume2hash["$total_debut_mois"]);
                        $new_hash = array_shift($new_hashes);
                        unset($volume2hash["$total_debut_mois"][$new_hash]);
                    }
                    if (isset($this->cache2datas[$cacheid]['tav'])  && (
                       ! $this->drmPrecedente->exist($new_hash)
                    || ! $this->drmPrecedente->get($new_hash)->exist('tav')
                    || ($this->cache2datas[$cacheid]['tav'] != $this->drmPrecedente->get($new_hash)->tav)
                    ) ) {
                        continue;
                    }
                    if (!$this->drmPrecedente->exist($this->cache[$cacheid]->getCepage()->getHash())
                       || !$this->drmPrecedente->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($this->cache2datas[$cacheid]['details_type'])
                       || !$this->drmPrecedente->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($this->cache2datas[$cacheid]['details_type'])->exist($this->cache[$cacheid]->getKey())
                       ) {
                        if ($this->drm->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($this->cache2datas[$cacheid]['details_type'])
                            && $this->drm->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($this->cache2datas[$cacheid]['details_type'])->exist($this->cache[$cacheid]->getKey())) {
                                $this->drm->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($this->cache2datas[$cacheid]['details_type'])->remove($this->cache[$cacheid]->getKey());
                        }
                    }
                    $detail = $this->drm->getOrAdd($new_hash);
                    $detail->getParent()->createESDetails($detail);
                    $this->cache[$cacheid] = $detail;
                    $this->cache2datas[$cacheid]['founded_produit'] = $this->cache[$cacheid]->getConfig();
                    $this->cache2datas[$cacheid]['hash'] = $this->cache2datas[$cacheid]['founded_produit']->getHash();
                    $this->cache2datas[$cacheid]['hash_detail'] = $this->cache[$cacheid]->getHash();
                }
            }
            //avec le reorder, les référence vers les details sautent, on les re-récupère donc ici :
            foreach($this->cache2datas as $cacheid => $params) {
                $this->cache[$cacheid] = $this->drm->getOrAdd($params['hash_detail']);
            }
            // On tente une dernière mise en cohérence en comparant les denomination complémentaires
            // et les tav de la drm preecente
            foreach($this->cache2datas as $cacheid => $cachedata) {
                if (!$cachedata['denomination_complementaire'] && !(isset($cachedata['tav']) && $cachedata['tav'])) {
                   continue;
                }
                $id_cepagedenomtav = $this->cache[$cacheid]->getCepage()->getHash().'-'.$cachedata['details_type'].'-'.$cachedata['denomination_complementaire'].'-'.$cachedata['tav'];
                if (!isset($cepagedenomtav[$id_cepagedenomtav])) {
                    continue;
                }
                if (!$this->drmPrecedente->exist($this->cache[$cacheid]->getCepage()->getHash())
                   || !$this->drmPrecedente->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($cachedata['details_type'])
                   || !$this->drmPrecedente->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($cachedata['details_type'])->exist($this->cache[$cacheid]->getKey())
                   ) {
                    if ($this->drm->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($cachedata['details_type'])) {
                        $this->drm->get($this->cache[$cacheid]->getCepage()->getHash())->getDetailsNoeud($cachedata['details_type'])->remove($this->cache[$cacheid]->getKey());
                    }
                }
                $detail = $this->drm->getOrAdd($cepagedenomtav[$id_cepagedenomtav]);
                $detail->getParent()->createESDetails($detail);
                $this->cache[$cacheid] = $detail;
                $this->cache2datas[$cacheid]['founded_produit'] = $this->cache[$cacheid]->getConfig();
                $this->cache2datas[$cacheid]['hash'] = $this->cache2datas[$cacheid]['founded_produit']->getHash();
                $this->cache2datas[$cacheid]['hash_detail'] = $this->cache[$cacheid]->getHash();
            }
            //avec le reorder, les référence vers les details sautent, on les re-récupère donc ici :
            foreach($this->cache2datas as $cacheid => $params) {
                if (isset($params['hash_detail'])) {
                    $this->cache[$cacheid] = $this->drm->get($params['hash_detail']);
                }
            }

            //DEBUG
            if (false) {
                echo "cache content :<br/>";
                foreach($this->cache2datas as $cacheid => $params) {
                    echo "$cacheid : ".$this->cache2datas[$cacheid]['hash_detail']."(".$this->cache2datas[$cacheid]['tav'].")<br/>\n";
                }
                echo "FIN";
                exit;
            }

        }

        public function getProduitFromCache($datas) {
            if (!isset($this->cache[$this->getCacheKeyFromData($datas)])) {
                return null;
            }
            return $this->cache[$this->getCacheKeyFromData($datas)];
        }

        /**
         * IMPORT DEPUIS LE CSV
         */
         public function importCSV($withSave = true) {
             $this->createCacheProduits();
             $this->importMouvementsFromCSV();
             $this->importCrdsFromCSV();
             $this->importAnnexesFromCSV();
             $this->drm->etape = ($this->fromEdi)? DRMClient::ETAPE_VALIDATION_EDI : DRMClient::ETAPE_VALIDATION;
             $this->drm->type_creation = DRMClient::DRM_CREATION_EDI;
             $this->drm->buildFavoris();
             $this->drm->storeDeclarant();
             $this->drm->initSociete();
             $this->updateAndControlCoheranceStocks();

             if($withSave) {
                 $this->drm->save();
             }
         }

         public function updateAndControlCoheranceStocks() {
             /*$stocks = array();
             foreach($this->drm->getProduitsDetails() as $detail) {
               $stocks[$detail->getHash()] = $detail->stocks_fin->final;
             }*/

             $this->drm->update();
             $this->drm->updateStockFinDeMoisAllCrds();

             /*foreach($this->drm->getProduitsDetails() as $detail) {
                 if(!array_key_exists($detail->getHash(), $stocks) || is_null($stocks[$detail->getHash()])) {
                     continue;
                 }

                 if(round($stocks[$detail->getHash()], 2) == round($detail->stocks_fin->final, 2)) {
                     continue;
                 }
                 $this->csvDoc->addErreur($this->createError(1, sprintf("%s %0.2f hl (CSV) / %0.2f hl (calculé)", $detail->produit_libelle, $stocks[$detail->getHash()], $detail->stocks_fin->final), "Le stock fin de mois du CSV différent du calculé"));
             }*/

             if ($this->csvDoc->hasErreurs()) {
                 $this->csvDoc->setStatut(self::STATUT_WARNING);
                 $this->csvDoc->save();
             }
         }

        private function checkCSVIntegrity() {
            $ligne_num = 1;
            foreach ($this->getDocRows() as $csvRow) {
                if (($ligne_num == 1)
                && (KeyInflector::slugify($csvRow[self::CSV_TYPE]) != self::TYPE_CAVE)
                && (KeyInflector::slugify($csvRow[self::CSV_TYPE]) != self::TYPE_CRD)
                && (KeyInflector::slugify($csvRow[self::CSV_TYPE]) != self::TYPE_ANNEXE)) {
                    $ligne_num++;
                    continue;
                }
                if (!in_array(KeyInflector::slugify($csvRow[self::CSV_TYPE]), self::$permitted_types)) {
                    $this->csvDoc->addErreur($this->createWrongFormatTypeError($ligne_num, $csvRow));
                }
                if (!preg_match('/^[0-9]{6}$/', KeyInflector::slugify($csvRow[self::CSV_PERIODE]))) {
                    $this->csvDoc->addErreur($this->createWrongFormatPeriodeError($ligne_num, $csvRow));
                }
                if (!$this->external_id && !preg_match('/^[0-9]{8}[0-8\-]*$/', KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]))) {
                    $this->csvDoc->addErreur($this->createWrongNumeroCompteError($ligne_num, $csvRow));
                }
                if (KeyInflector::slugify($csvRow[self::CSV_NUMACCISE]) && !preg_match('/^FR[0-9A-Z]{11}$/', KeyInflector::slugify($csvRow[self::CSV_NUMACCISE]))) {
                    $this->csvDoc->addErreur($this->createWrongFormatNumAcciseError($ligne_num, $csvRow));
                }
                if(!$this->external_id && $this->drm->getIdentifiant() != KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]) && ($this->societe->identifiant != KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]) && (!$csvRow[self::CSV_NUMACCISE] || $this->etablissement->no_accises != $csvRow[self::CSV_NUMACCISE]))) {
                    $this->csvDoc->addErreur($this->otherNumeroCompteError($ligne_num, $csvRow));
                }
                if(!$this->external_id && $this->drm->getIdentifiant() != KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT])){
                  $this->csvDoc->addErreur($this->otherNumeroCompteError($ligne_num, $csvRow));
                }
                if($this->drm->getPeriode() != KeyInflector::slugify($csvRow[self::CSV_PERIODE])){
                  $this->csvDoc->addErreur($this->otherPeriodeError($ligne_num, $csvRow));
                }
                if($this->fromEdi && (!$this->drm || !$this->drm->isCreationEdi()) && !$this->noSave){
                  $this->csvDoc->addErreur($this->drmIsNotCreationEdiError($ligne_num, $csvRow));
                }

                $ligne_num++;
            }
        }

        private function checkImportMouvementsFromCSV() {
            return $this->importMouvementsFromCSV(true);
        }

        private function checkImportCrdsFromCSV() {
            return $this->importCrdsFromCSV(true);
        }

        private function checkImportAnnexesFromCSV() {
            return $this->importAnnexesFromCSV(true);
        }

        private function importMouvementsFromCSV($just_check = false) {
            $num_ligne = 1;
            $stocksDebutModifies = array();
            foreach ($this->getDocRows() as $csvRow) {
                if (KeyInflector::slugify($csvRow[self::CSV_TYPE] != self::TYPE_CAVE)) {
                    $num_ligne++;
                    continue;
                }
                $drmDetails = $this->getProduitFromCache($csvRow);

                if (!$drmDetails) {
                    $this->csvDoc->addErreur($this->productNotFoundError($num_ligne, $csvRow));
                    $num_ligne++;
                    continue;
                }
                $founded_produit = $drmDetails->getCepage()->getConfig();

                $cat_mouvement = KeyInflector::slugify($csvRow[self::CSV_CAVE_CATEGORIE_MOUVEMENT]);
                if(strtoupper(KeyInflector::slugify($cat_mouvement)) == self::COMPLEMENT){
                    $this->importComplementMvt($csvRow,$drmDetails,$just_check);
                    continue;
                }

                $type_mouvement = KeyInflector::slugify($csvRow[self::CSV_CAVE_TYPE_MOUVEMENT]);
                $detailNode = DRMClient::$types_node_from_libelles[strtoupper($csvRow[self::CSV_CAVE_TYPE_DRM])];
                if (!array_key_exists($cat_mouvement, $this->mouvements[$detailNode])) {
                    $this->csvDoc->addErreur($this->categorieMouvementNotFoundError($num_ligne, $csvRow));
                    $num_ligne++;
                    continue;
                }
                if (!array_key_exists($type_mouvement, $this->mouvements[$detailNode][$cat_mouvement])) {
                    $this->csvDoc->addErreur($this->typeMouvementNotFoundError($num_ligne, $csvRow));
                    $num_ligne++;
                    continue;
                }

                $confDetailMvt = $this->mouvements[$detailNode][$cat_mouvement][$type_mouvement];

                if(!$confDetailMvt->isWritableForEtablissement($this->etablissement)) {
                    $this->csvDoc->addErreur($this->typeMouvementCompatibiliteError($num_ligne, $csvRow));
                    $num_ligne++;
                    continue;
                }

                $cat_key = $confDetailMvt->getParent()->getKey();
                $type_key = $confDetailMvt->getKey();
                if ($confDetailMvt->hasDetails() && $type_key == 'vrac' || $type_key == 'contrat') {
                  $identifiantContrat = $this->findContratDocId($csvRow);
                  if($identifiantContrat){
                      $vracObj = VracClient::getInstance()->find($identifiantContrat);
                      if(($vracObj->getVendeurIdentifiant() != $csvRow[self::CSV_IDENTIFIANT]) && !$this->noSave){
                        $this->csvDoc->addErreur($this->vracNotFoundError($num_ligne, $csvRow));
                        $num_ligne++;
                        continue;
                      }
                      if(preg_replace('/ /', "", $founded_produit->getLibelleFormat()) != preg_replace('/ /', "",$vracObj->getConfigProduit()->getLibelleFormat())){
                        $this->csvDoc->addErreur($this->vracProduitNotConformError($num_ligne, $csvRow));
                        $num_ligne++;
                        continue;
                      }
                      $isRaisinMout = (($vracObj->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS) ||
                              ($vracObj->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS));
                      if($isRaisinMout){
                        $this->csvDoc->addErreur($this->vracTypeNotConformError($num_ligne, $csvRow));
                        $num_ligne++;
                        continue;
                      }
                   }
                }

                if (!$just_check) {
                    $detailTotalVol = $this->convertNumber($csvRow[self::CSV_CAVE_VOLUME]);
                    $volume = $this->convertNumber($csvRow[self::CSV_CAVE_VOLUME]);


                    if($cat_key == "stocks_debut" && !$drmDetails->canSetStockDebutMois()) {
                        continue;
                    }

                    if (preg_match('/^2\d\d\d-\d\d-\d\d$/', $csvRow[self::CSV_CAVE_EXPORTPAYS])) {
                      $drmDetails->add("replacement_date", $csvRow[self::CSV_CAVE_EXPORTPAYS]);
                      $drmDetails->add('observations', $type_key);
                    }

                    if ($confDetailMvt->hasDetails()) {
                        $detailTotalVol += $this->convertNumber($drmDetails->getOrAdd($cat_key)->getOrAdd($type_key));

                        if ($type_key == 'export') {
                            $pays = $this->findPays($csvRow[self::CSV_CAVE_EXPORTPAYS]);
                            $detailNode = $drmDetails->getOrAdd($cat_key)->getOrAdd($type_key . '_details')->getOrAdd($pays,null);
                            if ($detailNode->volume) {
                                $volume+=$detailNode->volume;
                            }
                            $date = new DateTime($this->drm->getDate());
                            $detailNode->volume = $volume;
                            $detailNode->identifiant = $pays;
                            $detailNode->date_enlevement = $date->format('Y-m-d');
                        }
                        if ($type_key == 'vrac' || $type_key == 'contrat') {
                            $identifiantContrat = $this->findContratDocId($csvRow);
                            if(!$identifiantContrat){
                              continue;
                            }
                            $detailNode = $drmDetails->getOrAdd($cat_key)->getOrAdd($type_key . '_details')->getOrAdd($identifiantContrat,null);
                            if ($detailNode->volume) {
                                $volume+=$detailNode->volume;
                            }
                            $date = new DateTime($this->drm->getDate());
                            $detailNode->volume = $volume;
                            $detailNode->identifiant = $identifiantContrat;
                            $detailNode->date_enlevement = $date->format('Y-m-d');
                        }
                    } else {
                        $oldVolume = $drmDetails->getOrAdd($cat_key)->getOrAdd($type_key);
			if($cat_key == "stocks_debut" && !isset($stocksDebutModifies[$drmDetails->getHash()])) {
                            $oldVolume = 0;
                            $stocksDebutModifies[$drmDetails->getHash()] = true;
                        }

                        $drmDetails->getOrAdd($cat_key)->add($type_key, $oldVolume + $detailTotalVol);
                    }
                } else {
                    if ($confDetailMvt->hasDetails()) {
                        if ($confDetailMvt->getKey() == 'export') {
                            $pays = $this->findPays($csvRow[self::CSV_CAVE_EXPORTPAYS]);
                            if (!$pays) {
                                $this->csvDoc->addErreur($this->exportPaysNotFoundError($num_ligne, $csvRow));
                            }
                            $num_ligne++;
                            continue;
                        }
                        if ($confDetailMvt->getKey() == 'vrac' || $confDetailMvt->getKey() == 'contrat') {
                            if (!$csvRow[self::CSV_CAVE_CONTRATID]) {
                                $this->csvDoc->addErreur($this->contratIDNotFoundError($num_ligne, $csvRow));
                                $num_ligne++;
                                continue;
                            }
                            $vrac_id = $this->findContratDocId($csvRow);
                            if(!$vrac_id) {
                              $this->csvDoc->addErreur($this->contratIDNotFoundError($num_ligne, $csvRow));
                              $num_ligne++;
                              continue;
                            }
                        }
                        $vrac_id = $this->findContratDocId($csvRow);

                        if(!$vrac_id) {
                          $this->csvDoc->addErreur($this->contratIDNotFoundError($num_ligne, $csvRow));
                          $num_ligne++;
                          continue;
                        }
                    }
                }
                $num_ligne++;
            }
        }

        private function importComplementMvt($csvRow, $drmDetails, $just_check  = false){
              $type_complement = strtoupper(KeyInflector::slugify($csvRow[self::CSV_CAVE_TYPE_COMPLEMENT_PRODUIT]));
                if(!in_array($type_complement, self::$types_complement)){
                  $this->csvDoc->addErreur($this->typeComplementNotFoundError($num_ligne, $csvRow));
                  $num_ligne++;
                  return;
                }
                $valeur_complement = $csvRow[self::CSV_CAVE_VALEUR_COMPLEMENT_PRODUIT];
                if(!$valeur_complement){
                  $this->csvDoc->addErreur($this->valueComplementVide($num_ligne, $csvRow));
                  $num_ligne++;
                  return;
                }

                if($type_complement == self::COMPLEMENT_PREMIX && $csvRow[self::CSV_CAVE_VALEUR_COMPLEMENT_PRODUIT] && !$drmDetails->isPremix()) {
                    $this->csvDoc->addErreur($this->typeComplementNotFoundError($num_ligne, $csvRow));
                    return;
                }
                if(!$just_check){
                  $valeur_complement = $csvRow[self::CSV_CAVE_VALEUR_COMPLEMENT_PRODUIT];
                  $value = null;
                  switch ($type_complement) {
                    case self::COMPLEMENT_TAV:
                      $value = $this->convertNumber($valeur_complement);
                      break;
                    case self::COMPLEMENT_OBSERVATIONS:
                      $value = $valeur_complement;
                      break;
                    case self::COMPLEMENT_PREMIX:
                      return;
                  }
                  $field = strtolower($type_complement);
                  $drmDetails->add($field, $value);
                }
        }

        private static function row2litrage($r) {
            return strtoupper(preg_replace("/[ _]/","",str_replace(",",".", preg_replace('/([0-9])_([0-9])/', '$1.$2', preg_replace('/^([^_]+)_(.*)/', '$2 $1', $r)))));
        }

        private static function cdrreversekeyid($regime, $genre, $couleur, $libelle) {
            if (!$couleur) {
                $couleur = 'DEFAUT';
            }
            return $regime.'-'.$genre.'-'.$couleur.'-'.self::row2litrage($libelle);
        }

        private function importCrdsFromCSV($just_check = false) {
            if ($this->drm->canSetStockDebutMois()) {
                $this->drm->remove('crds');
                $this->drm->add('crds');
            }
            $num_ligne = 1;

            $all_contenances_origine = sfConfig::get('app_vrac_contenances');
            $all_contenances = array();
            foreach ($all_contenances_origine as $contenance_key => $contenance) {
              $newKey = strtoupper(str_replace(" ","",str_replace(",",".",$contenance_key)));
              $all_contenances[$newKey] = $contenance;
            }
            $crd_precedente = array();
            if ($this->drmPrecedente) {
                foreach($this->drmPrecedente->crds as $regime => $crds) {
                    foreach($crds as $key => $crd) {
                        $kid = self::cdrreversekeyid($regime, $crd->genre, $crd->couleur, $crd->detail_libelle);
                        if (!isset($crd_precedente[$kid])) {
                            $crd_precedente[$kid] = array();
                        }
                        $crd_precedente[$kid][] = $crd->getKey();
                    }
                }
            }
            foreach ($this->getDocRows() as $csvRow) {
                if (KeyInflector::slugify($csvRow[self::CSV_TYPE] != self::TYPE_CRD)) {
                    $num_ligne++;
                    continue;
                }
                $crd_regime = DRMClient::convertCRDRegime($csvRow[self::CSV_CRD_REGIME]);
                if(!$crd_regime){
                  $crd_regime = ($this->etablissement->exist('crd_regime'))? $this->etablissement->get('crd_regime') : EtablissementClient::REGIME_CRD_COLLECTIF_SUSPENDU;
                }

                $genre = $this->convertGenre($csvRow[self::CSV_CRD_GENRE]);
                $couleur = $this->convertCouleur($csvRow[self::CSV_CRD_COULEUR]);
                $litrageLibelle = self::row2litrage($csvRow[self::CSV_CRD_CENTILITRAGE]);
                $categorie_key = $csvRow[self::CSV_CRD_CATEGORIE_KEY];
                $type_key = $csvRow[self::CSV_CRD_TYPE_KEY];
                $quantite = KeyInflector::slugify($csvRow[self::CSV_CRD_QUANTITE]);
                if($categorie_key == "stocks_debut"){ $categorie_key = 'stock_debut'; }
                if($categorie_key == "stocks_fin"){ $categorie_key = 'stock_fin'; }
                $fieldNameCrd = $categorie_key;

                if ($categorie_key != "stock_debut" && $categorie_key != "stock_fin") {
                    $fieldNameCrd.="_" . $type_key;
                }

                $centilitrage = isset($all_contenances[$litrageLibelle]) ? $all_contenances[$litrageLibelle] : null;
                $regimeNode = $this->drm->getOrAdd('crds')->getOrAdd($crd_regime);
                $keyNode = null;
                $reverseKey = self::cdrreversekeyid($regime, $genre, $couleur, $litrageLibelle);
                if (isset($crd_precedente[$reverseKey])) {
                    $keyNode = array_pop($crd_precedente[$reverseKey]);
                }
                if (!$keyNode) {
                    $keyNode = $regimeNode->constructKey($genre, $couleur, $centilitrage, $litrageLibelle);
                }

                $drmPrecedente = DRMClient::getInstance()->find("DRM-".$this->drm->identifiant."-".DRMClient::getInstance()->getPeriodePrecedente($this->drm->periode));
                if ($drmPrecedente && !$drmPrecedente->isTeledeclare()) {
                    $drmPrecedente = null;
                }
                if ($drmPrecedente) {
                    if  ($fieldNameCrd == 'stock_debut') {
                      if ($quantite) {
                         if (!$drmPrecedente->crds->exist($crd_regime)) {
                            $this->csvDoc->addErreur($this->previousCRDProductError($num_ligne, $csvRow, "regime"));
                            $num_ligne++;
                            continue;
                         }
                         if (!$drmPrecedente->crds->get($crd_regime)->exist($keyNode)) {
                            $this->csvDoc->addErreur($this->previousCRDProductError($num_ligne, $csvRow, "type/centilisation : ".$keyNode));
                            $num_ligne++;
                            continue;
                         }
                      }

                      if ($drmPrecedente->crds->exist($crd_regime)  && $drmPrecedente->crds->get($crd_regime)->exist($keyNode) && !$this->drm->canSetStockDebutMois()) {
                        if ($drmPrecedente->crds->get($crd_regime)->get($keyNode)->stock_fin != $quantite) {
                          $this->csvDoc->addErreur($this->previousCRDStockError($num_ligne, $csvRow));
                          $num_ligne++;
                          continue;
                        }
                      }
                    }
                }

                if ($just_check) {
                    if(!array_key_exists($litrageLibelle,$all_contenances)){
                      $this->csvDoc->addErreur($this->crdContenanceWrongFormatError($num_ligne, $csvRow));
                    }
                    if(!in_array($categorie_key,self::$cat_crd_mvts)){
                      $this->csvDoc->addErreur($this->crdCatWrongFormatError($num_ligne, $csvRow));
                    }
                    if(!in_array($type_key,self::$type_crd_mvts)){
                      $this->csvDoc->addErreur($this->crdTypeWrongFormatError($num_ligne, $csvRow));
                    }
                    $num_ligne++;
                } else {
                    if(!array_key_exists($litrageLibelle,$all_contenances)){ continue; }
                    if (!$regimeNode->exist($keyNode)) {
                        $litrageLibelle = $csvRow[self::CSV_CRD_CENTILITRAGE];
                        $regimeNode->getOrAddCrdNode($genre, $couleur, $centilitrage, $litrageLibelle);
                    }
                    if (!preg_match('/^stock/', $fieldNameCrd) || $regimeNode->getOrAdd($keyNode)->{$fieldNameCrd} == null || ($this->drm->canSetStockDebutMois() && preg_match('/debut/', $fieldNameCrd))) {
                        $regimeNode->getOrAdd($keyNode)->{$fieldNameCrd} += intval($quantite);
                    }
                    $num_ligne++;
                }
            }

            if (! $this->etablissement->exist('crd_regime')) {
                $crd_regimes = [];
                foreach ($this->getDocRows() as $csvRow) {
                    $crd_regime = DRMClient::convertCRDRegime($csvRow[self::CSV_CRD_REGIME]);

                    if (! in_array($crd_regime, $crd_regimes)) {
                        $crd_regimes[] = $crd_regime;
                    }
                }

                $this->etablissement->add('crd_regime', implode(',', $crd_regimes));
                $this->etablissement->save();
            }
        }

        private function convertGenre($g){
          $g = KeyInflector::slugify($g);
          if (preg_match('/^T/', $g)) {
            return DRMClient::DRM_CRD_CATEGORIE_TRANQ;
          }
          if (preg_match('/^M/', $g)) {
            return DRMClient::DRM_CRD_CATEGORIE_MOUSSEUX;
          }
          if (preg_match('/^COGNAC/', $s)) {
              return DRMClient::DRM_CRD_CATEGORIE_COGNAC;
          }
          if (preg_match('/^ALCOOL/', $s)) {
              return self::DRM_CRD_CATEGORIE_ALCOOLS;
          }
          if (preg_match('/^PI/', $s) || preg_match('/^PRODUIT/', $s)) {
              return self::DRM_CRD_CATEGORIE_PI;
          }

          return null;
        }

        private function convertCouleur($c){
          $c = KeyInflector::slugify($c);
          if (preg_match('/BLEU/', $c)) {
            return DRMClient::DRM_BLEU;
          }
          if (preg_match('/VERT/', $c)) {
            return DRMClient::DRM_VERT;
          }
          if (preg_match('/LIE/', $c)) {
            return DRMClient::DRM_LIEDEVIN;
          }
          return DRMClient::DRM_DEFAUT;
        }

        private function importAnnexesFromCSV($just_check = false) {
            $this->drm->remove('releve_non_apurement');
            $num_ligne = 1;
            $typesAnnexes = array_keys($this->type_annexes);
            foreach ($this->getDocRows() as $csvRow) {
                if (KeyInflector::slugify($csvRow[self::CSV_TYPE] != self::TYPE_ANNEXE)) {
                    $num_ligne++;
                    continue;
                }
                switch (KeyInflector::slugify($csvRow[self::CSV_ANNEXE_TYPEANNEXE])) {
                    case self::TYPE_ANNEXE_NONAPUREMENT:
                        $numero_document = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_NUMERODOCUMENT]);
                        $date_emission = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_NONAPUREMENTDATEEMISSION]);
                        $dt = DateTime::createFromFormat("Y-m-d", $date_emission);
                        if (!$dt) {
                            $dt = DateTime::createFromFormat("d-m-Y", $date_emission);
                        }

                        $numero_accise = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_NONAPUREMENTACCISEDEST]);
                        if (!$numero_document) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesNumeroDocumentError($num_ligne, $csvRow));
                            }
                            $num_ligne++;
                            break;
                        }
                        if (!$date_emission || $dt == false || array_sum($dt->getLastErrors())) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesNonApurementWrongDateError($num_ligne, $csvRow));
                            }
                            $num_ligne++;
                            break;
                        }
                        if ($numero_accise && !preg_match('/^[A-Z]{2}[0-9A-Z]{11}$/', $numero_accise)) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesNonApurementWrongNumAcciseError($num_ligne, $csvRow));
                            }
                            $num_ligne++;
                            break;
                        }
                        if (!$just_check) {
                            $nonAppurementNode = $this->drm->getOrAdd('releve_non_apurement')->getOrAdd($numero_document);
                            $nonAppurementNode->numero_document = $numero_document;
                            $nonAppurementNode->date_emission = $dt->format('Y-m-d');
                            $nonAppurementNode->numero_accise = $numero_accise;
                        }
                        $num_ligne++;
                        break;

                    case self::TYPE_ANNEXE_OBSERVATIONS:
                        $observations = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_OBSERVATION]);
                        if (!$observations && $just_check) {
                            $this->csvDoc->addErreur($this->observationsEmptyError($num_ligne, $csvRow));
                        }
                        if (!$just_check) {
                            $this->drm->add('observations', $observations);
                        }
                        $num_ligne++;
                        break;


                    case self::TYPE_ANNEXE_SUCRE:
                        $quantite_sucre = str_replace(',', '.', $csvRow[self::CSV_ANNEXE_QUANTITE]);
                        if (!$quantite_sucre || !is_numeric($quantite_sucre)) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->sucreWrongFormatError($num_ligne, $csvRow));
                            }
                            $num_ligne++;
                            break;
                        }
                        if (!$just_check) {
                            $this->drm->add('quantite_sucre', $quantite_sucre);
                        }
                        $num_ligne++;
                        break;

                    case DRMClient::DRM_DOCUMENTACCOMPAGNEMENT_DAADAC:
                    case DRMClient::DRM_DOCUMENTACCOMPAGNEMENT_DSADSAC:
                    case DRMClient::DRM_DOCUMENTACCOMPAGNEMENT_EMPREINTE:
                        $docTypeAnnexe = $this->drm->getOrAdd('documents_annexes')->getOrAdd(KeyInflector::slugify($csvRow[self::CSV_ANNEXE_TYPEANNEXE]));
                        $annexeTypeMvt = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_TYPEMVT]);
                        $numDocument = KeyInflector::slugify($csvRow[self::CSV_ANNEXE_NUMERODOCUMENT]);
                        if (!in_array($annexeTypeMvt, self::$permitted_annexes_type_mouvements)) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesTypeMvtWrongFormatError($num_ligne, $csvRow));
                            } $num_ligne++;
                            break;
                        }
                        if (!$numDocument) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesNumeroDocumentError($num_ligne, $csvRow));
                            } $num_ligne++;
                            break;
                        }
                        if (!$just_check) {
                            $docTypeAnnexe->add(strtolower($annexeTypeMvt), $numDocument);
                        }
                        $num_ligne++;
                        break;
                    case self::TYPE_ANNEXE_STATS_EUROPEENES:
                        $typeStat = strtolower(KeyInflector::slugify($csvRow[self::CSV_ANNEXE_TYPEMVT]));
                        $stat = (float) str_replace(',', '.', $csvRow[self::CSV_ANNEXE_QUANTITE]);
                        if(!in_array($typeStat, array('jus', 'mcr', 'vinaigre'))) {
                            if ($just_check) {
                                $this->csvDoc->addErreur($this->annexesTypeMvtStatistiquesWrongFormatError($num_ligne, $csvRow));
                            } $num_ligne++;
                            break;
                        }
                        if (!$just_check) {
                            $this->drm->declaratif->statistiques->set($typeStat, $stat);
                        }
                        $num_ligne++;
                        break;
                    default:
                        if ($just_check) {
                            $this->csvDoc->addErreur($this->typeDocumentWrongFormatError($num_ligne, $csvRow));
                        }
                        $num_ligne++;
                        break;
                }
            }
        }

        private function convertNumber($number){
          $numberPointed = trim(str_replace(",",".",$number));
          return round(floatval($numberPointed), FloatHelper::getInstance()->getMaxDecimalAuthorized());
        }

        /**
         * Functions de création d'erreurs
         */

        private function createWrongFormatTypeError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_TYPE]),
                                      "Choix possible type : " . implode(', ', self::$permitted_types),
                                      CSVClient::LEVEL_WARNING);
        }

        private function createWrongNumeroCompteError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]),
                                      "Le numéro de compte est mal formatté : il doit être au format 12345601",
                                      CSVClient::LEVEL_ERROR);
        }

        private function otherNumeroCompteError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_IDENTIFIANT]),
                                      "Le numéro de compte n'est pas celui du ressortissant attendu",
                                      CSVClient::LEVEL_ERROR);
        }


        private function createWrongFormatPeriodeError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_PERIODE]),
                                      "Format période : AAAAMM",
                                      CSVClient::LEVEL_ERROR);
        }

        private function otherPeriodeError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_PERIODE]),
                                      "La période spécifiée ne correspond pas à celle transmise",
                                      CSVClient::LEVEL_ERROR);
        }

        private function drmIsNotCreationEdiError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      'DRM existante',
                                      "Une DRM sur cette période existe déjà",
                                      CSVClient::LEVEL_ERROR);
        }


        private function createWrongFormatNumAcciseError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      KeyInflector::slugify($csvRow[self::CSV_NUMACCISE]),
                                      "Format numéro d'accise : FRXXXXXXXXXXX",
                                      CSVClient::LEVEL_WARNING);
        }

        private function productNotFoundError($num_ligne, $csvRow) {
            $libellesArray = $this->buildLibellesArrayWithRow($csvRow);
            $libelles = ($this->isEmptyArray($libellesArray))? $csvRow[self::CSV_CAVE_LIBELLE_COMPLET] : implode(' ', $libellesArray);
            return $this->createError($num_ligne,
                                      $libelles,
                                      "Le produit n'a pas été trouvé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function categorieMouvementNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_CATEGORIE_MOUVEMENT],
                                      "Le catégorie de mouvement n'a pas été trouvé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function typeMouvementNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_TYPE_MOUVEMENT],
                                      "Le type de mouvement n'a pas été trouvé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function typeMouvementCompatibiliteError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_TYPE_MOUVEMENT],
                                      "Le type de mouvement n'est pas compatible avec le régime crd de l'établissement, le volume ne sera pas importé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function typeComplementNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_TYPE_COMPLEMENT_PRODUIT],
                                      "Le type de complément doit être observations, tav ou premix",
                                      CSVClient::LEVEL_WARNING);
        }

        private function valueComplementVide($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_VALEUR_COMPLEMENT_PRODUIT],
                                      "La valeur du complément doit être renseignée",
                                      CSVClient::LEVEL_WARNING);
        }

        private function exportPaysNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_EXPORTPAYS],
                                      "Le pays d'export n'a pas été trouvé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function contratIDNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_CONTRATID],
                                      "L'id du contrat ne peut est vide ou n'a pas été trouvé",
                                      CSVClient::LEVEL_WARNING);
        }

        private function observationsEmptyError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      "Observations",
                                      "Les observations sont vides.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function sucreWrongFormatError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_QUANTITE],
                                      "La quantité de sucre est nulle ou possède un mauvais format.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function typeDocumentWrongFormatError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_TYPEANNEXE],
                                      "Le type de document d'annexe n'est pas connu.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function crdContenanceWrongFormatError ($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CRD_CENTILITRAGE],
                                      "La contenance de ces CRD n'est pas reconnu.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function crdCatWrongFormatError ($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CRD_CATEGORIE_KEY],
                                      "La catégorie de ces CRD n'est pas reconnu.",
                                      CSVClient::LEVEL_WARNING);
        }


        private function crdTypeWrongFormatError ($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CRD_TYPE_KEY],
                                      "Le type de ces CRD n'est pas reconnu.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function previousCRDProductError($num_ligne, $csvRow, $more) {
          return $this->createError($num_ligne, $csvRow[self::CSV_CRD_REGIME], "Il n'existe pas de stock pour cette crd dans la DRM précédente ($more)");
        }

        private function previousCRDStockError($num_ligne, $csvRow) {
          return $this->createError($num_ligne, $csvRow[self::CSV_CRD_REGIME], "Le stock initial pour cette crd n'est pas conforme à la DRM précédente");
        }


        private function annexesTypeMvtWrongFormatError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_TYPEMVT],
                                      "Le type d'enregistrement des " . $csvRow[self::CSV_ANNEXE_TYPEANNEXE] . " doit être 'début' ou 'fin' .",
                                      CSVClient::LEVEL_WARNING);
        }

        private function annexesTypeMvtStatistiquesWrongFormatError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_TYPEMVT],
                                      "Le type d'enregistrement des " . $csvRow[self::CSV_ANNEXE_TYPEANNEXE] . " doit être 'jus', 'mcr' ou 'vinaigre' .",
                                      CSVClient::LEVEL_WARNING);
        }

        private function annexesNumeroDocumentError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_TYPEANNEXE],
                                      "Le numéro de document ne peut pas être vide.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function annexesNonApurementWrongDateError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_NONAPUREMENTDATEEMISSION],
                                      "La date est vide ou mal formattée.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function annexesNonApurementWrongNumAcciseError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_ANNEXE_NONAPUREMENTACCISEDEST], "La numéro d'accise du destinataire est mal formatté.",
                                      CSVClient::LEVEL_WARNING);
        }

        private function vracNotFoundError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_IDENTIFIANT],
                                      "Le contrat n'a pas été trouvé",
                                      CSVClient::LEVEL_ERROR);
        }

        private function vracTypeNotConformError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_LIBELLE_COMPLET],
                                      "Le contrat est un contrat de raisin ou de moût.",
                                      CSVClient::LEVEL_ERROR);
        }

        private function vracProduitNotConformError($num_ligne, $csvRow) {
            return $this->createError($num_ligne,
                                      $csvRow[self::CSV_CAVE_LIBELLE_COMPLET],
                                      "Le produit du contrat n'est pas celui décrit.",
                                      CSVClient::LEVEL_ERROR);
        }

        private function createError($num_ligne, $erreur_csv, $raison, $level = CSVClient::LEVEL_WARNING) {
            $error = new stdClass();
            $error->num_ligne = $num_ligne;
            $error->erreur_csv = $erreur_csv;
            $error->raison = $raison;
            $error->level = $level;
            return $error;

        }

        private function findContratDocId($csvRow) {
          if($vrac = VracClient::getInstance()->findByNumContrat("VRAC-".KeyInflector::slugify($csvRow[self::CSV_CAVE_CONTRATID]), acCouchdbClient::HYDRATE_JSON)) {
              return $vrac->_id;
          }

          $campagne = $this->drm->campagne;
          if (strpos($this->drm->periode, '07') === 4) { // Si en juillet
            $annees = explode('-', $campagne);
            $campagne = sprintf('%s-%s', $annees[0] + 1, $annees[1] + 1);
          }
          $vrac = VracClient::getInstance()->findDocIdByNumArchive($campagne, $csvRow[self::CSV_CAVE_CONTRATID], 2);
          $vracObj = VracClient::getInstance()->find($vrac);
          if((!$vrac || !$vracObj || $vracObj->getVendeurIdentifiant() != $csvRow[self::CSV_IDENTIFIANT])){
              $annees = explode('-', $campagne);
              $campagne = sprintf('%s-%s', $annees[0] - 1, $annees[1] - 1);
              $vrac = VracClient::getInstance()->findDocIdByNumArchive($campagne, $csvRow[self::CSV_CAVE_CONTRATID], 2);
          }
          return $vrac;
        }

        /**
         * Fin des functions de création d'erreurs
         */
        private function buildLibellesArrayWithRow($csvRow, $with_slugify = false) {
            $certification = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_CERTIFICATION]) : $csvRow[self::CSV_CAVE_CERTIFICATION];
            $genre = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_GENRE]) : $csvRow[self::CSV_CAVE_GENRE];
            $this->uniformisationGenre($genre);
            $appellation = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_APPELLATION]) : $csvRow[self::CSV_CAVE_APPELLATION];
            $mention = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_MENTION]) : $csvRow[self::CSV_CAVE_MENTION];
            $lieu = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_LIEU]) : $csvRow[self::CSV_CAVE_LIEU];
            $couleur = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_COULEUR]) : $csvRow[self::CSV_CAVE_COULEUR];
            $cepage = ($with_slugify) ? KeyInflector::slugify($csvRow[self::CSV_CAVE_CEPAGE]) : $csvRow[self::CSV_CAVE_CEPAGE];

            $libelles = array(strtoupper($certification),
                strtoupper($genre),
                strtoupper($appellation),
                strtoupper($mention),
                strtoupper($lieu),
                strtoupper($couleur),
                strtoupper($cepage));
            foreach ($libelles as $key => $libelle) {
                if (!$libelle) {
                    $libelles[$key] = null;
                }
            }
            return $libelles;
        }

        private function uniformisationGenre(&$genre){
          $gs = self::$genres_synonyme;
          $gslug = $this->slugifyProduitArrayOrString($genre);
          if(array_key_exists($gslug,$gs)){
            $genre = $this->slugifyProduitArrayOrString($gs[$gslug]);
          }
        }

        private function slugifyProduitArrayOrString($produitLibelles) {
          $produitLibellesStr = is_array($produitLibelles)? implode(" ",$produitLibelles) : $produitLibelles;
          return strtoupper(KeyInflector::slugify(trim(preg_replace("/[\ ]+/"," ",$produitLibellesStr))));
        }

        private function slugifyProduitConf($produit, $withAOP = false, $withGenre = true) {
            $libellesSlugified = array();
            foreach ($produit->getLibelles() as $key => $libelle) {
                $libellesSlugified[] = strtoupper(KeyInflector::slugify($libelle));
            }
            $genreKey = $produit->getGenre()->getKey();
            $genreLibelle = self::$genres[$genreKey];
            $libellesSlugified[1] = strtoupper(KeyInflector::slugify($genreLibelle));
            if($withAOP){
                if(($libellesSlugified[0] == "AOC")){
                    $libellesSlugified[0]="AOP";
                }
            }else{
                if(($libellesSlugified[0] == "AOP")){
                    $libellesSlugified[0]="AOC";
                }
            }
            foreach ($libellesSlugified as $key => $libelle) {
                if (!$libelle) {
                    $libellesSlugified[$key] = null;
                }
            }
            return $libellesSlugified;
        }

        private function buildAllMouvements() {
            $all_conf_details_slugified = array();
            foreach (DRMClient::$types_node_from_libelles as $detailsNode) {
              if($this->configuration->declaration->exist($detailsNode)){
                if (!array_key_exists($detailsNode, $all_conf_details_slugified)) {
                      $all_conf_details_slugified[$detailsNode] = array();
                }
                foreach ($this->configuration->declaration->get($detailsNode)->getAllDetails() as $all_conf_detail_cat_Key => $all_conf_detail_cat) {
                  foreach ($all_conf_detail_cat as $key_type => $type_detail) {
                    if (!array_key_exists(KeyInflector::slugify($all_conf_detail_cat_Key), $all_conf_details_slugified[$detailsNode])) {
                      $all_conf_details_slugified[$detailsNode][KeyInflector::slugify($all_conf_detail_cat_Key)] = array();
                    }
                    if (KeyInflector::slugify($key_type) == 'VRAC') {
                      $all_conf_details_slugified[$detailsNode][KeyInflector::slugify($all_conf_detail_cat_Key)]['CONTRAT'] = $type_detail;
                    }
                    $all_conf_details_slugified[$detailsNode][KeyInflector::slugify($all_conf_detail_cat_Key)][KeyInflector::slugify($key_type)] = $type_detail;
                  }
                }
              }
            }
            return $all_conf_details_slugified;
        }

        private function isEmptyArray($array){
            foreach ($array as $csvLibelle) {
                if($csvLibelle){
                    return false;
                }
            }
            return true;
        }

        public function setNoSave($noSave){
          $this->noSave = $noSave;
        }

		private function getIdDouane($datas)
		{
            $certification = trim(str_replace(array('(', ')'), '', $datas[self::CSV_CAVE_CERTIFICATION]));
        	if (
            	$certification &&
            	!trim($datas[self::CSV_CAVE_GENRE]) &&
            	!trim($datas[self::CSV_CAVE_APPELLATION]) &&
            	!trim($datas[self::CSV_CAVE_MENTION]) &&
            	!trim($datas[self::CSV_CAVE_LIEU]) &&
            	!trim($datas[self::CSV_CAVE_COULEUR]) &&
            	!trim($datas[self::CSV_CAVE_CEPAGE])
        	) {
        		return $certification;
        	}

            if (preg_match('/(.*[^ ]) *\(([^\)]+)\)/', $datas[self::CSV_CAVE_LIBELLE_COMPLET], $m) && trim($m[2])) {
                return $m[2];
            }

        	return null;
		}

        public static function getEdiDefaultFromInao($inao) {
            if (preg_match('/^.....M/', $inao) && preg_match('/^VM_/', $inao)) {
                return '/declaration/certifications/AUTRESVINS/genres/EFF/appellations/DEFAUT/mentions/DEFAUT/lieux/DEFAUT/couleurs/DEFAUT/cepages/DEFAUT';
            }
            if (!preg_match('/^VT_/', $inao) && preg_match('/_/', $inao)) {
                return '/declaration/certifications/ALCOOL/genres/DEFAUT/appellations/DEFAUT/mentions/DEFAUT/lieux/DEFAUT/couleurs/DEFAUT/cepages/DEFAUT';
            }

            return '/declaration/certifications/AUTRESVINS/genres/TRANQ/appellations/DEFAUT/mentions/DEFAUT/lieux/DEFAUT/couleurs/DEFAUT/cepages/DEFAUT';
        }

        private function floatize($value)
      	{
      		return self::floatizeVal($value);
      	}

      	public static function floatizeVal($value)
      	{
      	    if ($value === null) {
      	        return null;
      	    }
      	    $value = str_replace(',', '.', $value);
      	    return (is_numeric($value))? floatval($value) : str_replace('.', ',', $value);
      	}
    }
