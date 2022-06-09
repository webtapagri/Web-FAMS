@extends('adminlte::page')
@section('title', 'FAMS - Asset Class')
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
                                <th>JENIS ASSET CODE</th>
                                <th>JENIS ASSET DESCRIPTION</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="jenis_asset_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="jenis_asset_description" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            
            <div id="row-group-asset" style="margin-top:10px;display:none">
                <div class="callout callout-info">
                    <h4>GROUP ASSET</h4>
                    <p>JENIS ASSET CODE : <span id="jenis-asset-code"></span> </p>
                </div>
               
                <div class="table-container">
                     <div class="xtable-actions-wrapper pull-right">
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <!-- @if($data['access']->create == 1)-->
                        <button class="btn btn-sm btn-flat btn-danger btn-add-group-asset"><i class="glyphicon glyphicon-plus" title="Add New Data Group Asset"></i></button>
                        <!-- @endif -->
                    </div>
                    <table id="data-table-group-asset" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>GROUP CODE</th>
                                <th>GROUP DESCRIPTION</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="group_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="group_description" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="row-subgroup-asset" style="margin-top:10px;display:none">
                <div class="callout callout-info">
                    <h4>SUBGROUP ASSET</h4>
                    <p>JENIS ASSET CODE : <span id="id_jenis_asset_code"></span> / GROUP CODE : <span id="id_group_code"></span> </p>
                </div>
                <div class="table-container">
                     <div class="xtable-actions-wrapper pull-right">
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <!-- @if($data['access']->create == 1)-->
                        <button class="btn btn-sm btn-flat btn-danger btn-add-subgroup-code"><i class="glyphicon glyphicon-plus" title="Add new data detail job"></i></button>
                        <!-- @endif -->
                    </div>
                    <table id="data-table-subgroup-asset" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>JENIS ASSET</th>
                                <th>SUBGROUP CODE</th>
                                <th>SUBGROUP DESCRIPTION</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                 <th><input type="text" class="form-control input-xs form-filter" name="jenis_asset_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="subgroup_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="subgroup_description" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="row-asset-map" style="margin-top:10px;display:none">
                <div class="callout callout-info">
                    <h4>ASSET CONTROLLER MAP</h4>
                    <p>MAP CODE : <span id="id_map_code"></span> </p>
                </div>
                <div class="table-container">
                     <div class="xtable-actions-wrapper pull-right">
                        <button class="btn btn-sm btn-flat btn-danger btn-refresh-data-table" title="refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                        <!-- @if($data['access']->create == 1)-->
                        <button class="btn btn-sm btn-flat btn-danger btn-add-asset-map"><i class="glyphicon glyphicon-plus" title="Add new data - Asset Controller Map"></i></button>
                        <!-- @endif -->
                    </div>
                    <table id="data-table-asset-map" class="table table-condensed" width="100%">
                        <thead>
                            <tr role="row" class="heading">
                                <th>ID</th>
                                <th>MAP CODE</th>
                                <th>JENIS ASSET CODE</th>
                                <th>GROUP CODE</th>
                                <th>SUBGROUP CODE</th>
                                <th>ASSET CTRL CODE</th>
                                <th>ASSET CTRL DESCRIPTION</th>
                                <th>MANDATORY CODE AC</th>
                                <th>MANDATORY CHECK IO SAP</th>
                                <th width="8%">ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="map_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="jenis_asset_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="group_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="subgroup_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="asset_ctrl_code" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="asset_ctrl_description" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="mandatory_kode_asset_controller" autocomplete="off"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="mandatory_check_io_sap" autocomplete="off"></th>
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
                            <label class="control-label" for="jenis-asset-code">Jenis Asset Code</label>
                            <input class="form-control" name='jenis_asset_code' id="jenis_asset_code" maxlength="400" requried>
                            <input type="hidden" name='edit_id' id="edit_id">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="jenis-asset-description">Jenis Asset Description</label>
                            <input class="form-control" name='jenis_asset_description' id="jenis_asset_description" requried>
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

