<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of updateCompteWithDroitsAndTypeSociete
 *
 * @author mathurin
 */
class updateCompteWithDroitsAndTypeSocieteTask extends sfBaseTask
{
    
   protected $debug = false;
    
  protected function configure()
  {
     $this->addArguments(array(
       new sfCommandArgument('debug', sfCommandArgument::OPTIONAL, '0'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'compte';
    $this->name             = 'update-comptes-with-droits';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceCompteStatut|INFO] task does things.
Call it with:

  [php symfony maintenance:update-comptes-with-droits|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
      
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $this->debug = array_key_exists('debug', $arguments) && $arguments['debug'];
    
    //$rows = CompteAllView::getInstance()->findByInterproAndStatutVIEW("INTERPRO-inter-loire",  CompteClient::STATUT_ACTIF);
    $rows = CompteAllView::getInstance()->findByInterproVIEW("INTERPRO-inter-loire");

    foreach($rows as $row) {
        $compte = CompteClient::getInstance()->find($row->id);
        if(!$compte){
            echo $this->red("ERREUR : ")."Le compte $row->id est introuvable en base.\n";
            continue;
        }
        $societe = $compte->getSociete(); 
        if(!$societe){
            echo $this->red("ERREUR : ")."Le compte $row->id n'appartient a aucune société.\n";
            continue;
        }
        $type_societe = $societe->type_societe;
        if(!$type_societe){
            echo $this->red("ERREUR : ")."La societe $societe->_id n'a aucun type.\n";
            continue;
        }
        $compte->add('type_societe', $type_societe);
        if(!$compte->isActif()){
            $compte->save(false, false, false, true);
            echo $this->yellow("ENREGISTREMENT : ")."Le compte inactif $row->id a pour type société $type_societe.\n";
            continue;
        }
        if(!(($compte->compte_type == CompteClient::TYPE_COMPTE_SOCIETE) || ($compte->compte_type == CompteClient::TYPE_COMPTE_INTERLOCUTEUR))){
            echo $this->green("COMPTE ETB : ")."Le compte $row->id type société $type_societe et type compte $compte->compte_type non enregistré.\n";
            continue;
        }
        else{
            $compte->buildDroits(true);
            $droitsDisplay = "|";
            $teledeclaration = false;
            foreach ($compte->droits as $droit){
                $droitsDisplay.=$droit."|";
                if($droit == CompteClient::DROITS_COMPTE_TELEDECLARATION){
                    $teledeclaration = true;
                }
            }            
            $compte->save(false, false, false, true);
            
            echo $this->green("ENREGISTREMENT : ")."Le compte $row->id type société $type_societe (type compte $compte->compte_type) => $droitsDisplay. ";
            if($teledeclaration){
                if(!$compte->exist('email') || !$compte->email){
                   echo $this->yellow("EMAIL ABSENT"); 
                }else{
                   echo $this->green("EMAIL : $compte->email");  
                }
            }
            echo "\n";
        }
      }

  }
  
   public function green($string) {
        return "\033[32m".$string."\033[0m";
    }
        
    public function yellow($string) {
        return "\033[33m".$string."\033[0m";
    }
    
    public function red($string) {
        return "\033[31m".$string."\033[0m";
    }
}
