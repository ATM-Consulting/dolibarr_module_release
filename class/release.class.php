<?php

class Release extends SeedObject{
	
	public $table_element = 'release';
	
	public $fk_element = 'fk_release';
	
	public $childtable = array('ReleaseLineLink');
	
	function __construct(&$db) {
		
		$this->db = $db;
		$this->table_element = 'release';
		$this->fields=array(
				'fk_propal'=>array('type'=>'integer','index'=>true)
				,'label'=>array('type'=>'string')
		);
		
		//TODO par rapport à la définition de besoin, ne manque-t-il pas un champs ?
		//HELP Tu as lu le README ?
		
		$this->init();
		
		
	}
		
	function link( $lineid) {
		
		$k = $this->addChild('ReleaseLineLink');
		$this->TReleaseLineLink[$k]->fk_propal_line = $lineid;
		
		return $k;
		
	}	
	function unlink($linkid) {
		
		$this->removeChild('ReleaseLineLink', $linkid);
	}

	function facture() {
		
		// oui, j'ai pas eu le temps de tout finir non plus ;)
		
	}
	
	static function getAllReleaseForPropal($fk_propal) {
		
		global $db;

		$TRelease = array();
		
		$resql = $db->query("SELECT rowid FROM ".MAIN_DB_PREFIX."release WHERE fk_propal=".$fk_propal);
		
		//TODO rendre ça plus lisible
		//HELP En plus ça ne marche pas, j'ai true dans le tableau
		
		while($obj = $db->fetch_object($resql)) {
			$TRelease[] = (new Release($db))->fetch($obj->rowid); 
		}
		
		return $TRelease;
	}
}


class ReleaseLineLink extends SeedObject{
	
	public $table_element = 'release_line_link';
	
	function __construct(&$db) {
		
		$this->db = $db;
		$this->table_element = 'release_line_link';
		$this->fields=array(
				'fk_release'=>array('type'=>'integer','index'=>true)
				,'fk_propal_line'=>array('type'=>'integer')
				,'qty'=>array('type'=>'float') // en prévision de définir le nombre dans chaque rattachement à une release
				,'amount'=>array('type'=>'float')
		);
		
		$this->init();
		
		//TODO définir le champs quantité à 1 par défaut
		$this->line = null;
		
	}
		
	function fetch($id) {
		$res = parent::fetch($id);
		
		if($this->fk_propal_line) {
			global $db,$conf,$user,$langs;
			
			$this->line = new PropaleLigne($db);
			$this->line->fetch($this->fk_propal_line);
			
			//montant futur à facturer
			$this->amount = $this->qty / $line->qty * $line->total_ht;
		}
		
		return $res;
	}
}