<?php

require_once('functions.php');

$term =  $_REQUEST['term'];
$data = ['type' => 'search', 'data' => $term];

$response = sendRabbit($data);
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
    <title>Search</title>
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
    <a class="navbar-brand" href="index.html">Home</a>
    <a id="welcome-message" class="ml-auto navbar-text mr-3"></a>
</nav>


<div class="container mt-4 mb-4">

    <?php if($response !== 2) { ?>
        <h2 class="text-center">Search Results</h2>
        <hr>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Show Name</th>
                        <th>Network</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($response as $show) { ?>
                        <tr>
                            <td><a href="./shows.php?id=<?php echo $show['showID'] ?>"><?php echo $show['name']?></a></td>
                            <td><?php echo $show['network']?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } else { ?>
        <h2 class="text-center">Sorry. No search results found.</h2>
    <?php } ?>

</div>


<script src="jquery.min.js"></script>
<script>
    $(window).on('load', function() {
        $.ajax({
            url: 'validate.php', // The php script that checks if the session exists
            type: 'GET',
            dataType: 'json'
        }).done(function(data) {
            if(data.set) { // A Session exists. Switch to the signed-in version
                $("#welcome-message").text("Welcome, " + data.username + "!");
		$("#welcome-message").attr("href", "./profile.php");
		$(".no-session").hide();
                $(".has-session").show();
            } else {

            }
        })
    });

</script>

</body>
</html>
