<?php
class DRMControleWarning extends DRMControle
{
	const TYPE = 'warning';
	public function __construct($code, $lien) {
		parent::__construct(self::TYPE, self::TYPE.'_'.$code, $lien);
	}
}