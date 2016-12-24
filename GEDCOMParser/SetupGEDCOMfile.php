<?php

// Why yes, this file is a complete mess. Thank you very much.

include("exampledb.php");

set_time_limit(1200);

error_reporting(E_ALL);

$people = Array();

function matchMonth($month){
  if($month == "JAN"){
    return "01";
  } else if($month == "FEB"){
    return "02";
  } else if($month == "MAR"){
    return "03";
  } else if($month == "APR"){
    return "04";
  } else if($month == "MAY"){
    return "05";
  } else if($month == "JUN"){
    return "06";
  } else if($month == "JUL"){
    return "07";
  } else if($month == "AUG"){
    return "08";
  } else if($month == "SEP"){
    return "09";
  } else if($month == "OCT"){
    return "10";
  } else if($month == "NOV"){
    return "11";
  } else if($month == "DEC"){
    return "12";
  }
}

// for windows users
//ini_set('include_path', '.;'.$_SERVER['DOCUMENT_ROOT'].'/pear');

//require_once 'Benchmark/Timer.php';
require_once 'Genealogy/Gedcom.php';

//$timer =& new Benchmark_Timer();
//$timer->setMarker('Start');
$ged =& new Genealogy_Gedcom('test.ged');
//$timer->stop();
//$timer->display();

echo 'getNumberOfIndividuals :'.$ged->getNumberOfIndividuals();
echo '<br>';
echo 'getNumberOfFamilies :'.$ged->getNumberOfFamilies();
echo '<br>';
echo 'getNumberOfObjects :'.$ged->getNumberOfObjects();
echo '<br>';
echo 'Last Update :'.$ged->getLastUpdate();
echo '<br>';

echo '<pre>';

$families = $ged->GedcomFamiliesTreeObjects;


/**
 * Build the people array.
 */

echo "<p>number of people: " . count($ged->GedcomIndividualsTreeObjects);
 
