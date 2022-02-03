<?php

/**
 * Description of class AlerteConsultationSearch
 * @author jb
 */
class AlerteConsultationSearch {

    const ELASTICSEARCH_INDEX = 'ALERTE';
    const ELASTICSEARCH_LIMIT = 20;

    protected $values;
    protected $index;
    protected $nbResult;
    protected $page;

    public function __construct($values = null) {
        if ($values) {
            $this->values = $values;
        }else{
            $this->values = array();
        }
        $this->nbResult = 0;
    }

    public function getValues() {
        return $this->values;
    }

    public function setValues($values) {
        $this->values = $values;
    }

    public function getNbResult() {
        return $this->nbResult;
    }

    public function setNbResult($nbResult) {
        $this->nbResult = $nbResult;
    }

    public function getLimit() {
        return self::ELASTICSEARCH_LIMIT;
    }

    public function getElasticSearchResult($page = null, $max_res = null) {
        $index = acElasticaManager::getType('ALERTE');
        $q = new acElasticaQuery();
        $elasticaQueryString = new acElasticaQueryQueryString();
        $elasticaQueryString->setDefaultOperator('AND');
        $qstr = '';
        foreach ($this->values as $node => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach($values as $value) {
                if (strpos($value, '!') === 0) {
                    $qstr .= '!(doc.'.$node.':'.substr($value, 1).') ';
                }else{
                    $qstr .= 'doc.'.$node.':'.$value.' ';
                }
            }
        }
        if ($qstr) {
            $elasticaQueryString->setQuery($qstr);
            $q->setQuery($elasticaQueryString);
        }

        $this->page = 1;
        if (!$max_res) {
            $max_res = self::ELASTICSEARCH_LIMIT;
        }
        if ($page > 1) {
            $q->setFrom(($page - 1 ) *  $max_res);
            $this->page = $page;
        }
		$q->setLimit($max_res);

        //Search on the index.
        $res = $index->search($q);
        $this->setNbResult($res->getTotalHits());
        return $res->getResults();
    }

    protected function getResult($query) {
        $search = $this->index->search($query);
        $this->setNbResult($search->getTotalHits());
        return $search->getResults();
    }

    protected function getCampagneCourante() {
        $year = date('Y');
        return $year . '-' . ($year + 1);
    }

	protected function getFilters()
	{
        $and_filter = new acElasticaFilterAnd();
            foreach ($this->values as $node => $value) {
                $result = $this->buildQueryString($node, $value);
                if ($result) {
                    $and_filter->addFilter($result);
                }
            }
        return $and_filter;
    }

	protected function buildQueryString($node, $value)
	{
		$string = ($value !== null && $value !== '')? $node.':'.$value : null;
        if (!$string) {
            return null;
        }
        $query_string = new acElasticaQueryQueryString($string);
        $query_filter = new acElasticaFilterQuery($query_string);
        return $query_filter;
    }

}
