<?php //echo "<pre>"; print_r(session()->all()); die(); ?>

@extends('adminlte::page')
@section('title', 'FAMS - Download Master General QRCode')
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="table-container small">
					
					<h2>Download QRCode</h2>
					
					@if ($errors->any())
						<div class="alert alert-danger no-border">
							Terdapat error:
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					<form action="{{ route('download_masterasset_qrcode') }}" method="post">
						@csrf
						<div class="form-group">
							<label for="">From</label>
							<input type="text" name="from" id="id" class="form-control">
						</div>
						<div class="form-group">
							<label for="">To</label>
							<input type="text" name="to" id="di" class="form-control">
						</div>
						<div class="form-group col-xs-6">
							<label for=""></label>
							<input type="submit" name="submit" value="Export" class="form-control btn btn-primary">
						</div>
						<div class="form-group col-xs-6">
							<label for=""></label>
							<!-- <input type="submit" name="submit" value="Print" class="form-control btn btn-success"> -->
							<input type="button" name="paper-size" id="paper-size" value="Print" class="form-control btn btn-success" data-toggle="modal" data-target="#paper-modal">
						</div>
					</form>
				
				</div>

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

<!-- modal to select paper size -->
<div id="paper-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Please select paper size:</h4>
            </div>
            <div class="modal-body">
                <div class="xbox-body">
                    <!--div class="generate-qrcode text-center"></div-->
                    <div class="xvisible-print text-center">      
						<form action="{{ route('download_masterasset_qrcode') }}" method="post">
						
						@csrf
							<input type="hidden" name="from" id="from2" class="form-control">
							<input type="hidden" name="to" id="to2" class="form-control">

							<input type="radio" name="paper" value="reg"> 5 x 7 cm<br><br>
							<input type="radio" name="paper" value="small"> 4 x 5 cm<br><br>
							<input type="submit" name="submit" value="Print" class="form-control btn btn-success">
						</form>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div> -->
            </form>
        </div>
    </div>
</div>


@stop
@section('js')
   
<script type="text/javascript">
  $(document).ready(function () {
            $('#paper-size').click(function () {
				$('#from2').val($('#id').val());
				$('#to2').val($('#di').val());
			});
        });
</script>

@stop