@extends('adminlte::page')
@section('title', 'FAMS - Workflow')
@section('content')
<style>
td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 400px;
}
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
            
    <div class="callout callout-primary">
        <h4>JOBS</h4>
        <p><span id="workflow-code-detail"></span> </p>
    </div>
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span></span>
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <a href={{url('/run-jobs')}}><button class="btn btn-sm btn-flat btn-info"><i class="glyphicon glyphicon-repeat" title="Re-run jobs"></i></button></a>
                        <!-- @if($data['access']->create == 1)
                        <button class="btn btn-sm btn-flat btn-danger btn-add"><i class="glyphicon glyphicon-plus" title="Add new data"></i></button>
                        @endif -->
                    </div>
                    <table id="data-table" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>id</th>
                                <th>queue</th>
                                <th>payload</th>
                                <th>attempts</th>
                                <th>reserved_at</th>
                                <th>available_at</th>
                                <th>created_at</th>
                            </tr>
                            <tr role="row" class="filter">
                                <!-- <th></th> -->
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="queue" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="payload" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="attempts" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="reserved_at" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="available_at" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="created_at" autocomplete="off"></th>
                                <!-- <th></th> -->
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

            

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

@stop
@section('js')
<script>
    var attribute = [];
    jQuery(document).ready(function() 
    {
        // $("#row-detail").hide();
        // $("#row-detail-job").hide();

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
                    url: "{!! route('get.grid_jobs') !!}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'queue',
                        name: 'queue'
                    },
                    {
                        data: 'payload',
                        name: 'payload'
                    },
                    {
                        data: 'attempts',
                        name: 'attempts'
                    },
                    {
                        data: 'reserved_at',
                        name: 'reserved_at'
                    },
                    {
                        data: 'available_at',
                        name: 'available_at'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    // {
                    //     "render": function(data, type, row) {
                    //         var update = "{{ $data['access']->update }}";
                    //         var remove = "{{ $data['access']->delete }}";
                    //         var content = '';

                    //         if (update == 1) 
                    //         {
                    //             content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.workflow_code + '" onClick="edit(' + row.workflow_code + ')"><i class="fa fa-pencil"></i></button>';
                    //             content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view" title="detail data ' + row.workflow_code + '" onClick="detail(\''+row.workflow_code+'\',\''+row.workflow_name+'\')"><i class="fa fa-clone"></i></button>';
                    //         }

                    //         return content;
                    //     }
                    // }
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
    });

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


</script>
@stop