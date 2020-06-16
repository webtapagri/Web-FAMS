<?php

use App\Http\Controllers\MaterialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

Auth::routes();
Route::group(['middleware' => ['web']], function () {

});

/* DASHBOARD */
Route::match(['get', 'post'], 'grid-outstanding', [
    'as' => 'get.outstanding',
    'uses' => 'OutstandingController@dataGrid'
]);

Route::get('/asset/edit/', 'AssetController@show');
Route::resource('/help', 'HelpController');

/* PAGES */
Route::resource('/asset', 'AssetController');
Route::get('/asset/create/{type}', 'AssetController@create')->name('type');
Route::post('/asset/post', 'AssetController@store');
Route::get('/asset/edit/', 'AssetController@show');
Route::post('/asset/inactive', 'AssetController@inactive');
Route::get('grid-asset', ['as' => 'get.asset_grid', 'uses' => 'AssetController@dataGrid']);
Route::get('asset_pdf', ['as' => 'get.asset_pdf', 'uses' => 'AssetController@convertToPdf']);
Route::get('asset_report', ['as' => 'get.asset_report', 'uses' => 'AssetController@report']);

Route::resource('/request', 'RequestController');
Route::get('/request/create/{type}', 'RequestController@create')->name('type');
Route::post('/request/post', 'RequestController@store');
Route::get('/request/edit/', 'RequestController@show');
Route::post('/request/inactive', 'RequestController@inactive');
Route::post('/request/active', 'RequestController@active');
Route::get('grid-request', ['as' => 'get.request_grid', 'uses' => 'RequestController@dataGrid']);
Route::get('get-no_po', ['as' => 'get.no_po', 'uses' => 'RequestController@getPO']);
Route::get('requestpdf', ['as' => 'get.requestpdf', 'uses' => 'RequestController@pdfDoc']);
Route::get('get-businessarea', ['as' => 'get.businessarea', 'uses' => 'RequestController@businessarea']);
Route::get('get-qty-po', ['as' => 'get.qty_po', 'uses' => 'RequestController@qty_po']);

Route::resource('/approval', 'ApprovalController');
Route::get('/approval/create/{type}', 'ApprovalController@create')->name('type');
Route::post('/approval/post', 'ApprovalController@store');
Route::get('/approval/edit/', 'ApprovalController@show');
Route::post('/approval/inactive', 'ApprovalController@inactive');
Route::post('/approval/active', 'ApprovalController@active');
//Route::get('view-data-approval/{no_reg}', ['as' => 'get.approval_view', 'uses' => 'ApprovalController@dataView']);
Route::get('/approval/view/{no_reg}', 'ApprovalController@view')->name('no_reg');
Route::get('/approval/view_detail/{no_reg}/{id}', 'ApprovalController@get_asset_detail');
Route::post('/approval/delete_asset/{id}','ApprovalController@delete_asset');
Route::post('/approval/save_asset_sap/{id}','ApprovalController@save_asset_sap');
Route::post('/approval/update_costcenter/{kode}','ApprovalController@update_costcenter');
Route::post('/approval/save_item_detail/{id}','ApprovalController@save_item_detail');
Route::post('/approval/update_status/{status}/{no_reg}','ApprovalController@update_status');
Route::get('/approval/log_history/{no_reg}', 'ApprovalController@log_history')->name('no_reg');
Route::post('/approval/synchronize_sap', 'ApprovalController@synchronize_sap');
Route::post('/approval/synchronize_sap_mutasi', 'ApprovalController@synchronize_sap_mutasi');
Route::post('/approval/synchronize_amp', 'ApprovalController@synchronize_amp');
Route::post('/approval/update_ka_con_temp', 'ApprovalController@update_ka_con_temp');
Route::post('/approval/update_kode_vendor_aset_lain','ApprovalController@update_kode_vendor_aset_lain');
Route::post('/approval/update_kode_asset_controller','ApprovalController@update_kode_asset_controller');
Route::post('/approval/save_gi_number_year','ApprovalController@save_gi_number_year');
//Route::get('grid-approval', ['as' => 'get.approval_grid', 'uses' => 'ApprovalController@dataGrid']);
//Route::get('grid-approval-history', ['as' => 'get.approval_grid_history', 'uses' => 'ApprovalController@dataGridHistory']);
Route::match(['get', 'post'], 'grid-approval', [
    'as' => 'get.approval_grid',
    'uses' => 'ApprovalController@dataGrid'
]);
Route::match(['get', 'post'], 'grid-approval-history', [
    'as' => 'get.approval_grid_history',
    'uses' => 'ApprovalController@dataGridHistory'
]);
Route::get('/approval/berkas-amp/{no_reg}', 'ApprovalController@berkas_amp')->name('no_reg');
Route::get('/printio/{noreg}/{asset_po_id}/{jenis_kendaraan}/{no_reg_item}', 'ApprovalController@print_io');
Route::post('/approval/update_status_disposal/{status}/{no_reg}','ApprovalController@update_status_disposal');
Route::get('/approval/view_disposal/{no_reg}', 'ApprovalController@view_disposal')->name('no_reg');
Route::post('/approval/delete_asset_disposal', 'ApprovalController@delete_asset_disposal');
Route::get('/approval/view_mutasi/{no_reg}', 'ApprovalController@view_mutasi')->name('no_reg');
Route::post('/approval/update_status_mutasi/{status}/{no_reg}','ApprovalController@update_status_mutasi');
Route::post('/approval/delete_asset_mutasi', 'ApprovalController@delete_asset_mutasi');

