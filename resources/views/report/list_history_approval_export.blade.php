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
        $i = 0;
        $doc = array();
        $rowspan = array();

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
                $doc[] =  $v->DOCUMENT_CODE.$v->CREATE_DATE;
            }
            $rowspan = array_count_values($doc);

            $a = 1;
            foreach( $report as $v )
            {
                $l .="<tr>";
                $role_name = str_replace('and','&amp;',$v->ROLE_NAME);
                $name = str_replace('and','&amp;',$v->NAME);
                        if($a == 1){
                            $l .= " <td rowspan = ".$rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE].">".$v->DOCUMENT_CODE."</td>
                                    <td rowspan = ".$rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE].">".$v->AREA_CODE."</td>
                                    <td rowspan = ".$rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE].">".$role_name."</td>
                                    <td rowspan = ".$rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE].">".$v->STATUS_DOKUMEN."</td>
                                    <td rowspan = ".$rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE].">".$v->CREATE_DATE."</td>";
                        }

                        if($a < $rowspan[$v->DOCUMENT_CODE.$v->CREATE_DATE]){ 
                                $a++;
                        }else{
                            $l .="";
                            $a = 1;
                        }

                    $l .= "
                    <td>".$v->BA."</td>
                    <td>".$v->USER_ID."</td>
                    <td>".$name."</td>
                    <td>".$v->STATUS_APPROVAL."</td>
                    <td>".$v->NOTES."</td>
                    <td>".$v->DATE."</td>";

                    
                $l .="</tr>";

                $no++;
                $i++;
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

</body>
</html>
