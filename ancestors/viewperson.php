<?
include("../include/session.php");

$session->showHeader("View Person");
if(!$session->logged_in){
  die("You must be logged in");
}

/* Requested Username error checking */
$req_person = trim($_GET['person']);
if(!$req_person || strlen($req_person) == 0 || !$database->personExists($req_person)){
   die("Person does not exist");
}

echo "<h1>Person Info</h1>";

/* Display requested person's information */
$info = $database->getPersonInfo($req_person);

if ($req_user_info){
  echo "null? really?";
}

echo "<table width=\"1000\" cellpadding=\"5\"><tr><td>";

if($session->isAdmin()){
  echo "\n  <tr><td bgcolor=\"FF6666\"><p><b>Database ID: " . $info['id'] . "</b> &nbsp;&nbsp;&nbsp;&nbsp; <a href=\"editperson?person=" . $info['id'] . "\">Edit Person</a></td></tr>";
}

echo "\n  <tr><td bgcolor=\"DDDDFF\"><p><b>Name: " . $info['first'] . " " . $info['middle'] . " " . $info['last'] . "</b><br></td></tr>";
if($info['birth'] == "0000-00-00"){
  echo "\n  <tr><td>Born: Unknown</td></tr>";
} else {
  echo "\n  <tr><td>Born: " . $info['birth'] . "</td></tr>";
}

if($info['death'] == "0000-00-00"){
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Death: Unknown</td></tr>";
} else {
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Death: " . $info['death'] . "</td></tr>";
}

if(!$info['mother']){
  echo "\n  <tr><td>Mother: Unknown</td></tr>";
} else {
  $mother = $database->getPersonInfo($info['mother']);
  echo "\n  <tr><td>Mother: <a href=\"viewperson?person=" . $info['mother'] . "\">" . $mother['first'] . " " . $mother['middle'] . " " . $mother['last'] . "</a></td></tr>";
}

if(!$info['father']){
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Father: Unknown</td></tr>";
} else {
  $father = $database->getPersonInfo($info['father']);
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Father: <a href=\"viewperson?person=" . $info['father'] . "\">" . $father['first'] . " " . $father['middle'] . " " . $father['last'] . "</a></td></tr>";
}

if(!$info['children']){
  echo "\n  <tr><td>Children: None</td></tr>";
} else {
  $kids = split(',', $info['children']);
  $kidstring = "";
  foreach ($kids as $kid){
	$kiditem = $database->getPersonInfo($kid);
	$kidstring .= " <a href=\"viewperson?person=" . $kiditem['id'] . "\">" . $kiditem['first'] . " " . $kiditem['last'] . "</a>";
  }
  echo "\n  <tr><td>Children: " . $kidstring . "</td></tr>";
}

if(!$info['lifestory']){
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Life Story: None</td></tr>";
} else {
  echo "\n  <tr><td bgcolor=\"DDDDFF\">Life Story: " . $info['lifestory'] . "</td></tr>";
}

echo "</table>";

echo "<br><a href=\"" . docRoot . "/comment?id=" . $info['id'] . "&type=person\">Post a Comment</a>";
?>

<p><b>Comments:</b>
<?
$people = $database->getResourceComments($info['id'], 'person');

if($session->isAdmin()){
  $comments = $database->getResourceComments($info['id'], 'person', true);
} else {
  $comments = $database->getResourceComments($info['id'], 'person');
}

if($comments){
  foreach($comments as $comment){
    $color1 = userColor1;
    $color2 = userColor2;
    if($comment['silenced']){
      $color1 = adminColor1;
      $color2 = adminColor2;
    }
    echo "\n<p><table width=\"1000\" cellpadding=\"5\">";
    echo "\n  <tr><td width=100></td><td bgcolor=" . $color1 . ">Subject: <b>" . $comment['subject'] . "</b> ";
    if($session->isAdmin() || $session->username == $comment['username']){
      echo "<a href=\"../deletecomment?id=" . $comment['id'] . "&type=" . $comment['type'] . "\">[Delete Comment]</a> ";
      echo "<a href=\"../comment?id=" . $comment['id'] . "&type=" . $comment['type'] . "&edit=true\">[Edit Comment]</a>";
    }
    echo "</td><td width=100></td></tr>";
    echo "\n  <tr><td width=100></td><td bgcolor=" . $color2 . ">Posted by: " . $comment['username'] . " at " . $comment['timestamp'] . "</td><td width=100></td></tr>";
    echo "\n  <tr><td width=100></td><td bgcolor=" . $color1 . ">Comment:<br>" . $comment['text'] . "</td><td width=100></td></tr>";
    echo "</table>\n";
  }
}
?>

</body>
</html>