Route::get('get-select_jenis_kendaraan', ['as' => 'get.select_jenis_kendaraan', 'uses' => 'Select2Controller@select_jenis_kendaraan']);

Route::resource('/mutasi', 'MutasiController');
Route::get('/mutasi/create/{type}', 'MutasiController@create')->name('type');
Route::post('/mutasi/post', 'MutasiController@store');
Route::post('/mutasi/post_sewa', 'MutasiController@store_sewa');
Route::get('/mutasi/edit/', 'MutasiController@show');
Route::post('/mutasi/inactive', 'MutasiController@inactive');
Route::post('/mutasi/active', 'MutasiController@active');
Route::get('grid-mutasi', ['as' => 'get.mutasi_grid', 'uses' => 'MutasiController@dataGrid']);
Route::match(['get', 'post'], 'grid-asset-mutasi', [
    'as' => 'get.grid_asset_mutasi',
    'uses' => 'MutasiController@dataGridAssetMutasi'
]);
Route::get('/mutasi/list-upload/{kode_asset_ams}/{jenis_pengajuan}', 'MutasiController@list_file_category')->name('kode_asset_ams');
Route::post('/mutasi/upload_berkas_amp', 'MutasiController@upload_berkas_amp');
Route::post('/mutasi/add_temp', 'MutasiController@add_temp');
Route::post('/mutasi/delete_data_temp','MutasiController@delete_data_temp');
Route::post('/mutasi/delete_data_temp_sewa','MutasiController@delete_data_temp_sewa');
Route::get('/mutasi/view-berkas-detail/{kode_asset_ams}/{file_category}', 'MutasiController@berkas_detail')->name('kode_asset_ams');
Route::post('/mutasi/delete_berkas_temp','MutasiController@delete_berkas_temp');
Route::get('/mutasi/view-berkas-notes/{kode_asset_ams}', 'MutasiController@berkas_notes')->name('kode_asset_ams');
Route::get('/mutasi/berkas/sewa/{kode_asset_ams}', 'MutasiController@berkas_notes_sewa')->name('kode_asset_ams');
Route::post('/mutasi/delete_all_berkas_temp','MutasiController@delete_all_berkas_temp');

