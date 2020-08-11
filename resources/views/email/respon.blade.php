<!DOCTYPE html>
<html>
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<?php 
    $dt = unserialize(urldecode($_GET['id']));
   if (array_key_exists("status",$dt))
   {
        if($dt['status'] == 'R'){
            ?>
            <form id="reject-note">
                <label for="notes">Note Reject Document Code: <?php echo $message['noreg'] ?></label><br>
                <textarea id="notes" name="notes" rows="4" cols="50" required></textarea><br>
                <button type="button" class="btn btn-flat label-danger button-reject" OnClick="changeStatusDisposal('R')" >REJECT</button>
            <form>   

            <div id="result"></div>
            <?php
        }
   }
   else
   {
        $message = unserialize(urldecode($message));
        echo $message;
   }
?>

<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script type="text/javascript" charset="utf-8">
// $(document).ready(function(){
function changeStatusDisposal(status)
    {
        var getnoreg = $("#getnoreg").val();
        // var no_registrasi= getnoreg.replace(/\//g, '-');
        var specification = $("#notes").val();

        if( status == 'A' ){ status_desc = 'approve'; }else
        if( status == 'R' )
        { 
            status_desc = 'reject';
            note_reject = $("#notes").val();

            if( note_reject == '' )
            {
                notify({
                    type: 'warning',
                    message: " Note Reject is required (min 2 char)"
                });
                return false;
            } 

        }else{ status_desc = 'cancel'; }

        if(confirm('confirm '+status_desc+' data ?'))
        {
            var message = <?php echo json_encode($message); ?>;
            message['note'] = note_reject;
            var param = JSON.stringify(message);
            console.log(param);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: "{{ url('approval/update_disposal_email') }}?id="+param,
                method: "POST",
                data: param ,
                success: function() 
                {
                        send_email_create_po(message['noreg']);
                }
            });
        }
    };
// });

    function send_email_create_po(noreg)
    {
        //alert(noreg);

        var getnoreg = noreg;
        // var no_registrasi= getnoreg.replace(/\//g, '-');

        //alert(id+"_"+no_po+"_"+no_reg_item+"_"+no_registrasi);

        var param = '';//$("#request-form-detail-asset-sap").serialize();
        //alert(capitalized_on);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('request/email_create_po') }}",
            // method: "POST",
            type: "POST",
            // data: param+"&noreg="+no_registrasi,
            data: "noreg="+getnoreg,
            beforeSend: function() {
                $('.loading-event').fadeIn();
            },
            success: function(result){},
            complete: function() {
                jQuery('.loading-event').fadeOut();
            }
        }); 
    }
</script>
</body>
</html>