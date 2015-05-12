<?php
class DocumentValidationControle
{
    protected $type;
    protected $code;
    protected $lien;
    protected $message;
    protected $info;

    public function __construct($type, $code, $message) {
        $this->setType($type);
        $this->setCode($code);
        $this->setMessage($message);
    }

    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function getLien()
    {
        return $this->lien;
    }
    
    public function setLien($lien)
    {
        $this->lien = $lien;
    }

    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setInfo($info) {
        $this->info = $info;
    }

    public function getInfo() {
        
        return $this->info;
    }
    
    public function __toString()
    {
        if (!$this->getInfo()) {
            return sprintf("%s", $this->getMessage());
        }

        return sprintf("%s : %s", $this->getMessage(), $this->getInfo());
    }
}