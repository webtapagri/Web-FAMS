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
<section class="content">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-plus"></i> Pendaftaran {{ $type }}</h3>
                </div>
               <form class="form-horizontal">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-body">
                            <div class="form-group">
                                    <label for="plant" class="col-md-2">Tipe Transaksi</label>
                                        <div class="col-md-4">
                                        <input type="text" class="form-control input-sm attr-material-group" name="description" id="description" autocomplete="off">
                                    </div>    
                                </div>
                            <div class="form-group">
                                <label for="plant" class="col-md-2">Tanggal</label>
                                    <div class="col-md-4">
                                    <input type="text" class="form-control input-sm attr-material-group" name="description" id="description" autocomplete="off">
                                </div>    
                            </div>
                            <div class="form-group">
                                <label for="plant" class="col-md-2">Business Area</label>
                                    <div class="col-md-4">
                                    <input type="text" class="form-control input-sm attr-material-group" name="description" id="description" autocomplete="off">
                                </div>    
                            </div>
                                
                        </div>	 
                        <div class="box-footer clearfix">
                                <button type="button" class="btn btn-default btn-flat hide pull-right" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success btn-flat pull-right" style="margin-right: 5px;">Next</button>
                                <button type="button" class="btn btn-danger btn-flat btn-cancel pull-right" style="margin-right: 5px;">Cancel</button>
                            </div> 
                    </div>
               </form>
            </div>    
        <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>     
</div>
@stop
@section('js')
<script>
    var imgFiles = [];    
    var addFile = 2;
    jQuery(document).ready(function() {
        jQuery(".btn-cancel").on('click', function() {
            window.location.href = "{{ url('materialrequest') }}";
        });

        jQuery('#form-basic-data').on('submit', function(e) {
            e.preventDefault();
           jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form = jQuery('#form-initial').find('input, select, textarea').appendTo('#form-basic-data');
            var param = new FormData(this);
            jQuery.ajax({
				url:"{{ url('materialrequest/post') }}",
			    type:"POST",
				data: param,
				contentType:false,
				processData:false,
				cache:false,
				beforeSend:function(){jQuery('.loading-event').fadeIn();},
				success:function(result){
                    if(result.status){
                        notify({
                            type:'success',
                            message:result.message
                        });
                        window.location.href = "{{ url('mastermaterial') }}";
                    }else{
                        notify({
                            type:'warning',
                            message:result.message
                        });
                    } 
				},
				complete:function(){jQuery('.loading-event').fadeOut();}
			 });
        });

    });

    function openFile(id) {
        jQuery("#files_" + id).trigger('click');
    }

    function initialPanel() {
        jQuery('.panel-initial').attr("data-toggle","tab");
        jQuery('.panel-initial').click();
        jQuery('.panel-basic-data').removeAttr("data-toggle");
        jQuery('.panel-image').removeAttr("data-toggle");

        topFunction();
    }
  
    function basicDataPanel() {
        jQuery('.panel-basic-data').attr("data-toggle","tab");
        jQuery('.panel-basic-data').click();

        jQuery('.panel-initial').removeAttr("data-toggle");
        jQuery('.panel-image').removeAttr("data-toggle");
        topFunction();
    }
  
    function imagePanel() {
        jQuery('.panel-image').attr("data-toggle","tab");
        jQuery('.panel-image').click();

        jQuery('.panel-initial').removeAttr("data-toggle");
        jQuery('.panel-basic-data').removeAttr("data-toggle");
    }

    function showImage(id) {
         var src = document.getElementById("files_" + id);
        var target = document.getElementById("material-images-" + id);
        var fr=new FileReader();
        fr.onload = function(e) { target.src = this.result; };
        fr.readAsDataURL(src.files[0]);
        imgFiles.push(src.files[0]);
        jQuery('.btn-remove-image' + id).removeClass('hide');
        var status = jQuery('#material-images-' + id).data('status');

        if(status === 0) {
            genAddFile();
            jQuery('#material-images-' + id).data('status', 1);
        }
    }

    function removeImage(id) {
        var input = jQuery( "input:file");
        jQuery('#panel-image-' + id).remove();
    }


    function genAddFile() {
         var input = jQuery( "input:file");
        if (input.length == 10) {
            notify({
                type: 'warning',
                message: "max file image is 10"
            });
        } else {
            var content = '';
            content +='<div class="col-md-4" id="panel-image-' + addFile + '">';
            content +='<div class="form-group hide">';
            content +='<input type="file" id="files_' + addFile + '" name="files_' + addFile + '" accept="image/*"  OnChange="showImage(' + addFile + ')">';
            content +='<p class="help-block">*jpg, png</p>';
            content +='</div>';
            content +='<div class="image-group">';
            content +='<button type="button" class="btn btn-danger btn-xs btn-flat btn-add-file-image btn-remove-image' + addFile + ' hide" OnClick="removeImage(' + addFile + ')"><i class="fa fa-trash"></i></button>';
            content +='<img id="material-images-' + addFile + '" title="click to change image"  data-status="0" style="cursor:pointer" OnClick="openFile(' + addFile + ')" class="img-responsive select-img" src="{{URL::asset('img/add-img.png')}}">';
            content +='</div>'; 
            content +='</div>'; 

            jQuery('#filesContainer').append(content);
            addFile++;
        }
    }



</script>            
@stop