<?php
class DataLoggeur
{
	protected $logs;
	
	public function __construct()
	{
		$this->logs = array();
	}
	
	public function addLog($log)
	{
		$this->logs[] = $log;
	}
	
	public function getLogs()
	{
		return $this->logs;
	}
	
	public function setLogs(array $logs)
	{
		$this->logs = $logs;
	}
	
	public function hasLogs()
	{
		return (count($this->logs) > 0)? true : false;
	}
}