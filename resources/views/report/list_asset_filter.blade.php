@extends('adminlte::page')
@section('title', 'Report List Asset - FAMS Web TAP 2019')
@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-search"></i>  FILTER</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" id="freport" method="POST" action="{{ url('/report/list-asset/submit') }}" target="_blank">
              {{ csrf_field() }}
              <div class="box-body">
                
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Kode Aset Fams</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="kode-aset-fams" name="kode-aset-fams" placeholder="isi kode aset fams" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Kode Aset SAP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="kode-aset-sap" name="kode-aset-sap" placeholder="isi kode aset sap" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Kode Aset Controller</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="kode-aset-controller" name="kode-aset-controller" placeholder="isi kode aset controller" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Nama Aset</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="nama-aset" name="nama-aset" placeholder="isi nama aset" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Asset Class</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="asset-class" name="asset-class" placeholder="isi asset class" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="status" name="status" placeholder="isi status" value="">
                            </div>
                        </div>
                    </div>
                        
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Jenis Aset</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="jenis-asset" name="jenis-asset" onChange="get_group()">
                                    <option value="">Pilih Jenis Aset</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Group Aset</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="group-asset" name="group-asset" onChange="get_subgroup()">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">SubGroup Aset</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="subgroup-asset" name="subgroup-asset">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Bisnis Area Pemilik Aset</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="milik-aset" name="milik-aset">
                                    <option value=""></option>
                                </select>
                                <?php /* <input type="text" class="form-control" id="milik-aset" name="milik-aset" placeholder="isi bisnis area pemilik aset" value=""> */ ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Bisnis Area Lokasi Aset</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="lokasi-aset" name="lokasi-aset">
                                    <option value=""></option>
                                </select>
                                <?php /* <input type="text" class="form-control" id="lokasi-aset" name="lokasi-aset" placeholder="isi bisnis area lokasi aset" value=""> */ ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">No. of List</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no-of-list" name="no-of-list" placeholder="isi no of list" value="25" required="required">
                            </div>
                        </div>
                    </div>                    

                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <a href="{{url('/')}}"><button type="button" class="btn btn-default pull-right" style="margin-left:5px">Exit</button></a>
                <a href="#"><button type="button" class="btn btn-default pull-right"  OnClick="download()" style="margin-left:5px"><i class="fa fa-file-excel-o"></i> Export</button></a>
                <input type="submit" class="btn btn-info pull-right" xOnClick="submit_filter()" value="Search">
              </div>
              <!-- /.box-footer -->
            
            </form>
          </div>
        </div>
    </div>
</section>
@stop
@section('js')

<script>
$(document).ready(function()
{



var jenis_asset = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset_code") !!}')));
//$('#jenis_asset-'+no+'').select2({
$('#jenis-asset').select2({    
    data: jenis_asset,
    width: '100%',
    placeholder: '',
    allowClear: true,
});

var bisnis_area = $.parseJSON(JSON.stringify(dataJson('{!! route("get.generaldataplant") !!}')));
//$('#jenis_asset-'+no+'').select2({
$('#milik-aset, #lokasi-aset').select2({    
    data: bisnis_area,
    width: '100%',
    placeholder: '',
    allowClear: true,
});

/*var user_role = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_role_resume") !!}')));
$("#user-role-old").select2({
    data: user_role,
    width: "100%",
    allowClear: true,
    placeholder: 'Pilih Role'
}).on('change', function() 
{
    var user_id = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_user_resume") !!}?type=' + jQuery(this).val())));
    jQuery("#user-id-old, #user-id-new").empty().select2({
        data: user_id,
        width: "100%",
        allowClear: true,
        placeholder: ' '
    });
});*/

});

function download(){
	console.log(123);
	
	$('#freport').attr('action',"{{ url('/report/list-asset/download') }}");
	$('#freport').submit();
	$('#freport').attr('action',"{{ url('/report/list-asset/submit') }}");
	
	return false;
}

function get_group()
{
    var jenis_asset_code = $('#jenis-asset').val();
    //alert(jenis_asset_code);
    var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type='+jenis_asset_code )));
    $('#group-asset').empty().select2({
        data: assetgroup,
        width: "100%",
        allowClear: true,
        placeholder: ' '
    });
}

function get_subgroup()
{
    var jenis_asset_code = $('#jenis-asset').val();
    var group = $('#group-asset').val();
    var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group='+group+'&jenis_asset_code='+jenis_asset_code )));
        $('#subgroup-asset').empty().select2({
            data: assetsubgroup,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });
}

function submit_filter()
{
    var no_of_list = $("#no-of-list").val();
    var user_id_old = $("#user-id-old").val();
    var user_id_new = $("#user-id-new").val();
    //alert(no_document); return false;
    var param = '';

    if( $.trim(no_of_list) == "" )
    {
        notify({
            type: 'warning',
            message: " No of List is required"
        });
        return false;
    }

    if(confirm('Confirm Resume User ?'))
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('/resume/user-submit') }}",
            method: "POST",
            data: param+"&user_id_old="+user_id_old+"&user_id_new="+user_id_new,
            beforeSend: function() {
                $('.loading-event').fadeIn();
            },
            success: function(result) 
            {
                if (result.status) 
                {
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
}

</script>

@stop