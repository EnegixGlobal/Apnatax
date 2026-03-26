<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

///////////////////////////////////////////////
$startyear = '2023';
$curyear = date('Y');
if ($startyear < $curyear) {
	$curyear = $startyear . '-' . $curyear;
}
defined('PROJECT_NAME')        or define('PROJECT_NAME', 'ApnoTax');
defined('OUR_BRAND')       	   or define('OUR_BRAND', '<b>Developed & Managed </b> by <span class="text-danger"> Tripledots Software Services Pvt. Ltd.</span>');
defined('SESSION_YEAR')        or define('SESSION_YEAR', "$curyear");
defined('SITE_SALT')           or define('SITE_SALT', "Taxefi");
defined('TP')        		   or define('TP', "tf_"); // Table Prefix
defined('PRE')                 or define('PRE', "<pre>");
defined('HEADERTHEME')         or define('HEADERTHEME', "theme1"); // default themelight1
defined('LOGOTHEME')           or define('LOGOTHEME', "theme1"); //default theme6

/*-------------Notification---------------*/
defined('NFROM')               or define('NFROM', "top");
defined('NALIGN')              or define('NALIGN', "center");
defined('NANIMATEIN')          or define('NANIMATEIN', "flipInY");
defined('NANIMATEOUT')         or define('NANIMATEOUT', "flipOutY");

/*-------------Notification---------------*/

defined('REQUEST_LOG')         or define('REQUEST_LOG', TRUE); //REQUEST_LOG
defined('CI_DEBUGGER')         or define('CI_DEBUGGER', TRUE);

/////////////////////////////////////////////

defined('PHONEPE_MERCHANT_ID')    or define('PHONEPE_MERCHANT_ID', 'PGTESTPAYUAT105'); //MERCHANT_ID
defined('PHONEPE_SALT_KEY')       or define('PHONEPE_SALT_KEY', 'c45b52fe-f2c5-4ef6-a6b5-131aa89ed133'); //SALT_KEY
defined('PHONEPE_SALT_INDEX')     or define('PHONEPE_SALT_INDEX', '1'); //SALT_INDEX
defined('PHONEPE_ENV')            or define('PHONEPE_ENV', 'sandbox'); //PHONEPE_ENV

/////////////////////////////////////////////
// Razorpay Configuration
// IMPORTANT: Set these to your actual Razorpay credentials in your local env.
// Do NOT commit real live keys to version control.

// defined('RAZORPAY_KEY_ID')        or define('RAZORPAY_KEY_ID', 'rzp_live_SK31frYH5VlyOw');
// defined('RAZORPAY_KEY_SECRET')    or define('RAZORPAY_KEY_SECRET', '0QF4hDSZfy80XpCJBiZ1mb0d');
defined('RAZORPAY_KEY_ID')        or define('RAZORPAY_KEY_ID', 'rzp_test_WjIc9lHqMXp8Ma');
defined('RAZORPAY_KEY_SECRET')    or define('RAZORPAY_KEY_SECRET', '6Fq6oGGBt3N7s2REGjjw2Vu7');
defined('RAZORPAY_CURRENCY')      or define('RAZORPAY_CURRENCY', 'INR');

/////////////////////////////////////////////
// PayU Configuration
// defined('PAYU_MERCHANT_KEY')      or define('PAYU_MERCHANT_KEY', 'jAWu4T');
// defined('PAYU_SALT')              or define('PAYU_SALT', '2uOdfihzWVTGLoDLTJfaF4ntiVdEYc77');
// defined('PAYU_ENV')               or define('PAYU_ENV', 'production'); // 'test' or 'production'

defined('PAYU_MERCHANT_KEY')      or define('PAYU_MERCHANT_KEY', 'RWJEZb');
defined('PAYU_SALT')              or define('PAYU_SALT', '5zRO1ziP75t2ypUnxDCqihPJZwzpOsba');
defined('PAYU_ENV')               or define('PAYU_ENV', 'test'); // 'test' or 'production'
// PayU API URLs
defined('PAYU_TEST_URL')          or define('PAYU_TEST_URL', 'https://test.payu.in/_payment');
defined('PAYU_PRODUCTION_URL')   or define('PAYU_PRODUCTION_URL', 'https://secure.payu.in/_payment');
defined('PAYU_TEST_STATUS_URL')  or define('PAYU_TEST_STATUS_URL', 'https://test.payu.in/merchant/postservice?form=2');
defined('PAYU_PRODUCTION_STATUS_URL') or define('PAYU_PRODUCTION_STATUS_URL', 'https://info.payu.in/merchant/postservice?form=2');

/////////////////////////////////////////////

if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') {
	defined('DB_HOST')		? null : define('DB_HOST', 'localhost');
	defined('DB_USER')		? null : define('DB_USER', 'root');
	defined('DB_PASSWORD')	? null : define('DB_PASS', '');
	defined('DB_NAME')		? null : define('DB_NAME', 'db_taxefi');
} else {
	defined('DB_HOST')      ? null : define('DB_HOST', '127.0.0.1');
	defined('DB_USER')      ? null : define('DB_USER', 'u711511560_user_taxefi');
	defined('DB_PASSWORD')  ? null : define('DB_PASS', 'Taxefi@123#$');
	defined('DB_NAME')      ? null : define('DB_NAME', 'u711511560_db_taxefi');
}
