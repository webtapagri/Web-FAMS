@extends('adminlte::page')
@section('title', 'FAMS - Blanko')
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
                                    <th>ID</th>
                                    <th>File Description</th>
                                    <th>File Name</th>
                                    <th>Jenis File</th>
                                    <th>Doc Size</th>
                                    <th>File Upload</th>
                                    <th>Active</th>
                                    @if($data['access']->update == 1 || $data['access']->delete == 1)
                                    <th>Action</th>
                                    @endif
                                </tr>
                                <tr role="row" class="filter">
                                    <th></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="file_description" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="file_name" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="jenis_file" autocomplete="off"></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="doc_size" autocomplete="off"></th>
                                    <th></th>
                                    <th><input type="text" class="form-control input-xs form-filter" name="status" autocomplete="off"></th>
                                    <?php if($data['access']->update == 1 || $data['access']->delete == 1){?>
                                    <th></th>   
                                    <?php } ?>
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
            <form id="data-form"  enctype="multipart/form-data">
                
		    	{!! csrf_field() !!}
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="modal-body">
                
                <div class="callout callout-warning">
                    <p>Upload Berkas Maximum 1 MB </p>
                </div>

                    <div class="box-body">
                            <input type="hidden" class="form-control" name="id" id="edit_id">
                        <div class="col-xs-12">
                            <label class="control-label" for="name">File Description</label>
                            <input type="text" class="form-control" name="file_description" id="file_description">
                        </div>
                        
                        <span id="list-kategori-upload"></span>
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
                    [100, 100, 50, 100, 150],
                    [100, 100, 50, 100, 150]
                ],
                "pageLength": 10,
                "ajax": {
                    url: "{!! route('get.blanko') !!}"
                },
                columns: [
                    // {
                    //     "render": function(data, type, row) {
                    //         if (row.img) {
                    //             var content = '<img src="' + row.img + '" class="img-circle img-responsive">';
                    //         } else {
                    //             var content = '<img src="{{ asset("img/user-default.png") }}" class="img-circle img-responsive">';
                    //         }

                    //         return content;
                    //     }
                    // },
                    {
                        data: 'ID',
                        name: 'ID'
                    },
                    {
                        data: 'FILE_DESCRIPTION',
                        name: 'FILE_DESCRIPTION'
                    },
                    {
                        data: 'FILE_NAME',
                        name: 'FILE_NAME'
                    },
                    {
                        data: 'JENIS_FILE',
                        name: 'JENIS_FILE'
                    },
                    {
                        data: 'DOC_SIZE',
                        name: 'DOC_SIZE'
                    },
                    {
                        "render": function(data, type, row) {
                            var content = '';
                            var id= btoa(row.ID);
                            content += '<a href="{{ url("blanko/berkas") }}/'+id+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';

                            return content;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            if (row.DELETED == 0) {
                                var content = '<span class="badge bg-green">Y</span>';
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
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.ID + '" onClick="edit(' + row.ID + ')"><i class="fa fa-pencil"></i></button>';
                            }
                            if (remove == 1) {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-activated  {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.DELETED == 0 ? '' : 'hide') + '" style="margin-left:5px"  onClick="inactive(' + row.ID + ')"><i class="fa fa-trash"></i></button>';
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-inactivated {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.DELETED == 1 ? '' : 'hide') + '" style="margin-left:5px"  onClick="active(' + row.ID + ')"><i class="fa fa-check"></i></button>';
                            }

                            return content;
                        }
                    }
                ],
                columnDefs: [{
                        targets: [0],
                        visible: false
                    },
                    {
                        targets: [1],
                        width: '25%'
                    },
                    {
                        targets: [2],
                        width: '25%'
                    },
                    {
                        targets: [3],
                        className: 'text-center',
                        width: '6%',
                        orderable: false,
                    },
                    {
                        targets: [5],
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

        

        jQuery('.btn-add').on('click', function() {
            document.getElementById("data-form").reset();
            var id = document.getElementById("edit_id").value;
            if(id == ''){
                id = 0;
            }
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-plus'></i> Create new data");
            jQuery("#add-data-modal").modal("show");

                    //ALL BERKAS
            $.ajax({
                type: 'GET',
                url: "{{ url('blanko/list-kategori-upload') }}/"+id,
                data: "",
                //async: false,
                dataType: 'html',
                success: function(data) 
                {
                    $("#list-kategori-upload").html(data);
                },
                error: function(x) 
                {                           
                    alert("Error: "+ "\r\n\r\n" + x.responseText);
                }
            });
            
        });

        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            jQuery("#add-data-modal").modal("show");
            
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

        jQuery('#data-form').on('submit', function(e) {
            

            var file_desc = $("#file_description").val();
            var file_upload = $("#file_upload").val();

            if( file_desc == '' )
            {
                notify({
                    type: 'warning',
                    message: " File description harus diisi ! "
                });
                return false;
            }

            if( file_upload == '' )
            {
                notify({
                    type: 'warning',
                    message: " Pilih berkas yang akan diupload ! "
                });
                return false;
            }

            e.preventDefault();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var param = new FormData(this);
            jQuery.ajax({
                url: "{{ url('blanko/upload_berkas') }}",
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
        // document.getElementById("data-form").reset();
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('blanko/edit/?id=') }}" + id)));
        jQuery("#edit_id").val(result.ID);
        jQuery("#file_description").val(result.FILE_DESCRIPTION);
        // jQuery("#jenis_file").val(result.JENIS_FILE);
        // jQuery("#doc_size").val(result.doc_size);
        // jQuery("#file_upload").val(result.file_upload);

        jQuery("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data");
        jQuery("#add-data-modal").modal("show");
        $.ajax({
                type: 'GET',
                url: "{{ url('blanko/list-kategori-upload') }}/"+id,
                data: "",
                //async: false,
                dataType: 'html',
                success: function(data) 
                {
                    $("#list-kategori-upload").html(data);
                },
                error: function(x) 
                {                           
                    alert("Error: "+ "\r\n\r\n" + x.responseText);
                }
            });
    }


    function inactive(id) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('blanko/inactive') }}",
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
            url: "{{ url('blanko/active') }}",
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