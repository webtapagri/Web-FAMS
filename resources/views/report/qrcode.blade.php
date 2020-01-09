<style>
.main {
  font-family:Arial,Verdana,sans-serif;
  font-size:10px;
}
.page_break { page-break-before: always; }
</style>

<?php 
    // $qrdata = $data['qrdata'];
        // dd($data);
    for($i = 0; $i < count($data) ; $i++){
            $string[] = $data[$i]->KODE_ASSET_AMS;
            $string1[] = $data[$i]->NAMA_ASSET;
            $string2[]= 'M I L I K : '.$data[$i]->BA_PEMILIK_ASSET.' ('.$data[$i]->BA_PEMILIK_ASSET_DESCRIPTION.')';
            $string3[] = 'LOKASI : '.$data[$i]->LOKASI_BA_CODE.' ('.$data[$i]->LOKASI_BA_DESCRIPTION.')';
    }
?>
    @for($i = 0; $i < count($file_img) ; $i++)
    <div style="page-break-inside: avoid;">
        <div style = "margin: 40px 20px 20px 60px">
            <table class="main">
                <?php
                    echo "<tr><td align='center'>".$string[$i] ."</td></tr>";
                    echo "<tr><td align='center'>".$string1[$i] ."</td></tr>";
                    echo "<tr><td align='center'>"."<img src='".storage_path("app/public/").$file_img[$i]."' width='150px' />" ."</td></tr>";
                    echo "<tr><td align='center'>".$string2[$i] ."</td></tr>";
                    echo "<tr><td align='center'>".$string3[$i] ."</td></tr>";
                    
                ?>
            </table>
        </div>  
    </div>
    @endfor

<script type="text/javascript">
    window.print();
</script>