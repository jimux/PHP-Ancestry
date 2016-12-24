<?
include("../include/session.php");

$session->showHeader("People Stats");
if(!$session->logged_in){
  die("You must be logged in");
}

$oldest = $database->getOldestPeople();
?>

<h3>Top 10 Oldest People</h3>
<table border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Name</b></td> <td><b>Age</b></td></tr>

<?
foreach($oldest as $oldperson){
  $person = $database->getPersonInfo($oldperson['id']);
  
  echo "\n  <tr>";
  echo "\n    <td><a href=\"" . docRoot . "/ancestors/viewperson?person=" . $person['id'] . "\">" . $person['first'] . " " . $person['middle'] . " " . $person['last'] . "</a></td>";
  echo "\n    <td>" . $oldperson['age'] . "</td>";
  echo "\n  </tr>";
}
?>

</table></body></html>