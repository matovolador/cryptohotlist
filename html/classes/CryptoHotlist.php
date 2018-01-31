<?php
class CryptoHotlist{
	private $db;
	function __construct(){
		$this->db = new PDOdb();
	}

	public function getLastAPICall(){
		$res = $this->db->request("SELECT * FROM  api_calls ORDER BY time_created DESC LIMIT 1","select");
		return $res;
	}
	public function getFirstAPICall(){
		$res = $this->db->request("SELECT * FROM  api_calls ORDER BY time_created ASC LIMIT 1","select");
		return $res;	
	}
	public function getSymbols(){
		$res = $this->db->request("SELECT * FROM symbols","select");
		return $res;
	}
	public function getRanks($name=false){
		if ($name) $res = $this->db->request("SELECT * FROM ranks WHERE name = ?","select",[$name] );
		if (!$name) $res = $this->db->request("SELECT * FROM ranks","select" );
		return $res;
	}
	public function getRankChanges($name=false){
		$res = false;
		if ($name==false){
			$symbols = $this->getSymbols();
			
			$h = 0;
			for ($i=0;$i<count($symbols);$i++){
				$res[$h]=$this->db->request("SELECT * FROM cache WHERE name=? ORDER BY id DESC LIMIT 288","select",[$symbols[$i]['name']]);
			}	
		}else{
			$res=$this->db->request("SELECT * FROM cache WHERE name=? ORDER BY id DESC LIMIT 288","select",[$name]);
		}
		
		return $res;
		
	}

	public function getTopRanks($amount=100,$index=1,$max=false){
		$index = $index-1;
		$names = $this->db->request("SELECT * FROM ranks WHERE rank <= 100 ORDER BY id DESC LIMIT 100","select");
		$aux = false;
		$h = 0;
		for ($i=count($names)-1;$i>=0;$i--){
			$flag = false;
			$j=0;
			while($j<count($aux) && !$flag){

				if ($aux[$j]['name'] == $names[$i]['name']) $flag = true;

				$j++;
			}
			if (!$flag){
				$aux[$h]['name'] = $names[$i]['name'];
				$aux[$h]['symbol'] = $names[$i]['symbol'];
				$aux[$h]['rank'] = $names[$i]['rank'];
				$h++;
			}

		}
		if ($max!=false && $max>count($aux)) $max = count($aux);
		if ($max != false && $index < $max){
			$aux2 = false;
			$h=0;
			for ($i=$index;$i<$max;$i++){
				$aux2[$h] = $aux[$i];
				$h++;
			}	
			$aux = $aux2;
		}
		
		return $aux;
	}

	public function getPrice($name,$samples=288){
		$res = $this->db->request("SELECT * FROM cache WHERE name= ? ORDER BY id DESC LIMIT $samples","select",[$name]);
		return $res;
	}
	public function getPriceChange($name,$times=1,$max_time_hours=12){
		$samples = $max_time_hours*60/5;
		$res=$this->getPrice($name,$samples);
		$res= $this->flipArray($res);
		$aux = false;
		$h=0;
		$lastIndex = 0;
		for ($i=0;$i<count($res);$i++){

			if ($i==0){
				$lastIndex++;
				$aux[$h]['name'] = $res[$i]['name'];
				$aux[$h]['time'] =  $res[$i]['time_created'];
				$aux[$h]['price_usd'] = $res[$i]['price_usd'];
				$aux[$h]['price_five']= $res[$i]['price_five'];
				$aux[$h]['price_half']= $res[$i]['price_half'];
				$aux[$h]['price_hour']= $res[$i]['price_hour'];
				$aux[$h]['style_five'] = $this->setStyle($res[$i]['price_five']);
				$aux[$h]['style_half'] = $this->setStyle($res[$i]['price_half']);
				$aux[$h]['style_hour'] = $this->setStyle($res[$i]['price_hour']);
				$aux[$h]['vol'] = $res[$i]['volatility'];
				$aux[$h]['move'] = $res[$i]['movement'];
				
				$h++;
			}else{	
				if($i == $lastIndex * $times){
					$lastIndex++;
					$aux[$h]['name'] = $res[$i]['name'];
					$aux[$h]['time'] =  $res[$i]['time_created'];
					$aux[$h]['price_usd'] = $res[$i]['price_usd'];
					$aux[$h]['price_five']= $res[$i]['price_five'];
					$aux[$h]['price_half']= $res[$i]['price_half'];
					$aux[$h]['price_hour']= $res[$i]['price_hour'];
					$aux[$h]['style_five'] = $this->setStyle($res[$i]['price_five']);
					$aux[$h]['style_half'] = $this->setStyle($res[$i]['price_half']);
					$aux[$h]['style_hour'] = $this->setStyle($res[$i]['price_hour']);
					$aux[$h]['vol'] = $res[$i]['volatility'];
					$aux[$h]['move'] = $res[$i]['movement'];
					$h++;
				}
			}

		}

		$aux[count($aux)-1]['style_move'] = $this->setStyle($aux[count($aux)-1]['move']);
		$aux[count($aux)-1]['move'] = "%".$aux[count($aux)-1]['move'];

		

		$aux=$this->flipArray($aux);
		return $aux;
	}

