<?php
/**
 * Model for DRMGenre
 *
 */

class DRMGenre extends BaseDRMGenre {

    /**
     *
     * @return DRMGenre
     */
    public function getCertification() {

        return $this->getParentNode();
    }
    

    public function getChildrenNode() {

        return $this->appellations;
    }

}