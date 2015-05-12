<?php 

class SV12AllView extends acCouchdbView
{
    const KEY_IDENTIFIANT = 0;
    const KEY_CAMPAGNE = 1;
    const KEY_PERIODE = 2;
    const KEY_VERSION = 3;

    const VALUE_DECLARANT_NOM = 0;
    const VALUE_DECLARANT_CVI = 1;
    const VALUE_DECLARANT_COMMUNE = 2;
    const VALUE_STATUT = 3;
    const VALUE_DATE_SAISIE = 4;
    const VALUE_VOLUME_RAISINS = 5;
    const VALUE_VOLUME_MOUTS = 6;
    const VALUE_VOLUME_ECARTS = 7;

    public static function getInstance() {

        return acCouchdbManager::getView('sv12', 'all', 'SV12');
    }

    public function findAll() {
        return $this->client->getView($this->design, $this->view)->rows;
    }

    
    public function findByEtablissement($id_or_identifiant) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);

        return $this->client->startkey(array($identifiant))
                            ->endkey(array($identifiant, array()))
                            ->getView($this->design, $this->view);
    }

    public function findByEtablissementAndPeriode($id_or_identifiant, $periode) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);
        $campagne = $this->client->buildCampagne($periode);

        return $this->client->startkey(array($identifiant, $campagne, $periode))
                            ->endkey(array($identifiant, $campagne, $periode, array()))
                            ->getView($this->design, $this->view);
    }


    public function findByEtablissementPeriodeAndVersionRectificative($id_or_identifiant, $periode, $version_rectificative)
    {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);
        $campagne = $this->client->buildCampagne($periode);

        return $this->client->startkey(array($identifiant, $campagne, $periode, $version_rectificative))
                            ->endkey(array($identifiant, $campagne, $periode, $this->buildVersion($version_rectificative, 99),array()))
                            ->getView($this->design, $this->view);
    }

    public function getByEtablissement($id_or_identifiant) {

        return $this->builds($this->findByEtablissement($id_or_identifiant)->rows);      
    }

    public function getMasterByEtablissement($id_or_identifiant) {
        $masters = array();
        $items = $this->getByEtablissement($id_or_identifiant); 
        krsort($items);
        foreach($items as $item) {
            if (!array_key_exists($item->periode, $masters)) {
                $masters[$item->periode] = $item;
            }
        }

        return $masters;
    }

    public function getMasterByEtablissementAndPeriode($id_or_identifiant, $periode) {
        $items = $this->builds($this->findByEtablissementAndPeriode($id_or_identifiant, $periode)->rows);       
        krsort($items);
        foreach($items as $item) {

            return $item;
        }

        return null;
    }

    public function getMasterByEtablissementPeriodeAndVersionRectificative($id_or_identifiant, $periode, $version_rectificative) {
        $items = $this->builds($this->findByEtablissementPeriodeAndVersionRectificative($id_or_identifiant, $periode, $version_rectificative)->rows);
        krsort($items);
        foreach($items as $item) {

            return $item;
        }

        return null;
    }

    protected function builds($rows) {
        $items = array();
        foreach($rows as $row) {
            $items[$row->id] = $this->build($row);
        }

        return $items;
    }

    protected function build($row) {
        $sv12 = new stdClass();
        $sv12->_id = $row->id;
        $sv12->identifiant = $row->key[self::KEY_IDENTIFIANT];
        $sv12->campagne = $row->key[self::KEY_CAMPAGNE];
        $sv12->periode = $row->key[self::KEY_PERIODE];
        $sv12->version = $row->key[self::KEY_VERSION];
        $sv12->declarant = new stdClass();
        $sv12->declarant->nom = $row->value[self::VALUE_DECLARANT_NOM];
        $sv12->declarant->cvi = $row->value[self::VALUE_DECLARANT_CVI];
        $sv12->declarant->commune = $row->value[self::VALUE_DECLARANT_COMMUNE];
        $sv12->valide = new stdClass();
        $sv12->valide->statut = $row->value[self::VALUE_STATUT];
        $sv12->valide->date_saisie = $row->value[self::VALUE_DATE_SAISIE];
        $sv12->totaux = new stdClass();
        $sv12->totaux->volume_raisins = $row->value[self::VALUE_VOLUME_RAISINS];
        $sv12->totaux->volume_mouts = $row->value[self::VALUE_VOLUME_MOUTS];
        $sv12->totaux->volume_ecarts = $row->value[self::VALUE_VOLUME_ECARTS];

        return $sv12;
    }
}