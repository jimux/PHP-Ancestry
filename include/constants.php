<?
 
/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
// Address of the database server
define("DB_SERVER", "localhost");
// Username for mysql
define("DB_USER", "dbuser");
// Password for mysql
define("DB_PASS", "dbpass");
// Name of the mysql database you want to use
define("DB_NAME", "ancestry");
// Relative address of your root
define("docRoot", "/~jimmy/phpancestry");
// Absolute address of your root
define("absRoot", "/Users/jimmy/Sites/phpancestry");
// Your website's title
define("siteTitle", "Website Title");

// Define the color scheme for the site
define("userColor1", "DDDDFF");
define("userColor2", "EEEEFF");
define("adminColor1", "FF6666");
define("adminColor2", "FF7777");
define("pageBackground", "FFFFFF");

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_PERSON", "person");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");

define("NEWSCOUNT", 4);

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL",  1);
define("GUEST_LEVEL", 0);

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "YourName");
define("EMAIL_FROM_ADDR", "youremail@address.com");
define("EMAIL_WELCOME", false);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);
?>
