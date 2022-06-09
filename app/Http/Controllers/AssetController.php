<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\TrUser;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use Spipu\Html2Pdf\Html2Pdf;

class AssetController extends Controller
{
    public function index()
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');

        /* if (AccessRight::granted() == false)
            return response(view('errors.403'), 403); */

        //$access = AccessRight::access();
        $data['page_title'] = "Asset List";
        return view('assets.index')->with(compact('data'));
    }

    public function create(Request $request) {
        $type = ($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        return view('assets.add')->with(compact('type'));
    }

    public function dataGrid() {
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_user"
        ));
        $data = $service;
        return response()->json(array('data' => $data->data));
    }

    public function convertToPdf() {

        $html2pdf = new Html2Pdf('P', 'A4', 'en');
        $html2pdf->writeHTML(view('assets.pdf', [
            'name' => 'dadang kurniawan',
        ]));

        $pdf = $html2pdf->output("","S");
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Length', strlen($pdf))
            ->header('Content-Disposition', 'inline; filename="example.pdf"');
    }
    
    public function report() {

        $html2pdf = new Html2Pdf('L', 'A4', 'en');
        $html2pdf->writeHTML(view('assets.report', [
            'name' => 'dadang kurniawan',
        ]));

        $html2pdf->output("asset_report.pdf", "D");
    }

    public function show()
    {
        $param = $_REQUEST;
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_user/" . $param["id"]
        ));
        $data = $service;
        return response()->json(array('data' => $data->data));
    }
}
