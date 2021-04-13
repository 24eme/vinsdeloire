<?php

class myUser extends sfBasicSecurityUser {
    const CREDENTIAL_ADMIN = "admin";

    public function hasTeledeclarationDrm() {
        return false;
    }

}
