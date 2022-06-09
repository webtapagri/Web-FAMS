<?php //echo "<pre>"; print_r(session()->all()); die(); ?>

@extends('adminlte::page')
@section('title', 'FAMS - Master General Data')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container small">
                    <div class="table-actions-wrapper">
                        <span></span>
                        
						<button class="btn btn-sm btn-flat btn-danger" onClick="download()" title="Export to Excel"><i class="fa fa-file-excel-o"></i></button>
                        
						<button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        @if($data['access']->create == 1)
                        <!--button class="btn btn-sm btn-flat btn-danger btn-add"><i class="glyphicon glyphicon-plus" title="Add new data"></i></button-->
                        @endif
                    </div>
                    <table id="data-table" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>KODE MATERIAL</th>
                                <th>NAMA MATERIAL</th>
                                <th>BA PEMILIK ASSET</th>
                                <th>LOKASI ASSET</th>
                                <th>NAMA ASSET</th>
                                <th>KODE ASSET SAP</th>
                                <th>KODE ASSET AMS</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" class="form-control input-xs form-filter" name="kode_material" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="nama_material" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="ba_pemilik_asset" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="lokasi_ba_description" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="nama_asset" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="kode_asset_sap" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="kode_asset_ams" autocomplete="off"></th>
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
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="general-code">General Code</label>
                            <input class="form-control" name='general_code' id="general_code" maxlength="400" requried>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="description-code">Description Code</label>
                            <input class="form-control" name='description_code' id="description_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="description">Description</label>
                            <input class="form-control" name='description' id="description" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="status">Status</label>
                            <!--input class="form-control" name='status' id="status" requried-->
                            <select class="form-control" id="status" name="status">
                                <option value="t">True</option>
                                <option value="f">False</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-danger" style="margin-right: 5px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop
