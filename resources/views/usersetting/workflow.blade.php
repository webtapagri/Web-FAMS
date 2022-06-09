@extends('adminlte::page')
@section('title', 'FAMS - Workflow')
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
                                <th>NAME</th>
                                <th>MENU CODE</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="workflow_name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="menu_code" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            
            <div id="row-detail" style="margin-top:10px;display:none">
                <div class="callout callout-info">
                    <h4>DETAIL WORKFLOW</h4>
                    <p><span id="workflow-code-detail"></span> </p>
                </div>
               
                <div class="table-container">
                     <div class="xtable-actions-wrapper pull-right">
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <!-- @if($data['access']->create == 1)-->
                        <button class="btn btn-sm btn-flat btn-danger btn-add-detail"><i class="glyphicon glyphicon-plus" title="Add new data detail"></i></button>
                        <!-- @endif -->
                    </div>
                    <table id="data-table-detail" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>WORKFLOW CODE</th>
                                <th>GROUP NAME</th>
                                <th>SEQUENCE</th>
                                <th>DESCRIPTION</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="workflow_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="workflow_group_name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="seq" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="description" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="row-detail-job" style="margin-top:10px;display:none">
                <div class="callout callout-info">
                    <h4>JOB WORKFLOW</h4>
                    <p><span id="workflow-detail-code"></span> </p>
                </div>
                <div class="table-container">
                     <div class="xtable-actions-wrapper pull-right">
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <!-- @if($data['access']->create == 1)-->
                        <button class="btn btn-sm btn-flat btn-danger btn-add-detail-job"><i class="glyphicon glyphicon-plus" title="Add new data detail job"></i></button>
                        <!-- @endif -->
                    </div>
                    <table id="data-table-detail-job" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>WORKFLOW DETAIL CODE</th>
                                <th>ID ROLE</th>
                                <th>SEQUENCE</th>
                                <th>OPERATION</th>
                                <th>LINTAS</th>
                                <th>NEXT APPROVE</th>
                                <th>LIMIT APPROVE</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="workflow_group_name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="seq" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="operation" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="lintas" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="next_approve" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="limit_approve" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
                            <label class="control-label" for="workflow-name">Workflow Name</label>
                            <input class="form-control" name='workflow_name' id="workflow_name" maxlength="400" requried>
                            <input type="hidden" name='edit_workflow_code' id="edit_workflow_code">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="menu-code">Menu Code</label>
                            <input class="form-control" name='menu_code' id="menu_code" requried>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default tbm-menu" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-danger" style="margin-right: 5px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="add-data-modal-detail" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form-detail">
                <div class="modal-body">
                    <div class="box-body">
                        
                        <input type="hidden" name='edit_workflow_code_detail' id="edit_workflow_code_detail">
                        <input type="hidden" name='workflow_code_hide' id="workflow_code_hide">

                        <div class="col-xs-12">
                            <label class="control-label" for="workflow-code">Workflow Code</label>
                            <input class="form-control" name='workflow_code' id="workflow_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="group-name">Group Name</label>
                            <input class="form-control" name='workflow_group_name' id="workflow_group_name" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="seq">Seq</label>
                            <input class="form-control" name='seq' id="seq" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="description">Description</label>
                            <input class="form-control" name='description' id="description" maxlength="400" requried>
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

