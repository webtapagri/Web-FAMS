@extends('adminlte::page')
@section('title', 'FAMS - ROLE MAP')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        @if($data['access']->create == 1)
                        <button class="btn btn-sm btn-flat btn-danger btn-add"><i class="glyphicon glyphicon-plus" title="Add new data"></i></button>
                        @endif
                    </div>
                    <table id="data-table" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>CODE ROLE</th>
                                <th>ID ROLE</th>
                                <th>ROLE NAME</th>
                                <th>DESCRIPTION CODE</th>
                                <th>DESCRIPTION</th>
                                <th>ID USER</th>
                                <th>USERNAME</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" class="form-control input-xs form-filter" name="id" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="code_role_x_general_data" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="id_role" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="role_name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="description_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="description" autocomplete="off"></th><th><input type="text" class="form-control input-xs form-filter" name="id_user" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="username" autocomplete="off"></th>
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
                            <label class="control-label" for="">Role Code</label>
                            <input class="form-control" name='code_role_x_general_data' id="code_role_x_general_data" maxlength="400" readonly>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Role</label>
                            <input class="form-control" name='id_role' id="id_role" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Description</label>
                            <input class="form-control" name='description_code' id="description_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">User</label>
                            <input class="form-control" name='id_user' id="id_user" requried>
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
    $(document).ready(function() 
    {
        $("#row-detail").hide();

        $.ajaxSetup({
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
                    url: "{!! route('get.grid_role_map') !!}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        width: '30px',
                    },
                    {
                        data: 'code_role_x_general_data',
                        name: 'code_role_x_general_data'
                    },
                    {
                        data: 'id_role',
                        name: 'id_role'
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'description_code',
                        name: 'description_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'id_user',
                        name: 'id_user'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        "render": function(data, type, row) {
                            var update = "{{ $data['access']->update }}";
                            var remove = "{{ $data['access']->delete }}";
                            var content = '';

                            if (update == 1) 
                            {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data '+row.id + '" onClick="edit('+row.id+')"><i class="fa fa-pencil"></i></button>';
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

        var role_id = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_role_idname") !!}')));
        $('input[name="id_role"], #id_role').select2({
            data: role_id,
            width: '120px',
            placeholder: ' ',
            allowClear: true
        });

        var role_description = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.generaldata_assetcontroller") !!}')));
        $('input[name="description_code"], #description_code').select2({
            data: role_description,
            width: '120px',
            placeholder: ' ',
            allowClear: true
        });

        var role_user = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_user") !!}')));
        $('input[name="id_user"], #id_user').select2({
            data: role_user,
            width: '140px',
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

        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            jQuery("#add-data-modal").modal("show");
        });

        $('#data-form').on('submit', function(e) 
        {
            if(confirm('confirm submit data?'))
            {
                e.preventDefault();
                var param = $(this).serialize();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ url('role-map/post') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        $('.loading-event').fadeIn();
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

    function edit(id) 
    {
        //alert(id);
        document.getElementById("data-form").reset();
        $("#edit_id").val(id);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('role-map/edit/?id=') }}" + id)));
        $("#edit_id").val(result.ID);
        $("#code_role_x_general_data").val(result.CODE_ROLE_X_GENERAL_DATA);
        $("#id_role").val(result.ID_ROLE+'__'+result.ROLE_NAME);
        $("#id_role").trigger("change");
        $("#role_name").val(result.ROLE_NAME);
        $("#description_code").val(result.DESCRIPTION_CODE+'__'+result.DESCRIPTION);
        $("#description_code").trigger("change");
        $("#description").val(result.DESCRIPTION);
        $("#id_user").val(result.ID_USER+'__'+result.USERNAME);
        $("#id_user").trigger("change");

        $("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update Data " + result.description);
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