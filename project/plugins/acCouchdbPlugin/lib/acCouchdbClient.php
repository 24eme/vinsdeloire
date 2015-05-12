<?php

/* This file is part of the acCouchdbPlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acCouchdbClient model.
 * 
 * @package    acCouchdbPlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */

class acCouchdbClient extends couchClient {
    
    const HYDRATE_ON_DEMAND = 1;
    const HYDRATE_ON_DEMAND_WITH_DATA = 2;
    const HYDRATE_JSON = 3;
    const HYDRATE_ARRAY = 4;
    const HYDRATE_DOCUMENT = 5;

    /**
     * Store an acCouchdbDocument in the database
     * 
     * @param acCouchdbDocument $document An acCouchdbDocoument class
     * @return void
     */
    public function save($document) {
        $method = 'POST';
        $url = '/' . urlencode($this->dbname);
        if (!$document->isNew()) {
            $method = 'PUT';
            $url.='/' . urlencode($document->get('_id'));
        }
        $resultat = $this->_queryAndTest($method, $url, array(200, 201), array(), $document->getData());
        return $resultat;
    }

    /**
     * Store an acCouchdbDocument in the database
     * 
     * @param acCouchdbDocument $document An acCouchdbDocoument class
     * @return void
     * @deprecated
     * @see method save()
     */
    public function saveDocument($document) {
        $method = 'POST';
        $url = '/' . urlencode($this->dbname);
        if (!$document->isNew()) {
            $method = 'PUT';
            $url.='/' . urlencode($document->get('_id'));
        }
        return $this->_queryAndTest($method, $url, array(200, 201), array(), $document->getData());
    }

    /**
     * Remove an acCouchdbDocument from the database
     * 
     * @param acCouchdbDocument $document An acCouchdbDocoument class
     * @return mixed
     */
    public function delete($document) {

        return $this->deleteDoc($document->getData());
    }

    /**
     * Remove an acCouchdbDocument from the database
     * 
     * @param acCouchdbDocument $document An acCouchdbDocoument class
     * @return mixed 
     * @deprecated
     * @see method delete()
     */
    public function deleteDocument($document) {

        return $this->deleteDoc($document->getData());
    }

    /**
     * Fetch an acCouchdbDocument from database
     * 
     * @param string $id The couchdb document _id
     * @param int $hydrate Hydration mode: see acCouchdbClient::HYDRATE_* constants
     * @return acCouchdbDocument|stdClass|array Depending on hydration mode. null if no result.
     */
    public function find($id, $hydrate = self::HYDRATE_DOCUMENT, $force_return_ls = false) {
        try {
            if ($hydrate == self::HYDRATE_DOCUMENT) {
                $data = $this->getDoc($id);

                return $this->create($data, $force_return_ls);
            } elseif ($hydrate == self::HYDRATE_JSON) {

                return $this->getDoc($id);
            } elseif ($hydrate == self::HYDRATE_ARRAY) {

                return $this->asArray()->getDoc($id);
            } else {
                
                throw new acCouchdbException('This hydration method does not exist');
            }
        } catch (couchException $exc) {

            return null;
        }
    }

    /**
     * Fetch an acCouchdbDocument from database
     * 
     * @param string $id The couchdb document _id
     * @param int $hydrate Hydration mode: see acCouchdbClient::HYDRATE_* constants
     * @return acCouchdbDocument|stdClass|array Depending on hydration mode. null if no result.
     * @deprecated
     * @see method find()
     */
    public function retrieveDocumentById($id, $hydrate = self::HYDRATE_DOCUMENT, $force_return_ls = false) {
        
        return $this->find($id, $hydrate, $force_return_ls);
    }

    /**
     * Transform a couchdb document to an acCouchdbDocument class
     * 
     * @param stdClass $data The json data of couchdb document
     * @return acCouchdbDocument
     */
    public function create($data, $force_return_ls = false) {
        if (!isset($data->type)) {
            
            throw new acCouchdbException('Property "type" ($data->type)');
        }
        if (!class_exists($data->type)) {
            
            throw new acCouchdbException('Class "' . $data->type . '" not found');
        }
        
        $doc = new $data->type();
        $doc->loadFromCouchdb($data);

        if($doc->getType() == "LS" && $force_return_ls == false )
          return $this->find($doc->getPointeur());
        
        return $doc;
    }