/* USER SETTINGS */
//Route::get('/', 'HomeController@index')->name('home');
//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'ApprovalController@index')->name('home');
Route::get('/home', 'ApprovalController@index')->name('home');
Route::get('/profile', 'ProfileController@index');
Route::post('/ldaplogin', 'LDAPController@login');
Route::post('/ldaplogout', 'LDAPController@logout');

Route::resource('/users', 'UsersController');
Route::post('/users/post', 'UsersController@store');
Route::get('/users/edit/', 'UsersController@show');
Route::post('/users/inactive', 'UsersController@inactive');
Route::post('/users/active', 'UsersController@active');
Route::match(['get', 'post'], 'grid-users', [
    'as' => 'get.users',
    'uses' => 'UsersController@dataGrid'
]);

Route::resource('/menu', 'MenuController');
Route::post('/menu/post', 'MenuController@store');
Route::get('/menu/edit/', 'MenuController@show');
Route::post('/menu/inactive', 'MenuController@inactive');
Route::post('/menu/active', 'MenuController@active');
Route::match(['get', 'post'], 'grid-menu', [
    'as' => 'get.menu_grid',
    'uses' => 'MenuController@dataGrid'
]);

Route::resource('/modules', 'ModuleController');
Route::post('/modules/post', 'ModuleController@store');
Route::get('/modules/edit/', 'ModuleController@show');
Route::post('/modules/inactive', 'ModuleController@inactive');
Route::post('/modules/active', 'ModuleController@active');
Route::match(['get', 'post'], 'grid-modules', [
    'as' => 'get.grid_modules',
    'uses' => 'ModuleController@dataGrid'
]);

Route::resource('/roles', 'RolesController');
Route::post('/roles/post', 'RolesController@store');
Route::get('/roles/edit/', 'RolesController@show');
Route::post('/roles/inactive', 'RolesController@inactive');
Route::post('/roles/active', 'RolesController@active');
Route::match(['get', 'post'], 'grid-role_grid', [
    'as' => 'get.role_grid',
    'uses' => 'RolesController@dataGrid'
]);

Route::resource('/accessright', 'AccessRightController');
Route::post('/accessright/post', 'AccessRightController@store');
Route::get('/accessright/edit/', 'AccessRightController@show');
Route::post('/accessright/inactive', 'AccessRightController@inactive');
Route::post('/accessright/active', 'AccessRightController@active');
Route::match(['get', 'post'], 'grid-accessright', [
    'as' => 'get.accessright_grid',
    'uses' => 'AccessRightController@dataGrid'
]);

/* DOCS */
Route::get('SapDownloadExcel', 'SAPController@downloadExcel');

/* JSON DATA SOURCE */
Route::get( 'get-outstandingdetail', ['as' => 'get.outstandingdetail', 'uses' => 'OutstandingController@requestDetail']);
Route::get( 'get-outstandingdetailfiles', ['as' => 'get.outstandingdetailfiles', 'uses' => 'OutstandingController@requestDetailFiles']);
Route::get( 'get-outstandingdetailitem', ['as' => 'get.outstandingdetailitem', 'uses' => 'OutstandingController@requestDetailItem']);
Route::get( 'get-outstandingdetailitempo', ['as' => 'get.outstandingdetailitempo', 'uses' => 'OutstandingController@requestDetailItemPO']);
Route::get( 'get-outstandingdetailitemfile', ['as' => 'get.outstandingdetailitemfile', 'uses' => 'OutstandingController@requestDetailItemFile']);


