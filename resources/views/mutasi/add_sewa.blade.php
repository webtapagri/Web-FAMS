<?php ?>

@extends('adminlte::page')
@section('title', 'FAMS - Request')

@section('content')
<style>
    label {
        font-weight: 500;
    }

    .select-img:hover {
        opacity: 0.5
    }

    .fmdb-input-default {
        background-color: #eee !important;
    }
</style>

<div class="row">
    <div class="col-md-12 xcol-md-offset-1">
        <div class="box">
            <form class="form-horizontal request-form" id="request-form" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="box-body">
                        
                        <div class="form-group">
                            <label for="plant" class="col-md-3">TANGGAL <sup style="color:red">*</sup></label>
                            <div class="col-md-4">
                                <input type="text" class="form-control input-sm" name="request_date" id="request_date" value="{{ date('d M Y') }}" autocomplete="off" required readonly>
                            </div>
                        </div>
            
                        <h4><b><u>DETAIL ASSET</u></b></h4>
                        <div class="row">
                            <div class="col-md-2">
                                <label>KODE ASET <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control input-sm" name="detail_kode_aset" id="detail_kode_aset" placeholder="KODE ASSET AMS" value="" readonly="readonly"><br/>
                                <button type="button" id="pilih-kode-aset" name="pilih-kode-aset" class="btn btn-info btn-flat btn-sm" OnClick="data_asset_modal()">Cari Kode Asset</button>
                            </div>
                            <div class="col-md-2">
                                <label>NAMA ASSET </label>
                                <input type="text" class="form-control input-sm" name="detail_nama_asset" id="detail_nama_asset" placeholder="Nama Asset" value="" readonly="readonly">
                                <br/>
                            </div>
                            <div class="col-md-2">
                                <label>ASSET CONTROLLER </label>
                                <input type="text" class="form-control input-sm" name="detail_ac" id="detail_ac" placeholder="Asset Controller" value="" readonly="readonly">
                            </div>
                            <div class="col-md-2">
                                <label>KEPEMILIKAN </label>
                                <input type="text" class="form-control input-sm" name="detail_milik_company" id="detail_milik_company" placeholder="Kepemilikan Company" value="" readonly="readonly">
                                <br/>
                                <input type="text" class="form-control input-sm" name="detail_milik_area" id="detail_milik_area" placeholder="Kepemilikan Business Area" value="" readonly="readonly">
                            </div>
                            <div class="col-md-1">
                                <label>LOKASI </label>
                                <input type="text" class="form-control input-sm" name="detail_lokasi_company" id="detail_lokasi_company" placeholder="Lokasi Company" value="" readonly="readonly">
                                <br/>
                                <input type="text" class="form-control input-sm" name="detail_lokasi_area" id="detail_lokasi_area" placeholder="Lokasi Business Area" value="" readonly="readonly">
                            </div>
                            <div class="col-md-2">
                                <label>TUJUAN <sup style="color:red">*</sup></label>
                                <input type="text" class="form-control input-sm" name="detail_tujuan_company" id="detail_tujuan_company" placeholder="Tujuan Company" value="" readonly="readonly">
                                <br/>
                                <input type="text" class="form-control input-sm" name="detail_tujuan_area" id="detail_tujuan_area" placeholder="Tujuan Business Area" required="required">
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-flat btn-danger btn-add-items" OnClick="addItem();" style="margin-top:25px"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed" id="request-item-table">
                                    <tr>
                                        <th>KODE ASET</th>
                                        <th>NAMA ASSET</th>
                                        <th>ASSET CONTROLLER</th>
                                        <th>KEPEMILIKAN</th>
                                        <th>LOKASI</th>
                                        <th>TUJUAN</th>
                                        <th></th>
                                    </tr>
                                    
                                    <?php 

                                        if(!empty($data['data']))
                                        {
                                            $l = "";
                                            foreach($data['data'] as $k => $v)
                                            {
                                                $kode_asset_ams = base64_encode($v->KODE_ASSET_AMS);
                                                $lokasi = explode("-",$v->LOKASI_BA_DESCRIPTION);
                                                $LOKASI_CODE = @$lokasi[0];

                                                $l .= "<tr>";
                                                $l .= "<td><input type='hidden' id='kode_aset' name='kode_aset[]' value='".$v->KODE_ASSET_AMS."_".$v->TUJUAN_COMPANY."_".$v->TUJUAN."_".$v->ASSET_CONTROLLER."_".$LOKASI_CODE."'><a href='".url('master-asset/show-data')."/".$kode_asset_ams."' target='_blank'>".$v->KODE_ASSET_AMS."</a></td>";
                                                $l .= "<td>".$v->NAMA_ASSET."</td>";
                                                $l .= "<td>".$v->ASSET_CONTROLLER."</td>";
                                                $l .= "<td>".$v->BA_PEMILIK_ASSET_COMPANY."/".$v->BA_PEMILIK_ASSET."</td>";
                                                $l .= "<td>".$v->LOKASI_BA_CODE_COMPANY."/".$v->LOKASI_BA_DESCRIPTION."</td>";
                                                $l .= "<td>".$v->TUJUAN_COMPANY."/".$v->TUJUAN."</td>";
                                                $l .= "<td style='text-align:center' rowspan='rowspan'><a href='#' id='edit-berkas' idcontent='".$v->KODE_ASSET_AMS."' class='btn btn-icon-toggle' title='Edit Berkas' data-toggle='modal' data-target='#modal_upload_berkas'><i class='fa fa-upload'></i></a>&nbsp;<button type='button' class='btn btn-flat btn-xs btn-danger' onClick='remove_data_temp({$v->KODE_ASSET_AMS})'><i class='fa fa-trash'></i></button></td>";
                                                $l .= "</tr>";
                                            }
                                            echo $l;
                                        }
                                        else
                                        {
                                    ?>
                                        <tr>
                                            <td colspan="8" style="text-align:center;font-size: 12px;color: #808484"><br>Belum ada data </td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer clearfix">
                        
                        <button type="button" class="submit-data btn btn-danger btn-flat pull-right" style="margin-right: 5px;">Submit</button>
                        
                        <button type="button" class="btn btn-default btn-flat btn-cancel pull-right" style="margin-right: 5px;">Delete</button>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>

<div id="data-asset-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <table id="data-table-asset" class="table table-bordered table-condensed">
                         <thead>
                            <tr role="row" class="heading">
                                <th>KODE ASSET AMS</th>
                                <th>KODE ASSET SAP</th>
                                <th>NAMA MATERIAL</th>
                                <th>NAMA ASSET</th>
                                <th>BA PEMILIK ASSET</th>
                                <th>LOKASI BA DESCRIPTION</th>
                                <th>ASSET CONTROLLER</th>
                                <th>ACTION</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" data-column="1" class="form-control input-xs form-filter" name="KODE_ASSET_AMS" id="KODE_ASSET_AMS"></th>
                                <th><input type="text" data-column="2" class="form-control input-xs form-filter" name="KODE_ASSET_SAP" id="KODE_ASSET_SAP"></th>
                                <th><input type="text" data-column="3" class="form-control input-xs form-filter" name="NAMA_MATERIAL" id="NAMA_MATERIAL"></th>
                                <th><input type="text" data-column="4" class="form-control input-xs form-filter" name="NAMA_ASSET" id="NAMA_ASSET"></th>
                                <th><input type="text" data-column="5" class="form-control input-xs form-filter" name="BA_PEMILIK_ASSET" id="BA_PEMILIK_ASSET"></th>
                                <th><input type="text" data-column="6" class="form-control input-xs form-filter" name="LOKASI_BA_DESCRIPTION" id="LOKASI_BA_DESCRIPTION"></th>
                                <th><input type="text" data-column="7" class="form-control input-xs form-filter" name="ASSET_CONTROLLER" id="ASSET_CONTROLLER"></th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!--div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div-->
        </div>
    </div>
</div>

<div class="modal fade" id="modal_upload_berkas" xtabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 class="modal-title" id="myModalLabel">Upload Berkas</h3>
                <div class="callout callout-warning">
                    <p>Upload Berkas Maximum 1 MB </p>
                </div>
            </div>
            
            <form id="form-detil" name="form-detil" class="form-horizontal" method="POST" action="{{ url('/mutasi/upload_berkas_amp') }}" enctype="multipart/form-data">

                {!! csrf_field() !!}
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" class="form-control" id="tipe" name="tipe" value="sewa"/>
                
                <div class="modal-body">

                    <div class="callout callout-warning"><p>Note, Tipe File yang di Upload hanya JPG/PNG dan PDF</p></div>

                    <div class="form-group">
                        <label class="control-label col-xs-4" >KODE ASSET AMS</label>
                        <div class="col-xs-8">
                            <input type="text" class="form-control" id="kode_asset_ams" name="kode_asset_ams" value="" readonly="readonly" />
                        </div>
                    </div>

                    <span id="list-mutasi-upload"></span>

                    <div class="form-group">
                        <label class="control-label col-xs-4" >NOTES</label>
                        <div class="col-xs-8">
                            <textarea class="form-control" id="notes_asset" name="notes_asset" required></textarea>
                        </div>
                    </div>

                    

                </div>
                    
                <div class="modal-footer">
                    <button class="btn btn-flat btn-lg btn-info" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <input type="submit" class="btn btn-flat btn-lg btn-danger" id="" value="Upload">
                </div>
            </form>

        </div>  
    </div>
</div>

@stop
@section('js') 

@if(Session::has('alert'))
<script type="text/javascript">
    notify({
        type: 'warning',
        message: "{{ Session::get('alert') }}"
    });
</script>
@endif 

<script>
    var imgFiles = [];
    var addFile = 2;
    var request_item = {};
    var item_count = 1;
    var request_item_page = [];
    var data_page = new Array(3);
    var current_page = 1;
    var records_per_page = 1;
    var data_detail = {};
    var request_docs = [];
    var selected_detail_item = [];
    var transaction_type = jQuery("#transaction_type");
    var request_date = jQuery("#request_date");
    var business_area = jQuery("#business_area");
    var po_no = jQuery("#po_no");
    var po_date = jQuery("#po_date");
    var po_date = jQuery("#po_date");
    var vendor_code = jQuery("#vendor_code");
    var vendor_name = jQuery("#vendor_name");


    $(document).ready(function() 
    {
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });


        $('#request-detail-page').addClass('sub-loader');
        $(".btn-cancel").on('click', function() 
        {
            if (confirm("Confirm Delete All Data ?")) 
            {

                var param = '';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('mutasi/delete_all_berkas_temp') }}",
                    method: "POST",
                    data: param,
                    beforeSend: function() {
                        $('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        //alert(result.status); return false;
                        if (result.status) 
                        {
                            notify({
                                type: 'success',
                                message: result.message
                            });
                            setTimeout(reload_page, 500);
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

                /* =================================================
                request_item = [];
                request_item_page = [];
                createItemRequestTable();

                jQuery('#transaction_type').val('');
                jQuery('#transaction_type').trigger('change');

                /* jQuery('#business_area').val('');
                jQuery('#business_area').trigger('change'); */
                /* notify({
                    type: 'error',
                    message: 'form has been cleared!'
                });
                document.getElementById("request-form").reset();
                ====================================================== */ 
            }
        });

        jQuery("#po_date").datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: "today",
            maxDate: 'today'
        });

        jQuery("#transaction_type").select2({
            data: [{
                    id: '1',
                    text: 'Barang'
                },
                {
                    id: '2',
                    text: 'Jasa'
                },
                {
                    id: '3',
                    text: 'Lain-lain'
                },
            ],
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        var plant = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.generaldataplant") !!}')));
        jQuery("#business_area,  #asset_location").select2({
            data: plant,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        jQuery('#business_area').val('1211');
        jQuery('#business_area').trigger('change');
        jQuery('#business_area').attr('disabled', 'disabled');;

        jQuery("#request-form").on("submit", function(e) {
            e.preventDefault();

            if (requestItemData() > 0) {
                jQuery('.request-form').addClass('hide');
                jQuery('.code-asset-form').removeClass('hide');

                jQuery("#asset_request_date").val(request_date.val());
                jQuery("#asset_business_area").val(business_area.val());
                jQuery("#asset_po_no").val(po_no.val());
                jQuery("#asset_po_date").val(po_date.val());
                jQuery("#asset_vendor_code").val(vendor_code.val());
                jQuery("#asset_vendor_name").val(vendor_name.val());

                topFunction();
                var items = [];
                jQuery.each(request_item, function(key, val) {
                    if (val.name) {
                        items.push({
                            id: val.id,
                            text: val.code + ' - ' + val.name
                        })
                    }
                });


                jQuery("#detail_item_selected").select2({
                    data: items,
                    width: "100%",
                    allowClear: true,
                    placeholder: ' '
                }).on("change", function() {
                    getProp(jQuery(this).val());
                });

                jQuery("#detail_item_selected").val(items[0].id);
                jQuery("#detail_item_selected").trigger("change");


            } else {
                notify({
                    type: 'warning',
                    message: 'please, add an item'
                });
            }
        });

        jQuery("#asset_group").select2();
        jQuery("#asset_sub_group").select2();

        var jenisasset = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.jenisasset") !!}')));
        jQuery("#asset_type").select2({
            data: jenisasset,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        }).on('change', function() {
            var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type=' + jQuery(this).val())));
            jQuery("#asset_group").empty().select2({
                data: assetgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            })
        });

        jQuery("#asset_group").on('change', function() {
            var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group=' + jQuery(this).val())));
            jQuery("#asset_sub_group").empty().select2({
                data: assetsubgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            })
        });


        jQuery(".btn-back-request-form").on("click", function(e) {
            jQuery('.code-asset-form').addClass('hide');
            jQuery('.request-form').removeClass('hide');
            topFunction();
        })

        jQuery('#form-basic-data').on('submit', function(e) {
            e.preventDefault();
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /* var form = jQuery('#form-initial').find('input, select, textarea').appendTo('#form-basic-data'); */
            /* var param = new FormData(this); */
            var init_form = jQuery(this).serializeArray();
            var param = {
                "asset": request_form,
                "docs": request_docs
            };
            param.push(init_form);


            jQuery.ajax({
                url: "{{ url('materialrequest/post') }}",
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
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        window.location.href = "{{ url('mastermaterial') }}";
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

        jQuery("#docs").on("change", function() {
            for (var i = 0; i < $(this).get(0).files.length; ++i) {

                request_docs[i] = {
                    'name': '',
                    'size': '',
                    'type': '',
                    'file': ''
                };
            }
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                var file = $(this).get(0).files[i];

                request_docs[i].name = file.name;
                request_docs[i].size = file.size;
                request_docs[i].type = file.type;
                getBase64(i, file);
            }
        });

        jQuery("#asset_name").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_name = jQuery(this).val();
        });

        jQuery("#asset_type").on('change', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_type = jQuery(this).val();
        });

        jQuery("#asset_group").on('change', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_group = jQuery(this).val();
        });

        jQuery("#asset_sub_group").on('change', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_sub_group = jQuery(this).val();
        });

        jQuery("#asset_brand").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_brand = jQuery(this).val();
        });

        jQuery("#asset_imei").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_imei = jQuery(this).val();
        });

        jQuery("#asset_police_no").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_police_no = jQuery(this).val();
        });

        jQuery("#asset_serie_no").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_serie_no = jQuery(this).val();
        });

        jQuery("#asset_specification").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_specification = jQuery(this).val();
        });

        jQuery("#asset_year").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_year = jQuery(this).val();
        });

        jQuery("#asset_info").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_info = jQuery(this).val();
        });

        jQuery("#asset_pic_name").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_pic_name = jQuery(this).val();
        });

        jQuery("#asset_pic_level").on('keyup', function() {
            var id = current_page - 1;
            var obj = jQuery('#detail_item_selected').val();
            request_item[obj].detail[id].asset_pic_level = jQuery(this).val();
        });

        jQuery("#asset_location").on('change', function() {
            if (jQuery(this).val()) {
                var id = (current_page - 1);
                var obj = jQuery('#detail_item_selected').val();

                var data = jQuery(this).select2('data');
                request_item[obj].detail[id].asset_location = data[0].id;
                request_item[obj].detail[id].asset_location_desc = data[0].text;
            }
        });

        jQuery("input[name='asset_condition']").on('change', function() {
            if (jQuery(this).val()) {
                var id = current_page - 1;
                var obj = jQuery('#detail_item_selected').val();
                request_item[obj].detail[id].asset_condition = jQuery(this).val();
            }
        });

        $('.submit-data').on('click', function(e)
        {
            if(confirm('Confirm submit data?'))
            {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var param = $('#request-form').serialize();

                $.ajax({
                    url: "{{ url('mutasi/post_sewa') }}",
                    type: "POST",
                    data: param,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        if (result.status) {
                            notify({
                                type: 'success',
                                message: result.message
                            });
                            setTimeout(reload_page, 2000);
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

        $('#data-asset-modal').on('hidden.bs.modal', function () 
        {
            $('#data-table-asset').dataTable().fnDestroy();
        });
        $('#request-item-table').on('click', 'a', function (e) 
        {
            var idcontent = $( this ).attr("idcontent"); //alert(idcontent);return false;
            var jenis_pengajuan = 'sewa';
            $("#form-detil #kode_asset_ams").val(idcontent);

            //ALL BERKAS
            $.ajax({
                type: 'GET',
                url: "{{ url('mutasi/list-upload') }}/"+idcontent+"/"+jenis_pengajuan,
                data: "",
                //async: false,
                dataType: 'html',
                success: function(data) 
                {
                    $("#list-mutasi-upload").html(data);
                },
                error: function(x) 
                {                           
                    alert("Error: "+ "\r\n\r\n" + x.responseText);
                }
            });

            //BERKAS NOTES
            $.ajax({
                type: 'GET',
                url: "{{ url('mutasi/berkas/sewa') }}/"+idcontent,
                data: "",
                //async: false,
                dataType: 'json',
                success: function(data) 
                {
                    $("#notes_asset").val(data.notes);
                },
                error: function(x) 
                {                           
                    alert("Error: "+ "\r\n\r\n" + x.responseText);
                }
            });  

        });
    });

    function save(status) {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (validateSave()) {
            var param = {
                transaction_type: transaction_type.val(),
                request_date: request_date.val(),
                business_area: business_area.val(),
                po_no: po_no.val(),
                po_type: 1,
                po_date: po_date.val(),
                vendor_code: vendor_code.val(),
                vendor_name: vendor_name.val(),
                docs: request_docs,
                asset: request_item,
                status: status
            };

            jQuery.ajax({
                url: "{{ url('request/post') }}",
                type: "POST",
                data: param,
                /*  contentType: false,
                processData: false,
                cache: false, */
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) {
                    if (result.status) {
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        notify({
                            type: 'error',
                            message: 'reqeust has been submited!'
                        });
                        window.location.href = "{{ url('/') }}";
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
    }

    function validateSave() {
        var valid = true;
        jQuery.each(request_item, function(i, field) {
            if (field) {
                jQuery.each(field.detail, function(key, val) {
                    if (val.asset_imei === "") {
                        notify({
                            type: 'warning',
                            message: 'No Mesin / IMEI pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });

                        valid = false;
                        return false;
                    }

                    if (val.asset_serie_no === "") {
                        notify({
                            type: 'warning',
                            message: 'No Seri / Rangka pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });

                        valid = false;
                        return false
                    }

                    if (val.asset_year === "") {

                        notify({
                            type: 'warning',
                            message: 'Tahun pada asset  ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                    if (val.asset_location === "") {
                        notify({
                            type: 'warning',
                            message: 'Lokasi pada asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                    }

                    if (val.asset_condition === "") {
                        notify({
                            type: 'warning',
                            message: 'Kondisi pada  asset ' + field.name + ' page ' + (key + 1) + ' tidak boleh kosong!'
                        });
                        valid = false;
                        return false;
                    }

                });
            }
        });

        return valid;
    }


    function validateItem() 
    {
        var valid = true;
        var kode_aset = jQuery("#detail_kode_aset");
        var company = jQuery("#detail_company");
        var business_area = jQuery("#detail_barea");

        if (kode_aset.val() == "") {
            kode_aset.focus();
            valid = false;
        }


        if (company.val() == "") {
            company.focus();
            valid = false;
        }

        if (business_area.val() == "") {
            business_area.focus();
            valid = false;
        }

        return valid;

    }

    function addItem()
    {
        //alert("add item"); return false;

        var id = makeInt(5);
     
        var kode_aset = $("#detail_kode_aset");
        var detail_nama_asset = $("#detail_nama_asset");
        var detail_ac = $("#detail_ac");
        var detail_milik_company = $("#detail_milik_company");
        var detail_milik_area = $("#detail_milik_area");
        var detail_lokasi_company = $("#detail_lokasi_company");
        var detail_lokasi_area = $("#detail_lokasi_area");
        var detail_tujuan_company = $("#detail_tujuan_company");
        var detail_tujuan_area = $("#detail_tujuan_area");
        var jenis_pengajuan = 'sewa';

        if( detail_tujuan_area.val() == '' )
        {
            notify({
                type: 'warning',
                message: 'Tujuan Business Area is required'
            });
            return false;
        }
        
        //if(confirm('Confirm Add ?')){
            var param = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('mutasi/add_temp') }}",
                method: "POST",
                data: param+"&jenis="+jenis_pengajuan+"&kode_asset_ams="+kode_aset.val()+"&nama_aset="+detail_nama_asset.val()+"&asset_controller="+detail_ac.val()+"&milik_company="+detail_milik_company.val()+"&milik_area="+detail_milik_area.val()+"&lokasi_company="+detail_lokasi_company.val()+"&lokasi_area="+detail_lokasi_area.val()+"&tujuan_company="+detail_tujuan_company.val()+"&tujuan_area="+detail_tujuan_area.val(),
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status); return false;
                    if (result.status) 
                    {
                        //$("#approve-disposal-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        setTimeout(reload_page, 1000); 
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
        //}
    }

    function addItem_v1() 
    {
        if (validateItem()) 
        {
            var id = makeInt(5);
     
            var kode_aset = $("#detail_kode_aset");
            var detail_nama_asset = $("#detail_nama_asset");
            var detail_ac = $("#detail_ac");
            var detail_milik_company = $("#detail_milik_company");
            var detail_milik_area = $("#detail_milik_area");
            var detail_lokasi_company = $("#detail_lokasi_company");
            var detail_lokasi_area = $("#detail_lokasi_area");
            var detail_tujuan_company = $("#detail_tujuan_company");
            var detail_tujuan_area = $("#detail_tujuan_area");

            if( detail_tujuan_area.val() == '' )
            {
                notify({
                    type: 'warning',
                    message: 'Tujuan Business Area is required'
                });
                return false;
            }

            //var detail_berkas = $("#berkas_asset");

            /*
            $('#berkas_asset').change(function(e)
            {
                var fileName = e.target.files[0].name;
                alert('The file "' + fileName +  '" has been selected.');
            });
            */
            //alert(detail_berkas); 
            //console.log(detail_berkas[0].files[0].name); return false;
            //console.log(detail_berkas[0].files[0].size); return false;

            if(validate_additem(detail_ac,kode_aset,detail_milik_area,request_item))
            {
                request_item[id] = 
                {
                    id: id,
                    kode_aset: kode_aset.val(),
                    detail_nama_asset: detail_nama_asset.val(),
                    detail_ac:detail_ac.val(),
                    detail_milik_company: detail_milik_company.val(),
                    detail_milik_area: detail_milik_area.val(),
                    detail_lokasi_company: detail_lokasi_company.val(),
                    detail_lokasi_area: detail_lokasi_area.val(),
                    detail_tujuan_company: detail_tujuan_company.val(),
                    detail_tujuan_area: detail_tujuan_area.val(),
                    detail: []
                };

                //console.log(request_item); 
                //return false;

                createItemRequestTable();

                kode_aset.val("");
                detail_nama_asset.val("");
                detail_ac.val("");
                detail_tujuan_company.val("");
                detail_tujuan_area.val("");
                detail_milik_company.val("");
                detail_milik_area.val("");
                detail_lokasi_company.val("");
                detail_lokasi_area.val("");
                detail_tujuan_company.val("");
            }
            
        }
    }

    function createPage(id) 
    {
        request_item_page = [];
        data_page = [];
        var item_detail = [];
        var item = request_item[id];
        
        for (var i = 0; i < item.request_qty; i++) 
        {
            item_detail.push({
                asset_type: '',
                asset_group: '',
                asset_sub_group: '',
                //asset_name: item.name + (item.request_qty > 1 ? ' - ' + (i + 1) : ''),
                asset_name: item.name,
                asset_brand: '',
                asset_imei: '',
                asset_police_no: '',
                asset_serie_no: '',
                asset_specification: '',
                asset_location: '',
                asset_location_desc: '',
                asset_condition: '',
                asset_year: '',
                asset_pic_name: '',
                asset_pic_level: '',
                asset_foto: '',
                asset_info: '',
                foto_asset: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                },
                foto_asset_seri: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                },
                foto_asset_mesin: {
                    name: '',
                    size: '',
                    type: '',
                    file: ''
                }
            });
        }

        request_item[id].detail = item_detail;
    }

    function makeid(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    function makeInt(length) {
        var text = "";
        var possible = "0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    function remove(obj) 
    {
        var conf = confirm("Are you sure you want to delete this data?");
        if (conf == true) 
        {
            request_item[obj] = [];
            data_detail[obj] = [];
            //console.log(request_item);
            createItemRequestTable();
        }
    }

    function createItemRequestTable() 
    {
        var item = '<table class="table table-bordered table-condensed" id="request-item-table">';
        item += '<tr>';
        item += '<th>KODE ASET</th>';
        item += '<th>NAMA ASSET</th>';
        item += '<th>ASSET CONTROLLER</th>';
        item += '<th>KEPEMILIKAN</th>';
        item += '<th>LOKASI</th>';
        item += '<th>TUJUAN</th>';
        item += '<th></th>';
        item += '</tr>';

        $.each(request_item, function(key, val) 
        {
            var kode_asset_ams = btoa(val.kode_aset);

            if (val.kode_aset) 
            {
                item += "<tr>";
                item += "<td><input type='hidden' id='kode_aset' name='kode_aset[]' value='"+val.kode_aset+"_"+val.detail_tujuan_company+"_"+val.detail_tujuan_area+"_"+val.detail_ac+"'><a href='{{ url('master-asset/show-data') }}/"+kode_asset_ams+"' target='_blank'>" + val.kode_aset + "</a></td>";
                item += "<td>"+val.detail_nama_asset+"</td>";
                item += "<td>"+val.detail_ac+"</td>";
                item += "<td>" + val.detail_milik_company + " / " + val.detail_milik_area + "</td>";
                item += "<td>" + val.detail_lokasi_company + " / " + val.detail_lokasi_area + "</td>";
                item += "<td>" + val.detail_tujuan_company + " / " + val.detail_tujuan_area + "</td>";
                //item += "<td>" + val.bisnis_area + "</td>";
                item += '<td style="text-align:center" rowspan="rowspan"><a href="#" id="edit-berkas" idcontent="'+val.kode_aset+'" class="btn btn-icon-toggle" title="Edit Berkas" data-toggle="modal" data-target="#modal_upload_berkas"><i class="fa fa-upload"></i></a>&nbsp;<button type="button" class="btn btn-flat btn-xs btn-danger" onClick="remove(\'' + val.id + '\');"><i class="fa fa-trash"></i></button></td>';
                item += "</tr>";
            }
        });
        
        item += "</table>";
        $("#request-item-table").html(item);
    }

    function qtyEdit(obj) {
        var selected = request_item[obj];
        var qty = jQuery('#qty_' + obj).val();
        request_item[obj].request_qty = qty;
        createItemRequestTable();
        createPage(obj);
    }

    function getProp(id) {
        var item = request_item[id];
        request_item_page = item.detail;
        jQuery('#item_po').val(item.item_po);
        jQuery('#item_code').val(item.code);
        jQuery('#item_name').val(item.name);
        jQuery('#item_qty_index').val(item.request_qty);

        current_page = 1;
        changePage(1);
    }

    function assetInfo(index) 
    {
        var obj = index - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        jQuery('#asset_name').val(item.asset_name);
        jQuery('#asset_brand').val(item.asset_brand);
        jQuery('#asset_imei').val(item.asset_imei);
        jQuery('#asset_police_no').val(item.asset_police_no);
        jQuery('#asset_serie_no').val(item.asset_police_no);
        jQuery('#asset_specification').val(item.asset_specification);
        jQuery('#asset_year').val(item.asset_year);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);
        jQuery('#asset_info').val(item.asset_info);
        jQuery('#asset_pic_name').val(item.asset_pic_name);
        jQuery('#asset_pic_level').val(item.asset_pic_level);

        if (item.asset_condition === 'B') {
            jQuery('#condition1').prop("checked", true);
        } else if (item.asset_condition === 'BP') {
            jQuery('#condition2').prop("checked", true);
        } else if (item.asset_condition === 'TB') {
            jQuery('#condition3').prop("checked", true);
        } else {
            jQuery("input[name='asset_condition']").prop("checked", false);
        }

        jQuery('#asset_location').val(item.asset_location);
        jQuery('#asset_location').trigger('change');
        jQuery('#asset_location').val(item.asset_location);
        jQuery('#asset_location').trigger('change');
        jQuery('#asset_type').val(item.asset_type);
        jQuery('#asset_type').trigger("change");
        jQuery('#asset_group').val(item.asset_group);
        jQuery('#asset_group').trigger('change');
        jQuery('#asset_sub_group').val(item.asset_sub_group);
        jQuery('#asset_sub_group').trigger('change');

        if (item.foto_asset.file) {
            jQuery("#foto_asset_thumb_1").prop('src', item.foto_asset.file);
            jQuery(".btn-foto-asset-remove").removeClass('hide');
        } else {
            jQuery("#foto_asset_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-asset-remove").addClass('hide');
        }

        if (item.foto_asset_seri.file) {
            jQuery("#foto_no_seri_thumb_1").prop('src', item.foto_asset_seri.file);
            jQuery(".btn-foto-seri-remove").removeClass('hide');
        } else {
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-seri-remove").addClass('hide');
        }

        if (item.foto_asset_mesin.file) {
            jQuery("#foto_mesin_thumb_1").prop('src', item.foto_asset_mesin.file);
            jQuery(".btn-foto-mesin-remove").removeClass('hide');
        } else {
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-mesin-remove").addClass('hide');
        }
    }

    function openFile(code, id) {
        if (code == 'asset') {
            jQuery("#foto_asset_" + id).trigger('click');
        } else if (code == 'seri') {
            jQuery("#foto_no_seri_" + id).trigger('click');
        } else if (code == 'mesin') {
            jQuery("#foto_mesin_" + id).trigger('click');
        }
    }

    function showImage(code, id) {
        var obj = current_page - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        if (code == 'asset') {
            var src = document.getElementById("foto_asset_1");
            var target = document.getElementById("foto_asset_thumb_1");
        } else if (code == 'seri') {
            var src = document.getElementById("foto_no_seri_1");
            var target = document.getElementById("foto_no_seri_thumb_1");
        } else if (code == 'mesin') {
            var src = document.getElementById("foto_mesin_1");
            var target = document.getElementById("foto_mesin_thumb_1");
        }


        var fr = new FileReader();
        fr.onload = function(e) {
            target.src = this.result;
            if (code == 'asset') {
                item.foto_asset.file = this.result;
            } else if (code == 'seri') {
                item.foto_asset_seri.file = this.result;
            } else if (code == 'mesin') {
                item.foto_asset_mesin.file = this.result;
            }
        };

        var foto = src.files[0];
        if (code == 'asset') {
            item.foto_asset.name = foto.name;
            item.foto_asset.type = "asset";
            item.foto_asset.size = foto.size;
            jQuery(".btn-foto-asset-remove").removeClass('hide');
        } else if (code == 'seri') {
            item.foto_asset_seri.name = foto.name;
            item.foto_asset_seri.type = "no seri";
            item.foto_asset_seri.size = foto.size;
            jQuery(".btn-foto-seri-remove").removeClass('hide');
        } else if (code == 'mesin') {
            item.foto_asset_mesin.name = foto.name;
            item.foto_asset_mesin.type = "imei";
            item.foto_asset_mesin.size = foto.size;
            jQuery(".btn-foto-mesin-remove").removeClass('hide');
        }

        fr.readAsDataURL(src.files[0]);
        jQuery('.btn-remove-image' + id).removeClass('hide');
        var status = jQuery('#material-images-' + id).data('status');
    }

    function removeImage(code, id) {
        var obj = id - 1;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];

        if (code == 'asset') {
            item.foto_asset.file = '';
            item.foto_asset.name = '';
            item.foto_asset.type = '';
            item.foto_asset.size = '';
            jQuery("#foto_asset_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-asset-remove").addClass('hide');
            jQuery("#foto_asset_1").val("");
        } else if (code == 'seri') {
            item.foto_asset_seri.file = "";
            item.foto_asset_seri.name = "";
            item.foto_asset_seri.type = "";
            item.foto_asset_seri.size = "";
            jQuery("#foto_no_seri_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-seri-remove").addClass('hide');
            jQuery("#foto_no_seri_1").val("");
        } else if (code == 'mesin') {
            item.foto_asset_mesin.file = "";
            item.foto_asset_mesin.name = "";
            item.foto_asset_mesin.type = "";
            item.foto_asset_mesin.size = "";
            jQuery("#foto_mesin_thumb_1").prop('src', "{{URL::asset('img/add-img.png')}}");
            jQuery(".btn-foto-mesin-remove").addClass('hide');
            jQuery("#foto_mesin_1").val("");
        }
    }

    function prevPage() {
        var id = (current_page - 1);
        if (validatePage(id)) {
            jQuery(".loading-event").fadeIn();
            if (current_page > 1) {
                current_page--;
                changePage(current_page);
            }
            jQuery(".loading-event").fadeOut();
        }
    }

    function nextPage() {
        var id = (current_page - 1);
        if (validatePage(id)) {
            jQuery(".loading-event").fadeIn();
            if (current_page < numPages()) {
                current_page++;
                changePage(current_page);
            }
            jQuery(".loading-event").fadeOut();
        }
    }

    function changePage(page) {
        /* var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var page_span = document.getElementById("page"); */

        var btn_next = document.getElementsByClassName("btn_next");
        var btn_prev = document.getElementsByClassName("btn_prev");
        var page_span = document.getElementsByClassName("total-page");

        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();
        /*  page_span.innerHTML = page + '/' + request_item_page.length; */
        jQuery('.total-page').text(page + '/' + request_item_page.length);

        if (page == 1) {
            /* btn_prev.style.visibility = "hidden"; */
            jQuery('.btn_prev').addClass('hide');
        } else {
            /* btn_prev.style.visibility = "visible"; */
            jQuery('.btn_prev').removeClass('hide');
        }

        if (page == numPages()) {
            /* btn_next.style.visibility = "hidden"; */
            jQuery('.btn_next').addClass('hide');
        } else {
            /* btn_next.style.visibility = "visible"; */
            jQuery('.btn_next').removeClass('hide');
        }

        assetInfo(current_page);
        jQuery("#foto_asset_1").val("");
        jQuery("#foto_no_seri_1").val("");
        jQuery("#foto_mesin_1").val("");

        jQuery("#detail-item-request-panel").stop().animate({
            scrollTop: 0
        }, 500, 'swing', function() {

        });

    }

    function validatePage(id) {
        var obj = id;
        var key = jQuery('#detail_item_selected').val();
        var request = request_item[key];
        var item = request.detail[obj];
        var valid = true;

        if (item.asset_imei === "") {
            valid = false;
            jQuery('#asset_imei').focus();
            notify({
                type: 'warning',
                message: 'No Mesin / IMEI tidak boleh kosong!'
            });
        }

        if (item.asset_serie_no === "") {
            valid = false;
            jQuery('#asset_serie_no').parent().closest('div').addClass('has-warning');
            notify({
                type: 'warning',
                message: 'No Seri / Rangka tidak boleh kosong!'
            });
        } else {
            jQuery('#asset_serie_no').parent().closest('div').removeClass('has-warning');
        }

        if (item.asset_year === "") {
            valid = false;
            jQuery('#asset_year').parent().closest('div').addClass('has-warning');
            notify({
                type: 'warning',
                message: 'Tahun Asset tidak boleh kosong!'
            });
        } else {
            jQuery('#asset_year').parent().closest('div').removeClass('has-warning');
        }

        if (item.asset_location === "") {
            valid = false;
            jQuery('#asset_location').focus();
            notify({
                type: 'warning',
                message: 'Lokasi Asset tidak boleh kosong!'
            });
        }

        if (item.asset_condition === "") {
            valid = false;
            notify({
                type: 'warning',
                message: 'Kondisi Asset tidak boleh kosong!'
            });
        }

        return valid;
    }

    function numPages() {
        return Math.ceil(request_item_page.length / records_per_page);
    }

    function min(param) {
        if (jQuery("#" + param).val() > 1) {
            if (jQuery("#" + param).val() > 1) jQuery("#" + param).val(+jQuery("#" + param).val() - 1);
        }
    }

    function plus(param) {
        jQuery("#" + param).val(+jQuery("#" + param).val() + 1)
    }

    jQuery('.add').click(function() {
        jQuery(this).prev().val(+jQuery(this).prev().val() + 1);
    });

    jQuery('.sub').click(function() {
        if (jQuery(this).next().val() > 1) {
            if (jQuery(this).next().val() > 1) jQuery(this).next().val(+jQuery(this).next().val() - 1);
        }
    });

    function requestItemData() {
        var total = 0;
        jQuery.each(request_item, function(key, val) {
            if (val.name) {
                total++;
            }
        });
        return total;
    }

    function getBase64(i, file) {
        var reader = new FileReader();
        var base64 = ''
        reader.readAsDataURL(file);
        reader.onload = function() {
            request_docs[i].file = reader.result;
        };
        reader.onerror = function(error) {
            console.log('Error: ', error);
        };
    }

    function data_asset_modal()
    {
        var grid_asset = new Datatable();

        grid_asset.init({
            src: $("#data-table-asset"),
            onSuccess: function(grid_asset) {},
            onError: function(grid_asset) {},
            onDataLoad: function(grid_asset) {},
            destroy: true,
            loadingMessage: 'Loading...',
            dataTable: {
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "bStateSave": true, 
                "lengthMenu": [
                    [5, 20, 50, 100, 150],
                    [5, 20, 50, 100, 150]
                ],
                "pageLength": 5,
                "ajax": {
                    headers: 
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{!! route('get.grid_asset_mutasi') !!}"
                },
                columns: [
                    {
                        data: 'KODE_ASSET_AMS',
                        name: 'KODE_ASSET_AMS'
                    },
                    {
                        data: 'KODE_ASSET_SAP',
                        name: 'KODE_ASSET_SAP'
                    },
                    {
                        data: 'NAMA_MATERIAL',
                        name: 'NAMA_MATERIAL'
                    },
                    {
                        data: 'NAMA_ASSET',
                        name: 'NAMA_ASSET'
                    },
                    {
                        data: 'BA_PEMILIK_ASSET',
                        name: 'BA_PEMILIK_ASSET'
                    },
                    {
                        data: 'LOKASI_BA_DESCRIPTION',
                        name: 'LOKASI_BA_DESCRIPTION'
                    }, 
                    {
                        data: 'ASSET_CONTROLLER',
                        name: 'ASSET_CONTROLLER'
                    },
                    {
                        "render": function(data, type, row) 
                        {
                            return '<a href="javascript:;" style="font-weight:bold" OnClick="get_asset_mutasi(\''+row.KODE_ASSET_AMS+'\',\''+row.NAMA_ASSET+'\',\''+row.ASSET_CONTROLLER+'\',\''+row.BA_PEMILIK_ASSET+'\',\''+row.LOKASI_BA_DESCRIPTION+'\',\''+row.LOKASI_BA_CODE+'\')"><i class="fa fa-plus"></i></a>';
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: [0],
                        width: '15%'
                    },
                    {
                        targets: [1],
                        width: '10%'
                    },
                    {
                        targets: [2],
                        width: '8%'
                    }
                ],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": [],
            },
        });

        $("#data-asset-modal .modal-title").html("<i class='fa fa-document'></i> Data Asset ");
        $('#data-asset-modal').modal('show');
    }

    function get_asset_mutasi(kode_asset_ams,nama_asset,asset_controller,ba_pemilik_asset,lokasi_ba_description, lokasi_ba_code)
    {
        //alert(kode_asset_ams);
        $("#detail_kode_aset").val(kode_asset_ams);
        $("#detail_nama_asset").val(nama_asset);
        $("#detail_ac").val(asset_controller);

        var pemilik = ba_pemilik_asset.substr(0,2);
        var lokasi = lokasi_ba_code.substr(0,2);

        $("#detail_milik_company").val(pemilik);
        $("#detail_milik_area").val(ba_pemilik_asset);
        
        $("#detail_lokasi_company").val(lokasi);
        $("#detail_lokasi_area").val(lokasi_ba_description);
        
        $("#detail_tujuan_company").val(lokasi);

        var data_tujuan = $.parseJSON(JSON.stringify(dataJson('{!! route("get.tujuan_business_area") !!}?type='+lokasi)));
        $('input[name="detail_tujuan_area"]').select2({
            data: data_tujuan,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });

        $('#data-asset-modal').modal('toggle');
    }

    function validate_additem(ac,kode_asset_ams,kepemilikan_area,request_item)
    {
        //console.log(request_item);
        //alert(ac.val());

        var valid = true;
        
        $.each(request_item, function(key, val) 
        {
            //alert(val.detail_ac);
            //if( val.detail_ac === "undefined" ){
                if( val.detail_ac != ac.val() )
                {
                    notify({
                        type: 'warning',
                        message: 'Asset Controller tidak sama'
                    });

                    valid = false;
                    return valid;
                }
                //return valid;
            //}

            
            //if( val.kode_aset === "undefined" ){
                if( val.kode_aset == kode_asset_ams.val() )
                {
                    notify({
                        type: 'warning',
                        message: 'Kode Asset AMS sudah diinput'
                    });

                    valid = false;
                    return valid;
                }
            //}

            //if( val.detail_milik_area === "undefined" ){
                if( val.detail_milik_area != kepemilikan_area.val() )
                {
                    notify({
                        type: 'warning',
                        message: 'Kepemilikan Business Area tidak sama'
                    });

                    valid = false;
                    return valid;
                }
            //}
            
            return valid;
        });
        
        return valid;
    }

    function reload_page(){window.location.href = "{{ url('/mutasi/create/2') }}";}

    function remove_data_temp(kode_asset_ams) 
    {
        
        var conf = confirm("Are you sure you want to delete this data?");
        if (conf == true) 
        {
            var param = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('mutasi/delete_data_temp_sewa') }}",
                method: "POST",
                data: param+"&kode_asset_ams="+kode_asset_ams,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status); return false;
                    if (result.status) 
                    {
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        setTimeout(reload_page, 500); 
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
    }

    function delete_berkas(kode_asset_ams,file_category)
    {
        var param = '';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('mutasi/delete_berkas_temp') }}",
            method: "POST",
            data: param+"&kode_asset_ams="+kode_asset_ams+"&file_category="+file_category,
            beforeSend: function() {
                $('.loading-event').fadeIn();
            },
            success: function(result) 
            {
                //alert(result.status); return false;
                if (result.status) 
                {
                    $("#file-berkas-"+kode_asset_ams+"").hide();
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

</script>

@stop