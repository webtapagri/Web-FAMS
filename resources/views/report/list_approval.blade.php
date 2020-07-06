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

    <center><h1><u>REPORT HISTORICAL APPROVAL</u></h1><h3>Per tanggal : <?php echo date('d/m/Y'); ?></h3></center>

    <?php 
        $l = "";
        $no = 1;

        if(!empty($data))
        {
            $l .= "<table border=1 cellspacing=0 cellpadding=5 class='table tabel-responsive table-bordered' id='myTable'>";
            $l .= "<tr>
                <th>DOCUMENT CODE</th>
                <th>AREA CODE</th>
                <th>ROLE NAME</th>
                <th>STATUS DOCUMENT</th>
                <th>DATE</th>
                <th>AREA CODE</th>
                <th>USER ID</th>
                <th>NAME</th>
                <th>STATUS APPROVAL</th>
                <th>NOTES</th>
                <th>DATE</th>
            </tr>";
            
            foreach( $data['report'] as $k => $v )
            {

                $l .= "<tr> 
                    <td>".$v['DOCUMENT_CODE']."</td>
                    <td>".$v['AREA_CODE']."</td>
                    <td>".$v['ROLE_NAME']."</td>
                    <td>".$v['STATUS_DOCUMENT']."</td>
                    <td>".$v['PO_DATE']."</td>
                    <td>".$v['BA']."</td>
                    <td>".$v['USER_ID']."</td>
                    <td>".$v['NAME']."</td>
                    <td>".$v['STATUS_APPROVAL']."</td>
                    <td>".$v['NOTES']."</td>
                    <td>".$v['APPROVE_DATE']."</td>
                </tr>
                ";

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

<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.rowspanizer.js') }}"></script>

<script>
$(document).ready(function()
{
    $('table').rowspanizer({
        columns: [0,1,2,3,4]
    });


});
</script>