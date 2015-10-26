<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SimplePoll</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        width: 768px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
  </head>


   <body><img src="https://ga-beacon.appspot.com/UA-68001702-1/simplepoll/makepoll?pixel">
    <div class="container">
    <h1>SimplePoll</h1><br>
<?php
require "medoo.php";
require "vendor/autoload.php";
use Jenssegers\Optimus\Optimus;
$optimus = new Optimus(1206616819, 2051302971, 838816212);


if(!empty($_POST['question'])){

$ans = $_POST['captcha']; //
$checksum = md5(strtolower(trim($ans)));
if (!in_array($checksum,$_SESSION['captcha_ans'])) { die("<h1>INVAILD ANTI-BOT RESPONSE</h1><br>01001001 01001110 01010110 01000001 01001001 01001100 01000100 01000001 01001110 01010100 01001001 00101101 01000010 01001111 01010100 01010010 01000101 01010011 01010000 01001111 01001110 01010011 01000101");}
// check all the fields for bullshit
$data['q'] = substr(htmlentities(trim(filter_var($_POST['question'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))), 0, 255);
if(strlen($data['q']) < 6){die("<h1>Question was too short! Go back and make it longer!</h1>");}
for ($x = 1; $x <= 10; $x++) {
$data['c'][] = substr(htmlentities(trim(filter_var($_POST["c_$x"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))), 0, 255);
}
foreach($data['c'] as $k => $v){ if(empty($data['c'][$k])) {unset($data['c'][$k]);}}
$data['c'] = array_values($data['c']);
if(count($data['c']) < 2){die("<h1>You must have more then 1 choice for polls!</h1>");}
if($_POST['type'] != 1 && $_POST['type'] != 2){die('<iframe width="560" height="315" src="https://www.youtube.com/embed/xn2fRrDkfGw?autoplay=1" frameborder="0" allowfullscreen></iframe>');}
if(!empty($_POST['ipcheck'])){$ipcheck = 1;} else { $ipcheck = 0; }
if(!empty($_POST['botcheck'])){$botcheck = 1;} else { $botcheck = 0; }
// VAILD POLL - DATA AT THIS POINT - #YOLO
$_SESSION['captcha_ans'] = time();
$database = new medoo();
 
$id = $database->insert("polls", [
	"ip" => sha1($_SERVER["HTTP_X_FORWARDED_FOR"]),
	"title" => $data['q'],
	"type" => (int) $_POST['type'],
	"botcheck" => $botcheck,
	"ipcheck" => $ipcheck,
	"created" => time()
]);
foreach($data['c'] as $c){
$cid = $database->insert("questions", [
        "text" => $c,
        "pid" => $id
]);
//insert cache
$database->insert("results", [
        "pid" => $id,
        "qid" => $cid,
	"amount" => 0,
]);
}
$oid = base62encode($id);
echo "<h3>Your Poll has been submitted</h3><br><h4>You can find your new poll at  <a href=http://jrwr.space/p/$oid>http://jrwr.space/p/$oid</a></h4><br><br>Polls only last one month starting when the poll started<br>Polls and their votes are removed after two months.";
}else{
?><h1>Make a New Poll</h1><br><small>All fleids are maxed at 255 characters</small><br><br><br>
<form action="makepoll.php" method="POST">
  Poll Question: <br>
  <input type="text" maxlength="255"  name="question" value=""><br><br>
  <br>
  <b>Poll Type</b><br>
  <input type="radio" name="type" value="1" checked>Multiple Choice
  <br>
  <input type="radio" name="type" value="2">Single Choice<br><br>
  <br><br>
<?php
for ($x = 1; $x <= 9; $x++) {
    echo 'Choice #' . $x . ': <input type="text" maxlength="255"  name="c_' . $x . '"><br><br>';
}
echo 'Choice #10: <input type="text" maxlength="255"  name="c_10" value="Checkbox"><br><br>';
?>
<b>Poll Options</b><br>
<input type="checkbox" name="ipcheck" value="1" checked> One Vote per IP<br>
<input type="checkbox" name="botcheck" value="1"> Anti-Bot Protections<br>
<br><br>
<?php
$url = 'http://api.textcaptcha.com/redditxjrwr.json';
$captcha = json_decode(file_get_contents($url),true);
if (!$captcha) {
 $captcha = array( // fallback challenge
  'q'=>'Is ice hot or cold?',
  'a'=>array(md5('cold'))
 );
}
echo "<b>Anti-Bot Question:</b><br> " . htmlentities($captcha['q']);
// store answers in session
$_SESSION['captcha_ans'] = $captcha['a'];
?>
<br><input type="text" maxlength="255"  name="captcha" value=""><br><input type="submit" value="Submit">
<?php
}
