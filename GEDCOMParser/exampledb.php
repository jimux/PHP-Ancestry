<?
include("constants.php");

class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_members;        //Number of signed-up users
   var $purifier;
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   function MySQLDB($purifier){
   	  $this->purifier = $purifier;
   	  
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());

      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;
      
      if(TRACK_VISITORS){
         /* Calculate number of users at site */
         $this->calcNumActiveUsers();
      
         /* Calculate number of guests at site */
         $this->calcNumActiveGuests();
      }
   }
   
   function postToLog($message){
     $q = "INSERT INTO `ancestry`.`log` (`message`, `time`) VALUES ('" . $message . "',NOW())";
     //return mysql_query($q, $this->connection) or die(mysql_error());
   }

   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }
      
      $username = mysql_real_escape_string($username);

      /* Verify that user is in database */
      $q = "SELECT password FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if($password == $dbarray['password']){
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }
   
   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($username, $userid){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }
      
      $username = mysql_real_escape_string($username);

      /* Verify that user is in database */
      $q = "SELECT userid FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve userid from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);

      /* Validate that userid is correct */
      if($userid == $dbarray['userid']){
         return 0; //Success! Username and userid confirmed
      }
      else{
         return 2; //Indicates userid invalid
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $username = mysql_real_escape_string($username);
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $username = mysql_real_escape_string($username);
      $q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email){
      $username = mysql_real_escape_string($username);
      $password = mysql_real_escape_string($password);
      $email = mysql_real_escape_string($email);
      $time = time();
      /* If admin sign up, give admin user level */
      if(strcasecmp($username, ADMIN_NAME) == 0){
         $ulevel = ADMIN_LEVEL;
      }else{
         $ulevel = USER_LEVEL;
      }
      $username = mysql_real_escape_string($username);
      $password = mysql_real_escape_string($password);
      $email = mysql_real_escape_string($email);
      $q = "INSERT INTO ".TBL_USERS." VALUES ('$username', '$password', '0', $ulevel, '$email', $time)";
      return mysql_query($q, $this->connection);
   }
   
   function postEditItem($subject, $body, $postid, $userid){
     $userid = mysql_real_escape_string($userid);
     $q = "UPDATE news SET body = '$body' WHERE id='" . $postid. "'";
     mysql_query($q, $this->connection);
     
     $q = "UPDATE news SET subject = '$subject' WHERE id='" . $postid. "'";
     return mysql_query($q, $this->connection);
   }
   
   function postNewsItem($subject, $body, $userid){
      $time = time();
      $username = mysql_real_escape_string($userid);
      $subject = mysql_real_escape_string($subject);
      $body = mysql_real_escape_string($body);
      $q = "INSERT INTO news (subject, body, userid) VALUES ('$subject', '$body', '$userid')";
      $result = mysql_query($q, $this->connection);
      return 1;
   }
   
   function postNewPerson($subject, $body, $userid){
      $time = time();
      $userid = mysql_real_escape_string($userid);
      $q = "INSERT INTO news (subject, body, userid) VALUES ('$subject', '$body', '$userid')";
      $result = mysql_query($q, $this->connection);
      return 1;
   }
   
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
      $username = mysql_real_escape_string($username);
      $q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
      return mysql_query($q, $this->connection);
   }
   
   /**
    * getLatestNews - Returns the latest news item.
    * parameter: offset, the number to subtract from the 'latest'. So the 2nd to last is when offset is 1.
    */
   function printLatestNews($offset=0){
     global $session;
     // Find the newest post
     $q = "SELECT MAX(id) AS mid FROM news";
     $result = mysql_query($q, $this->connection);
     $resarray = mysql_fetch_array($result);
     $index = intval($resarray['mid'] - $offset);
     
     // Fetch it
     $q = "SELECT * FROM news WHERE id = '". $index ."'";
     $result = mysql_query($q, $this->connection);

     // Error occurred
      if(!$result || (mysql_numrows($result) < 1)){
         return false;
      }

      // Return result array
      $dbarray = mysql_fetch_array($result);
      $dbarray['subject'] = $this->purifier->purify($dbarray['subject']);
      $dbarray['body'] =    $this->purifier->purify($dbarray['body']);
      $dbarray['userid'] =  $this->purifier->purify($dbarray['userid']);
      if(!$dbarray['silenced']){
        echo "<br>";
        echo "\n<table cellpadding=\"5\" width=\"950\">";
        echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"CCCCFF\"><b>Subject: " . $dbarray['subject'] . "</b> ";
        if($session->isAdmin()){
          echo "<a href=\"" . docRoot . "/admin/deletenews.php?id=" . $index . "\">[Delete Post]</a> ";
          echo "<a href=\"" . docRoot . "/admin/editnews.php?item=" . $index . "\">[Edit Post]</a>";
        }
        echo "</td></tr>";
        echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"EEEEFF\">Posted: " . $dbarray['time'] . " - by ". $dbarray['userid'] ."</td></tr>";
        echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"CCCCFF\">Message:<p>" . str_replace(array("\n"), '<br>', $dbarray['body']) . "</td></tr>";
        echo "\n</table>";
      } else {
        return false;
      }
   }
   
   function getUserbyID($userid){
     $userid = mysql_real_escape_string($userid);
     $q = "SELECT * FROM users WHERE userid = '$userid'";
     $result = mysql_query($q, $this->connection);
     
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return "User Not Found";
      }
      
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      $dbarray['username'] = $this->purifier->purify($dbarray['username']);
      return $dbarray["username"];
   }
   
   function searchPersons($fname, $mname, $lname, $keys){
		$query = "SELECT * FROM person WHERE ";
		$empty = $query;
	    $items = Array();
	    
	    $count = 0;
	    foreach($keys as $key){
	    	$items[$count] = "`lifestory` LIKE '%" . mysql_real_escape_string($key) . "%'";
	    	$count += 1;
	    }
		
	 	if($fname !== ""){
	 	    $items["first"] = "first LIKE '" . mysql_real_escape_string($fname) . "'";
		}
		
		if($mname !== ""){
			$items["middle"] = "middle LIKE '" . mysql_real_escape_string($mname) . "'";
		}
		
		if($lname !== ""){
		    $items["last"] = "last LIKE '" . mysql_real_escape_string($lname) . "'";
		}
		
		$lenitems = count($items);
		$count = 0;
		foreach($items as $item){
		  $query = $query . $item;
		  if($lenitems != $count+1){
		  	$query = $query . " && ";
		  }
		  $count += 1;
		}
		
		if($query == "SELECT * FROM person WHERE `lifestory` LIKE '%%'"){
			return 0;
		}
		
		if($query == $empty){
			return 0;
		}
		
		$result = mysql_query($query);
		return $result;
   }
   
   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
      $username = mysql_real_escape_string($username);
      $q = "SELECT * FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      $dbarray['username'] = $this->purifier->purify($dbarray['username']);
      $dbarray['password'] = $this->purifier->purify($dbarray['password']);
      $dbarray['userid'] =   $this->purifier->purify($dbarray['userid']);
      $dbarray['email'] =    $this->purifier->purify($dbarray['email']);
      return $dbarray;
   }
   
   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers(){
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = mysql_query($q, $this->connection);
         $this->num_members = mysql_numrows($result);
      }
      return $this->num_members;
   }
   
   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveUsers(){
      /* Calculate number of users at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_USERS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_users = mysql_numrows($result);
   }
   
   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveGuests(){
      /* Calculate number of guests at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_guests = mysql_numrows($result);
   }
   
   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    */
   function addActiveUser($username, $time){
      $username = mysql_real_escape_string($username);
      $q = "UPDATE ".TBL_USERS." SET timestamp = '$time' WHERE username = '$username'";
      mysql_query($q, $this->connection);
      
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES ('$username', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
   
   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time){
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   
   /* These functions are self explanatory, no need for comments */
   
   /* removeActiveUser */
   function removeActiveUser($username){
      if(!TRACK_VISITORS) return;
      $username = mysql_real_escape_string($username);
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = '$username'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
   
   /* removeActiveGuest */
   function removeActiveGuest($ip){
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   
   /* removeInactiveUsers */
   function removeInactiveUsers(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function personExists($pid){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($pid);
      }
      $q = "SELECT id FROM person WHERE id='$pid'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   function newPerson($first, $middle, $last, $mother, $father, $children,
                      $story, $bM, $bD, $bY, $dM, $dD, $dY, $sex, $id){
                      	
     if($bY == 0 or $bY == ""){
	   $bdate = date("0000-00-00");
	 } else {
	   $bdate = date(strval($bY) . "-" . strval($bM) . "-" . strval($bD));
	 }
	 
	 if($dY == 0 or $dY == ""){
	   $ddate = date("0000-00-00");
	 } else {
	   $ddate = date(strval($dY) . "-" . strval($dM) . "-" . strval($dD));
	 }
	 
     $query = "INSERT INTO person (id,first,middle,last,sex,mother,father,children,lifestory,birth,death) VALUES ('".$id."','".mysql_real_escape_string($first)."','".mysql_real_escape_string($middle)."','".mysql_real_escape_string($last)."','".$sex."','".$mother."','".$father."','".$children."','".$story."','".$bdate."','".$ddate."')";
     echo "<p>" . $query;
     $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());

     if ($retval == true) {
       $q = "SELECT MAX(id) AS mid FROM person";
       $result = mysql_query($q, $this->connection);
       $resarray = mysql_fetch_array($result);
       $index = intval($resarray['mid']);
       return $index;
     } else {
       return false;
     }
   }
   
   function updatePerson($first, $middle, $last, $mother, $father, $children,
                      $story, $bM, $bD, $bY, $dM, $dD, $dY, $sex, $id){
     if(bY == ""){
	   $bdate = date("0000-00-00");
	 } else {
	   $bY = intval($bY);
	   $bdate = date($bY . "-" . $bM . "-" . $bD);
	 }
	 
	 if(dY == ""){
	   $ddate = date("0000-00-00");
	 } else {
	   $dY = intval($dY);
	   $ddate = date($dY . "-" . $dM . "-" . $dD);
	 }
	 
	 // Set the lifestory.
	 $query = "UPDATE person SET lifestory='" . $story . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the birth.
	 $query = "UPDATE person SET birth='" . $bdate . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the death.
	 $query = "UPDATE person SET death='" . $ddate . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the first name.
	 $query = "UPDATE person SET first='" . $first . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the middle name.
	 $query = "UPDATE person SET middle='" . $middle . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the last name.
	 $query = "UPDATE person SET last='" . $last . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the sex.
	 $query = "UPDATE person SET sex='" . $sex . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the mother.
	 $query = "UPDATE person SET mother='" . $mother . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the father.
	 $query = "UPDATE person SET father='" . $father . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());
	 
	 // Set the children.
	 $query = "UPDATE person SET children='" . $children . "' WHERE id='" . $id . "'";
	 $retval = mysql_query($query, $this->connection) or die('Invalid query: ' . mysql_error());

     if ($retval == true) {
       $q = "SELECT MAX(id) AS mid FROM person";
       $result = mysql_query($q, $this->connection);
       $resarray = mysql_fetch_array($result);
       $index = intval($resarray['mid']);
       return $index;
     } else {
       return false;
     }
   }
   
   function getPersonInfo($pid){
     $q = "SELECT * FROM person WHERE id = '$pid'";
     $result = mysql_query($q, $this->connection);
     
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return "User Not Found";
      }
      
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      $dbarray['first'] =     $this->purifier->purify($dbarray['first']);
      $dbarray['middle'] =    $this->purifier->purify($dbarray['middle']);
      $dbarray['last'] =      $this->purifier->purify($dbarray['last']);
      $dbarray['lifestory'] = $this->purifier->purify($dbarray['lifestory']);
      $dbarray['marriages'] = $this->purifier->purify($dbarray['marriages']);
      $dbarray['comments'] =  $this->purifier->purify($dbarray['comments']);
      $dbarray['admins'] =    $this->purifier->purify($dbarray['admins']);
      return $dbarray;
   }
   
   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query){
      return mysql_query($query, $this->connection);
   }
};

$database = new MySQLDB($purifier);

?>
