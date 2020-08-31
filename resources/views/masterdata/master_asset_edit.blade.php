<?php 
  //echo "<pre>".PHP_OS; die();
  //echo "7<pre>"; print_r($data['content']); die();
  $qrcode = url('master-asset/show-data/'.base64_encode($data['id']).'');
  $code_ams = base64_encode($data['id']);

  //#### GENERATE PNG IMAGE
  $string = @$data['content']->KODE_ASSET_AMS; 
  $string2 = 'MILIK : '.@$data['content']->BA_PEMILIK_ASSET.' ('.@$data['content']->BA_PEMILIK_ASSET_DESCRIPTION.')';
  $string3 = 'LOKASI : '.@$data['content']->LOKASI_BA_CODE.' ('.@$data['content']->LOKASI_BA_DESCRIPTION.')';
  $string4 = @$data['content']->KODE_ASSET_CONTROLLER;

  $width  = 350;
  $height = 450;
  $font = 2;
  $im = @imagecreate ($width, $height);
  $text_color = imagecolorallocate($im, 0, 0, 0); //black text
  // white background
  // $background_color = imagecolorallocate ($im, 255, 255, 255);
  // transparent background
  $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
  imagefill($im, 0, 0, $transparent);
  imagesavealpha($im, true);
  
  $width1 = imagefontwidth($font) * strlen($string); 
  imagestring ($im, $font, ($width/2)-($width1/2), 350, $string, $text_color);

  $width2 = imagefontwidth($font) * strlen($string2); 
  imagestring ($im, $font, ($width/2)-($width2/2), 370, $string2, $text_color);

  $width3 = imagefontwidth($font) * strlen($string3); 
  imagestring ($im, $font, ($width/2)-($width3/2), 390, $string3, $text_color);

  $width4 = imagefontwidth($font) * strlen($string4); 
  imagestring ($im, $font, ($width/2)-($width4/2), 410, $string4, $text_color);
  
  ob_start();
  imagepng($im);
  $imstr = base64_encode(ob_get_clean());
  imagedestroy($im);

  // Save Image in folder from string base64
  $img = 'data:image/png;base64,'.$imstr;
  $image_parts = explode(";base64,", $img);
  $image_type_aux = explode("image/", $image_parts[0]);
  $image_type = $image_type_aux[1];
  $image_base64 = base64_decode($image_parts[1]);
  $folderPath = app_path();
  $file = $folderPath . '/qrcode_temp.png';
  // MOve to folder
  file_put_contents($file, $image_base64);
  //#### END GENERATE PNG IMAGE


  //CHECK OPERATING SYSTEM
  $os = PHP_OS; 
  if( $os != "WINNT" )
  {
      $file_qrcode = '/app/qrcode_temp.png';
  }
  else
  {
      $file_qrcode = '\app\qrcode_temp.png';
  }

?>

@extends('adminlte::page')
@section('title', 'Data - Master Asset')
@section('content')

<style>
.show_qrcode{cursor:pointer;}
@media screen {
  #printSection {
      display: none;
  }
}

@media print 
{
  body * {
    visibility:hidden;
  }
  #printSection, #printSection * {
    visibility:visible;
  }
  #printSection 
  {
    position: absolute;
    left: 50%;
    transform: translate(-50%, 0);
  }
}

.disposal {
  padding:5px;
  font-weight:bold;
  animation: blink-animation 1s steps(5, start) infinite;
  -webkit-animation: blink-animation 1s steps(5, start) infinite;
}
@keyframes blink-animation {
  to {
    visibility: hidden;
  }
}
@-webkit-keyframes blink-animation {
  to {
    visibility: hidden;
  }
}
</style>

<div class="row">
<section class="content-header" style="margin-top:-3%">
	<h1>
		Master Asset
		<small>Preview</small>
	</h1>
	<ol class="breadcrumb">
    <?php
          if( $data['content']->DISPOSAL_FLAG != '' ){ 
            if (strpos($data['content']->DISPOSAL_FLAG, 'MTSA') !== false) {
              ?>
              <li><span class="disposal callout callout-danger">SUDAH DIMUTASI : {{ $data['content']->DISPOSAL_FLAG }}</span></li>
            <?php
          }else{
        ?>
          <li><span class="disposal callout callout-danger">SUDAH DIDISPOSAL : {{ $data['content']->DISPOSAL_FLAG }}</span></li>
        <?php } 
        }?>
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Master Data</a></li>
		<li class="active">Master Asset</li>
	</ol>