/* SELECT 2 */
Route::get('get-select_module', ['as' => 'get.select_module', 'uses' => 'ModuleController@select2']);
Route::get('get-select_menu', ['as' => 'get.select_menu', 'uses' => 'MenuController@select2']);
Route::get('get-select_role', ['as' => 'get.select_role', 'uses' => 'RolesController@select2']);
Route::get('get-generaldataplant', ['as' => 'get.generaldataplant', 'uses' => 'Select2Controller@generaldataplant']);
Route::get('get-assetgroup', ['as' => 'get.assetgroup', 'uses' => 'Select2Controller@assetgroup']);
Route::get('get-assetgroupcondition', ['as' => 'get.assetgroupcondition', 'uses' => 'Select2Controller@assetgroupcondition']);
Route::get('get-assetsubgroup', ['as' => 'get.assetsubgroup', 'uses' => 'Select2Controller@assetsubgroup']);
Route::get( 'get-jenisasset', ['as' => 'get.jenisasset', 'uses' => 'Select2Controller@jenisasset']);
Route::get('get-select_workflow_code', ['as' => 'get.select_workflow_code', 'uses' => 'WorkflowController@workflowcode']);
Route::get('get-select_workflow_detail_code', ['as' => 'get.select_workflow_detail_code', 'uses' => 'WorkflowController@workflowcodedetail']);
Route::get('get-select_workflow_detail_role', ['as' => 'get.select_workflow_detail_role', 'uses' => 'WorkflowController@workflowcoderole']);
Route::get('get-select_workflow_detail_code', ['as' => 'get.select_workflow_detail_code', 'uses' => 'WorkflowController@workflowcodedetail']);
Route::get('get-select_jenis_asset_code', ['as' => 'get.select_jenis_asset_code', 'uses' => 'AssetClassController@select_jenis_asset_code']);
Route::get('get-select_jenis_asset_code_text_only', ['as' => 'get.select_jenis_asset_code_text_only', 'uses' => 'AssetClassController@select_jenis_asset_code_text_only']);
Route::get('get-select_group_code', ['as' => 'get.select_group_code', 'uses' => 'AssetClassController@select_group_code']);
Route::get('get-select_subgroup_code', ['as' => 'get.select_subgroup_code', 'uses' => 'AssetClassController@select_subgroup_code']);
Route::get('get-select_subgroup_code_condition', ['as' => 'get.select_subgroup_code_condition', 'uses' => 'AssetClassController@select_subgroup_code_condition']);
Route::get('get-select_asset_controller', ['as' => 'get.select_asset_controller', 'uses' => 'AssetClassController@select_asset_controller']);
Route::get('get-generaldata-assetcontroller', ['as' => 'get.generaldata_assetcontroller', 'uses' => 'Select2Controller@generaldata_assetcontroller']);
Route::get('get-select_role_idname', ['as' => 'get.select_role_idname', 'uses' => 'RolesController@select_role']);
Route::get('get-select_user', ['as' => 'get.select_user', 'uses' => 'UsersController@select2']);
Route::get('get-select_uom', ['as' => 'get.select_uom', 'uses' => 'Select2Controller@select_uom']);
Route::get('get-tujuan_business_area', ['as' => 'get.tujuan_business_area', 'uses' => 'Select2Controller@tujuan_business_area']);

/* WORKFLOW SETTING */
Route::resource('/setting/workflow', 'WorkflowController');
Route::post('/workflow/post', 'WorkflowController@store');
Route::post('/workflow/post-detail', 'WorkflowController@store_detail');
Route::post('/workflow/post-detail-job', 'WorkflowController@store_detail_job');
Route::get('/workflow/edit/', 'WorkflowController@show');
Route::get('/workflow/edit-detail/', 'WorkflowController@show_detail');
Route::get('/workflow/edit-detail-job/', 'WorkflowController@show_detail_job');
Route::match(['get', 'post'], 'grid-workflow', [
    'as' => 'get.grid_workflow',
    'uses' => 'WorkflowController@dataGrid'
]);
/*
Route::match(['get', 'post'], 'grid-workflow-detail', [
    'as' => 'get.grid_workflow_detail',
    'uses' => 'WorkflowController@dataGrid'
]);
*/
Route::post('grid-workflow-detail/{id}', 'WorkflowController@dataGridDetail')->name('grid-workflow-detail/{id}');
Route::post('grid-workflow-detail-job/{id}', 'WorkflowController@dataGridDetailJob')->name('grid-workflow-detail-job/{id}');

