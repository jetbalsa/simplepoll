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
<?php
require "medoo.php";
require "vendor/autoload.php";
use Jenssegers\Optimus\Optimus;
$optimus = new Optimus(1206616819, 2051302971, 838816212);
$database = new medoo();

//antibot
$url = 'http://api.textcaptcha.com/redditxjrwr.json';
$captcha = json_decode(file_get_contents($url),true);
if (!$captcha) {
 $captcha = array( // fallback challenge
  'q'=>'Is ice hot or cold?',
  'a'=>array(md5('cold'))
 );
}


echo '<img src="https://ga-beacon.appspot.com/UA-68001702-1/simplepoll/getpoll-'.$getid.'?pixel">';
$getid = basename($_GET['q']);
$pid = (int) base62decode($getid);
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
if(empty($poll['type'])){var_dump($pid);die("<h1>SimplePoll</h1><br><pre>
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
// 30days 2592000
if(time() - $poll['created'] < 2592000){
?>
<form action="/p/submitpoll.php" method="POST" class="form-signin"><h1>SimplePoll</h1><br>
<?php
}else{
echo '<form class="form-signin" method="POST"><h1>SimplePoll</h1><br>';
echo '<div class="alert alert-error">This poll is now closed! Please see the results <a href="/p/r/' . $getpid  .'">here</a></div>';
}
switch ($poll['type']) {
    case 1: // MultiChoice Template
	$title = html_entity_decode ( wordwrap($poll['title'], 50, "<br />\n", true) );
        echo "<h3>$title</h3><br><small>Please choose any of the options below</small><br><br><ul class='unstyled'>";
        $questions = $database->select("questions", [
        "qid",
        "text" ],[
        "pid" => $pid ]);
        foreach($questions as &$quest){
	$quest['text'] = html_entity_decode ( wordwrap($quest['text'], 50, "<br />\n", true));
        echo '<li><div class="checkbox checkbox-success"><input id="check' . $quest['qid'] . '" type="checkbox" class="styled checkbox-circle" name="choice[]" value="' . $quest['qid'] . '">
<label for="check' . $quest['qid'] . '">' . $quest['text'] . '</label></div></li>';
        }echo "</ul><br><br>";
        if($poll["botcheck"]){
        echo "<b>Anti-Bot Question:</b><br> " . htmlentities($captcha['q']);echo '<br><input type="text" maxlength="255" name="captcha" value="">';
        // store answers in session
        $_SESSION['captcha_ans'] = $captcha['a'];
        } echo '<input type="hidden" name="pid" value="' . $getid  . '">';
        echo '<br><br><input type="submit" class="btn btn-large btn-primary" value="Submit">';

        break;
    case 2: // Single Choice Template
	$title = wordwrap($poll['title'], 50, "<br />\n", true);
	echo "<h3>$title</h3><br><small>Please choose one of the options below<br>";
	$questions = $database->select("questions", [
	"qid",
	"text" ],[
	"pid" => $pid ]);
	foreach($questions as &$quest){
         $quest['text'] = html_entity_decode ( wordwrap($quest['text'], 50, "<br />\n", true));
        echo '<div class="radio radio-success"><input type="radio" name="radio" id="radio' . $quest['qid'] . '" value="' . $quest['qid'] . '"><label for="radio' . $quest['qid'] . '">' . $quest['text'] . '</label></div>';
	}echo "<br><br>";
        if($poll["botcheck"]){
	echo "<b>Anti-Bot Question:</b><br> " . htmlentities($captcha['q']);echo '<br><input type="text" maxlength="255" name="captcha" value="">';
	// store answers in session
	$_SESSION['captcha_ans'] = $captcha['a'];
	} echo '<input type="hidden" name="pid" value="' . $getid  . '">';
	echo '<br><br><input type="submit" class="btn btn-large btn-primary" value="Submit">';
    break;
    default: // GG NOT FOUND
	echo "ERROR";
        break;
}
?>   <a href="/p/r/<?php echo $getid;?>">Skip and see results</a><br><br><br><small>Made by /u/xJRWR - Jet Balsa, Send ISK, I'm space broke</small>
   </div>
  </body>
</html>

