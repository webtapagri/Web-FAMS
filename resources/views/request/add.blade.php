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
</style>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box">
            <form class="form-horizontal request-form" id="request-form" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tipe Transaksi</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="transaction_type" id="transaction_type">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tanggal</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm datepicker" name="request_date" id="request_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Business Area</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm" name="business_area" id="business_area">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">No. Purchare Order</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="" autocomplete="off" maxlength="10" onkeypress="return isNumber(event)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tgl PO</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm datepicker" name="po_date" id="po_date" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Kode vendor</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="vendor_code" id="vendor_code" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Nama vendor</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="vendor_name" id="vendor_name" autocomplete="off" required readonly>
                            </div>
                        </div>
                        <div class="form-group {{ $data['type'] == 'amp' ? '':'hide' }}">
                            <label for="plant" class="col-md-3">Berita acara serah terima</label>
                            <div class="col-md-4">
                                <input type="file" class="form-control input-sm" name="docs" multiple id="docs">(Upload Berkas Max 1 MB)
                            </div>
                        </div>
                        <div class="form-group select-item-panel hide">
                            <label for="plant" class="col-md-3">
                                <button type="button" class="btn btn-flat btn-danger btn-add-items">Select Item</button>
                            </label>
                            <div class="col-md-9">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed" id="request-item-table">
                                    <tr>
                                        <th>Item PO</th>
                                        <th>Kode</th>
                                        <th>Name</th>
                                        <th>Qty</th>
                                        <th>Qty diajukan</th>
                                        <th>Qty Outstanding</th>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align:center">No item selected</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer clearfix">
                        <button type="submit" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit</button>
                        <button type="button" class="btn btn-default btn-flat btn-cancel pull-right" style="margin-right: 5px;">Clear</button>
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
                                <span class="help-block">Please select the item to show the detail</span>
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
                                    <li class="active" style="border-bottom:none !important;"><a href="#panel-initial" data-toggle="tab" class="panel-initial" style="background-color:#f3f3f3;border-bottom:none;font-weight:800">Rincian Informasi Asset | page: <span id="page"></span></a></li>
                                    <li class="pull-right"><a href="javascript:nextPage()" class="text-muted" id="btn_next">Next <i class="fa fa-arrow-right"></i></a></li>
                                    <li class="pull-right"><a href="javascript:prevPage()" class="text-muted" id="btn_prev"><i class="fa fa-arrow-left"></i> Prev</a></li>
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
                                                <label for="plant" class="col-md-2 text-right">Jenis asset</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="asset_type" value="" id="asset_type" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Group</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="asset_group" value="" id="asset_group" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Sub Group</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="asset_sub_group" value="" id="asset_sub_group" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-3">
                                                    <h4>Asset Class</h4>
                                                </label>
                                                <div class="col-md-9">
                                                    <h4>E4010</h4>
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
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Seri / No Rangka</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_serie_no" id="asset_serie_no">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Mesin / IMEI</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_imei" id="asset_imei">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Polisi</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_police_no" id="asset_police_no">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Lokasi Asset</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_location" id="asset_location">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Tahun Asset</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_year" id="asset_year">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Kondisi Asset</label>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="optionsRadios1" value="1" checked>
                                                                Baik
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="optionsRadios2" value="2">
                                                                Butuh Perbaikan
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="asset_condition" id="optionsRadios3" value="3">
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
                                                <label class="col-md-2 col-md-offset-1 ">Foto aset</label>
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
                                                <label class="col-md-2 col-md-offset-1 ">Foto no. seri / no rangka</label>
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
                                                <label class="col-md-2 col-md-offset-1 ">Foto No msin / IMEI</label>
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
                                            <h4>Penanggung jawab Aset:</h4>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Nama</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_pic_name" id="asset_pic_name">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Jabatan</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="asset_pic_level" id="asset_pic_level">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer clearfix">
                        <button type="submit" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Draft</button>
                        <button type="submit" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit</button>
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
                            <th>Item PO</th>
                            <th>Kode</th>
                            <th>Name</th>
                            <th>Qty</th>
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
    var po_date = jQuery("#po_date");
    var vendor_code = jQuery("#vendor_code");
    var vendor_name = jQuery("#vendor_name");


    jQuery(document).ready(function() {
        jQuery(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });


        jQuery('#request-detail-page').addClass('sub-loader');
        jQuery(".btn-cancel").on('click', function() {
            var conf = confirm("Are you sure you want to cancel this request?");
            document.getElementById("request-form").reset();
            if (conf == true) {
                request_item = [];
                request_item_page = [];
                createItemRequestTable();
                notify({
                    type: 'error',
                    message: 'form has been cleared!'
                });
            }
        });

        jQuery("#request_date, #po_date").datepicker({
            format: "mm/dd/yyyy",
            autoclose: true
        });

        jQuery("#transaction_type").select2({
            data: [{
                    id: '1',
                    text: 'Barang'
                },
                {
                    id: '2',
                    text: 'Jasa'
                },
                {
                    id: '3',
                    text: 'Lain-lain'
                },
            ],
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        jQuery("#business_area").select2({
            data: [{
                    id: '1',
                    text: 'LoV'
                },
                {
                    id: '2111',
                    text: '2111 - HO JAKARTA SAWIT'
                },
                {
                    id: '2112',
                    text: '2112 - RO JAMBI'
                },
                {
                    id: '2113',
                    text: '2113 - HO JAKARTA KARET'
                },
            ],
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        jQuery("#request-form").on("submit", function(e) {
            e.preventDefault();

            if (requestItemData() > 0) {
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
                jQuery.each(request_item, function(key, val) {
                    if (val.name) {
                        items.push({
                            id: val.id,
                            text: val.code + ' - ' + val.name
                        })
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


            } else {
                notify({
                    type: 'warning',
                    message: 'please, add an item'
                });
            }
        });

        jQuery("#code-asset-form").on("submit", function(e) {
            e.preventDefault();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var param = {
                transaction_type: transaction_type.val(),
                request_date: request_date.val(),
                business_area: business_area.val(),
                po_no: po_no.val(),
                po_date: po_date.val(),
                vendor_code: vendor_code.val(),
                vendor_name: vendor_name.val(),
                docs: request_docs,
                asset: request_item
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
                success: function(result) {
                    if (result.status) {
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        notify({
                            type: 'error',
                            message: 'reqeust has been submited!'
                        });
                        window.location.href = "{{ url('request') }}";
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
            console.log(param);
            return false;
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
                        window.location.href = "{{ url('mastermaterial') }}";
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

        jQuery("#asset_name").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_name = jQuery(this).val();
        });

        jQuery("#asset_type").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_type = jQuery(this).val();
        });

        jQuery("#asset_group").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_group = jQuery(this).val();
        });

        jQuery("#asset_sub_group").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_sub_group = jQuery(this).val();
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

        jQuery("#asset_location").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_location = jQuery(this).val();
        });
    });

    function showPO() {
        jQuery('.loading-event').fadeIn();
        var no_po = jQuery("#po_no").val();
        var data = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.no_po") !!}?no_po=' + no_po)));

        if (data.AEDAT) {
            jQuery("#po_date").val(data.AEDAT);
            jQuery("#vendor_code").val(data.LIFNR);
            jQuery("#vendor_name").val(data.NAME1);
            jQuery('.select-item-panel').removeClass("hide");
            var item = '<table class="table table-bordered table-condensed" id="request-item-table">';
            item += '<tr>';
            item += '<th width="45px">Select</th>';
            item += '<th>Item PO</th>';
            item += '<th>Kode</th>';
            item += ' <th>Name</th>';
            item += '<th>Qty</th>';
            item += '</tr>';

            jQuery.each(data.DETAIL_ITEM, function(key, val) {
                selected_detail_item.push(val);
                item += "<tr>";
                item += "<td><input type='checkbox' value='" + key + "' ></td>";
                item += "<td>" + val.EBELP + "</td>";
                item += "<td>" + val.MATNR + "</td>";
                item += "<td>" + val.MAKTX + "</td>";
                item += "<td style='text-align:right'>" + val.MENGE + "</td>";
                item += "</tr>";
            });
            item += "</table>";
            jQuery("#table-detail-item").html(item);
        } else {
            jQuery("#po_date").val("");
            jQuery("#vendor_code").val("");
            jQuery("#vendor_name").val("");
            jQuery('.select-item-panel').addClass("hide");
            notify({
                type: 'warning',
                message: "No PO is not found!"
            });
        }
        jQuery('.loading-event').fadeOut();
    }

    function addItem() {
        $('#table-detail-item').find('input[type="checkbox"]:checked').each(function() {
            var index = jQuery(this).val();
            console.log(index);
        });

        return false;

        var id = makeInt(5);
        request_item[id] = {
            id: id,
            item_po: item_count,
            code: makeid(5),
            name: "SEPEDA MOTOR 150 HONDA VERZA - " + item_count,
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
            detail: []
        };

        item_count++;

        createPage(id);
        createItemRequestTable();
        jQuery("#item-detail-modal").modal('hide');
    }

    function createPage(id) {
        request_item_page = [];
        data_page = [];
        var item_detail = [];
        var item = request_item[id];
        for (var i = 0; i < item.request_qty; i++) {
            item_detail.push({
                asset_type: '',
                asset_group: '',
                asset_sub_group: '',
                asset_name: '',
                asset_brand: '',
                asset_imei: '',
                asset_police_no: '',
                asset_serie_no: '',
                asset_specification: '',
                asset_location: '',
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

    function makeid(length) {
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

    function remove(obj) {
        var conf = confirm("Are you sure you want to delete this data?");
        if (conf == true) {
            request_item[obj] = [];
            data_detail[obj] = [];

            createItemRequestTable();
        }
    }

    function createItemRequestTable() {
        var item = '<table class="table table-bordered table-condensed" id="request-item-table">';
        item += '<tr>';
        item += '<th>Item PO</th>';
        item += '<th>Kode</th>';
        item += ' <th>Name</th>';
        item += '<th>Qty</th>';
        item += '<th width="115px">Qty diajukan</th></th>';
        item += '<th>Qty Outstanding</th>';
        item += '<th style="width: 40px"></th>';
        item += '</tr>';

        if (requestItemData() > 0) {
            jQuery.each(request_item, function(key, val) {
                if (val.name) {
                    item += "<tr>";
                    item += "<td>" + val.item_po + "</td>";
                    item += "<td>" + val.code + "</td>";
                    item += "<td>" + val.name + "</td>";
                    item += "<td style='text-align:right'>" + val.qty + "</td>";
                    item += '<td class="text-center">';
                    item += '<div class="input-group">';
                    item += ' <div style="cursor:pointer" class="input-group-addon bg-gray"  OnClick="min(\'qty_' + val.id + '\');qtyEdit(\'' + val.id + '\')">-</div>';
                    item += '<input type="text" class="form-control input-sm text-center" value=' + val.request_qty + ' id="qty_' + val.id + '" maxlength="6">';
                    item += ' <div style="cursor:pointer" class="input-group-addon bg-gray" OnClick="plus(\'qty_' + val.id + '\');qtyEdit(\'' + val.id + '\')">+</div>';
                    item += '</td>';
                    item += "<td style='text-align:right'>" + val.outstanding_qty + "</td>";
                    item += '<td width="30px" style="text-align:center"><button type="button" class="btn btn-flat btn-xs btn-danger" onClick="remove(\'' + val.id + '\');"><i class="fa fa-trash"></i></button></td>';
                    item += "</tr>";
                }
            });
        } else {
            item += '<tr>';
            item += ' <td colspan="7" style="text-align:center">No item selected</td>';
            item += '</tr>';
        }
        item += "</table>";
        jQuery("#request-item-table").html(item);
    }

    function qtyEdit(obj) {
        var selected = request_item[obj];
        var qty = jQuery('#qty_' + obj).val();
        request_item[obj].request_qty = qty;
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

        changePage(1);
    }

    function assetInfo(index) {
        var obj = index - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        jQuery('#asset_name').val(item.asset_name);
        jQuery('#asset_type').val(item.asset_type);
        jQuery('#asset_group').val(item.asset_group);
        jQuery('#asset_sub_group').val(item.asset_sub_group);
        jQuery('#asset_brand').val(item.asset_brand);
        jQuery('#asset_imei').val(item.asset_imei);
        jQuery('#asset_police_no').val(item.asset_police_no);
        jQuery('#asset_serie_no').val(item.asset_police_no);
        jQuery('#asset_specification').val(item.asset_specification);
        jQuery('#asset_year').val(item.asset_year);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);
        jQuery('#asset_info').val(item.asset_info);
        jQuery('#asset_location').val(item.asset_location);
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

    function showImage(code, id) {
        var obj = current_page - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        if (code == 'asset') {
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
        fr.onload = function(e) {
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
        if (code == 'asset') {
            item.foto_asset.name = foto.name;
            item.foto_asset.type = "asset";
            item.foto_asset.size = foto.size;
            jQuery(".btn-foto-asset-remove").removeClass('hide');
        } else if (code == 'seri') {
            item.foto_asset_seri.name = foto.name;
            item.foto_asset_seri.type = "no seri";
            item.foto_asset_seri.size = foto.size;
            jQuery(".btn-foto-seri-remove").removeClass('hide');
        } else if (code == 'mesin') {
            item.foto_asset_mesin.name = foto.name;
            item.foto_asset_mesin.type = "imei";
            item.foto_asset_mesin.size = foto.size;
            jQuery(".btn-foto-mesin-remove").removeClass('hide');
        }

        fr.readAsDataURL(src.files[0]);
        jQuery('.btn-remove-image' + id).removeClass('hide');
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
        } else if (code == 'seri') {
            item.foto_asset_seri.file = "";
            item.foto_asset_seri.name = "";
            item.foto_asset_seri.type = "";
            item.foto_asset_seri.size = "";
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-seri-remove").addClass('hide');
        } else if (code == 'mesin') {
            item.foto_asset_mesin.file = "";
            item.foto_asset_mesin.name = "";
            item.foto_asset_mesin.type = "";
            item.foto_asset_mesin.size = "";
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-mesin-remove").addClass('hide');
        }
    }

    function prevPage() {
        jQuery(".loading-event").fadeIn();
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
        jQuery(".loading-event").fadeOut();
    }

    function nextPage() {
        jQuery(".loading-event").fadeIn();
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
        jQuery(".loading-event").fadeOut();
    }

    function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var page_span = document.getElementById("page");

        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();
        page_span.innerHTML = page + '/' + request_item_page.length;

        if (page == 1) {
            btn_prev.style.visibility = "hidden";
        } else {
            btn_prev.style.visibility = "visible";
        }

        if (page == numPages()) {
            btn_next.style.visibility = "hidden";
        } else {
            btn_next.style.visibility = "visible";

        }

        assetInfo(current_page);
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
            if (val.name) {
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
</script>

@stop