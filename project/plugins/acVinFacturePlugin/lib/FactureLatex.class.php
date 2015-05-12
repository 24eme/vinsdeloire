<?php

class FactureLatex extends GenericLatex {

  private $facture = null;

  const MAX_LIGNES_PERPAGE = 50;
  const NB_LIGNES_PAPILLONS_FIXE = 2;
  const NB_LIGNES_PAPILLONS_PAR_ECHEANCE = 3;
  const NB_LIGNES_ENTETE = 10;
  const NB_LIGNES_REGLEMENT = 7;
  const MAX_NB_LIGNES_ORGA = 3;

  function __construct(Facture $f, $config = null) {
    sfProjectConfiguration::getActive()->loadHelpers("Partial", "Url", "MyHelper");
    $this->facture = $f;
  }

  public function getNbLignes() {
    $nbLignes = $this->facture->getNbLignesMouvements() + self::NB_LIGNES_REGLEMENT + self::NB_LIGNES_ENTETE + self::MAX_NB_LIGNES_ORGA;
    $nb_echeances = count($this->facture->echeances);
    if ($nb_echeances)
      $nbLignes += self::NB_LIGNES_PAPILLONS_FIXE + self::NB_LIGNES_PAPILLONS_PAR_ECHEANCE * $nb_echeances;
    return $nbLignes;
  }
  
  public function getNbPages() {
    return floor(($this->getNbLignes()/ self::MAX_LIGNES_PERPAGE) + 1);
  }
  
  private function getFileNameWithoutExtention() {
    return  'facture_'.$this->facture->identifiant.'_'.str_replace('/', '-', $this->facture->numero_interloire).'_'.$this->facture->numero_facture.'_'.$this->facture->_rev;
  }

  public function getLatexFileNameWithoutExtention() {
    return $this->getTEXWorkingDir().$this->getFileNameWithoutExtention();
  }


  public function getLatexFileContents() {
    return html_entity_decode(htmlspecialchars_decode(
						      get_partial('facture/latexContent', array('facture' => $this->facture,
												'nb_pages' => $this->getNbPages(),
												'nb_lines' => $this->getNbLignes()))
						      , HTML_ENTITIES));
  }


  public function getFactureId() {
    return $this->facture->_id;
  }

  public function getPublicFileName($extention = '.pdf') {
    return $this->getFileNameWithoutExtention().$extention;
  }

}
