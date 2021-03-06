<?php
if(isset($_GET["ajax"])) { // if key($_GET) == "ajax"
	$locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	setlocale(LC_MONETARY, $locale);

	if(!isset($_POST["amount"]) || !isset($_POST["tip"]) || !isset($_POST["people"])) {
		die(json_encode(array("error" => "server error: server request parameters missing/incomplete")));
	}
	if(!is_numeric($_POST["amount"]) ||
	  ($_POST["tip"] != "custom" && !is_numeric($_POST["tip"]) || ($_POST["tip"] == "custom" && !is_numeric(@$_POST["tip-custom"]))) || 
	  ($_POST["people"] != "custom" && !is_numeric($_POST["people"])) || ($_POST["people"] == "custom" && !is_numeric(@$_POST["people-custom"]))) {
		die(json_encode(array("error" => "Invalid entry")));
	}

	$amount = intval($_POST["amount"]);
	$tip = intval($_POST["tip"]);
		if($_POST["tip"] == "custom") $tip = intval($_POST["tip-custom"]);
	$people = intval($_POST["people"]);
		if($_POST["people"] == "custom") $people = intval($_POST["people-custom"]);

	$grandtotal = $amount + ($amount * ($tip/100));
	$perhead = $grandtotal / $people;
	die(json_encode(array("grand-total" => money_format('%n', $grandtotal), "per-head" => money_format('%n', $perhead))));
}
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width">
		<meta name="viewport" content="initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="main.css">
		<script
		  src="https://code.jquery.com/jquery-3.1.1.min.js"
		  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
		  crossorigin="anonymous">
		</script>
	</head>
	<body onload="load()">
		<div class="splash fullscreen">
			<div class="vertical-align">
				<img class="centered" src="img/AppIcon1024x1024.png">
				<br>
				<div class="progress centered-sm"><div class="bar"></div></div>
			</div>
		</div>
		<form name="tipsy" onsubmit="event.preventDefault(); setTimeout(submitAjax,0); return false;">
		<div class="main fullscreen" style="overflow:scroll">
			<div class="header block">
				<img src="img/AppIcon1024x1024.png">
				<div class="brand">
					tipsy<span class="disclaimers">&trade;</span>
				</div>
			</div>
			<div class="body block">
					<div>
						<div class="left">$</div>
						<div class="right underline">
							<input name="amount" autocomplete="false" id="amount" type="number" min="0.01" step="0.01" onkeyup="$(document.tipsy).submit()">
						</div>
					</div>
					<div>
						<div class="left"></div>
						<div class="right">
							<br>
							<div class="list-group segmented-control tip">
<?php
							for($i = 10; $i <= 20; $i += 5) {
					                	echo "
								<a href='#' class='list-group-item'>
									$i%
									<input type='radio' name='tip' value='$i'/>
								</a>";
							}
?>
				                <a href="#" class="list-group-item">
				                    <input name="tip-custom" id="percent" size="3" type="text" pattern="\d*" maxlength="3" placeholder="custom" class="min-width" onfocus="$(document.tipsy).submit()" onkeyup="adjustMinWidth(this);$(document.tipsy).submit()"> %
				                    <input type="radio" name="tip" value="custom"/>
				                </a>
				            </div>

				            <span class="divider"><br></span>

				            <div class="list-group segmented-control people">
<?php
							$i = 1;
							while($i <= 3) {
					                	echo "
								<a href='#' class='list-group-item'>
									<img src='img/head.png'> $i
									<input type='radio' name='people' value='$i'/>
								</a>";
								$i++;
							}
?>
				                <a href="#" class="list-group-item">
				                    <img src="img/head.png"> <input name="people-custom" id="people" size="3" type="text" pattern="\d*" maxlength="3" placeholder="custom" class="min-width" onfocus="$(document.tipsy).submit()" onkeyup="adjustMinWidth(this);$(document.tipsy).submit()" style="text-align:left">
				                    <input type="radio" name="people" value="custom"/>
				                </a>
				            </div>
						</div>
					</div>
					<br>
					<br>
					<br>
					<br>
					<div id="total">
					<hr>
						<div class="left">total:</div>
						<div class="right">
							<div class="overlap" id="error"></div>
							<div class="overlap" id="result">
								<span class="grand-total">$104.34</span>
								<br>
								<span class="per-head">$2</span>
								<span>/</span>
								<img src="img/head.png" style="">							
							</div>
							<div class="sk-cube-grid">
								<div class="sk-cube sk-cube1"></div>
								<div class="sk-cube sk-cube2"></div>
								<div class="sk-cube sk-cube3"></div>
								<div class="sk-cube sk-cube4"></div>
								<div class="sk-cube sk-cube5"></div>
								<div class="sk-cube sk-cube6"></div>
								<div class="sk-cube sk-cube7"></div>
								<div class="sk-cube sk-cube8"></div>
								<div class="sk-cube sk-cube9"></div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</form>
	</body>
	<script type="text/javascript" src="main.js"></script>
</html>