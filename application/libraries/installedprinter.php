<?php

/**
 * It represents a printer installed in the client machine with an associated OS driver.
 */
class Installedprinter{
	
    /**
     * Creates an instance of the InstalledPrinter class with the specified printer name.
     * @param string $printerName The name of the printer installed in the client machine.
     */
    public function __construct($printerName = '') {
        $this->printerId = chr(1);
        $this->printerName = $printerName;
    }
    
    
    public function serialize() {
        
        if (Utils::isNullOrEmptyString($this->printerName)){
             throw new Exception("The specified printer name is null or empty.");
        }
        
        return $this->printerId.$this->printerName;
    }
	
    public $printerId;
}