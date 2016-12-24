<? include("../include/session.php"); ?>

<?php
/****************************************/
/*Function:DateSelector v1.1 */
/*Code: PHP 3          */
/*Author: Leon Atkinson <leon@clearink.com> */
/*Creates three form fields for get month/day/year */
/*Input: Prefix to name of field, default date */
/*Output: HTML to define three date fields */
/****************************************/

function DateSelector($inName, $form, $databse, $useDate=0)
{
  global $database;
  $req_person = trim($_GET['person']);
  if(!$database->personExists($req_person)){
    die("Person does not exist");
  }
  $info = $database->getPersonInfo($req_person);
  /* create array so we can name months */
  $monthName = array("01", "02", "03", "04", "05", "06",
                     "07", "08", "09", "10", "11", "12");
  $bdate = explode('-', $info['birth']);
  $byear = $bdate[0];
  $bmonth = $bdate[1];
  $bday = $bdate[2];

  $ddate = explode('-', $info['death']);
  $dyear = $ddate[0];
  $dday = $ddate[1];
  $dmonth = $ddate[2];

  /* if date invalid or not supplied, use current time */
  if($useDate == 0)
  {
    $useDate = Time();
  }

  if($inName == "birth"){
    /* make month selector */
    echo "<SELECT NAME=" . $inName . "Month>\n";
    for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
    {
      echo "<OPTION VALUE=\"";
      echo intval($currentMonth);
      echo "\"";
      if($currentMonth == intval($bmonth))
      {
        echo " SELECTED";
      }
      echo ">" . $currentMonth . "\n";
    }
    echo "</SELECT>";
  } else {
    /* make month selector */
    echo "<SELECT NAME=" . $inName . "Month>\n";
    for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
    {
      echo "<OPTION VALUE=\"";
      echo intval($currentMonth);
      echo "\"";
      if($currentMonth == intval($dmonth))
      {
        echo " SELECTED";
      }
      echo ">" . $currentMonth . "\n";
    }
    echo "</SELECT>";
  }
  
  if($inName == "birth"){
    /* make day selector */
    echo " <SELECT NAME=" . $inName . "Day>\n";
    for($currentDay=1; $currentDay <= 31; $currentDay++)
    {
      echo "<OPTION VALUE=\"$currentDay\"";
      if($currentDay == intval($bday))
      {
        echo " SELECTED";
      }
      echo ">$currentDay\n";
    }
    echo "</SELECT>";
  } else{
    echo " <SELECT NAME=" . $inName . "Day>\n";
    for($currentDay=1; $currentDay <= 31; $currentDay++)
    {
      echo "<OPTION VALUE=\"$currentDay\"";
      if($currentDay == intval($dday))
      {
        echo " SELECTED";
      }
      echo ">$currentDay\n";
    }
    echo "</SELECT>";
  }

  if($inName == "birth"){
  	if($byear == "0000"){
      echo " <input type=\"text\" size=\"4\" name=\"" . $inName . "Year\" maxlength=\"4\" value=\"\">";
    } else {
      echo " <input type=\"text\" size=\"4\" name=\"" . $inName . "Year\" maxlength=\"4\" value=\"" . $byear . "\">";
    }
  } else if($inName == "death"){
    if($dyear == "0000"){
      echo " <input type=\"text\" size=\"4\" name=\"" . $inName . "Year\" maxlength=\"4\" value=\"\">";
    } else {
      echo " <input type=\"text\" size=\"4\" name=\"" . $inName . "Year\" maxlength=\"4\" value=\"" . $dyear . "\">";
    }
  }
}
?> 

<?
$req_person = trim($_GET['person']);
if(!$database->personExists($req_person)){
   die("Person does not exist");
}
$info = $database->getPersonInfo($req_person);
$first = $info['first'];
$middle = $info['middle'];
$last = $info['last'];
if($info['mother']){
  $mother = $info['mother'];
} else { $mother = ""; }
if($info['father']){
  $father = $info['father'];
} else { $father = ""; }
$children = $info['children'];
$story = $info['lifestory'];
$sex = $info['sex'];

$bdate = explode('-', $info['birth']);
$byear = $bdate[0];
$bmonth = $bdate[1];
$bday = $bdate[2];

$ddate = explode('-', $info['death']);
$dyear = $ddate[0];
$dday = $ddate[1];
$dmonth = $ddate[2];
?>

<? $session->showHeader("Edit Person");

if(!$session->logged_in){
  die("You must be logged in");
}
?>

<form action="../process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>Name </td>
    <td> First: <input type="text" size="20" name="first" maxlength="100" value="<? echo $first; ?>"></td>
    <td> Middle: <input type="text" size="20" name="middle" maxlength="100" value="<? echo $middle; ?>"></td>
    <td> Last: <input type="text" size="20" name="last" maxlength="100" value="<? echo $last; ?>"></td>
    <td><? echo $form->error("first"); ?> <? echo $form->error("middle"); ?> <? echo $form->error("last"); ?></td>
  </tr><tr>
    <td colspan="2">Mother: <input type="text" size="10" name="mother" maxlength="100" value="<? echo $mother; ?>"></td>
    <td>Father: <input type="text" size="10" name="father" maxlength="100" value="<? echo $father; ?>"></td>
    <td colspan="2">Children: <input type="text" size="10" name="children" maxlength="100" value="<? echo $children; ?>"></td>
  </tr><tr>
    <td colspan="5">Birth: <? DateSelector("birth", $form, $database); ?> Leave year blank if date is unknown</td>
  </tr><tr>
    <td colspan="5">Death: <? DateSelector("death", $form, $database); ?> Leave year blank if date is unknown</td>
  </tr><tr>
    <td>
      <select name="sex">
<?
if ($info['sex'] == "male"){
  echo "<option value=\"male\" SELECTED>Male</option>";
} else {
  echo "<option value=\"male\">Male</option>";
}
if ($info['sex'] == "female"){
  echo "<option value=\"female\" SELECTED>Female</option>";
} else {
  echo "<option value=\"female\">Female</option>";
}
if ($info['sex'] == "unset"){
  echo "<option value=\"unset\" SELECTED>Unset</option>";
} else {
  echo "<option value=\"unset\">Unset</option>";
}
?>
      </select>
    </td>
  </tr><tr>
    <td colspan="5">Life Story:</td>
  </tr><tr>
    <td colspan="5"><textarea type="text" cols="80" rows="40" name="story" maxlength="56000" value=""><? echo $story; ?></textarea></td>
    <td><? echo $form->error("story"); ?></td>
  </tr><tr>
    <td colspan="5" align="right">

<?
      echo "<input type=\"hidden\" name=\"pid\" value=\"" . trim($_GET['person']) . "\">";
?>
      <input type="hidden" name="subeditperson" value="1">
      <input type="submit" value="Post Person">
    </td>
  </tr><tr>
    <td colspan="5" align="left"><a href="../main.php">Back to Main</a></td>
  </tr><tr>
    <td COLSPAN="5"><? echo $form->error("error"); ?></td>
  </tr>
</table>
</form>