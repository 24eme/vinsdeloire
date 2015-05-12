<?php 
use_helper('Vrac');
use_helper('Vracpdf');
use_helper('Display');
?>


\def\INTERLOIRECOORDONNEESTITRE{<?php echo "Interprofession des Vins du Val de Loire"; ?>}
\def\INTERLOIRECOORDONNEESADRESSE{<?php echo "InterLoire - 62, rue Blaise Pascal - CS 61921"; ?>}
\def\INTERLOIRECOORDONNEESCPVILLE{<?php echo "37019 TOURS CEDEX 1"; ?>}
\def\INTERLOIRECOORDONNEESTELEPHONENANTES{<?php echo "Vignoble Nantais : Tél. 02 47 60 55 15"; ?>}
\def\INTERLOIRECOORDONNEESTELEPHONEANJOU{<?php echo "Vignoble Anjou-Saumur : Tèl. 02 47 60 55 36"; ?>}
\def\INTERLOIRECOORDONNEESTELEPHONETOURS{<?php echo "Vignoble Touraine : Tèl. 02 47 60 55 18"; ?>}
\def\INTERLOIRECOORDONNEESFAX{<?php echo "Fax : 02 47 60 55 09"; ?>}
\def\INTERLOIRECOORDONNEESEMAIL{<?php echo "Email : contact@vinsvaldeloire.fr"; ?>}

\def\CONTRATNUMENREGISTREMENT{<?php echo $vrac->getNumeroArchive(); ?>}
\def\CONTRATVISA{<?php echo $vrac->getVisa(); ?>}
\def\CONTRATDATEENTETE{<?php echo Date::francizeDate(getDateValidation($vrac)); ?>}

\def\CONTRAT_TITRE{<?php echo "CONTRAT D'ACHAT EN PROPRIETE"; ?>}


\def\CONTRATVENDEURNOM{<?php echo cut_latex_string($vrac->vendeur->raison_sociale
, 50); ?>}
\def\CONTRATVENDEURCVI{<?php echo $vrac->vendeur->cvi; ?>}
\def\CONTRATVENDEURSIRET{<?php echo $vrac->getVendeurObject()->getSociete()->getSiret(); ?>}
\def\CONTRATVENDEURADRESSE{<?php echo cut_latex_string($vrac->vendeur->adresse, 45); ?>}
\def\CONTRATVENDEURCOMMUNE{<?php echo cut_latex_string(sprintf("%s %s", $vrac->vendeur->code_postal, $vrac->vendeur->commune), 55); ?>}


\def\CONTRATACHETEUREURNOM{<?php echo cut_latex_string($vrac->acheteur->raison_sociale
, 50); ?>}
\def\CONTRATACHETEURCVI{<?php echo $vrac->acheteur->cvi; ?>}
\def\CONTRATACHETEURSIRET{<?php echo $vrac->getAcheteurObject()->getSociete()->getSiret(); ?>}
\def\CONTRATACHETEURADRESSE{<?php echo cut_latex_string($vrac->acheteur->adresse, 45); ?>}
\def\CONTRATACHETEURCOMMUNE{<?php echo cut_latex_string(sprintf("%s %s", $vrac->acheteur->code_postal, $vrac->acheteur->commune), 55); ?>}

\def\CONTRATCOURTIERNOM{<?php echo cut_latex_string($vrac->mandataire->raison_sociale
, 55); ?>}
\def\CONTRATCOURTIERCARTEPRO{<?php echo $vrac->mandataire->carte_pro; ?>}

\def\CONTRATTYPE{<?php echo showType($vrac); ?>}
\def\CONTRATTYPEUNITE{<?php echo showUnite($vrac) ?>}
\def\CONTRATPRODUITLIBELLE{<?php echo $vrac->produit_libelle; ?>}
\def\CONTRATPRODUITMILLESIME{<?php echo $vrac->millesime; ?>}
\def\CONTRATPRODUITQUANTITE{<?php echo formatQuantiteFr($vrac); ?>}
\def\CONTRATPRIXUNITAIRE{<?php echo formatPrixFr($vrac->prix_unitaire); ?>}
\def\CONTRATTYPEEXPLICATIONPRIX{<?php echo vracTypeExplication($vrac);?>}

\def\CONTRATDATEMAXENLEVEMENT{Au plus tard le~<?php echo cut_latex_string(Date::francizeDate($vrac->getMaxEnlevement()),50); ?>}
\def\CONTRATFRAISDEGARDE{ <?php echo formatPrixFr($vrac->getFraisDeGarde()); ?>~\euro/hl}

\def\CONTRATLIEUCREATION{<?php echo cut_latex_string($vrac->getResponsableLieu(),70); ?>}
\def\CONTRATDATECREATION{<?php echo cut_latex_string(Date::francizeDate($vrac->valide->date_saisie),70); ?>}

\def\CONTRATMANDATAIREVISA{<?php echo getMandataireVisa($vrac); ?>}
\def\CONTRATDATESIGNATUREVENDEUR{<?php echo getDateSignatureVendeur($vrac); ?>}
\def\CONTRATDATESIGNATUREACHETEUR{<?php echo getDateSignatureAcheteur($vrac); ?>}

<?php if($vrac->isDomaine()): ?>
\def\CONTRATGENERIQUEDOMAINE{Domaine <?php echo $vrac->domaine ?>}
<?php else: ?>
\def\CONTRATGENERIQUEDOMAINE{Générique}
<?php endif; ?>