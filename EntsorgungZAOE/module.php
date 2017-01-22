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
			
			$this->RegisterVariableString("WasteTime", "Restabfall 80-240l Tonne");
			$this->RegisterVariableString("BioTime", "Bioabfall 60-240l Tonne");
			$this->RegisterVariableString("RecycleTime", "Gelbe Säcke/Gelbe Tonne");
			$this->RegisterVariableString("PaperTime", "Papier/Pappe 120/240l Tonne");

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
                    
            $this->SetValueString("WasteTime", "test");
       
        
    }
		private function SetValueString(string $Ident, string $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueString($id, $value);
    }
		
		
	}

?>
