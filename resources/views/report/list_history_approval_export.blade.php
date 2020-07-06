<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report List Asset</title>
</head>
<body>
<div class="container">
    <div xclass="xtable-scroll ex1" xstyle="background-color: #FFF;overflow: auto;">

    <center><h1><u>REPORT HISTORICAL APPROVAL</u></h1><h3>Per tanggal : <?php echo date('d/m/Y'); ?></h3></center>

    <?php 
        $l = "";
        $no = 1;
        $i = 1;

        if(!empty($report))
        {
            $l .= "<table border=1 cellspacing=0 cellpadding=5 class='table tabel-responsive table-bordered'>";
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


            foreach( $report as $v )
            {
                $role_name = str_replace('and','&amp;',$v->ROLE_NAME);
                $name = str_replace('and','&amp;',$v->NAME);
                $l .= "<tr> 
                            <td>".$v->DOCUMENT_CODE."</td>
                            <td>".$v->AREA_CODE."</td>
                            <td>".$role_name."</td>
                            <td>".$v->STATUS_DOCUMENT."</td>
                            <td>".$v->PO_DATE."</td>
                            <td>".$v->BA."</td>
                            <td>".$v->USER_ID."</td>
                            <td>".$name."</td>
                            <td>".$v->STATUS_APPROVAL."</td>
                            <td>".$v->NOTES."</td>
                            <td>".$v->APPROVE_DATE."</td>
                    </tr>
                    ";

                $i++;
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
</body>
</html>
