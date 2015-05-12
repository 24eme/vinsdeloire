<?php
class DRMControleEngagement extends DRMControle
{
	const TYPE = 'engagement';
	public function __construct($code) 
	{		
		parent::__construct(self::TYPE, self::TYPE.'_'.$code, null);
	}
}