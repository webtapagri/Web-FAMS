<?php 
    $user_role = Session::get('role');
    $user_id = Session::get('user_id');
    $user_area_code = Session::get('area_code');
    if( !empty($_GET) )
    {
        $email_noreg = base64_decode($_GET['noreg']);
        $email_noreg = str_replace("-", "/", $email_noreg);
    }
    else
    {
        $email_noreg = "";
    }
?>

@extends('adminlte::page')
@section('title', 'FAMS - approval')

@section('content')

<style>
.fa-eye, .fa-trash {cursor:pointer;}{}
.mutasi{
    width:90%;
}
</style>

<div class="row" style="margin-top:-3%">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            TASK
            <small>Approval</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Task Approval</li>
        </ol>
    </section><br/>

<div class="col-md-12">
  <!-- Custom Tabs -->
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">OUTSTANDING</a></li>
      <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">HISTORY</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <div class="small table-container">
                
                <div class="table-scroll">

                <div class="table-actions-wrapper">
                    <button class="btn btn-flat btn-sm btn-flat label-danger btn-refresh refresh-outstanding"><i class="glyphicon glyphicon-refresh" title="Refresh"></i></button><?php /* <div OnClick="approval('19.06/AMS/PDFA/00027')">test</div>*/ ?>
                </div>
                <table id="data-table" class="table table-bordered table-condensed">
                        <thead>
                            <tr role="row" class="heading">
                                <th>DOCUMENT CODE</th>
                                <th>TIPE</th>
                                <th>PO</th>
                                <th>NO PO</th>
                                <th>TGL PENGAJUAN</th>
                                <th>REQUESTOR</th>
                                <th>TGL PO</th>
                                <th>KODE VENDOR</th>
                                <th>NAMA VENDOR</th>
                                <th>BERKAS</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" data-column="1" class="form-control input-xs form-filter" name="NO_REG" id="NO_REG"></th>
                                <th>
                                    <select data-column="2" class="form-control input-xs form-filter" name="TYPE" id="TYPE">
                                        <option></option>
                                    </select>
                                </th>
                                <th>
                                    <select data-column="3" class="form-control input-xs form-filter" name="PO_TYPE" id="PO_TYPE">
                                        <option></option>
                                    </select>
                                </th>
                                <th><input type="text" data-column="4" class="form-control input-xs form-filter" name="NO_PO" id="NO_PO"></th>
                                <th><input type="text" data-column="5" class="form-control input-xs form-filter datepicker" name="REQUEST_DATE" id="REQUEST_DATE" autocomplete="off"></th>
                                <th><input type="text" data-column="6" class="form-control input-xs form-filter" name="REQUESTOR"></th>
                                <th><input type="text" data-column="7" class="form-control input-xs form-filter datepicker" name="PO_DATE" id="PO_DATE" autocomplete="off"></th>
                                <th><input type="text" data-column="8" class="form-control input-xs form-filter" name="VENDOR_CODE"></th>
                                <th><input type="text" data-column="9" class="form-control input-xs form-filter" name="VENDOR_NAME"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                </table>
                
                </div>

            </div>
        </div>
      <!-- /.tab-pane -->
      <div class="tab-pane" id="tab_2">
            <div class="small table-container">
                <div class="table-scroll">
                    <div class="table-actions-wrapper">
                        <button class="btn btn-flat btn-sm btn-flat label-danger btn-refresh refresh-history"><i class="glyphicon glyphicon-refresh" title="Refresh"></i></button><?php /* <div OnClick="history('19.06/AMS/PDFA/00009')">test</div> */ ?>
                    </div>
                    <table id="data-table-history" class="table table-bordered table-condensed">
                        <thead>
                            <tr role="row" class="heading">
                                <th>DOCUMENT CODE</th>
                                <th>AREA CODE</th>
                                <th>ROLE NAME</th>
                                <th>STATUS DOCUMENT</th>
                                <th>DATE</th>
                                <th>BERKAS</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th><input type="text" class="form-control input-xs form-filter" name="document_code"></th>
                                <th>
                                    <input type="text" class="form-control input-xs form-filter" name="area_code">
                                </th>
                                <th><input type="text" class="form-control input-xs form-filter" name="name"></th>
                                <th><input type="text" class="form-control input-xs form-filter" name="status_dokumen"></th>
                                <th><input type="text" class="form-control input-xs form-filter datepicker" name="date_history" id="date_history" autocomplete="off"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
      </div>
      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
  </div>
  <!-- nav-tabs-custom -->
</div>
<!-- /.col -->
</div>

<div id="approve-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TYPE TRANSAKSI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="type-transaksi" name="type-transaksi" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">JENIS PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="po-type" name="po-type" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">KODE VENDOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="kode-vendor" name="kode-vendor" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BUSINESS AREA</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="business-area" name="business-area" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NAMA VENDOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="nama-vendor" name="nama-vendor" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        
                        <br>
                        <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                        <div class="form-group">
                            <div class="col-md-12 table-scroll" id="box-item-detail">
                            </div>
                        </div>
                        
                        <!-- ITEM DETAIL OUTSTANDING -->
                        <div id="box-detail-item"></div>

                        <!-- LOG HISTORY OUTSTANDING --> 
                        <div id="log-history-box-outstanding" class="table-scroll"></div>

                        <div class="form-group">
                            <label class="col-md-2">NOTE</label>
                            <div class="col-md-8">
                                <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification" id="specification"></textarea>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($user_role == 'AC'){ ?> 
                   <span id="create-button-sync-sap"></span>
                <?php }  ?>
                <?php if($user_role != 'Super Administrator'){ if($data['outstanding'] != 0 ){ ?> 
                    <span id="button-approve">
                        <button type="button" class="btn btn-flat label-danger" OnClick="changeStatus('A')" style="margin-right: 5px;">APPROVE</button>
                    </span>
                    <button type="button" class="btn btn-flat label-danger button-reject" OnClick="changeStatus('R')" style="margin-right: 5px;">REJECT</button>
                <?php }} ?>
                
            </div>
            </form>
        </div>
    </div>
</div>

<div id="history-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form-history" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TYPE TRANSAKSI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="type-transaksi" name="type-transaksi" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">JENIS PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="po-type" name="po-type" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">KODE VENDOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="kode-vendor" name="kode-vendor" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BUSINESS AREA</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="business-area" name="business-area" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NAMA VENDOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="nama-vendor" name="nama-vendor" readonly>
                                    </div>
                                </div>
                            </div>
                        
                            <br>
                            <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                            <div class="form-group">
                                <div class="col-md-12 table-scroll" id="box-item-detail-history"></div>
                            </div>
                            
                            <!-- ITEM DETAIL HISTORY -->
                            <div id="box-detail-item-history"></div>

                            <div class="form-group" id="history-notes">
                                <label class="col-md-2"><span class="label bg-blue"><i class="fa fa-bars"></i> NOTE</span></label>
                                <div class="col-md-8">
                                    <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification" id="specification" readonly></textarea>
                                </div>
                            </div>

                            <!-- LOG HISTORY -->
                            <div id="log-history-box" class="table-scroll"></div>
                        
                        </div><!-- ROW -->                        
                        
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="pdf-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i id="modalHeader"></i></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div id="approve-disposal-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BUSINESS AREA</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="business-area" name="business-area" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>                        
                        
                        <br>
                        <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                        <div class="form-group">
                            <div class="col-md-12" id="box-item-detail-disposal">
                            </div>
                        </div>
                        
                        <!-- ITEM DETAIL OUTSTANDING -->
                        <div id="box-detail-item-disposal"></div>

                        <!-- LOG HISTORY OUTSTANDING --> 
                        <div id="log-history-box-disposal" class="table-scroll"></div>

                        <div class="form-group">
                            <label class="col-md-2">NOTE</label>
                            <div class="col-md-8">
                                <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification-disposal" id="specification-disposal"></textarea>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($user_role == 'AC'){ ?> 
                   <span id="create-button-sync-sap"></span>
                <?php }  ?>
                <?php if($user_role != 'Super Administrator'){ if($data['outstanding'] != 0 ){ ?> 
                    <span id="button-approve">
                        <button type="button" class="btn btn-flat label-danger" OnClick="changeStatusDisposal('A')" style="margin-right: 5px;">APPROVE</button>
                    </span>
                    <button type="button" class="btn btn-flat label-danger button-reject" OnClick="changeStatusDisposal('R')" style="margin-right: 5px;">REJECT</button>
                <?php }} ?>
                
            </div>
            </form>
        </div>
    </div>
</div>

<div id="history-modal-disposal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form-history" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BUSINESS AREA</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="business-area" name="business-area" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                        
                            <br>
                            <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                            <div class="form-group">
                                <div class="col-md-12" id="box-item-detail-history-disposal"></div>
                            </div>
                            
                            <!-- ITEM DETAIL HISTORY -->
                            <div id="box-detail-item-history-disposal"></div>

                            <div class="form-group" id="history-notes-disposal">
                                <label class="col-md-2"><span class="label bg-blue"><i class="fa fa-bars"></i> NOTE</span></label>
                                <div class="col-md-8">
                                    <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification-disposal" id="specification-disposal" readonly></textarea>
                                </div>
                            </div>

                            <!-- LOG HISTORY -->
                            <div id="log-history-box-disposal-history" class="table-scroll"></div>
                        
                        </div><!-- ROW -->                        
                        
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="history-modal-mutasi" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form-history" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">PEMILIK ASET</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="ba-pemilik-asset" name="ba-pemilik-asset" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BA TUJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="ba-tujuan" name="ba-tujuan" readonly >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">COST CENTER</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="cost-center" name="cost-center" readonly >
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                        
                            <br>
                            <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                            <div class="form-group">
                                <div class="col-md-12" id="box-item-detail-history-mutasi"></div>
                            </div>
                            
                            <!-- ITEM DETAIL HISTORY -->
                            <div id="box-detail-item-history-mutasi"></div>

                            <div class="form-group" id="history-notes-mutasi">
                                <label class="col-md-2"><span class="label bg-blue"><i class="fa fa-bars"></i> NOTE</span></label>
                                <div class="col-md-8">
                                    <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification-mutasi" id="specification-mutasi" readonly></textarea>
                                </div>
                            </div>

                            <!-- LOG HISTORY -->
                            <div id="log-history-box-mutasi" class="table-scroll"></div>
                        
                        </div><!-- ROW -->       

                             


                        
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="approve-mutasi-modal" class="modal fade" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-lg mutasi">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="request-form" class="form-horizontal" style="font-size:13px !important">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">NO REGISTRASI</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control xinput-sm" value="" id="no-reg" name="no-reg" readonly>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">TANGGAL PENGAJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="tanggal-reg" name="tanggal-reg" readonly>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">PEMILIK ASET</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="ba-pemilik-asset" name="ba-pemilik-asset" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">REQUESTOR</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="requestor" name="requestor" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">BA TUJUAN</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-sm" value="" id="ba-tujuan" name="ba-tujuan" readonly >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plant" class="col-md-4">COST CENTER</label>
                                    <div class="col-md-6">
                                    <?php 
                                    if( $user_role == 'AMS'){ 
                                        if($data['outstanding']!= 0) { 
                                            $readonly = "";
                                        }
                                        else{
                                            $readonly = "readonly"; 
                                        }
                                    }else{
                                        $readonly = "readonly";
                                    }
                                    ?>
                                        <input type="text" class="form-control input-sm" value="" id="cost-center" name="cost-center" <?php echo $readonly; ?> readonly >
                                        <input type="hidden" class="form-control input-sm" value="" id="kode-asset-ams" name="kode-asset-ams" >
                                    </div>
                                </div>
                            </div>
                            
                        </div>  
                        
                        <br>
                        <span class="label bg-blue"><i class="fa fa-bars"></i> ITEM DETAIL</span> <br/><br/>
                        <div class="form-group">
                            <div class="col-md-12" id="box-item-detail-mutasi">
                            </div>
                        </div>
                        
                        <!-- ITEM DETAIL OUTSTANDING -->
                        <div id="box-detail-item-mutasi"></div>

                        <!-- LOG HISTORY OUTSTANDING --> 
                        <div id="log-history-outstanding-mutasi" class="table-scroll"></div>

                        <div class="form-group">
                            <label class="col-md-2">NOTE</label>
                            <div class="col-md-8">
                                <textarea type="text" class="form-control input-sm attr-material-group" row="3" name="specification-mutasi-approval" id="specification-mutasi-approval"></textarea>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($user_role == 'AC'){ ?> 
                   <span id="create-button-sync-sap-mutasi"></span>
                <?php }  ?>
                <?php if($user_role != 'Super Administrator'){ if($data['outstanding'] != 0 ){ ?> 
                    <span id="button-approve-mutasi">
                        <button type="button" class="btn btn-flat label-danger" OnClick="changeStatusMutasi('A')" style="margin-right: 5px;">APPROVE</button>
                    </span>
                    <button type="button" class="btn btn-flat label-danger button-reject-mutasi" OnClick="changeStatusMutasi('R')" style="margin-right: 5px;">REJECT</button>
                <?php }} ?>
                
            </div>
            </form>
        </div>
    </div>
</div>

