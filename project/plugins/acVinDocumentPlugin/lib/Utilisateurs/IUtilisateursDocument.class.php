<?php
interface IUtilisateursDocument {
    
  function addEdition($id_user,$date);
  function addValidation($id_user,$date);
  
  function getLastEdition();
  function getLastValidation();
  
  function removeValidation();
}