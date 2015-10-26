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


   <body>
    <div class="container">
    <h1>SimplePoll</h1><br>
<?php
//check if PID is defined and findable
if(empty($_POST['pid'])){die('Missing PID<br><iframe width="560" height="315" src="https://www.youtube.com/embed/xn2fRrDkfGw?autoplay=1" frameborder="0" allowfullscreen></iframe>');}
require "medoo.php";
require "vendor/autoload.php";
use Jenssegers\Optimus\Optimus;
$optimus = new Optimus(1206616819, 2051302971, 838816212);
$database = new medoo();
$getid = basename($_POST['pid']);
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
if(empty($poll["title"])){die('Bad PID<br><iframe width="560" height="315" src="https://www.youtube.com/embed/xn2fRrDkfGw?autoplay=1" frameborder="0" allowfullscreen></iframe>');}
//botcheck
if($poll["botcheck"] == 1){
$ans = $_POST['captcha']; //
$checksum = md5(strtolower(trim($ans)));
if (!in_array($checksum,$_SESSION['captcha_ans'])) { die("<h1>INVAILD ANTI-BOT RESPONSE</h1><br>01001001 01001110 01010110 01000001 01001001 01001100 01000100 01000001 01001110 01010100 01001001 00101101 01000010 01001111 01010100 01010010 01000101 01010011 01010000 010"); }
$_SESSION['captcha_ans'] = time();
}
//ipcheck
$ip = sha1($_SERVER['REMOTE_ADDR']);
if($poll["ipcheck"] == 1){
$ip = strtoupper($ip);
$ipcheck = $database->select("votes", ["vid", "pid"], [ "AND" => [ "ip" => $ip, "pid" => $pid ] ]);

if(!empty($ipcheck[0]["vid"])){die("<h2>You may only vote once on this poll, Sorry about that.</h2><br><br>$ip");}
}

//Main Switch

switch ($poll["type"]) {
    case 1: //Multichoice
        $questdb = $database->select("questions", [
        "qid",
        "text" ],[
        "pid" => $pid ]);
	//redo array to have key/value for this
	foreach($questdb as &$quest){
	$questions[$quest['qid']] = $quest['text'];
	}
	//match the arrays up and post the data
	foreach($_POST['choice'] as &$choice){
	$choice = (int) $choice;
	if(!empty($questions[$choice])){
	$choicedb[] = $choice;
	}else{
	die('<img src="http://i.imgur.com/cuMtVS8.jpg"><br><br><h1>Missing Choice<br>WTF DID YOU DO? STOP FUCKING ABOUT WITH THE POST DATA</h1>');
	}
	}
	
	//store votes
	foreach($choicedb as &$cid){
	$id = $database->insert("votes", [
	"ip" => $ip,
	"qid" => $cid,
	"pid" => $pid,
	"data" => 1
	]);
	//update results
	$id2 = $database->update("results", [ "amount[+]" => 1 ],[ "AND" => [ "qid" => $cid, "pid" => $pid ]]);
	}
        break;
    case 2: //Single Choice
        $questdb = $database->select("questions", [
        "qid",
        "text" ],[
        "pid" => $pid ]);
        //redo array to have key/value for this
        foreach($questdb as &$quest){
        $questions[$quest['qid']] = $quest['text'];
        }
        //match the arrays up and post the data
        $choice = (int) $_POST['radio'];
	if(!empty($questions[$choice])){
	$id = $database->insert("votes", [
        "ip" => $ip,
        "qid" => $choice,
        "pid" => $pid,
        "data" => 1
        ]);
	//update results
        $id2 = $database->update("results", [ "amount[+]" => 1 ],[ "AND" => [ "qid" => $choice, "pid" => $pid ]]);
	}else{
	die('<img src="http://i.imgur.com/cuMtVS8.jpg"><br><br><h1>Missing Choice<br>WTF DID YOU DO? STOP FUCKING ABOUT WITH THE POST DATA</h1>');
	}
        break;
    default:
       die("GG SHIT BROKE");
}
?>
<h2>Your choices have been logged and added to the poll. <br><a href="/p/r/<?php echo $getid;?>">Click here to see the poll results</a></h2>
</div>
</body>
</html>
