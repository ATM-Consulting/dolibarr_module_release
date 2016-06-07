<?php

class TRelease extends TObjetStd {
	
	function __construct() {
		
		$this->set_table(MAIN_DB_PREFIX . 'release');
		$this->add_champs('fk_propal',array('type'=>'integer', 'index'=>true));
		//TODO par rapport à la définition de besoin, ne manque-t-il pas un champs ?
		
		
		$this->_init_vars('label');
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
	
}


class TReleaseLineLink extends TObjetStd {
	
	function __construct() {
		
		$this->set_table(MAIN_DB_PREFIX . 'release_line_link');
		$this->add_champs('fk_release',array('type'=>'integer', 'index'=>true));
		$this->add_champs('fk_propal_line',array('type'=>'integer'));
		$this->add_champs('qty',array('type'=>'float')); // en prévision de définir le nombre dans chaque rattachement à une release
		
		$this->_init_vars();
		$this->start();
		
		//TODO définir le champs quantité à 1 par défaut
		
	}
		
}