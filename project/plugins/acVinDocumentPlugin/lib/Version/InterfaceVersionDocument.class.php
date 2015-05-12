<?php

interface InterfaceVersionDocument
{
    public static function buildVersion($rectificative, $modificative);
    public static function buildRectificative($version);
    public static function buildModificative($version);
    
    public function getVersion();
    public function hasVersion();
    public function isVersionnable();

    public function getRectificative();
    public function isRectificative();
    public function isRectifiable();

    public function getModificative();
    public function isModificative();
    public function isModifiable();

    public function getPreviousVersion();
    public function getMasterVersionOfRectificative();
    public function needNextVersion();
    
    public function getMaster();
    public function isMaster();
    public function findMaster();  // Fonction à définir
    
    public function findDocumentByVersion($version);  // Fonction à définir
    
    public function getMother();
    public function motherGet($hash);
    public function motherExist($hash);
    public function motherHasChanged(); // Fonction à définir
    public function getDiffWithMother();
    public function isModifiedMother($hash_or_object, $key = null);
    
    public function generateRectificative();
    public function generateModificative();
    public function generateNextVersion();

    public function listenerGenerateVersion($document);
    public function listenerGenerateNextVersion($document);

    //Fonctions nécessaire mais non spécifique au versionnage d'un document 
    public function getSuivante(); // Fonction à définir
}