for($i=0 ; $i < count($ged->GedcomIndividualsTreeObjects) ; $i++){
  $fandm = explode(' ', $ged->GedcomIndividualsTreeObjects[$i]->getFirstname());
  
  $middlename = "";
  $firstname = $fandm[0];
  if(count($fandm) > 1){
    $middlename = $fandm[1];
  } else {
    $middlename = "";
  }
  
  $birthdate = explode(' ', $ged->GedcomIndividualsTreeObjects[$i]->getBirthDate());
  $birthPlace = $ged->GedcomIndividualsTreeObjects[$i]->getBirthPlace();
  $birthSource = $ged->GedcomIndividualsTreeObjects[$i]->getBirthSource();
  $birthNote = $ged->GetNote(intval(str_replace('H', '', str_replace('@', '', $ged->GedcomIndividualsTreeObjects[$i]->getBirthNote()))));
  if($birthNote == "After immigration, he moved to Clyde, Ohio"){
    $birthNote = "";
  }
  
  $deathdate = explode(' ', $ged->GedcomIndividualsTreeObjects[$i]->getDeathDate());
  $deathPlace = $ged->GedcomIndividualsTreeObjects[$i]->getDeathPlace();
  $deathSource = $ged->GedcomIndividualsTreeObjects[$i]->getDeathSource();
  $deathNote = $ged->GetNote(intval(str_replace('H', '', str_replace('@', '', $ged->GedcomIndividualsTreeObjects[$i]->getDeathNote()))));
  if($deathNote == "After immigration, he moved to Clyde, Ohio"){
    $deathNote = "";
  }
  
  $fcDate = $ged->GedcomIndividualsTreeObjects[$i]->getFCDate();
  $fcPlace = $ged->GedcomIndividualsTreeObjects[$i]->getFCPlace();
  $fcSource = $ged->GedcomIndividualsTreeObjects[$i]->getFCSource();
  $fcNote = $ged->GetNote(intval(str_replace('H', '', str_replace('@', '', $ged->GedcomIndividualsTreeObjects[$i]->getFCNote()))));
  if($fcNote == "After immigration, he moved to Clyde, Ohio"){
    $fcNote = "";
  }
  
  $occupation = str_replace('1 OCCU ', '', $ged->GedcomIndividualsTreeObjects[$i]->getOccupation());
  $source = $ged->GedcomIndividualsTreeObjects[$i]->getSource();
  $object = $ged->GedcomIndividualsTreeObjects[$i]->getObject();
  $famSpouse = $ged->GedcomIndividualsTreeObjects[$i]->getFamSpouse();
  $famChild = $ged->GedcomIndividualsTreeObjects[$i]->getFamChild();
  $nation = $ged->GedcomIndividualsTreeObjects[$i]->getNationality();
  $note = $ged->GetNote(intval(str_replace('H', '', str_replace('@', '', $ged->GedcomIndividualsTreeObjects[$i]->getNote()))));
  if($note == "After immigration, he moved to Clyde, Ohio"){
    $note = "";
  }
  $sex = $ged->GedcomIndividualsTreeObjects[$i]->getSex() or "";

  $title = $ged->GedcomIndividualsTreeObjects[$i]->getTitle();
  $lastname = $ged->GedcomIndividualsTreeObjects[$i]->getLastname();
  $id = intval(str_replace('I', '', $ged->GedcomIndividualsTreeObjects[$i]->getIdent()));
  $marriages = Array();
  $children = Array();
  $mother = 0;
  $father = 0;

"check name sanity \"widow of blah blah\" etc, and last names with parenthases.";

  echo "<br>ID: " . $id;
  echo "<br>Title: " . $title;
  echo "<br>Name: " . $firstname . " " . $middlename . " " . $lastname;
  
  foreach($occupation as $oc){
    echo "<br>Occupation: " . $oc;
  }
  
  echo "<br>Source: " . $source;
  echo "<br>Object: " . $object;
  
  foreach($famSpouse as $famId){
    $marriages[] = intval(str_replace('F', '', $famId));
  	echo "<br>Family: " . intval(str_replace('F', '', $famId));
  }
  
  
  foreach($marriages as $marriage){
    foreach($families as $family){
 	  if($marriage !== 0){
  	    if( $marriage == intval(str_replace('F', '', $family->Identifier))){
          
 	      foreach( $family->Child as $child ){
  	        $children[] = intval(str_replace('I', '', $child));
  	        echo "<br>Child: " . intval(str_replace('I', '', $child));
  		  }
  		}
 	  }
    }
  }
  
  foreach($families as $family){
    if( $famChild == $family->Identifier){
      $mother = intval(str_replace('I', '', $family->Wife));
      $father = intval(str_replace('I', '', $family->Husband));
      echo "<br>Mother: " . intval(str_replace('F', '', $mother));
      echo "<br>Father: " . intval(str_replace('F', '', $father));
    }
  }
  
  echo "<br>Nationality: " . $nation;
  echo "<br>Note: " . $note;
  echo "<br>Sex: " . $sex;
  echo "<br>Birth place: " . $birthPlace;
  echo "<br>Birth source: " . $birthSource;
  echo "<br>Birth note: " . $birthNote;
  
  $bD = "00";
  $bM = "00";
  $bY = "0000";
  
  $dD = "00";
  $dM = "00";
  $dY = "0000";

  if(count($birthdate) > 0 ){
    if($birthdate[0] == "ABT"){
    } else if($birthdate[0] == 'JAN' || $birthdate[0] == 'FEB' || $birthdate[0] == 'MAR' || $birthdate[0] == 'APR' || $birthdate[0] == 'MAY' || $birthdate[0] == 'JUN' || $birthdate[0] == 'JUL' || $birthdate[0] == 'AUG' || $birthdate[0] == 'SEP' || $birthdate[0] == 'OCT' || $birthdate[0] == 'NOV' || $birthdate[0] == 'DEC'){
      $bM = matchMonth($birthdate[0]);
    } else if(intval($birthdate[0]) < 32 && $birthdate[0] !== ""){
      $bD = $birthdate[0];
    } else if(intval($birthdate[0]) > 32){
      $bY = $birthdate[0];
    }
  } if(count($birthdate) > 1 ){
    if($birthdate[1] == "ABT"){
    } else if($birthdate[1] == 'JAN' || $birthdate[1] == 'FEB' || $birthdate[1] == 'MAR' || $birthdate[1] == 'APR' || $birthdate[1] == 'MAY' || $birthdate[1] == 'JUN' || $birthdate[1] == 'JUL' || $birthdate[1] == 'AUG' || $birthdate[1] == 'SEP' || $birthdate[1] == 'OCT' || $birthdate[1] == 'NOV' || $birthdate[1] == 'DEC'){
      $bM = matchMonth($birthdate[1]);
    } else if(intval($birthdate[1]) < 32 && $birthdate[1] !== ""){
	  $bD = $birthdate[1];
    } else if(intval($birthdate[1]) > 32){
      $bY = $birthdate[1];
    }
  } if(count($birthdate) > 2 ){
    if($birthdate[1] == "ABT"){
    } else if($birthdate[2] == 'JAN' || $birthdate[2] == 'FEB' || $birthdate[2] == 'MAR' || $birthdate[2] == 'APR' || $birthdate[2] == 'MAY' || $birthdate[2] == 'JUN' || $birthdate[2] == 'JUL' || $birthdate[2] == 'AUG' || $birthdate[2] == 'SEP' || $birthdate[2] == 'OCT' || $birthdate[2] == 'NOV' || $birthdate[2] == 'DEC'){
      $bM = matchMonth($birthdate[2]);
    } else if(intval($birthdate[2]) < 32 && $birthdate[2] !== ""){
      $bD = $birthdate[2];
    } else if(intval($birthdate[2]) > 32){
      $bY = $birthdate[2];
    }
  }
  
  $birthdate = $bY . "-" . $bM . "-" . $bD;
  echo "<br>Birth Date: " . $birthdate;
  
  echo "<br>Death place: " . $deathPlace;
  echo "<br>Death source: " . $deathSource;
  echo "<br>Death note: " . $deathNote;
  
  if(count($deathdate) > 0 ){
    if($deathdate[0] == "ABT"){
    } else if($deathdate[0] == 'JAN' || $deathdate[0] == 'FEB' || $deathdate[0] == 'MAR' || $deathdate[0] == 'APR' || $deathdate[0] == 'MAY' || $deathdate[0] == 'JUN' || $deathdate[0] == 'JUL' || $deathdate[0] == 'AUG' || $deathdate[0] == 'SEP' || $deathdate[0] == 'OCT' || $deathdate[0] == 'NOV' || $deathdate[0] == 'DEC'){
      $dM = matchMonth($deathdate[0]);
    } else if(intval($deathdate[0]) < 32 && $deathdate[0] !== ""){
      $dD = $deathdate[0];
    } else if(intval($deathdate[0]) > 32){
      $dY = $deathdate[0];
    }
  } if(count($deathdate) > 1 ){
    if($deathdate[1] == "ABT"){
    } else if($deathdate[1] == 'JAN' || $deathdate[0] == 'FEB' || $deathdate[1] == 'MAR' || $deathdate[1] == 'APR' || $deathdate[1] == 'MAY' || $deathdate[1] == 'JUN' || $deathdate[1] == 'JUL' || $deathdate[1] == 'AUG' || $deathdate[1] == 'SEP' || $deathdate[1] == 'OCT' || $deathdate[1] == 'NOV' || $deathdate[1] == 'DEC'){
      $dM = matchMonth($deathdate[1]);
    } else if(intval($deathdate[1]) < 32 && $deathdate[0] !== ""){
      $dD = $deathdate[1];
    } else if(intval($deathdate[1]) > 32){
      $dY = $deathdate[1];
    }
  } if(count($deathdate) > 2 ){
    if($deathdate[1] == "ABT"){
    } else if($deathdate[2] == 'JAN' || $deathdate[0] == 'FEB' || $deathdate[2] == 'MAR' || $deathdate[2] == 'APR' || $deathdate[2] == 'MAY' || $deathdate[2] == 'JUN' || $deathdate[2] == 'JUL' || $deathdate[2] == 'AUG' || $deathdate[2] == 'SEP' || $deathdate[2] == 'OCT' || $deathdate[2] == 'NOV' || $deathdate[2] == 'DEC'){
      $dM = matchMonth($deathdate[2]);
    } else if(intval($deathdate[2]) < 32 && $deathdate[0] !== ""){
      $dD = $deathdate[2];
    } else if(intval($deathdate[2]) > 32){
      $dY = $deathdate[2];
    }
  }
  
  if($bY == $dY && $dM == $bM && $bD == $dD){
    $dD = "00";
    $dM = "00";
    $dY = "0000";
    $deathPlace = "";
    $deathSource = "";
    $deathNote = "";
  }
  
  $deathdate = $dY . "-" . $dM . "-" . $dD;
  echo "<br>Death Date: " . $deathdate;
  
  $marryString = "";
  $j = 0;
  foreach($marriages as $marriage){
    if($j == 0){
      $marryString = $marriage;
    } else {
      $marryString = $marryString . "," . $marriage;
    }
    $j += 1;
  }
  $childString = "";
  $j = 0;
  foreach($children as $child){
    if($j == 0){
      $childString = $child;
    } else {
      $childString = $childString . "," . $child;
    }
    $j += 1;
  }
  
  echo "<br>Child string: " . $childString;
  echo "<br>Marriage string: " . $marryString;
  
  if(!$sex){
    $sex = "unset";
  } else if($sex == "M"){
    $sex = "male";
  } else if($sex == "F"){
    $sex = "female";
  }
  
  
  $story = "";
  
  if($birthPlace || $birthSource || $birthNote ){
    $story = $story . "<p>Birth:<br>" . "Place: " . mysql_real_escape_string($birthPlace) . "<br>Source: " . mysql_real_escape_string($birthSource) . "<br>Note: " . mysql_real_escape_string($birthNote);
  }
  
  if($deathPlace || $deathSource || $deathNote ){
    $story = $story . "<p>Death:<br>" . "Place: " . mysql_real_escape_string($deathPlace) . "<br>Source: " . mysql_real_escape_string($deathSource) . "<br>Note: " . mysql_real_escape_string($deathNote);
  }
  
  foreach($occupation as $occu){
    if($occu !== ""){
      $story = $story . "<p>Occupation: " . mysql_real_escape_string($occu);
    }
  }
  
  if($note){
    $story = $story. "<p>Life story: " . mysql_real_escape_string($note);
  }
  
  echo "<p>Story: " . $story;

  $people[$id] = Array('id' => $id,
                       'first' => $firstname,
                       'middle' => $middlename,
                       'last' => $lastname,
                       'sex' => $sex,
                       'mother' => $mother,
                       'father' => $father,
                       'children' => $childString,
                       'marriages' => $marryString,
                       'bD' => $bD,
                       'bM' => $bM,
                       'bY' => $bY,
                       'dD' => $dD,
                       'dM' => $dM,
                       'dY' => $dY,
                       'story' => $story);
}


foreach($people as $person){
  print_r($person);
  $database->newPerson($person['first'],
                       $person['middle'],
                       $person['last'],
                       $person['mother'],
                       $person['father'],
                       $person['children'],
                       $person['story'],
                       $person['bM'],
                       $person['bD'],
                       $person['bY'],
                       $person['dM'],
                       $person['dD'],
                       $person['dY'],
                       $person['sex'],
                       $person['id']);
}

print_r($people);

//print_r($ged->GedcomFamiliesTreeObjects[0]);
//print_r($ged->GedcomObjectsTreeObjects);
//print_r($ged->GedcomHeaderTreeObject);
//print_r($ged->getIndividual('I1'));
//print_r($ged->getFamily('F1'));
//print_r($ged->getObject('O1'));
echo '</pre>';
?>
