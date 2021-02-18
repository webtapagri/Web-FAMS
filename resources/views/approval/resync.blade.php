<?php 
//echo "<pre>"; print_r($data['list_kategori_upload']); die();
?>

@extends('adminlte::page')
@section('title', 'FAMS - Resynchronize')

@section('content_header')

<h1>Resynchronize Asset</h1><br/>
@stop	

@section('content')



<div class="row">

	<div class="col-md-5">
		<form action="{{ url(('/resynchronize')) }}" method="post" id="form" onsubmit="return validate(this);">

			{!! csrf_field() !!}
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="box box-default">

				@if(Session::has('alert'))
					@section('js')
					<script type="text/javascript">
						//alert("okesip");
						notify({
							type: 'warning',
							message: "{{ Session::get('alert') }}"
						});

					</script>
					@stop
				@endif
				
				@if(Session::has('message'))</div-->
					@section('js')
					<script type="text/javascript">
						
						notify({
								type: 'success',
								message: "{{ Session::get('message') }}"
							});

					</script>
					@stop 

				@endif

				<div class="box-header with-border">
					<h3 class="box-title">Document Code</h3>
				</div>

				

				<div class="box-body">

					<input class="form-control" id="noreg" name="noreg" type="text"  placeholder="Insert Document Code">

				</div>

				<div class="box-footer clearfix">
					<button type="submit" class="btn btn-danger pull-right"> PROSES</button>
				</div>

			</div>

		</form>

	</div>
</div>
@stop

@section('js')
<script type="text/javascript">

	function validate(form) 
	{
		var valid = true;

		if(!valid) {
			alert('Please correct the errors in the form!');
			return false;
		}
		else {
			return confirm('Confirm proses resynchronize ?');
		}
	}

</script>
@stop