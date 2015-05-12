<?php

function isVersionnerCssClass($object, $key) {

    if (isVersionner($object, $key)) {

        return versionnerCssClass();
    } else {

        return null;
    }
}

function versionnerCssClass() {

    return 'versionner';
}

function isVersionner($object, $key) {

    return !$object->getDocument()->isValidee() && $object->getDocument()->isModifiedMother($object->getHash(), $key);
}