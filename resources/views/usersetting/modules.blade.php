@extends('adminlte::page')
@section('title', 'FAMS - Module')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container">
                    <div class="table-scroll">
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
                                    <th width="10%">Icon</th>
                                    <th width="10%">Sort</th>
                                    <th width="20%">Name</th>
                                    <th>Desc</th>
                                    <th width="10%">Active</th>
                                    <th width="8%">Action</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <th></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="sort" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="name" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="desc" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="status" autocomplete="off"></th>
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
                        <div class="col-xs-3">
                            <label class="control-label" for="name">Sort</label>
                            <input class="form-control" name='sort' id="sort" maxlength="2" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Nama</label>
                            <input class="form-control" name='name' id="name" maxlength="200" requried>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Description</label>
                            <textarea class="form-control" name='description' id="description"></textarea>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label" for="name">Icon</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="" id="font-awesome-result"></i></span>
                                <input type="text" class="form-control" placeholder="use fontawesome" name="icon" id="icon">
                            </div>
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
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150]
                ],
                "pageLength": 10,
                "ajax": {
                    url: "{!! route('get.grid_modules') !!}"
                },
                columns: [{
                        "render": function(data, type, row) {
                            return '<i  class="' + row.icon + '"></i>';
                        }
                    },
                    {
                        data: 'sort',
                        name: 'sort'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.deleted == 0) {
                                var content = '<span class="badge bg-red">Y</span>';
                            } else {
                                var content = '<span class="badge bg-grey">N</span>';
                            }
                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var update = "{{ $data['access']->update }}";
                            var remove = "{{ $data['access']->delete }}";
                            var content = '';
                            if (update == 1) {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.id + '" onClick="edit(' + row.id + ')"><i class="fa fa-pencil"></i></button>';
                            }
                            if (remove == 1) {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-activated  {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 0 ? '' : 'hide') + '" style="margin-left:5px"  onClick="inactive(' + row.id + ')"><i class="fa fa-trash"></i></button>';
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-inactivated {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 1 ? '' : 'hide') + '" style="margin-left:5px"  onClick="active(' + row.id + ')"><i class="fa fa-check"></i></button>';
                            }

                            return content;
                        }
                    }
                ],
                columnDefs: [{
                        targets: [5],
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        targets: [0],
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        targets: [1],
                        className: 'text-center',
                        width: '6%'
                    },
                    {
                        targets: [4],
                        width: '6%',
                        className: 'text-center'
                    }
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
        })

        jQuery("#icon").on('keyup', function() {
            jQuery("#font-awesome-result").removeClass();
            jQuery("#font-awesome-result").addClass(jQuery(this).val());
        });

        jQuery('.btn-add').on('click', function() {
            document.getElementById("data-form").reset();
            jQuery('#role_id').prop('disabled', false);
            jQuery("#edit_id").val("");
            jQuery("#font-awesome-result").removeClass();
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");
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

        jQuery('#data-form').on('submit', function(e) {
            e.preventDefault();
            var param = jQuery(this).serialize();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('modules/post') }}",
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


        })
    });

    function edit(id) {
        document.getElementById("data-form").reset();
        jQuery("#edit_id").val(id);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('modules/edit/?id=') }}" + id)));
        jQuery("#edit_id").val(result.id);
        jQuery("#name").val(result.name);
        jQuery("#sort").val(result.sort);
        jQuery("#icon").val(result.icon);
        jQuery("#font-awesome-result").removeClass();
        jQuery("#font-awesome-result").addClass(result.icon);
        jQuery("#description").val(result.description);
        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data " + result.name);
        jQuery("#add-data-modal").modal("show");
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