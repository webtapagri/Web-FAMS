<?php //echo "yes"; die(); ?>

@extends('adminlte::page')
@section('title', 'FAMS - Request')

@section('content')
<style>
    label {
        font-weight: 500;
    }

    .select-img:hover {
        opacity: 0.5
    }

    .fmdb-input-default {
        background-color: #eee !important;
    }

    .filters input {
        width: 100%;
    }
</style>

<div class="row" >
    <div class="col-md-12 xcol-md-offset-1">
        <div class="box">
            <form class="form-horizontal request-form" id="request-form" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="plant" class="col-md-3">TIPE TRANSAKSI<sup style="color:red">*</sup></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="transaction_type" id="transaction_type" value="BARANG" autocomplete="off" readonly="readonly">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">TANGGAL <sup style="color:red">*</sup></label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm" name="request_date" id="request_date" value="{{ date('d M Y') }}" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">BUSINESS AREA <sup style="color:red">*</sup></label>
                            <div class="col-md-4">
                                <select type="text" class="form-control input-sm" name="business_area" id="business_area" required></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">NO. PURCHASE ORDER <sup style="color:red">*</sup></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="" autocomplete="off" maxlength="10" required>
                            </div>
                            <div class="col-md-3" id="ganti-no-po" style="margin-top:2px;color:red;font-weight:bold">
                                <i class="fa fa-edit"></i> Ganti NO PO
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">TGL PO <sup style="color:red">*</sup></label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm" name="po_date" id="po_date" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">KODE VENDOR <sup style="color:red">*</sup></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="vendor_code" id="vendor_code" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">NAMA VENDOR <sup style="color:red">*</sup></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="vendor_name" id="vendor_name" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group {{ $data['type'] == 'amp' ? '':'hide' }}">
                            <label for="plant" class="col-md-3">BERITA ACARA SERAH TERIMA</label>
                            <div class="col-md-4">
                                <input type="file" class="form-control input-sm" name="docs" multiple id="docs">
                            </div>
                        </div>
                        <div class="form-group select-item-panel hide">
                            <label for="plant" class="col-md-3">
                                <button type="button" class="btn btn-flat btn-danger btn-add-items">SELECT ITEM</button>
                            </label>
                            <div class="col-md-9">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed" id="request-item-table">
                                    <tr>
                                        <th>ITEM PO</th>
                                        <th>KODE</th>
                                        <th>NAME</th>
                                        <th>QTY</th>
                                        <th>QTY DIAJUKAN</th>
                                        <th>QTY OUTSTANDING</th>
                                    </tr>
                                    <tr>
                                        <td colspan="6" align="center"><span class="text-red">Silahkan input Nomor Purchase Order di atas untuk memilih item dan click tombol "select item" </span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer clearfix">
                        <button type="submit" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">SUBMIT</button>
                        <button type="button" class="btn btn-default btn-flat btn-cancel pull-right" style="margin-right: 5px;">CLEAR</button>
                    </div>
                </div>
            </form>
            
            <form class="form-horizontal code-asset-form hide" id="code-asset-form">
                <div class="box-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Tanggal</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm " name="asset_request_date" id="asset_request_date" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Business Area</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="asset_business_area" id="asset_business_area" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">No. PO</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="asset_po_no" id="asset_po_no" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Tgl PO</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="asset_po_date" id="asset_po_date" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Kode vendor</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="asset_vendor_code" id="asset_vendor_code" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Nama vendor</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="asset_vendor_name" id="asset_vendor_name" autocomplete="off" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-md-2"><b>ITEM DETAIL</b></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm text-right" name="detail_item_selected" id="detail_item_selected" readonly style="border:1px solid red;background-color: #f4433630">
                                <span class="help-block has-error">Please select the item to show the detail</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Item PO</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-sm text-right" name="item_po" value="" id="item_po" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Qty Index</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-sm text-right" name="item_qty_index" id="item_qty_index" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Kode material</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="item_code" id="item_code" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Nama material</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="item_name" value="" id="item_name" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <li class="active" style="border-bottom:none !important;"><a href="#panel-initial" data-toggle="tab" class="panel-initial" style="background-color:#f3f3f3;border-bottom:none;font-weight:800">RINCIAN INFORMASI ASSET | PAGE: <span class="total-page"></span></a></li>
                                    <li class="pull-right"><a href="javascript:nextPage()" class="text-muted btn_next">Next <i class="fa fa-arrow-right"></i></a></li>
                                    <li class="pull-right"><a href="javascript:prevPage()" class="text-muted btn_prev"><i class="fa fa-arrow-left"></i> Prev</a></li>
                                </ul>
                                <div class="tab-content" style="border-left: 1px solid #e0dcdc;border-right: 1px solid #e0dcdc;border-bottom: 1px solid #e0dcdc;border-top:none;background-color:#f3f3f3;">
                                    <!-- Font Awesome Icons -->
                                    <div class="tab-pane active" id="panel-initial">
                                        <div class="box-body">
                                            <div class="form-group hide">
                                                <label for="plant" class="col-md-2 text-right">Company</label>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control input-sm" name="description" value="" id="description" autocomplete="off" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group hide">
                                                <label for="plant" class="col-md-2 text-right">Asset</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <input type="email" class="form-control input-sm" placeholder="" readonly>
                                                        <span class="input-group-addon btn btn-sm btn-danger"><i class="fa fa-files-o"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 col-md-offset-1">Jenis asset <sup style="color:red">*</sup></label>
                                                
                                                <div class="col-md-8">
                                                    <select class="form-control input-sm" name="asset_type" value="" id="asset_type"></select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 col-md-offset-1">Group <sup style="color:red">*</sup></label>
                                                
                                                <div class="col-md-8">
                                                    <select class="form-control input-sm" name="asset_group" value="" id="asset_group"></select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 col-md-offset-1">Sub Group <sup style="color:red">*</sup></label>
                                                
                                                <div class="col-md-8">
                                                    <select class="form-control input-sm" name="asset_sub_group" value="" id="asset_sub_group"></select>
                                                </div>
                                            </div>

                                            <div class="form-group material-group-input" id="input-description">

                                                <label for="part_no" class="col-md-2 col-md-offset-1">Nama Aset</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_name" id="asset_name" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-part-no">
                                                <label for="part_no" class="col-md-2 col-md-offset-1">Merk</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_brand" id="asset_brand" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Spesifikasi / Warna</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_specification" id="asset_specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Seri / Rangka <sup style="color:red">*</sup></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_serie_no" id="asset_serie_no" placeholder="Khusus untuk kendaraan & alat berat">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Mesin / IMEI <sup style="color:red">*</sup></label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_imei" id="asset_imei" placeholder="Khusus untuk kendaraan & alat berat">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Polisi </label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_police_no" id="asset_police_no" placeholder="Khusus untuk kendaraan">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Lokasi Asset <sup style="color:red">*</sup></label>
                                                <div class="col-md-8">
                                                    <!--  <input type="text" class="form-control input-sm attr-material-group" name="asset_location" id="asset_location"> -->
                                                    <select type="text" class="form-control input-sm attr-material-group" name="asset_location" id="asset_location"></select>
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Tahun Asset<sup style="color:red">*</sup></label>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control input-sm attr-material-group" name="asset_year" id="asset_year" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Kondisi Asset <sup style="color:red">*</sup></label>
                                                <div class="col-md-8" style="margin-left:15px">
                                                    <div class="form-group">
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="condition1" value="B">
                                                                Baik
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="condition2" value="BP">
                                                                Butuh Perbaikan
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="condition3" value="TB">
                                                                Tidak baik
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Informasi</label>
                                                <div class="col-md-8">
                                                    <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="asset_info" id="asset_info"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 col-md-offset-1 ">Foto Asset</label>
                                                <div class="col-md-9">
                                                    <div id="filesContainer">
                                                        <div class="col-md-4" id="panel-image-1">
                                                            <div class="form-group hide">
                                                                <input type="file" id="foto_asset_1" name="foto_asset_1" accept='image/*' OnChange="showImage('asset', 1)">
                                                                <p class="help-block">*jpg, png</p>
                                                            </div>
                                                            <div class="image-group">
                                                                <button type="button" class="btn btn-danger btn-xs btn-flat btn-foto-asset-remove hide" OnClick="removeImage('asset',1)"><i class="fa fa-trash"></i></button>
                                                                <img id="foto_asset_thumb_1" data-status="0" style="cursor:pointer" title="click to change image" OnClick="openFile('asset',1)" class="img-responsive select-img" src="{{URL::asset('img/add-img.png')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 col-md-offset-1 ">Foto No Seri / No Rangka</label>
                                                <div class="col-md-9">
                                                    <div id="filesContainer">
                                                        <div class="col-md-4" id="panel-image-1">
                                                            <div class="form-group hide">
                                                                <input type="file" id="foto_no_seri_1" name="foto_no_seri_1" accept='image/*' OnChange="showImage('seri',1)">
                                                                <p class="help-block">*jpg, png</p>
                                                            </div>
                                                            <div class="image-group">
                                                                <button type="button" class="btn btn-danger btn-xs btn-flat btn-foto-seri-remove hide" OnClick="removeImage('seri',1)"><i class="fa fa-trash"></i></button>
                                                                <img id="foto_no_seri_thumb_1" data-status="0" style="cursor:pointer" title="click to change image" OnClick="openFile('seri',1)" class="img-responsive select-img" src="{{URL::asset('img/add-img.png')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 col-md-offset-1 ">Foto No Mesin / IMEI</label>
                                                <div class="col-md-9">
                                                    <div id="filesContainer">
                                                        <div class="col-md-4" id="panel-image-1">
                                                            <div class="form-group hide">
                                                                <input type="file" id="foto_mesin_1" name="foto_mesin_1" accept='image/*' OnChange="showImage('mesin', 1)">
                                                                <p class="help-block">*jpg, png</p>
                                                            </div>
                                                            <div class="image-group">
                                                                <button type="button" class="btn btn-danger btn-xs btn-flat btn-foto-mesin-remove hide" OnClick="removeImage('mesin', 1)"><i class="fa fa-trash"></i></button>
                                                                <img id="foto_mesin_thumb_1" data-status="0" style="cursor:pointer" title="click to change image" OnClick="openFile('mesin', 1)" class="img-responsive select-img" src="{{URL::asset('img/add-img.png')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h4>PENANGGUNG JAWAB ASSET : </h4>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Nama<sup style="color:red">*</sup></label>
                                                
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_pic_name" id="asset_pic_name" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Jabatan<sup style="color:red">*</sup></label>

                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_pic_level" id="asset_pic_level" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="active" style="border-bottom:none !important;"><a href="#panel-initial" data-toggle="tab" class="panel-initial" style="background-color:#f3f3f3;border-bottom:none;font-weight:800">RINCIAN INFORMASI ASSET | PAGE : <span class="total-page"></span></a></li>
                                    <li class="pull-right"><a href="javascript:nextPage()" class="text-muted btn_next">Next <i class="fa fa-arrow-right"></i></a></li>
                                    <li class="pull-right"><a href="javascript:prevPage()" class="text-muted btn_prev"><i class="fa fa-arrow-left"></i> Prev</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer clearfix">
                        @if($data['access']->create == 1)
                        <button type="submit" class="btn btn-danger btn-flat pull-right hide" OnClick="save(0)" style="margin-right: 5px;">Draft</button>
                        <button type="submit" class="btn btn-danger btn-flat pull-right" onClick="save(1)" style="margin-right: 5px;">Submit</button>
                        @endif
                        <button type="button" class="btn btn-default btn-flat btn-back-request-form pull-right hide" style="margin-right: 5px;"><i class="fa fa-arrow-circle-left"></i> Back</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>

