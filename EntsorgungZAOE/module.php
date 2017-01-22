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
			
			$this->RegisterVariableString("tonne", "tonne");
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
                
        $jahr = date("Y");
        $link = $this->ReadPropertyString('http://www.zaoe.de/ical/download/' . ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=2&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B4%5D=5&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B5%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16');
       		
		$this->SendDebug('GET', $link, 0);
        $meldung = @file($link);
        if ($meldung === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
        $this->SendDebug('LINES', count($meldung), 0);

		$tonne = "Keine Tonne";
		
		$anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            if (strstr($meldung[$count], "SUMMARY:"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $this->SendDebug('SUMMARY', $name, 0);
                $this->SendDebug('START', $start, 0);
                $this->SendDebug('END', $ende, 0);
                $jetzt = date("Ymd") . "\n";
                if (($jetzt >= $start) and ( $jetzt <= $ende))
                {
                    $tonne = explode(' ', $name)[0];
                    $this->SendDebug('FOUND', $tonne, 0);
                }
            }
        }
	$this->SetValueString("tonne", $tonne);
		return $tonne;
    }
	
	private function SetValueString(string $Ident, string $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueString($id, $value);
    }
		
		
	}

?>
