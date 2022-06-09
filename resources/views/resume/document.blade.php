@extends('adminlte::page')
@section('title', 'Resume Document - FAMS Web TAP 2019')
@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Resume Document Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="" class="col-sm-3 control-label">No. Document</label>

                  <div class="col-sm-9">
                        <input type="text" class="form-control" id="no-document" name="no-document" placeholder="Isi No. Document">
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
$(document).ready(function() {});

function submit_resume()
{
    var no_document = $("#no-document").val();
    //alert(no_document); return false;
    var param = '';

    if( $.trim(no_document) < 2 )
    {
        notify({
            type: 'warning',
            message: " No. Document is required (min 2 char)"
        });
        return false;
    }

    if(confirm('Confirm Resume No. Document '+no_document+' ?'))
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('/resume/document-submit') }}",
            method: "POST",
            data: param+"&no_document="+no_document,
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