/* MASTER GENERAL DATA SETTING */
Route::resource('/setting/general-data', 'GeneralDataController');
Route::match(['get', 'post'], 'grid-general-data', [
    'as' => 'get.grid_general_data',
    'uses' => 'GeneralDataController@dataGrid'
]);
Route::post('/general-data/post', 'GeneralDataController@store');
Route::get('/general-data/edit/', 'GeneralDataController@show');

/* ASSET CLASS - SETTING */
Route::resource('/setting/asset-class', 'AssetClassController');
Route::match(['get', 'post'], 'grid-asset-class', [
    'as' => 'get.grid_asset_class',
    'uses' => 'AssetClassController@dataGrid'
]);
Route::post('/asset-class/post', 'AssetClassController@store');
Route::post('/asset-class/post-group-asset', 'AssetClassController@store_group_asset');
Route::post('/asset-class/post-subgroup-asset', 'AssetClassController@store_subgroup_asset');
Route::post('/asset-class/post-asset-map', 'AssetClassController@store_asset_map');
Route::get('/asset-class/edit/', 'AssetClassController@show');
Route::get('/asset-class/edit-group-asset/', 'AssetClassController@show_group_asset');
Route::get('/asset-class/edit-subgroup-asset/', 'AssetClassController@show_subgroup_asset');
Route::get('/asset-class/edit-asset-map/', 'AssetClassController@show_asset_map');
Route::post('grid-ac-group-asset/{id}', 'AssetClassController@dataGridGroupAsset')->name('grid-ac-group-asset/{id}');
Route::post('grid-ac-subgroup-asset/{id}/{id_jenis_asset_code}', 'AssetClassController@dataGridSubGroupAsset')->name('grid-ac-subgroup-asset/{id}/{id_jenis_asset_code}');
Route::post('grid-ac-asset-map/{id}', 'AssetClassController@dataGridAssetMap')->name('grid-ac-asset-map/{id}');

/* SETTING - ROLE MAP */
Route::resource('/setting/role-map', 'RoleMapController');
Route::match(['get', 'post'], 'grid-role-map', [
    'as' => 'get.grid_role_map',
    'uses' => 'RoleMapController@dataGrid'
]);
Route::get('/role-map/edit/', 'RoleMapController@show');
Route::post('/role-map/post', 'RoleMapController@store');

/* SEND EMAIL */
Route::post('/request/send_email_create_po','FamsEmailController@index');
// Route::post('/request/email_create_po','FamsEmailController@index');

/* MASTER ASSET */
Route::resource('/master-asset', 'MasterAssetController');
Route::match(['get', 'post'], 'grid-master-asset', [
    'as' => 'get.grid_master_asset',
    'uses' => 'MasterAssetController@dataGrid'
]);
//Route::get('/master-asset/edit/', 'MasterAssetController@show');
Route::get('/master-asset/show-data/{id}', 'MasterAssetController@show_edit');
Route::get('/master-asset/show_qrcode/{ams}', 'MasterAssetController@show_qrcode')->name('ams');
Route::get('/test_qrcode', 'MasterAssetController@test_qrcode');
Route::get('/master-asset/print-qrcode/{noreg}', 'MasterAssetController@print_qrcode')->name('noreg');
Route::post('/master-asset/download', 'MasterAssetController@download')->name('master_asset.download');
Route::get('bulk-download', 'MasterAssetController@view_download_masterasset_qrcode')->name('view_download_masterasset_qrcode');
Route::post('download_masterasset_qrcode', 'MasterAssetController@download_masterasset_qrcode')->name('download_masterasset_qrcode');