<div id="add-data-modal-group-asset" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form-group-asset">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="">Jenis Asset</label>
                            <input type="text" class="form-control" name='ga_jenis_asset_code' id="ga_jenis_asset_code" readonly="readonly">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Group Code</label>
                            <input class="form-control" name='ga_group_code' id="ga_group_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">Group Description</label>
                            <input class="form-control" name='ga_group_description' id="ga_group_description" maxlength="400" requried>
                            <input type="hidden" name='edit_ga_id' id="edit_ga_id">
                            <input type="hidden" name='val_jenis_asset_code' id="val_jenis_asset_code" value="">
                            <input type="hidden" name='val_jenis_asset_code_name' id="val_jenis_asset_code_name" value="">
                            <input type="hidden" name='edit_ga_jenis_asset_code' id="edit_ga_jenis_asset_code" value="">
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

<div id="add-data-modal-subgroup-code" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form-subgroup-code">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="">JENIS ASSET CODE</label>
                            <input class="form-control" name='sgc_jenis_asset_code' id="sgc_jenis_asset_code" readonly>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">GROUP CODE</label>
                            <input class="form-control" name='sgc_group_code' id="sgc_group_code" readonly>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">SUBGROUP CODE</label>
                            <input class="form-control" name='sgc_subgroup_code' id="sgc_subgroup_code" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">SUBGROUP DESCRIPTION</label>
                            <input class="form-control" name='sgc_subgroup_description' id="sgc_subgroup_description" maxlength="400" requried>
                            <input type="hidden" name='edit_sgc_id' id="edit_sgc_id">
                            <input type="hidden" name='val_jenis_asset_code' id="val_jenis_asset_code" value="">
                            <input type="hidden" name='val_jenis_asset_code_name' id="val_jenis_asset_code_name" value="">
                            <input type="hidden" name='val_group_code' id="val_group_code" value="">
                            <input type="hidden" name='val_group_code_name' id="val_group_code_name" value="">
                            <input type="hidden" name='edit_sgc_group_code' id="edit_sgc_group_code" value="">
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

