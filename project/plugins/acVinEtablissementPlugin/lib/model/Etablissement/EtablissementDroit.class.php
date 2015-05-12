<?php

class EtablissementDroit
{
    const DROIT_DRM_PAPIER = 'DRM_PAPIER';
    const DROIT_DRM_DTI = 'DRM_DTI';
    const DROIT_VRAC = 'VRAC';


    protected $etablissement;

    public function __construct(Etablissement $etablissement)
    {
        $this->etablissement = $etablissement;
    }

    public function has($droit) {

        return in_array($droit, $this->get());
    }

    public function get() {

        return $this->etablissement->getDroits();
    }
}