<?php
$l = '';
$url_img = url("img/logoTAP.png");//echo $url_img; die();
//$l .= '<div class="xcontainer" xstyle="border:1px solid red">';
if($data)
{
    $l .= "<table style='border-collapse:collapse;text-align:left;width:100%;height:auto'>";
    $l .= "<tr><td align='right'><img src='".$url_img."' style='width:60px'></td></tr>";
    $l .= "<tr align='center'><td align='center'><h4>FORM PARAMETER INTERNAL ORDER</h4></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'>Tanggal ".date('d/m/Y')."</td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr align='center' style='background-color:#d3e869'><td align='left'><input type='checkbox'> Add &nbsp;<input type='checkbox'> Add &nbsp;<input type='checkbox'> Non Aktif &nbsp;</td></tr>";
    $l .= "<tr align='lef'><td>";
    $l .= "<table width='100%' style='margin-top:10px'>";
    $l .= "<tr class='alt' style='width:175px'><td>KODE PERUSAHAAN</td>
                <td>:</td>
                <td>".substr($data[0]['lokasi_ba_code'],0,2)."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr><td style='width:175px'>ESTATE CODE</td>
                <td>:</td>
                <td>".substr($data[0]['lokasi_ba_code'],2,2)."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr style='width:175px' class='alt'><td>JENIS KENDARAAN</td>
                <td>:</td>
                <td>".$jenis_kendaraan."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr><td style='width:175px'>KODE INTERNAL ORDER</td>
                <td>:</td>
                <td></td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr class='alt' valign='top'><td valign='top' style='width:175px'>DESKRIPSI KENDARAAN</td>
                <td>:</td>
                <td>".$data[0]['nama_asset']."<br/>".$data[0]['merk']."<br>".$data[0]['jenis_asset']."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr><td style='width:175px'>NO POLISI</td>
                <td>:</td>
                <td>".$data[0]['no_polisi']."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr class='alt' style='width:175px'><td>NO CHASIS</td>
                <td>:</td>
                <td>".$data[0]['no_rangka_or_no_seri']."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr><td style='width:175px'>NO MESIN</td>
                <td>:</td>
                <td>".$data[0]['no_mesin_or_imei']."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
     $l .= "<tr class='alt' style='width:175px'><td>TAHUN PEROLEHAN</td>
                <td>:</td>
                <td>".$data[0]['tahun']."</td>
            </tr>";
     $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr class='alt' style='width:175px'><td>KODE ASSET FAMS</td>
                <td>:</td>
                <td>".$data[0]['kode_asset_ams']."</td>
            </tr>";
     $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
     $l .= "<tr class='alt' style='width:175px'><td>KODE ASSET SAP</td>
                <td>:</td>
                <td>".$data[0]['kode_asset_sap']."</td>
            </tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px' colspan='3'></td></tr>";
    $l .= "</table>";
    $l .= "</td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
     $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
      $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
       $l .= "<tr align='center'><td style='margin-left:100px'></td></tr>";
    $l .= "<tr><td><table border=0 style='margin-top:25px;xfont-weight:bold'>";
    $l .= "<tr align='center'><td style='width:175px'>&nbsp;</td>
                <td style='width:175px'>&nbsp;</td>
                <td style='width:175px'>&nbsp;&nbsp;&nbsp;</td>
                <td style='width:175px'>&nbsp;</td></tr>";
    $l .= "<tr align='center'><td style='width:175px;height:75px'></td>
                <td style='width:175px;height:75px'></td>
                <td style='width:175px;height:75px'></td>
                <td style='width:175px;height:75px'></td></tr>";
    $l .= "<tr align='center'><td style='width:175px'>&nbsp;</td>
                <td style='width:175px'>&nbsp;</td>
                <td style='width:175px'>&nbsp;</td>
                <td style='width:175px'>&nbsp;</td></tr>";
    $l .= "</table></td></tr>";
    $l .= "</table>";    
}

//$l .= '</div>';

echo $l;

    //echo '1.'.$name.'<br/>'.$no_document.'<br/>'.print_r($data[0]['nama_asset_1']); 
    /*
    3

Array
(
    [0] => Array
        (
            [id] => 151
            [no_po] => 123456
            [asset_po_id] => 115
            [tgl_po] => 2019-08-14 22:43:31
            [kondisi_asset] => Baik
            [jenis_asset] => E4010-EST - KENDARAAN
            [group] => G4-EST - KENDARAAN
            [sub_group] => SG26-EST - SEPEDA MOTOR
            [nama_asset] => MOTOR VARIO
            [merk] => 
            [spesifikasi_or_warna] => 
            [no_rangka_or_no_seri] => MH1KC0114KK013360
            [no_mesin_or_imei] => RR81527U593538Y
            [lokasi] => 2111-HO JAKARTA SAWIT
            [tahun] => 2019
            [nama_penanggung_jawab_asset] => Irvan
            [jabatan_penanggung_jawab_asset] => Aset
            [info] => 
            [file] => Array
                (
                )

            [nama_asset_1] => MOTOR VARIO
            [nama_asset_2] => 123456
            [nama_asset_3] => MOTOR VARIO
            [quantity_asset_sap] => 1.00
            [uom_asset_sap] => UN
            [capitalized_on] => 
            [deactivation_on] => 
            [cost_center] => 2100118074
            [book_deprec_01] => 4
            [fiscal_deprec_15] => 4
            [group_deprec_30] => 4
            [no_reg_item] => 1
            [vendor] => 12345-six
            [business_area] => 2111
            [kode_asset_sap] => 
            [kode_asset_controller] => 
            [kode_asset_ams] => 
            [po_type] => 2
            [gi_number] => 
            [gi_year] => 
            [total_asset] => 1
            [nama_material] => MOTOR VARIO
        )

)

    */
 ?>

<?php /*
 <table style="border-collapse:collapse;text-align:left;width:100%;height:auto">
     <tr>
         <th style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;background-color:#991821;background-image:none;background-repeat:repeat;background-position:center top;background-attachment:scroll;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');color:#FFFFFF;font-size:15px;font-weight:bold;border-left-width:1px;border-left-style:solid;border-left-color:#B01C26;">Kode Asset Fams</th>
         <th style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;background-color:#991821;background-image:none;background-repeat:repeat;background-position:center top;background-attachment:scroll;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');color:#FFFFFF;font-size:15px;font-weight:bold;border-left-width:1px;border-left-style:solid;border-left-color:#B01C26;">header</th>
         <th style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;background-color:#991821;background-image:none;background-repeat:repeat;background-position:center top;background-attachment:scroll;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');color:#FFFFFF;font-size:15px;font-weight:bold;border-left-width:1px;border-left-style:solid;border-left-color:#B01C26;">header</th>
         <th style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;background-color:#991821;background-image:none;background-repeat:repeat;background-position:center top;background-attachment:scroll;filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');color:#FFFFFF;font-size:15px;font-weight:bold;border-left-width:1px;border-left-style:solid;border-left-color:#B01C26;">header</th>
     </tr>
     <tr>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
     </tr>
     <tr class="alt">
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
     </tr>
     <tr>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
     </tr>
     <tr class="alt">
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;background-color:#F7CDCD;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;color:#80141C;">data</td>
     </tr>
     <tr>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
         <td style="padding-top:3px;padding-bottom:3px;padding-right:10px;padding-left:10px;color:#80141C;border-left-width:1px;border-left-style:solid;border-left-color:#F7CDCD;font-size:12px;font-weight:normal;">data</td>
     </tr>
 </table>

 */ ?>