<div id="add-data-modal-asset-map" class="modal fade" role="dialog">
    <div class="modal-dialog" width="900px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <form id="data-form-asset-map">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <label class="control-label" for="">MAP CODE</label>
                            <input class="form-control" name='map_code' id="map_code" requried readonly="readonly">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">JENIS ASSET CODE 8</label>
                            <input class="form-control" name='acm_jenis_asset_code' id="acm_jenis_asset_code" maxlength="400" requried readonly="readonly">
                            <input type="hidden" name='edit_map_code_id' id="edit_map_code_id">
                            <input type="hidden" name='edit_acm_jenis_asset_code' id="edit_acm_jenis_asset_code">
                            <input type="hidden" name='edit_acm_jenis_asset_code_val' id="edit_acm_jenis_asset_code_val">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">GROUP CODE</label>
                            <input class="form-control" name='acm_group_code' id="acm_group_code" maxlength="400" requried readonly="readonly">
                            <input type="hidden" name='edit_acm_group_code' id="edit_acm_group_code">
                            <input type="hidden" name='edit_acm_group_code_val' id="edit_acm_group_code_val">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">SUB GROUP CODE</label>
                            <input class="form-control" name='acm_subgroup_code' id="acm_subgroup_code" maxlength="400" requried readonly="readonly">
                            <input type="hidden" name='edit_acm_subgroup_code' id="edit_acm_subgroup_code">
                            <input type="hidden" name='edit_acm_subgroup_code_val' id="edit_acm_subgroup_code_val">
                        </div>
                         <div class="col-xs-12">
                            <label class="control-label" for="">ASSET CONTROLLER</label>
                            <input class="form-control" name='acm_asset_ctrl_code' id="acm_asset_ctrl_code" maxlength="400" requried>
                            <input type="hidden" name='acm_asset_ctrl_description' id="acm_asset_ctrl_description">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">MANDATORY KODE ASSET CONTROLLER</label>
                            <input class="form-control" name='acm_mandatory_kode_asset_controller' id="acm_mandatory_kode_asset_controller" maxlength="400" requried>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" for="">MANDATORY CHECK IO SAP</label>
                            <input class="form-control" name='acm_mandatory_check_io_sap' id="acm_mandatory_check_io_sap" maxlength="400" requried>
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
        $("#row-group-asset").hide();

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
                    url: "{!! route('get.grid_asset_class') !!}"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'jenis_asset_code',
                        name: 'jenis_asset_code'
                    },
                    {
                        data: 'jenis_asset_description',
                        name: 'jenis_asset_description'
                    },
                    {
                        "render": function(data, type, row) {
                            var update = "{{ $data['access']->update }}";
                            var remove = "{{ $data['access']->delete }}";
                            var content = '';

                            if (update == 1) 
                            {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit" title="edit data ' + row.id + '" onClick="edit(' + row.id + ')"><i class="fa fa-pencil"></i></button>';
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view" title="detail data ' + row.id + '" onClick="group_asset(\''+row.jenis_asset_code+'\',\''+row.jenis_asset_description+'\')"><i class="fa fa-clone"></i></button>';
                            }
                            
                            /*
                            if (remove == 1) 
                            {
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-activated  {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 0 ? '' : 'hide') + '" style="margin-left:5px"  onClick="inactive(' + row.id + ')"><i class="fa fa-trash"></i></button>';
                                content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-inactivated {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 1 ? '' : 'hide') + '" style="margin-left:5px"  onClick="active(' + row.id + ')"><i class="fa fa-check"></i></button>';
                            }
                            */

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

        /*
        var role_jenis_asset_code = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset_code") !!}')));
        $('input[name="acm_jenis_asset_code"], #acm_jenis_asset_code').select2({
            data: role_jenis_asset_code,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
            readonly: true,
        }).on('change', function() {
            var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type=' + jQuery(this).val())));
            $("#acm_group_code").empty().select2({
                data: assetgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
            $("#acm_group_code").trigger('change');
        });
        
        var role_group_code = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_group_code") !!}')));
        $('input[name="acm_group_code"], #acm_group_code').select2({
            data: role_group_code,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
            readonly: true,
        }).on('change', function() 
        {
            var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group=' + jQuery(this).val())));
            $("#acm_subgroup_code").empty().select2({
                data: assetsubgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
            $("#acm_subgroup_code").trigger('change');
        });

        var role_subgroup_code = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_subgroup_code") !!}')));
        $('input[name="acm_subgroup_code"], #acm_subgroup_code').select2({
            data: role_subgroup_code,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
            readonly: true,
        });
        */

        var role_asset_controller = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.select_asset_controller") !!}')));
        $('input[name="acm_asset_ctrl_code"], #acm_asset_ctrl_code').select2({
            data: role_asset_controller,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
            readonly: true,
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

        $('.btn-add-group-asset').on('click', function() 
        {
            document.getElementById("data-form-group-asset").reset();

            //alert($("#val_jenis_asset_code").val());

            $('#role_id').prop('disabled', false);
            $("#edit_ga_id").val("");
            $("#edit_ga_jenis_asset_code").val($("#val_jenis_asset_code").val());
            $("#ga_jenis_asset_code").val($("#val_jenis_asset_code").val()+'-'+$("#val_jenis_asset_code_name").val());
            $("#font-awesome-result").removeClass();
            $("#add-data-modal-group-asset").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#add-data-modal-group-asset .modal-title").html("<i class='fa fa-plus'></i> CREATE NEW DATA - GROUP ASSET");
            $("#add-data-modal-group-asset").modal("show");
        });

        $('.btn-add-subgroup-code').on('click', function() 
        {
            document.getElementById("data-form-subgroup-code").reset();
            
            $("#edit_sgc_id").val("");
            $("#edit_sgc_group_code").val($("#val_group_code").val());
            $("#sgc_jenis_asset_code").val($("#val_jenis_asset_code").val()+'-'+$("#val_jenis_asset_code_name").val());
            $("#sgc_group_code").val($("#val_group_code").val()+'-'+$("#val_group_code_name").val());
            
            $("#font-awesome-result").removeClass();
            $("#add-data-modal-subgroup-code").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#add-data-modal-subgroup-code .modal-title").html("<i class='fa fa-plus'></i> CREATE NEW DATA - SUBGROUP ASSET");
            $("#add-data-modal-subgroup-code").modal("show");
        });

        $('.btn-add-asset-map').on('click', function() 
        {
            document.getElementById("data-form-asset-map").reset();
            $('#role_id').prop('disabled', false);

            //$("#map_code").hide();
            $("#edit_map_code_id").val("");
            $("#acm_jenis_asset_code").val($("#edit_acm_jenis_asset_code").val()+'-'+$("#edit_acm_jenis_asset_code_val").val());
            //$("#acm_jenis_asset_code").trigger("change");
            $("#acm_group_code").val($("#edit_acm_group_code").val()+'-'+$("#edit_acm_group_code_val").val());
            //$("#acm_group_code").trigger("change");
            $("#acm_subgroup_code").val($("#edit_acm_subgroup_code").val()+'-'+$("#edit_acm_subgroup_code_val").val());
            //$("#acm_subgroup_code").trigger("change");
            $("#acm_asset_ctrl_code").val("");
            $("#acm_asset_ctrl_code").trigger("change");
            $("#font-awesome-result").removeClass();

            $("#add-data-modal-asset-map").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#add-data-modal-asset-map .modal-title").html("<i class='fa fa-plus'></i> CREATE NEW DATA ~ ASSET CONTROLLER MAP");
            $("#add-data-modal-asset-map").modal("show");
        });

        jQuery('.btn-edit').on('click', function() {
            jQuery("#add-data-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            jQuery("#add-data-modal .modal-title").html("<i class='fa fa-pencil'></i> Edit data");
            jQuery("#add-data-modal").modal("show");
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
                    url: "{{ url('asset-class/post') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            jQuery("#add-data-modal").modal("hide");
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

        $('#data-form-group-asset').on('submit', function(e) 
        {
            if(confirm('confirm submit data Group Asset ?'))
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
                    url: "{{ url('asset-class/post-group-asset') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            jQuery("#add-data-modal-group-asset").modal("hide");
                            jQuery("#data-table-group-asset").DataTable().ajax.reload();
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

        $('#data-form-subgroup-code').on('submit', function(e) 
        {
            if(confirm('Confirm Submit Data SubGroup Asset ?'))
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
                    url: "{{ url('asset-class/post-subgroup-asset') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        $('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            $("#add-data-modal-subgroup-code").modal("hide");
                            $("#data-table-subgroup-asset").DataTable().ajax.reload();
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
                        $('.loading-event').fadeOut();
                    }
                });

            }
        });

        $('#data-form-asset-map').on('submit', function(e) 
        {
            if(confirm('confirm submit data Asset Controller Map ?'))
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
                    url: "{{ url('asset-class/post-asset-map') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) 
                        {
                            $("#add-data-modal-asset-map").modal("hide");
                            $("#data-table-asset-map").DataTable().ajax.reload();
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
    });

    function edit(id) 
    {
        //alert(id);
        document.getElementById("data-form").reset();
        $("#edit_id").val(id);
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('asset-class/edit/?id=') }}" + id)));
        $("#edit_id").val(result.ID);
        $("#jenis_asset_code").val(result.JENIS_ASSET_CODE);
        $("#jenis_asset_description").val(result.JENIS_ASSET_DESCRIPTION);
        //$("#menu_code").val(result.menu_code);
        //$("#menu_code").trigger("change");

        $("#add-data-modal .modal-title").html("<i class='fa fa-edit'></i> Update data " + result.JENIS_ASSET_DESCRIPTION);
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

    function group_asset(id,name)
    {
        //alert(name);
        $("#data-table-group-asset").DataTable().destroy()

        //alert(id);
        $("#row-group-asset").fadeOut();
        $("#row-subgroup-asset").hide();
        
        $("#jenis-asset-code").html(id+'-'+name);
        $("#val_jenis_asset_code").val(id);
        $("#val_jenis_asset_code_name").val(name);
        //$("#workflow-code-name").html('('+name+')');
        $("#row-group-asset").fadeIn();

        //if ( ! $.fn.DataTable.isDataTable( '#data-table-detail' ) ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var grid_group_asset = new Datatable();
            grid_group_asset.init({
                src: $("#data-table-group-asset"),
                onSuccess: function(grid_group_asset) {},
                onError: function(grid_group_asset) {},
                onDataLoad: function(grid_group_asset) {},
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
                        url: '{{ url("grid-ac-group-asset/") }}'+'/'+id
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'group_code',
                            name: 'group_code'
                        },
                        {
                            data: 'group_description',
                            name: 'group_description'
                        },
                        {
                            "render": function(data, type, row) {
                                var update = "{{ $data['access']->update }}";
                                var remove = "{{ $data['access']->delete }}";
                                var content = '';
                                if (update == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit-detail" title="edit data detail : ' + row.id + '" onClick="edit_group_asset('+row.id+',\''+name+'\')"><i class="fa fa-pencil"></i></button>';
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view-detail" title="detail data ' + row.group_code + '" onClick="detail_subgroup_asset(\''+id+'\',\''+row.group_code+'\',\''+row.group_description+'\',\''+name+'\')"><i class="fa fa-clone"></i></button>';
                                }
                                
                                /*
                                if (remove == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-activated  {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 0 ? '' : 'hide') + '" style="margin-left:5px"  onClick="inactive(' + row.id + ')"><i class="fa fa-trash"></i></button>';
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-inactivated {{ ($data["access"]->delete == 1 ? "":"hide") }}  ' + (row.deleted == 1 ? '' : 'hide') + '" style="margin-left:5px"  onClick="active(' + row.id + ')"><i class="fa fa-check"></i></button>';
                                }
                                */

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
        //}
    }

    function edit_group_asset(id,jenis_asset_name) 
    {
        //alert(id); return false;
        document.getElementById("data-form-group-asset").reset();
        $("#edit_ga_id").val(id);
        var result = $.parseJSON(JSON.stringify(dataJson("{{ url('asset-class/edit-group-asset/?id=') }}" + id)));
        
        $("#edit_ga_id").val(result.ID);
        $("#ga_group_code").val(result.GROUP_CODE);
        $("#ga_group_description").val(result.GROUP_DESCRIPTION);
        $("#ga_jenis_asset_code").val(result.JENIS_ASSET_CODE+"-"+jenis_asset_name);

        $("#add-data-modal-group-asset .modal-title").html("<i class='fa fa-edit'></i> UPDATE DATA - GROUP ASSET "+result.GROUP_CODE+" : " + result.GROUP_DESCRIPTION);
        $("#add-data-modal-group-asset").modal("show");
    }

    function detail_subgroup_asset(id_jenis_asset_code,id_group_code, name_group_code, name_jenis_asset_code)
    {
        //alert(id_jenis_asset_code+"~"+id_group_code); return false;
        $("#data-table-subgroup-asset").DataTable().destroy()

        //alert(id);
        $("#row-subgroup-asset").fadeOut();
        $("#id_jenis_asset_code").html(id_jenis_asset_code+'-'+name_jenis_asset_code);
        $("#id_group_code").html(id_group_code+'-'+name_group_code);
        $("#val_jenis_asset_code").val(id_jenis_asset_code);
        $("#val_jenis_asset_code_name").val(name_jenis_asset_code);
        $("#val_group_code").val(id_group_code);
        $("#val_group_code_name").val(name_group_code);
        //$("#workflow-code-name").html('('+name+')');
        $("#row-subgroup-asset").fadeIn();

        //if ( ! $.fn.DataTable.isDataTable( '#data-table-detail' ) ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var grid_subgroup_asset = new Datatable();
            grid_subgroup_asset.init({
                src: $("#data-table-subgroup-asset"),
                onSuccess: function(grid_subgroup_asset) {},
                onError: function(grid_subgroup_asset) {},
                onDataLoad: function(grid_subgroup_asset) {},
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
                        url: '{{ url("grid-ac-subgroup-asset/") }}'+'/'+id_group_code+'/'+id_jenis_asset_code
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'jenis_asset_code',
                            name: 'jenis_asset_code'
                        },
                        {
                            data: 'subgroup_code',
                            name: 'subgroup_code'
                        },
                        {
                            data: 'subgroup_description',
                            name: 'subgroup_description'
                        },
                        {
                            "render": function(data, type, row) {
                                var update = "{{ $data['access']->update }}";
                                var remove = "{{ $data['access']->delete }}";
                                var content = '';
                                if (update == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit-detail-job" title="Edit Data - SubGroup Asset : ' + row.id + '" onClick="edit_subgroup_code('+ row.id+',\''+id_jenis_asset_code+'\',\''+name_jenis_asset_code+'\',\''+id_group_code+'\',\''+name_group_code+'\')"><i class="fa fa-pencil"></i></button>';
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-view-detail" title="detail data ' + row.group_code + '" onClick="detail_asset_map(\''+id_jenis_asset_code+'\',\''+id_group_code+'\',\''+row.subgroup_code+'\',\''+name_jenis_asset_code+'\',\''+name_group_code+'\',\''+row.subgroup_description+'\')"><i class="fa fa-clone"></i></button>';
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

    function edit_subgroup_code(id,id_jenis_asset_code,name_jenis_asset_code,id_group_code,name_group_code) 
    {
        //alert(id); return false;
        document.getElementById("data-form-subgroup-code").reset();
        $("#edit_sgc_id").val(id);
        
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('asset-class/edit-subgroup-asset/?id=') }}" + id)));
        
        $("#edit_sgc_id").val(result.ID);

        $("#sgc_jenis_asset_code").val(id_jenis_asset_code+"-"+name_jenis_asset_code);
        $("#sgc_group_code").val(id_group_code+"-"+name_group_code);
        $("#sgc_subgroup_code").val(result.SUBGROUP_CODE);
        $("#sgc_subgroup_description").val(result.SUBGROUP_DESCRIPTION);

        $("#add-data-modal-subgroup-code .modal-title").html("<i class='fa fa-edit'></i> EDIT DATA - SUBGROUP ASSET "+result.SUBGROUP_CODE);
        $("#add-data-modal-subgroup-code").modal("show");
    }

    function edit_asset_map(id) 
    {
        //alert(id); return false;
        document.getElementById("data-form-asset-map").reset();
        $("#edit_map_code_id").val(id);
        
        var result = jQuery.parseJSON(JSON.stringify(dataJson("{{ url('asset-class/edit-asset-map/?id=') }}" + id)));

        //alert(result.ASSET_CTRL_CODE);
        
        $("#edit_map_code_id").val(result.ID);
        $("#map_code").val(result.MAP_CODE);
        
        $("#acm_jenis_asset_code").val(result.JENIS_ASSET_CODE);
        //$("#acm_jenis_asset_code").trigger("change");

        $("#acm_group_code").val(result.GROUP_CODE);
        //$("#acm_group_code").trigger("change");

        $("#acm_subgroup_code").val(result.SUBGROUP_CODE);
        //$("#acm_subgroup_code").trigger("change");

        $("#acm_asset_ctrl_code").val(result.ASSET_CTRL_CODE);
        $("#acm_asset_ctrl_code").trigger("change");

        $("#acm_asset_ctrl_description").val(result.ASSET_CTRL_DESCRIPTION);

        $("#acm_mandatory_kode_asset_controller").val(result.MANDATORY_KODE_ASSET_CONTROLLER);
        //$("#acm_mandatory_kode_asset_controller").trigger("change");

        $("#acm_mandatory_check_io_sap").val(result.MANDATORY_CHECK_IO_SAP);

        $("#add-data-modal-asset-map .modal-title").html("<i class='fa fa-edit'></i> UPDATE DATA - ASSET CONTROLLER MAP "+result.MAP_CODE);
        $("#add-data-modal-asset-map").modal("show");
    }

    function detail_asset_map(id_jenis_asset_code,id_group_code,id_subgroup_code, name_jenis_asset_code, name_group_code, subgroup_description)
    {
        var map_code = id_jenis_asset_code+id_group_code+id_subgroup_code;
        var idvar = id_jenis_asset_code+'__'+id_group_code+'__'+id_subgroup_code;

        //alert(id_jenis_asset_code+"~"+id_group_code); return false;
        $("#data-table-asset-map").DataTable().destroy();

        //alert(id);
        $("#row-asset-map").fadeOut();

        $("#id_jenis_asset_code").html(id_jenis_asset_code);
        $("#id_group_code").html(id_group_code);
        $("#id_subgroup_code").html(id_subgroup_code);
        $("#id_map_code").html(map_code);
        $("#edit_acm_jenis_asset_code").val(id_jenis_asset_code);
        $("#edit_acm_jenis_asset_code_val").val(name_jenis_asset_code);
        $("#edit_acm_group_code").val(id_group_code);
        $("#edit_acm_group_code_val").val(name_group_code);
        $("#edit_acm_subgroup_code").val(id_subgroup_code);
        $("#edit_acm_subgroup_code_val").val(subgroup_description);

        /* FOR ADD BUTTON */
        $("#map_code").val(map_code);        
       
        $("#row-asset-map").fadeIn();

        //if ( ! $.fn.DataTable.isDataTable( '#data-table-detail' ) ) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var grid_asset_map = new Datatable();
            grid_asset_map.init({
                src: $("#data-table-asset-map"),
                onSuccess: function(grid_asset_map) {},
                onError: function(grid_asset_map) {},
                onDataLoad: function(grid_asset_map) {},
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
                        url: '{{ url("grid-ac-asset-map/") }}'+'/'+idvar
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'map_code',
                            name: 'map_code'
                        },
                        {
                            data: 'jenis_asset_code',
                            name: 'jenis_asset_code'
                        },
                        {
                            data: 'group_code',
                            name: 'group_code'
                        },
                        {
                            data: 'subgroup_code',
                            name: 'subgroup_code'
                        },
                        {
                            data: 'asset_ctrl_code',
                            name: 'asset_ctrl_code'
                        },
                        {
                            data: 'asset_ctrl_description',
                            name: 'asset_ctrl_description'
                        },
                        {
                            data: 'mandatory_kode_asset_controller',
                            name: 'mandatory_kode_asset_controller'
                        },
                        {
                            data: 'mandatory_check_io_sap',
                            name: 'mandatory_check_io_sap'
                        },
                        {
                            "render": function(data, type, row) {
                                var update = "{{ $data['access']->update }}";
                                var remove = "{{ $data['access']->delete }}";
                                var content = '';
                                if (update == 1) 
                                {
                                    content += '<button class="btn btn-flat btn-xs btn-danger btn-action btn-edit-detail-job" title="edit data : ' + row.id + '" onClick="edit_asset_map(' + row.id + ')"><i class="fa fa-pencil"></i></button>';
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

</script>
@stop