<div id="add-data-modal-detail-job" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form-detail-job">
                <div class="modal-body">
                    <div class="box-body">

                        <input type="hidden" name='edit_workflow_code_detail_job' id="edit_workflow_code_detail_job">
                        <input type="hidden" name='workflow_detail_code_hide' id="workflow_detail_code_hide">

                        <div class="col-xs-12">
                            <label class="control-label" for="workflow-detail-code">Workflow Detail Code</label>
                            <input class="form-control" name='workflow_detail_code' id="workflow_detail_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="id-role">ID Role</label>
                            <input class="form-control" name='id_role' id="id_role" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="seq">Seq</label>
                            <input class="form-control" name='seq_job' id="seq_job" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="operation">Operation</label>
                            <input class="form-control" name='operation' id="operation" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="lintas">Lintas</label>
                            <input class="form-control" name='lintas' id="lintas" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Next Approve</label>
                            <input class="form-control" name='next_approve' id="next_approve" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Limit Approve</label>
                            <input class="form-control" name='limit_approve' id="limit_approve" maxlength="400" requried>
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
        $("#row-detail-job").hide();

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
                    url: "{!! route('get.grid_workflow') !!}"
                },
                columns: [
                    {
                        data: 'workflow_code',
                        name: 'workflow_code'
                    },
                    {
                        data: 'workflow_name',
                        name: 'workflow_name'
                    },
                    {
                        data: 'menu_code',
                        name: 'menu_code'
                    },
                    {
                        "render": function(data, type, row) {
                            var update = "{{ $data['access']->update }}";
                            var remove = "{{ $data['access']->delete }}";
                            var content = '';

                            if (update == 1) 
                            {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.workflow_code + '" onClick="edit(' + row.workflow_code + ')"><i class="fa fa-pencil"></i></button>';
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view" title="detail data ' + row.workflow_code + '" onClick="detail(\''+row.workflow_code+'\',\''+row.workflow_name+'\')"><i class="fa fa-clone"></i></button>';
                            }

                            return content;
                        }
                    }
                ],
                columnDefs: [],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": [],
            }
        });

        $("input[name='status']").select2({
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

        var role = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_menu") !!}')));
        $('input[name="menu_code"], #menu_code').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
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
        $('input[name="id_role"], #id_role, #next_approve').select2({
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
            jQuery("#edit_workflow_code").val("");
            jQuery("#font-awesome-result").removeClass();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");
            jQuery("#add-data-modal").modal("show");
        });

        $('.btn-add-detail').on('click', function() 
        {
            document.getElementById("data-form-detail").reset();

            $('#role_id').prop('disabled', false);
            $("#edit_workflow_code_detail").val("");
            $("#font-awesome-result").removeClass();
            $('#workflow_code').val("");
            $('#workflow_code').val($("#workflow_code_hide").val()).trigger('change');

            $("#add-data-modal-detail").modal({
                backdrop: 'static',
                keyboard: false
            });
            
            $("#add-data-modal-detail .modal-title").html("<i class='fa fa-plus'></i> Create new data detail");
            $("#add-data-modal-detail").modal("show");
        });

        $('.btn-add-detail-job').on('click', function() 
        {
            var workflow_detail_code_hide = $("#workflow_detail_code_hide").val();
            //alert(workflow_detail_code_hide);

            $('#workflow_detail_code').val("");
            $('#workflow_detail_code').val(workflow_detail_code_hide).trigger('change');

            document.getElementById("data-form-detail-job").reset();
            
            $('#role_id').prop('disabled', false);
            $("#edit_workflow_code_detail-job").val("");
            $("#font-awesome-result").removeClass();

            $("#add-data-modal-detail-job").modal({
                backdrop: 'static',
                keyboard: false
            });
            
            $("#add-data-modal-detail-job .modal-title").html("<i class='fa fa-plus'></i> Create new data detail job");
            $("#add-data-modal-detail-job").modal("show");
        });

        $('.btn-edit').on('click', function() 
        {
            $("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            
            $("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            $("#add-data-modal").modal("show");
        });

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
                    url: "{{ url('workflow/post') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            $("#add-data-modal").modal("hide");
                            $("#data-table").DataTable().ajax.reload();
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
        });

        $('.btn-edit-detail').on('click', function() 
        {
            //alert("dialog detail");
            $("#add-data-modal-detail").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#add-data-modal-detail .modal-title").html("<i class='fa fa-pencil'></i> Edit data Detail");
            $("#add-data-modal-detail").modal("show");
        });

        $('#data-form-detail').on('submit', function(e) 
        {
            if(confirm('confirm submit data detail ?'))
            {
                //alert("submit data detail cuk");

                e.preventDefault();
                var param = jQuery(this).serialize();
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('workflow/post-detail') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            jQuery("#add-data-modal-detail").modal("hide");
                            jQuery("#data-table-detail").DataTable().ajax.reload();
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
        });

        $('#data-form-detail-job').on('submit', function(e) 
        {
            if(confirm('confirm submit data detail job ?'))
            {
                //alert("submit data detail cuk");

                e.preventDefault();
                var param = jQuery(this).serialize();
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: "{{ url('workflow/post-detail-job') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            jQuery("#add-data-modal-detail-job").modal("hide");
                            jQuery("#data-table-detail-job").DataTable().ajax.reload();
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
        });

        $('.tbm-menu').on('click', function() 
        {
            //alert("Clear data"); 
            //$("#menu_code").select2("val", "");
            //$("#menu_code").val("");
            //$("#menu_code").val("").trigger("change");
        });

    });

    function edit(id) 
    {
        document.getElementById("data-form").reset();
        
        $("#edit_workflow_code").val(id);
        var result = $.parseJSON(JSON.stringify(dataJson("{{ url('workflow/edit/?workflow_code=') }}" + id)));
        
        $("#edit_workflow_code").val(result.workflow_code);
        $("#workflow_name").val(result.workflow_name);
        
        $("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data " + result.workflow_name);
        $("#add-data-modal").modal("show");

        var role = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_menu") !!}')));
        $('input[name="menu_code"], #menu_code').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
        });
        $('#menu_code').val(result.menu_code).trigger('change');
    }

    function inactive(id) 
    {
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

    function detail(id,name)
    {
        //alert(name);
        $("#data-table-detail").DataTable().destroy()

        $("#row-detail-job").fadeOut();
        $("#row-detail").fadeOut();
        $("#workflow-code-detail").html(' <b>'+id+' - '+name.toUpperCase()+'</b>');
        $("#workflow-code-name").html('('+name+')');
        $("#workflow_code_hide").val(id);
        $("#row-detail").fadeIn();

        //if ( ! $.fn.DataTable.isDataTable( '#data-table-detail' ) ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var grid_detail = new Datatable();
            grid_detail.init({
                src: $("#data-table-detail"),
                onSuccess: function(grid_detail) {},
                onError: function(grid_detail) {},
                onDataLoad: function(grid_detail) {},
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
                        url: '{{ url("grid-workflow-detail/") }}'+'/'+id
                    },
                    columns: [
                        {
                            data: 'workflow_detail_code',
                            name: 'workflow_detail_code'
                        },
                        {
                            data: 'workflow_name',
                            name: 'workflow_name'
                        },
                        {
                            data: 'workflow_group_name',
                            name: 'workflow_group_name'
                        },
                        {
                            data: 'seq',
                            name: 'seq'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            "render": function(data, type, row) {
                                var update = "{{ $data['access']->update }}";
                                var remove = "{{ $data['access']->delete }}";
                                var content = '';
                                if (update == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit-detail" title="edit data detail : ' + row.workflow_code + '" onClick="edit_detail(' + row.workflow_detail_code + ')"><i class="fa fa-pencil"></i></button>';
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view-detail" title="detail data ' + row.workflow_code + '" onClick="detail_job(\''+row.workflow_detail_code+'\',\''+row.workflow_name+'\',\''+row.workflow_group_name+'\')"><i class="fa fa-clone"></i></button>';
                                }

                                return content;
                            }
                        }
                    ],
                    columnDefs: [],
                    oLanguage: {
                        sProcessing: "<div id='datatable-loader'></div>",
                        sEmptyTable: "Data tidak di temukan",
                        sLoadingRecords: ""
                    },
                    "order": [],
                }
            });
        //}
    }

    function edit_detail(id) 
    {
        //alert(id); return false;
        document.getElementById("data-form-detail").reset();
        $("#edit_workflow_code_detail").val(id);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('workflow/edit-detail/?workflow_detail_code=') }}" + id)));
        $("#edit_workflow_code_detail").val(result.workflow_detail_code);
        $("#workflow_group_name").val(result.workflow_group_name);
        
        var role_detail = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_code") !!}')));
        $('input[name="workflow_code"], #workflow_code').select2({
            data: role_detail,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });
        $("#workflow_code").val(result.workflow_code);
        $("#workflow_code").trigger("change");
        
        $("#seq").val(result.seq);
        $("#description").val(result.description);

        $("#add-data-modal-detail .modal-title").html("<i class='fa fa-edit'></i> Update data detail "+result.workflow_detail_code+" : " + result.workflow_group_name);
        $("#add-data-modal-detail").modal("show");
    }

    function detail_job(id,name,group_name)
    {
        //alert(id); //   return false;
        //alert(name);
        $("#data-table-detail-job").DataTable().destroy()

        //alert(id);
        $("#row-detail-job").fadeOut();
        $("#workflow-detail-code").html(" <b>"+id+" - "+name.toUpperCase()+" | "+group_name.toUpperCase()+"</b>");
        //$("#workflow-code-name").html('('+name+')');
        $("#row-detail-job").fadeIn();
        $("#workflow_detail_code_hide").val("");
        $("#workflow_detail_code_hide").val(id);

        //if ( ! $.fn.DataTable.isDataTable( '#data-table-detail' ) ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var grid_detail = new Datatable();
            grid_detail.init({
                src: $("#data-table-detail-job"),
                onSuccess: function(grid_detail) {},
                onError: function(grid_detail) {},
                onDataLoad: function(grid_detail) {},
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
                        url: '{{ url("grid-workflow-detail-job/") }}'+'/'+id
                    },
                    columns: [
                        {
                            data: 'workflow_job_code',
                            name: 'workflow_job_code'
                        },
                        {
                            data: 'workflow_group_name',
                            name: 'workflow_group_name'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'seq',
                            name: 'seq'
                        },
                        {
                            data: 'operation',
                            name: 'operation'
                        },
                        {
                            data: 'lintas',
                            name: 'lintas'
                        },
                        {
                            data: 'next_approve',
                            name: 'next_approve'
                        },
                        {
                            data: 'limit_approve',
                            name: 'limit_approve'
                        },
                        {
                            "render": function(data, type, row) {
                                var update = "{{ $data['access']->update }}";
                                var remove = "{{ $data['access']->delete }}";
                                var content = '';
                                if (update == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit-detail-job" title="edit data detail job : ' + row.workflow_job_code + '" onClick="edit_detail_job(' + row.workflow_job_code + ')"><i class="fa fa-pencil"></i></button>';
                                }

                                return content;
                            }
                        }
                    ],
                    columnDefs: [
                    ],
                    oLanguage: {
                        sProcessing: "<div id='datatable-loader'></div>",
                        sEmptyTable: "Data tidak di temukan",
                        sLoadingRecords: ""
                    },
                    "order": [],
                }
            });
        //}
    }

    function edit_detail_job(id) 
    {
        //alert(id); return false;
        document.getElementById("data-form-detail-job").reset();
        $("#edit_workflow_code_detail_job").val(id);
        
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('workflow/edit-detail-job/?workflow_job_code=') }}" + id)));
        
        $("#edit_workflow_code_detail_job").val(result.workflow_job_code);

        var role_detail_job_code = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_detail_code") !!}')));
        $('input[name="workflow_detail_code"], #workflow_detail_code').select2({
            data: role_detail_job_code,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });
        $("#workflow_detail_code").val(result.workflow_detail_code);
        $("#workflow_detail_code").trigger("change");
        
        var role_detail_job_role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_workflow_detail_role") !!}')));
        $('input[name="id_role"], #id_role, #next_approve').select2({
            data: role_detail_job_role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });
        $("#id_role").val("").trigger("change");
        $("#id_role").val(result.id_role).trigger("change");
        
        $("#seq_job").val(result.seq);
        $("#operation").val(result.operation);
        $("#lintas").val(result.lintas);
        $("#next_approve").val(result.next_approve);
        $("#next_approve").trigger("change");
        $("#limit_approve").val(result.limit_approve);

        $("#add-data-modal-detail-job .modal-title").html("<i class='fa fa-edit'></i> Update data detail job "+result.workflow_job_code);
        $("#add-data-modal-detail-job").modal("show");
    }

</script>
@stop