    /**
     * Transform a couchdb document to an acCouchdbDocument class
     * 
     * @param stdClass $data The json data of couchdb document
     * @return acCouchdbDocument
     * @deprecated
     * @see method create()
     */
    public function createDocumentFromData($data) {
        
        return $this->create($data);
    }

    /**
     * Fecth a collection of couchdb document from a view
     * 
     * @param string $id The couchdb view document _id
     * @param string $name The name of the view
     * @param int $hydrate Hydration mode: see acCouchdbClient::HYDRATE_* constants
     * @return acCouchdbValueCollection Collection of the view results
     */
    public function executeView($id, $name, $hydrate = self::HYDRATE_DOCUMENT) {
        if ($hydrate != self::HYDRATE_ON_DEMAND) {
            $this->include_docs(true);
        }
        if ($hydrate == self::HYDRATE_ARRAY) {
            $this->asArray();
        }
        return new acCouchdbValueCollection($this->getView($id, $name), $hydrate);
    }

    /**
     * Fecth a collection of couchdb documents
     * 
     * @param int $hydrate Hydration mode: see acCouchdbClient::HYDRATE_* constants
     * @return acCouchdbValueCollection Collection class of couchdb documents 
     */
    public function execute($hydrate = self::HYDRATE_DOCUMENT) {
        if ($hydrate != self::HYDRATE_ON_DEMAND) {
            $this->include_docs(true);
        }
        if ($hydrate == self::HYDRATE_ARRAY) {
            $this->asArray();
        }
        
        return new acCouchdbDocumentCollection($this->getAllDocs(), $hydrate);
    }

    public function rollBack($id, $revision = null) {
        
        $doc_to_roll_back = $this->getPreviousDoc($id, $revision);

        $doc = $this->find($id, self::HYDRATE_JSON);

        $doc_to_roll_back->_rev = $doc->_rev;

        $ret = $this->storeDoc($doc_to_roll_back);

        $doc_to_roll_back->_rev = $ret->rev;

        return $doc_to_roll_back;   
    }

    public function rollBackDoc($doc, $revision = null) {
        
        $doc_to_roll_back = $this->getPreviousDoc($id, $revision);
        $doc_to_roll_back->_rev = $doc->_rev;

        return $ret = $this->storeDoc($doc_to_roll_back);;   
    }

    public function getPreviousDoc($id, $revision = null) {
        if(!$revision) {
            $revision = "-1";
        }
        
        if(!preg_match('/^[0-9]+-.+/', $revision)) {
            $revision = $this->findRevision($id, $revision);
        }

        $doc_to_roll_back = $this->rev($revision)->find($id, self::HYDRATE_JSON);

        if(!$doc_to_roll_back) {

            throw new sfException(sprintf("Document to rollback %s@%s does not exist", $id, $revision));
        }

        return $doc_to_roll_back;
    }

    public function findRevision($id, $revision_search) {
        $revisions = $this->getRevisions($id);
        $revision_num = null;

        if(preg_match("/^[0-9]+$/", $revision_search)) {
            $revision_num = $revision_search;
        }

        if(preg_match("/^(-|\+)[0-9]+$/", $revision_search)) {
            $revision_num = max(array_keys($revisions)) + (int)$revision_search;
        }

        if(preg_match("/^[0-9]+-.+/", $revision_search)) {
            foreach($revisions as $num => $rev) {
                if($rev[0] == $revision_search) {
                   $revision_num = $num;
                   break;
                }
            }
        }

        if(!$revision_num || !isset($revisions[$revision_num])) {
            
            throw new sfException(sprintf("La révision %s n'a pas été trouvé", $revision_search));
        }

        return $revisions[$revision_num][0];
    }

    public function getRevisions($id) {
        
        $doc = $this->revs_info()->find($id, self::HYDRATE_JSON);
        
        if(!$doc) {

            throw new sfException(sprintf("Doc %s not found", $id));
        }

        $revisions = array();
        foreach($doc->_revs_info as $rev) {
            $revisions[preg_replace('/^([0-9]+)-.+/', '\1', $rev->rev)] = array($rev->rev, $rev->status);
        }

        return $revisions;
    }

}
