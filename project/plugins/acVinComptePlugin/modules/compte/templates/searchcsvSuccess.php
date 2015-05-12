<?php
printf("\xef\xbb\xbf");//UTF8 BOM (pour windows)
echo "#nom complet ; type ; civilité ; nom ; prénom ; adresse ; adresse complémentaire ; code postal ; commune ; pays ; téléphone bureau ; téléphone mobile ; téléphone perso ; fax ; email ; commentaire ; id société ; type société ; société raison sociale ; société adresse ; société adresse complémentaire ; société code postal ; société commune ; société téléphone ; société fax ; société email\n";
foreach ($results as $res) {
  $data = $res->getData(ESC_RAW);
  
  $societe_informations = $data['societe_informations'];
                                                
  echo '"'.$data['nom_a_afficher']. '";';
  echo '"'.CompteClient::getInstance()->createTypeFromOrigines($data['origines']).'";';
  echo '"'.$data['civilite']. '";';
  echo '"'.$data['prenom']. '";';
  echo '"'.$data['nom']. '";';
  echo '"'.$data['adresse']. '";';
  echo '"'.$data['adresse_complementaire']. '";';
  echo '"'.$data['code_postal']. '";';
  echo '"'.$data['commune']. '";';
  echo '"'.$data['pays']. '";';
  echo '"'.$data['telephone_bureau']. '";';
  echo '"'.$data['telephone_mobile']. '";';
  echo '"'.$data['telephone_perso']. '";';
  echo '"'.$data['fax']. '";';
  echo '"'.$data['email']. '";';  
  echo '"'.$data['commentaire']. '";';
  echo '"'.preg_replace('/SOCIETE-/', '', $data['id_societe']). '";';
  echo '"'.$societe_informations['type']. '";';
  echo '"'.$societe_informations['raison_sociale']. '";';
  echo '"'.$societe_informations['adresse']. '";';
  echo '"'.$societe_informations['adresse_complementaire']. '";';
  echo '"'.$societe_informations['code_postal']. '";';
  echo '"'.$societe_informations['commune']. '";';
  echo '"'.$societe_informations['telephone']. '";';
  echo '"'.$societe_informations['fax']. '";';
  echo '"'.$societe_informations['email']. '"';
  echo "\n";
}
