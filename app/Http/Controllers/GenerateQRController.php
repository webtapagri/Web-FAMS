<?php

namespace App\Http\Controllers;

 
use Session;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use API;
use AccessRight;
use App\Workflow;
use App\TR_QRDATA;
use App\Imports\DataImport;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\MasterAssetExport;
use Debugbar;


class GenerateQRController extends Controller
{

    public function index()
    {
        return view('generatedata.bulk-download');
    }

	
	function download_qrcode_result(Request $request){
        // \File::deleteDirectory( storage_path("app/public/tmp_download") );
        // echo "hai";
        // dd($request->hasFile('file_qr'));
        $request->validate([
            'file_qr' => 'required|mimes:xlsx,xls',
        ]);

        if($request->hasFile('file_qr')){
            // $path = $request->file('file_qr')->getRealPath();
            $path1 = $request->file('file_qr')->store('temp'); 
            $path = storage_path('app').'/'.$path1;  

            // $data = Excel::import($path)->get();
            $data = Excel::import(new DataImport, $path);
            $sql = " SELECT * from TR_QRDATA";

            $getLup = DB::SELECT($sql);
            if(!empty($getLup)){
                // $data['qrdata'] = $getLup;
                // dd($getLup); 
                if(count($getLup)!= 0) { 
                    foreach($getLup as $k=> $dt){
                        // print_r($dt);
                        $trg = base64_encode($dt->QR_CODE);
                        $data = $dt->QR_CODE;           
                        if( $this->gen_png_img($data) ){
                        
                            $qrcode = $trg;
                            // echo \QrCode::margin(0)->size(250)->generate(''.$qrcode.'').'<br/>'; 
                            $os = PHP_OS; 
                            if( $os != "WINNT" ){
                                $file_qrcode = '/app/qrcode_tempe.png';
                            }else{
                                $file_qrcode = '\app\qrcode_tempe.png';
                            }
                            $file_data = 'data:image/png;base64, '.base64_encode(\QrCode::format('png')->merge(''.$file_qrcode.'', 1)->margin(5)->size(300)->generate(''.$qrcode.'')); 
                            $file_name = 'tmp_download/'.$dt->QR_CODE.'.png';
                            @list($type, $file_data) = explode(';', $file_data);                                                                                                                            
                            @list(, $file_data) = explode(',', $file_data); 
                            if($file_data!=""){ 
                                \Storage::disk('public')->put($file_name,base64_decode($file_data)); 
                            }		
                        }
                        
                        
                    }
                    $this->gen_zip();
                    
                    $headers = array(
                        'Content-Type' => 'application/octet-stream',
                    );
                    $filetopath = storage_path("app/public/tmp_download/tmp_download.zip");
                    
                    if(file_exists($filetopath)){
                        return response()->download($filetopath,'GENERATEDATA_QR_'.date('YmdHis').'.zip',$headers);
                        //\File::deleteDirectory( storage_path("app/public/tmp_download") );
                    }
                    
                }
                else{
                    return Redirect::back()->withErrors('');
                }
            }
        }

        
    }
    
    
	
	function gen_zip() {
		$files = glob(storage_path("app/public/tmp_download/*.png"));

		$archiveFile = storage_path("app/public/tmp_download/tmp_download.zip");
		$archive = new \ZipArchive();
		
		if ($archive->open($archiveFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
			foreach ($files as $file) {
				$img = \Intervention\Image\Facades\Image::make($file)->crop(310, 420 )->encode('png');
				$hash = basename($file);
				$img->save(storage_path('app/public/tmp_download/'.$hash));
				if ($archive->addFile($file, basename($file))) {
					continue;
				} else {
					throw new Exception("file `{$file}` could not be added to the zip file: " . $archive->getStatusString());
				}
			}

            DB::DELETE(" DELETE FROM TR_QRDATA "); 

            DB::commit();

			if ($archive->close()) {
				return response()->download($archiveFile, basename($archiveFile))->deleteFileAfterSend(true);
			} else {
				throw new Exception("could not close zip file: " . $archive->getStatusString());
			}
		} else {
		  throw new Exception("zip file could not be created: " . $archive->getStatusString());
		}
	}
	
	function gen_png_img($data){
		$string = $data; 

		$width  = 350;
		$height = 450;
        $font = 8;
        // dd($fontfam);
		$im = @imagecreate ($width, $height);
		$text_color = imagecolorallocate($im, 0, 0, 0); //black text
		$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $transparent);
		imagesavealpha($im, true);
	  
        $width1 = imagefontwidth($font) * strlen($string); 
        imagestring ($im, $font,($width/2)-($width1/2), 40, $string, $text_color);
	  
        ob_start();
        imagealphablending($im, false);
        imagesavealpha($im, true);
		imagepng($im);
		$imstr = base64_encode(ob_get_clean());
		imagedestroy($im);

		// Save Image in folder from string base64
		$img = 'data:image/png;base64,'.$imstr;
		$image_parts = explode(";base64,", $img);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		$image_base64 = base64_decode($image_parts[1]);
		$folderPath = app_path();
		$file = $folderPath . '/qrcode_tempe.png';
		// MOve to folder
		if(file_put_contents($file, $image_base64)){
			return true;
		}else{
			return false;
		}
    }

    
}
