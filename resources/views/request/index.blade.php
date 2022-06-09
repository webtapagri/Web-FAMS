@extends('adminlte::page')
@section('title', 'FAMS - request')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <button href="#" class="btn btn-flat btn-sm btn-flat label-danger btn-refresh pull-right"><i class="glyphicon glyphicon-refresh" title="Refresh"></i></button>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-danger btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
                                Pengajuan &nbsp;&nbsp;&nbsp;
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:;" class="btn-add-po">PO</a></li>
                                <li><a href="javascript:;" class="btn-sewa-amp">Sewa AMP</a></li>
                                <li><a href="javascript:;" class="btn-mutasi-amp" title="Mutasi asset sewa AMP antar site">Mutasi AMP</a></li>
                            </ul>
                        </div>
                    </div>
                    <table id="data-table" class="table table-hover table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>No Reqgistrasi</th>
                                <th>Tgl Pengajuan</th>
                                <th>Kode Asset</th>
                                <th>Kode Asset FAMS</th>
                                <th>Verifikasi BA</th>
                                <th width="35px">Action</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" class="form-control input-sm form-filter" name="request_no"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="request_date"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="controller_asset_code"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="controller_asset_code"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="verification"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<div id="add-data-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form" class="form-horizontal">
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tipe Transaksi</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="transaction_type" id="transaction_type">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tanggal</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm datepicker" name="request_date" id="request_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Business Area</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="business_area" id="business_area">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">No. Purchare Order</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Tgl PO</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="description" value="23/03/2019" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Kode vendor</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="description" id="description" value="XXXXX" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">Nama vendor</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="description" value="DAYA ANUGERAH MANDIRI" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-3">
                                <button type="submit" class="btn btn-flat btn-danger">Add</button>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-flat label-danger" OnClick="saveRequest()" style="margin-right: 5px;">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="pdf-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i id="modalHeader"></i></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div id="code-asset-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="code-asset-form" class="form-horizontal">
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Tanggal</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm " name="" id="" value="23/03/2019" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Business Area</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="business_area" id="business_area" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">No. PO</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Tgl PO</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="description" value="23/03/2019" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Kode vendor</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control input-sm" name="description" id="description" value="XXXXX" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Nama vendor</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="description" value="DAYA ANUGERAH MANDIRI" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Item PO</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm text-right" name="description" value="1" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Qty Index</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm text-right" name="description" value="1" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Kode material</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="description" value="XXXX-1" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-2">Nama material</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-sm" name="description" value="SEPEDA MOTOR 150 HONDA VERZA" id="description" autocomplete="off" readonly>
                            </div>
                        </div>
                        <hr>
                        <h4>Rincian Informasi Asset</h4>
                        <div class="form-group">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#panel-initial" data-toggle="tab" class="panel-initial">Page 1</a></li>
                                    <li><a href="#panel-basic-data" class="panel-basic-data" data-toggle="tab">Page 2</a></li>
                                </ul>
                                <div class="tab-content" style="border: 1px solid #e0dcdc;border-top:none">
                                    <!-- Font Awesome Icons -->
                                    <div class="tab-pane active" id="panel-initial">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Company</label>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control input-sm" name="description" value="" id="description" autocomplete="off" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Asset</label>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <input type="email" class="form-control input-sm" placeholder="" readonly>
                                                        <span class="input-group-addon btn btn-sm btn-danger"><i class="fa fa-files-o"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Jenis asset</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="description" value="" id="description" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Group</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="description" value="" id="description" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="plant" class="col-md-2 text-right">Sub Group</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control input-sm" name="description" value="" id="description" autocomplete="off">
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
                                                    <input type="text" class="form-control input-sm attr-material-group" name="description" id="description" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-part-no">
                                                <label for="part_no" class="col-md-2 col-md-offset-1">Merk</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="part_no" id="part_no" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Spesifikasi / Warna</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Seri / No Rangka</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Mesin / IMEI</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">No Polisi</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Lokasi Asset</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Tahun Asset</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Kondisi Asset</label>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
                                                                Baik
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
                                                                Butuh Perbaikan
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label>
                                                                <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
                                                                Tidak baik
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Informasi</label>
                                                <div class="col-md-8">
                                                    <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification" id="specification"></textarea>
                                                </div>
                                            </div>
                                            <h4>Penanggung jawab Aset:</h4>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Nama</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                            <div class="form-group material-group-input" id="input-specification">
                                                <label for="part_no" class="col-md-2 col-md-offset-1 col-form-label">Jabatan</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-sm attr-material-group" name="specification" id="specification">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /#fa-icons -->

                                    <!-- glyphicons-->
                                    <div class="tab-pane" id="panel-basic-data">
                                        page 2
                                    </div>

                                    <!-- /#ion-icons -->

                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-flat label-danger" OnClick="saveRequest()" style="margin-right: 5px;">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    var request_item = [];
    var bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    jQuery(document).ready(function() {
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
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150]
                ],
                "pageLength": 10,
                ajax: "{!! route('get.request_grid') !!}",
                columns: [{
                        "render": function(data, type, row) {
                            return '<a href="javascript:;" style="font-weight:bold" OnClick="Request(' + row.request_no + ')">' + row.request_no + '</a>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var date = new Date(row.request_date);
                            var month = date.getMonth();
                            return date.getDate() + ' ' + bulan[month] + ' ' + date.getFullYear();
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.controller_asset_code) {
                                var content = row.controller_asset_code;
                            } else {
                                var content = "menunggu persetujuan";
                            }
                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.fams_asset_code) {
                                var content = row.fams_asset_code;
                            } else {
                                var content = "menunggu persetujuan";
                            }
                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.verification == 1) {
                                var content = "BA";
                            } else {
                                var content = "Non BA";
                            }
                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var content = '<button class="btn btn-flat btn-flat btn-xs label-danger btn-action btn-edit " title="edit" onClick="edit(' + row.id + ')"><i class="fa fa-pencil"></i></button>';
                            content += '<button class="btn btn-flat btn-flat btn-xs btn-danger btn-action btn-activated" title="Convert document pengajuan" style="margin-left:5px" onClick="printPdf(' + row.id + ')"><i class="fa fa-file-pdf-o"></i></button>';
                            content += '<button class="btn btn-flat btn-flat btn-xs btn-danger btn-action btn-activated" title="cancel" style="margin-left:5px" onClick="cancel(' + row.id + ')"><i class="fa fa-trash"></i></button>';
                            return content;
                        }
                    }
                ],
                columnDefs: [{
                        targets: 0,
                        width: '15%'
                    },
                    {
                        targets: [5],
                        className: 'text-center',
                        orderable: false,
                        width: '12%'
                    },
                ]
            }
        });

        jQuery("input[name='request_date']").datepicker({
            format: "dd/mm/yyyy",
            autoclose: true
        });

        jQuery("#request_date").datepicker({
            format: "dd/mm/yyyy",
            autoclose: true
        });

        jQuery("input[name='verification']").select2({
            data: [{
                    id: '0',
                    text: 'non BA'
                },
                {
                    id: '1',
                    text: 'BA'
                },
            ],
            width: "100%",
            allowClear: true,
            placeholder: ' '
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

        jQuery('.btn-add').on('click', function() {
            request_item = [];
            document.getElementById("request-form").reset();
            jQuery("#area_code").val('');
            jQuery("#area_code").trigger('change');
            jQuery('#username').prop("readonly", false);
            jQuery("#edit_id").val("");

            createItemRequestTable();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i>  Penambahan Asset melalui PO");
            jQuery("#add-data-modal").modal("show");
        });

        jQuery('.btn-add-po').on('click', function() {
            request_item = [];
            document.getElementById("request-form").reset();

            createItemRequestTable();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i>  Penambahan Asset melalui PO");
            jQuery("#add-data-modal").modal("show");
        });

        jQuery('.btn-sewa-amp').on('click', function() {
            request_item = [];
            document.getElementById("request-form").reset();

            createItemRequestTable();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i>  Penambahan Asset melalui sewa AMP");
            jQuery("#add-data-modal").modal("show");
        });

        jQuery('.btn-mutasi-amp').on('click', function() {
            request_item = [];
            document.getElementById("request-form").reset();

            createItemRequestTable();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i>  Penambahan Asset melalui Mutasi AMP");
            jQuery("#add-data-modal").modal("show");
        });

        jQuery("#request-form").on("submit", function(e) {
            e.preventDefault();
            request_item.push({
                item_po: 1,
                code: "XXXX",
                name: "SEPEDA MOTOR 150 HONDA VERZA",
                qty: 1,
                request_qty: 1,
                outstanding_qty: 2,
            });
            createItemRequestTable();
        })

        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            jQuery("#add-data-modal").modal("show");
        });

        jQuery('#data-form').on('submit', function(e) {
            e.preventDefault();
            var param = jQuery(this).serialize();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('users/post') }}",
                method: "POST",
                data: param,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) {
                    if (result.status) {
                        jQuery("#add-data-modal").modal("hide");
                        jQuery("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
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
    });

    function Request(id) {
        document.getElementById("request-form").reset();
        request_item = [];
        jQuery("#edit_id").val(id);

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });
        createItemRequestTable();
        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i>  Penambahan Asset melalui PO - <span style='color:#dd4b39'>" + id + "</span>");
        jQuery("#add-data-modal").modal("show");
    }

    function edit(id) {
        document.getElementById("request-form").reset();
        request_item = [];
        jQuery("#edit_id").val(id);

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });

        request_item.push({
            item_po: 1,
            code: "XXXX",
            name: "SEPEDA MOTOR 150 HONDA VERZA",
            qty: 1,
            request_qty: 1,
            outstanding_qty: 2,
        });
        createItemRequestTable();
        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data");
        jQuery("#add-data-modal").modal("show");
    }

    function codeAsset(id) {
        jQuery("#code-asset-modal .modal-title").html("<i class='fa fa-edit'></i> Pengajuan kode asset - <span style='color:#dd4b39'>" + id + "</span>");
        jQuery("#code-asset-modal").modal({
            backdrop: 'static',
            keyboard: false
        })
        jQuery("#code-asset-modal").modal("show");
    }

    function saveRequest() {
        /*  notify({
             type: 'error',
             message: 'Penambahan Asset melalui PO berhasil disimpan'
         }); */
        jQuery("#add-data-modal").modal('hide');


        $('#add-data-modal').on('hidden.bs.modal', function() {
            jQuery("#code-asset-modal .modal-title").html("<i class='fa fa-edit'></i> Pengajuan kode asset - <span style='color:#dd4b39'>294039049309403943</span>");
            jQuery("#code-asset-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#code-asset-modal").modal("show");
        })

    }

    function cancel(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('users/inactive') }}",
            method: "POST",
            data: {
                id: id
            },
            beforeSend: function() {
                jQuery('.loading-event').fadeIn();
            },
            success: function(result) {
                if (result.status) {
                    jQuery("#data-table").DataTable().ajax.reload();
                    notify({
                        type: 'success',
                        message: result.message
                    });
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

        if (request_item.length > 0) {
            jQuery.each(request_item, function(key, val) {
                item += "<tr>";
                item += "<td>" + val.item_po + "</td>";
                item += "<td>" + val.code + "-" + key + "</td>";
                item += "<td>" + val.name + " " + key + "</td>";
                item += "<td style='text-align:right'>" + val.qty + "</td>";
                item += '<td class="text-center">';
                item += '<div class="input-group">';
                item += ' <div style="cursor:pointer" class="input-group-addon bg-gray"  OnClick="min(\'qty_' + key + '\');">-</div>';
                item += '<input type="text" class="form-control input-sm text-center" value=' + val.request_qty + ' id="qty_' + key + '" maxlength="6">';
                item += ' <div style="cursor:pointer" class="input-group-addon bg-gray" OnClick="plus(\'qty_' + key + '\');">+</div>';
                item += '</td>';
                item += "<td style='text-align:right'>" + val.outstanding_qty + "</td>";
                item += '<td width="30px" style="text-align:center"><button type="button" class="btn btn-flat btn-xs btn-danger" onClick="remove(\'' + key + '\')"><i class="fa fa-trash"></i></button></td>';
                item += "</tr>";
            });
        } else {
            item += '<tr>';
            item += ' <td colspan="7" style="text-align:center">No item selected</td>';
            item += '</tr>';
        }
        item += "</table>";
        jQuery("#request-item-table").html(item);
    }

    function remove(obj) {
        var selected = request_item[obj];
        var conf = confirm("Are you sure you want to delete this data?");
        if (conf == true) {
            /*  if (selected.id) {
                 request_item[obj] = {
                     "id": selected.id,
                     "item": selected.item,
                     "name": selected.name,
                     "price": selected.price,
                     "qty": selected.qty,
                     "deleted": 1
                 }
             } else {
                 var index = product_selected.indexOf(product_selected[obj]);
                 if (index > -1) {
                     product_selected.splice(obj, 1);
                 }
             } */

            var index = request_item.indexOf(request_item[obj]);
            if (index > -1) {
                request_item.splice(obj, 1);
            }

            createItemRequestTable();
        }
    }

    function min(param) {
        if (jQuery("#" + param).val() > 1) {
            if (jQuery("#" + param).val() > 1) jQuery("#" + param).val(+jQuery("#" + param).val() - 1);
        }
    }

    function plus(param) {
        jQuery("#" + param).val(+jQuery("#" + param).val() + 1)
    }

    function printPdf(id) {
        jQuery('#pdf-modal .modal-title').text('Request Doc');
        jQuery('#pdf-modal .modal-body').html('<iframe id="print" style="width:100%;height:500px;" frameborder="0" src="{{ url("requestpdf") }}">');
        jQuery('#pdf-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        jQuery('#print-modal').modal('show');
    }
</script>
@stop