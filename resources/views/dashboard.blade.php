{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<!-- <h1>Dashboard</h1> -->
@stop

@section('content')
<style>
    @media screen and (max-width: 600px) {
        .filter {
            display: none !important;
        }
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                    </div>
                    <table id="data-table" class="table table-bordered table-condensed dt-responsive" style="width:100%" cellspacing="0">
                        <thead>
                            <tr class="heading">
                                <th>No Reg</th>
                                <th>Tipe</th>
                                <th>PO</th>
                                <th>No PO</th>
                                <th>Tgl.Pengajuan</th>
                                <th>Requestor</th>
                                <th>Tgl. PO</th>
                                <th>Kode Vendor</th>
                                <th>Nama Vendor</th>
                            </tr>
                            <tr class="filter">
                                <th><input type="text" class="form-control input-xs form-filter" name="no_reg"></th>
                                <th>
                                    <select type="text" class="form-control input-xs form-filter" name="transaction_type" id="flt_transaction_type">
                                        <option></option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control input-xs form-filter" name="po_type" id="po_type">
                                        <option></option>
                                    </select>
                                </th>
                                <th><input type="text" class="form-control input-xs form-filter" name="no_po"></th>
                                <th><input type="text" class="form-control input-xs form-filter datepicker" name="request_date" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="requestor"></th>
                                <th><input type="text" class="form-control input-xs form-filter datepicker" name="po_date" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="vendor_code"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="vendor_name"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="detail-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i id="modalHeader"></i></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal code-asset-form" id="code-asset-form">
                    <fieldset disabled="disabled">
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

                                <div class="docs-files-detail hide">
                                    <hr>
                                    <div class="form-group">
                                        <label for="plant" class="col-md-2">Berita acara serah terima</label>
                                        <div class="col-md-10">
                                            <div id="berita-acara-detail"></div>
                                        </div>
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
                                                        <label class="col-md-2 col-md-offset-1 ">Foto aset</label>
                                                        <div class="col-md-9">
                                                            <div id="filesContainer">
                                                                <div class="col-md-4" id="panel-image-1">
                                                                    <div class="form-group hide">
                                                                        <input type="file" id="foto_asset_1" name="foto_asset_1" accept='image/*' OnChange="showImage('asset', 1)">
                                                                        <p class="help-block">*jpg, png</p>
                                                                    </div>
                                                                    <div class="image-group">
                                                                        <div class="sp-wrap">
                                                                            <a href="" id="a_foto_asset_thumb_1"><img id="foto_asset_thumb_1" data-status="0" title="click to change image" class="img-responsive" src="{{URL::asset('img/add-img.png')}}"></a>
                                                                        </div>
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
                                                                        <div class="sp-wrap">
                                                                            <a href="" id="a_foto_no_seri_thumb_1">
                                                                                <img id="foto_no_seri_thumb_1" data-status="0" title="click to change image" class="img-responsive" src="{{URL::asset('img/add-img.png')}}">
                                                                            </a>
                                                                        </div>
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
                                                                        <div class="sp-wrap">
                                                                            <a href="" id="a_foto_mesin_thumb_1">
                                                                                <img id="foto_mesin_thumb_1" data-status="0" title="click to change image" class="img-responsive" src="{{URL::asset('img/add-img.png')}}">
                                                                            </a>
                                                                        </div>
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
                    </fieldset>
                    <div class="box-footer clearfix">
                        <button type="button" class="btn btn-default btn-flat btn-back-request-form pull-right" data-dismiss="modal" style="margin-right: 5px;">Close</button>
                    </div>
            </div>

            </form>
        </div>
    </div>
</div>
</div>
@stop

@section('js')
<script>
    var attribute = [];
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
    jQuery(document).ready(function() {

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var grid = new Datatable();
        grid.init({
            src: jQuery("#data-table"),
            onSuccess: function(grid) {},
            onError: function(grid) {},
            onDataLoad: function(grid) {},
            loadingMessage: 'Loading...',
            dataTable: {
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "autoWidth": true,
                "responsive": true,
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150]
                ],
                "pageLength": 10,
                "ajax": {
                    url: "{!! route('get.outstanding') !!}"
                },
                columns: [{
                        "render": function(data, type, row) {
                            var content = '<a href="javascript:;" style="font-weight:bold" OnClick="requestDetail(' + row.id + ')" title="klik untuk menampikan detail request dari no reg ' + row.no_reg + '" >' + row.no_reg + ' &nbsp;<i style="font-size:7px" class="fa fa-mail-forward"></i></a>';
                            return content;
                        }
                    }, {
                        "render": function(data, type, row) {
                            if (row.type == 1) {
                                var content = 'Barang'
                            } else if (row.type == 2) {
                                var content = 'Jasa'
                            } else if (row.type == 3) {
                                var content = 'lain-lain'
                            }

                            return content;
                        }
                    }, {
                        "render": function(data, type, row) {
                            if (row.po_type == 0) {
                                var content = '<span class="label label-primary">SAP</span>';
                            } else if (row.po_type == 1) {
                                var content = '<span class="label label-danger">AMP</span>';
                            }

                            return content;
                        }
                    },
                    {
                        data: 'no_po',
                        name: 'no_po'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'requestor',
                        name: 'requestor'
                    },
                    {
                        data: 'po_date',
                        name: 'po_date'
                    },
                    {
                        data: 'vendor_code',
                        name: 'vendor_code'
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name'
                    }
                ],
                columnDefs: [{
                        targets: [3, 7],
                        width: '10%'
                    },
                    {
                        targets: [8],
                        width: '15%'
                    },
                    {
                        targets: [4, 6, 5, 7],
                        width: '10%'
                    },
                    {
                        targets: [0, 1],
                        width: '8%'
                    },
                    {
                        targets: [2],
                        className: 'text-center',
                        width: '5%'
                    },
                ],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": [],
            }
        });

        jQuery("#data-table").on('shown.bs.collapse', function() {
            jQuery($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

        jQuery('#data-table').wrap('<div class="dataTables_scroll" />');

        jQuery('.sp-wrap').smoothproducts();

        jQuery(".datepicker").datepicker({
            format: "mm/dd/yyyy",
            autoclose: true
        });

        jQuery("#flt_transaction_type").select2({
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

        jQuery("#po_type").select2({
            data: [{
                    id: '0',
                    text: 'SAP'
                },
                {
                    id: '1',
                    text: 'AMP'
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

        jQuery(".btn-refresh-data-table").on("click", function() {
            jQuery("#flt_transaction_type").val('');
            jQuery("#flt_transaction_type").trigger("change");

            jQuery("#po_type").val('');
            jQuery("#po_type").trigger("change");

            var oTable = $('#data-table').dataTable();

            $('.sorting, .sorting_asc, .sorting_desc').dblclick(function() {
                oTable.fnSort([]);
                return false;
            });

            /* jQuery('#data-table').dataTable().fnSortNeutral(); */
        });
    });

    function requestDetail(id) {
        jQuery(".loading-event").fadeIn();
        var asset = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetail") !!}/?id=' + id)));
        var data = asset[0];
        jQuery("#asset_request_date").val(getDate(data.request_date));
        /* jQuery("#asset_business_area").val(data.business_area); */
        jQuery("#asset_po_no").val(data.no_po);
        jQuery("#asset_business_area").val(data.business_area);
        jQuery("#asset_po_date").val(getDate(data.po_date));
        jQuery("#asset_vendor_code").val(data.vendor_code);
        jQuery("#asset_vendor_name").val(data.vendor_name);

        var asset_files = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetailfiles") !!}/?id=' + id)));
        if (asset_files.length > 0) {
            var body = '';
            jQuery.each(asset_files, function(key, val) {
                body += '<div class="col-md-8">';
                body += '<div class="input-group">';
                body += '<input type="text" class="form-control input-sm" value="' + val.file_name + '">';
                body += '<a href="' + val.file + '" class="input-group-addon btn-red" Download="' + val.file_name + '"><i class="fa fa-download"></i></a>';
                body += ' </div></div>';
            });
            jQuery('#berita-acara-detail').html(body);
            jQuery(".docs-files-detail").removeClass("hide");
        } else {
            jQuery('#berita-acara-detail').html('');
            jQuery(".docs-files-detail").addClass("hide");
        }

        var asset_item = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetailitem") !!}/?id=' + id)));
        var select_item = [];
        jQuery.each(asset_item, function(key, val) {
            select_item.push({
                id: val.id,
                text: val.material_code + ' - ' + val.material_name
            });
        });

        jQuery("#detail_item_selected").select2({
            data: select_item,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        }).on("change", function() {
            getProp(jQuery(this).val());
        });

        request_item = [];
        jQuery.each(asset_item, function(key, val) {
            request_item[val.id] = {
                id: val.id,
                item_po: val.item_id,
                code: val.material_code,
                name: val.material_name,
                qty: val.qty,
                request_qty: val.qty_request,
                outstanding_qty: (val.qty - val.qty_request),
                detail: []
            };
            createPage(val.id);
        });

        jQuery("#detail-modal .modal-title").text('Request detail of ' + data.no_reg + ' / ' + data.vendor_name);
        jQuery("#detail-modal").modal({
            backdrop: 'static',
            keyboard: false
        });
        jQuery("#detail-modal").modal('show');
        jQuery("#detail_item_selected").val(select_item[0].id);
        jQuery("#detail_item_selected").trigger("change");
        jQuery(".loading-event").fadeOut();
    }

    function createPage(id) {
        request_item_page = [];
        data_page = [];
        var item_detail = [];
        var item = request_item[id];
        var asset_item_po = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetailitempo") !!}/?id=' + id)));
        jQuery.each(asset_item_po, function(key, val) {
            var files = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetailitemfile") !!}/?id=' + val.id)));
            var file_foto_asset = [];
            var file_foto_seri = [];
            var file_foto_mesin = [];
            jQuery.each(files, function(i, field) {
                if (field.category === "asset") {
                    file_foto_asset = {
                        name: field.file_name,
                        size: field.size,
                        type: field.type,
                        file: field.file
                    };
                } else if (field.category === "no seri") {
                    file_foto_seri = {
                        name: field.file_name,
                        size: field.size,
                        type: field.type,
                        file: field.file
                    };
                } else if (field.category === "imei") {
                    file_foto_mesin = {
                        name: field.file_name,
                        size: field.size,
                        type: field.type,
                        file: field.file
                    };
                }
            });

            item_detail.push({
                asset_type: val.asset_type,
                asset_group: val.asset_group,
                asset_sub_group: val.asset_sub_group,
                asset_name: val.asset_name,
                asset_brand: val.asset_brand,
                asset_imei: val.asset_imei,
                asset_police_no: val.asset_police_no,
                asset_serie_no: val.asset_serie_no,
                asset_specification: val.asset_specification,
                asset_location: val.asset_location,
                asset_condition: val.asset_condition,
                asset_year: val.asset_year,
                asset_pic_name: val.asset_pic_name,
                asset_pic_level: val.asset_pic_level,
                asset_foto: '',
                asset_info: val.asset_info,
                foto_asset: file_foto_asset,
                foto_asset_seri: file_foto_seri,
                foto_asset_mesin: file_foto_mesin
            });
        });

        request_item[id].detail = item_detail;
    }


    function requestItemData() {
        var total = 0;
        jQuery.each(request_item, function(key, val) {

            if (val) {
                total++;
            }
        });
        return total;
    }

    function getProp(id) {

        var item = request_item[id];
        request_item_page = item.detail;
        jQuery('#item_po').val(item.item_po);
        jQuery('#item_code').val(item.code);
        jQuery('#item_name').val(item.name);
        jQuery('#item_qty_index').val(item.request_qty);
        current_page = 1;
        changePage(1);
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

        assetInfo(page);
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
        jQuery('#asset_serie_no').val(item.asset_serie_no);
        jQuery('#asset_specification').val(item.asset_specification);
        jQuery('#asset_year').val(item.asset_year);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);
        jQuery('#asset_info').val(item.asset_info);
        jQuery('#asset_location').val(item.asset_location);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);

        if (item.asset_condition === 'B') {
            jQuery('#condition1').prop("checked", true);
        } else if (item.asset_condition === 'BP') {
            jQuery('#condition2').prop("checked", true);
        } else if (item.asset_condition === 'TB') {
            jQuery('#condition3').prop("checked", true);
        } else {
            jQuery("input[name='asset_condition']").prop("checked", false);
        }

        if (item.foto_asset.file) {
            jQuery("#foto_asset_thumb_1").prop('src', item.foto_asset.file);
            jQuery("#a_foto_asset_thumb_1").prop('href', item.foto_asset.file);
        } else {
            jQuery("#foto_asset_thumb_1").prop('src', "{{URL::asset('img/default-img.png')}}");
            jQuery("#a_foto_asset_thumb_1").prop('href', "{{URL::asset('img/default-img.png')}}");
        }

        if (item.foto_asset_seri.file) {
            jQuery("#foto_no_seri_thumb_1").prop('src', item.foto_asset_seri.file);
            jQuery("#a_foto_no_seri_thumb_1").prop('href', item.foto_asset_seri.file);
        } else {
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/default-img.png')}}");
            jQuery("#a_foto_no_seri_thumb_1").prop('href', "{{URL::asset('img/default-img.png')}}");
        }

        if (item.foto_asset_mesin.file) {
            jQuery("#foto_mesin_thumb_1").prop('src', item.foto_asset_mesin.file);
            jQuery("#a_foto_mesin_thumb_1").prop('href', item.foto_asset_mesin.file);
        } else {
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/default-img.png')}}");
            jQuery("#a_foto_mesin_thumb_1").prop('href', "{{URL::asset('img/default-img.png')}}");
        }


    }


    function numPages() {
        return Math.ceil(request_item_page.length / records_per_page);
    }

    function getDate(param) {
        var bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        var date = new Date(param);
        var month = date.getMonth();

        return date.getDate() + " " + bulan[month] + " " + date.getFullYear();
    }
</script>
@stop