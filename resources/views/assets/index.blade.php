@extends('adminlte::page')
@section('title', 'FAMS - asset')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <button class="btn btn-flat btn-sm btn-flat label-danger btn-refresh"><i class="glyphicon glyphicon-refresh" title="Refresh"></i></button>
                        <button class="btn btn-flat btn-sm btn-flat label-danger btn-search"><i class="fa fa-search" title="Search data"></i></button>
                        <button class="btn btn-flat btn-sm btn-flat label-danger btn-download"><i class=" fa fa-file-pdf-o" title="Add new data"></i></button>
                    </div>
                    <table id="data-table" class="table table-bordered table-condensed">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="15px"></th>
                                <th width="40px">#</th>
                                <th width="">Count Reprint</th>
                                <th width="">Kode Aset FAMS</th>
                                <th width="">Kode SAP</th>
                                <th width="">Kode Aset Controller</th>
                                <th width="">BA PT Pemilik</th>
                                <th width="">Nama PT Pemilik</th>
                                <th width="">BA PT Lokasi</th>
                                <!--<th width="">Nama PT Lokasi</th>
                            <th width="">Nama Aset</th>
                            <th width="180px"> Merk</th>
                            <th width="180px"> No Seri / Rangka</th>
                            <th width="180px"> No Mesin / IMEI</th>
                            <th width="180px"> No Polisi</th>
                            <th width="180px"> Lokasi Aset</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>B</th>
                            <th>BP</th>
                            <th>RTLP</th>
                            <th width="180px"> No PO/SPO</th>
                            <th width="180px"> Vendor</th>
                            <th width="180px"> Vendor</th>
                            <th width="180px"> Informasi Tambahan</th>
                            <th width="180px"> Foto Aset</th>
                            <th width="180px"> Foto No Seri</th>
                            <th width="180px"> Foto No Mesin </th>
                            <th width="180px"> Aset Class </th>
                            <th width="180px"> Tahun Perolehan </th>
                            <th width="180px"> Harga Perolehan </th>
                            <th width="180px"> Nilai Buku saat ini </th>
                            <th width="180px"> Use Life </th>
                            <th width="180px"> Cost Center </th>
                            <th width="180px"> Qty </th>
                            <th width="180px"> UoM </th>
                            <th width="180px"> Jenis Aset </th>
                            <th width="180px"> Group Aset </th>
                            <th width="180px"> Subgroup Aset </th>
                            <th width="180px"> MRP </th>
                            <th width="180px"> status aset </th>
                            <th width="180px"> status sewa </th>
                            <th width="180px"> status Disposal </th> -->
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="name"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="email"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="job_code"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="nik"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                                <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                                <!--<th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th>
                            <th><input type="text" class="form-control input-sm form-filter" name="active"></th> -->
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
<div id="search-modal" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search Data</h4>
            </div>
            <form id="data-form" class="form-horizontal">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="plant" class="col-md-4">Kode aset fams</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">kode aset sap</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">kode aset controller</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">nama aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">jenis aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">group aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">subgroup aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">aset class</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">business area pemilik aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">business area lokasi aset</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">Status</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plant" class="col-md-4">No of list</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" name="po_no" id="po_no" value="5013103287">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-danger" style="margin-right: 5px;">Clear</button>
                    <button type="submit" class="btn btn-flat btn-danger" style="margin-right: 5px;">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="print-modal" class="modal fade" role="dialog">
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
@stop
@section('js')
<script>
    var attribute = [];
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
                ajax: "{!! route('get.grid_tr_user') !!}",
                columns: [{
                        defaultContent: '',
                        name: 'username'
                    },
                    {
                        "render": function(data, type, row) {
                            var content = '<button class="btn btn-flat btn-flat btn-xs label-danger" OnClick="print(' + row.id + ')" title="edit data ' + row.mat_group + '" ><i class="fa fa-print"></i></button>';
                            return content;
                        }
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    /*{
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'username',
                        username: 'username'
                    },
                    {
                        data: 'email',
                        username: 'email'
                    },
                    {
                        data: 'job_code',
                        name: 'job_code'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.fl_active == 1) {
                                var content = '<span class="badge bg-red">Y</span>';
                            } else {
                                var content = '<span class="badge bg-grey">N</span>';
                            }
                            return content;
                        }
                    }, */
                ],
                columnDefs: [{
                        className: 'details-control text-center',
                        targets: [0],

                    },
                    {
                        targets: [0],
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        targets: [5],
                        className: 'text-center',
                    },
                ]
            }
        });

        var dt = new $.fn.dataTable.Api('#data-table');
        var detailRows = [];
        $('#data-table tbody').on('click', 'tr td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = dt.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                tr.addClass('details');
                row.child(format(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }
        });

        // On each draw, loop over the `detailRows` array and show any child rows
        dt.on('draw', function() {
            $.each(detailRows, function(i, id) {
                $('#' + id + ' td.details-control').trigger('click');
            });
        });

        jQuery(".btn-search").on("click", function() {
            jQuery('#search-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery('#search-modal').modal("show");
        });

        jQuery(".btn-download").on('click', function() {
            document.location.assign('{{ url("asset_report") }}');
        });
    });


    function format(d) {
        var item = '<div style="margin:10px;width:130vh;overflow:auto"><table class="table table-striped table-condensed table-bordered table-advance" style="font-size:12px;color:grey;width:100%" class="text-right"><thead>';
        item += '<tr>';
        item += '<th width="180px">Nama PT Lokasi</th>';
        item += '<th width="">Nama Aset</th>';
        item += '<th width="180px"> Merk</th>';
        item += '<th width="180px"> No Seri / Rangka</th>';
        item += '<th width="180px"> No Mesin / IMEI</th>';
        item += '<th width="180px"> No Polisi</th>';
        item += '<th width="180px"> Lokasi Aset</th>';
        item += '<th>Nama</th>';
        item += '<th>Jabatan</th>';
        item += '<th>B</th>';
        item += '<th>BP</th>';
        item += '<th>RTLP</th>';
        item += '<th width="180px"> No PO/SPO</th>';
        item += '<th width="180px"> Vendor</th>';
        item += '<th width="180px"> Vendor</th>';
        item += '<th width="180px"> Informasi Tambahan</th>';
        item += '<th width="180px"> Foto Aset</th>';
        item += '<th width="180px"> Foto No Seri</th>';
        item += '<th width="180px"> Foto No Mesin </th>';
        item += '<th width="180px"> Aset Class </th>';
        item += '<th width="180px"> Tahun Perolehan </th>';
        item += '<th width="180px"> Harga Perolehan </th>';
        item += '<th width="180px"> Nilai Buku saat ini </th>';
        item += '<th width="180px"> Use Life </th>';
        item += '<th width="180px"> Cost Center </th>';
        item += '<th width="180px"> Qty </th>';
        item += '<th width="180px"> UoM </th>';
        item += '<th width="180px"> Jenis Aset </th>';
        item += '<th width="180px"> Group Aset </th>';
        item += '<th width="180px"> Subgroup Aset </th>';
        item += '<th width="180px"> MRP </th>';
        item += '<th width="180px"> status aset </th>';
        item += '<th width="180px"> status sewa </th>';
        item += '<th width="180px"> status Disposal </th>';
        item += '</tr>';
        item += '<tbody>';
        var param = d[13];
        var id = jQuery(param).data("id");
        item += '<tr><td colspan="35">No data</td></tr>';
        //var data = jQuery.parseJSON(JSON.stringify(dataJson("pets_view", "parent_id=" + id)));
        /* if (data.length > 0) {
            jQuery.each(data, function(key, val) {
                item += '<tr>';
                item += '<td>' + val.name + '</td>';
                item += '<td>' + val.birth_date + '</td>';
                item += '<td>' + (val.gender == "M" ? "Male" : "Female") + '</td>';
                item += '<td>' + val.pet_type + '</td>';
                item += '<td>' + val.breed + '</td>';
                item += '</tr>';
            });
        } else {
            item += '<tr><td colspan="5"><center>No data</center></td></tr>';
        } */
        item += '</tbody></table><div>';

        return item;
    }

    function print(id) {
        jQuery('#print-modal .modal-title').text('Print asset');
        jQuery('#print-modal .modal-body').html('<iframe id="print" style="width:100%;height:500px;" frameborder="0" src="{{ url("asset_pdf") }}">');
        jQuery('#print-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        jQuery('#print-modal').modal('show');
    }
</script>
@stop