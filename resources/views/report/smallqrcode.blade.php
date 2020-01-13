<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
        <style>
            .main {
            font-family:Arial,Verdana,sans-serif;
            font-size:  7px; 
            }
            .dm {  margin-left: 2px; margin-top: 10px; margin-bottom: 2px;margin-right: 2px; }
            @page {  margin-left: 17px; margin-top: 7px; margin-bottom: 2px;margin-right: 2px; }
            .page_break { 
                    page-break-inside: avoid;
            } 
            img { 
                width:90%; 
                height:90%; 
                object-fit:cover; 
            } 
        </style>
</head>
<body onload="window.print()">


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
    <div width="100%" class="page_break">
        <div class = "dm">
        <!-- <div> -->
            <table class="main" width="100px">
                <?php
                    echo "<tr><td align='center'>".$string[$i] ."</td></tr>";
                    echo "<tr><td align='center'>".$string1[$i] ."</td></tr>";
                    echo "<tr><td align='center'>"."<img src='".storage_path("app/public/").$file_img[$i]."' />" ."</td></tr>";
                    // echo "<tr><td align='center'>"."<img src='".storage_path("app/public/").$file_img[$i]."' width='150px' />" ."</td></tr>";
                    echo "<tr><td align='center'>".$string2[$i] ."</td></tr>";
                    echo "<tr><td align='center'>".$string3[$i] ."</td></tr>";
                    
                ?>
            </table>
        </div>  
    </div>
    @endfor

<script type="text/javascript">
    $('td').each(function() {
    var $self = $(this), 
		fs = parseInt($self.css('font-size'));
    
    while($self.height() > threshold) {
        $self.css({'font-size': fs-- });
    }
    $self.height(threshold);
});
</script>

    
</body>
</html>