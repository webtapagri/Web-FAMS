<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Config;
use URL;

class StorageController extends Controller {

	#   		 								  				  ▁ ▂ ▄ ▅ ▆ ▇ █ CONSTRUCTOR
	# -------------------------------------------------------------------------------------
	public function __construct() {
	
	}
	
	#   		 								  				        ▁ ▂ ▄ ▅ ▆ ▇ █ INDEX
	# -------------------------------------------------------------------------------------
	public function index() {
		
	}

	#   		 								  				  ▁ ▂ ▄ ▅ ▆ ▇ █ CREATE USER
	# -------------------------------------------------------------------------------------
	public function image($filename) {
	
		return Image::make(storage_path('public/' . $filename))->response();
	}

}