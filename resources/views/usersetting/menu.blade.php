@extends('adminlte::page')
@section('title', 'FAMS - Menu')
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
                                <th width="8%">Sort</th>
                                <th width="15%">Module</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Url</th>
                                <th>Active</th>
                                <th width="8%">Action</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" class="form-control input-xs form-filter" name="sort" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="module" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="menu_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="name" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="url" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="status" autocomplete="off"></th>
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
                            <label class="control-label" for="module">Module</label>
                            <input class="form-control" name='module' id="module" requried>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Nama</label>
                            <input class="form-control" name='name' id="name" maxlength="200" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="menu_code">Menu Code</label>
                            <input class="form-control" name='menu_code' id="menu_code" maxlength="200" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="url">Url</label>
                            <input class="form-control" name='url' id="url" placeholder="put the url from routes (e.g: menu for {{ url('/') }}/menu)" maxlength="200" requried>
                        </div>
                        <div class="col-xs-4">
                            <label class="control-label" for="sorting">Sorting</label>
                            <input class="form-control" name='sorting' id="sorting" maxlength="4" onkeypress="return isNumber(event)" onpaste="return false" ondrop="return false">
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

        var role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.outstandingdetail") !!}')));

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
                    url: "{!! route('get.menu_grid') !!}"
                },
                columns: [{
                        data: 'sort',
                        name: 'sort'
                    },
                    {
                        data: 'module_name',
                        name: 'module_name'
                    },
                    {
                        data: 'menu_code',
                        name: 'menu_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'url',
                        name: 'url'
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

        var role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_module") !!}')));
        jQuery('input[name="module"], #module').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        jQuery('.btn-add').on('click', function() {
            jQuery("#module").select2("val", "");
            jQuery("#module").val("");
            jQuery("#module").trigger("change");

            document.getElementById("data-form").reset();
            jQuery("#edit_id").val("");
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");
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
                url: "{{ url('menu/post') }}",
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

    function edit(id) 
    {
        document.getElementById("data-form").reset();
        $("#edit_id").val(id);
        var result = $.parseJSON(JSON.stringify(dataJson("{{ url('menu/edit/?id=') }}" + id)));

        $("#name").val(result.name);
        $("#url").val(result.url);
        $("#sorting").val(result.sort);
        $("#module").val(result.module_id);
        $("#menu_code").val(result.menu_code);
        $("#module").trigger("change");
        $("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data " + result.name);
        $("#add-data-modal").modal("show");
    }

    function inactive(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('menu/inactive') }}",
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
            url: "{{ url('menu/active') }}",
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

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
@stop