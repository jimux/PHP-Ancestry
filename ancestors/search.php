<?
include("../include/session.php");
?>

<?
$get = @$_GET['firstname'] ;
$fname = trim($get);

$get = @$_GET['middlename'] ;
$mname = trim($get);

$get = @$_GET['lastname'] ;
$lname = trim($get);

$skeys = @$_GET['storykeys'] ;
$keys = explode(',', trim($skeys));
?>

<? $session->showHeader("Search");
if(!$session->logged_in){
  die("You must be logged in");
}
?>
<p>
<form name="form" action="search.php?" method="get">
  <table bgcolor="EEEEEFF">
    <tr>
      <td colspan=3>Enter your search strings here. No spaces or quotes.<br>Each keyword for story searches should be delinated by a coma.</td>
    </tr><tr>
      <td bgcolor="EEEEFF">First Name: </td><td><input size="30" type="text" name="firstname" value="<? echo $fname ?>"/></td><td></td>
    </tr><tr>
      <td bgcolor="DDDDFF">Middle Name: </td><td><input size="30" type="text" name="middlename"  value="<? echo $mname ?>"/></td><td></td>
    </tr><tr>
      <td bgcolor="EEEEFF">Last Name: </td><td><input size="30" type="text" name="lastname"  value="<? echo $lname ?>"/></td><td></td>
    </tr><tr>
      <td bgcolor="DDDDFF">Story Keywords: </td><td><input size="70" type="text" name="storykeys"  value="<? echo $skeys ?>"/></td>
    </tr><tr>
      <td><input type="submit" name="Submit" value="Search" /></td><td></td>
    </tr>
  </table>
</form>

<p>

<?
$result = $database->searchPersons($fname, $mname, $lname, $keys);
if($result == 0){
	die("<p>No results can be found with given input");
}
$num_rows = mysql_num_rows($result);

if(!$result || ($num_rows < 0) || $num_rows == 0){
  echo "<p>No results can be found with given input";
  return;
}

echo "<table align=\"left\" cellspacing=\"0\" cellpadding=\"3\">\n";
echo "<tr><td width=\"50\"></td><td><h3>Results:</h3></td></tr>\n";
for($i=0; $i<$num_rows; $i++){
   if ( $i&1 ){
   	 $bgcolor = "EFEFFF";
   } else {
   	 $bgcolor = "DEDEFF";
   }
   $id     = mysql_result($result,$i,"id");
   $first  = mysql_result($result,$i,"first");
   $middle = mysql_result($result,$i,"middle");
   $last   = mysql_result($result,$i,"last");
   echo "<tr><td width=\"50\"></td><td bgcolor=\"" . $bgcolor . "\"><a href=\"" . docRoot . "/ancestors/viewperson.php?person=" . $id . "\">" . $first . " " . $middle . " " . $last . "</a></td></tr>\n";
}
echo "</table><br>\n";
?>

</body></html>