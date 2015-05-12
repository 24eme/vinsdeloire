<?php

/**
* 
*/
class acCouchdbJsonNative
{
	
	protected $_stdclass = null;
	protected $_array = null;
	protected $_flat_array = null;

	public function __construct(stdClass $stdclass)
	{
		$this->_stdclass = $stdclass;
	}

	public function toStdClass() {
		return $this->_stdclass;
	}

	public function toArray() {
		if (is_null($this->_array)) {
			$this->_array = $this->stdClassToArray($this->_stdclass);
		}

		return $this->_array;
	}

	public function toFlatArray() {
		if (is_null($this->_flat_array)) {
			$this->_flat_array = $this->flattenArray($this->toArray());
		}

		return $this->_flat_array;
	}

	public function diff(acCouchdbJsonNative $object) {

		return array_diff_assoc($this->toFlatArray(), $object->toFlatArray());
	}

	public function equal(acCouchdbJsonNative $object) {

		return count($this->diff($object)) == 0 && count($object->diff($this)) == 0;
	}

	protected function stdClassToArray($stdclass) {

		return json_decode(json_encode($stdclass), true);
	}

	protected function flattenArray($array, $prefix = null, $decorator = "/")  {
		$flat_array = array();

		foreach($array as $key => $value) {
			if(is_array($value))  {
				$flat_array = array_merge($flat_array, $this->flattenArray($value, $prefix.$decorator.$key));
			} else {
				$flat_array[$prefix.$decorator.$key] = $value;
			}
		}

		return $flat_array;
	}
}