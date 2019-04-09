<?php


function gaji_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	 
		

		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('gaji_edit_form');
		return drupal_render($output_form);// . $output;
		 
	
} 

    
function gaji_edit_form($form, &$form_state){
	
	$referer = $_SERVER['HTTP_REFERER'];

	$gajiid = arg(2);
	$tahun = date('Y');
	$bulan = date('n');
	$jenis = 'reguler';
	$keterangan = '';	
	
	//drupal_set_message($gajiid);
	
	if ($gajiid!='new') {
	
		$results = db_query('select gajiid, tahun, bulan, jenis, keterangan from {gaji} where gajiid=:gajiid', array(':gajiid'=>$gajiid));
		foreach ($results as $data) {
			$tahun = $data->tahun;
			$bulan = $data->bulan;
			$jenis = $data->jenis;
			$keterangan = $data->keterangan;
		}
	}
	
	drupal_set_title('Gaji (' . $gajiid . ')' );

	$form['gajiid']= array(
		'#type'         => 'value', 
		'#title'        => 'gajiid',
		'#value'=> $gajiid, 
	); 
	
	//TAHUN
	$opt_tahun = array('2018','2019','2020');
	$opt_tahun['2018'] = '2018';
	$opt_tahun['2019'] = '2019';	
	$opt_tahun['2020'] = '2020';	
	$opt_tahun['2021'] = '2021';	
	
	$form['tahun'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opt_tahun,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$tahun,
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',		
	);
	
	//BULAN
	$option_bulan =array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',		
		
	);	

	//JENIS
	$opt_jenis['reguler'] = 'REGULER';
	$opt_jenis['kekurangan'] = 'KEKURANGAN';	
	$opt_jenis['susulan'] = 'SUSULAN';	
	$opt_jenis['terusan'] = 'TERUSAN';	
	$form['jenis'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis'),
		'#options' => $opt_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenis,
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',				
	);	 
	$form['keterangan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$keterangan,
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',				
	);		
		
	$n = 0;	
	$form['formskpd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'OPD',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',				
	);	
		if ($gajiid=='new') {
			$results = db_query("select kodeuk,namasingkat, 1 as ada from {unitkerja} where kodeuk<>'00' and kodeuk<>'YY' order by kodedinas");
			
			
			
			
		} else {
			$results = db_query('select uk.kodeuk,uk.namasingkat,g.ada from {unitkerja} uk inner join {gajifile} g on uk.kodeuk=g.kodeuk where g.gajiid=:gajiid order by uk.kodedinas', array(':gajiid' => $gajiid)); 
		}
		foreach ($results as $data) {
			$n++;
			
			$form['formskpd']['kodeuk' . $n] = array(
				'#type' => 'value',
				'#value' => $data->kodeuk,
			);					
			$form['formskpd']['pilih' . $n] = array(
				'#type' => 'checkbox',
				'#title' =>  '(' . $data->kodeuk . ') ' . $data->namasingkat,
				'#default_value' => $data->ada,
				'#prefix' => '<div class="col-md-4">',
				'#suffix' => '</div>',				
				
			);				
			
		}	

	$form['numopd'] = array(
		'#type' => 'value',
		'#value' => $n,
	);				

	$form['submitgroup'] = array(
		'#type' => 'item',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',				
		
	);	
	
	$form['submitgroup']['submit'] = array (
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		//'#prefix' => '<div class="col-md-12">',

	);

	$form['submitgroup']['submitrekap']= array(
		'#type' => 'submit',
		'#value' => 'Cek Rekap',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	$form['submitgroup']['submitdaftar']= array(
		'#type' => 'submit',
		'#value' => 'Cek Daftar',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);	
	
    return $form;
}


function gaji_edit_form_submit($form, &$form_state) {
    
	$gajiid = $form_state['values']['gajiid'];
	$tahun = $form_state['values']['tahun'];
	$bulan = $form_state['values']['bulan']+1;
	$jenis = $form_state['values']['jenis'];
	$keterangan = $form_state['values']['keterangan'];
	$numopd = $form_state['values']['numopd'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['submitrekap']) {
		drupal_goto('gaji/CEK/' . $gajiid . '/R');
	} if($form_state['clicked_button']['#value'] == $form_state['values']['submitdaftar']) {
		drupal_goto('gaji/CEK/' . $gajiid . '/D');
	} else {

		if ($gajiid=='new') {
			$gajiid = db_insert('gaji') // Table name no longer needs {}
					->fields(array(
						'tahun' => $tahun,
						'bulan' => $bulan,
						'jenis' => $jenis,
						'keterangan' => $keterangan,
			))
			->execute();
			
			//drupal_set_message($gajiid);
			
			
			for($n=1; $n<=$numopd; $n++){

				$ada = $form_state['values']['pilih' . $n];
				$kodeuk = $form_state['values']['kodeuk' . $n];
				
				$ret = db_insert('gajifile') // Table name no longer needs {}
						->fields(array(
							'gajiid' => $gajiid,
							'kodeuk' => $kodeuk,
							'ada' => $ada,
				))
				->execute();
			}
			
			//skpd
			//$results = db_query('select gajiid, tahun, bulan, jenis, keterangan from {gaji} where gajiid=:gajiid', array(':gajiid'=>$gajiid));
			
			//$new_folder = 'public://ledger_' . $gajiid . '/' ;
			//file_prepare_directory($new_folder, FILE_CREATE_DIRECTORY);		
			
			mkdir('files/ledger_' . $gajiid, 0777, true);	
			
		} else {
			$query = db_update('gaji')
			->fields( 
					array(
						'tahun' => $tahun,
						'bulan' => $bulan,
						'jenis' => $jenis,
						'keterangan' => $keterangan,

					)
				);
			$query->condition('gajiid', $gajiid, '=');
			$ret = $query->execute();

			for($n=1; $n<=$numopd; $n++){

				$ada = $form_state['values']['pilih' . $n];
				$kodeuk = $form_state['values']['kodeuk' . $n];
				
				$query = db_update('gajifile') // Table name no longer needs {}
						->fields(array(
							'ada' => $ada,
				));
				$query->condition('gajiid', $gajiid, '=');			
				$query->condition('kodeuk', $kodeuk, '=');	
				$ret = $query->execute();
			}		
		}
		drupal_goto('');
	}
}
?>