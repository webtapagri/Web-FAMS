<link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<style>
html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Source Sans Pro', sans-serif;
        font-weight: 200;
        height: 100vh;
        margin: 5;
    }

</style>

<div class="container">
    <div xclass="xtable-scroll ex1" xstyle="background-color: #FFF;overflow: auto;">

    <center><h1><u>REPORT ASSET</u></h1><h3>Per tanggal : <?php echo date('d/m/Y'); ?></h3></center>

    <?php 
        $l = "";
        $no = 1;

        if(!empty($data))
        {
            $l .= "<table border=1 cellspacing=0 cellpadding=5 class='table tabel-responsive table-bordered'>";
            $l .= "<tr>
                <th rowspan='2'>NO</th>
                <th rowspan='2'>BA PT PEMILIK</th>
                <th rowspan='2'>KODE ASET FAMS</th>
                <th rowspan='2'>NAMA ASET</th>
                <th rowspan='2'>QTY</th>
                <th rowspan='2'>UOM</th>
                <th rowspan='2'>MERK</th>
                <th rowspan='2'>SPESIFIKASI / TYPE / MODEL</th>
                <th rowspan='2'>NO SERI / RANGKA</th>
                <th rowspan='2'>NO MESIN / IMEI</th>
                <th rowspan='2'>NO POLISI</th>
                <th rowspan='2'>TAHUN PEROLEHAN</th>
                <th rowspan='2'>LOKASI ASET</th>
                <th colspan='2'>PENANGGUNG JAWAB</th>
                <th colspan='3'>KONDISI</th>
                <th rowspan='2'>FOTO ASSET</th>
                <th rowspan='2'>FOTO NO SERI / RANGKA</th>
                <th rowspan='2'>FOTO NO MESIN / IMEI</th>
                <th rowspan='2'>HARGA PEROLEHAN</th>
                <th rowspan='2'>NILAI BUKU SAAT INI</th>
                <th colspan='2'>STATUS</th>
            </tr>
            <tr>
                <th>NAMA</th>
                <th>JABATAN</th>
                <th>B</th>
                <th>BP</th>
                <th>RTLP</th>
                <th>DISPOSAL</th>
                <th>MUTASI</th>
            </tr>";
            // $l .= "<tr>
            //     <th rowspan='2'>NO</th>
            //     <th rowspan='2'>KODE ASET FAMS</th>
            //     <th rowspan='2'>KODE SAP</th>
            //     <th rowspan='2'>KODE ASET CONTROLLER</th>
            //     <th rowspan='2'>BA PT PEMILIK</th>
            //     <th rowspan='2'>NAMA PT PEMILIK</th>
            //     <th rowspan='2'>BA PT LOKASI</th>
            //     <th rowspan='2'>NAMA PT LOKASI</th>
            //     <th rowspan='2'>NAMA ASET</th>
            //     <th rowspan='2'>MERK</th>
            //     <th rowspan='2'>SPESIFIKASI / TYPE / MODEL</th>
            //     <th rowspan='2'>NO SERI / RANGKA</th>
            //     <th rowspan='2'>NO MESIN / IMEI</th>
            //     <th rowspan='2'>NO POLISI</th>
            //     <th rowspan='2'>LOKASI ASET</th>
            //     <th colspan='2'>PENANGGUNG JAWAB</th>
            //     <th colspan='3'>KONDISI</th>
            //     <th rowspan='2'>NO PO / SPO / KONTRAK</th>
            //     <th rowspan='2'>NAMA VENDOR</th>
            //     <th rowspan='2'>INFORMASI TAMBAHAN</th>
            //     <th rowspan='2'>FOTO ASSET</th>
            //     <th rowspan='2'>FOTO NO SERI / RANGKA</th>
            //     <th rowspan='2'>FOTO NO MESIN / IMEI</th>
            //     <th rowspan='2'>ASSET CLASS</th>
            //     <th rowspan='2'>TAHUN PEROLEHAN</th>
            //     <th rowspan='2'>HARGA PEROLEHAN</th>
            //     <th rowspan='2'>NILAI BUKU SAAT INI</th>
            //     <th rowspan='2'>USE LIFE</th>
            //     <th rowspan='2'>COST CENTER</th>
            //     <th rowspan='2'>QTY</th>
            //     <th rowspan='2'>UOM</th>
            //     <th rowspan='2'>MRP</th>
            //     <th colspan='3'>ASSET</th>
            //     <th colspan='4'>STATUS</th>
            // </tr>
            // <tr>
            //     <th>NAMA</th>
            //     <th>JABATAN</th>
            //     <th>B</th>
            //     <th>BP</th>
            //     <th>RTLP</th>
            //     <th>JENIS</th>
            //     <th>GROUP</th>
            //     <th>SUB GROUP</th>
            //     <th>ASET</th>
            //     <th>SEWA</th>
            //     <th>DISPOSAL</th>
            //     <th>MUTASI</th>
            // </tr>";

            $b = "";
            $bp = "";
            $rltp = "";
            $dspa = "";
            $mtsa = "";
            $milik = "";
            $sewa = "";
            $foto_aset = "";
            $foto_seri = "";
            $foto_mesin = ""; 
            $hperolehan = "";
            $nbuku = "";
            
            foreach( $data['report'] as $k => $v )
            {

                $hperolehan = $v['HARGA_PEROLEHAN'];
                $nbuku = $v['NILAI_BUKU'];
                if($v['KONDISI_ASSET'] == 'B')
                {
                    $b = "<i class='fa fa-check'></i>";
                }

                if($v['KONDISI_ASSET'] == 'BP')
                {
                    $bp = "<i class='fa fa-check'></i>";
                }

                if($v['KONDISI_ASSET'] == 'RLTP')
                {
                    $rltp = "<i class='fa fa-check'></i>";
                }

                if($v['STATUS_DOCUMENT'] != '')
                {
                    if(strpos( $v['STATUS_DOCUMENT'], 'DSPA' )){
                        $dspa = "<i class='fa fa-check'></i>";
                        $mtsa = "";
                        $hperolehan = "0";
                        $nbuku = "0";
                    }
                    else{
                        $mtsa = "<i class='fa fa-check'></i>";
                        $dspa = "";
                        $hperolehan = "0";
                        $nbuku = "0";
                    }
                    
                }

                if(substr($v['BA_PEMILIK_ASSET'],0,2) == 12){
                    $milik =  "";
                    $sewa = "<i class='fa fa-check'></i>";
                }else{
                    $milik =  "<i class='fa fa-check'></i>";
                    $sewa = "";
                }
                if($v['FOTO_ASET'] <>''){
                    $foto_aset = "<img src='{$v['FOTO_ASET']}' width='100px' onerror="."this.style.display='none'"." />";
                }
                if($v['FOTO_SERI'] <>''){
                    $foto_seri = "<img src='{$v['FOTO_SERI']}' width='100px' onerror="."this.style.display='none'"." />";
                }
                if($v['FOTO_MESIN'] <>''){
                    $foto_mesin = "<img src='{$v['FOTO_MESIN']}' width='100px' onerror="."this.style.display='none'"." />";
                }
               
                $l .= "<tr> 
                    <td>$no</td>
                    <td>".$v['BA_PEMILIK_ASSET']."</td>
                    <td>".$v['KODE_ASSET_AMS']."</td>
                    <td>".$v['NAMA_ASSET']."</td>
                    <td>".$v['QUANTITY_ASSET_SAP']."</td>
                    <td>".$v['UOM_ASSET_SAP']."</td>
                    <td>".$v['MERK']."</td>
                    <td>".$v['SPESIFIKASI_OR_WARNA']."</td>
                    <td>".$v['NO_RANGKA_OR_NO_SERI']."</td>
                    <td>".$v['NO_MESIN_OR_IMEI']."</td>
                    <td>".$v['NO_POLISI']."</td>
                    <td>".$v['TAHUN_ASSET']."</td>
                    <td></td>
                    <td>".$v['NAMA_PENANGGUNG_JAWAB_ASSET']."</td>
                    <td>".$v['JABATAN_PENANGGUNG_JAWAB_ASSET']."</td>
                    <td>".$b."</td>
                    <td>".$bp."</td>
                    <td>".$rltp."</td>
                    <td>".$foto_aset."</td>
                    <td>".$foto_seri."</td>
                    <td>".$foto_mesin."</td>
                    <td>".number_format($hperolehan,0,',','.')."</td>
                    <td>".number_format($nbuku,0,',','.')."</td>
                    <td>".$dspa."</td>
                    <td>".$mtsa."</td>
                </tr>
                ";
               
                // $l .= "<tr> 
                //     <td>$no</td>
                //     <td>".$v['KODE_ASSET_AMS']."</td>
                //     <td>".$v['KODE_ASSET_SAP']."</td>
                //     <td>".$v['KODE_ASSET_CONTROLLER']."</td>
                //     <td>".$v['BA_PEMILIK_ASSET']."</td>
                //     <td>".$v['NAMA_PT_PEMILIK']."</td>
                //     <td>".$v['LOKASI_BA_CODE']."</td>
                //     <td>".$v['LOKASI_BA_DESCRIPTION']."</td>
                //     <td>".$v['NAMA_ASSET']."</td>
                //     <td>".$v['MERK']."</td>
                //     <td>".$v['SPESIFIKASI_OR_WARNA']."</td>
                //     <td>".$v['NO_RANGKA_OR_NO_SERI']."</td>
                //     <td>".$v['NO_MESIN_OR_IMEI']."</td>
                //     <td>".$v['NO_POLISI']."</td>
                //     <td></td>
                //     <td>".$v['NAMA_PENANGGUNG_JAWAB_ASSET']."</td>
                //     <td>".$v['JABATAN_PENANGGUNG_JAWAB_ASSET']."</td>
                //     <td>".$b."</td>
                //     <td>".$bp."</td>
                //     <td>".$rltp."</td>
                //     <td>".$v['NO_PO']."</td>
                //     <td>".$v['NAMA_VENDOR']."</td>
                //     <td>".$v['INFORMASI']."</td>
                //     <td>".$foto_aset."</td>
                //     <td>".$foto_seri."</td>
                //     <td>".$foto_mesin."</td>
                //     <td>".$v['ASSET_CLASS']."</td>
                //     <td>".$v['TAHUN_ASSET']."</td>
                //     <td>".number_format($v['HARGA_PEROLEHAN'],0,',','.')."</td>
                //     <td>".number_format($v['NILAI_BUKU'],0,',','.')."</td>
                //     <td>".$v['BOOK_DEPREC_01']."</td>
                //     <td>".$v['COST_CENTER']."</td>
                //     <td>".$v['QUANTITY_ASSET_SAP']."</td>
                //     <td>".$v['UOM_ASSET_SAP']."</td>
                //     <td></td>
                //     <td>".$v['JENIS_ASSET']."</td>
                //     <td>".$v['GROUP']."</td>
                //     <td>".$v['SUB_GROUP']."</td>
                //     <td>".$milik."</td>
                //     <td>".$sewa."</td>
                //     <td>".$dspa."</td>
                //     <td>".$mtsa."</td>
                // </tr>
                // ";

                $no++;
            }
            
            $l .= "</table>";
        }
        else
        {
            $l .= "Data not found!";
        }

        echo $l;

    ?>

    </div>
</div>