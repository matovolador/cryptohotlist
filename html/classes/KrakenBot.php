<?php
class KrakenBot{
	private $db;
	function __construct(){
		$this->db = new PDOdb("kraken_bot");
	}

	public function getVolumes($name,$sample=false){
		if ($sample!=false AND !is_numeric($sample)) return false;
		if ($sample==false || $sample == 1){
			$res=$this->db->request("SELECT * FROM volumes WHERE name = ? ORDER BY id DESC LIMIT ".SAMPLE_TIME_CHART,"select",[$name]);
		}else{
			$res=$this->db->request("SELECT * FROM volumes WHERE name = ? AND id mod $sample = 1 ORDER BY id DESC LIMIT ".SAMPLE_TIME_CHART,"select",[$name]);

		}
		
		return $this->flipArray($res);
	}
	public function getPrices($name,$sample=false){
		if ($sample!=false AND !is_numeric($sample)) return false;
		if ($sample==false || $sample == 1){
			$res=$this->db->request("SELECT * FROM prices WHERE name = ? ORDER BY id DESC LIMIT ".SAMPLE_TIME_CHART,"select",[$name]);
		}else{
			$res=$this->db->request("SELECT * FROM prices WHERE name = ? AND id mod $sample = 1 ORDER BY id DESC LIMIT ".SAMPLE_TIME_CHART,"select",[$name]);	
		}
		return $this->flipArray($res);
	}
	public function getHistorical($name,$sample=false){
		if ($sample==false || $sample == 1){
			$res=$this->db->request("SELECT * FROM prices WHERE name = ? AND id ORDER BY id DESC","select",[$name]);
		}else{
			$res=$this->db->request("SELECT * FROM prices WHERE name = ? AND id mod $sample = 1 ORDER BY id DESC","select",[$name]);
		}
		return $this->flipArray($res);
	}

	public function getLastId($name){
		$res = $this->db->request("SELECT * FROM prices WHERE name=? ORDER BY id DESC LIMIT 1","select",[$name],true);
		return $res['id'];
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
}