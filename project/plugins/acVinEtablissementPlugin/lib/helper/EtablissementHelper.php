<?php

function display_adresse_societe($etablissement){
    echo ($etablissement->isSameContactThanSociete())? 'Oui' : 'Non';
}