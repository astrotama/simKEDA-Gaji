<?php

function gaji_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
     
    $gajiid = arg(2);
	//drupal_set_message($gajiid);	
    if (isset($gajiid)) {


		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Konfirmasi Penghapusan Gaji',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);
		

		$results = db_query('select tahun,bulan,keterangan from {gaji} where gajiid=:gajiid', array(':gajiid'=>$gajiid));
		foreach ($results as $data) {
			$tahun = $data->tahun;
			$bulan = $data->bulan;
			$keterangan = $data->keterangan;
		}	
		
		$referer = $_SERVER['HTTP_REFERER'];
		$form['formdata']['referer'] = array('#type' => 'value', '#value' => $referer);
		
		//drupal_set_message($uid);	
		$form['formdata']['gajiid'] = array('#type' => 'value', '#value' => $gajiid);
		$form['formdata']['keterangan'] = array (
					//'#type' => 'markup',
					'#markup' => 'Mengahpus gaji ' . $tahun . '/' . $bulan . ', ' . $keterangan,
					);
		
		//FORM NAVIGATION	

		return confirm_form($form,
							'Anda yakin menghapus data gaji ' . $gajiid ,
							$referer,
							'PERHATIAN : Data yang dihapus tidak bisa dikembalikan lagi.',
							//'<button type="button" class="btn btn-danger">Hapus</button>',
							//'<em class="btn btn-danger">Hapus</em>',
							//'<input class="btn btn-danger" type="button" value="Hapus">',
							'Hapus',
							'Batal');
    }
}
function gaji_delete_form_validate($form, &$form_state) {
}
function gaji_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $gajiid = $form_state['values']['gajiid'];
		$referer = $form_state['values']['referer'];
		
		//delete apbdop
		$num = db_delete('gaji')
		  ->condition('gajiid', $gajiid)
		  ->execute();

		$num = db_delete('gajifile')
		  ->condition('gajiid', $gajiid)
		  ->execute();
		  
        if ($num) {
			
            drupal_goto('gaji');
        }
        
    }
}
?>