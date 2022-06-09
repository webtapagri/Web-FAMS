<script type="text/javascript">
//alert("okesip 1");
window.print();

$(document).ready(function() 
{
	print_document();
});

function print_document()
{
	window.print();
	alert("okesip 2");
}

</script>

<?php 
	//echo "<pre>"; print_r($data); 
	/*
	Array
	(
	    [qrcode] => IRVAN TAZRIAN
	)
	{!! QrCode::size(200)->generate('$data[qrcode]'); !!}
	*/
?>
<center>
<div class="visible-print text-center">
    <?php

    	$qrcode = url('master-asset/show-data/'.$data['code_ams'].'');

    	echo QrCode::margin(0)->size(250)->generate(''.$qrcode.'').'<br/>'; 

    	//echo "2<pre>"; print_r($data['data']);

    	if(!empty($data['data']))
    	{
    		foreach( $data['data'] as $k => $v )
    		{
    			echo '<b>'.$v->KODE_ASSET_AMS.'</b><br/>';
    			echo '<b>MILIK : '.$v->BA_PEMILIK_ASSET.' ('.$v->BA_PEMILIK_ASSET_DESCRIPTION.')</b><br/>';
    			echo '<b>LOKASI : '.$v->LOKASI_BA_CODE.' ('.$v->LOKASI_BA_DESCRIPTION.')</b><br/>';
    			echo '<b>'.$v->KODE_ASSET_CONTROLLER.'</b><br/>';

    			//echo "1<pre>"; print_r($v);

    			/*
    			stdClass Object
(
    [ID] => 246
    [ASSET_PO_ID] => 310
    [NO_REG_ITEM] => 2
    [NO_REG] => 19.07/AMS/PDFA/00159
    [ITEM_PO] => 4
    [KODE_MATERIAL] => 000000000208010191
    [NAMA_MATERIAL] => TRIPLEK 4 MM
    [NO_PO] => 2013010191
    [BA_PEMILIK_ASSET] => 2121
    [JENIS_ASSET] => E4030
    [GROUP] => G20
    [SUB_GROUP] => SG163
    [ASSET_CLASS] => 
    [NAMA_ASSET] => TRIPLEK 4 MM
    [MERK] => 
    [SPESIFIKASI_OR_WARNA] => 
    [NO_RANGKA_OR_NO_SERI] => 12
    [NO_MESIN_OR_IMEI] => 12
    [NO_POLISI] => 
    [LOKASI_BA_CODE] => 2121
    [LOKASI_BA_DESCRIPTION] => 2121-ESTATE BBB
    [TAHUN_ASSET] => 2019
    [KONDISI_ASSET] => BP
    [INFORMASI] => 
    [NAMA_PENANGGUNG_JAWAB_ASSET] => 1b
    [JABATAN_PENANGGUNG_JAWAB_ASSET] => 1b
    [ASSET_CONTROLLER] => IT
    [KODE_ASSET_CONTROLLER] => 12
    [NAMA_ASSET_1] => versa1
    [NAMA_ASSET_2] => versa2
    [NAMA_ASSET_3] => versa3
    [QUANTITY_ASSET_SAP] => 1.00
    [UOM_ASSET_SAP] => UN
    [CAPITALIZED_ON] => 
    [DEACTIVATION_ON] => 
    [COST_CENTER] => 21zd210999
    [BOOK_DEPREC_01] => 4
    [FISCAL_DEPREC_15] => 4
    [GROUP_DEPREC_30] => 4
    [DELETED] => 
    [CREATED_BY] => 22
    [CREATED_AT] => 2019-07-25 09:46:40
    [UPDATED_BY] => 22
    [UPDATED_AT] => 2019-07-25 10:44:16
    [KODE_ASSET_SAP] => 40300309
    [KODE_ASSET_SUBNO_SAP] => 
    [GI_NUMBER] => 12
    [GI_YEAR] => 2019
    [KODE_ASSET_AMS] => 2140300309
)
    			*/
    		}
    		//die();
    	}

    ?>
    
</div>
</center>