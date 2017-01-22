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
			$this->RegisterTimer("Updatetonne", 15 * 60 * 1000, 'Tonne_Update(\$_IPS[\'TARGET\']);'); 
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
        try
        {
            $holiday = $this->GetFeiertag();
        }
        catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            $this->SendDebug('ERROR', $exc->getMessage(), 0);
            return false;
        }


        $this->SetValueString("SchoolHoliday", $holiday);
        if ($holiday == "Keine Ferien")
        {
            $this->SetValueBoolean("IsSchoolHoliday", false);
        }
        else
        {
            $this->SetValueBoolean("IsSchoolHoliday", true);
        }
        return true;
    }
		
		
		
		public function RequestInfo()
		{
		
			
			$city = $this->ReadPropertyString("city");

			$buffer = file_get_contents("http://www.zvo-entsorgung.de/service/abfall-abfuhrkalender-".date("Y").".html?tx_aak_pi1%5B__referrer%5D%5BextensionName%5D=Aak&tx_aak_pi1%5B__referrer%5D%5BcontrollerName%5D=Form&tx_aak_pi1%5B__referrer%5D%5BactionName%5D=index&tx_aak_pi1%5B__hmac%5D=a%3A3%3A%7Bs%3A8%3A%22cityForm%22%3Ba%3A1%3A%7Bs%3A4%3A%22city%22%3Bi%3A1%3B%7Ds%3A6%3A%22action%22%3Bi%3A1%3Bs%3A10%3A%22controller%22%3Bi%3A1%3B%7D0e741c742f645570bede528d6702485f9bf59d6d&tx_aak_pi1%5BcityForm%5D%5Bcity%5D=".urlencode($city));
			$buffer = stristr($buffer, "Ihre n&auml;chsten Abfuhrtermine f&uuml;r:");

			$buffer = stristr($buffer, "Gelber Sack, Biotonne, Restm&uuml;lltonne:");
			$buffer = stristr($buffer, "<td class=\"right\">");
			$buffer = stristr($buffer, ">");
			$date1 = substr($buffer, 1, strpos($buffer, "</td>")-1);
			SetValue($this->GetIDForIdent("WasteTime"), strtotime($date1));
			SetValue($this->GetIDForIdent("BioTime"), strtotime($date1));
			SetValue($this->GetIDForIdent("RecycleTime"), strtotime($date1));

			$buffer = stristr($buffer, "Blaue Tonne:");
			$buffer = stristr($buffer, "<td class=\"right\">");
			$buffer = stristr($buffer, ">");
			$date2 = substr($buffer, 1, strpos($buffer, "</td>")-1);
			SetValue($this->GetIDForIdent("PaperTime"), strtotime($date2));
			
		}
	
	}

?>
