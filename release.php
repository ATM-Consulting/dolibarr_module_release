<?php

	require 'config.php';
	dol_include_once('/release/class/release.class.php');
	dol_include_once('/comm/propal/class/propal.class.php');
	dol_include_once('/societe/class/societe.class.php');
	
	$id = GETPOST('id');
	$id_release = GETPOST('id_release');
	$lineid = GETPOST('lineid');
	
	$propal = new Propal($db);
	if($propal->fetch($id)<=0) exit('ProblemToFetchPropal');
	
	$action = GETPOST('action');
	
	$PDOdb = new TPDOdb;
	
	$release = new TRelease;
	if($id_release>0) {
		$release->load($PDOdb, $id_release);
	}

	$hookmanager->initHooks(array('releasecard','globalcard'));
	$parameters = array();
	$reshook = $hookmanager->executeHooks('doActions', $parameters, $release, $action); // Note that $action and $object$
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
	
	switch ($action) {
		case 'add':
			
			$release->fk_propal = $id;
			$release->save($PDOdb);
			
			_card($PDOdb, $propal);
			break;
		
		case 'save':
			
			$TRelease = GETPOST('TRelease','array');
			
			if(!empty($TRelease)) {
				
				foreach($TRelease as $id_release=>$dataRelease) {
					
					if($release->load($PDOdb, $id_release)) {
						
						$release->set_values($dataRelease);
						
						if(!empty($dataRelease['TReleaseLineLink'])) {
							
							foreach($dataRelease['TReleaseLineLink'] as $k=>$dataLink) {
								
								$release->TReleaseLineLink[$k]->set_values($dataLink);
								
							}
							
						}
						
						if(!empty($dataRelease['bt_add_line_release']) && !empty($dataRelease['line_to_add'])) {
	
							//TODO completer l'appel pour lier une ligne à une release
							//HELP Regarde dans la classe :-|						
							
						}
						
						if(!empty($dataRelease['bt_facture_release'])) {
							$release->facture();
						}
						
					}
					
				}
				
			}
			
			_card($PDOdb, $propal);
			break;
		
		case 'delete':
			$release->delete($PDOdb);
			
			_card($PDOdb, $propal);
			break;
		
		case 'unlink':
			//TODO completer l'appel pour délier une ligne à une release
			
			
			_card($PDOdb, $propal);
			break;
		
		default:
			_card($PDOdb, $propal);
			
			break;
	}
	
function _card(&$PDOdb, &$propal) {
	global $langs;
	
	llxHeader();
	_entete($propal);
	
	// TODO finaliser l'affichage de la liste des releases
	// HELP, il manque quelques points
	
	$TRelease = TRelease::getAllReleaseForPropal($PDOdb, $propal->id);
	
	$formCore = new TFormCore($_SERVER['PHP_SELF'], 'formRelease','post');
	echo $formCore->hidden('action', 'save');
	echo $formCore->hidden('id', $propal->id);
	
	
	foreach($TRelease as &$release) {
		
		//TODO A internationaliser
		echo '<br /><br /> 
		<table class="border" width="100%"><tr class="liste_titre"><th>Label</th><th>Line</th><th>.</th></tr>';
		
		echo '<tr>
			<td>'.$formCore->texte('','TRelease['.$release->getId().'][label]', $release->label,40,255).'</td>
			<td>'.$formCore->combo('','TRelease['.$release->getId().'][line_to_add]', TRelease::getAllLineCombo($propal), -1).' '.$formCore->btsubmit($langs->trans('AddLineRelease'), 'TRelease['.$release->getId().'][bt_add_line_release]').'</td>
			<td>'.$formCore->btsubmit($langs->trans('FactureThisRelease'), 'TRelease['.$release->getId().'][bt_facture_release]').'</td>
			<td><a href="?id='.$propal->id.'&atcion=delet">'.img_delete().'</a></td>
		</tr>'; //TODO je crois que j'ai fait une erreur sur le lien de suppression
		
		foreach($release->TReleaseLineLink as &$link) {
			
			
			echo '<tr>
				<td> *** </td>
				<td>'.$link->getLineTitle().'</td>
				<td>'.$formCore->texte('','TRelease['.$release->getId().'][TReleaseLineLink]['.$link->getId().'][qty]', $link->qty,3,10).'</td>
			<td><a href="?id='.$propal->id.'&atcion=unlink&lineid='.$link->fk_propal_line.'">'.img_delete().'</a></td>
			</tr>';
		}
		
		//TODO afficher le total du montant de la release
		echo '<tr><td align="right" colspan="2">'.$langs->trans('Total').'</td><td align="right">'.price($total).'</td></tr>';
		echo '</table>';	
	}	
	
	?>
	
	
	<div class="tabsAction">
<?php
// TODO ajouter l'appel d'un hook addMoreActionsButtons
?>
		<div class="inline-block divButAction"><a href="?id=<?php echo $propal->id?>&action=add" class="butAction"><?php echo $langs->trans('Add') ?></a></div>
		<div class="inline-block divButAction"><input type="submit" name="bt_save" class="butAction" value="<?php echo $langs->trans('Save') ?>" /></div>
	</div>
	
	<?php
	
	$formCore->end();
	
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
