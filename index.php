<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>People & Group CSV Import</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
</head>
<body>
<div id="cover"></div>
<div class="container">
    <br>
        <h3> People and Groups import</h3>
    </br>
    <div class="row">
        <div class="alert alert-success alert-dismissible fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> <span class="msg">This alert box could indicate a successful or positive action.</span>
        </div>

        <div class="alert alert-danger alert-dismissible fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Danger!</strong> <span class="msg">This alert box could indicate a successful or positive action.</span>
        </div>
    </div>

    <form id="upload_form" class="form-horizontal "action="includes/import.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="csv" class="control-label col-xs-2">CSV file</label>
            <div class="col-xs-3">
            <input type="file" class="form-control" name="csv" id="csv" placeholder="" required>
            </div>
        </div>

        <div class="form-group">
        <label for="csv" class="control-label col-xs-2"></label>
        <div class="col-xs-3">
        <button type="submit" class="btn btn-primary">Upload</button>
        </div>
        </div>
    </form>
    <div id="cus-progress-wrp"><div class="cus-progress-bar"></div ><div class="status">0%</div></div>
    <table id="person_table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Person Id</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Group</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
    </table>
</div>
<script type="text/javascript">
$(window).on('load', function(){
    $('#cover').fadeOut(1000);
})
$(document).ready(function() {
    person_table();

var my_form_id = '#upload_form'; //ID of an element for response output
var progress_bar_id = '#cus-progress-wrp'; //ID of an element for response output

$(progress_bar_id).hide();
$('.alert').hide();
//on form submit
$(my_form_id).on( "submit", function(event) {

    $(progress_bar_id).show();
    //$('#cover').show();
	event.preventDefault();
	var proceed = true; //set proceed flag
	var error = [];	//errors

	//reset cus-progressbar
	$(progress_bar_id +" .cus-progress-bar").css("width", "0%");
	$(progress_bar_id + " .status").text("0%");

    var submit_btn  = $(this).find("input[type=submit]"); //form submit button

    //if everything looks good, proceed with jQuery Ajax
    if(proceed){
        //submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
        var form_data = new FormData(this); //Creates new FormData object
        var post_url = $(this).attr("action"); //get action URL of form

        //jQuery Ajax to Post form data
        $.ajax({
            url : post_url,
            type: "POST",
            data : form_data,
            contentType: false,
            cache: false,
            processData:false,
            xhr: function(){
                //upload Progress
                var xhr = $.ajaxSettings.xhr();

                xhr.addEventListener('progress', function(event) {
                    console.log(event);
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //update progressbar
                    $(progress_bar_id +" .cus-progress-bar").css("width", + percent +"%");
                    $(progress_bar_id + " .status").text(percent +"% uploaded");
                }, true);
                return xhr;
            },
            mimeType:"multipart/form-data"
        }).progress(function(ev) {
            console.log(ev);
        }).done(function(res){ //
            $(my_form_id)[0].reset(); //reset form
            res = JSON.parse(res);
            console.log(res);
            if (res.type == 'success') {
                $('.alert-success').show();
            }else{
                $('.alert-danger').show();
            }
            $('.msg').html(res.msg);
            $(progress_bar_id).delay(10000).hide();
            //$('#cover').delay(8000).hide();
            person_table();

            submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
        });
    }
});


    function person_table(){
        url = "includes/get_person_data.php";
        var table = $('#person_table').DataTable({
            "bDestroy" : true,
			"processing": true,
			"deferRender": true,
            "columnDefs": [
                { "visible": false, "targets": 4 }
            ],
            "order": [[ 4, 'asc' ]],
            "displayLength": 10,
			"ajax": {
				"url"		:	url,
				"type"		: 	"POST",
				"cache"		:	"false"
			},
            "drawCallback": function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;

                api.column(4, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                        );

                        last = group;
                    }
                } );
            }
        } );
    }
} );


</script>
</body>
</html>
