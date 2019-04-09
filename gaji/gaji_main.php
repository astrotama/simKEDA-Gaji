<?php
function gaji_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;

	$tahun = date('Y');
	$jenis = 'all';

	if ($arg) {
		switch($arg) {
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$tahun = arg(2);
				$jenis = arg(3);
				break;
				

		}
		
	} 
	
	//drupal_set_message('tahun ' . $tahun);
	//drupal_set_message('jenis ' . $jenis);
	
	$output_form = drupal_get_form('gaji_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode','width' => '20px', 'valign'=>'top'),
		array('data' => 'Tahun', 'width' => '50px', 'field'=> 'tahun', 'align'=>'center', 'valign'=>'top'),
		array('data' => 'Bulan', 'field'=> 'bulan', 'width' => '60px', 'field'=> 'tanggal','align'=>'center', 'valign'=>'top'),
		array('data' => 'Jenis', 'field'=> 'jenis', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => '', 'width' => '5px', 'valign'=>'top'),
		array('data' => '', 'width' => '5px', 'valign'=>'top'),
		
	);
		

	$query = db_select('gaji', 'g')->extend('PagerDefault')->extend('TableSort');
	
	# get the desired fields from the database
	$query->fields('g', array('gajiid', 'tahun', 'bulan', 'jenis', 'keterangan'));
	
	$query->condition('g.tahun', $tahun, '=');
	if ($jenis !='all') $query->condition('g.jenis', $jenis, '=');

	
	$query->orderByHeader($header);
	$query->orderBy('g.tahun', 'DESC');
	$query->orderBy('g.bulan', 'DESC');
	$query->limit($limit);
	

	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 
	
	$rows = array();
	foreach ($results as $data) {
		$no++;
		
		$editlink = apbd_button_jurnal('gaji/edit/' . $data->gajiid);
		$deletelink =  apbd_button_hapus('gaji/delete/' . $data->gajiid);

		$rows[] = array(
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . $data->gajiid . '</strong>','align' => 'center', 'valign'=>'top'),
					array('data' => $data->tahun,'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_get_namabulan($data->bulan),'align' => 'left', 'valign'=>'top'),
					array('data' => ucfirst($data->jenis),'align' => 'left', 'valign'=>'top'),
					array('data' => $data->keterangan,'align' => 'left', 'valign'=>'top'),
					$editlink,						
					$deletelink,	
				);
	}
	
	

	
	//$btn = apbd_button_print('/laporanbk2/' . $kodeuk );
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $output;
	
	
}

function gaji_main_form_submit($form, &$form_state) {
	$tahun = $form_state['values']['tahun'];
	$jenis = $form_state['values']['jenis'];
	
	$uri = 'gaji/filter/' . $tahun . '/' .  $jenis ;
	drupal_goto($uri);
	
}


function gaji_main_form($form, &$form_state) {
	
	if(arg(2)!=null){
		
		$tahun = arg(2);
		$jenis = arg(3);
		

	} else {
		$tahun = date('Y');
		$jenis = 'all';
		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right"></small></em>',	
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	//JENIS
	$opt_jenis['all'] = 'SEMUA';
	$opt_jenis['reguler'] = 'REGULER';
	$opt_jenis['kekurangan'] = 'KEKURANGAN';	
	$opt_jenis['susulan'] = 'SUSULAN';	
	$opt_jenis['terusan'] = 'TERUSAN';	
	$form['formdata']['jenis'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis'),
		'#options' => $opt_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenis,
	);	 
	
	//TAHUN
	$opt_tahun['2018'] = '2018';
	$opt_tahun['2019'] = '2019';	
	$opt_tahun['2020'] = '2020';	
	$opt_tahun['2021'] = '2021';
	$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opt_tahun,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$tahun,
	);
	

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

?>
