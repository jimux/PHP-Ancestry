<?
include("../include/session.php");
?>

<? $database->postToLog("User " . $session->username . " viewed the log."); ?>

<?
$session->showHeader("Log");
if(!$session->isAdmin()){
  die("You must be an admin");
}
?>

<h1>Site log:</h1>

<?
   $q = "SELECT message,time FROM log";
   $result = mysql_query($q, $database->connection);
   $num_rows = mysql_numrows($result);
   
   /* Display table contents */
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Message</b></td><td><b>Timestamp</b></td></tr>\n";
   for($i=0; $i<$num_rows; $i++){
      $message = mysql_result($result,$i,"message");
      $time  = mysql_result($result,$i,"time");

      echo "<tr><td>$message</td><td>$time</td></tr>\n";
   }
   echo "</table><br>\n";
?>