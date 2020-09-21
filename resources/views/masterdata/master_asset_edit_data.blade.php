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

<form class="form-horizontal request-form" id="request-form">

<div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title"><span class="direct-chat-text" style="margin-left:0%">KODE ASSET AMS : <b>{{ $data['id'] }}</b></span></h3>
      
        <?php if (strpos($data['content']->DISPOSAL_FLAG, 'DSPA') !== false) { ?>
        <?php  } else{?>
          <span class="xpull-right badge bg-green show_qrcode" OnClick="show_qrcode('{{$data['id']}}','{{@$data['content']->BA_PEMILIK_ASSET}}','{{@$data['content']->LOKASI_BA_CODE}}','{{@$data['content']->KODE_ASSET_CONTROLLER}}','{{@$data['content']->KODE_ASSET_AMS}}','<?php echo @$data['content']->BA_PEMILIK_ASSET_DESCRIPTION; ?>','<?php echo @$data['content']->LOKASI_BA_DESCRIPTION; ?>')"><i class="fa fa-fw fa-barcode"></i> SHOW QR CODE</span>
          
        <?php }?>
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
                <input type="text" class="form-control" name="no_reg" id="no_reg" placeholder="Document Code" value="{{@$data['content']->NO_REG}}"  @if($data['editable']['NO_REG'] != 1) readonly="1" @endif>
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
                <input type="hidden" class="form-control" name="kode_asset_ams" id="kode_asset_ams" value="{{$data['content']->KODE_ASSET_AMS}}">
                <input type="hidden" class="form-control" name="no_reg_item" id="no_reg_item" value="{{$data['content']->NO_REG_ITEM}}">
                <input type="text" class="form-control" name="kode_material" id="kode_material" placeholder="Kode Material" value="{{@$data['content']->KODE_MATERIAL}}" @if($data['editable']['KODE_MATERIAL'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Lokasi BA Code</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="lokasi_ba_code" id="lokasi_ba_code" placeholder="Lokasi BA Code" value="{{@$data['content']->LOKASI_BA_CODE}}" @if($data['editable']['LOKASI_BA_CODE'] != 1) readonly="1" @endif>
              </div>
            </div>

        </div>
        <!-- /.col -->
        
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">No PO</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" name="no_po" id="no_po" placeholder="No PO" value="{{@$data['content']->NO_PO}}" @if($data['editable']['NO_PO'] != 1) readonly="1" @endif>
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
                <input type="text" class="form-control" name="nama_material" id="nama_material" placeholder="Nama Material" value="{{@$data['content']->NAMA_MATERIAL}}" @if($data['editable']['NAMA_MATERIAL'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Lokasi BA Description</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="lokasi_ba_description" id="lokasi_ba_description" placeholder="Lokasi BA Description" value="{{@$data['content']->LOKASI_BA_DESCRIPTION}}" readonly="1">
              </div>
            </div>
        
        </div>
        <!-- /.col -->



      </div>
      <!-- /.row -->
     <?php
          //  if($data['editable']['NO_REG'] != 1 || $data['editable']['NO_PO'] != 1 || $data['editable']['KODE_MATERIAL'] != 1 || $data['editable']['LOKASI_BA_CODE'] != 1 || $data['editable']['NAMA_MATERIAL'] != 1){
          //   $disable = "";
          // }
          // else{
          //   $disable = "disabled";
          // };
      ?>
    <button type="button" id="btnsave" onClick="save()"  class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit</button>

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
                <input type="text" class="form-control" name="jenis_asset" id="jenis_asset" placeholder="Jenis Asset" value="{{@$data['content']->JENIS_ASSET}}" @if($data['editable']['JENIS_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Group</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="group" id="group" placeholder="Group" value="{{@$data['content']->GROUP}}" @if($data['editable']['GROUP'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Asset Class</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="asset_class" id="asset_class" placeholder="Asset Class" value="{{@$data['content']->ASSET_CLASS}}" @if($data['editable']['ASSET_CLASS'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Merk</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="merk" id="merk" placeholder="Merk" value="{{@$data['content']->MERK}}" @if($data['editable']['MERK'] != 1) readonly="1" @endif>
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Mesin / IMEI</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="no_mesin_or_imei" id="no_mesin_or_imei" placeholder="No Mesin / Imei" value="{{@$data['content']->NO_MESIN_OR_IMEI}}" @if($data['editable']['NO_MESIN_OR_IMEI'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Polisi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="no_polisi" id="no_polisi" placeholder="No Polisi" value="{{@$data['content']->NO_POLISI}}" @if($data['editable']['NO_POLISI'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Kondisi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="kondisi_asset" id="kondisi_asset" placeholder="Kondisi Asset" value="{{@$data['content']->KONDISI_ASSET}}" @if($data['editable']['KONDISI_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Penanggung Jawab</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="nama_penanggung_jawab_asset" id="nama_penanggung_jawab_asset" placeholder="Nama Penanggung Jawab Asset" value="{{@$data['content']->NAMA_PENANGGUNG_JAWAB_ASSET}}" @if($data['editable']['NAMA_PENANGGUNG_JAWAB_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>

        </div>
        <!-- /.col -->
        
        <div class="col-md-6">

        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">BA Pemilik Asset</label>

              <div class="col-sm-8">
                <input type="text" class="form-control" name="ba_pemilik_asset" id="ba_pemilik_asset" placeholder="BA Pemilik Asset" value="{{@$data['content']->BA_PEMILIK_ASSET}}" @if($data['editable']['BA_PEMILIK_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>
        	
        	<div class="form-group">
              <label for="" class="col-sm-4 control-label">Sub Group</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="sub_group" id="sub_group" placeholder="Sub Group" value="{{@$data['content']->SUB_GROUP}}" @if($data['editable']['SUB_GROUP'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Nama Asset</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="nama_asset" id="nama_asset" placeholder="Nama Asset" value="{{@$data['content']->NAMA_ASSET}}" @if($data['editable']['NAMA_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">Spesifikasi / Warna</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="spesifikasi_or_warna" id="spesifikasi_or_warna" placeholder="Spesifikasi / Warna" value="{{@$data['content']->SPESIFIKASI_OR_WARNA}}" @if($data['editable']['SPESIFIKASI_OR_WARNA'] != 1) readonly="1" @endif>
              </div>
            </div>

            <div class="form-group">
              <label for="" class="col-sm-4 control-label">No Rangka / No Seri</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="no_rangka_or_no_seri" id="no_rangka_or_no_seri" placeholder="No Rangka / No Seri" value="{{@$data['content']->NO_RANGKA_OR_NO_SERI}}" @if($data['editable']['NO_RANGKA_OR_NO_SERI'] != 1) readonly="1" @endif>
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Tahun Asset</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="tahun_asset" id="tahun_asset" placeholder="Tahun Asset" value="{{@$data['content']->TAHUN_ASSET}}" @if($data['editable']['TAHUN_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Informasi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="informasi" id="informasi" placeholder="Informasi" value="{{@$data['content']->INFORMASI}}" @if($data['editable']['INFORMASI'] != 1) readonly="1" @endif>
              </div>
            </div>

             <div class="form-group">
              <label for="" class="col-sm-4 control-label">Jabatan</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="jabatan_penanggung_jawab_asset"  id="jabatan_penanggung_jawab_asset" placeholder="Jabatan Penanggung Jawab Asset" value="{{@$data['content']->JABATAN_PENANGGUNG_JAWAB_ASSET}}" @if($data['editable']['JABATAN_PENANGGUNG_JAWAB_ASSET'] != 1) readonly="1" @endif>
              </div>
            </div>
        
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

    <button type="button" id="btnsave" onClick="save()"  class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit</button>


    </div><!-- /.box-body -->

    
</div><!-- /.box danger -->

<!-- <form id="image-form" enctype="multipart/form-data"> -->
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
                $l .= "<input type='file' accept='image/*' id='imgupload_asset' name='foto_asset' style='visibility:hidden'/> ";
                $l .= "<div class='image-group'>";
                $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-asset-remove hide' OnClick=removeImage('asset')><i class='fa fa-trash'></i></button>";
                $l .= "<img src='".$v->FILE_UPLOAD."' title='Click to change image' id='fotoasset' class='img img-responsive'/>";
                $l .= "</div>";
      					$l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
                $l .= "</div>";

    				}

            
    				if( $v->FILE_CATEGORY == 'no seri' )
    				{
                $file_category_noseri .= 'no seri';
      					$l .= "<div class='col-xs-4' align='center'>";
      					$l .= "<span class='username'><b>".$v->JENIS_FOTO."</b></span>";
                $l .= "<input type='file' accept='image/*' id='imgupload_seri' name='foto_seri' style='visibility:hidden'/> ";
      					$l .= "<div class='image-group'>";
                $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-seri-remove hide' OnClick=removeImage('seri')><i class='fa fa-trash'></i></button>";
                $l .= "<img src='".$v->FILE_UPLOAD."' title='Click to change image' id='fotoseri' class='img img-responsive'/>";
      					$l .= "</div>";
      					$l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
      					$l .= "</div>";
    				}
  
            
    				if( $v->FILE_CATEGORY == 'imei' )
    				{
                  $file_category_imei .= 'imei';
        					$l .= "<div class='col-xs-4' align='center'>";
        					$l .= "<span class='username'><b>".$v->JENIS_FOTO."</b></span>";
        					$l .= "<input type='file' accept='image/*' id='imgupload_imei' name='foto_imei' style='visibility:hidden'/> ";
                  $l .= "<div class='image-group'>";
                  $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-mesin-remove hide' OnClick=removeImage('mesin')><i class='fa fa-trash'></i></button>";
                  $l .= "<img src='".$v->FILE_UPLOAD."' title='Click to change image' id='fotoimei' class='img img-responsive'/>";
                  $l .= "</div";
                  $l .= "<span class='username'><b>".$v->FILENAME."</b></span>";
        					$l .= "</div>";
    				}
    					
    			}

          if( $file_category_asset == '' )
          {
              $m .= "<div class='col-xs-4' align='center'>";
              $m .= "<span class='username'>Foto asset</span>";
              $m .= "<input type='file' accept='image/*' id='imgupload_asset' name='foto_asset' style='visibility:hidden'/> ";
              $m .= "<div class='image-group'>";
              $m .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-asset-remove hide' OnClick=removeImage('asset')><i class='fa fa-trash'></i></button>";
              $m .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoasset' class='img img-responsive'/>";
              $m .= "</div>";
              //$m .= "<span class='username'><b>".$v->FILENAME."</b></span>";
              $m .= "</div>";
          }

          if( $file_category_noseri == '' )
          {
               $l .= "<div class='col-xs-4' align='center'>";
               $l .= "<span class='username'>Foto no. seri / no rangka</span>";
               $l .= "<input type='file' accept='image/*' id='imgupload_seri' name='foto_seri' style='visibility:hidden'/> ";
               $l .= "<div class='image-group'>";
               $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-seri-remove hide' OnClick=removeImage('seri')><i class='fa fa-trash'></i></button>";
               $l .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoseri' class='img img-responsive'/>";
               $l .= "</div>";
               //$l .= "<span class='username'></span>";
               $l .= "</div>";
          }

          if( $file_category_imei == '' )
          {
              $l .= "<div class='col-xs-4' align='center'>";
              $l .= "<span class='username'>Foto No msin / IMEI</span>";
              $l .= "<input type='file' accept='image/*' id='imgupload_imei' name='foto_imei' style='visibility:hidden'/> ";
              $l .= "<div class='image-group'>";
              $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-mesin-remove hide' OnClick=removeImage('mesin')><i class='fa fa-trash'></i></button>";
              $l .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoimei' class='img img-responsive'/>";
              $l .= "</div>";
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
                $m .= "<input type='file' accept='image/*' id='imgupload_asset' name='foto_asset' style='visibility:hidden'/> ";
                $m .= "<div class='image-group'>";
                $m .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-asset-remove hide' OnClick=removeImage('asset')><i class='fa fa-trash'></i></button>";
                $m .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoasset' class='img img-responsive'/>";
                $m .= "</div>"; 
                //$m .= "<span class='username'><b>".$v->FILENAME."</b></span>";
                $m .= "</div>";
            }

            if( $file_category_noseri == '' )
            {
                 $l .= "<div class='col-xs-4' align='center'>";
                 $l .= "<span class='username'>Foto no. seri / no rangka</span>";
                 $l .= "<input type='file' accept='image/*' id='imgupload_seri' name='foto_seri' style='visibility:hidden'/> ";
                 $l .= "<div class='image-group'>";
                 $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-seri-remove hide' OnClick=removeImage('seri ')><i class='fa fa-trash'></i></button>";
                 $l .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoseri' class='img img-responsive'/>";
                 $l .= "</div>";
                 //$l .= "<span class='username'></span>";
                 $l .= "</div>";
            }

            if( $file_category_imei == '' )
            {
                $l .= "<div class='col-xs-4' align='center'>";
                $l .= "<span class='username'>Foto No msin / IMEI</span>";
                $l .= "<input type='file' accept='image/*' id='imgupload_imei' name='foto_imei' style='visibility:hidden'/> ";
                $l .= "<div class='image-group'>";
                $l .= "<button type='button' class='btn btn-danger btn-xs btn-flat btn-foto-mesin-remove hide' OnClick=removeImage('mesin')><i class='fa fa-trash'></i></button>";
                $l .= "<img src='".url('img/default-img.png')."' title='Click to change image' id='fotoimei' class='img img-responsive'/>";
                //$l .= "<span class='username'></span>";
                $l .= "</div>";    
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
      
      <button type="submit" id="btnsaveImage" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit Image</button>

    </div><!-- /.box-body -->
</div><!-- /.box default -->


<br>
<br>
<br>
<br>
<br>
<br>
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

var el = document.getElementById("jenis_asset"); 
var readonly_jenis_asset = el.getAttribute("readonly");
if(readonly_jenis_asset !== '1'){
var jenis_asset = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset") !!}')));
jQuery("#jenis_asset").select2({
    data: jenis_asset,
    width: '100%',
    placeholder: ' ',
    allowClear: true,
});
}

var jenis_asset_code = $('input[name="jenis_asset"]').val();

var el2 = document.getElementById("group"); 
var readonly_group = el2.getAttribute("readonly");
if(readonly_group !== '1'){
  var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type='+jenis_asset_code )));
  jQuery("#group").select2({
      data: assetgroup,
      width: "100%",
      allowClear: true,
      placeholder: ' '
  });
}
var group = $('input[name="asset_group"]').val();

var el3 = document.getElementById("sub_group"); 
var readonly_subgroup = el3.getAttribute("readonly");
if(readonly_subgroup !== '1'){
  var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group='+group+'&jenis_asset_code='+jenis_asset_code )));
  jQuery("#sub_group").select2({
      data: assetsubgroup,
      width: "100%",
      allowClear: true,
      placeholder: ' '
  });
}


var el4 = document.getElementById("lokasi_ba_code"); 
var readonly_ba = el4.getAttribute("readonly");
if(readonly_ba !== '1'){
var plant = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.businessarea") !!}')));
  jQuery("#lokasi_ba_code").select2({
      data: plant,
      width: "100%",
      allowClear: true,
      placeholder: ' '
  });
}

  $("#lokasi_ba_code").change(function() { //this occurs when select 1 changes
    $("#ba_pemilik_asset").val($(this).select2('data')[0].id);
    $("#lokasi_ba_description").val($(this).select2('data')[0].text);
  });


$('#fotoasset').click(function(){ $('#imgupload_asset').trigger('click'); });

function readURLasset(input) {
  if (input.files && input.files[0]) {
    
    if( input.files[0].size > 500000 )
    {
        notify({
                type: 'warning',
                message: " Foto Asset <= 500 KB ! "
            });
        return false;
    }
    
    $(".btn-foto-asset-remove").removeClass('hide');

    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#fotoasset').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#imgupload_asset").change(function() {
  readURLasset(this);
});

$('#fotoseri').click(function(){ $('#imgupload_seri').trigger('click'); });

function readURLseri(input) {
  if (input.files && input.files[0]) {
    
    if( input.files[0].size > 500000 )
    {
        notify({
                type: 'warning',
                message: " Foto No. Seri <= 500 KB ! "
            });
        return false;
    }
    
    $(".btn-foto-seri-remove").removeClass('hide');

    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#fotoseri').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#imgupload_seri").change(function() {
  readURLseri(this);
});


$('#fotoimei').click(function(){ $('#imgupload_imei').trigger('click'); });

function readURLimei(input) {
  if (input.files && input.files[0]) {

    if( input.files[0].size > 500000 )
    {
        notify({
                type: 'warning',
                message: " Foto Mesin Seri <= 500 KB ! "
            });
        return false;
    }
    
    $(".btn-foto-mesin-remove").removeClass('hide');

    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#fotoimei').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#imgupload_imei").change(function() {
  readURLimei(this);
});

function removeImage(code) {

  if (code == 'asset') {
      jQuery("#fotoasset").attr('src', "{{URL::asset('img/default-img.png')}}");
      jQuery(".btn-foto-asset-remove").addClass('hide');
      jQuery("#imgupload_asset").val("");
  } else if (code == 'seri') {
      jQuery("#fotoseri").attr('src', "{{URL::asset('img/default-img.png')}}");
      jQuery(".btn-foto-seri-remove").addClass('hide');
      jQuery("#imgupload_seri").val("");
  } else if (code == 'mesin') {
      jQuery("#fotoimei").attr('src', "{{URL::asset('img/default-img.png')}}");
      jQuery(".btn-foto-mesin-remove").addClass('hide');
      jQuery("#imgupload_imei").val("");
  }
}


function save() 
    {
      
      // jQuery('#btnsave').on('click', function(e) {       
        var param = $("#request-form").serialize();
        // var file_foto_asset = $("#imgupload_asset")[0].files;
        // var file_foto_seri = $("#imgupload_seri")[0].files;
        // var file_foto_imei = $("#imgupload_imei")[0].files;
        // var text_foto_asset = $('#imgupload_asset').val();
        // var text_foto_seri = $('#imgupload_seri').val();
        // var text_foto_imei = $('#imgupload_imei').val();
        // var fd = new FormData();
        // fd.append('file_foto_asset', file_foto_asset);
        // fd.append('file_foto_seri', file_foto_seri);
        // fd.append('file_foto_imei', file_foto_imei);
        // fd.append('text_foto_asset',text_foto_asset);
        // fd.append('text_foto_seri',text_foto_seri);
        // fd.append('text_foto_imei',text_foto_imei);

          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                  'Access-Control-Allow-Methods': 'GET, POST',
              }
          });

        if (validateSave()) 
        {
            if (confirm("Apakah anda yakin akan melakukan submit data ini?")) 
            {
               
                $.ajax({
                    url: "{{ url('asset/update') }}",
                    type: "POST",
                    data: param,
                    // data: param+"&formData="+fd, 
                    // dataType: 'json',
                    // processData: false,
                    // contentType: false,
                    /*  contentType: false,
                    processData: false,
                    cache: false, */
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) 
                        {
                            //SEND EMAIL 
                            // send_email_create_po(result.new_noreg);

                            notify({
                                type: 'success',
                                message: result.message
                            });
                            
                            // setTimeout(reload_page, 2000);

                        } else {
                            notify({
                                type: 'warning',
                                message: result.message
                            });
                        }
                    },
                    complete: function() {
                        jQuery('.loading-event').fadeOut();
                    }
                });
            }
        }
      // });
    }


function validateSave() 
{
    var valid = true;
    var thisyear = <?php echo date('Y'); ?>;

    
    var asset_condition = document.getElementById("kondisi_asset").value;
    var asset_year = document.getElementById("tahun_asset").value;
    var asset_serie_no = document.getElementById("no_rangka_or_no_seri").value;
    var asset_imei = document.getElementById("no_mesin_or_imei").value;
    var asset_pic_name = document.getElementById("nama_penanggung_jawab_asset").value;
    var asset_pic_level = document.getElementById("jabatan_penanggung_jawab_asset").value;
    

    if(readonly_ba !== '1'){
      var asset_location = $('input[name="lokasi_ba_code"]').val();
    }else{
      var asset_location = document.getElementById("lokasi_ba_code").value;
    }

    if(readonly_jenis_asset !== '1'){
      var asset_type = $('input[name="jenis_asset"]').val();
    }else{
      var asset_type = document.getElementById("jenis_asset").value;
    }

    if(readonly_group !== '1'){
      var asset_group = $('input[name="group"]').val();
    }else{
      var asset_group = document.getElementById("group").value;
    }

    if(readonly_subgroup !== '1'){
      var asset_sub_group = $('input[name="sub_group"]').val();
    }else{
      var asset_sub_group = document.getElementById("sub_group").value;
    }


    if (asset_type === "" || asset_type == null  ) 
    {
        notify({
            type: 'warning',
            message: 'Jenis Asset  tidak boleh kosong!'
        });
        valid = false;
        return false;
    }
    
    if (asset_group === "" || asset_group == null  ) 
    {
        notify({
            type: 'warning',
            message: 'Group  tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    if (asset_sub_group === "" || asset_group == null ) 
    {
        notify({
            type: 'warning',
            message: 'Sub Group  tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    //IF JENIS ASSET TYPE = 4030-KENDARAAN & ALAT BERAT
    if( asset_type == 'U4010' || asset_type == 'M4010' || asset_type == 'A4010' || asset_type == 'E4010' || asset_type == 'E4030' || asset_type == 4030 || asset_type == 4010 )
    {
        if (asset_serie_no === "") {
            notify({
                type: 'warning',
                message: 'No Seri / Rangka tidak boleh kosong!'
            });

            valid = false;
            return false
        }

        if( $.trim(asset_serie_no).length < 3 )
        {
            notify({
                type: 'warning',
                message: 'No Seri / Rangka minimal 3 char '
            });
            valid = false;
            return false;
        }
        
        if (asset_imei === "") {
            notify({
                type: 'warning',
                message: 'No Mesin / IMEI tidak boleh kosong!'
            });

            valid = false;
            return false;
        }

        if( $.trim(asset_imei).length < 3 )
        {
            notify({
                type: 'warning',
                message: 'No Mesin / IMEI minimal 3 char '
            });
            valid = false;
            return false;
        }
    }

    if (asset_year === "") 
    {
        notify({
            type: 'warning',
            message: 'Tahun  tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    if( $.trim(asset_year).length != 4 )
    {
        notify({
            type: 'warning',
            message: 'Format Tahun masih salah '
        });
        valid = false;
        return false;
    }

    if( asset_year < 1945 || asset_year > thisyear )
    {
        notify({
            type: 'warning',
            message: 'Tahun masih belum benar / maksimal tahun '+thisyear+' '
        });
        valid = false;
        return false;
    }

    if (asset_location === "") {
        notify({
            type: 'warning',
            message: 'Lokasi tidak boleh kosong!'
        });
    }

    if (asset_condition === "") {
        notify({
            type: 'warning',
            message: 'Kondisi pada  asset tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    if (asset_pic_name === "") {
        notify({
            type: 'warning',
            message: 'Nama Penanggung Jawab Asset tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    if (asset_pic_level === "") {
        notify({
            type: 'warning',
            message: 'Jabatan Penanggung Jawab Asset tidak boleh kosong!'
        });
        valid = false;
        return false;
    }

    return valid;
}



jQuery('#request-form').on('submit', function(e) {

    e.preventDefault();
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var param = new FormData(this);
    jQuery.ajax({
        url: "{{ url('asset/upload_image') }}",
        type: "POST",
        data: param,
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function() {
            jQuery('.loading-event').fadeIn();
        },
        success: function(result) {
            if (result.status) 
            {
                notify({
                    type: 'success',
                    message: result.message
                });
                
                // setTimeout(reload_page, 2000);

            } else {
                notify({
                    type: 'warning',
                    message: result.message
                });
            }
        },
        complete: function() {
            jQuery('.loading-event').fadeOut();
        }
    });
})

</script>
@stop