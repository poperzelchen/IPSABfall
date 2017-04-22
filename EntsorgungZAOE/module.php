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
			$this->RegisterTimer("Updatetonne", 15 * 60 * 1000, 'ZAOE_Update($_IPS[\'TARGET\']);'); 
		}		
	
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			$this->RegisterVariableString("tonne", "Tonne");
			$this->RegisterVariableBoolean("IsAbholung", "Ist Abholung ?");
			$this->RegisterVariableString("WasteTime", "Restabfall 80-240l Tonne");
			$this->RegisterVariableString("BioTime", "Bioabfall 60-240l Tonne");
			$this->RegisterVariableString("RecycleTime", "Gelbe Säcke/Gelbe Tonne");
			$this->RegisterVariableString("PaperTime", "Papier/Pappe 120/240l Tonne");
		
			$this->Update();
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
            $TonneB = $this->GetTonneB()[0];
			$AbholungB = $this->GetTonneB()[1];
			$TonneP = $this->GetTonneP()[0];
			$AbholungP = $this->GetTonneP()[1];
			$TonneG = $this->GetTonneG()[0];
			$AbholungG = $this->GetTonneG()[1];
			$TonneR = $this->GetTonneR()[0];
			$AbholungR = $this->GetTonneR()[1];
		}
        catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            $this->SendDebug('ERROR', $exc->getMessage(), 0);
            return false;
        }

		$Tonnealle = $TonneB .", ". $TonneP .", ". $TonneG .", ". $TonneR ;
		$Tonne = str_replace("Keine Tonne", "", $Tonnealle);
        $Tonne = trim($Tonne, ", ");
		
		$this->SetValueString("BioTime", $AbholungB);
		$this->SetValueString("PaperTime", $AbholungP);
		$this->SetValueString("RecycleTime", $AbholungG);
		$this->SetValueString("WasteTime", $AbholungR);
		if ($Tonne == "")
        {
            $this->SetValueBoolean("IsAbholung", false);
			$this->SetValueString("tonne", "Keine Abholung");
        }
        else
        {
            $this->SetValueBoolean("IsAbholung", true);
			$this->SetValueString("tonne", $Tonne);
        }
        return true;
    }
		

			   public function GetTonneB()
    {
 
		if ((int)date("md") < 110)
        {
            $jahr = date("Y") - 1;
            $link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
			$this->SendDebug('GET', $link, 0);
            $meldung = @file($link);
            if ($meldung === false)
                throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
            $this->SendDebug('LINES', count($meldung), 0);
        } else
        {
            $meldung = array();
        }       
               $jahr = date("Y");
 		
				$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		
		$this->SendDebug('GET', $link, 0);
        $meldung2 = @file($link);
        if ($meldung2 === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
        $this->SendDebug('LINES', count($meldung2), 0);
		
		$meldung = array_merge($meldung, $meldung2);
		$tonne = "Keine Tonne";
		$tonnedate = "-";
		
		$anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            if (strstr($meldung[$count], "SUMMARY:Bioabfall"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $this->SendDebug('SUMMARY', $name, 0);
                $this->SendDebug('START', $start, 0);
                $this->SendDebug('END', $ende, 0);
                $jetzt = date("Ymd") . "\n";
				$jetzt1 = date("Ymd",time() + 86400);
				$jetzt2 = date("Ymd",time() + 172800);
				$jetzt3 = date("Ymd",time() + 259200);
				$jetzt4 = date("Ymd",time() + 345600);
				$jetzt5 = date("Ymd",time() + 432000);
				$jetzt6 = date("Ymd",time() + 518400);				
				$jetzt7 = date("Ymd",time() + 604800);
				if (($jetzt7 == $start) || ($jetzt6 == $start) || ($jetzt5 == $start) || ($jetzt4 == $start) || ($jetzt3 == $start) || ($jetzt2 == $start) || ($jetzt1 == $start) || ($jetzt == $start) )
                { 
					$tonnedate = date("d.m.Y", strtotime($start));
				}
				if (($jetzt +1 == $start))
                {
					$tonne = "Morgen " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
				elseif ($jetzt == $start)
				{
					$tonne = "Heute " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
            }
			
        }
	
				return array($tonne,$tonnedate);
		
    }
	
			   public function GetTonneP()
    {
 
		if ((int)date("md") < 110)
        {
            $jahr = date("Y") - 1;
            $link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
			$this->SendDebug('GET', $link, 0);
            $meldung = @file($link);
            if ($meldung === false)
                throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
            $this->SendDebug('LINES', count($meldung), 0);
        } else
        {
            $meldung = array();
        }       
               $jahr = date("Y");
 
		$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		$this->SendDebug('GET', $link, 0);
        $meldung2 = @file($link);
        if ($meldung2 === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
        $this->SendDebug('LINES', count($meldung2), 0);
		
		$meldung = array_merge($meldung, $meldung2);
		$tonne = "Keine Tonne";
		$tonnedate = "-";

		
		$anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            if (strstr($meldung[$count], "SUMMARY:Papier"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $this->SendDebug('SUMMARY', $name, 0);
                $this->SendDebug('START', $start, 0);
                $this->SendDebug('END', $ende, 0);
                 $jetzt = date("Ymd") . "\n";
				$jetzt1 = date("Ymd",time() + 86400);
				$jetzt2 = date("Ymd",time() + 172800);
				$jetzt3 = date("Ymd",time() + 259200);
				$jetzt4 = date("Ymd",time() + 345600);
				$jetzt5 = date("Ymd",time() + 432000);
				$jetzt6 = date("Ymd",time() + 518400);				
				$jetzt7 = date("Ymd",time() + 604800);
				if (($jetzt7 == $start) || ($jetzt6 == $start) || ($jetzt5 == $start) || ($jetzt4 == $start) || ($jetzt3 == $start) || ($jetzt2 == $start) || ($jetzt1 == $start) || ($jetzt == $start) )
                {
					$tonnedate = date("d.m.Y", strtotime($start));
                }
				if (($jetzt +1 == $start))
                {
					$tonne = "Morgen " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
				elseif ($jetzt == $start)
				{
					$tonne = "Heute " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
            }
			
        }
	
		
		return array($tonne,$tonnedate);
		
    }
	
			   public function GetTonneR()
    {
 
		if ((int)date("md") < 110)
        {
            $jahr = date("Y") - 1;
            //$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
			$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr .'&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr .'&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		
			$this->SendDebug('GET', $link, 0);
            $meldung = @file($link);
            if ($meldung === false)
                throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
            $this->SendDebug('LINES', count($meldung), 0);
        } else
        {
            $meldung = array();
        }       
               $jahr = date("Y");
 
	//	$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr .'&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr .'&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		$this->SendDebug('GET', $link, 0);
        $meldung2 = @file($link);
        if ($meldung2 === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
        $this->SendDebug('LINES', count($meldung2), 0);
		
	//	$meldung = array_merge($meldung, $meldung2);
	$meldung = $meldung2
	$tonne = "Keine Tonne";
		$tonnedate = "-";

		
		$anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            
			if (strstr($meldung[$count], "SUMMARY:Restabfall"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $this->SendDebug('SUMMARY', $name, 0);
                $this->SendDebug('START', $start, 0);
                $this->SendDebug('END', $ende, 0);
                $jetzt = date("Ymd") . "\n";
				$jetzt1 = date("Ymd",time() + 86400);
				$jetzt2 = date("Ymd",time() + 172800);
				$jetzt3 = date("Ymd",time() + 259200);
				$jetzt4 = date("Ymd",time() + 345600);
				$jetzt5 = date("Ymd",time() + 432000);
				$jetzt6 = date("Ymd",time() + 518400);				
				$jetzt7 = date("Ymd",time() + 604800);
				if (($jetzt5 == $start) || ($jetzt4 == $start) || ($jetzt3 == $start) || ($jetzt2 == $start) || ($jetzt1 == $start) || ($jetzt == $start) )
                {
					$tonnedate = date("d.m.Y", strtotime($start));
                }
				if (($jetzt +1 == $start))
                {
					$tonne = "Morgen " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
				elseif ($jetzt == $start)
				{
					$tonne = "Heute " . explode(' ', $name) [0];
					$this->SendDebug('FOUND', $tonne , 0);
                }
            }
        }
			
		return array($tonne,$tonnedate);
	}
			   public function GetTonneG()
    {
 
		if ((int)date("md") < 110)
        {
            $jahr = date("Y") - 1;
            $link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
			$this->SendDebug('GET', $link, 0);
            $meldung = @file($link);
            if ($meldung === false)
                throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
            $this->SendDebug('LINES', count($meldung), 0);
        } else
        {
            $meldung = array();
        }       
               $jahr = date("Y");
 
		$link = 'http://www.zaoe.de/ical/download/' . $this->ReadPropertyString("strasse") . '/16/?tx_kalenderausgaben_pi3%5Bauswahl_start_us%5D='. $jahr . '-01-01&tx_kalenderausgaben_pi3%5Bauswahl_end_us%5D='. $jahr . '-12-31&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B0%5D=1&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B1%5D=3&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B2%5D=4&tx_kalenderausgaben_pi3%5Bauswahl_tonnen_ids%5D%5B3%5D=6&tx_kalenderausgaben_pi3%5Bauswahl_start%5D=01.01.'. $jahr . '&tx_kalenderausgaben_pi3%5Bauswahl_end%5D=31.12.'. $jahr . '&tx_kalenderausgaben_pi3%5Bswitch%5D=ical&tx_kalenderausgaben_pi3%5Bauswahl_zeitraum%5D=16';
		$this->SendDebug('GET', $link, 0);
        $meldung2 = @file($link);
        if ($meldung2 === false)
            throw new Exception("Cannot load iCal Data.", E_USER_NOTICE);
        $this->SendDebug('LINES', count($meldung2), 0);
		
		$meldung = array_merge($meldung, $meldung2);
		$tonne = "Keine Tonne";
		$tonnedate = "-";

		
		$anzahl = (count($meldung) - 1);

        for ($count = 0; $count < $anzahl; $count++)
        {
            if (strstr($meldung[$count], "SUMMARY:Gelbe"))
            {
                $name = trim(substr($meldung[$count], 8));
                $start = trim(substr($meldung[$count + 1], 19));
                $ende = trim(substr($meldung[$count + 2], 17));
                $this->SendDebug('SUMMARY', $name, 0);
                $this->SendDebug('START', $start, 0);
                $this->SendDebug('END', $ende, 0);
                $jetzt = date("Ymd") . "\n";
				$jetzt1 = date("Ymd",time() + 86400);
				$jetzt2 = date("Ymd",time() + 172800);
				$jetzt3 = date("Ymd",time() + 259200);
				$jetzt4 = date("Ymd",time() + 345600);
				$jetzt5 = date("Ymd",time() + 432000);
				$jetzt6 = date("Ymd",time() + 518400);				
				$jetzt7 = date("Ymd",time() + 604800);
				if (($jetzt7 == $start) || ($jetzt6 == $start) || ($jetzt5 == $start) || ($jetzt4 == $start) || ($jetzt3 == $start) || ($jetzt2 == $start) || ($jetzt1 == $start) || ($jetzt == $start) )
                 {
					$tonnedate = date("d.m.Y", strtotime($start));
                }
				if (($jetzt +1 == $start))
                {
					$tonne = "Morgen gelber Sack";
					$this->SendDebug('FOUND', $tonne , 0);
                }
				elseif ($jetzt == $start)
				{
					$tonne = "Heute gelber Sack";
					$this->SendDebug('FOUND', $tonne , 0);
                }
            }
			
        }
	
		
		return array($tonne,$tonnedate);
		
    }	
		
    
	
	  private function SetValueBoolean(string $Ident, bool $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueBoolean($id, $value);
    }
	
	private function SetValueString(string $Ident, string $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueString($id, $value);
    }
		
		
	}

?>
