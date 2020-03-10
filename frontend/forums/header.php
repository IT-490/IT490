<!DOCTYPE html>
<html lang="en">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../bootstrap.min.css">
    <title>Home</title>
    <style type="text/css">
        nav {
            box-shadow: 2px 2px 10px #888;
	}
	table{
		box-shadow: 2px 2px 10px #888;
	}
        button {
            border-radius: unset !important;
            box-shadow: 2px 2px #888;
        }
    </style>
</head>
<body>

<nav class="no-session navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="../index.html">Go to Home</a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="../login.html"><button type="button" class="btn btn-primary">Login</button></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../register.html"><button type="button" class="btn btn-success">Register</button></a>
        </li>
    </ul>
</nav>

<nav style="display: none" class="has-session navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="../index.html">Home</a>
    <a id="welcome-message" class="ml-auto navbar-text mr-3"></a>
</nav>
<?php
session_start();
	if(isset($_SESSION['user'])){
		echo 
		'<script>
		$("#welcome-message").text("Welcome, ' . $_SESSION['user'] . '!");
		$("#welcome-message").attr("href", "../profile.php");
		$(".no-session").hide();
		$(".has-session").show();
		</script>';
	}else{
		echo
		'<script>
		$("#welcome-message").text("");
                $(".has-session").hide();
		$(".no-session").show();
		</script>';
	}
?>