	private function getTotalMovement($res,$times){
		for ($i=0;$i<count($res);$i++){

			if ($i==0){
				if ($times == 1){
					$lastVol = $res[$i]['price_five'];
				}else if($times == 6){
					$lastVol = $res[$i]['price_half'];
				}else if ($times == 12){
					$lastVol = $res[$i]['price_hour'];
				}
				
				
				$res[$i]['move'] = $lastVol;
			}else{
				$lastVol = $res[$i-1]['move'];
				if ($times == 1){
					$current_percent = $res[$i]['price_five'];
				}else if($times == 6){
					$current_percent = $res[$i]['price_half'];
				}else if ($times == 12){
					$current_percent = $res[$i]['price_hour'];
				}
				$res[$i]['move'] = $lastVol + $current_percent;
			}
		}
		
		$res[count($res)-1]['style_move'] = $this->setStyle($res[count($res)-1]['move']);
		$res[count($res)-1]['move'] = "%".number_format($res[count($res)-1]['move'],2);
		return $res;
	}

	private function getVolatility($res,$times){
		for ($i=0;$i<count($res);$i++){

			if ($i==0){
				if ($times == 1){
					$lastVol = $res[$i]['price_five'];
				}else if($times == 6){
					$lastVol = $res[$i]['price_half'];
				}else if ($times == 12){
					$lastVol = $res[$i]['price_hour'];
				}

				if ($lastVol<0) $lastVol = $lastVol*(-1);

				$res[$i]['vol'] = $lastVol;
			}else{
				$lastVol = $res[$i-1]['vol'];
				if ($times == 1){
					$current_percent = $res[$i]['price_five'];
				}else if($times == 6){
					$current_percent = $res[$i]['price_half'];
				}else if ($times == 12){
					$current_percent = $res[$i]['price_hour'];
				}
				if ($current_percent<0) $current_percent = $current_percent *(-1);
				$res[$i]['vol'] = $lastVol + $current_percent;
			}
		}
		$res[count($res)-1]['vol'] = number_format($res[count($res)-1]['vol']/count($res),2);
		return $res;
	}
	
	private function flipArray($array){
		$h=0;
		$aux = array();
		for ($i=count($array)-1;$i>=0;$i--){
			$aux[$h]=$array[$i];
			$h++;
		}
		return $aux;
	}

	public function setStyle($percent){
		$res = "";
		if ($percent<0 && $percent >= -1 || $percent>0 && $percent <= 1){
			return "";	
		}
		if ($percent>0){
			$color = "green";
		}
		if ($percent<0){
			$color = "red";
			$percent = $percent * (-1);
		}
		if ($percent  == 0){
			return "";
		}

		if ($percent>=10) {
			$percent = 10;
		}else if ($percent>=9){
			$percent = 9;
		}else if ($percent >=8){
			$percent = 8;
		}else if($percent >=7){
			$percent = 7;
		}else if ($percent >= 6){
			$percent = 6;
		}else if($percent>=5){
			$percent = 5;
		}else if ($percent >=4){
			$percent = 4;
		}else if($percent >=3){
			$percent = 3;
		}else if($percent >= 2){
			$percent = 2;
		}else if($percent >=1){
			$percent = 1;
		}else if ($percent >0){
			$percent = 0;
		}

		
		return $color."-".$percent;
		
	}

}