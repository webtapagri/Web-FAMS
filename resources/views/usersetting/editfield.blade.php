@extends('adminlte::page')
@section('title', 'FAMS - Editable field')
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
                            <button class="btn btn-sm btn-flat btn-danger btn-save" OnClick="save()" title="save"><i class="fa fa-save"></i></button>
                            @endif
                        </div>
                        <table id="data-table" class="table table-condensed" width="100%">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="30%">Role</th>
                                    <th>Table Name</th>
                                    <th>Field Name</th>
                                    <th>Editable</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td><input type="text" class="form-control input-sm form-filter" name="role"> </td>
                                    <td><input type="text" class="form-control input-sm form-filter" name="tablename"> </td>
                                    <td><input type="text" class="form-control input-sm form-filter" name="fieldname"> </td>
                                    <td><input type="text" class="form-control input-sm form-filter" name="editable"> </td>
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
                            <label class="control-label" for="name">Role</label>
                            <select class="form-control select2" name='role_id' id="role_id" requried>
                                <option></option>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Menu</label>
                            <select class="form-control select2" name='menu' id="menu" maxlength="200" requried>
                                <option></option>
                            </select>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Operation</label>
                            <select class="form-control select2" name='operation' id="operation" required>
                                <option></option>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Description</label>
                            <textarea class="form-control" name='description' id="description" requried></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-flat btn-success" style="margin-right: 5px;">Submit</button>
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
                    url: "{!! route('get.editfield_grid') !!}"
                },
                columns: [{
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'tablename',
                        name: 'tablename'
                    },
                    {
                        "render": function(data, type, row) {
                            var content = '<span data-id="' + row.role_id + "-" + row.tablename + "-" + row.fieldname + "-"+ (row.field_id ? row.field_id : '') + '">' + row.fieldname + '</span>';
                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var content = '<center><input type="checkbox" class="checkbox editable"  ' + (row.editable == 1 ? 'checked' : '') + ' OnChange="changeAccess(this)"/></center>';

                            return content;
                        }
                    },
                ],
                columnDefs: [{
                    targets: [3],
                    orderable: false,
                    width: '6%',
                    className: 'text-center'
                }],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": [],
            }
        });

        var role = jQuery.parseJSON(JSON.stringify(dataJson("{!! route('get.select_role') !!}")));
        jQuery('input[name="role"]').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        jQuery('input[name="editable"]').select2({
            data: [{
                    id: '0',
                    text: 'N'
                },
                {
                    id: '1',
                    text: 'Y'
                },
            ],
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var tbl = jQuery.parseJSON(JSON.stringify(dataJson("{!! route('get.select_tablename') !!}")));
        jQuery('input[name="tablename"]').select2({
            data: tbl,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var input = document.getElementsByName('tablename'); 
        var tbl = $('input[name="tablename"]').val();

        var fieldname = jQuery.parseJSON(JSON.stringify(dataJson('{!! route('get.select_fieldname')!!}?tbl='+tbl )));
        jQuery('input[name="fieldname"]').select2({
            data: fieldname,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        // jQuery('.btn-add').on('click', function() {
        //     document.getElementById("data-form").reset();
        //     jQuery('#menu').select2('val', '');
        //     jQuery('#menu').trigger('change');
        //     jQuery('#role_id').select2('val', '');
        //     jQuery('#role_id').trigger('change');

        //     jQuery('#role_id').prop('disabled', false);
        //     jQuery("#edit_id").val("");
        //     jQuery("#add-data-modal").modal({
        //         backdrop: 'static',
        //         keyboard: false
        //     });
        //     jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");
        //     jQuery("#add-data-modal").modal("show");
        // });

        // jQuery('.btn-edit').on('click', function() {
        //     jQuery("#add-data-modal").modal({
        //         backdrop: 'static',
        //         keyboard: false
        //     });
        //     jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
        //     jQuery("#add-data-modal").modal("show");
        // });

        jQuery('#data-form').on('submit', function(e) {
            e.preventDefault();
            var param = jQuery(this).serialize();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('editfield/post') }}",
                method: "POST",
                data: param,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) {
                    if (result.status) {
                        if (result.exist) {
                            notify({
                                type: 'warning',
                                message: result.message
                            });
                        } else {
                            jQuery("#add-data-modal").modal("hide");
                            jQuery("#data-table").DataTable().ajax.reload();
                            notify({
                                type: 'success',
                                message: result.message
                            });
                        }
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
    });

    function changeAccess(param) {
        var row = jQuery(param).closest('tr');
        if (row.find('.editable').is(':checked')) {
            row.find('.grant-access-all').prop('checked', 'checked');
        } else {
            row.find('.grant-access-all').prop('checked', '');
        }
    }

    function checkAll(param) {
        var row = jQuery(param).closest('tr');
        if (jQuery(param).is(':checked')) {
            row.find('.editable').prop('checked', 'checked');
        } else {
            row.find('.editable').prop('checked', '');
        }
    }

    function save() {
        var param = [];
        jQuery('#data-table > tbody  > tr').each(function() {
            var editable = jQuery(this).find('.editable').is(':checked');
            var detail_id = jQuery(this).find('span').data('id');
            console.log(detail_id);
            var id = detail_id.split('-');
            param.push({
                role_id: id[0],
                tablename: id[1],
                fieldname: id[2],
                field_id: (id[3] === null ? '' : id[3]),
                editable: (editable ? 1 : 0)
            });
        });

        jQuery.ajax({
            url: "{{ url('editfield/post') }}",
            type: "POST",
            data: {
                param: param
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
</script>
@stop