</section>

<section class="content">

<form class="form-horizontal">

<div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title"><span class="direct-chat-text" style="margin-left:0%">KODE ASSET AMS : <b>{{ $data['id'] }}</b></span></h3>
      
      <?php if (strpos($data['content']->DISPOSAL_FLAG, 'DSPA') === true) { ?>
      <span class="xpull-right badge bg-green show_qrcode" OnClick="show_qrcode('{{$data['id']}}','{{@$data['content']->BA_PEMILIK_ASSET}}','{{@$data['content']->LOKASI_BA_CODE}}','{{@$data['content']->KODE_ASSET_CONTROLLER}}','{{@$data['content']->KODE_ASSET_AMS}}','<?php echo @$data['content']->BA_PEMILIK_ASSET_DESCRIPTION; ?>','<?php echo @$data['content']->LOKASI_BA_DESCRIPTION; ?>')"><i class="fa fa-fw fa-barcode"></i> SHOW QR CODE</span>
      <?php  } ?>
      <div class="box-tools pull-right">
        
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <!--button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button-->
      </div>
    </div><!-- /.box-header -->
    
    <div class="box-body">

      <div class="row">

        <div class="col-md-6">

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Document Code</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_reg" placeholder="Document Code" value="{{@$data['content']->NO_REG}}" readonly="1">
              </div>
            </div>

            <!--div class="form-group">
              <label for="" class="col-sm-3 control-label">No Reg Item</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="no_reg_item" placeholder="No Reg Item" value="{{@$data['content']->NO_REG_ITEM}}" readonly="1">
              </div>
            </div-->

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Kode Material</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kode_material" placeholder="Kode Material" value="{{@$data['content']->KODE_MATERIAL}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Lokasi BA Code</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="lokasi_ba_code" placeholder="Lokasi BA Code" value="{{@$data['content']->LOKASI_BA_CODE}}" readonly="1">
              </div>
            </div>

        </div>
        <!-- /.col -->
        
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">No PO</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_po" placeholder="No PO" value="{{@$data['content']->NO_PO}}" readonly="1">
              </div>
            </div>
        	
        	<!--div class="form-group">
              <label for="" class="col-sm-3 control-label">Item PO</label>

              <div class="col-sm-9">
                <input type="text" class="form-control" id="item_po" placeholder="Item PO" value="{{@$data['content']->ITEM_PO}}" readonly="1">
              </div>
            </div-->

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Material</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_material" placeholder="Nama Material" value="{{@$data['content']->NAMA_MATERIAL}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Lokasi BA Description</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="lokasi_ba_description" placeholder="Lokasi BA Description" value="{{@$data['content']->LOKASI_BA_DESCRIPTION}}" readonly="1">
              </div>
            </div>
        
        </div>
        <!-- /.col -->



      </div>
      <!-- /.row -->
    </div><!-- /.box-body -->
</div><!-- /.box default -->

<div class="box box-danger">
	<div class="box-header with-border">
      <h3 class="box-title"><span class="direct-chat-text" style="margin-left:0%"><b>RINCIAN INFORMASI ASSET</b></span></h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <!--button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button-->
      </div>
    </div><!-- /.box-header -->

    <div class="box-body">

      <div class="row">
        <div class="col-md-6">

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Jenis Asset</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="jenis_asset" placeholder="Jenis Asset" value="{{@$data['content']->JENIS_ASSET}} - {{@$data['content']->JENIS_ASSET_DESCRIPTION}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Group</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="group" placeholder="Group" value="{{@$data['content']->GROUP}} - {{@$data['content']->GROUP_DESCRIPTION}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Asset Class</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="asset_class" placeholder="Asset Class" value="{{@$data['content']->ASSET_CLASS}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Merk</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="merk" placeholder="Merk" value="{{@$data['content']->MERK}}" readonly="1">
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Mesin / IMEI</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_mesin_or_imei" placeholder="No Mesin / Imei" value="{{@$data['content']->NO_MESIN_OR_IMEI}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Polisi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_polisi" placeholder="No Polisi" value="{{@$data['content']->NO_POLISI}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Kondisi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kondisi_asset" placeholder="Kondisi Asset" value="{{@$data['content']->KONDISI_ASSET}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Penanggung Jawab</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_penanggung_jawab_asset" placeholder="Nama Penanggung Jawab Asset" value="{{@$data['content']->NAMA_PENANGGUNG_JAWAB_ASSET}}" readonly="1">
              </div>
            </div>

        </div>
        <!-- /.col -->
        
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">BA Pemilik Asset</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="ba_pemilik_asset" placeholder="BA Pemilik Asset" value="{{@$data['content']->BA_PEMILIK_ASSET}}" readonly="1">
              </div>
            </div>
        	
        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">Sub Group</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="sub_group" placeholder="Sub Group" value="{{@$data['content']->SUB_GROUP}} - {{@$data['content']->SUBGROUP_DESCRIPTION}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Asset</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_asset" placeholder="Nama Asset" value="{{@$data['content']->NAMA_ASSET}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Spesifikasi / Warna</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="spesifikasi_or_warna" placeholder="Spesifikasi / Warna" value="{{@$data['content']->SPESIFIKASI_OR_WARNA}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Rangka / No Seri</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_rangka_or_no_seri" placeholder="No Rangka / No Seri" value="{{@$data['content']->NO_RANGKA_OR_NO_SERI}}" readonly="1">
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Tahun Asset</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="tahun_asset" placeholder="Tahun Asset" value="{{@$data['content']->TAHUN_ASSET}}" readonly="1">
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Informasi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="informasi" placeholder="Informasi" value="{{@$data['content']->INFORMASI}}" readonly="1">
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Jabatan</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="jabatan_penanggung_jawab_asset" placeholder="Jabatan Penanggung Jawab Asset" value="{{@$data['content']->JABATAN_PENANGGUNG_JAWAB_ASSET}}" readonly="1">
              </div>
            </div>
        
        </div>
        <!-- /.col -->



      </div>
      <!-- /.row -->
    </div><!-- /.box-body -->
</div><!-- /.box danger -->

<div class="box box-primary">

    <div class="box-header with-border">
      <h3 class="box-title"><span class="direct-chat-text" style="margin-left:0%"><b>DETAIL ASSET SAP</b></span></h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <!--button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button-->
      </div>
    </div><!-- /.box-header -->
    
    <div class="box-body">

      <div class="row">
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">Kode Asset SAP</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kode_asset_sap" name="kode_asset_sap" placeholder="Kode Asset SAP" value="{{@$data['content']->KODE_ASSET_SAP}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Asset Controller</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="asset_controller" placeholder="Asset Controller" value="{{@$data['content']->ASSET_CONTROLLER}}" readonly="1">
              </div>
            </div>

            <!--div class="form-group">
              <label for="" class="col-sm-3 control-label">No Reg Item</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="no_reg_item" placeholder="No Reg Item" value="{{@$data['content']->NO_REG_ITEM}}" readonly="1">
              </div>
            </div-->

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Asset 1</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_asset_1" name="nama_asset_1" placeholder="Nama Asset 1" value="{{@$data['content']->NAMA_ASSET_1}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Asset 3</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_asset_3" name="nama_asset_3" placeholder="Nama Asset 3" value="{{@$data['content']->NAMA_ASSET_3}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Capitalized On</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="capitalized_on" name="capitalized_on" placeholder="Capitalized On" value="{{@$data['content']->CAPITALIZED_ON}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Deactivation On</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="deactivation_on" name="deactivation_on" placeholder="Deactivation On" value="{{@$data['content']->DEACTIVATION_ON}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Kode Asset SubNo SAP</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kode_asset_subno_sap" name="kode_asset_subno_sap" placeholder="Kode Asset SubNo SAP" value="{{@$data['content']->KODE_ASSET_SUBNO_SAP}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">GI Number</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="gi_number" name="gi_number" placeholder="GI Number" value="{{@$data['content']->GI_NUMBER}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">GI Year</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="gi_year" name="gi_year" placeholder="GI Year" value="{{@$data['content']->GI_YEAR}}" readonly="1">
              </div>
            </div>

        </div>
        <!-- /.col -->
        
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">Kode Asset Controller</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" id="kode_asset_controller" placeholder="Kode Asset Controller" value="{{@$data['content']->KODE_ASSET_CONTROLLER}}" readonly="1">
              </div>
            </div>
        	
        	<!--div class="form-group">
              <label for="" class="col-sm-3 control-label">Item PO</label>

              <div class="col-sm-9">
                <input type="text" class="form-control" id="item_po" placeholder="Item PO" value="{{@$data['content']->ITEM_PO}}" readonly="1">
              </div>
            </div-->

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Asset 2</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_asset_2" placeholder="Nama Asset 2" value="{{@$data['content']->NAMA_ASSET_2}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Quantity</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="quantity_asset_sap" placeholder="Quantity Asset SAP" value="{{@$data['content']->QUANTITY_ASSET_SAP}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">UOM</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="uom_asset_sap" placeholder="UOM Asset SAP" value="{{@$data['content']->UOM_ASSET_SAP}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Cost Center</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="cost_center" placeholder="Cost Center" value="{{@$data['content']->COST_CENTER}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Book Deprec 01</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="book_deprec_01" placeholder="Book Deprec 01" value="{{@$data['content']->BOOK_DEPREC_01}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Fiscal Deprec 15</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="fiscal_deprec_15" placeholder="Fiscal Deprec 15" value="{{@$data['content']->FISCAL_DEPREC_15}}" readonly="1">
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Group Deprec 30</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="group_deprec_01" placeholder="Group Deprec 30" value="{{@$data['content']->GROUP_DEPREC_30}}" readonly="1">
              </div>
            </div>
        
        </div>
        <!-- /.col -->



      </div>
      <!-- /.row -->
    </div><!-- /.box-body -->
</div><!-- /.box default -->

<div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title"><span class="direct-chat-text" style="margin-left:0%"><b>RINCIAN FILE ASSET 3</b></span></h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <!--button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button-->
      </div>
    </div><!-- /.box-header -->
    
    <div class="box-body">

    	<div class="row">
    	<?php 

    		//echo "3<pre>"; print_r($data['file']); die();
    		$l = "";
        $m = "";
        $file_category_asset = '';
        $file_category_noseri = '';
        $file_category_imei = '';

    		if(!empty($data['file']))
    		{
    			header("Content-type: image/jpeg");
 
    			foreach( $data['file'] as $k => $v )
    			{ 
    				
            if( $v->FILE_CATEGORY == 'asset' )
    				{
                $file_category_asset .= 'asset';
      					$l .= "<div class='col-xs-4' align='center'>";
      					$l .= "<span class='username'><b>".$v->JENIS_FOTO."</b></span>";
      					$l .= "<img src='".$v->FILE_UPLOAD."' class='img img-responsive'/>";
      					$l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
      					$l .= "</div>";
    				}

            
    				if( $v->FILE_CATEGORY == 'no seri' )
    				{
                $file_category_noseri .= 'no seri';
      					$l .= "<div class='col-xs-4' align='center'>";
      					$l .= "<span class='username'><b>".$v->JENIS_FOTO."</b></span>";
      					$l .= "<img src='".$v->FILE_UPLOAD."' class='img img-responsive'/>";
      					$l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
      					$l .= "</div>";
    				}
  
            
    				if( $v->FILE_CATEGORY == 'imei' )
    				{
                  $file_category_imei .= 'imei';
        					$l .= "<div class='col-xs-4' align='center'>";
        					$l .= "<span class='username'><b>".$v->JENIS_FOTO."</b></span>";
        					$l .= "<img src='".$v->FILE_UPLOAD."' class='img img-responsive'/>";
        					$l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
        					$l .= "</div>";
    				}
    					
    			}

          if( $file_category_asset == '' )
          {
              $m .= "<div class='col-xs-4' align='center'>";
              $m .= "<span class='username'>Foto asset</span>";
              $m .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
              //$m .= "<span class='username'><b>".$v->FILENAME."</b></span>";
              $m .= "</div>";
          }

          if( $file_category_noseri == '' )
          {
               $l .= "<div class='col-xs-4' align='center'>";
               $l .= "<span class='username'>Foto no. seri / no rangka</span>";
               $l .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
               //$l .= "<span class='username'></span>";
               $l .= "</div>";
          }

          if( $file_category_imei == '' )
          {
              $l .= "<div class='col-xs-4' align='center'>";
              $l .= "<span class='username'>Foto No msin / IMEI</span>";
              $l .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
              //$l .= "<span class='username'></span>";
              $l .= "</div>";    
          }
    		}
        else
        {
            if( $file_category_asset == '' )
            {
                $m .= "<div class='col-xs-4' align='center'>";
                $m .= "<span class='username'>Foto asset</span>";
                $m .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
                //$m .= "<span class='username'><b>".$v->FILENAME."</b></span>";
                $m .= "</div>";
            }

            if( $file_category_noseri == '' )
            {
                 $l .= "<div class='col-xs-4' align='center'>";
                 $l .= "<span class='username'>Foto no. seri / no rangka</span>";
                 $l .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
                 //$l .= "<span class='username'></span>";
                 $l .= "</div>";
            }

            if( $file_category_imei == '' )
            {
                $l .= "<div class='col-xs-4' align='center'>";
                $l .= "<span class='username'>Foto No msin / IMEI</span>";
                $l .= "<img src='".url('img/default-img.png')."' class='img img-responsive'/>";
                //$l .= "<span class='username'></span>";
                $l .= "</div>";    
            }
        }
    		echo $l;
        echo $m; 
    	?>
    	</div>

    	<?php /*
		<div class="row">
		<div class="col-md-6">
		</div>
		<!-- /.col -->

		<div class="col-md-6">
		</div>
		<!-- /.col -->
		</div><!-- /.row -->
		*/ ?>
    
    </div><!-- /.box-body -->
</div><!-- /.box default -->

</form>

</section>

</div>

<div id="qrcode-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="xbox-body">
                    <!--div class="generate-qrcode text-center"></div-->
                    <div class="xvisible-print text-center">      

                        <!--img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(250)->generate('$qrcode')) !!} "-->
                        <div id="print-qr-code">
                            <?php 
                              echo QrCode::margin(0)->size(250)->generate(''.$qrcode.''); 
                              //$pathfile = app_path('40100136.png');
                              //echo QrCode::format('png')->merge('\app\qrcode.jpg', .3)->generate(''.$qrcode.'');
                            ?>
                            <div class="qrcode-modal-info text-center" style="margin-top:-2%;font-weight:bold"></div>
                        </div>

                        <a href="data:image/png;base64, <?php echo base64_encode(QrCode::format('png')->merge(''.$file_qrcode.'', 1)->margin(15)->size(450)->generate(''.$qrcode.'')); ?>" target="_blank" download="{!! $data['id'].'.png' !!}"><button type="button" class="btn bg-navy btn-flat margin"><i class="fa fa-download"></i> DOWNLOAD </button></a>

                        <a href="<?php echo url('master-asset/print-qrcode').'/'.$code_ams; ?>" target="_blank">
                        <button type="button" id="btnPrint" class="btn bg-navy btn-flat margin"><i class="fa fa-print"></i> PRINT</button></a>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

@stop
@section('js')
<script type="text/javascript">

$(document).ready(function() 
{
    //alert("modal");
    $('#qrcode-modal').on('show.bs.modal', function () {
           $(this).find('.modal-body').css({
                  width:'auto', //probably not needed
                  height:'auto', //probably not needed 
                  'max-height':'100%'
           });
    });

    /*
    document.getElementById("btnPrint").onclick = function() 
    {
        printElement(document.getElementById("print-qr-code"));
        window.print();
    }
    */
});

function printElement(elem, append, delimiter) 
{
    var domClone = elem.cloneNode(true);

    var $printSection = document.getElementById("printSection");

    if (!$printSection) {
        var $printSection = document.createElement("div");
        $printSection.id = "printSection";
        document.body.appendChild($printSection);
    }

    if (append !== true) {
        $printSection.innerHTML = "";
    }

    else if (append === true) {
        if (typeof(delimiter) === "string") {
            $printSection.innerHTML += delimiter;
        }
        else if (typeof(delimiter) === "object") {
            $printSection.appendChlid(delimiter);
        }
    }

    $printSection.appendChild(domClone);
}

function show_qrcode(amscode,milik,lokasi_code,kode_asset_controller,kode_asset_ams, milik_desc, lokasi_desc)
{
    //alert(test);

    var ams = btoa(amscode);

    $.ajax({
        type: 'GET',
        url: "{{ url('/master-asset/show_qrcode') }}/"+ams,
        data: "",
        //async: false,
        dataType: 'json',
        success: function(data) 
        {
            var item = "<span='bg-green'>"+kode_asset_ams+"</span><br/>";
                item += "MILIK : "+milik+" ("+milik_desc+") <br/>";
                item += "LOKASI : "+lokasi_code+" ("+lokasi_desc+") <br/>";
                item += kode_asset_controller;

            $("#qrcode-modal .generate-qrcode").html("<span='bg-green'>"+data.filename+"</span>");
            $("#qrcode-modal .qrcode-modal-info").html(item);
            $("#qrcode-modal .modal-title").html("<i class='fa fa-edit'></i>  QR Code AMS - <span style='color:#dd4b39'>"+amscode+"</span>");
            $('#qrcode-modal').modal('show');
        },
        error: function(x) 
        {                           
            alert("Error: "+ "\r\n\r\n" + x.responseText);
        }
    }); 

    
}

</script>
@stop