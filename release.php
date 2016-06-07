<?php

	require 'config.php';
	dol_include_once('/comm/propal/class/propal.class.php');
	dol_include_once('/societe/class/societe.class.php');
	
	$id = GETPOST('id');
	
	$propal = new Propal($db);
	$propal->fetch($id);
	
	$action = GETPOST('action');
	
	$PDOdb = new TPDOdb;
	
	switch ($action) {
		case 'add':
			
			_card($PDOdb, $propal);
			break;
		
		case 'save':
			
			_card($PDOdb, $propal);
			break;
		
		case 'delete':
			
			_card($PDOdb, $propal);
			break;
		
		case 'link':
			
			_card($PDOdb, $propal);
			break;
			
		case 'unlink':
			
			_card($PDOdb, $propal);
			break;
		
		default:
			_card($PDOdb, $propal);
			
			break;
	}
	
function _card(&$PDOdb, &$propal) {
	
	llxHeader();
	_entete($propal);
	
	// TODO finaliser l'affichage de la liste des releases
	
	
	dol_fiche_end();
	
	llxFooter();
	
}
function _entete(&$object) {
	global $db,$langs,$user,$conf;
	
	$soc = new Societe($db);
	$soc->fetch($object->socid);

	dol_include_once('/core/lib/propal.lib.php'); //TODO placer cette inclusion à un endroit plus propice
	$head = propal_prepare_head($object);
	dol_fiche_head($head, 'release', $langs->trans('Proposal'), 0, 'propal');
	
	//gros copier-coller moisi incoming !
	
	
	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td>' . $langs->trans('Ref') . '</td><td colspan="5">';
	print '<div class="inline-block floatleft refid refidpadding">'. $object->ref .'</div>';
	print '</td></tr>';

	// Ref customer
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td class="nowrap">';
	print $langs->trans('RefCustomer') . '</td>';
	print '</td>';
	print '</tr></table>';
	print '</td><td colspan="5">';
		print $object->ref_client;
	print '</td>';
	print '</tr>';

	// Company
	print '<tr><td>' . $langs->trans('Company') . '</td><td colspan="5">' . $soc->getNomUrl(1) . '</td>';
	print '</tr>';
	print '</table>';
	
	
}
