<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SimplePoll</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="refresh" content="20">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://flatlogic.github.io/awesome-bootstrap-checkbox/demo/build.css" rel="stylesheet">
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


   <body>
    <div class="container">
    <h1>SimplePoll</h1><br>
<?php
require "medoo.php";
require "vendor/autoload.php";
use Jenssegers\Optimus\Optimus;
$optimus = new Optimus(1206616819, 2051302971, 838816212);
$database = new medoo();

$getid = basename($_GET['q']);
$pid = (int) base62decode($getid);
echo '<img src="https://ga-beacon.appspot.com/UA-68001702-1/simplepoll/getpoll-'.$getid.'?pixel">';
$poll = $database->select("polls", [
        "title",
        "type",
        "botcheck",
        "ipcheck",
        "created"

], [
        "id" => $pid
]);
$poll = $poll[0];
if(empty($poll['type'])){die("<pre>
███████╗██████╗ ██████╗  ██████╗ ██████╗     ██╗  ██╗ ██████╗ ██╗  ██╗
██╔════╝██╔══██╗██╔══██╗██╔═══██╗██╔══██╗    ██║  ██║██╔═████╗██║  ██║
█████╗  ██████╔╝██████╔╝██║   ██║██████╔╝    ███████║██║██╔██║███████║
██╔══╝  ██╔══██╗██╔══██╗██║   ██║██╔══██╗    ╚════██║████╔╝██║╚════██║
███████╗██║  ██║██║  ██║╚██████╔╝██║  ██║         ██║╚██████╔╝     ██║
╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝         ╚═╝ ╚═════╝      ╚═╝

██████╗  ██████╗ ██╗     ██╗         ███╗   ██╗ ██████╗ ████████╗    ███████╗ ██████╗ ██╗   ██╗███╗   ██╗██████╗
██╔══██╗██╔═══██╗██║     ██║         ████╗  ██║██╔═══██╗╚══██╔══╝    ██╔════╝██╔═══██╗██║   ██║████╗  ██║██╔══██╗
██████╔╝██║   ██║██║     ██║         ██╔██╗ ██║██║   ██║   ██║       █████╗  ██║   ██║██║   ██║██╔██╗ ██║██║  ██║
██╔═══╝ ██║   ██║██║     ██║         ██║╚██╗██║██║   ██║   ██║       ██╔══╝  ██║   ██║██║   ██║██║╚██╗██║██║  ██║
██║     ╚██████╔╝███████╗███████╗    ██║ ╚████║╚██████╔╝   ██║       ██║     ╚██████╔╝╚██████╔╝██║ ╚████║██████╔╝
╚═╝      ╚═════╝ ╚══════╝╚══════╝    ╚═╝  ╚═══╝ ╚═════╝    ╚═╝       ╚═╝      ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═════╝
</pre>");}
//grab poll data
$results = $database->select("results", [
        "qid",
        "amount"
], [
        "pid" => $pid
]);
        $questions = $database->select("questions", [
        "qid",
        "text" ],[
        "pid" => $pid ]);
//format questions
foreach($questions as &$quest){
$questdb[$quest['qid']]['text'] = $quest['text'];
}
//grab totals and add them to the questdb
$totalvotes = 0;
foreach($results as &$rid){
$totalvotes += $rid['amount'];
$questdb[$rid['qid']]['amount'] = $rid['amount'];
}
//display votes
echo "<h2>" . html_entity_decode ( wordwrap($poll['title'], 50, "<br />\n", true)) . "</h2><br>";
foreach($questdb as &$result){
//do precentage math
$pre = $result['amount']/$totalvotes;
echo html_entity_decode (wordwrap($result['text'], 50, "<br />\n", true));
echo '
<div style="width: 30%;" title="' .html_entity_decode ( $result['text']). '">
<div style="text-align: left; margin: 2px auto; font-size: 0px; line-height: 0px; border: solid 1px #AAAAAA; background: #DDDDDD; overflow: hidden; ">
<div style="font-size: 0px; line-height: 0px; height: 20px; min-width: 0%; max-width: ' . number_format( $pre * 100, 2 ) . '%; width: ' . number_format( $pre * 100, 2 ) . '%; background: #1D3D8D; ">
<!----></div></div><div style="margin: auto; text-align: center; font-size: 8pt;">' . number_format( $pre * 100, 2 ) . '% ' .$result['amount'] . '/'.$totalvotes.' </div></div>
';
}
?><br><br><small>This page auto-refreshes every 20 seconds</small><br><br><small><small>Made by /u/xJRWR - Jet Balsa, Send ISK, I'm space broke</small></small>
   </div>
  </body>
</html>


