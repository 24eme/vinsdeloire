<?php

interface InterfaceMouvementDocument
{
    public function getMouvements();
    public function getMouvementsCalcule();
    public function getMouvementsCalculeByIdentifiant($identifiant);
    public function generateMouvements();
    public function findMouvement($cle, $id = null);
    public function facturerMouvements();
    public function clearMouvements();
    public function isFactures();
    public function isNonFactures();
}