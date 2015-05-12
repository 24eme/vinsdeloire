<?php

class DSLatex extends GenericLatex {

  private $ds = null;
  const MAX_LIGNE_TEMPLATE_ONEPAGE = 35;
  const DS_OUTPUT_TYPE_PDF = 'pdf';
  const DS_OUTPUT_TYPE_LATEX = 'latex';
  

  function __construct(DS $d, $config = null) {
    sfProjectConfiguration::getActive()->loadHelpers("Partial", "Url", "MyHelper");
    $this->ds = $d;
  }

  public function getNbPages() {
    return 1;
  }
  
  public function getLatexFileNameWithoutExtention() {
    return $this->getTEXWorkingDir().$this->ds->identifiant.'_'.$this->ds->periode.'_'.$this->ds->_rev;
  }

  
  public function getLatexFileContents() {
    return html_entity_decode(htmlspecialchars_decode(
						      get_partial('ds/generateTex', array('ds' => $this->ds,
                                                                                          'etablissement' => EtablissementClient::getInstance()->find($this->ds->identifiant),
											  'nb_page' => $this->getNbPages()))
						      , HTML_ENTITIES));
  }

  public function getFactureId() {
    return $this->ds->_id;
  }

  public function getPublicFileName($extention = '.pdf') {
    return 'ds_'.$this->ds->identifiant.'_'.$this->ds->periode.'_page'.$this->getNbPages().'_'.$this->ds->_rev.$extention;
  }

}