@stop
@section('js')

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
<script>
    var request_item = [];
    var bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    var request_kode_aset_data = [];
    var request_check_gi = [];
    var txtasset = [];

    $(document).ready(function() 
    {
        <?php

            if($email_noreg != '')
            {
                if (strpos($email_noreg, 'PDFA') !== false) 
                {
                    echo " approval('{$email_noreg}') ";
                }
                else if (strpos($email_noreg, 'DSPA') !== false) 
                {
                    echo " approval_disposal('{$email_noreg}') ";
                }
                else
                {
                    echo " approval_mutasi('{$email_noreg}') ";
                }
            } 
        ?>

        $("#box-detail-item").fadeIn();

        $.ajaxSetup({
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
                    url: "{!! route('get.approval_grid') !!}"
                },
                columns: [
                    {
                        "render": function(data, type, row) 
                        {
                            var no_registrasi= row.NO_REG.replace(/\//g, '-');
                            var disposal = no_registrasi.includes("DSPA");
                            var mutasi = no_registrasi.includes("MTSA");

                            if(disposal)
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="approval_disposal(\'' + no_registrasi + '\')">' + row.NO_REG + '</a>'; 
                            }
                            else if(mutasi)
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="approval_mutasi(\'' + no_registrasi + '\')">' + row.NO_REG + '</a>';
                            }
                            else
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="approval(\'' + no_registrasi + '\')">' + row.NO_REG + '</a>';
                            }
                            
                        }
                    }, {
                        "render": function(data, type, row) 
                        {

                            if (row.TYPE == 1) {
                                var content = 'Barang'
                            } else if (row.TYPE == 2) {
                                var content = 'Jasa'
                            } else if (row.TYPE == 3) {
                                var content = 'lain-lain'
                            } else {
                                var content = '-'
                            }

                            return content;
                        }
                    }, {
                        "render": function(data, type, row) 
                        {
                            var no_registrasi= row.NO_REG.replace(/\//g, '-');
                            var mutasi = no_registrasi.includes("MTSA");

                            if (row.PO_TYPE == 0) {
                                var content = '<span class="label label-primary">SAP</span>';
                            } else if (row.PO_TYPE == 1) {
                                var content = '<span class="label label-danger">AMP</span>';
                            }else if (row.PO_TYPE == 2) {
                                var content = '<span class="label label-warning">ASSET LAIN</span>';
                            }else if (mutasi) {
                                var content = '<span class="label label-success">MUTASI</span>';
                            }else{
                                var content = '<span class="label label-default">DISPOSAL</span>';
                            }

                            return content;
                        }
                    },
                    {
                        data: 'NO_PO',
                        name: 'NO_PO'
                    },
                    {
                        // "render": function(data, type, row) 
                        // {
                        //     var req= row.REQUEST_DATE;

                        //     if (req == 0) {
                        //         var content = 'tidak ada';
                        //     }else{
                        //         var content = req;
                        //     }
                        //     return content;
                        // }
                        data: 'REQUEST_DATE',
                        "render" : function(data){
                                if(moment(data).isValid()){
                                    var content =  moment(data).format('D MMM YYYY');
                                   return content;
                                } 
                                else {
                                    var content = "";
                                    return content;
                                }
                            }
                    },
                    {
                        data: 'REQUESTOR',
                        name: 'REQUESTOR'
                    },
                    {
                        data: 'PO_DATE',
                        "render" : function(data){
                                if(moment(data).isValid()){
                                    var content =  moment(data).format('D MMM YYYY');
                                   return content;
                                } 
                                else {
                                    var content = "";
                                    return content;
                                }
                            }
                    },
                    {
                        data: 'VENDOR_CODE',
                        name: 'VENDOR_CODE'
                    },
                    {
                        data: 'VENDOR_NAME',
                        name: 'VENDOR_NAME'
                    },  
                    {
                        "render": function(data, type, row) 
                        {
                            var content = '';
                            var no_registrasi= btoa(row.NO_REG);
                            var noreg = row.NO_REG.replace(/\//g, '-');
                            var dspa = noreg.includes("DSPA");
                            var mutasi = noreg.includes("MTSA");

                            if (row.PO_TYPE == 1 || row.PO_TYPE == 2) 
                            {
                                content += '<a href="{{ url("approval/berkas-amp") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else if( dspa )
                            {
                                //alert("berkas");
                                content += '<a href="{{ url("approval/berkas-disposal") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else if( mutasi )
                            {
                                //alert("berkas");
                                content += '<a href="{{ url("approval/berkas-mutasi") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else
                            {
                                content += '-';
                            }

                            return content;
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
                    },
                    {
                        orderable: true,
                        type: "date-dd MMM yyyy",
                        targets: [4]
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

        $("#PO_TYPE").select2({
            data: [{
                    id: 0,
                    text: 'SAP'
                },
                {
                    id: 1,
                    text: 'AMP'
                },
                {
                    id: 2,
                    text: 'ASET LAIN'
                },
            ],
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        $("#TYPE").select2({
            data: [{
                    id: 1,
                    text: 'Barang'
                },
                {
                    id: 2,
                    text: 'Jasa'
                },
                {
                    id: 3,
                    text: 'Lain-lain'
                },
            ],
            width: '100%',
            placeholder: ' ',
            allowClear: true
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var grid_history = new Datatable();
        grid_history.init({
            src: $("#data-table-history"),
            onSuccess: function(grid_history) {},
            onError: function(grid_history) {},
            onDataLoad: function(grid_history) {},
            destroy: true,
            loadingMessage: 'Loading...',
            dataTable: 
            {
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [50, 100, 250, 500, 1000],
                    [50, 100, 250, 500, 1000]
                ],
                "pageLength": 50,
                "ajax": {
                    url: "{!! route('get.approval_grid_history') !!}"
                },
                columns: [{
                        "render": function(data, type, row) 
                        {
                            var no_registrasi= row.document_code.replace(/\//g, '-');
                            var disposal = no_registrasi.includes("DSPA");
                            var mutasi = no_registrasi.includes("MTSA");

                            if(disposal)
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="history_disposal(\'' + no_registrasi + '\')">' + row.document_code + '</a>';
                            }
                            else if(mutasi)
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="history_mutasi(\'' + no_registrasi + '\')">' + row.document_code + '</a>';
                            }
                            else
                            {
                                return '<a href="javascript:;" style="font-weight:bold" OnClick="history(\'' + no_registrasi + '\')">' + row.document_code + '</a>';
                            }
                            
                        }
                    }, 
                    {
                        data: 'area_code',
                        name: 'area_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status_dokumen',
                        name: 'status_dokumen'
                    },
                    {
                        data: 'po_date',
                        "render" : function(data){
                                if(moment(data).isValid()){
                                    var content =  moment(data).format('D MMM YYYY');
                                   return content;
                                } 
                                else {
                                    var content = "";
                                    return content;
                                }
                            }
                    }, 
                    {
                        "render": function(data, type, row) 
                        {
                            var content = '';
                            var no_registrasi= btoa(row.document_code);
                            var noreg = row.document_code.replace(/\//g, '-');
                            var dspa = noreg.includes("DSPA");
                            var mutasi = noreg.includes("MTSA");

                            //alert(row.po_type);

                            if (row.po_type == 1 || row.po_type == 2) 
                            {
                                content += '<a href="{{ url("approval/berkas-amp") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else if( dspa )
                            {
                                //alert("berkas");
                                content += '<a href="{{ url("approval/berkas-disposal") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else if( mutasi )
                            {
                                //alert("berkas");
                                content += '<a href="{{ url("approval/berkas-mutasi") }}/'+no_registrasi+'" target="_blank"><span class="label label-default"><i class="fa fa-download"></i></span></a>';
                            }
                            else
                            {
                                content += '-';
                            }

                            return content;
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
                        //width: '8%'
                    }
                ],
                oLanguage: {
                    sProcessing: "<div id='datatable-loader'></div>",
                    sEmptyTable: "Data tidak di temukan",
                    sLoadingRecords: ""
                },
                "order": []
            }
        });
       

        $(".capitalized_on_date, #REQUEST_DATE, #PO_DATE").datepicker(
        {
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: "today",
            maxDate: 'today'
        });
        //var get_kode_aset_data = [];

        $("#date_history").datepicker({
            format: "dd/mm/yyyy",
            //autoclose: true,
            //endDate: "today",
            maxDate: 'today'
        });

        $(".refresh-outstanding").click(function()
        {
            //$('#data-table').DataTable().fnDestroy();
            $("#data-table").DataTable().ajax.url("{!! route('get.approval_grid') !!}").load();
        });

        $(".refresh-history").click(function()
        {
            //$('#data-table-history').DataTable().fnDestroy();
            $("#data-table-history").DataTable().ajax.url("{!! route('get.approval_grid_history') !!}").load();
        });
        
        // $('body').on('change', '.jenis_asset', function() {
        //     $("option", $(this)).removeAttr("selected");
        //     $("option:selected", $(this)).attr("selected", true);
        // })


    });

    function approval(id)
    {
        //alert(id);
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); return false;

         $("#box-detail-item").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.cek_reject);
                $("#request-form #no-reg").val(data.no_reg);
                $("#request-form #type-transaksi").val(data.type_transaksi);
                $("#request-form #po-type").val(data.po_type);
                $("#request-form #business-area").val(data.business_area);
                $("#request-form #requestor").val(data.requestor);
                $("#request-form #tanggal-reg").val(data.tanggal_reg);
                $("#request-form #kode-vendor").val(data.kode_vendor);
                $("#request-form #nama-vendor").val(data.nama_vendor);

                if(data.po_type == 'SAP')
                {
                    //VALIDASI SYNC VIEW SAP
                    if(data.sync_sap != '')
                    {
                        $("#create-button-sync-sap").show();
                        $("#create-button-sync-sap").html('<button type="button" class="btn btn-flat label-danger" OnClick="sinkronisasi()" style="margin-right: 5px;">SYNC SAP</button>');
                        
                        <?php if( $user_role == 'AC' ){ ?>
                            $("#button-approve").hide();
                            $(".button-reject").attr("disabled", false); 
                        <?php } ?>
                    }
                    else
                    {
                        $("#create-button-sync-sap").hide();
                        if(data.cek_reject==0){$("#button-approve").show();}
                        //$(".button-reject").attr("disabled", true); 
                        $(".button-reject").hide(); 
                    }
                }
                else if( data.po_type == 'Asset Lainnya' )
                {
                    //alert(data.po_type);
                    //VALIDASI SYNC ASET LAIN IT@080819
                    if(data.sync_lain == 'SAP')
                    {
                        $("#create-button-sync-sap").show();
                        $("#create-button-sync-sap").html('<button type="button" class="btn btn-flat label-danger" OnClick="sinkronisasi()" style="margin-right: 5px;">SYNC ASSET LAIN (SAP)</button>');
                        <?php if( $user_role == 'AC' ){ ?>
                            $("#button-approve").hide();
                            $(".button-reject").attr("disabled", false); 
                        <?php } ?>
                    }
                    else if(data.sync_lain == 'AMP')
                    {
                        $("#create-button-sync-sap").show();
                        $("#create-button-sync-sap").html('<button type="button" class="btn btn-flat label-danger" OnClick="sinkronisasi_amp()" style="margin-right: 5px;">SYNC ASSET LAIN</button>');
                        <?php if( $user_role == 'AC' ){ ?>
                            $("#button-approve").hide();
                            $(".button-reject").attr("disabled", false); 
                        <?php } ?>
                    }
                    else
                    {
                        $("#create-button-sync-sap").hide();
                        if(data.cek_reject==0){$("#button-approve").show();}
                        $(".button-reject").hide(); 
                    }
                }
                else
                {
                    //VALIDASI SYNC AMP IT@160719
                    if(data.sync_amp != 0)
                    {
                        $("#create-button-sync-sap").show();
                        $("#create-button-sync-sap").html('<button type="button" class="btn btn-flat label-danger" OnClick="sinkronisasi_amp()" style="margin-right: 5px;">SYNC AMP</button>');
                        <?php if( $user_role == 'AC' ){ ?>
                            $("#button-approve").hide();
                            $(".button-reject").attr("disabled", false); 
                        <?php } ?>
                    }
                    else
                    {
                        $("#create-button-sync-sap").hide();
                        if(data.cek_reject==0){$("#button-approve").show();}
                        $(".button-reject").hide(); 
                    }

                }
                    
                var item = '<table class="table xtable-condensed table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>NO PO</th>';
                item += '<th>ITEM PO</th>';
                item += '<th>QTY</th>';
                item += '<th>KODE MATERIAL</th>';
                item += '<th>NAMA MATERIAL</th>';
                item += '<th>VIEW DETAIL</th>';
                if (data.item_detail.length > 0) 
                {
                    var no = 1;
                    $.each(data.item_detail, function(key, val) 
                    {
                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.no_po + "</td>";
                        item += "<td>" + val.item + "</td>";
                        item += "<td>" + val.qty + "</td>";
                        item += "<td>" + val.kode + "</td>";
                        item += "<td>" + val.nama + "</td>";
                        item += "<td><i class='fa fa-eye' OnClick='getDetailItem(\"" + noreg + "\","+val.id+",1,"+no+")'></i></td>";
                        item += "</tr>";
                        no++;
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';

                $("#box-item-detail").html(item);

                log_history(id,1);
                $("#box-item-detail-history").html(item);

                $("#approve-modal .modal-title").html("<i class='fa fa-edit'></i>  APPROVAL PENDAFTARAN - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#approve-modal').modal('show');
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }


    function update_costcenter()
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var cost_center = $("#request-form #cost-center").val();
        var tgl_pengajuan = $("#tanggal-reg").val();
        var ba_pemilik_asset = $("#ba-pemilik-asset").val();
        var requestor = $("#requestor").val();
        var cost_center_old = $("#cost-center-old").val();
        var kode_asset_ams = $("#kode-asset-ams").val();
        var updated_by = <?php echo $user_id ?>;
        var cc = $("#request-form #cost-center").val().length;

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);
        
        
        // VALIDASI COST CENTER HARUS 10 CHAR
        if( cc < 10 )
        {
            notify({
                type: 'warning',
                message: " Cost Center < 10 char "
            });
            return false;
        } 
       
        if(confirm('Confirm Update Cost Center ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // 'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
                    'Access-Control-Allow-Methods': 'GET, POST',
                }
            });
            $.ajax({
                // url: "{{ url('approval/save_asset_sap') }}/"+id,
                url: "{{ url('approval/update_costcenter') }}/"+kode_asset_ams,
                method: "POST",
                data: param+"&no_reg="+no_registrasi+"&tgl_pengajuan="+tgl_pengajuan+"&ba_pemilik_asset="+ba_pemilik_asset+"&requestor="+requestor+"&cost_center="+cost_center+"&cost_center_old="+cost_center_old+"&updated_by="+updated_by,
                // beforeSend: function() {
                //     $('.loading-event').fadeIn();
                // },
                success: function(result) 
                {
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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
    function update_pic()
    {
        
        var param = '';     
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        
        var input = document.getElementsByName('penanggung_jawab[]'); 
        var penanggung_jawab = [];
    
        for (var i = 0; i < input.length/2; i++) { 
            penanggung_jawab.push(input[i+input.length/2].value); 
        } 

        var input2 = document.getElementsByName('jabatan[]'); 
        var jabatan = [];
    
        for (var i = 0; i < input2.length/2; i++) { 
            jabatan.push(input2[i+input2.length/2].value); 
        } 

        var input3 = document.getElementsByName('kode_asset_ams[]'); 
        var kode_asset_ams = [];
    
        for (var i = 0; i < input3.length/2; i++) { 
            kode_asset_ams.push(input3[i+input3.length/2].value); 
        } 
          
        // VALIDASI
       
        if( penanggung_jawab.includes("") )
        {
            notify({
                type: 'warning',
                message: " Penanggung Jawab Harus Diisi "
            });
            return false;
        } 
        if( jabatan.includes("") )
        {
            notify({
                type: 'warning',
                message: " Jabatan Harus Diisi "
            });
            return false;
        } 
       
       
        if(confirm('Confirm Penanggung Jawab ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // 'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
                    'Access-Control-Allow-Methods': 'GET, POST',
                }
            });
            $.ajax({
                url: "{{ url('approval/update_pic') }}",
                method: "POST",
                data: param+"&no_reg="+no_registrasi+"&penanggung_jawab="+penanggung_jawab+"&jabatan="+jabatan+"&kode_asset_ams="+kode_asset_ams,
                
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


    function update_jenis_asset()
    {
        
        var param = '';     
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var input2 = document.getElementsByName('asset_class[]'); 
        var asset_class = [];
        var no = 1;
        var data = [];
        var jenis_asset = "";
        var jns_asset = [];
        

        for (var i = 0; i < input2.length/2; i++) { 
            asset_class.push(input2[i+input2.length/2].value);
            var noreg = $("#noreg-"+no).val();
            var input4 = document.getElementsByName('jenis_asset-'+noreg); 
            var input5 = document.getElementsByName('jenis_asset_group-'+noreg); 
            var input6 = document.getElementsByName('jenis_asset_subgroup-'+noreg); 

            if (typeof input4[i+input4.length/2] !== 'undefined'){
               jenis_asset = (input4[i+input4.length/2].value);
               jenis_asset_group = (input5[i+input5.length/2].value);
               jenis_asset_subgroup = (input6[i+input6.length/2].value);
            }else{
                jenis_asset = $('input[name="jenis_asset-'+noreg+'"]').select2('data')[0].id;
                jenis_asset_group = $('input[name="jenis_asset_group-'+noreg+'"]').select2('data')[0].id;
                jenis_asset_subgroup = $('input[name="jenis_asset_subgroup-'+noreg+'"]').select2('data')[0].id;
            }
            jns_asset.push(jenis_asset);
            group.push(jenis_asset_group);
            subgroup.push(jenis_asset_subgroup);
            console.log(jenis_asset);
            if( $.trim(jenis_asset) == "" )
            {
                notify({
                    type: 'warning',
                    message: " Jenis Asset harus diisi"
                });
                return false;
            } 
            if( $.trim(jenis_asset_group) == "" )
            {
                notify({
                    type: 'warning',
                    message: " Jenis Asset Group harus diisi"
                });
                return false;
            } 

            if(jenis_asset.charAt(0) !== asset_class[i])
            {
                notify({
                    type: 'warning',
                    message: " Jenis Asset tidak sesuai "
                });
                return false;
            }                
            no++;
        } 
        var input3 = document.getElementsByName('kode_asset_ams[]'); 
        var kode_asset_ams = [];
    
        for (var i = 0; i < input3.length/2; i++) { 
            kode_asset_ams.push(input3[i+input3.length/2].value); 
        }   
       
        if(confirm('Confirm Jenis Asset ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // 'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
                    'Access-Control-Allow-Methods': 'GET, POST',
                }
            });
            $.ajax({
                url: "{{ url('approval/update_jenis_asset') }}",
                method: "POST",
                data: param+"&no_reg="+no_registrasi+"&jenis_asset="+jns_asset+"&group="+group+"&subgroup="+subgroup+"&kode_asset_ams="+kode_asset_ams,
                
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


    function approval_disposal(id)
    {
        //alert(id); return false;
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); //return false;

        $("#box-detail-item-disposal").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_disposal') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.cek_reject);
                $("#request-form #no-reg").val(data.no_reg);
                $("#request-form #type-transaksi").val(data.type_transaksi);
                $("#request-form #po-type").val(data.po_type);
                $("#request-form #business-area").val(data.business_area);
                $("#request-form #requestor").val(data.requestor);
                $("#request-form #tanggal-reg").val(data.tanggal_reg);
                $("#request-form #kode-vendor").val(data.kode_vendor);
                $("#request-form #nama-vendor").val(data.nama_vendor);

                $("#create-button-sync-sap").hide();
                if(data.cek_reject==0){$("#button-approve").show();}
                $(".button-reject").show(); 
                    
                var item = '<table class="table table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>KODE ASSET AMS</th>';
                item += '<th>KODE ASSET SAP</th>';
                item += '<th>NAMA MATERIAL</th>';
                item += '<th>LOKASI BA CODE</th>';
                item += '<th>NAMA ASSET</th>';
                item += '<th>HARGA PEROLEHAN (RP)</th>';
                item += '<th>NILAI BUKU (RP)</th>';
                item += '<th>VIEW DETAIL</th>';
                if (data.item_detail.length > 0) 
                {
                    var no = 1;
                    $.each(data.item_detail, function(key, val) 
                    {
                        var doc_no = val.document_code.replace(/\//g, '-');
                        var kode_fams = btoa(val.kode_asset_ams);
                        var jenis_pengajuan = val.jenis_pengajuan;
                        
                        if( jenis_pengajuan == 1 )
                        {
                            pengajuan = 'PENJUALAN';
                        }
                        else if( jenis_pengajuan == 2 )
                        {
                            pengajuan = 'HILANG';
                        }else{
                            pengajuan = 'RUSAK';
                        }

                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.kode_asset_ams + "</td>";
                        item += "<td>" + val.kode_asset_sap + "</td>";
                        item += "<td>" + val.nama_material + "</td>";
                        item += "<td>" + val.lokasi_ba_description + "</td>";
                        item += "<td>" + val.nama_asset_1 + "</td>";
                        item += "<td>" + val.harga_perolehan + "</td>";
                        item += "<td>" + val.nilai_buku + "</td>";

                        if( data.item_detail.length != 1 )
                        {
                            item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a> &nbsp;&nbsp;&nbsp; <i class='fa fa-trash' style='color:red' OnClick='delDisposal(\""+data.no_reg+"\","+val.kode_asset_ams+")'></i> </td>";
                        }
                        else
                        {
                            item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a></td>";
                        }

                        item += "</tr>";
                        no++;
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';

                $("#box-item-detail-disposal").html(item);

                log_history(id,3);
                $("#box-item-detail-history-disposal").html(item);

                $("#approve-disposal-modal .modal-title").html("<i class='fa fa-edit'></i> APPROVAL DISPOSAL "+ pengajuan +" - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#approve-disposal-modal').modal('show');
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function createItemRequestTable() {
        var item = '<table class="table table-condensed" id="request-item-table" style="font-size:13px">';
        item += '<th>No</th>';
        item += '<th>Item</th>';
        item += '<th>Qty index</th>';
        item += '<th>Kode material</th>';
        item += '<th>nama material</th>';
        item += '<th>MRP</th>';
        item += '<th>BA Pemilik Aset</th>';
        item += '<th>BA Lokasi Aset</th>';
        item += '<th>Requestor</th>';

        if (request_item.length > 0) {
            var no = 1;
            jQuery.each(request_item, function(key, val) {
                item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                item += "<td>" + no + "</td>";
                item += "<td>" + val.item_po + "</td>";
                item += "<td>" + val.qty_index + "</td>";
                item += "<td>" + val.code + "-" + key + "</td>";
                item += "<td>" + val.name + " " + key + "</td>";
                item += "<td>" + val.mrp + "</td>";
                item += "<td>" + val.business_area + "</td>";
                item += "<td>" + val.business_area_location + "</td>";
                item += "<td>" + val.requestor + "</td>";
                item += "</tr>";
                no++;
            });
        } else {
            item += '<tr>';
            item += ' <td colspan="7" style="text-align:center">No item selected</td>';
            item += '</tr>';
        }
        item += "</table>";
        jQuery("#request-item-table").html(item);
    }

    function getDetailItem(noreg,id,tipe,no_urut)
    {
        //alert(JSON.stringify(id));
        //alert(tipe);
        //$(".rincian-informasi-aset-box").reset(); //return false;
        $('.rincian-informasi-aset-box input[type="text"]').val('');

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_detail') }}/"+noreg+"/"+id,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            {
                //alert(data.length);
                var total_tab = data.length;
                var i;
                var j;
                var no=1;
                var num=1;

                var item = "<span class='label bg-blue'><i class='fa fa-bars'></i> RINCIAN INFORMASI ASET</span><br/><br/>";
                item += "<div class='form-group'>";
                item += "<div class='col-md-12 xnav-tabs-custom'><input type='hidden' id='total_tab' name='total_tab' value='"+total_tab+"'>";

                if(total_tab == 0)
                {
                    item += "<div class='callout callout-danger'><h4>Warning!</h4><p>Belum ada Informasi aset</p></div>";
                }

                item += "<ul class='nav nav-tabs'>";
                
                for(i=0;i<total_tab;i++)
                {
                    var active = '';
                    if(i==0){ active = 'active'; }

                    item += "<li class='"+active+"'><a href='#panel-"+no+"' data-toggle='tab' class='panel-"+i+"'>Aset "+no_urut+"."+no+"</a></li> ";
                    no++;
                }
                //item += "<li class='"+active+"'><a href='#panel-file' data-toggle='tab' class='panel-"+no+"'>Asset "+no+"</a></li> ";
                item += "</ul>";

                item += "<div class='tab-content' style='border: 1px solid #e0dcdc;border-top:none; font-size:12px !important'>";
            

                $.each(data, function(key, val) 
                {
                    //alert(val.deleted);
                    var aktif = '';

                    if(key==0){ aktif = 'active'; }
                    item += "<div class='tab-pane "+aktif+" ' id='panel-"+num+"'>";
                    
                    item += "<div class='box-body rincian-informasi-aset-box'>";

                    <?php if( $user_role == 'AC' || $user_role == 'PGA' ){$readonly = "readonly";}else{ $readonly = ""; }?>
                    
                    item += "<div class='col-md-6'>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO PO</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_po+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>TGL PO</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.tgl_po+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>KONDISI ASET</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.kondisi_asset+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NAMA ASSET</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.nama_asset+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO RANGKA/SERI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_rangka_or_no_seri+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO MESIN / IMEI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_mesin_or_imei+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO POLISI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_polisi+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>LOKASI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.lokasi+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>INFORMASI</label><div class='col-md-8'><textarea class='form-control' readonly>"+val.info+"</textarea></div></div>";
                    
                    item += "</div>";

                    item += "<div class='col-md-6'>";
                    
                    item += " <div class='form-group'><label for='plant' class='col-md-4'>JENIS ASET <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm select-jenis-aset' name='jenis_asset-"+val.no_reg_item+"' value='"+val.jenis_asset+"' id='jenis_asset-"+val.no_reg_item+"' autocomplete='off' onChange='get_group("+val.no_reg_item+")' <?php echo $readonly; ?> ></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>GROUP <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' id='jenis_asset_group-"+val.no_reg_item+"' name='jenis_asset_group-"+val.no_reg_item+"' value='"+val.group+"' id='' autocomplete='off' onChange='get_subgroup("+val.no_reg_item+")' <?php echo $readonly; ?> ></div></div>";
                    
                    item += " <div class='form-group'><label for='plant' class='col-md-4'>SUB GROUP <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' id='jenis_asset_subgroup-"+val.no_reg_item+"' name='jenis_asset_subgroup-"+val.no_reg_item+"' value='"+val.sub_group+"' id='' autocomplete='off' <?php echo $readonly; ?> ></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>MERK</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.merk+"' id='' autocomplete='off' readonly></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>SPESIFIKASI/WARNA</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.spesifikasi_or_warna+"' id='' autocomplete='off' readonly></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>TAHUN</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.tahun+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='' class='col-md-4'>PENANGGUNG JAWAB</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_penanggung_jawab_asset-"+val.no_reg_item+"' value='"+val.nama_penanggung_jawab_asset+"' id='nama_penanggung_jawab_asset-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>JABATAN PENANGGUNG JAWAB</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='jabatan_penanggung_jawab_asset-"+val.no_reg_item+"' value='"+val.jabatan_penanggung_jawab_asset+"' id='jabatan_penanggung_jawab_asset-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                    if(tipe==1)
                    {
                        //if( val.deleted == "" ){
                            if( val.total_asset == 1 )
                            {
                                //alert(total_tab);
                                //$(".button-delete").hide(); 

                                //alert(val.no_po); return false; //EST/AMP-NPN/JKTO/07/14/0186

                                <?php if( $user_role == 'AMS' ){ if($data['outstanding'] != 0 ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveItemDetail("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:5px;xmargin-top:5px'><i class='fa fa-save'></i> SAVE</div><button type='button' class='btn btn-warning btn-sm' OnClick='delAsset("+val.id+")' style='margin-right: 15px' disabled><i class='fa fa-trash'></i> DELETE</button></div>";
                                <?php } }else{ if($data['outstanding'] != 0 && $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><button type='button' class='btn btn-warning btn-sm' OnClick='delAsset("+val.id+")' style='margin-right: 15px' disabled><i class='fa fa-trash'></i> DELETE</button></div>";
                                <?php } }  ?>
                            }
                            else
                            {
                                <?php if( $user_role == 'AMS' ){ if($data['outstanding'] != 0 ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveItemDetail("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:5px;xmargin-top:5px'><i class='fa fa-save'></i> SAVE</div><div class='btn btn-warning btn-sm button-delete' value='Delete' OnClick='delAsset("+val.id+")' style='margin-right:15px'><i class='fa fa-trash'></i> DELETE</div></div>";
                                <?php } }else{ if($data['outstanding'] != 0 && $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm button-delete' value='Delete' OnClick='delAsset("+val.id+")' style='margin-right:15px'><i class='fa fa-trash'></i> DELETE</div></div>";
                                <?php } } ?>
                                //item += "<div class='form-group' align='right'><button type='button' class='btn btn-flat label-danger' OnClick='delAsset("+val.id+")' style='margin-right: 5px'>Delete</button></div>";
                            }
                        //}
                    }

                    item += "</div>";

                    /* FILE UPLOAD */
                    item += "<div class='col-md-12'><div class='row'>";
                    item += "<span class='label bg-blue'><i class='fa fa-bars'></i> RINCIAN FILE ASET</span><br/><br/>";
                    
                    var file_foto_asset = [];
                    var file_foto_seri = [];
                    var file_foto_mesin = [];

                    if (val.file === undefined || val.file.length == 0) 
                    {
                        //item += "<div class='callout callout-danger'><h4>Warning!</h4><p>Belum ada file asset</p></div>";
                        item += "<div class='col-md-4'><b>aset</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        item += "<div class='col-md-4'><b>no seri / no rangka</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        item += "<div class='col-md-4'><b>imei / no mesin</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                    }
                    else
                    {
                        var kategori = ["asset", "no seri", "imei"];
                        var print_kategori = [];

                        $.each(val.file, function(k, v) 
                        {
                            if( $.inArray(v.file_category, kategori) !== -1 )
                            {   
                                //alert(kategori[k]);
                                item += "<div class='col-md-4'><b>"+v.file_category+"</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='"+v.file_thumb+"'></div>";
                                print_kategori.push(v.file_category);
                            }
                        });

                        //alert(val.file.length);
                        //alert(print_kategori);

                        //if( print_kategori != 'asset' )
                        if( print_kategori.includes("asset") == false )
                        {
                            item += "<div class='col-md-4'><b>aset</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }

                        //if( print_kategori != 'no seri' )
                        if( print_kategori.includes("no seri") == false )
                        {
                            item += "<div class='col-md-4'><b>no seri / no rangka</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }

                        //if( print_kategori != 'imei' )
                        if( print_kategori.includes("imei") == false )
                        {
                            item += "<div class='col-md-4'><b>imei / no mesin</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }
                    }
                    
                    item += "</div></div>";
                    /* END FILE UPLOAD */

                    //alert(val.po_type);
                    if( val.po_type == 0 || val.po_type == 2 )
                    {
                        var nama_aset_2 = '';
                        if( val.nama_asset_2 == '' ){nama_asset_2 = val.no_po;}else{nama_asset_2 = val.nama_asset_2;}

                        var nama_aset_1 = '';
                        var nama_aset_3 = '';
                        if( val.nama_asset_1 == '' ){nama_asset_1 = val.nama_material}else{nama_asset_1=val.nama_asset_1;}
                        if( val.nama_asset_3 == '' ){nama_asset_3 = val.nama_material}else{nama_asset_3=val.nama_asset_3;}

                        /* BOX DETAIL ASSET SAP */
                        item += "<div class='col-md-12'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> DETAIL ASET SAP</span><br/><br/>";
                        
                        //item += "<form id='request-form-detail-asset-sap' class='form-horizontal' style=''>";
                        item += "<div class='col-md-6'> ";

                        item += "<div class='form-group'><label for='' class='col-md-4'>DESCRIPTION</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_1-"+val.no_reg_item+"' value='"+nama_asset_1+"' id='nama_asset_1-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 1' required <?php echo $readonly; ?> ></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'></label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_2-"+val.no_reg_item+"' value='"+nama_asset_2+"' id='nama_asset_2-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 2' <?php echo $readonly; ?> ></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'>ASET MAIN NO. TEXT</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_3-"+val.no_reg_item+"' value='"+nama_asset_3+"' id='nama_asset_3-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 3' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>ACCT DETERMINATION</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='acct_determination-"+val.no_reg_item+"' value='"+val.jenis_asset+"' id='nama_asset_3-"+val.no_reg_item+"' autocomplete='off' placeholder='Acct Determination' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>SERIAL NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='serial_number-"+val.no_reg_item+"' value='"+val.no_rangka_or_no_seri+"' id='serial_number-"+val.no_reg_item+"' autocomplete='off' placeholder='Serial Number' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>INVENTORY NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='inventory_number-"+val.no_reg_item+"' value='"+val.no_mesin_or_imei+"' id='inventory_number-"+val.no_reg_item+"' autocomplete='off' placeholder='Inventory Number' readonly></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'>QUANTITY <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='number' class='form-control input-sm' name='quantity-"+val.no_reg_item+"' value='"+val.quantity_asset_sap+"' id='quantity-"+val.no_reg_item+"' autocomplete='off' required <?php echo $readonly; ?> ></div></div>";
                        
                        //alert(val.uom_asset_sap);                            
                        item += "<div class='form-group'><label for='' class='col-md-4'>UOM <sup style='color:red'>*</sup></label><div class='col-md-8' style='display: inline-block'><input type='text' class='form-control input-sm' name='uom-"+val.no_reg_item+"' value='"+val.uom_asset_sap+"' id='uom-"+val.no_reg_item+"' required <?php echo $readonly; ?> ></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>CAPITALIZED</label><div class='col-md-8'><input type='text' class='form-control input-sm capitalized_on_date' name='capitalized_on-"+val.no_reg_item+"' value='"+val.capitalized_on+"' id='capitalized_on-"+val.no_reg_item+"' autocomplete='off' placeholder='yyyy-mm-dd' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>DEACTIVATION</label><div class='col-md-8'><input type='text' class='form-control input-sm capitalized_on_date' name='deactivation_on-"+val.no_reg_item+"' value='"+val.deactivation_on+"' id='deactivation_on-"+val.no_reg_item+"' autocomplete='off' placeholder='yyyy-mm-dd' required <?php echo $readonly; ?>></div></div>";
                        
                        item += "</div>";
                        
                        item += "<div class='col-md-6'> ";

                        item += "<div class='form-group'><label for='' class='col-md-4'>BUSINESS AREA</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='business_area-"+val.no_reg_item+"' value='"+val.business_area+"' id='business_area-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>COST CENTER <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' name='cost_center-"+val.no_reg_item+"' value='"+val.cost_center+"' id='cost_center-"+val.no_reg_item+"' autocomplete='off' minlength='10' maxlength='10' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>PLANT</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='plant-"+val.no_reg_item+"' value='"+val.business_area+"' id='plant-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        if( val.po_type == 2 )
                        {
                            <?php if( $user_role == 'AMS' ){ ?>

                            var kode_vendor = val.vendor.split('-');
                            //var jenis_asset_code = jenis_asset.split('-');

                            item += "<div class='form-group'><label for='' class='col-md-4'>VENDOR</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='vendor-"+val.no_reg_item+"' value='"+kode_vendor[0]+"' id='vendor-"+val.no_reg_item+"' autocomplete='off' placeholder='Masukkan Kode Vendor'> <div class='btn btn-warning btn-sm' value='Update' OnClick='updateKodeVendor(\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-save'></i> UPDATE VENDOR</div></div></div>";
                            <?php } ?>
                        }
                        else
                        {
                            item += "<div class='form-group'><label for='' class='col-md-4'>VENDOR</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='vendor-"+val.no_reg_item+"' value='"+val.vendor+"' id='vendor-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";
                        }

                        item += "<div class='xform-group'><label for='' class='xcol-md-4'>DEPREC, AREAS</label><br/>";
                        item += "<table class='tabel table-bordered table-responsive table-condensed table-striped table-container'>";
                        item += "<tr><th>Area Number</th><th>Depreciation Area</th><th>Dkey</th><th>Use Life</th></tr>";
                        item += "<tr><td>01</td><td>Book</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='book_deprec_01-"+val.no_reg_item+"' value='"+val.book_deprec_01+"' id='book_deprec_01-"+val.no_reg_item+"' autocomplete='off' required <?php echo $readonly; ?>></td></tr>";
                        item += "<tr><td>15</td><td>Fiscal</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='fiscal_deprec_15-"+val.no_reg_item+"' value='"+val.fiscal_deprec_15+"' id='fiscal_deprec_15-"+val.no_reg_item+"' autocomplete='off' onkeyup='fiscalgroup("+val.no_reg_item+")' required <?php echo $readonly; ?>></td></tr>";
                        item += "<tr><td>30</td><td>Group</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='group_deprec_30-"+val.no_reg_item+"' value='"+val.group_deprec_30+"' id='group_deprec_30-"+val.no_reg_item+"' autocomplete='off' placeholder='' readonly></td></tr>";
                        item += "</table>";
                        item += "</div>";

                        //if( val.deleted == '' ){
                            if(val.kode_asset_sap == '')
                            {
                                <?php if( $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveAssetSap("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-save'></i> SAVE</div></div>";
                                <?php } ?>
                            }
                        //}

                        item += "</div>";
                        item += "</div></div>";
                        /* END BOX DETAIL ASSET SAP */
                    }

                    /* BOX KODE ASET CONTROLLER */
                    if( val.kode_asset_sap != '' && val.po_type == 0 )
                    {
                        item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> KODE ASET</span><br/><br/>";
                        
                        item += "<div class='col-md-4'> ";
                        
                        <?php if( $user_role == 'AC' ){ ?>
                            
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset("+val.no_reg_item+")'><input type='hidden' id='request_kode_aset_input' name='request_kode_aset_input'>"; 
                            
                            if(tipe == 1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiKodeAssetController("+val.po_type+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px;margin-bottom:5px'><i class='fa fa-save'></i> SAVE</div>";
                                item += "<input type='text' class='form-control' placeholder='Jenis Kendaraan' id='jenis-kendaraan-"+val.no_reg_item+"' name='jenis-kendaraan-"+val.no_reg_item+"'>";
                                item += "<div class='btn btn-info btn-sm' OnClick='printFormIO("+val.asset_po_id+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-print'></i> PRINT FORM IO</div>";
                            }
                            
                            item += "</div></div></div>";

                        <?php }else{ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset("+val.no_reg_item+")' <?php echo $readonly; ?> ><input type='hidden' id='request_kode_aset_input' name='request_kode_aset_input'></div></div>";
                            item += "</div>";
                        <?php } ?>

                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET SAP</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_sap-"+val.no_reg_item+"' value='"+val.kode_asset_sap+"' id='kode_aset_sap-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET FAMS</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_asset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_asset_ams-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";

                        item += "</div></div>";

                        if( val.po_type == 0 || val.po_type == 2 )
                        { //TIDAK JADI IT@250719  ~ HIDE KRN AMP JG MENGINPUT GI IT@170619
                                
                            <?php if( $user_role == 'PGA' ){ ?>

                            /* BOX CHECK GI */
                            item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                            item += "<span class='label bg-blue'><i class='fa fa-bars'></i> MATERIAL DOCUMENT / CHECK GI</span><br/><br/>";

                            item += "<div class='col-md-4'> ";
                            item += "<div class='form-group'><label for='' class='col-md-4'>NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='md_number-"+val.no_reg_item+"' value='"+val.gi_number+"' id='md_number-"+val.no_reg_item+"' autocomplete='off'></div></div>";
                            item += "</div>";

                            item += "<div class='col-md-4'> ";
                            item += "<div class='form-group'><label for='' class='col-md-4'>YEAR</label><div class='col-md-8'><input type='text' class='form-control input-sm md_year' name='md_year-"+val.no_reg_item+"' value='"+val.gi_year+"' id='md_year-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_check_gi("+val.no_reg_item+","+val.po_type+")'></div></div>";
                            item += "</div>";

                            item += "<div class='col-md-4'>";
                            
                            if(tipe == 1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiGINumberYear("+val.po_type+","+val.no_reg_item+")'><i class='fa fa-save'></i> SAVE GI NUMBER & YEAR</div>";
                            }

                            item += "</div>";

                            item += "</div></div>";
                            /* END BOX CHECK GI */

                            <?php } ?>

                        }
                    }
                    /* END BOX KODE ASET CONTROLLER */

                    /* FOR AMP & ASET LAIN */
                    if( val.po_type == 1 || val.po_type == 2 )
                    {
                        item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> KODE ASET</span><br/><br/>";
                        
                        item += "<div class='col-md-4'> ";
                        <?php if( $user_role == 'AC' ){ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset_amp("+val.no_reg_item+")'><input type='hidden' class='form-control input-sm' name='kode_aset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_aset_ams-"+val.no_reg_item+"'>";
                            
                            if(tipe==1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiKodeAssetController("+val.po_type+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px;margin-bottom:5px'><i class='fa fa-save'></i> SAVE</div>";
                                item += "<input type='text' class='form-control' placeholder='Jenis Kendaraan' id='jenis-kendaraan-"+val.no_reg_item+"' name='jenis-kendaraan-"+val.no_reg_item+"'>";
                                item += "<div class='btn btn-info btn-sm' OnClick='printFormIO("+val.asset_po_id+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-print'></i> PRINT FORM IO</div>";
                            }
                            
                            item += "</div></div></div>";
                        <?php }else{ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset_amp("+val.no_reg_item+")'><input type='hidden' class='form-control input-sm' name='kode_aset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_aset_ams-"+val.no_reg_item+"'></div></div>";
                            item += "</div>";
                        <?php } ?>
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET SAP</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_sap-"+val.no_reg_item+"' value='"+val.kode_asset_sap+"' id='kode_aset_sap-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET FAMS</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_asset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_asset_ams-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        
                        item += "</div>";
                        item += "</div></div>";
                    }
                    
                    item += "</div>";
                    item += "</div>";

                    num++;
                });

                item += "<div>";

                item += "</div>";
                item += "</div>";

                if(tipe==1)
                {
                    $("#box-detail-item").fadeIn();
                    $("#box-detail-item").html(item);
                }
                else
                {
                    //alert(tipe);
                    $("#box-detail-item-history").fadeIn();
                    $("#box-detail-item-history").html(item);
                }

                //alert(noreg);

                /*$('#quantity-'+val.no_reg_item+'').keypress(function(event){
                    console.log(event.which);
                if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
                    event.preventDefault();
                }});*/

                <?php if( $user_role == 'AMS' ){ ?>
                $.each(data, function(key, val) 
                {
                    trigger_edit_asset(val.no_reg_item,val.jenis_asset,val.group,val.sub_group,val.uom_asset_sap);
                });
                $.each(data, function(key, val) 
                {
                    if( val.po_type == 0 || val.po_type == 2 )
                        {
                                $('#uom-"+val.no_reg_item+"').val(val.uom_asset_sap)
                        }
                });
                <?php } ?>

                <?php if( $user_role == 'AC' ){ ?>
                $.each(data, function(key, val) 
                {
                    opt_tipe_kendaraan(val.no_reg_item);
                });
                <?php } ?>                    
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        });  
        
    }

    function getDetailItemDisposal(noreg,id,tipe,no_urut)
    {
        //alert(JSON.stringify(id));
        //alert(tipe); return false;
        //$(".rincian-informasi-aset-box").reset(); //return false;
        $('.rincian-informasi-aset-box input[type="text"]').val('');

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_detail') }}/"+noreg+"/"+id,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            {
                //alert(data.length);
                var total_tab = data.length;
                var i;
                var j;
                var no=1;
                var num=1;

                var item = "<span class='label bg-blue'><i class='fa fa-bars'></i> RINCIAN INFORMASI ASET</span><br/><br/>";
                item += "<div class='form-group'>";
                item += "<div class='col-md-12 xnav-tabs-custom'><input type='hidden' id='total_tab' name='total_tab' value='"+total_tab+"'>";

                if(total_tab == 0)
                {
                    item += "<div class='callout callout-danger'><h4>Warning!</h4><p>Belum ada Informasi aset</p></div>";
                }

                item += "<ul class='nav nav-tabs'>";
                
                for(i=0;i<total_tab;i++)
                {
                    var active = '';
                    if(i==0){ active = 'active'; }

                    item += "<li class='"+active+"'><a href='#panel-"+no+"' data-toggle='tab' class='panel-"+i+"'>Aset "+no_urut+"."+no+"</a></li> ";
                    no++;
                }
                //item += "<li class='"+active+"'><a href='#panel-file' data-toggle='tab' class='panel-"+no+"'>Asset "+no+"</a></li> ";
                item += "</ul>";

                item += "<div class='tab-content' style='border: 1px solid #e0dcdc;border-top:none; font-size:12px !important'>";
            

                $.each(data, function(key, val) 
                {
                    //alert(val.deleted);
                    var aktif = '';

                    if(key==0){ aktif = 'active'; }
                    item += "<div class='tab-pane "+aktif+" ' id='panel-"+num+"'>";
                    
                    item += "<div class='box-body rincian-informasi-aset-box'>";

                    <?php if( $user_role == 'AC' || $user_role == 'PGA' ){$readonly = "readonly";}else{ $readonly = ""; }?>
                    item += "<div class='col-md-6'>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO PO</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_po+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>TGL PO</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.tgl_po+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>KONDISI ASET</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.kondisi_asset+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NAMA ASSET</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.nama_asset+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO RANGKA/SERI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_rangka_or_no_seri+"' id='' autocomplete='off' readonly></div></div>";
                    
                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO MESIN / IMEI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_mesin_or_imei+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>NO POLISI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.no_polisi+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>LOKASI</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.lokasi+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='plant' class='col-md-4'>INFORMASI</label><div class='col-md-8'><textarea class='form-control' readonly>"+val.info+"</textarea></div></div>";
                    
                    item += "</div>";

                    item += "<div class='col-md-6'>";
                    
                    item += " <div class='form-group'><label for='plant' class='col-md-4'>JENIS ASET <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm select-jenis-aset' name='xjenis_asset-"+val.no_reg_item+"' value='"+val.jenis_asset+"' id='xjenis_asset-"+val.no_reg_item+"' autocomplete='off' onChange='get_group("+val.no_reg_item+")' readonly='readonly' ></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>GROUP <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' id='xjenis_asset_group-"+val.no_reg_item+"' name='xjenis_asset_group-"+val.no_reg_item+"' value='"+val.group+"' id='' autocomplete='off' onChange='get_subgroup("+val.no_reg_item+")' readonly='readonly' ></div></div>";
                    
                    item += " <div class='form-group'><label for='plant' class='col-md-4'>SUB GROUP <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' id='jenis_asset_subgroup-"+val.no_reg_item+"' name='xjenis_asset_subgroup-"+val.no_reg_item+"' value='"+val.sub_group+"' id='' autocomplete='off' readonly='readonly' ></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>MERK</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.merk+"' id='' autocomplete='off' readonly></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>SPESIFIKASI/WARNA</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.spesifikasi_or_warna+"' id='' autocomplete='off' readonly></div></div>";

                    item += " <div class='form-group'><label for='plant' class='col-md-4'>TAHUN</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='' value='"+val.tahun+"' id='' autocomplete='off' readonly></div></div>";

                    item += "<div class='form-group'><label for='' class='col-md-4'>PENANGGUNG JAWAB</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_penanggung_jawab_asset-"+val.no_reg_item+"' value='"+val.nama_penanggung_jawab_asset+"' id='nama_penanggung_jawab_asset-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>JABATAN PENANGGUNG JAWAB</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='jabatan_penanggung_jawab_asset-"+val.no_reg_item+"' value='"+val.jabatan_penanggung_jawab_asset+"' id='jabatan_penanggung_jawab_asset-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                    if(tipe==1)
                    {
                        //if( val.deleted == "" ){
                            if( val.total_asset == 1 )
                            {
                                //alert(total_tab);
                                //$(".button-delete").hide(); 

                                //alert(val.no_po); return false; //EST/AMP-NPN/JKTO/07/14/0186

                                <?php if( $user_role == 'AMS' ){ if($data['outstanding'] != 0 ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveItemDetail("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:5px;xmargin-top:5px'><i class='fa fa-save'></i> SAVE</div><button type='button' class='btn btn-warning btn-sm' OnClick='delAsset("+val.id+")' style='margin-right: 15px' disabled><i class='fa fa-trash'></i> DELETE</button></div>";
                                <?php } }else{ if($data['outstanding'] != 0 && $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><button type='button' class='btn btn-warning btn-sm' OnClick='delAsset("+val.id+")' style='margin-right: 15px' disabled><i class='fa fa-trash'></i> DELETE</button></div>";
                                <?php } }  ?>
                            }
                            else
                            {
                                <?php if( $user_role == 'AMS' ){ if($data['outstanding'] != 0 ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveItemDetail("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:5px;xmargin-top:5px'><i class='fa fa-save'></i> SAVE</div><div class='btn btn-warning btn-sm button-delete' value='Delete' OnClick='delAsset("+val.id+")' style='margin-right:15px'><i class='fa fa-trash'></i> DELETE</div></div>";
                                <?php } }else{ if($data['outstanding'] != 0 && $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm button-delete' value='Delete' OnClick='delAsset("+val.id+")' style='margin-right:15px'><i class='fa fa-trash'></i> DELETE</div></div>";
                                <?php } } ?>
                                //item += "<div class='form-group' align='right'><button type='button' class='btn btn-flat label-danger' OnClick='delAsset("+val.id+")' style='margin-right: 5px'>Delete</button></div>";
                            }
                        //}
                    }

                    item += "</div>";

                    /* FILE UPLOAD */
                    item += "<div class='col-md-12'><div class='row'>";
                    item += "<span class='label bg-blue'><i class='fa fa-bars'></i> RINCIAN FILE ASET</span><br/><br/>";
                    
                    var file_foto_asset = [];
                    var file_foto_seri = [];
                    var file_foto_mesin = [];

                    if (val.file === undefined || val.file.length == 0) 
                    {
                        //item += "<div class='callout callout-danger'><h4>Warning!</h4><p>Belum ada file asset</p></div>";
                        item += "<div class='col-md-4'><b>aset</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        item += "<div class='col-md-4'><b>no seri / no rangka</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        item += "<div class='col-md-4'><b>imei / no mesin</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                    }
                    else
                    {
                        var kategori = ["asset", "no seri", "imei"];
                        var print_kategori = [];

                        $.each(val.file, function(k, v) 
                        {
                            if( $.inArray(v.file_category, kategori) !== -1 )
                            {   
                                //alert(kategori[k]);
                                item += "<div class='col-md-4'><b>"+v.file_category+"</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='"+v.file_thumb+"'></div>";
                                print_kategori.push(v.file_category);
                            }
                        });

                        //alert(val.file.length);
                        //alert(print_kategori);

                        //if( print_kategori != 'asset' )
                        if( print_kategori.includes("asset") == false )
                        {
                            item += "<div class='col-md-4'><b>aset</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }

                        //if( print_kategori != 'no seri' )
                        if( print_kategori.includes("no seri") == false )
                        {
                            item += "<div class='col-md-4'><b>no seri / no rangka</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }

                        //if( print_kategori != 'imei' )
                        if( print_kategori.includes("imei") == false )
                        {
                            item += "<div class='col-md-4'><b>imei / no mesin</b><br/> <img id='foto_thumb' name='foto_thumb' data-status='0' title='' class='img-responsive' src='{{ url('img/default-img.png') }}' width='200'><br/></div>";
                        }
                    }
                    
                    item += "</div></div>";
                    /* END FILE UPLOAD */

                    //alert(val.po_type);
                    if( val.po_type == 0 || val.po_type == 2 )
                    {
                        var nama_aset_2 = '';
                        if( val.nama_asset_2 == '' ){nama_asset_2 = val.no_po;}else{nama_asset_2 = val.nama_asset_2;}

                        var nama_aset_1 = '';
                        var nama_aset_3 = '';
                        if( val.nama_asset_1 == '' ){nama_asset_1 = val.nama_material}else{nama_asset_1=val.nama_asset_1;}
                        if( val.nama_asset_3 == '' ){nama_asset_3 = val.nama_material}else{nama_asset_3=val.nama_asset_3;}

                        /* BOX DETAIL ASSET SAP */
                        item += "<div class='col-md-12'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> DETAIL ASET SAP</span><br/><br/>";
                        
                        //item += "<form id='request-form-detail-asset-sap' class='form-horizontal' style=''>";
                        item += "<div class='col-md-6'> ";

                        item += "<div class='form-group'><label for='' class='col-md-4'>DESCRIPTION</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_1-"+val.no_reg_item+"' value='"+nama_asset_1+"' id='nama_asset_1-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 1' required <?php echo $readonly; ?> ></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'></label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_2-"+val.no_reg_item+"' value='"+nama_asset_2+"' id='nama_asset_2-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 2' <?php echo $readonly; ?> ></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'>ASET MAIN NO. TEXT</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='nama_asset_3-"+val.no_reg_item+"' value='"+nama_asset_3+"' id='nama_asset_3-"+val.no_reg_item+"' autocomplete='off' placeholder='Nama Asset 3' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>ACCT DETERMINATION</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='acct_determination-"+val.no_reg_item+"' value='"+val.jenis_asset+"' id='nama_asset_3-"+val.no_reg_item+"' autocomplete='off' placeholder='Acct Determination' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>SERIAL NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='serial_number-"+val.no_reg_item+"' value='"+val.no_rangka_or_no_seri+"' id='serial_number-"+val.no_reg_item+"' autocomplete='off' placeholder='Serial Number' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>INVENTORY NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='inventory_number-"+val.no_reg_item+"' value='"+val.no_mesin_or_imei+"' id='inventory_number-"+val.no_reg_item+"' autocomplete='off' placeholder='Inventory Number' readonly></div></div>";
                        
                        item += "<div class='form-group'><label for='' class='col-md-4'>QUANTITY <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='number' class='form-control input-sm' name='quantity-"+val.no_reg_item+"' value='"+val.quantity_asset_sap+"' id='quantity-"+val.no_reg_item+"' autocomplete='off' required <?php echo $readonly; ?> ></div></div>";
                        
                        //alert(val.uom_asset_sap);                            
                        item += "<div class='form-group'><label for='' class='col-md-4'>UOM <sup style='color:red'>*</sup></label><div class='col-md-8' style='display: inline-block'><input type='text' class='form-control input-sm' name='uom-"+val.no_reg_item+"' value='"+val.uom_asset_sap+"' id='uom-"+val.no_reg_item+"' required <?php echo $readonly; ?> ></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>CAPITALIZED</label><div class='col-md-8'><input type='text' class='form-control input-sm capitalized_on_date' name='capitalized_on-"+val.no_reg_item+"' value='"+val.capitalized_on+"' id='capitalized_on-"+val.no_reg_item+"' autocomplete='off' placeholder='yyyy-mm-dd' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>DEACTIVATION</label><div class='col-md-8'><input type='text' class='form-control input-sm capitalized_on_date' name='deactivation_on-"+val.no_reg_item+"' value='"+val.deactivation_on+"' id='deactivation_on-"+val.no_reg_item+"' autocomplete='off' placeholder='yyyy-mm-dd' required <?php echo $readonly; ?>></div></div>";
                        
                        item += "</div>";
                        
                        item += "<div class='col-md-6'> ";

                        item += "<div class='form-group'><label for='' class='col-md-4'>BUSINESS AREA</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='business_area-"+val.no_reg_item+"' value='"+val.business_area+"' id='business_area-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>COST CENTER <sup style='color:red'>*</sup></label><div class='col-md-8'><input type='text' class='form-control input-sm' name='cost_center-"+val.no_reg_item+"' value='"+val.cost_center+"' id='cost_center-"+val.no_reg_item+"' autocomplete='off' minlength='10' maxlength='10' required <?php echo $readonly; ?>></div></div>";

                        item += "<div class='form-group'><label for='' class='col-md-4'>PLANT</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='plant-"+val.no_reg_item+"' value='"+val.business_area+"' id='plant-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";

                        if( val.po_type == 2 )
                        {
                            <?php if( $user_role == 'AMS' ){ ?>

                            var kode_vendor = val.vendor.split('-');
                            //var jenis_asset_code = jenis_asset.split('-');

                            item += "<div class='form-group'><label for='' class='col-md-4'>VENDOR</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='vendor-"+val.no_reg_item+"' value='"+kode_vendor[0]+"' id='vendor-"+val.no_reg_item+"' autocomplete='off' placeholder='Masukkan Kode Vendor'> <div class='btn btn-warning btn-sm' value='Update' OnClick='updateKodeVendor(\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-save'></i> UPDATE VENDOR</div></div></div>";
                            <?php } ?>
                        }
                        else
                        {
                            item += "<div class='form-group'><label for='' class='col-md-4'>VENDOR</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='vendor-"+val.no_reg_item+"' value='"+val.vendor+"' id='vendor-"+val.no_reg_item+"' autocomplete='off' readonly></div></div>";
                        }

                        item += "<div class='xform-group'><label for='' class='xcol-md-4'>DEPREC, AREAS</label><br/>";
                        item += "<table class='tabel table-bordered table-responsive table-condensed table-striped table-container'>";
                        item += "<tr><th>Area Number</th><th>Depreciation Area</th><th>Dkey</th><th>Use Life</th></tr>";
                        item += "<tr><td>01</td><td>Book</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='book_deprec_01-"+val.no_reg_item+"' value='"+val.book_deprec_01+"' id='book_deprec_01-"+val.no_reg_item+"' autocomplete='off' required <?php echo $readonly; ?>></td></tr>";
                        item += "<tr><td>15</td><td>Fiscal</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='fiscal_deprec_15-"+val.no_reg_item+"' value='"+val.fiscal_deprec_15+"' id='fiscal_deprec_15-"+val.no_reg_item+"' autocomplete='off' onkeyup='fiscalgroup("+val.no_reg_item+")' required <?php echo $readonly; ?>></td></tr>";
                        item += "<tr><td>30</td><td>Group</td><td>Z001</td><td><input type='text' class='form-control input-sm' name='group_deprec_30-"+val.no_reg_item+"' value='"+val.group_deprec_30+"' id='group_deprec_30-"+val.no_reg_item+"' autocomplete='off' placeholder='' readonly></td></tr>";
                        item += "</table>";
                        item += "</div>";

                        //if( val.deleted == '' ){
                            if(val.kode_asset_sap == '')
                            {
                                <?php if( $user_role == 'AMS' ){ ?>
                                    item += "<div class='form-group' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='saveAssetSap("+val.id+",\""+val.no_po+"\","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-save'></i> SAVE</div></div>";
                                <?php } ?>
                            }
                        //}

                        item += "</div>";
                        item += "</div></div>";
                        /* END BOX DETAIL ASSET SAP */
                    }

                    /* BOX KODE ASET CONTROLLER */
                    if( val.kode_asset_sap != '' && val.po_type == 0 )
                    {
                        item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> KODE ASET</span><br/><br/>";
                        
                        item += "<div class='col-md-4'> ";
                        
                        <?php if( $user_role == 'AC' ){ ?>
                            
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset("+val.no_reg_item+")'><input type='hidden' id='request_kode_aset_input' name='request_kode_aset_input'>"; 
                            
                            if(tipe == 1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiKodeAssetController("+val.po_type+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px;margin-bottom:5px'><i class='fa fa-save'></i> SAVE</div>";
                                item += "<input type='text' class='form-control' placeholder='Jenis Kendaraan' id='jenis-kendaraan' name='jenis-kendaraan'>";
                                item += "<div class='btn btn-info btn-sm' OnClick='printFormIO("+val.asset_po_id+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-print'></i> PRINT FORM IO</div>";
                            }
                            
                            item += "</div></div></div>";

                        <?php }else{ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset("+val.no_reg_item+")' <?php echo $readonly; ?> ><input type='hidden' id='request_kode_aset_input' name='request_kode_aset_input'></div></div>";
                            item += "</div>";
                        <?php } ?>

                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET SAP</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_sap-"+val.no_reg_item+"' value='"+val.kode_asset_sap+"' id='kode_aset_sap-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET FAMS</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_asset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_asset_ams-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";

                        item += "</div></div>";

                        if( val.po_type == 0 || val.po_type == 2 )
                        { //TIDAK JADI IT@250719  ~ HIDE KRN AMP JG MENGINPUT GI IT@170619
                                
                            <?php if( $user_role == 'PGA' ){ ?>

                            /* BOX CHECK GI */
                            item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                            item += "<span class='label bg-blue'><i class='fa fa-bars'></i> MATERIAL DOCUMENT / CHECK GI</span><br/><br/>";

                            item += "<div class='col-md-4'> ";
                            item += "<div class='form-group'><label for='' class='col-md-4'>NUMBER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='md_number-"+val.no_reg_item+"' value='"+val.gi_number+"' id='md_number-"+val.no_reg_item+"' autocomplete='off'></div></div>";
                            item += "</div>";

                            item += "<div class='col-md-4'> ";
                            item += "<div class='form-group'><label for='' class='col-md-4'>YEAR</label><div class='col-md-8'><input type='text' class='form-control input-sm md_year' name='md_year-"+val.no_reg_item+"' value='"+val.gi_year+"' id='md_year-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_check_gi("+val.no_reg_item+","+val.po_type+")'></div></div>";
                            item += "</div>";

                            item += "<div class='col-md-4'>";
                            
                            if(tipe == 1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiGINumberYear("+val.po_type+","+val.no_reg_item+")'><i class='fa fa-save'></i> SAVE GI NUMBER & YEAR</div>";
                            }

                            item += "</div>";

                            item += "</div></div>";
                            /* END BOX CHECK GI */

                            <?php } ?>

                        }
                    }
                    /* END BOX KODE ASET CONTROLLER */

                    /* FOR AMP & ASET LAIN */
                    if( val.po_type == 1 || val.po_type == 2 )
                    {
                        item += "<div class='col-md-12 box-kode-asset-controller'><div class='row'>";
                        item += "<span class='label bg-blue'><i class='fa fa-bars'></i> KODE ASET</span><br/><br/>";
                        
                        item += "<div class='col-md-4'> ";
                        <?php if( $user_role == 'AC' ){ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset_amp("+val.no_reg_item+")'><input type='hidden' class='form-control input-sm' name='kode_aset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_aset_ams-"+val.no_reg_item+"'>";
                            
                            if(tipe==1)
                            {
                                item += "<div class='btn btn-warning btn-sm' OnClick='validasiKodeAssetController("+val.po_type+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px;margin-bottom:5px'><i class='fa fa-save'></i> SAVE</div>";
                                item += "<input type='text' class='form-control' placeholder='Jenis Kendaraan' id='jenis-kendaraan' name='jenis-kendaraan'>";
                                item += "<div class='btn btn-info btn-sm' OnClick='printFormIO("+val.asset_po_id+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px'><i class='fa fa-print'></i> PRINT FORM IO</div>";
                            }
                            
                            item += "</div></div></div>";
                        <?php }else{ ?>
                            item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET CONTROLLER</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset_amp("+val.no_reg_item+")'><input type='hidden' class='form-control input-sm' name='kode_aset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_aset_ams-"+val.no_reg_item+"'></div></div>";
                            item += "</div>";
                        <?php } ?>
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET SAP</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_aset_sap-"+val.no_reg_item+"' value='"+val.kode_asset_sap+"' id='kode_aset_sap-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        item += "</div>";
                        
                        item += "<div class='col-md-4'> ";
                        item += "<div class='form-group'><label for='' class='col-md-4'>KODE ASET FAMS</label><div class='col-md-8'><input type='text' class='form-control input-sm' name='kode_asset_ams-"+val.no_reg_item+"' value='"+val.kode_asset_ams+"' id='kode_asset_ams-"+val.no_reg_item+"' autocomplete='off' readonly='1'></div></div>";
                        
                        item += "</div>";
                        item += "</div></div>";
                    }
                    
                    item += "</div>";
                    item += "</div>";

                    num++;
                });

                item += "<div>";

                item += "</div>";
                item += "</div>";

                if(tipe==3)
                {
                    $("#box-detail-item-disposal").fadeIn();
                    $("#box-detail-item-disposal").html(item);
                }
                else
                {
                    //alert(tipe);
                    $("#box-detail-item-history").fadeIn();
                    $("#box-detail-item-history").html(item);
                }
               

                <?php if( $user_role == 'AMS' ){ ?>
                $.each(data, function(key, val) 
                {
                    trigger_edit_asset(val.no_reg_item,val.jenis_asset,val.group,val.sub_group,val.uom_asset_sap);
                });

                $.each(data, function(key, val) 
                {
                    if( val.po_type == 0 || val.po_type == 2 )
                        {
                                $('#uom-"+val.no_reg_item+"').val(val.uom_asset_sap)
                        }
                });

                <?php } ?>

                var tipekendaraan = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_kendaraan") !!}')));
                $('input[name="jenis-kendaraan"]').select2({
                    data: tipekendaraan,
                    width: '100%',
                    placeholder: ' ',
                    allowClear: true,
                });
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        });  
        
    }

    function trigger_edit_asset(no,jenis_asset,group,sub_group,uom)
    {
        //alert(sub_group); //return false;
        
        var jenis_asset_code = jenis_asset.split('-');
        var role_jenis_asset_code = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset_code") !!}')));
        //$('#jenis_asset-'+no+'').select2({
        $('input[name="jenis_asset-'+no+'"]').select2({    
            data: role_jenis_asset_code,
            width: '100%',
            placeholder: '',
            allowClear: true,
            //readonly: true,
        });
        $('input[name="jenis_asset-'+no+'"]').val(jenis_asset_code[0]).trigger('change');

        var ja_group = group.split('-'); 
        var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroupcondition") !!}?type=' + jenis_asset_code[0] )));
        //var assetgroup = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_group_code") !!}' )));
        $('input[name="jenis_asset_group-'+no+'"]').select2({
            data: assetgroup,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });
        $('input[name="jenis_asset_group-'+no+'"]').val(ja_group[0]).trigger('change');

        var ja_subgroup = sub_group.split('-');
        //var assetsubgroup = $.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group=' + group )));
        var assetsubgroup = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_subgroup_code_condition") !!}?group='+ ja_group[0]+'&jenis_asset='+jenis_asset_code[0]+'&subgroup='+ja_subgroup[0] )));
            $('input[name="jenis_asset_subgroup-'+no+'"]').select2({
                data: assetsubgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
        $('input[name="jenis_asset_subgroup-'+no+'"]').val(ja_subgroup[0]).trigger('change');

        var uom = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_uom") !!}' )));
            //$('input[name="jenis_asset_subgroup-'+no+'"]').empty().select2({
            $('input[name="uom-'+no+'"]').select2({
                data: uom,
                width: "100%",
                allowClear: false,
                placeholder: ' '
            });
        $('input[name="uom-'+no+'"]').val(uom).trigger('change');
    }

    function delAsset(id)
    {
        if(confirm('are you sure?'))
        {
            
            //e.preventDefault();
            var param = $(this).serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/delete_asset') }}/"+id,
                method: "POST",
                data: param,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        $("#approve-modal").modal("hide");
                        $("#data-table").DataTable().ajax.reload();
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
    }

    function saveItemDetail(id,no_po,no_reg_item)
    {
        if(confirm('Confirm Save Rincian Informasi Asset ?'))
        {
            var getnoreg = $("#getnoreg").val();
            var no_registrasi= getnoreg.replace(/\//g, '-');

            var jenis_asset = $("#jenis_asset-"+no_reg_item+"").val();
            var group = $("#jenis_asset_group-"+no_reg_item+"").val();
            var subgroup = $("#jenis_asset_subgroup-"+no_reg_item+"").val();
            //alert(jenis_asset);return false;

            //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

            var param = '';//$("#request-form-detail-asset-sap").serialize();
            //alert(capitalized_on);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/save_item_detail') }}/"+id,
                method: "POST",
                data: param+"&getnoreg="+getnoreg+"&no_po="+no_po+"&no_reg_item="+no_reg_item+"&jenis_asset="+jenis_asset+"&group="+group+"&subgroup="+subgroup,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    //replace old value
    
    $('input').each(function() {
        var inputVal = $(this).val();
            $(this).on('change', function() {
                console.log('Current Value: ',$(this).val());
                console.log('Old Value: ', inputVal);
                inputVal = $(this).val();
        });
    }); 
    

    function saveAssetSap(id,no_po,no_reg_item)
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');

        var nama_asset_1 = $("#nama_asset_1-"+no_reg_item+"").val();
        var nama_asset_2 = $("#nama_asset_2-"+no_reg_item+"").val();
        var nama_asset_3 = $("#nama_asset_3-"+no_reg_item+"").val();
        var quantity = $("#quantity-"+no_reg_item+"").val();
        var uom = $("#uom-"+no_reg_item+"").val();
        var capitalized_on = $("#capitalized_on-"+no_reg_item+"").val();
        var deactivation_on = $("#deactivation_on-"+no_reg_item+"").val();
        var cost_center = $("#cost_center-"+no_reg_item+"").val();
        var book_deprec_01 = $("#book_deprec_01-"+no_reg_item+"").val();
        var fiscal_deprec_15 = $("#fiscal_deprec_15-"+no_reg_item+"").val();
        var group_deprec_30 = $("#group_deprec_30-"+no_reg_item+"").val();

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);

        // VALIDASI QUANTITY ONLY NUMBER
        if(!checkNumericValue(quantity))
        {
            notify({
                type: 'warning',
                message: " Quantity is Numberic value & min 0 "
            });
            return false;
        }

        // VALIDASI COST CENTER HARUS 10 CHAR
        if( $.trim(cost_center).length < 10 )
        {
            notify({
                type: 'warning',
                message: " Cost Center < 10 char "
            });
            return false;
        } 

        // VALIDASI UOM
        if( $.trim(uom) == "" )
        {
            notify({
                type: 'warning',
                message: " UOM is required "
            });
            return false;
        } 

        if(confirm('Confirm Save Detail Aset SAP ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // 'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
                    'Access-Control-Allow-Methods': 'GET, POST',
                }
            });
            $.ajax({
                url: "{{ url('approval/save_asset_sap') }}/"+id,
                method: "POST",
                data: param+"&nama_asset_1="+nama_asset_1+"&nama_asset_2="+nama_asset_2+"&nama_asset_3="+nama_asset_3+"&quantity="+quantity+"&uom="+uom+"&capitalized_on="+capitalized_on+"&deactivation_on="+deactivation_on+"&cost_center="+cost_center+"&book_deprec_01="+book_deprec_01+"&fiscal_deprec_15="+fiscal_deprec_15+"&group_deprec_30="+group_deprec_30+"&getnoreg="+getnoreg+"&no_po="+no_po+"&no_reg_item="+no_reg_item,
                // beforeSend: function() {
                //     $('.loading-event').fadeIn();
                // },
                success: function(result) 
                {
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function changeStatus(status)
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var specification = $("#specification").val();

        if( status == 'A' ){ status_desc = 'approve'; }else
        if( status == 'R' )
        { 
            status_desc = 'reject';
            note_reject = $("#specification").val();

            if( $.trim(note_reject) < 2 )
            {
                notify({
                    type: 'warning',
                    message: " Note Reject is required (min 2 char)"
                });
                return false;
            } 

        }else{ status_desc = 'cancel'; }

        if(confirm('confirm '+status_desc+' data ?'))
        {
            //console.log(request_check_gi); return false;

            //e.preventDefault();
            var param = $("#request-form").serialize();
            var request_ka = JSON.stringify(request_kode_aset_data);
            var request_gi = JSON.stringify(request_check_gi);
            //alert(param); //return false;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: "{{ url('approval/update_status') }}/"+status+"/"+no_registrasi,
                method: "POST",
                data: param+"&parNote="+specification+"&request_ka="+request_ka+"&request_gi="+request_gi,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //SEND EMAIL 
                        send_email_create_po(result.new_noreg);

                        $("#approve-modal").modal("hide");
                        $("#data-table").DataTable().ajax.reload();
                        $("#data-table-history").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                    } 
                    else 
                    {
                        request_check_gi = [];
                        request_kode_aset_data = [];
                        //$(".md_year").val("");
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

    function changeStatusDisposal(status)
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var specification = $("#specification-disposal").val();

        if( status == 'A' ){ status_desc = 'approve'; }else
        if( status == 'R' )
        { 
            status_desc = 'reject';
            note_reject = $("#specification-disposal").val();

            if( $.trim(note_reject) < 2 )
            {
                notify({
                    type: 'warning',
                    message: " Note Reject is required (min 2 char)"
                });
                return false;
            } 

        }else{ status_desc = 'cancel'; }

        if(confirm('confirm '+status_desc+' data ?'))
        {
            //console.log(request_check_gi); return false;

            //e.preventDefault();
            var param = $("#request-form").serialize();
            var request_ka = JSON.stringify(request_kode_aset_data);
            var request_gi = JSON.stringify(request_check_gi);
            //alert(param); //return false;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: "{{ url('approval/update_status_disposal') }}/"+status+"/"+no_registrasi,
                method: "POST",
                data: param+"&parNote="+specification+"&request_ka="+request_ka+"&request_gi="+request_gi,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //SEND EMAIL 
                        send_email_create_po(result.new_noreg);

                        $("#approve-disposal-modal").modal("hide");
                        $("#data-table").DataTable().ajax.reload();
                        $("#data-table-history").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                    } 
                    else 
                    {
                        request_check_gi = [];
                        request_kode_aset_data = [];
                        //$(".md_year").val("");
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

    function history(id)
    {
        //alert(id); //return false;
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); return false;

        $("#box-detail-item-history").hide();
        $("#history-notes").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.no_reg);
                $("#request-form-history #no-reg").val(data.no_reg);
                $("#request-form-history #type-transaksi").val(data.type_transaksi);
                $("#request-form-history #po-type").val(data.po_type);
                $("#request-form-history #business-area").val(data.business_area);
                $("#request-form-history #requestor").val(data.requestor);
                $("#request-form-history #tanggal-reg").val(data.tanggal_reg);
                $("#request-form-history #kode-vendor").val(data.kode_vendor);
                $("#request-form-history #nama-vendor").val(data.nama_vendor);

                var item = '<table class="table xtable-condensed table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>NO PO</th>';
                item += '<th>ITEM PO</th>';
                item += '<th>QTY</th>';
                item += '<th>KODE MATERIAL</th>';
                item += '<th>NAMA MATERIAL</th>';
                item += '<th>VIEW DETAIL</th>';
                if (data.item_detail.length > 0) 
                {
                    var no = 1;
                    $.each(data.item_detail, function(key, val) 
                    {
                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.no_po + "</td>";
                        item += "<td>" + val.item + "</td>";
                        item += "<td>" + val.qty + "</td>";
                        item += "<td>" + val.kode + "</td>";
                        item += "<td>" + val.nama + "</td>";
                        item += "<td><i class='fa fa-eye' OnClick='getDetailItem(\""+noreg+"\","+val.id+",2,"+no+")'></i></td>";
                        item += "</tr>";
                        no++;
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';

                log_history(id,2);

                $("#box-item-detail-history").html(item);

                $("#history-modal .modal-title").html("<i class='fa fa-edit'></i>  HISTORY APPROVAL PENDAFTARAN - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#history-modal').modal('show');
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function history_disposal(id)
    {
        //alert(id); //return false;
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); return false;

        $("#box-detail-item-history-disposal").hide();
        $("#history-notes-disposal").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_disposal') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.no_reg);
                $("#request-form-history #no-reg").val(data.no_reg);
                $("#request-form-history #type-transaksi").val(data.type_transaksi);
                $("#request-form-history #po-type").val(data.po_type);
                $("#request-form-history #business-area").val(data.business_area);
                $("#request-form-history #requestor").val(data.requestor);
                $("#request-form-history #tanggal-reg").val(data.tanggal_reg);
                $("#request-form-history #kode-vendor").val(data.kode_vendor);
                $("#request-form-history #nama-vendor").val(data.nama_vendor);

                var item = '<table class="table xtable-condensed table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>KODE ASSET AMS</th>';
                item += '<th>KODE ASSET SAP</th>';
                item += '<th>NAMA MATERIAL</th>';
                item += '<th>LOKASI BA CODE</th>';
                item += '<th>NAMA ASSET</th>';
                item += '<th>HARGA PEROLEHAN (RP)</th>';
                item += '<th>NILAI BUKU (RP)</th>';
                item += '<th>VIEW DETAIL</th>';
                if (data.item_detail.length > 0) 
                {
                    var no = 1;

                    $.each(data.item_detail, function(key, val) 
                    {
                        var kode_fams = btoa(val.kode_asset_ams);
                        var jenis_pengajuan = val.jenis_pengajuan;
                        
                        if( jenis_pengajuan == 1 )
                        {
                            pengajuan = 'PENJUALAN';
                        }
                        else if( jenis_pengajuan == 2 )
                        {
                            pengajuan = 'HILANG';
                        }else{
                            pengajuan = 'RUSAK';
                        }

                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.kode_asset_ams + "</td>";
                        item += "<td>" + val.kode_asset_sap + "</td>";
                        item += "<td>" + val.nama_material + "</td>";
                        item += "<td>" + val.lokasi_ba_description + "</td>";
                        item += "<td>" + val.nama_asset_1 + "</td>";
                        item += "<td>" + val.harga_perolehan + "</td>";
                        item += "<td>" + val.nilai_buku + "</td>";

                        item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a></td>";
                        //item += "<td><i class='fa fa-eye' OnClick='getDetailItem(\""+noreg+"\","+val.id+",2,"+no+")'></i></td>";
                        item += "</tr>";
                        no++;
                        
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';

                log_history(id,4);

                $("#box-item-detail-history-disposal").html(item);

                $("#history-modal-disposal .modal-title").html("<i class='fa fa-edit'></i>  HISTORY DISPOSAL "+ pengajuan +" - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#history-modal-disposal').modal('show');
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function log_history(id,tipe)
    {
        //alert(tipe);
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); return false;

        $.ajax({
        type: 'GET',
        url: "{{ url('approval/log_history') }}/"+noreg,
        data: "",
        dataType: 'json',
        success: function(data) 
        {
            //alert(data.length); 
            var item = '<br/><span class="label bg-blue"><i class="fa fa-bars"></i> LOG HISTORY</span> <br/><br/>';
            item += '<table class="table xtable-condensed table-responsive table-striped" id="request-item-table" style="font-size:13px">';
            item += '<th>NO.</th>';
            item += '<th>AREA CODE</th>';
            item += '<th>USER ID</th>';
            item += '<th>NAME</th>';
            //item += '<th>STATUS DOKUMEN</th>';
            item += '<th>STATUS APPROVAL</th>';
            item += '<th>NOTES</th>';
            item += '<th>DATE</th>';
            if (data.length > 0) 
            {
                no = 1;
                $.each(data, function(key, val) 
                {
                    item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                    item += "<td>" + no + "</td>";
                    item += "<td>" + val.area_code + "</td>";
                    item += "<td>" + val.user_id + "</td>";
                    item += "<td>" + val.name + "</td>";
                    //item += "<td>" + val.status_dokumen + "</td>";
                    item += "<td>" + val.status_approval + "</td>";
                    item += "<td>" + val.notes + "</td>";
                    item += "<td>" + val.date + "</td>";
                    item += "</tr>";
                    no++;
                });
            }
            else
            {
                item += '<tr>';
                item += ' <td colspan="8" style="text-align:center">-</td>';
                item += '</tr>';
            }
            item += '</table>';

            
            if(tipe==2)
            {
                $("#log-history-box").html(item);
            }
            else if( tipe == 1 )
            {
                $("#log-history-box-outstanding").html(item);
            }
            else if( tipe == 4 )
            {
                $("#log-history-box-disposal-history").html(item);
            }
            else if( tipe == 5 )
            {
                $("#log-history-box-mutasi").html(item);
            }
            else if( tipe == 6 )
            {
                $("#log-history-outstanding-mutasi").html(item);
            }
            else
            {
                $("#log-history-box-disposal").html(item);
            }

        },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function sinkronisasi()
    {
        //var noreg = $("#no-reg").val();
        //alert(noreg);

        $("#box-detail-item").hide();

        if(confirm('Confirm Synchronize SAP ?'))
        {
            var getnoreg = $("#getnoreg").val();
            var no_registrasi= getnoreg.replace(/\//g, '-');

            //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

            var param = '';//$("#request-form-detail-asset-sap").serialize();
            //alert(capitalized_on);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/synchronize_sap') }}",
                method: "POST",
                data: param+"&noreg="+getnoreg,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });

                        $("#create-button-sync-sap").hide();
                        $("#button-approve").show();
                        $(".button-reject").attr("disabled", true); 
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

    function sinkronisasi_amp()
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        //alert(noreg);

        $("#box-detail-item").hide();

        if(confirm('Confirm Submit Data ?'))
        {   
            var param = $("#request-form").serialize();
            //alert(param); //return false;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: "{{ url('approval/synchronize_amp') }}",
                method: "POST",
                data: param+"&noreg="+getnoreg,
                beforeSend: function() {
                    jQuery('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });

                        $("#create-button-sync-sap").hide();
                        $("#button-approve").show();
                        $(".button-reject").attr("disabled", true); 
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

    function fiscalgroup(no)
    {
        var fiscal = $("#fiscal_deprec_15-"+no+"").val();
        $("#group_deprec_30-"+no+"").val(fiscal);
    }

    function get_kode_aset(no)
    {
        var ka_con = $("#kode_aset_controller-"+no+"").val();
        var ka_sap = $("#kode_asset_ams-"+no+"").val();
        var noreg = $("#no-reg").val();
        
        request_kode_aset_data.push({
            kode_aset_controller: ka_con,
            kode_aset_sap: ka_sap,
            no_registrasi: noreg,
        });
    }

    function get_kode_aset_amp(no)
    {
        //alert(no);
        var ka_con = $("#kode_aset_controller-"+no+"").val();
        var ka_sap = $("#kode_aset_ams-"+no+"").val();
        var noreg = $("#no-reg").val();
        //alert(ka_sap); return false;

        /* #1 */ 
        request_kode_aset_data.push({
            kode_aset_controller: ka_con,
            kode_aset_sap: ka_sap,
            no_registrasi: noreg,
        });
    }

    function get_check_gi(no, po_type)
    {
        //alert(po_type); return false;
        var cgi_number = $("#md_number-"+no+"").val();
        //alert(cgi_number);
        var cgi_year = $("#md_year-"+no+"").val();
        
        if(po_type == 1)
        {
            //AMP
            var csap = $("#kode_aset_ams-"+no+"").val();
        }
        else
        {
            //SAP
            var csap = $("#kode_aset_sap-"+no+"").val();
        }
        
        
        var cnoreg = $("#no-reg").val();

        request_check_gi.push(
        {
            gi_number: cgi_number,
            gi_year: cgi_year,
            kode_sap : csap,
            no_registrasi:cnoreg
        })
    }

    function reload_page(){window.location.href = "{{ url('/') }}";}

    function send_email_create_po(noreg)
    {
        //alert(noreg);

        var getnoreg = noreg;
        var no_registrasi= getnoreg.replace(/\//g, '-');

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('request/email_create_po') }}",
            // method: "POST",
            type: "POST",
            // data: param+"&noreg="+no_registrasi,
            data: "noreg="+no_registrasi,
            beforeSend: function() {
                $('.loading-event').fadeIn();
            },
            success: function(result){},
            complete: function() {
                jQuery('.loading-event').fadeOut();
            }
        }); 
    }

    function get_group(no)
    {
        var jenis_asset_code = $('input[name="jenis_asset-'+no+'"]').val();
        //alert(jenis_asset_code);
        if(jenis_asset_code == ''){
            jenis_asset_code = $('input[name="jenis_asset-'+no+'"]').select2('data')[0].id;
        }
        var assetgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetgroup") !!}?type='+jenis_asset_code )));
        $('input[name="jenis_asset_group-'+no+'"]').empty().select2({
            data: assetgroup,
            width: "100%",
            allowClear: true,
            placeholder: ' '
        });
    }

    function get_subgroup(no)
    {
        var jenis_asset_code = $('input[name="jenis_asset-'+no+'"]').val();
        if(jenis_asset_code == ''){
            jenis_asset_code = $('input[name="jenis_asset-'+no+'"]').select2('data')[0].id;
        }

        var group = $('input[name="jenis_asset_group-'+no+'"]').val();
        if(group == ''){
            group = $('input[name="jenis_asset_group-'+no+'"]').select2('data')[0].id;
        }
        var assetsubgroup = jQuery.parseJSON(JSON.stringify(dataJson('{!! route("get.assetsubgroup") !!}?group='+group+'&jenis_asset_code='+jenis_asset_code )));
            $('input[name="jenis_asset_subgroup-'+no+'"]').empty().select2({
                data: assetsubgroup,
                width: "100%",
                allowClear: true,
                placeholder: ' '
            });
    }

    function checkNumericValue(num)
    {
        if(num.indexOf('-') != 0)
        { 
            var objRegExp  =  /(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/;
            return objRegExp.test(num);
        }
        else
        {
            return false;
        }
    }

    function updateKodeVendor(no_po,no_reg_item)
    {
        //alert(noreg); 
        //alert(no_po); 
        //alert(kode_vendor_update); //return false;

        var getnoreg = $("#getnoreg").val(); //alert(getnoreg);
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var new_kode_vendor = $("#vendor-"+no_reg_item+"").val();

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);

        // VALIDASI COST CENTER HARUS 10 CHAR
        if( $.trim(new_kode_vendor) < 2 )
        {
            notify({
                type: 'warning',
                message: " Kode Vendor belum diisi (min 2 char)"
            });
            return false;
        } 

        if(confirm('Confirm Update Kode Vendor '+getnoreg+' ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/update_kode_vendor_aset_lain') }}",
                method: "POST",
                data: param+"&new_kode_vendor="+new_kode_vendor+"&getnoreg="+getnoreg+"&no_po="+no_po+"&no_reg_item="+no_reg_item,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function validasiKodeAssetControllerMutasi(po_type,no_reg_item)
    {
        var getnoreg = $("#getnoreg").val(); //alert(getnoreg);
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var kode_asset_controller = $("#request-form #kode_aset_controller-"+no_reg_item+"").val();
        if(po_type == 1 || po_type == 2 )
        {
            // AMP & LAIN
            var kode_asset_nilai = $("#request-form #kode_asset_ams-"+no_reg_item+"").val();
        }
        else
        {
            // SAP
            var kode_asset_nilai = $("#request-form #kode_aset_sap-"+no_reg_item+"").val();
        }
        

        var param = '';

        // VALIDASI KODE ASSET CONTROLLER 2 CHAR
        if( $.trim(kode_asset_controller) < 2 )
        {
            console.log(kode_asset_controller);
            notify({
                type: 'warning',
                message: " Kode Asset Controller belum diisi (min 2 char)"
            });
            return false;
        } 

        if(confirm('Confirm Update Code Asset Controller '+getnoreg+' ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/update_kode_asset_controller_mutasi') }}",
                method: "POST",
                data: param+"&kode_asset_controller="+kode_asset_controller+"&getnoreg="+getnoreg+"&kode_asset_nilai="+kode_asset_nilai+"&no_reg_item="+no_reg_item+"&po_type="+po_type,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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
    function validasiKodeAssetController(po_type,no_reg_item)
    {
        //alert(po_type); return false(); 
        //alert(no_po); 
        //alert(kode_vendor_update); //return false;

        var getnoreg = $("#getnoreg").val(); //alert(getnoreg);
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var kode_asset_controller = $("#kode_aset_controller-"+no_reg_item+"").val();
        
        if(po_type == 1 || po_type == 2 )
        {
            // AMP & LAIN
            var kode_asset_nilai = $("#kode_asset_ams-"+no_reg_item+"").val();
        }
        else
        {
            // SAP
            var kode_asset_nilai = $("#kode_aset_sap-"+no_reg_item+"").val();
        }
        

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);

        // VALIDASI COST CENTER HARUS 10 CHAR
        if( $.trim(kode_asset_controller) < 2 )
        {
            notify({
                type: 'warning',
                message: " Kode Asset Controller belum diisi (min 2 char)"
            });
            return false;
        } 

        if(confirm('Confirm Update Code Asset Controller '+getnoreg+' ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/update_kode_asset_controller') }}",
                method: "POST",
                data: param+"&kode_asset_controller="+kode_asset_controller+"&getnoreg="+getnoreg+"&kode_asset_nilai="+kode_asset_nilai+"&no_reg_item="+no_reg_item+"&po_type="+po_type,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function validasiGINumberYear(po_type,no_reg_item)
    {
        //alert(po_type); return false(); 
        //alert(no_po); 
        //alert(kode_vendor_update); //return false;

        var getnoreg = $("#getnoreg").val(); //alert(getnoreg);
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var md_number = $("#md_number-"+no_reg_item+"").val();
        var md_year = $("#md_year-"+no_reg_item+"").val();
        var ka_sap = $("#kode_aset_sap-"+no_reg_item+"").val();

        var param = '';

        // VALIDASI YEAR HARUS 4 CHAR
        if( $.trim(md_year) < 4 )
        {
            notify({
                type: 'warning',
                message: " Year is required (4 char)"
            });
            return false;
        } 

        if(confirm('Confirm Save Number '+md_number+' & Year '+md_year+' ?'))
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/save_gi_number_year') }}",
                method: "POST",
                data: param+"&md_number="+md_number+"&getnoreg="+getnoreg+"&md_year="+md_year+"&no_reg_item="+no_reg_item+"&po_type="+po_type+"&ka_sap="+ka_sap,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        //$("#approve-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function printFormIO(asset_po_id,no_reg_item)
    {
        //alert(asset_po_id);
        var getnoreg = $("#getnoreg").val(); //alert(getnoreg); return false;
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var jenis_kendaraan = $("#jenis-kendaraan-"+no_reg_item+"").val();

        if( jenis_kendaraan == '' )
        {
            notify({
                type: 'warning',
                message: " Jenis Kendaraan is required ! "
            });
            return false;
        }
           
        $('#pdf-modal .modal-title').text('FORM PARAMETER INTERNAL ORDER '+getnoreg+'');
        $('#pdf-modal .modal-body').html('<iframe id="print" style="width:100%;height:500px;" frameborder="0" src="{{ url("printio") }}/'+no_registrasi+'/'+asset_po_id+'/'+jenis_kendaraan+'/'+no_reg_item+'">');
        $('#pdf-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#print-modal').modal('show');
    }

    function printFormIOMutasi(asset_po_id,no_reg_item)
    {
        //alert(asset_po_id);
        var getnoreg = $("#getnoreg").val(); //alert(getnoreg); return false;
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var jenis_kendaraan = $("#request-form jenis-kendaraan-"+no_reg_item+"").val();

        if( jenis_kendaraan == '' )
        {
            notify({
                type: 'warning',
                message: " Jenis Kendaraan is required ! "
            });
            return false;
        }
           
        $('#pdf-modal .modal-title').text('FORM PARAMETER INTERNAL ORDER '+getnoreg+'');
        $('#pdf-modal .modal-body').html('<iframe id="print" style="width:100%;height:500px;" frameborder="0" src="{{ url("printio") }}/'+no_registrasi+'/'+asset_po_id+'/'+jenis_kendaraan+'/'+no_reg_item+'">');
        $('#pdf-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#print-modal').modal('show');
    }

    function delDisposal(noreg,kode_asset_ams)
    {
        //alert(noreg)
        if(confirm('Confirm Delete ?'))
        {
            var no_registrasi= noreg.replace(/\//g, '-');
            var param = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/delete_asset_disposal') }}",
                method: "POST",
                data: param+"&getnoreg="+no_registrasi+"&kode_asset_ams="+kode_asset_ams,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        $("#approve-disposal-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function opt_tipe_kendaraan(no_reg_item)
    {
        var tipekendaraan = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_kendaraan") !!}')));
        $('input[name="jenis-kendaraan-'+no_reg_item+'"]').select2({
            data: tipekendaraan,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
        });
    }

    function opt_jenis_asset(no)
    {
        var jenis_asset = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset") !!}')));
        $('input[name="jenis_asset-'+no+'"]').select2({
            data: jenis_asset,
            width: '100%',
            placeholder: ' ',
            allowClear: true,
        });
    }
    
    function history_mutasi(id)
    {
        //alert(id); return false;
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        //alert(noreg); return false;

        //$("#box-detail-item-history-mutasi").hide();
        $("#history-notes-mutasi").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_mutasi') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.no_reg);
                <?php // if( $user_role == 'AMS' && $data['outstanding'] != 0 ) { ?>
                    //  var costcenter = '';
                <?php // } if( $user_role == 'AMS' && $data['outstanding'] != 1 ) { ?>
                     // var costcenter = data.cost_center;
                <?php // } else { ?>
                    //var costcenter = data.cost_center;
                <?php  //}?>
                var costcenter = data.cost_center;
                
                $("#request-form-history #no-reg").val(data.no_reg);
                $("#request-form-history #type-transaksi").val(data.type_transaksi);
                $("#request-form-history #po-type").val(data.po_type);
                $("#request-form-history #ba-pemilik-asset").val(data.ba_pemilik_asset);
                $("#request-form-history #requestor").val(data.requestor);
                $("#request-form-history #tanggal-reg").val(data.tanggal_reg);
                $("#request-form-history #kode-vendor").val(data.kode_vendor);
                $("#request-form-history #nama-vendor").val(data.nama_vendor);
                $("#request-form-history #ba-tujuan").val(data.ba_tujuan);
                $("#request-form-history #cost-center").val(costcenter);

                var item = '<table class="table xtable-condensed table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>KODE ASSET AMS</th>';
                item += '<th>KODE ASSET SAP</th>';
                item += '<th>NAMA ASSET</th>';
                item += '<th>LOKASI BA CODE</th>';
                item += '<th>TUJUAN</th>';
                item += '<th>KODE ASSET AMS TUJUAN</th>';
                item += '<th>KODE SAP TUJUAN</th>';
                item += '<th>VIEW DETAIL</th>';
                if (data.item_detail.length > 0) 
                {
                    var no = 1;

                    $.each(data.item_detail, function(key, val) 
                    {
                        var kode_fams = btoa(val.kode_asset_ams);
                        var kode_tujuan = btoa(val.kode_asset_ams_tujuan);
                        var jenis_pengajuan = val.jenis_pengajuan;
                        
                        if( jenis_pengajuan == 1 )
                        {
                            pengajuan = 'AMP';
                        }
                        else{
                            pengajuan = 'NON AMP';
                        }

                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.kode_asset_ams + "</td>";
                        item += "<td>" + val.kode_asset_sap + "</td>";
                        item += "<td>" + val.nama_asset_1 + "</td>";
                        item += "<td>" + val.lokasi_ba_description + "</td>";
                        item += "<td>" + val.tujuan + "</td>";
                        item += "<td>" + val.kode_asset_ams_tujuan + "</td>";
                        item += "<td>" + val.kode_sap_tujuan + "</td>";

                        if(data.new_asset > 0) { 
                            item += "<td width='150px'><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i> Awal</a>";
                            item += "&emsp;<a href='<?php {{ echo url("/master-asset/show"); }} ?>/"+kode_tujuan+"' target='_blank'><i class='fa fa-eye'></i> Tujuan</a></td>";
                        } 
                        else {
                            item += "<td width='70px'><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a>";
                        }
                        item += "</tr>";
                        no++;
                        
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';

                log_history(id,5);

                $("#box-item-detail-history-mutasi").html(item);

                $("#history-modal-mutasi .modal-title").html("<i class='fa fa-edit'></i>  HISTORY MUTASI "+ pengajuan +" - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#history-modal-mutasi').modal('show');
            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function approval_mutasi(id)
    {
        //alert(id); return false;
        var kata = id;
        var noreg= kata.replace(/\//g, '-');
        var area_code = "<?php echo $user_area_code ?>";
        //alert(noreg); //return false;
        
        $("#box-detail-item-mutasi").hide();

        $.ajax({
            type: 'GET',
            url: "{{ url('approval/view_mutasi') }}/"+noreg,
            data: "",
            //async: false,
            dataType: 'json',
            success: function(data) 
            { 
                //alert(data.cek_reject);
                <?php //if( $user_role == 'AMS' && $data['outstanding'] != 0 ) { ?>
                    // var costcenter = '';
                <?php //} else { ?>
                    //var costcenter = data.cost_center;
                <?php // }?>
                var costcenter = data.cost_center;
                var po_type = data.po_type;
                $("#request-form #no-reg").val(data.no_reg);
                $("#request-form #type-transaksi").val(data.type_transaksi);
                $("#request-form #po-type").val(data.po_type);
                $("#request-form #ba-pemilik-asset").val(data.ba_pemilik_asset);
                $("#request-form #requestor").val(data.requestor);
                $("#request-form #tanggal-reg").val(data.tanggal_reg);
                $("#request-form #kode-vendor").val(data.kode_vendor);
                $("#request-form #nama-vendor").val(data.nama_vendor);
                $("#request-form #cost-center").val(costcenter);
                $("#request-form #cost-center-old").val(data.cost_center);
                $("#request-form #ba-tujuan").val(data.ba_tujuan);
                $("#request-form #kode-asset-ams").val(data.kode_asset_ams);

                //VALIDASI SYNC VIEW SAP
                //alert(data.sync_sap); 
                if(data.sync_sap != '')
                {
                    $("#create-button-sync-sap-mutasi").show();
                    $("#create-button-sync-sap-mutasi").html('<button type="button" class="btn btn-flat label-danger" OnClick="sinkronisasi_mutasi()" style="margin-right: 5px;">SYNC SAP (MUTASI)</button>');
                    
                    <?php if( $user_role == 'AC' ){ ?>
                        $("#button-approve-mutasi").hide();
                        $(".button-reject-mutasi").attr("disabled", false); 
                    <?php } ?>
                }
                else
                {
                    $("#create-button-sync-sap-mutasi").hide();
                    if(data.cek_reject==0){$("#button-approve-mutasi").show();}
                    //$(".button-reject").attr("disabled", true); 
                    $(".button-reject-mutasi").hide(); 
                }

                /*
                $("#create-button-sync-sap").hide();
                if(data.cek_reject==0){$("#button-approve").show();}
                $(".button-reject").show(); 
                */  

              

                var item = '<table class="table table-responsive table-striped" id="request-item-table" style="font-size:13px">';
                item += '<th>NO.</th>';
                item += '<th>KODE ASSET AMS</th>';
                item += '<th>KODE ASSET SAP</th>';
                item += '<th>NAMA ASSET</th>';
                item += '<th>LOKASI BA CODE</th>';
                item += '<th>TUJUAN</th>';
                item += '<th width="120px">JENIS ASSET TUJUAN</th>';
                item += '<th width="120px">GROUP</th>';
                item += '<th width="120px">SUB GROUP</th>';
                item += '<th>KODE ASSET AMS TUJUAN</th>';
                item += '<th>KODE SAP TUJUAN</th>';
                // item += '<th>GROUP</th>';
                // item += '<th>SUB GROUP</th>';
                item += '<th>ASSET CONTROLLER</th>';
                item += '<th>KODE ASSET CONTROLLER</th>';
                <?php if( $user_role == 'AC' ){ ?>
                item += '<th>JENIS KENDARAAN</th>';
                <?php }?>
                
                var no = 1;
                $.each(data.item_detail, function(key, val) 
                    {
                        if(area_code.includes(val.tujuan)){
                            if(no > 1){
                                return false;
                            }
                            item += '<th>PENANGGUNG JAWAB</th>';
                            item += '<th>JABATAN</th>';
                            
                        }
                        no++;
                    });
                item += '<th>VIEW DETAIL</th>';

                if (data.item_detail.length > 0) 
                {
                    var no = 1;
                    
                    $.each(data.item_detail, function(key, val) 
                    {
                        var doc_no = val.document_code.replace(/\//g, '-');
                        var kode_fams = btoa(val.kode_asset_ams);
                        var jenis_pengajuan = val.jenis_pengajuan;
                        
                        if( jenis_pengajuan == 1 )
                        {
                            pengajuan = 'AMP';
                        }
                        else{
                            pengajuan = 'NON AMP';
                        }

                              
                        var jenis_asset = $.parseJSON(JSON.stringify(dataJson('{!! route("get.select_jenis_asset") !!}')));
                        var length = jenis_asset.length;
                        // // var dataoption = "";
                        // var selected = "";
                        // for(var j = 0; j < length; j++)
                        // {                            
                        //     if(val.jenis_asset == jenis_asset[j].text){
                        //         selected = "selected='selected'";
                        //     }else{
                        //         selected = "";
                        //     }
                        // //     dataoption += "<option value='"+jenis_asset[j].id+"' "+ selected +">"+jenis_asset[j].text+"</option>";
                        // }


                        item += "<tr style='height: 30px !important;font-size:11px !important;'>";
                        item += "<td>" + no + "</td>";
                        item += "<td>" + val.kode_asset_ams + "</td>";
                        item += "<td>" + val.kode_asset_sap + "</td>";
                        item += "<td>" + val.nama_asset_1 + "</td>";
                        item += "<td>" + val.lokasi_ba_description + "</td>";
                        item += "<td>" + val.tujuan + "</td>";
                        <?php if( $user_role == 'AMS' ){ ?>
                                        if(val.kode_asset_ams_tujuan === "") { 
                                            item += "<td width='10%'><input type='text' class='form-control input-sm select-jenis-aset' name='jenis_asset-"+val.no_reg_item+"' id='jenis_asset-"+val.no_reg_item+"' autocomplete='off' onChange='get_group("+val.no_reg_item+")' ><input type='hidden' name='noreg-"+no+"' id='noreg-"+no+"' value='"+val.no_reg_item+"'><input type='hidden' class='form-control input-sm' name='asset_class[]' id='asset_class[]' value='"+ val.kode_asset_class +"'></td>";
                                            item += "<td width='10%'><input type='text' class='form-control input-sm' id='jenis_asset_group-"+val.no_reg_item+"' name='jenis_asset_group-"+val.no_reg_item+"'  id='' autocomplete='off' onChange='get_subgroup("+val.no_reg_item+")'></td>";
                                            item += "<td width='10%'><input type='text' class='form-control input-sm' id='jenis_asset_subgroup-"+val.no_reg_item+"' name='jenis_asset_subgroup-"+val.no_reg_item+"'  id='' autocomplete='off' ></td>";
                                        }
                                        else{
                                            item += "<td>" + val.jenis_asset_tujuan + "</td>";
                                            item += "<td>" + val.group_tujuan + "</td>";
                                            item += "<td>" + val.sub_group_tujuan + "</td>";
                                        }
                        <?php } else {?>
                                    item += "<td>" + val.jenis_asset_tujuan + "</td>";
                        <?php  } ?>
                        item += "<td>" + val.kode_asset_ams_tujuan + "</td>";
                        item += "<td>" + val.kode_sap_tujuan + "</td>";
                        // item += "<td>" + val.group + "</td>";
                        // item += "<td>" + val.sub_group + "</td>";
                        item += "<td>" + val.asset_controller + "</td>";
                        <?php if( $user_role == 'AC' ){ ?>
                        
                                item += "<input type='hidden' class='form-control input-sm' name='kode_asset_ams-"+val.no_reg_item+"' id='kode_asset_ams-"+val.no_reg_item+"' value='"+ val.kode_asset_ams +"'>";
                                item += "<td><input type='text' class='form-control input-sm' name='kode_aset_controller-"+val.no_reg_item+"' value='"+val.kode_asset_controller+"' id='kode_aset_controller-"+val.no_reg_item+"' autocomplete='off' onkeyup='get_kode_aset("+val.no_reg_item+")'><input type='hidden' id='request_kode_aset_input' name='request_kode_aset_input'><div class='btn btn-warning btn-sm' OnClick='validasiKodeAssetControllerMutasi("+po_type+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px;margin-bottom:5px'><i class='fa fa-save'></i> SAVE</div></td>";
                                item += "<td><input type='text' class='form-control' placeholder='Jenis Kendaraan' id='jenis-kendaraan-"+val.no_reg_item+"' name='jenis-kendaraan-"+val.no_reg_item+"'><div class='btn btn-info btn-sm' OnClick='printFormIOMutasi("+val.asset_po_id+","+val.no_reg_item+")' style='margin-right:25px;margin-top:5px' data-toggle='modal' data-dismiss='modal'><i class='fa fa-print'> PRINT FORM IO</i></div></td>";
                        <?php } else {?>
                                    item += "<td>" + val.kode_asset_controller + "</td>";
                        <?php  } ?>
                        if(area_code.includes(val.tujuan)){
                            item += "<td><input type='text' class='form-control input-sm' name='penanggung_jawab[]' id='penanggung_jawab[]' value='"+ val.penanggung_jawab +"' required></td>";
                            item += "<td><input type='text' class='form-control input-sm' name='jabatan[]' id='jabatan[]' value='"+ val.jabatan +"' required></td>";
                        }
                        if(data.item_detail.length != 1)
                        {
                            item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a> </td>";
                            // item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a> &nbsp;&nbsp;&nbsp; <i class='fa fa-trash' style='color:red' OnClick='delMutasi(\""+data.no_reg+"\","+val.kode_asset_ams+")'></i> </td>";
                        }
                        else
                        {
                            item += "<td><a href='<?php {{ echo url("/master-asset/show-data"); }} ?>/"+kode_fams+"' target='_blank'><i class='fa fa-eye'></i></a></td>";
                        }
                        
                        item += "<input type='hidden' class='form-control input-sm' name='kode_asset_ams[]' id='kode_asset_ams[]' value='"+ val.kode_asset_ams +"'>";
                       
                        item += "</tr>";

                        // $('body').on('change', '#jenis_asset-'+no, function() {
                        //     $("option", $(this)).removeAttr("selected");
                        //     $("option:selected", $(this)).attr("selected", true);
                        //     var a = $(this).val();
                        //     // console.log(txtasset[no]);
                        //     // var index = txtasset.indexOf(txtasset[no]);
                        //     // if (index !== -1) {
                        //         txtasset[no] = $(this).val();
                        //     // }
                        //     // txtasset[no] = $(this).val();
                        // })

                        no++;
                   
                    });
                    var i = 1;
                    $.each(data.item_detail, function(key, val) 
                    {
                        if(area_code.includes(val.tujuan)){
                            if(i > 1){
                                return false;
                            }
                            item += "<tr><td colspan='15' align='right'><div class='btn btn-warning btn-sm' value='Save' OnClick='update_pic()' style='margin-right:5px;xmargin-top:5px'><i class='fa fa-save'></i> SAVE</div></td></tr>"
                            
                        }
                        i++;
                    });
                }
                else
                {
                    item += '<tr>';
                    item += ' <td colspan="7" style="text-align:center">No item selected</td>';
                    item += '</tr>';
                }
                item += '</table>';
                $("#box-item-detail-mutasi").html(item);
  

                log_history(id,6);
                $("#log-history-box-mutasi").html(item);

                $("#approve-mutasi-modal .modal-title").html("<i class='fa fa-edit'></i> APPROVAL MUTASI "+ pengajuan +" - <span style='color:#dd4b39'>" + data.no_reg + "</span><input type='hidden' id='getnoreg' name='getnoreg' value='"+data.no_reg+"' >");

                $('#approve-mutasi-modal').on('shown.bs.modal', function () {
                    <?php if( $user_role == 'AC' ){ ?>
                    $.each(data.item_detail, function(key, val) 
                    {
                        opt_tipe_kendaraan(val.no_reg_item);
                    });
                    <?php } ?>    

                    <?php if( $user_role == 'AMS' ){ ?>
                    // var i = 1;
                    $.each(data.item_detail, function(key, val) 
                    {
                        opt_jenis_asset(val.no_reg_item);
                        i++;
                    });
                    <?php } ?>  
                    

                });

                $('#approve-mutasi-modal').modal('show');


            },
            error: function(x) 
            {                           
                alert("Error: "+ "\r\n\r\n" + x.responseText);
            }
        }); 
    }

    function changeStatusMutasi(status)
    {
        //alert(status); return false;

        var cost_center = $("#request-form #cost-center").val();
        var tujuan = $("#request-form #ba-tujuan").val();
        var area_code = "<?php echo $user_area_code ?>";
        if (cost_center == "") {
            notify({
                            type: 'warning',
                            message: 'COST CENTER HARUS DIISI.',
                        });
            return false;
        }
        
        <?php if( $user_role == 'AMS' ){ ?>
                if(update_jenis_asset() ==  false ){
                    return false
                }
        <?php } ?>
            
            if(area_code.includes(tujuan)){
                if(update_pic() ==  false ){
                    return false;
                }
            }
            
            var getnoreg = $("#getnoreg").val(); //alert(getnoreg); return false;
            var no_registrasi= getnoreg.replace(/\//g, '-');
            var specification = $("#specification-mutasi-approval").val();

            if( status == 'A' ){ status_desc = 'approve'; }else
            if( status == 'R' )
            { 
                status_desc = 'reject';
                note_reject = $("#specification-mutasi-approval").val();

                if( $.trim(note_reject) < 2 )
                {
                    notify({
                        type: 'warning',
                        message: " Note Reject is required (min 2 char)"
                    });
                    return false;
                } 

            }else{ status_desc = 'cancel'; }


            if(confirm('confirm '+status_desc+' data ?'))
            {
                //console.log(request_check_gi); return false;

                //e.preventDefault();
                var param = $("#request-form").serialize();
                var request_ka = JSON.stringify(request_kode_aset_data);
                var request_gi = JSON.stringify(request_check_gi);
                //alert(param); //return false;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: "{{ url('approval/update_status_mutasi') }}/"+status+"/"+no_registrasi,
                    method: "POST",
                    data: param+"&parNote="+specification+"&request_ka="+request_ka+"&request_gi="+request_gi,
                    beforeSend: function() {
                        jQuery('.loading-event').fadeIn();
                    },
                    success: function(result) 
                    {
                        //alert(result.status);
                        if (result.status) 
                        {
                            //SEND EMAIL 
                            send_email_create_po(result.new_noreg);

                            $("#approve-mutasi-modal").modal("hide");
                            $("#data-table").DataTable().ajax.reload();
                            $("#data-table-history").DataTable().ajax.reload();
                            // notify({
                            //     type: 'success',
                            //     message: result.message
                            // });
                        } 
                        else 
                        {
                            request_check_gi = [];
                            request_kode_aset_data = [];
                            //$(".md_year").val("");
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

    function delMutasi(noreg,kode_asset_ams)
    {
        //alert(noreg); return false;
        if(confirm('Confirm Delete ?'))
        {
            var no_registrasi= noreg.replace(/\//g, '-');
            var param = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/delete_asset_mutasi') }}",
                method: "POST",
                data: param+"&getnoreg="+no_registrasi+"&kode_asset_ams="+kode_asset_ams,
                beforeSend: function() {
                    $('.loading-event').fadeIn();
                },
                success: function(result) 
                {
                    //alert(result.status);
                    if (result.status) 
                    {
                        $("#approve-mutasi-modal").modal("hide");
                        //$("#data-table").DataTable().ajax.reload();
                        notify({
                            type: 'success',
                            message: result.message
                        });
                        //setTimeout(reload_page, 1000); 
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

    function sinkronisasi_mutasi()
    {
        $("#box-detail-item").hide();

        if(confirm('Confirm Synchronize SAP ?'))
        {
            var getnoreg = $("#getnoreg").val();
            var no_registrasi= getnoreg.replace(/\//g, '-');
            var kode_asset_ams = $("#kode-asset-ams").val();

            var param = '';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('approval/synchronize_sap_mutasi') }}",
                method: "POST",
                // data: param+"&noreg="+getnoreg,
                data: param+"&noreg="+getnoreg+"&kode_asset_ams="+kode_asset_ams,
                beforeSend: function() 
                {
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

                        $("#create-button-sync-sap-mutasi").hide();
                        $("#button-approve-mutasi").show();
                        $(".button-reject-mutasi").attr("disabled", true); 
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
                    $('.loading-event').fadeOut();
                }
                
            }); 

        }
    }

</script>
@stop