<?php

require_once('functions.php');
session_start();
$user = sanatize($_SESSION['user']);
$response = sendRabbit(array('type'=> 'getSchedule', 'data'=> $user));
$data = array('Sunday'=> array('name'=> 'Sunday'), 'Monday'=> array('name'=> 'Monday'), 'Tuesday'=> array('name'=> 'Tuesday'), 'Wednesday'=> array('name'=> 'Wednesday'), 'Thursday'=> array('name'=>'Thursday'), 'Friday'=> array('name'=>'Friday'),'Saturday'=> array('name'=>'Saturday'));

foreach($response as $show){
	$data[date('l', strtotime($show['airdate']))]['shows'][] = array('name'=> $show['name'], 'airdate'=> date('g:i A', strtotime($show['airdate']))); 	
}
// If the request was sent via AJAX
if(isset($_REQUEST['ajax'])) {
    echo json_encode($response);
    exit();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="bootstrap.min.css">
    <title>Schedule</title>
    <style type="text/css">
        nav {
            box-shadow: 2px 2px 10px #888;
        }
        button {
            border-radius: unset !important;
            box-shadow: 2px 2px #888;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="index.html">Go to Home</a>
    <span id="welcome-message" class="ml-auto navbar-text mr-3"></span>
</nav>


<div class="container mt-4 mb-4">
    <h2 class="text-center">Schedule</h2>
    <hr>

    <div class="table-responsive">
        <table id="schedule" class="table table-bordered">
           <thead class="bg-light">
		<tr>
                    <?php foreach ($data as $day) { ?>
                        <th><?php echo $day['name'] ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach ($data as $shows){ ?>
                        <td>
                            <ul class="">
                                <?php foreach ($shows['shows'] as $show) { ?>
                                    <li><b><?php echo  $show['name'] ?></b> <br> <?php echo $show['airdate']?></li>
                                <?php } ?>
                            </ul>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>

</div>


<script src="jquery.min.js"></script>
<script>
    $(window).on('load', function() {
        $.ajax({
            url: 'validate.php', // The php script that checks if the session exists
            type: 'GET',
            dataType: 'json'
        })
            .done(function(data) {
                if(data.set) { // A Session exists. Switch to the signed-in version
                    $("#welcome-message").text("Welcome, " + data.username + "!");
                    $(".no-session").hide();
                    $(".has-session").show();
                } else { // No session. Redirect to home
                    window.location.href = "index.html";
                }
            })
    });

    $(document).ready(function() {
        // Follow show
        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: {"ajax": true, "action": "get_shows"},
            dataType: 'json'
        })
            .done(function (data) {
                //for (let show of data)
                   // $("#shows-list").append("<a href='shows.php?id=" + show.id + "' class='list-group-item'>" + show.name + "</a>");
            });
    });

</script>

</body>
</html>
