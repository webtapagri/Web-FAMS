@extends('adminlte::page')
@section('title', 'FAMS - Users')
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
                                    <th>img</th>
                                    <th>Username</th>
                                    <th width="13%">Role</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Job Code</th>
                                    <th>NIK</th>
                                    <th>Area Code</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <th></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="username" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="role" id="flt_role" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="name" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="email" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="job_code" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="nik" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="area_code" autocomplete="off"></th>
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
            <form id="data-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Role</label>
                            <input type="text" class="form-control" name="role_id" id="role_id" required>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Username</label>
                            <input type="text" class="form-control" name="username" id="username" maxlength="50" required>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Nama</label>
                            <input class="form-control" name='name' id="name" maxlength="200" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Email</label>
                            <input type="email" class="form-control" name='email' id="email" maxlength="250" required>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Job Code</label>
                            <input type="text" class="form-control" name='job_code' id="job_code" maxlength="150">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">NIK</label>
                            <input type="text" class="form-control" name='nik' id="nik" maxlength="80">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="name">Area Code</label>
                            <select class="form-control" name='area_code[]' multiple="multiple" id="area_code">

                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label for="volume_unit">Image</label>
                            <div class="form-group hide">
                                <input type="file" id="files_1" name="files_1" accept='image/*' OnChange="showImage(1)">
                                <p class="help-block">*jpg, png</p>
                            </div>
                            <div class="image-group">
                                <button type="button" class="btn btn-danger btn-xs btn-flat btn-add-file-image btn-remove-image1 hide" OnClick="removeImage(1)"><i class="fa fa-trash"></i></button>
                                <img id="material-images-1" data-status="0" style="cursor:pointer" title="click to change image" OnClick="openFile(1)" class="img-responsive select-img" src="{{URL::asset('img/add-img.png')}}">
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Access-Control-Allow-Methods': 'GET, POST',
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
                    url: "{!! route('get.users') !!}"
                },
                columns: [{
                        "render": function(data, type, row) {
                            if (row.img) {
                                var content = '<img src="' + row.img + '" class="img-circle img-responsive">';
                            } else {
                                var content = '<img src="{{ asset("img/user-default.png") }}" class="img-circle img-responsive">';
                            }

                            return content;
                        }
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'job_code',
                        name: 'job_code'
                    },
                    {
                        data: 'NIK',
                        name: 'nik'
                    },
                    {
                        "render": function(data, type, row) {
                            var area_code = row.area_code;
                            return area_code.replace(/,/g, ', ');
                        }
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
                        targets: [0],
                        width: '2%',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        targets: 1,
                        width: '15%'
                    },
                    {
                        targets: [8],
                        className: 'text-center',
                        width: '6%'
                    },
                    {
                        targets: [9],
                        className: 'text-center',
                        orderable: false,
                        width: '8%'
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

        var role = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_role") !!}')));
        jQuery('input[name="role"], #role_id').select2({
            data: role,
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        var busines_area = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.generaldataplant") !!}')));
        jQuery("#area_code").select2({
            data: busines_area,
            width: '100%',
            placeholder: ' ',
            allowClear: true
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

        jQuery('.btn-add').on('click', function() {
            document.getElementById("data-form").reset();
            jQuery("#area_code").val('');
            jQuery("#area_code").trigger('change');
            jQuery('#username').prop("readonly", false);
            jQuery("#edit_id").val("");

            jQuery('#material-images-1').prop('src', "{{URL::asset('img/add-img.png')}}");

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
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var param = new FormData(this);
            jQuery.ajax({
                url: "{{ url('users/post') }}",
                type: "POST",
                data: param,
                contentType: false,
                processData: false,
                cache: false,
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
        jQuery("#area_code").val('');
        jQuery("#area_code").trigger('change');

        jQuery("#edit_id").val(id);
        jQuery('#username').prop("readonly", true);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('users/edit/?id=') }}" + id)));
        jQuery("#edit_id").val(result.id);
        jQuery("#username").val(result.username);
        jQuery("#name").val(result.name);
        jQuery("#email").val(result.email);
        jQuery("#job_code").val(result.job_code);
        jQuery("#nik").val(result.NIK);
        var area_code = result.area_code;
        jQuery("#area_code").val(area_code.split(','));
        jQuery("#area_code").trigger('change');

        jQuery('#material-images-1').prop('src', (result.img ? result.img : "{{URL::asset('img/add-img.png')}}"));

        jQuery('#role_id').val(result.role_id);
        jQuery('#role_id').trigger("change");

        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data");
        jQuery("#add-data-modal").modal("show");
    }

    function inactive(id) {
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
            url: "{{ url('users/active') }}",
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

    function openFile(id) {
        jQuery("#files_" + id).trigger('click');
    }

    function showImage(id) {
        var src = document.getElementById("files_" + id);
        var target = document.getElementById("material-images-" + id);
        var fr = new FileReader();
        fr.onload = function(e) {
            target.src = this.result;
        };
        fr.readAsDataURL(src.files[0]);
        jQuery('.btn-remove-image' + id).removeClass('hide');
        var status = jQuery('#material-images-' + id).data('status');
    }

    function removeImage(id) {
        var input = jQuery("input:file");
        jQuery('#panel-image-' + id).remove();
    }
</script>
@stop