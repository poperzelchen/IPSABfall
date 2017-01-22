<?

	class Abfallkalender extends IPSModule
	{

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			
			$this->RegisterPropertyString("region", "4");
			$this->RegisterPropertyString("area", "789");
			$this->RegisterPropertyString("ort", "916");
			$this->RegisterPropertyString("strasse", "4411");
			$this->RegisterTimer("Updatetonne", 15 * 60 * 1000, 'ZAOE_Update(\$_IPS[\'TARGET\']);'); 
		}		
	
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			$this->RegisterVariableInteger("WasteTime", "Restabfall 80-240l Tonne", "~UnixTimestamp");
			$this->RegisterVariableInteger("BioTime", "Bioabfall 60-240l Tonne", "~UnixTimestamp");
			$this->RegisterVariableInteger("RecycleTime", "Gelbe Säcke/Gelbe Tonne", "~UnixTimestamp");
			$this->RegisterVariableInteger("PaperTime", "Papier/Pappe 120/240l Tonne", "~UnixTimestamp");

		}
	
		/**
		* This function will be available automatically after the module is imported with the module control.
		* Using the custom prefix this function will be callable from PHP and JSON-RPC through:
		*
		* EZVO_RequestInfo($id);
		*
		*/
		
		    public function Update()
    {
                    
            $this->SetValueInteger("WasteTime", date);
       
        return true;
    }
		
		
		
	}

?>
