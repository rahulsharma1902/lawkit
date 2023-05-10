<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

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
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/* define('STRIPE_API_KEY',				'sk_test_51HSqIiEp2krUL6QUbZGpWwfGwBl2WC3swHbVnDBeFTPXSWoxTdYCIWWE1U47n765fJcnrcvUf9vlNsmD8EbBWjZ900lGwexbbQ');
define('STRIPE_PUBLISHABLE_KEY',		'pk_test_51HSqIiEp2krUL6QUQEiL7P8CyYLWymeRBP9cZx88qXjoBL4qzV37Mny83wTbAmx4LHTYJJXx5wzKmjVhJhfNecLz00AgNuQuSu'); */

/**
 #Use these stripe key when project is published
// define('STRIPE_API_KEY', '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');
// define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51HSqIiEp2krUL6QU2DfRiR4FeaWDWAQYEEXLYuwb7et6c3xUzfX1jyQ3Pst8BW9CKL5kmf2gYCXVetHjmNCiGzTH00CRKr7FVS');
 */
/** stripe  */
define('STRIPE_API_KEY', 'sk_test_51N5MbaSIRfv8P9JxkvDnezYkFIKcYb7EOPAebqejc6bIiuZrcQOgeZzHFC0g48nPwrZU0Uoq63zvcvUJqIsJNpba00uCtUaggR');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51N5MbaSIRfv8P9JxGuCtqVMrF5jXs2UPsT4TiUjMxnvgrHLv74N6rPwBXEt2RawZKm8nZSNjb8E5JpjW9fiJ3Wqt00m4MB5eWg');
define('PLAN_LAWKIT',      				'plan_I5TQ7CIW6yw1P8');
define('LAWKIT_10',                     'price_1N5NrYSIRfv8P9Jx1zrWZFgx'); // When this project go live change this key with =>> price_1LhdJREp2krUL6QUVFCC0vpp
define('LAWKIT_10_DISCOUNT',            15);


/** tools */
define('API_CALENDARIO', 				'https://calendario.lawkit.mx/api/');
define('API_CB', 						'https://contratos.lawkit.mx/api/');
define('API_LAWKIT', 					'https://lawkit.mx/api/');// production: .lawkit.mx | development: .lawkit.local
define('COOKIE_HOSTNAME',  				'.lawkit.mx');// production: .lawkit.mx | development: .lawkit.local
define('CALCULADORA',					'https://calendario.lawkit.mx');
define('BUSCADOR',						'https://buscador.lawkit.mx');

/** facturama */
define('FACTURAMA_SANDBOX',    'https://apisandbox.facturama.mx');
define('FACTURAMA_PRODUCTION', 'https://api.facturama.mx');
define('FACTURAMA_USER',       'lawkit');
define('FACTURAMA_PASSWORD',   'vKkaPesWDJ5GVT.');