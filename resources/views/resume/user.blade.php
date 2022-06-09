@extends('adminlte::page')
@section('title', 'Resume User - FAMS Web TAP 2019')
@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Resume User Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
              
              <div class="box-body">
                
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Username Old</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="user-role-old" name="user-role-old">
                                    <option value="">Pilih Role</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" id="user-id-old" name="user-id-old">
                                    <option value="">Pilih User</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="btn bg-red btn-flat margin" style="margin-top:0px">TO</div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Username New</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="user-id-new" name="user-id-new">
                                    <option value="">Pilih User</option>
                                </select>
                            </div>
                        </div>
                    </div>                    

                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <a href="{{url('/')}}"><button type="button" class="btn btn-default pull-right" style="margin-left:5px">Exit</button></a>
                <button type="button" class="btn btn-info pull-right" OnClick="submit_resume()">Resume</button>
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
$(document).ready(function(){

var user_role = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_role_resume") !!}')));
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
});

});

function submit_resume()
{
    var user_role_old = $("#user-role-old").val();
    var user_id_old = $("#user-id-old").val();
    var user_id_new = $("#user-id-new").val();
    //alert(no_document); return false;
    var param = '';

    if( $.trim(user_role_old) == "" )
    {
        notify({
            type: 'warning',
            message: " User Role is required"
        });
        return false;
    }

    if( $.trim(user_id_old) == "" )
    {
        notify({
            type: 'warning',
            message: " User Old is required"
        });
        return false;
    }

    if( $.trim(user_id_new) == "" )
    {
        notify({
            type: 'warning',
            message: " User New is required"
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