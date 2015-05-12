<?php

class acVinSV12RegenerateMouvementsTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID Document'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'sv12';
    $this->name             = 'regenerate_mouvements';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $doc = acCouchdbManager::getClient('SV12')->find($arguments['doc_id']);

    $save = false;
    
    $mouvements = $doc->getMouvements()->toArray(true, false);

    $doc->generateMouvements();

    $new_mouvements = $doc->getMouvements()->toArray(true, false);
    
    foreach($mouvements as $identifiant => $mouvement) {
        foreach($mouvement as $hash => $m) {
            $egal = false;
            foreach($new_mouvements[$identifiant] as $new_hash => $new_m) {
                if($this->compareMouvement($m, $new_m)) {
                    $egal = !$egal;
                    unset($new_mouvements[$identifiant][$new_hash]);
                }
            }
            if(!$egal) {
                var_dump($new_mouvements[$identifiant]);
                var_dump($m);
                print_r(array_diff($new_m, $m));
                throw new sfException(sprintf("mouvement non trouvé ou ambigue %s@%s", $identifiant, $hash));
            }
        }
    }

    $nb_total = 0;
    foreach($new_mouvements as $identifiant => $mouvement) {
        $nb = 0;
        foreach($mouvement as $hash => $m) {
            $save = true;
            $mouvements[$identifiant][$hash] = $m;  
            $nb++;
        }
        $nb_total += $nb;
        if($nb > 0) {
            echo sprintf("%s;%s;%s nouveau(x) mouvement(s)\n", $doc->_id, $identifiant, $nb);
        }
    }

    $doc->remove('mouvements');
    $doc->add('mouvements', $mouvements);

    if($save) {
        $doc->save();
        echo sprintf("%s;saved\n", $doc->_id);
    }
  }

  protected function compareMouvement($m1, $m2) {
    if($m1['ecart'] != $m2['ecart']) {

        return false;
    }

    if($m1['produit_hash'] != $m2['produit_hash']) {

        return false;
    }

    if($m1['type_hash'] != $m2['type_hash']) {

        return false;
    }

    if($m1['vrac_numero'] != $m2['vrac_numero']) {

        return false;
    }

    if($m1['vrac_destinataire'] != $m2['vrac_destinataire']) {

        return false;
    }

    if($m1['detail_identifiant'] != $m2['detail_identifiant']) {

        return false;
    }

    if($m1['detail_libelle'] != $m2['detail_libelle']) {

        return false;
    }

    if(round($m1['volume'], 2) != round($m2['volume'], 2)) {
        return false;
    }

    if(round($m1['cvo'], 2) != round($m2['cvo'], 2)) {

        return false;
    }

    if($m1['facturable'] != $m2['facturable']) {

        return false;
    }

    if($m1['date'] != $m2['date']) {

        return false;
    }

    if($m1['date_version'] != $m2['date_version']) {

        return false;
    }

    if($m1['version'] !== $m2['version']) {

        return false;
    }

    return true;
  }
}