<div id="item-detail-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-condensed" width="100%" id="table-detail-item">
                    <thead>
                        <tr>
                            <th>ITEM PO</th>
                            <th>KODE</th>
                            <th>NAME</th>
                            <th>QTY</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-flat label-danger" OnClick="addItem()" style="margin-right: 5px;">Select</button>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script>
    var imgFiles = [];
    var addFile = 2;
    var request_item = {};
    var item_count = 1;
    var request_item_page = [];
    var data_page = new Array(3);
    var current_page = 1;
    var records_per_page = 1;
    var data_detail = {};
    var request_docs = [];
    var selected_detail_item = [];
    var transaction_type = jQuery("#transaction_type");
    var request_date = jQuery("#request_date");
    var business_area = jQuery("#business_area");
    var po_no = jQuery("#po_no");
    var po_date = jQuery("#po_date");
    var vendor_code = jQuery("#vendor_code");
    var vendor_name = jQuery("#vendor_name");


    $(document).ready(function() 
    {
        $("#ganti-no-po").hide();
        $('#ganti-no-po').click(function() 
        {
            request_item = [];
            request_item_page = [];
            createItemRequestTable();
            $("#po_date").val("");
            $("#vendor_code").val("");
            $("#vendor_name").val("");
            $("#po_no").attr("readonly",false);
        });

        $('input[type="text"]').change(function(){
            this.value = $.trim(this.value);
        });

        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('#request-detail-page').addClass('sub-loader');
        $(".btn-cancel").on('click', function() 
        {
            if (confirm("Apakah anda yakin akan menghapus data ini?")) 
            {
                request_item = [];
                request_item_page = [];
                createItemRequestTable();

                $('#transaction_type').val('');
                $('#transaction_type').trigger('change');

                $('#business_area').val('');
                $('#business_area').trigger('change');
                
                notify({
                    type: 'error',
                    message: 'form has been cleared!'
                });
                
                document.getElementById("request-form").reset();

                $("#po_no").attr("readonly",false);
                $("#ganti-no-po").hide();
            }
        });

        var plant = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.businessarea") !!}')));
        jQuery("#business_area").select2({
            data: plant,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        var plant_all = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.generaldataplant") !!}')));
        jQuery("#asset_location").select2({
            data: plant,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        jQuery("#asset_group").select2();
        jQuery("#asset_sub_group").select2();

        var jenisasset = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.jenisasset") !!}')));
        jQuery("#asset_type").select2({
            data: jenisasset,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        }).on('change', function() 
        {
            //alert("change 2");
            var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type=' + jQuery(this).val())));
            jQuery("#asset_group").empty().select2({
                data: assetgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
            //jQuery("#asset_group").trigger('change');
        });

        $("#asset_group").on('change', function() 
        {
            var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group='+$(this).val()+'&jenis_asset_code='+$("#asset_type").val() )));
            $("#asset_sub_group").empty().select2({
                data: assetsubgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
            //$("#asset_sub_group").trigger('change');

            //var asset_type_val = $("#asset_type").val();
            //asset_group_val = $("#asset_group").val();
            //asset_sub_group_val = $("#asset_sub_group").val();
            //get_asset_controller(asset_type_val,asset_group_val,asset_sub_group_val);

        });

        //$("#asset_type").trigger('change');
        //$("#asset_group").trigger('change');
        //$("#asset_sub_group").trigger('change');

        $("#request-form").on("submit", function(e) 
        {
            e.preventDefault();

            //alert(request_item); 
            //alert(JSON.stringify(request_item)); //return false;
            //alert("submit gaes"); return false;

            if(request_item.length == 0)
            {
                notify({
                    type: 'warning',
                    message: 'please, add an item'
                });
            }   

            if(validateQty(request_item))
            {
                if (requestItemData() > 0) 
                {
                    jQuery('.request-form').addClass('hide');
                    jQuery('.code-asset-form').removeClass('hide');

                    jQuery("#asset_request_date").val(request_date.val());
                    jQuery("#asset_business_area").val(business_area.val());
                    jQuery("#asset_po_no").val(po_no.val());
                    jQuery("#asset_po_date").val(po_date.val());
                    jQuery("#asset_vendor_code").val(vendor_code.val());
                    jQuery("#asset_vendor_name").val(vendor_name.val());

                    topFunction();
                    var items = [];
                    //alert(items);

                    $.each(request_item, function(key, val) 
                    {
                        
                        if (val) 
                        {
                            if (val.id != undefined) 
                            {
                                items.push({
                                    id: val.id,
                                    text: val.code + ' - ' + val.name
                                })
                            }
                        }
                    });


                    jQuery("#detail_item_selected").select2({
                        data: items,
                        width: "100%",
                        allowClear: true,
                        placeholder: ' '
                    }).on("change", function() {
                        getProp(jQuery(this).val());
                    });

                    $("#detail_item_selected").val(items[0].id);
                    $("#detail_item_selected").trigger("change");

                } 
                else 
                {
                    notify({
                        type: 'warning',
                        message: 'please, add an item'
                    });
                }
            }
                
        });

        jQuery("#code-asset-form").on("submit", function(e) {
            e.preventDefault();
        });

        jQuery(".btn-back-request-form").on("click", function(e) {
            jQuery('.code-asset-form').addClass('hide');
            jQuery('.request-form').removeClass('hide');
            topFunction();
        })

        jQuery('#form-basic-data').on('submit', function(e) {
            e.preventDefault();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /* var form = jQuery('#form-initial').find('input, select, textarea').appendTo('#form-basic-data'); */
            /* var param = new FormData(this); */
            var init_form = jQuery(this).serializeArray();
            var param = {
                "asset": request_form,
                "docs": request_docs
            };
            param.push(init_form);

            jQuery.ajax({
                url: "{{ url('materialrequest/post') }}",
                type: "POST",
                data: param,
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) {
                    if (result.status) {
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        window.location.href = "{{ url('/') }}";
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
        });

        jQuery("#docs").on("change", function() {
            for (var i = 0; i < $(this).get(0).files.length; ++i) {

                request_docs[i] = {
                    'name': '',
                    'size': '',
                    'type': '',
                    'file': ''
                };
            }
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                var file = $(this).get(0).files[i];

                request_docs[i].name = file.name;
                request_docs[i].size = file.size;
                request_docs[i].type = file.type;
                getBase64(i, file);
            }
        });

        jQuery("#po_no").on("keyup", function(e) {
            if (e.keyCode == 13) {
                showPO();
            }
        });

        jQuery('.btn-add-items').on("click", function() {
            jQuery("#item-detail-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#item-detail-modal .modal-title").text('Detail item from PO ' + jQuery("#po_no").val());
            jQuery("#item-detail-modal").modal('show');
        });

        jQuery("#asset_name").on('keyup', function() 
        {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_name = jQuery(this).val();
        });

        jQuery("#asset_brand").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_brand = jQuery(this).val();
        });

        jQuery("#asset_imei").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_imei = jQuery(this).val();
        });

        jQuery("#asset_police_no").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_police_no = jQuery(this).val();
        });

        jQuery("#asset_serie_no").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_serie_no = jQuery(this).val();
        });

        jQuery("#asset_specification").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_specification = jQuery(this).val();
        });

        jQuery("#asset_year").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_year = jQuery(this).val();
        });

        jQuery("#asset_info").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_info = jQuery(this).val();
        });

        jQuery("#asset_pic_name").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_pic_name = jQuery(this).val();
        });

        jQuery("#asset_pic_level").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_pic_level = jQuery(this).val();
        });

        jQuery("#asset_location").on('change', function() {
            if (jQuery(this).val()) {
                var id = current_page - 1;
                var obj = jQuery('#detail_item_selected').val();

                var data = jQuery(this).select2('data');
                request_item[obj].detail[id].asset_location = data[0].id;
                request_item[obj].detail[id].asset_location_desc = data[0].text;
            }
        });

        jQuery("#asset_type").on('change', function() 
        {
            //alert("change");
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_type = jQuery(this).val();
        });

        jQuery("#asset_group").on('change', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_group = jQuery(this).val();
        });

        $("#asset_sub_group").on('change', function() 
        {
            //var asset_type_val = $("#asset_type").val();
            //asset_group_val = $("#asset_group").val();
            //asset_sub_group_val = $("#asset_sub_group").val();
            //get_asset_controller(asset_type_val,asset_group_val,asset_sub_group_val);

            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_sub_group = jQuery(this).val();
        });



        jQuery("input[name='asset_condition']").on('change', function() {
            if (jQuery(this).val()) {
                var id = current_page - 1;
                var obj = jQuery('#detail_item_selected').val();
                request_item[obj].detail[id].asset_condition = jQuery(this).val();
            }
        });
        $('#asset_year').keypress(function(event){
            console.log(event.which);
        if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
            event.preventDefault();
        }});

        $('#asset_serie_no').keypress(function(e){
            var regex = new RegExp("^[a-zA-Z12345678910-]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else
            {
                e.preventDefault();
                //alert('Please Enter Alphabate');
                return false;
            }
        });

        $('#asset_imei').keypress(function(e){
            var regex = new RegExp("^[a-zA-Z12345678910-]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else
            {
                e.preventDefault();
                //alert('Please Enter Alphabate');
                return false;
            }
        });
    });

    function save(status) 
    {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (validateSave()) 
        {
            if (confirm("Apakah anda yakin akan melakukan submit data ini?")) 
            {
                var param = {
                    transaction_type: transaction_type.val(),
                    request_date: request_date.val(),
                    business_area: business_area.val(),
                    po_no: po_no.val(),
                    po_type: 0,
                    po_date: po_date.val(),
                    vendor_code: vendor_code.val(),
                    vendor_name: vendor_name.val(),
                    docs: request_docs,
                    asset: request_item,
                    status: status
                };

                jQuery.ajax({
                    url: "{{ url('request/post') }}",
                    type: "POST",
                    data: param,
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
                            send_email_create_po(result.new_noreg);

                            notify({
                                type: 'success',
                                message: result.message
                            });
                            
                            setTimeout(reload_page, 2000);

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
    }

    function reload_page(){window.location.href = "{{ url('/') }}";}

    function validateQty(request_item)
    {
        var val = "";
        var valid = true;

        jQuery.each(request_item, function(i, val) 
        {
            if(val)
            {
                if (val.request_qty === 0) 
                {
                    notify({
                        type: 'warning',
                        message: 'QTY DIAJUKAN Tidak boleh kosong'
                    });

                    valid = false;
                    return false;
                }
            }               
        });
        return valid;
    }

    function validateSave() 
    {
        var valid = true;
        var thisyear = <?php echo date('Y'); ?>

        // $.each(JSON.parse(request_item), function(key, data) 
        $.each(request_item, function(i, field) 
        {
            console.log(field);
            if (field) 
            {
                $.each(field.detail, function(key, val) 
                {
                    //alert(val.asset_year); valid = false;

                    if (val.asset_type === "" || val.asset_type == null  ) 
                    {
                        notify({
                            type: 'warning',
                            message: 'Jenis Asset pada asset  ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }
                    
                    if (val.asset_group === "" || val.asset_group == null  ) 
                    {
                        notify({
                            type: 'warning',
                            message: 'Group pada asset  ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    if (val.asset_sub_group === "" || val.asset_group == null ) 
                    {
                        notify({
                            type: 'warning',
                            message: 'Sub Group pada asset  ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    //IF JENIS ASSET TYPE = 4030-KENDARAAN & ALAT BERAT
                    if( val.asset_type == 'U4010' || val.asset_type == 'M4010' || val.asset_type == 'A4010' || val.asset_type == 'E4010' || val.asset_type == 'E4030' || val.asset_type == 4030 || val.asset_type == 4010 )
                    {
                        if (val.asset_serie_no === "") {
                            notify({
                                type: 'warning',
                                message: 'No Seri / Rangka pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                            });

                            valid = false;
                            return false
                        }

                        if( $.trim(val.asset_serie_no).length < 3 )
                        {
                            notify({
                                type: 'warning',
                                message: 'No Seri / Rangka minimal 3 char pada asset ' + field.name + ' page ' + (key + 1) + ' '
                            });
                            valid = false;
                            return false;
                        }
                        
                        if (val.asset_imei === "") {
                            notify({
                                type: 'warning',
                                message: 'No Mesin / IMEI pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                            });

                            valid = false;
                            return false;
                        }

                        if( $.trim(val.asset_imei).length < 3 )
                        {
                            notify({
                                type: 'warning',
                                message: 'No Mesin / IMEI minimal 3 char pada asset ' + field.name + ' page ' + (key + 1) + ' '
                            });
                            valid = false;
                            return false;
                        }
                    }

                    if (val.asset_year === "") 
                    {
                        notify({
                            type: 'warning',
                            message: 'Tahun pada asset  ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    if( $.trim(val.asset_year).length != 4 )
                    {
                        notify({
                            type: 'warning',
                            message: 'Format Tahun masih salah pada asset ' + field.name + ' page ' + (key + 1) + ' '
                        });
                        valid = false;
                        return false;
                    }

                    if( val.asset_year < 1945 || val.asset_year > thisyear )
                    {
                        notify({
                            type: 'warning',
                            message: 'Tahun masih belum benar / maksimal tahun '+thisyear+' pada asset ' + field.name + ' page ' + (key + 1) + ' '
                        });
                        valid = false;
                        return false;
                    }

                    if (val.asset_location === "") {
                        notify({
                            type: 'warning',
                            message: 'Lokasi pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                    }

                    if (val.asset_condition === "") {
                        notify({
                            type: 'warning',
                            message: 'Kondisi pada  asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    if (val.asset_pic_name === "") {
                        notify({
                            type: 'warning',
                            message: 'Nama Penanggung Jawab Asset pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    if (val.asset_pic_level === "") {
                        notify({
                            type: 'warning',
                            message: 'Jabatan Penanggung Jawab Asset pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                });
            }
        });

        return valid;
    }

    function showPO() 
    {
        $('.loading-event').fadeIn();

        var no_po = $("#po_no").val();
        var data = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.no_po") !!}?no_po=' + no_po)));
        var ba_user = new Array(<?php echo $data['ba_user']; ?>);
        var count = 0;

        if (data.AEDAT) 
        {
            $("#po_no").attr("readonly", true);
            $("#po_date").val(data.AEDAT);
            $("#vendor_code").val(data.LIFNR);
            $("#vendor_name").val(data.NAME1);
            $('.select-item-panel').removeClass("hide");
            $("#ganti-no-po").fadeIn();    

            var item = '<table class="table table-bordered table-condensed table-hover" id="table-detail-item">';
            
            item += '<tr>';
            item += '<th width="45px">SELECT</th>';
            item += '<th>ITEM PO</th>';
            item += '<th>KODE</th>';
            item += '<th>NAME</th>';
            item += '<th>HARGA (RP)</th>';
            item += '<th class="text-right">QTY</th>';
            item += '</tr>';
            
            selected_detail_item = [];

            $.each(data.DETAIL_ITEM, function(key, val) 
            {
                if( ba_user == 'All' )
                {
                    var HARGA = val.NETPR;
                    selected_detail_item.push(val);
                    item += "<tr>";
                    item += "<td><input type='checkbox' onClick='selectPOItem(this)' value='" + key + "' ></td>";
                    item += "<td>" + val.EBELP + "</td>";
                    item += "<td>" + val.MATNR + "</td>";
                    item += "<td>" + val.MAKTX + "</td>";
                    item += "<td>" + convertToRupiah(HARGA.replace(".00", "")); + "</td>";
                    item += "<td class='text-right'>" + val.MENGE + "</td>";
                    item += "</tr>";
                    count++;
                }
                else
                {
                    if( $.inArray(val.WERKS, ba_user) !== -1 )
                    {
                        var HARGA = val.NETPR;
                        selected_detail_item.push(val);
                        item += "<tr>";
                        item += "<td><input type='checkbox' onClick='selectPOItem(this)' value='" + key + "' ></td>";
                        item += "<td>" + val.EBELP + "</td>";
                        item += "<td>" + val.MATNR + "</td>";
                        item += "<td>" + val.MAKTX + "</td>";
                        item += "<td>" + convertToRupiah(HARGA.replace(".00", "")); + "</td>";
                        item += "<td class='text-right'>" + val.MENGE + "</td>";
                        item += "</tr>";
                        count++;
                    }
                }
                

            });
            
            item += "</table>";

            // IT@220719 : IF BA_USER = ALL PT 
            if( ba_user == 'All' )
            {
                count = 1;
            }
            
            if(count>0)
            {
                $("#table-detail-item").html(item);
            }
            else
            {
                $("#po_date").val("");
                $("#vendor_code").val("");
                $("#vendor_name").val("");
                $('.select-item-panel').addClass("hide");
                notify({
                    type: 'warning',
                    message: "PO number is not your Business Area!"
                }); 
            }
                 
        } 
        else 
        {
            $("#po_date").val("");
            $("#vendor_code").val("");
            $("#vendor_name").val("");
            $('.select-item-panel').addClass("hide");
            notify({
                type: 'warning',
                //message: "PO number is not found!"
                message: "PO number belum di GR / belum ada di SAP"
            });
        }
        
        $('.loading-event').fadeOut();
    }

    function convertToRupiah(angka)
    {
        //alert(angka); return false;
        var rupiah = '';        
        var angkarev = angka.toString().split('').reverse().join('');
        for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
        return rupiah.split('',rupiah.length-1).reverse().join('');
    }

    function selectPOItem(param) {
        if (jQuery(param).prop('checked') == true) {
            jQuery(param).closest('tr').css('background-color', 'rgba(221, 75, 57, 0.38)');
        } else {
            jQuery(param).closest('tr').css('background-color', '');
        }
    }

    function addItem() 
    {
        //alert("add item");
        request_item = [];
        
        $('#table-detail-item').find('input[type="checkbox"]:checked').each(function() 
        {
            var index = $(this).val();
            var item = selected_detail_item[index];
            var id = makeInt(5);
            
            request_item[index] = 
            {
                id: index,
                item_po: item.EBELP,
                code: item.MATNR,
                name: item.MAKTX,
                qty: get_qty_po(item.EBELP,item.MENGE,item.MATNR),
                //request_qty: 1,
                //outstanding_qty: (item.MENGE - 1),
                request_qty: 0,
                outstanding_qty: get_qty_po(item.EBELP,item.MENGE,item.MATNR),
                detail: []
            };

            createPage(index);
        });

        createItemRequestTable();
        $("#item-detail-modal").modal('hide');
    }

    function createPage(id) 
    {
        request_item_page = [];
        data_page = [];
        var item_detail = [];
        var item = request_item[id];
        
        for (var i = 0; i < item.request_qty; i++) 
        {
            item_detail.push({
                asset_type: '',
                asset_group: '',
                asset_sub_group: '',
                //asset_controller: '',
                //asset_name: item.name + (item.request_qty > 1 ? ' - ' + (i + 1) : ''),
                asset_name: item.name,
                asset_brand: '',
                asset_imei: '',
                asset_police_no: '',
                asset_serie_no: '',
                asset_specification: '',
                asset_location: '',
                asset_location_desc: '',
                asset_condition: '',
                asset_year: '',
                asset_pic_name: '',
                asset_pic_level: '',
                asset_foto: '',
                asset_info: '',
                foto_asset: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                },
                foto_asset_seri: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                },
                foto_asset_mesin: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                }
            });
        }

        request_item[id].detail = item_detail;
    }

    function makeid(length) 
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    function makeInt(length) {
        var text = "";
        var possible = "0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    function remove(obj) 
    {
        var conf = confirm("Are you sure you want to delete this data?");
        
        if (conf == true) 
        {
            request_item[obj] = [];
            data_detail[obj] = [];

            //delete request_item[obj];
            //delete data_detail[obj];

            createItemRequestTable();
        }
    }

    function createItemRequestTable() 
    {
        var item = '<table class="table table-bordered table-condensed" id="request-item-table">';
        item += '<tr>';
        item += '<th>ITEM PO</th>';
        item += '<th>KODE</th>';
        item += ' <th>NAME</th>';
        item += '<th>QTY</th>';
        item += '<th width="115px">QTY DIAJUKAN</th></th>';
        item += '<th>QTY OUTSTANDING</th>';
        item += '<th style="width: 40px;display:none"></th>';
        item += '</tr>';

        if (requestItemData() > 0) 
        {
            $.each(request_item, function(key, val) 
            {
                //alert(key+"~~~~~~~"+val.id);
                //if (val.id != undefined)
                if(val) 
                {
                    if (val.id != undefined)
                    {   
                        item += "<tr>";
                        item += "<td>" + val.item_po + "</td>";
                        item += "<td>" + val.code + "</td>";
                        item += "<td>" + val.name + "</td>";
                        item += "<td style='text-align:right'>" + val.qty + "</td>";
                        item += '<td class="text-center">';
                        item += '<div class="input-group">';
                        item += ' <div style="cursor:pointer" class="input-group-addon bg-gray"  OnClick="min(\'qty_' + val.id + '\');qtyEdit(\'' + val.id + '\')">-</div>';
                        item += '<input type="text" class="form-control input-sm text-center" value=' + val.request_qty + ' id="qty_' + val.id + '" maxlength="6" max="' + val.outstanding_qty + '">';
                        item += ' <div style="cursor:pointer" class="input-group-addon bg-gray" OnClick="plus(\'qty_' + val.id + '\');qtyEdit(\'' + val.id + '\')">+</div>';
                        item += '</td>';
                        item += "<td style='text-align:right'>" + val.outstanding_qty + "</td>";
                        item += '<td width="30px" style="text-align:center;xdisplay:none"><button type="button" class="btn btn-flat btn-xs btn-danger" onClick="remove(\'' + val.id + '\');"><i class="fa fa-trash"></i></button></td>';
                        item += "</tr>";
                    }
                }
            });
        } 
        else 
        {
            item += '<tr>';
            item += '<td colspan="7" style="text-align:center;font-size: 9px;color: #808484;height: 45px;"><br>Silahkan input Nomor Purchase Order di atas untuk memilih item dan click tombol "select item"</td>';
            item += '</tr>';
        }

        item += "</table>";
        $("#request-item-table").html(item);
    }

    function qtyEdit(obj) {
        var selected = request_item[obj];
        var qty = jQuery('#qty_' + obj).val();

        if (qty < request_item[obj].qty) {
            request_item[obj].request_qty = qty;
            request_item[obj].outstanding_qty = (request_item[obj].qty - qty);
        } else {
            request_item[obj].request_qty = request_item[obj].qty;
            request_item[obj].outstanding_qty = 0;
        }

        createItemRequestTable();
        createPage(obj);
    }

    function getProp(id) {
        var item = request_item[id];
        request_item_page = item.detail;
        jQuery('#item_po').val(item.item_po);
        jQuery('#item_code').val(item.code);
        jQuery('#item_name').val(item.name);
        jQuery('#item_qty_index').val(item.request_qty);
        current_page = 1;

        jQuery('#asset_serie_no').parent().closest('div').removeClass('has-warning');
        jQuery('#asset_year').parent().closest('div').removeClass('has-warning');

        changePage(1);
    }

    function assetInfo(index) 
    {
        var obj = index - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        //alert(item.asset_group); 

        jQuery('#asset_name').val(item.asset_name);
        jQuery('#asset_brand').val(item.asset_brand);
        jQuery('#asset_imei').val(item.asset_imei);
        jQuery('#asset_police_no').val(item.asset_police_no);
        jQuery('#asset_serie_no').val(item.asset_serie_no);
        jQuery('#asset_specification').val(item.asset_specification);
        jQuery('#asset_year').val(item.asset_year);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);
        jQuery('#asset_info').val(item.asset_info);

        if (item.asset_condition === 'B') {
            jQuery('#condition1').prop("checked", true);
        } else if (item.asset_condition === 'BP') {
            jQuery('#condition2').prop("checked", true);
        } else if (item.asset_condition === 'TB') {
            jQuery('#condition3').prop("checked", true);
        } else {
            jQuery("input[name='asset_condition']").prop("checked", false);
        }

        //jQuery('#asset_location').val(item.asset_location);
        $('#asset_location').val(business_area.val());
        jQuery('#asset_location').trigger('change');
        jQuery('#asset_type').val(item.asset_type);
        jQuery('#asset_type').trigger("change");
        jQuery('#asset_group').val(item.asset_group);
        jQuery('#asset_group').trigger('change');
        jQuery('#asset_sub_group').val(item.asset_sub_group);
        jQuery('#asset_sub_group').trigger('change');

        //$('#asset_controller').val(item.asset_controller);

        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);

        if (item.foto_asset.file) {
            jQuery("#foto_asset_thumb_1").prop('src', item.foto_asset.file);
            jQuery(".btn-foto-asset-remove").removeClass('hide');
        } else {
            jQuery("#foto_asset_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-asset-remove").addClass('hide');
        }

        if (item.foto_asset_seri.file) {
            jQuery("#foto_no_seri_thumb_1").prop('src', item.foto_asset_seri.file);
            jQuery(".btn-foto-seri-remove").removeClass('hide');
        } else {
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-seri-remove").addClass('hide');
        }

        if (item.foto_asset_mesin.file) {
            jQuery("#foto_mesin_thumb_1").prop('src', item.foto_asset_mesin.file);
            jQuery(".btn-foto-mesin-remove").removeClass('hide');
        } else {
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-mesin-remove").addClass('hide');
        }
    }

    function openFile(code, id) {
        if (code == 'asset') {
            jQuery("#foto_asset_" + id).trigger('click');
        } else if (code == 'seri') {
            jQuery("#foto_no_seri_" + id).trigger('click');
        } else if (code == 'mesin') {
            jQuery("#foto_mesin_" + id).trigger('click');
        }
    }

    function showImage(code, id) 
    {
        var obj = current_page - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        if (code == 'asset') 
        {
            var src = document.getElementById("foto_asset_1");
            var target = document.getElementById("foto_asset_thumb_1");
        } else if (code == 'seri') {
            var src = document.getElementById("foto_no_seri_1");
            var target = document.getElementById("foto_no_seri_thumb_1");
        } else if (code == 'mesin') {
            var src = document.getElementById("foto_mesin_1");
            var target = document.getElementById("foto_mesin_thumb_1");
        }

        var fr = new FileReader();
        fr.onload = function(e) 
        {
            target.src = this.result;
            if (code == 'asset') {
                item.foto_asset.file = this.result;
            } else if (code == 'seri') {
                item.foto_asset_seri.file = this.result;
            } else if (code == 'mesin') {
                item.foto_asset_mesin.file = this.result;
            }
        };

        var foto = src.files[0];
        //var total_size_foto = 0;
        
        if (code == 'asset') 
        {
            item.foto_asset.name = foto.name;
            item.foto_asset.type = "asset";
            item.foto_asset.size = foto.size;

            if( item.foto_asset.size > 500000 )
            {
                notify({
                        type: 'warning',
                        message: " Foto Asset Size <= 500 KB ! "
                    });
            }      

            $(".btn-foto-asset-remove").removeClass('hide');
        } 
        else if (code == 'seri') 
        {
            item.foto_asset_seri.name = foto.name;
            item.foto_asset_seri.type = "no seri";
            item.foto_asset_seri.size = foto.size;
            
            if( item.foto_asset_seri.size > 500000 )
            {
                notify({
                        type: 'warning',
                        message: " Foto Asset Seri <= 500 KB ! "
                    });
                return false;
            }

            $(".btn-foto-seri-remove").removeClass('hide');
        } else if (code == 'mesin') {
            item.foto_asset_mesin.name = foto.name;
            item.foto_asset_mesin.type = "imei";
            item.foto_asset_mesin.size = foto.size;
            
            if( item.foto_asset_mesin.size > 500000 )
            {
                notify({
                        type: 'warning',
                        message: " Foto Mesin Seri <= 500 KB ! "
                    });
                return false;
            }

            $(".btn-foto-mesin-remove").removeClass('hide');
        }

        fr.readAsDataURL(src.files[0]);
        $('.btn-remove-image' + id).removeClass('hide');
        var status = jQuery('#material-images-' + id).data('status');
    }

    function removeImage(code, id) {
        var obj = id - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        if (code == 'asset') {
            item.foto_asset.file = '';
            item.foto_asset.name = '';
            item.foto_asset.type = '';
            item.foto_asset.size = '';
            jQuery("#foto_asset_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-asset-remove").addClass('hide');
            jQuery("#foto_asset_1").val("");
        } else if (code == 'seri') {
            item.foto_asset_seri.file = "";
            item.foto_asset_seri.name = "";
            item.foto_asset_seri.type = "";
            item.foto_asset_seri.size = "";
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-seri-remove").addClass('hide');
            jQuery("#foto_no_seri_1").val("");
        } else if (code == 'mesin') {
            item.foto_asset_mesin.file = "";
            item.foto_asset_mesin.name = "";
            item.foto_asset_mesin.type = "";
            item.foto_asset_mesin.size = "";
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-mesin-remove").addClass('hide');
            jQuery("#foto_mesin_1").val("");
        }
    }

    function prevPage() {
        var id = (current_page - 1);
        if (validatePage(id)) {
            jQuery(".loading-event").fadeIn();
            if (current_page > 1) {
                current_page--;
                changePage(current_page);
            }
            jQuery(".loading-event").fadeOut();
        }
    }

    function nextPage() {
        var id = (current_page - 1);
        if (validatePage(id)) {
            jQuery(".loading-event").fadeIn();
            if (current_page < numPages()) {
                current_page++;
                changePage(current_page);
            }
            jQuery(".loading-event").fadeOut();
        }
    }


    function changePage(page) 
    {
        /* var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var page_span = document.getElementById("page"); */

        var btn_next = document.getElementsByClassName("btn_next");
        var btn_prev = document.getElementsByClassName("btn_prev");
        var page_span = document.getElementsByClassName("total-page");

        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();
        /*  page_span.innerHTML = page + '/' + request_item_page.length; */
        $('.total-page').text(page + '/' + request_item_page.length);

        if (page == 1) {
            /* btn_prev.style.visibility = "hidden"; */
            jQuery('.btn_prev').addClass('hide');
        } else {
            /* btn_prev.style.visibility = "visible"; */
            jQuery('.btn_prev').removeClass('hide');
        }

        if (page == numPages()) {
            /* btn_next.style.visibility = "hidden"; */
            $('.btn_next').addClass('hide');
        } else {
            /* btn_next.style.visibility = "visible"; */
            $('.btn_next').removeClass('hide');
        }

        assetInfo(current_page);
        $("#foto_asset_1").val("");
        $("#foto_no_seri_1").val("");
        $("#foto_mesin_1").val("");

        $("#detail-item-request-panel").stop().animate({
            scrollTop: 0
        }, 500, 'swing', function() {

        });

    }

    function validatePage(id) 
    {
        var obj = id;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];
        var valid = true;
        $.each(request, function(i, field) 
        {
            if (field) 
            {
                $.each(field.detail, function(key, val) 
                {
                    if( val.asset_type == 'U4010' || val.asset_type == 'M4010' || val.asset_type == 'A4010' || val.asset_type == 'E4010' || val.asset_type == 'E4030' || val.asset_type == 4030 || val.asset_type == 4010 )
                    {
                        if (val.asset_serie_no === "") {
                            valid = false;
                            jQuery('#asset_serie_no').parent().closest('div').addClass('has-warning');
                            notify({
                                type: 'warning',
                                message: 'No Seri / Rangka tidak boleh kosong!'
                            });
                        } else {
                            jQuery('#asset_serie_no').parent().closest('div').removeClass('has-warning');
                        }

                        if( $.trim(val.asset_serie_no).length < 3 )
                        {
                            valid = false;
                            $('#asset_serie_no').parent().closest('div').addClass('has-warning');
                            notify({
                                type: 'warning',
                                message: 'No Seri / Rangka minimal 3 char pada asset ' + field.name + ' page ' + (key + 1) + ' '
                            });
                        }else {
                            $('#asset_serie_no').parent().closest('div').removeClass('has-warning');
                        }

                        if (val.asset_imei === "") {
                            valid = false;
                            jQuery('#asset_imei').focus();
                            notify({
                                type: 'warning',
                                message: 'No Mesin / IMEI tidak boleh kosong!'
                            });
                        }

                        if( $.trim(val.asset_imei).length < 3 )
                        {
                            valid = false;
                            $('#asset_imei').parent().closest('div').addClass('has-warning');
                            notify({
                                type: 'warning',
                                message: 'No Mesin / IMEI minimal 3 char pada asset ' + field.name + ' page ' + (key + 1) + ' '
                            });
                        }else {
                            $('#asset_imei').parent().closest('div').removeClass('has-warning');
                        }
                        
                    }

                    if (val.asset_year === "") {
                        valid = false;
                        jQuery('#asset_year').parent().closest('div').addClass('has-warning');
                        notify({
                            type: 'warning',
                            message: 'Tahun Asset tidak boleh kosong!'
                        });
                    } else {
                        jQuery('#asset_year').parent().closest('div').removeClass('has-warning');
                    }

                    if (val.asset_location === "") {
                        valid = false;
                        jQuery('#asset_location').focus();
                        notify({
                            type: 'warning',
                            message: 'Lokasi Asset tidak boleh kosong!'
                        });
                    }

                    if (val.asset_condition === "") {
                        valid = false;
                        notify({
                            type: 'warning',
                            message: 'Kondisi Asset tidak boleh kosong!'
                        });
                    }

                    if (val.asset_pic_name === "") {
                        valid = false;
                        notify({
                            type: 'warning',
                            message: 'Nama Penanggung Jawab Asset tidak boleh kosong!'
                        });
                    }

                    if (val.asset_pic_level === "") {
                        valid = false;
                        notify({
                            type: 'warning',
                            message: 'Jabatan Penanggung Jawab Asset tidak boleh kosong!'
                        });
                    }
            //     });
            // }
        });
        return valid;
    }

    function numPages() {
        return Math.ceil(request_item_page.length / records_per_page);
    }

    function min(param) {
        if (jQuery("#" + param).val() > 1) {
            if (jQuery("#" + param).val() > 1) jQuery("#" + param).val(+jQuery("#" + param).val() - 1);
        }
    }

    function plus(param) {
        jQuery("#" + param).val(+jQuery("#" + param).val() + 1)
    }

    jQuery('.add').click(function() {
        jQuery(this).prev().val(+jQuery(this).prev().val() + 1);
    });

    jQuery('.sub').click(function() {
        if (jQuery(this).next().val() > 1) {
            if (jQuery(this).next().val() > 1) jQuery(this).next().val(+jQuery(this).next().val() - 1);
        }
    });

    function requestItemData() {
        var total = 0;
        jQuery.each(request_item, function(key, val) {
            if (val) {
                total++;
            }
        });
        return total;
    }

    function getBase64(i, file) {
        var reader = new FileReader();
        var base64 = ''
        reader.readAsDataURL(file);
        reader.onload = function() {
            request_docs[i].file = reader.result;
        };
        reader.onerror = function(error) {
            console.log('Error: ', error);
        };
    }

    function get_qty_po(item_po, qty, kode_material)
    {
        var po_no = $("#po_no").val();
        var datax = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.qty_po") !!}?po_no='+po_no+'&item_po='+item_po+'&kode_material='+kode_material)));

        var qty_po = qty-datax.nilai;

        return qty_po;
    }

    function send_email_create_po(noreg)
    {
        //alert(noreg);

        var getnoreg = noreg;
        var no_registrasi= getnoreg.replace(/\//g, '-');

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('request/email_create_po') }}",
            method: "POST",
            data: param+"&noreg="+no_registrasi,
            beforeSend: function() {
                $('.loading-event').fadeIn();
            },
            success: function(result) 
            {
                /*
                //alert(result.status);
                if (result.status) 
                {
                    //$("#approve-modal").modal("hide");
                    //$("#data-table").DataTable().ajax.reload();
                    notify({
                        type: 'success',
                        message: result.message
                    });

                    $("#create-button-sync-sap").hide();
                    $("#button-approve").show();
                    $(".button-reject").attr("disabled", true); 
                } 
                else 
                {
                    notify({
                        type: 'warning',
                        message: result.message
                    });
                }
                */
            },
            complete: function() {
                jQuery('.loading-event').fadeOut();
            }
        }); 
    }

    function getdatepicker()
    {
        $("#asset_year").datepicker({
            format: "yyyy",
            autoclose: true,
            viewMode: "years", 
            minViewMode: "years",
            maxDate: "today"
        });
    }

</script>

@stop