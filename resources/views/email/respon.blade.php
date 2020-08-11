<!DOCTYPE html>
<html>
<body>

<?php 
   if (array_key_exists("status",$message))
   {
        if($message['status'] == 'R'){
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
        echo $message;
   }
?>

<script>

// $("#reject-note").submit(function(event) {

//     var note_reject = $("#notes").val();
//     /* stop form from submitting normally */
//     event.preventDefault();

//     /* get the action attribute from the <form action=""> element */
//             var $form = $(this),
//             url = "{{ url('approval/update_status_disposal_email') }}";
//             var message = <?php echo json_encode($message); ?>
//             message['note'] = note_reject;
//             var param = JSON.stringify(message);

//     /* Send the data using post with element id name and name2*/
//         var posting = $.post(url, param);

//     /* Alerts the results */
//     posting.done(function(data) {
//         send_email_create_po(message['noreg']);
//         $('#result').text('Data successfully updated');
//     });
//     posting.fail(function() {
//          $('#result').text('Failed to Update');
//     });
// });

// $("#reject-note").submit(function(event){
//         var getnoreg = $("#getnoreg").val();
//         var no_registrasi= getnoreg.replace(/\//g, '-');
//         var specification = $("#specification-disposal").val();
//         var note_reject = $("#notes").val();

function changeStatusDisposal(status)
    {
        var getnoreg = $("#getnoreg").val();
        var no_registrasi= getnoreg.replace(/\//g, '-');
        var specification = $("#specification-disposal").val();

        if( status == 'A' ){ status_desc = 'approve'; }else
        if( status == 'R' )
        { 
            status_desc = 'reject';
            note_reject = $("#specification-disposal").val();

            if( $.trim(note_reject) < 2 )
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
            var message = <?php echo json_encode($message); ?>
            message['note'] = note_reject;
            var param = JSON.stringify(message);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: "{{ url('approval/update_status_disposal_email') }}",
                method: "POST",
                data: param,
                success: function() 
                {
                        send_email_create_po(message['noreg']);
                }
            });
        }
    };

    function send_email_create_po(noreg)
    {
        //alert(noreg);

        var getnoreg = noreg;
        var no_registrasi= getnoreg.replace(/\//g, '-');

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
            data: "noreg="+no_registrasi,
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