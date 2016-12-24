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

function DateSelector($inName, $form, $useDate=0)
{
  /* create array so we can name months */
  $monthName = array("01", "02", "03", "04", "05", "06",
                     "07", "08", "09", "10", "11", "12");

  /* if date invalid or not supplied, use current time */
  if($useDate == 0)
  {
    $useDate = Time();
  }

  /* make month selector */
  echo "<SELECT NAME=" . $inName . "Month>\n";
  for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
  {
    echo "<OPTION VALUE=\"";
    echo intval($currentMonth);
    echo "\"";
    if(intval(date( "m", $useDate))==$currentMonth)
    {
      echo " SELECTED";
    }
    echo ">" . $monthName[$currentMonth] . "\n";
  }
  echo "</SELECT>";

  /* make day selector */
  echo " <SELECT NAME=" . $inName . "Day>\n";
  for($currentDay=1; $currentDay <= 31; $currentDay++)
  {
    echo "<OPTION VALUE=\"$currentDay\"";
    if(intval(date( "d", $useDate))==$currentDay)
    {
      echo " SELECTED";
    }
    echo ">$currentDay\n";
  }
  echo "</SELECT>";

  echo " <input type=\"text\" size=\"4\" name=\"" . $inName . "Year\" maxlength=\"4\" value=\"\">";
}
?> 

<? $session->showHeader("New Person");
if(!$session->logged_in){
  die("You must be logged in");
}
?>

<form action="../process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>Name </td>
    <td> First: <input type="text" size="20" name="first" maxlength="100" value="<? echo $form->value("first"); ?>"></td>
    <td> Middle: <input type="text" size="20" name="middle" maxlength="100" value="<? echo $form->value("middle"); ?>"></td>
    <td> Last: <input type="text" size="20" name="last" maxlength="100" value="<? echo $form->value("last"); ?>"></td>
    <td><? echo $form->error("first"); ?> <? echo $form->error("middle"); ?> <? echo $form->error("last"); ?></td>
  </tr><tr>
    <td colspan="2">Mother: <input type="text" size="10" name="mother" maxlength="100" value="<? echo $form->value("mother"); ?>"></td>
    <td>Father: <input type="text" size="10" name="father" maxlength="100" value="<? echo $form->value("father"); ?>"></td>
    <td colspan="2">Children: <input type="text" size="10" name="children" maxlength="100" value="<? echo $form->value("children"); ?>"></td>
  </tr><tr>
    <td colspan="5">Birth: <? DateSelector("birth", $form); ?> Leave year blank if date is unknown</td>
  </tr><tr>
    <td colspan="5">Death: <? DateSelector("death", $form); ?> Leave year blank if date is unknown</td>
  </tr><tr>
    <td>
      <select name="sex">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="unset">Unknown</option>
      </select>
    </td>
  </tr><tr>
    <td colspan="5">Life Story:</td>
  </tr><tr>
    <td colspan="5"><textarea type="text" cols="80" rows="40" name="story" maxlength="56000" value="<? echo $form->value("story"); ?>">Enter their story here....</textarea></td>
    <td><? echo $form->error("story"); ?></td>
  </tr><tr>
    <td colspan="5" align="right">
      <input type="hidden" name="subperson" value="1">
      <input type="submit" value="Post Person">
    </td>
  </tr><tr>
    <td colspan="5" align="left"><a href="../main.php">Back to Main</a></td>
  </tr><tr>
    <td COLSPAN="5"><? echo $form->error("error"); ?></td>
  </tr>
</table>
</form>