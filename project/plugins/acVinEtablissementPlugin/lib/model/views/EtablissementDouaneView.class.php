<?php

class EtablissementDouaneView extends acCouchdbView
{
	const VALUE_ETABLISSEMENT_ID = 0;
	const VALUE_NOM = 1;
	const VALUE_IDENTIFIANT = 2;
	const VALUE_RAISON_SOCIALE = 3;
	const VALUE_SIRET = 4;
	const VALUE_CVI = 5;
	const VALUE_FAMILLE = 6;
	const VALUE_COMMUNE = 7;
	const VALUE_CODE_POSTAL = 8;

	public static function getInstance() {

        return acCouchdbManager::getView('etablissement', 'douane', 'Etablissement');
    }

    public function findByDouane($douane) {

    	return $this->client->startkey(array($douane))
                    		->endkey(array($douane, array()))
                    		->getView($this->design, $this->view);
    }

    public static function makeLibelle($datas) {
        $libelle = '';

        if ($nom = $datas[self::VALUE_NOM]) {
            $libelle .= $nom;
        }

        if (isset($datas[self::VALUE_RAISON_SOCIALE]) && $rs = $datas[self::VALUE_RAISON_SOCIALE]) {
            if ($libelle) {
                $libelle .= ' / ';
            }
            $libelle .= $rs;
        }

        $libelle .= ' ('.$datas[self::VALUE_IDENTIFIANT];
        if (isset($datas[self::VALUE_SIRET]) && $siret = $datas[self::VALUE_SIRET]) {
            $libelle .= ' / '.$siret;
        }

        if (isset($datas[self::VALUE_CVI]) && $cvi = $datas[self::VALUE_CVI]) {
            $libelle .= ' / '.$cvi;
        }
        $libelle .= ') ';

    	if (isset($datas[self::VALUE_FAMILLE]))
    	  	$libelle .= $datas[self::VALUE_FAMILLE];

    	if (isset($datas[self::VALUE_COMMUNE]))
    	  	$libelle .= ' '.$datas[self::VALUE_COMMUNE];

    	if (isset($datas[$datas[self::VALUE_CODE_POSTAL]]))
    	  	$libelle .= ' '.$datas[$datas[self::VALUE_CODE_POSTAL]];
        
        return trim($libelle);
    }

}  