@section('js')
<script>
    var attribute = [];
    jQuery(document).ready(function() 
    {
        $("#row-detail").hide();

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var grid = new Datatable();
        grid.init({
            src: $("#data-table"),
            onSuccess: function(grid) {},
            onError: function(grid) {},
            onDataLoad: function(grid) {},
            destroy: true,
            loadingMessage: 'Loading...',
            dataTable: {
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150]
                ],
                "pageLength": 10,
                "ajax": {
                    url: "{!! route('get.grid_master_asset') !!}"
                },
                columns: [
                    {
                        data: 'kode_material',
                        name: 'kode_material'
                    },
                    {
                        data: 'nama_material',
                        name: 'nama_material'
                    },
                    {
                        data: 'ba_pemilik_asset',
                        name: 'ba_pemilik_asset'
                    },
                    {
                        data: 'lokasi_ba_description',
                        name: 'lokasi_ba_description'
                    },
                    {
                        data: 'nama_asset',
                        name: 'nama_asset'
                    },
                    {
                        data: 'kode_asset_sap',
                        name: 'kode_asset_sap'
                    },
                    {
                        data: 'kode_asset_ams',
                        name: 'kode_asset_ams'
                    },
                    {
                        "render": function(data, type, row) 
                        {
                            var update = "{{ $data['access']->update }}";
                            var remove = "{{ $data['access']->delete }}";
                            var content = '';
                            var kode_asset_ams = btoa(row.kode_asset_ams);

                            if (update == 1) 
                            {
                                <?php /*
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.id + '" onClick="edit(' + row.id + ')"><i class="fa fa-pencil"></i></button>';
                                */ ?>
                                content += '<a href="{{ url("master-asset/show-data") }}/'+kode_asset_ams+'" target="blank"><button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="view detail '+row.kode_asset_ams+'"><i class="fa fa-arrow-circle-right"></i></button></a>&nbsp;&nbsp;';
                                content += '<a href="{{ url("master-asset/edit-data") }}/'+kode_asset_ams+'" target="_blank"><button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data '+row.kode_asset_ams+'"><i class="fa fa-pencil"></i></button></a>';
                            }

                            return content;
                        }
                    }
                ],
                columnDefs: [
                    /*{
                        targets: [0],
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        targets: [2],
                        className: 'text-center',
                        orderable: false,
                        width: '10%'
                    }*/
                ],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": [],
            }
        });

        jQuery("input[name='status']").select2({
            data: [{
                    id: 0,
                    text: 'Y'
                },
                {
                    id: 1,
                    text: 'N'
                },
            ],
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_menu") !!}')));
        $('input[name="menu_code"], #menu_code').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var role_detail = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_code") !!}')));
        $('input[name="workflow_code"], #workflow_code').select2({
            data: role_detail,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var role_detail_job_code = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_detail_code") !!}')));
        $('input[name="workflow_detail_code"], #workflow_detail_code').select2({
            data: role_detail_job_code,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var role_detail_job_role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_detail_role") !!}')));
        $('input[name="id_role"], #id_role').select2({
            data: role_detail_job_role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        jQuery("#icon").on('keyup', function() {
            jQuery("#font-awesome-result").removeClass();
            jQuery("#font-awesome-result").addClass(jQuery(this).val());
        });

        jQuery('.btn-add').on('click', function() 
        {
            document.getElementById("data-form").reset();
            jQuery('#role_id').prop('disabled', false);
            jQuery("#edit_id").val("");
            jQuery("#font-awesome-result").removeClass();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> CREATE NEW DATA");
            jQuery("#add-data-modal").modal("show");
        });

        <?php /*
        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            jQuery("#add-data-modal").modal("show");
        });
        */ ?>

        jQuery('#data-form').on('submit', function(e) 
        {
            if(confirm('confirm submit data?'))
            {
                e.preventDefault();
                var param = jQuery(this).serialize();
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('general-data/post') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) 
                        {
                            document.getElementById("data-form").reset();
                            jQuery("#add-data-modal").modal("hide");
                            jQuery("#data-table").DataTable().ajax.reload();
                            notify({
                                type: 'success',
                                message: result.message
                            });
                        } 
                        else 
                        {
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
        });

        $('.btn-edit-detail').on('click', function() 
        {
            alert("dialog detail");
            $("#add-data-modal-detail").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#add-data-modal-detail .modal-title").html("<i class='fa fa-pencil'></i> Edit data Detail");
            $("#add-data-modal-detail").modal("show");
        });
        
    });

	function download(){
		console.log(123);
		var f = '<form id="fwaw" method="post" action="{{ route('master_asset.download') }}">';
		f += '@csrf';
		f += '<input type="hidden" name="kode_asset_ams" value="'+$('input[name=kode_asset_ams]').val()+'" id="">';
		f += '<input type="hidden" name="kode_material" value="'+$('input[name=kode_material]').val()+'" id="">';
		f += '<input type="hidden" name="nama_material" value="'+$('input[name=nama_material]').val()+'" id="">';
		f += '<input type="hidden" name="ba_pemilik_asset" value="'+$('input[name=ba_pemilik_asset]').val()+'" id="">';
		f += '<input type="hidden" name="lokasi_ba_description" value="'+$('input[name=lokasi_ba_description]').val()+'" id="">';
		f += '<input type="hidden" name="nama_asset" value="'+$('input[name=nama_asset]').val()+'" id="">';
		f += '<input type="hidden" name="kode_asset_sap" value="'+$('input[name=kode_asset_sap]').val()+'" id="">';
		f += '</form>';		
		
		$('body').append(f);
		$('#fwaw').submit();
		$('#fwaw').remove();
		console.log(f);
		return false;
	}
	
    function edit(id) 
    {
        //alert(id);
        document.getElementById("data-form").reset();
        $("#edit_id").val(id);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('general-data/edit/?id=') }}" + id)));
        $("#edit_id").val(result.ID);
        $("#general_code").val(result.GENERAL_CODE);
        $("#description_code").val(result.DESCRIPTION_CODE);
        $("#description").val(result.DESCRIPTION);
        $("#status").val(result.STATUS);
        $("#status").trigger("status");

        $("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data " + result.description);
        $("#add-data-modal").modal("show");
    }

    function inactive(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('modules/inactive') }}",
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
                        type: 'error',
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

    function active(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('modules/active') }}",
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
                        type: 'error',
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

</script>
@stop