/* REQUEST ASET LAINNYA */
Route::resource('/request', 'RequestAsetLainController');
Route::get('/create/aset-lain', 'RequestAsetLainController@create');
Route::post('/aset-lain/post', 'RequestAsetLainController@store');

/* RESUME PROCESS */
Route::get('/resume/document', 'ResumeController@index');
Route::post('/resume/document-submit','ResumeController@document_submit');
Route::get('/resume/user', 'ResumeController@user');
Route::get('get-select_role_resume', ['as' => 'get.select_role_resume', 'uses' => 'Select2Controller@select_role']);
Route::get('get-select_user_resume', ['as' => 'get.select_user_resume', 'uses' => 'Select2Controller@select_user']);
Route::post('/resume/user-submit','ResumeController@user_submit');

/* ALL REPORT */
Route::get('/report/list-asset', 'ReportController@list_asset');
Route::post('/report/list-asset/submit', 'ReportController@list_asset_submit');
Route::post('/report/list-asset/download', 'ReportController@list_asset_download');

/* DISPOSAL */
Route::resource('/disposal-penjualan', 'DisposalController');
Route::get('/disposal-penjualan/add/{id}/{pengajuan}', 'DisposalController@add');
Route::get('/disposal-penjualan/delete/{kode_asset_ams}', 'DisposalController@remove');

Route::get('/disposal-hilang', 'DisposalController@index_hilang');
Route::get('/disposal-hilang/add_hilang/{id}/{pengajuan}', 'DisposalController@add_hilang');
Route::get('/disposal-hilang/delete_hilang/{kode_asset_ams}', 'DisposalController@remove_hilang');

Route::get('/disposal-rusak', 'DisposalController@index_rusak');
Route::get('/disposal-rusak/add_rusak/{id}/{pengajuan}', 'DisposalController@add_rusak');
Route::get('/disposal-rusak/delete_rusak/{kode_asset_ams}', 'DisposalController@remove_rusak');

Route::post('/proses_disposal/{tipe}','DisposalController@proses');
Route::post('/disposal/edit_harga', 'DisposalController@update_harga_perolehan');
Route::post('/disposal/upload_berkas_hilang', 'DisposalController@upload_berkas_hilang');
Route::post('/disposal/upload_berkas_rusak', 'DisposalController@upload_berkas_rusak');
Route::post('/disposal/upload_berkas', 'DisposalController@upload_berkas');
Route::get('/approval/berkas-disposal/{no_reg}', 'ApprovalController@berkas_disposal')->name('no_reg');
Route::get('/approval/berkas-mutasi/{no_reg}', 'ApprovalController@berkas_mutasi')->name('no_reg');
Route::get('/disposal/view-berkas/{no_reg}', 'DisposalController@berkas_disposal')->name('no_reg');
Route::get('/disposal/view-berkas-serah-terima/{kode_asset_ams}', 'DisposalController@berkas_serah_terima')->name('kode_asset_ams');
Route::get('/disposal/view-berkas-detail/{kode_asset_ams}/{file_category}', 'DisposalController@berkas_disposal_detail')->name('kode_asset_ams');
Route::get('/disposal/list-kategori-upload/{kode_asset_ams}/{jenis_pengajuan}', 'DisposalController@list_file_category')->name('kode_asset_ams');
Route::get('/disposal/view-berkas-notes/{kode_asset_ams}', 'DisposalController@berkas_notes')->name('kode_asset_ams');
Route::get('/disposal/view-berkas-by-type/{kode_asset_ams}/{file_category}', 'DisposalController@file_download')->name('kode_asset_ams');
Route::get('/disposal/delete_berkas_temp','DisposalController@delete_berkas_temp');
/* END DISPOSAL */


// Route::get('storage/{filename}', function ($filename)
// {
//     return Image::make(storage_path('public/' . $filename))->response();
// });



#image
Route::get( 'storage/{filename}', 'StorageController@image' );
