<?php

class TRelease extends TObjetStd {
	
	function __construct() {
		
		$this->set_table(MAIN_DB_PREFIX . 'release');
		$this->add_champs('fk_propal',array('type'=>'integer', 'index'=>true));
		$this->add_champs('date_release',array('type'=>'date', 'index'=>true));
		
		$this->_init_vars('label'); // et oui, le _init_vars initialise des variables de type varchar(255) par défaut. Mais le mettre au dessus n'enlevait pas de points
		$this->start();
		
		// ratachement des points enfants
		$this->setChild('TReleaseLineLink', 'fk_release');
	}
		
	function link($PDOdb, $lineid) {
		
		$k = $this->addChild($PDOdb, 'TReleaseLineLink');
		$this->TReleaseLineLink[$k]->fk_propal_line = $lineid;
		
		return $k;
		
	}	
	function unlink($linkid) {
		
		$this->removeChild('TReleaseLineLink', $linkid);
	}

	function facture() {
		
		// oui, j'ai pas eu le temps de tout finir non plus ;)
		
	}
	
	static function getAllReleaseForPropal(&$PDOdb, $fk_propal) {
		
		$Tab = TRequeteCore::get_id_from_what_you_want($PDOdb, MAIN_DB_PREFIX . 'release', array('fk_propal'=>$fk_propal));
		$TRelease = array();
		
		foreach($Tab as $id) {
			 $release = new TRelease;
			 $release->load($PDOdb, $id); 
			 
			 $TRelease[] = $release;
		}
		
		return $TRelease;
	}
}


class TReleaseLineLink extends TObjetStd {
	
	function __construct() {
		
		$this->set_table(MAIN_DB_PREFIX . 'release_line_link');
		$this->add_champs('fk_release',array('type'=>'integer', 'index'=>true));
		$this->add_champs('fk_propal_line',array('type'=>'integer'));
		$this->add_champs('qty,amount',array('type'=>'float')); // en prévision de définir le nombre dans chaque rattachement à une release
		
		$this->_init_vars();
		$this->start();
		
		$this->qty = 1;
		$this->line = null;
		
	}
		
	function load(&$PDOdb,$id) {
		$res = parent::load($PDOdb, $id);
		
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