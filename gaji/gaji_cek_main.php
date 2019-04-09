<?php


function gaji_cek_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	 
		

		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('gaji_cek_form');
		return drupal_render($output_form);// . $output;
		 
	
} 

    
function gaji_cek_form($form, &$form_state){
	
	$referer = $_SERVER['HTTP_REFERER'];

	$gajiid = arg(2);
	$dok = arg(3);
	if ($dok=='') $dok = 'D';
	
	/*
	$results = db_query('select gajiid, tahun, bulan, jenis, keterangan from {gaji} where gajiid=:gajiid', array(':gajiid'=>$gajiid));
	foreach ($results as $data) {
		$tahun = $data->tahun;
		$bulan = $data->bulan;
		$jenis = $data->jenis;
		$keterangan = $data->keterangan;
	}
	*/
	
	if ($dok == 'D')
		drupal_set_title('Cek File Daftar Ledger Gaji (' . $gajiid . ')' );
	else
		drupal_set_title('Cek File Rekap Ledger Gaji (' . $gajiid . ')' );

	$form['referer']= array(
		'#type'         => 'value', 
		'#value'=> $referer, 
	); 
		
	$n = 0;	

	$results = db_query('select uk.kodeuk,uk.namasingkat,g.ada from {unitkerja} uk inner join {gajifile} g on uk.kodeuk=g.kodeuk where g.ada=1 and g.gajiid=:gajiid order by uk.kodedinas', array(':gajiid' => $gajiid)); 
	foreach ($results as $data) {
		$n++; 
		
		$caption = apbd_icon_belum();
		
		
		if ($gajiid!='new') {				
			if ($dok=='D') {
				if (apbd_is_file_exist_daftar($gajiid, $data->kodeuk)) {
					$caption = apbd_link('files/ledger_' . $gajiid . '/' . $data->kodeuk . 'D.pdf', apbd_icon_sudah());
				}
			} else {		
			
				if (apbd_is_file_exist_rekap($gajiid, $data->kodeuk)) {
					$caption = apbd_link('files/ledger_' . $gajiid . '/' . $data->kodeuk . 'R.pdf', apbd_icon_sudah());
				}
			}
		}
		
		
		$form['kodeuk' . $n] = array(
			'#type' => 'value',
			'#value' => $data->kodeuk,
		);	

		$form['group' . $n] = array(
			'#type' => 'item',
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',				
			 
		);			
		$form['group' . $n]['markupdaftar' . $n] = array(
			'#type' => 'item',
			'#markup' => $caption,
			'#prefix' => '<div class="col-md-1">',
			'#suffix' => '</div>',				
			 
		);				
		$form['group' . $n]['group' . $n]['pilih' . $n] = array(
			'#type' => 'item',
			'#markup' =>  '(' . $data->kodeuk . ') ' . $data->namasingkat,
			'#prefix' => '<div class="col-md-11">',
			'#suffix' => '</div>',				
			
		);				
		
	}	

	$form['submit'] = array (
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> OK',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#prefix' => '<div class="col-md-12">',

	);
    return $form;
}


function gaji_cek_form_submit($form, &$form_state) {
    
	$referer = $form_state['values']['referer'];

	drupal_goto($referer);
}
?>