<?php
/**
 * @title Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry SORIA <pierrehenrysoria@gmail.com>
 * @copyright        (c) 2012, Pierre-Henry Soria. All Rights Reserved.
 * @license          Lesser General Public License (LGPL) (http://www.gnu.org/copyleft/lesser.html)
 * @version          2.0
 */

/**
 * Gets list of name of directories inside a directory.
 *
 * @param string $sDir
 * @return array
 */
function get_dir_list($sDir) {
    $aDirList = array();

    if($rHandle = opendir($sDir)) {
        while(false !== ($sFile = readdir($rHandle))) {
            if($sFile != '.' && $sFile != '..' && is_dir($sDir . '/' . $sFile))
                $aDirList[] = $sFile;
        }
        closedir($rHandle);
        asort($aDirList);
        reset($aDirList);
    }
    return $aDirList;
}

/**
 * Check valid directory.
 *
 * @param string $sDir
 * @return boolean
 */
function is_directory($sDir) {
    $sPathProtected = check_ext_start(check_ext_end(trim($sDir)));
    if(is_dir($sPathProtected)) {
        if(is_writable($sPathProtected)) {
            return true;
        }
    }

    return false;
}

/**
 * Check start extension.
 *
 * @param string $sDir
 * @return string The good extension.
 */
function check_ext_start ($sDir) {
    if(substr($sDir, 0, 1) != '/')
        return '/' . $sDir;
    return $sDir;
}

/**
 * Check end extension.
 *
 * @param string $sDir
 * @return string The good extension.
 */
function check_ext_end($sDir) {
    if(substr($sDir, -1) != '/')
        return $sDir  . '/';
    return $sDir;
}

/**
 * Validate username.
 *
 * @param string $sUsername
 * @param integer $iMin Default 4
 * @param integer $iMax Default 40
 * @return string (ok, empty, tooshort, toolong, badusername).
 */
function validate_username($sUsername, $iMin = 4, $iMax = 40) {
    if(empty($sUsername)) return 'empty';
    elseif(strlen($sUsername) < $iMin) return 'tooshort';
    elseif(strlen($sUsername) > $iMax) return 'toolong';
    elseif(preg_match('/[^\w]+$/', $sUsername)) return 'badusername';
    else return 'ok';
}

/**
 * Validate password.
 *
 * @param string $sPassword
 * @param integer $iMin 6
 * @param integer $iMax 92
 * @return string (ok, empty, tooshort, toolong, nonumber, noupper).
 */
function validate_password($sPassword, $iMin = 6, $iMax = 92) {
    if(empty($sPassword)) return 'empty';
    else if(strlen($sPassword) < $iMin) return 'tooshort';
    else if(strlen($sPassword) > $iMax) return 'toolong';
    else if(!preg_match('#[0-9]{1,}#', $sPassword)) return 'nonumber';
    else if(!preg_match('#[A-Z]{1,}#', $sPassword)) return 'noupper';
    else return 'ok';
}

/**
 * Validate email.
 *
 * @param string $sEmail
 * @return string (ok, empty, bademail).
 */
function validate_email($sEmail) {
    if($sEmail == '') return 'empty';
    if(filter_var($sEmail, FILTER_VALIDATE_EMAIL)== false) return 'bademail';
    else return 'ok';
}

/**
 * Validate name (first name and last name).
 *
 * @param string $sName
 * @param integer $iMin Default 2
 * @param integer $iMax Default 30
 * @return boolean
 */
function validate_name($sName, $iMin = 2, $iMax = 30) {
    if(is_string($sName) && strlen($sName) >= $iMin && strlen($sName) <= $iMax)
        return true;
    return false;
}

/**
 * Check that all fields are filled.
 *
 * @param array $aVars
 * @return boolean
 */
function filled_out($aVars) {
    foreach($aVars as $sKey => $sValue) {
        if((!isset($sKey)) || ($sValue == '')) {
            return false;
        }
        return true;
    }
    return false; // Default value
}

/**
 * Check a string identical.
 *
 * @param string $sVal1
 * @param string $sVal2
 * @return boolean
 */
function validate_identical($sVal1, $sVal2) {
    return ($sVal1 === $sVal2);
}

/**
 * Redirect to another URL.
 *
 * @param string $sUrl
 * @param boolean $bPermanent Default TRUE
 * @return void
 */
function redirect($sUrl, $bPermanent = true) {
    if($bPermanent)
        header('HTTP/1.1 301 Moved Permanently');

    header('Location: ' . $sUrl);
    exit;
}

/**
 * Delete directory.
 *
 * @param string $sPath
 * @return boolean
 */
function delete_dir($sPath) {
    return is_file($sPath) ?
    @unlink($sPath) :
    is_dir($sPath) ?
    array_map('delete_dir',glob($sPath.'/*')) === @rmdir($sPath) :
    false;
}

/**
 * Executes SQL queries.
 *
 * @param object PDO
 * @param string $sSqlFile SQL File.
 * @param string $sOldPrefix Default NULL
 * @param string $sNewPrefix Default NULL
 * @return boolean
 */
function exec_file_query($oDb, $sSqlFile, $sOldPrefix = null, $sNewPrefix = null) {
    if(!is_file($sSqlFile)) return false;

    $sSqlContent = file_get_contents($sSqlFile);
    $sSqlContent = str_replace($sOldPrefi, $sNewPrefix, $sSqlContent);
    $rStmt = $oDb->exec($sSqlContent);
    unset($sSqlContent);

    return ($rStmt === false) ? $rStmt->errorInfo() : true;
}

/**
 * Generate Hash.
 *
 * @param integer $iLength Default 80
 * @return string The random hash. Maximum 128 characters with whirlpool encryption.
 */
function generate_hash($iLength = 80) {
    return substr(hash('whirlpool', time() . hash('sha512', client_ip() . uniqid(mt_rand(), true) . microtime(true)*999999999999)), 0, $iLength);
}

/**
 * Check the URL rewrite file (.htaccess).
 *
 * @param string $sDir
 * @param string $sFile Default .htaccess
 * @return boolean
 */
function is_url_rewrite($sDir, $sFile = '.htaccess') {
    return is_file($sDir . $sFile);
}

/**
 * Check if the OS is Windows.
 *
 * @return boolean
 */
function is_windows() {
    return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

/**
 * Get the current URL.
 *
 * @return string URL.
 */
function current_url() {
    // URL association for SSL and protocol compatibility
    $sHttp = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';

    // Determines the domain name with the port
    $sDomain = ($_SERVER['SERVER_PORT'] != '80') ?  $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];

    return $sHttp . $sDomain . $_SERVER['REQUEST_URI'];
}

/**
 * Get the client IP address.
 *
 * @return string
 */
function client_ip() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $sIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $sIp = $_SERVER['HTTP_CLIENT_IP'];
    }
    else
    {
        $sIp = $_SERVER['REMOTE_ADDR'];
    }

    return preg_match('/^[a-z0-9:.]{7,}$/', $sIp) ? $sIp : '0.0.0.0';
}

/**
 * PHP 6 was to give birth to this function, but the development PHP team to decline this feature :-(, so we create this.
 *
 * @param string $sVar a variable (e.g. $_GET['foo'])
 * @param string $sOr a message if $sVar is empty (optional)
 * @return string $sVar or $sOr
 */
function ifsetor($sVar, $sOr = '') {
    return (isset($sVar)) ? $sVar : $sOr;
}

/**
 * Gets file contents with CURL.
 *
 * @param string $sFile
 * @return string
 */
function get_file_contents($sFile) {
    $rCh = curl_init();
    curl_setopt($rCh, CURLOPT_URL, $sFile);
    curl_setopt($rCh, CURLOPT_HEADER, 0);
    curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
    $sResult = curl_exec($rCh);
    curl_close($rCh);
    unset($rCh);

    return $sResult;
}

/**
 * Display a page if the file exists, otherwise displays a 404.
 *
 * @param string $sPage The page.
 * @return void
 */
function get_page($sPage) {
    if(is_file($sPage)) {
        $sPage = file_get_contents($sPage);
        echo parse_var($sPage);
    } else {
        // Page Not Found!
        error_404();
    }
}

/**
 * Sets an error 404 page with HTTP 404 code status.
 *
 * @param string $sPathPage You can specify the path to a custom error page, otherwise displays a simple error page. Default NULL
 * @return void
 */
function error_404($sPathPage = null) {
    header('HTTP/1.1 404 Not Found');

    if(!empty($sPathPage) && is_file($sPathPage))
        $sHtmlPage = get_page($sPathPage);
    else
        $sHtmlPage = <<<HTML
        <!DOCTYPE html>
        <html>
          <head>
            <title>Page Not Found</title>
          </head>
          <body>
            <h1>Page Not Found!</h1>
            <p>Whoops! The page you requested couldn't be found.</p>
          </body>
        </html>
HTML;

    echo $sHtmlPage;
}

/**
 * Required HTTP Authentification.
 *
 * @param string $sUsr
 * @param string $sPwd
 * @return boolean TRUE if the authentication is correct, otherwise FALSE.
 */
function require_auth($sUsr, $sPwd) {
    $sAuthUsr = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
    $sAuthPwd = !empty($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

    if(!($sAuthUsr == $sUsr && $sAuthPwd == $sPwd))
    {
        header('WWW-Authenticate: Basic realm="HTTP Basic Authentication"');
        header('HTTP/1.1 401 Unauthorized');
        echo tr('You must enter a valid login ID and password to access this resource.') . "\n";
        exit(false);
    }
    else
        return true;
}

/**
 * Get the Gravatar URL.
 *
 * @param string $sEmail The user email address.
 * @param string $sType The default image type to show. Default wavatar
 * @param integer $iSize  The size of the image. Default 80
 * @param string $sRating The max image rating allowed. Default G (for all)
 * @return string The Link Avatar.
 */
function gravatar_url($sEmail, $sType = 'wavatar', $iSize = 80, $sRating = 'g') {
    return 'http://www.gravatar.com/avatar/' . md5( strtolower($sEmail) ) . '?d=' . $sType . '&amp;s=' . $iSize . '&amp;r=' . $sRating;
}

/**
 * Extract Zip archive.
 *
 * @param string $sFile
 * @param string $sDir
 * @return boolean
 */
function zip_extract($sFile, $sDir) {
    $oZip = new \ZipArchive;

    $mRes = $oZip->open($sFile);

    if($mRes === true)
    {
        $oZip->extractTo($sDir);
        $oZip->close();
        return true;
    }

    return false; // Return error value
}

/**
 * Import a file.
 *
 * @param string $sFile The path of file.
 * @return string The resource.
 * @throws \Exception If the file does not exist.
 */
function import($sFile) {
    if(!@require_once($sFile))
        throw new \Exception(sprintf('The "%s" file is not found!', escape($sFile)));
}

/**
 * Check valid URL.
 *
 * @return string $sUrl
 * @return boolean
 */
function check_url($sUrl) {
    // Checks if URL is valid with HTTP status code '200 OK' or '301 Moved Permanently'
    $aUrl = @get_headers($sUrl);
    return (strpos($aUrl[0], '200 OK') || strpos($aUrl[0], '301 Moved Permanently'));
}

/**
 * Gets Browser User Language.
 * @return string The first two lowercase letter of the browser language.
 */
function get_browser_lang() {
    $aLang = explode(',' ,@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    return htmlspecialchars(strtolower(substr(chop($aLang[0]), 0, 2)));
}

/**
 * Escape function, uses the PHP native htmlspecialchars but improves.
 *
 * @param string $sText
 * @param boolean $bStrip If true, the text will be passed through the strip_tags function PHP
 * @return string text to HTML entities
 */
function escape($sText, $bStrip = false) {
    return ($bStrip) ? strip_tags($sText) : htmlspecialchars($sText, ENT_QUOTES);
}

/**
 * Translate Language helper function.
 *
 * @param string $sVar [, string $... ]
 * @return string Returns the text with gettext function.
 */
function tr() {
    $sToken = func_get_arg(0);

    for($i = 1; $i < func_num_args(); $i++)
        $sToken = str_replace('%'. ($i-1) . '%', func_get_arg($i), $sToken);

    return gettext($sToken);
}

/**
 * Plurial version of tr() function.
 *
 * @param string $sMsg1
 * @param string $sMsg2
 * @param integer $iNumber
 * @return string Returns the text with ngettext function the correct plural form of message identified by msgid1 and msgid2 for count n.
 */
function nt($sMsg1, $sMsg2, $iNumber) {
    $sMsg1 = str_replace('%n%', $iNumber, $sMsg1);
    $sMsg2 = str_replace('%n%', $iNumber, $sMsg2);
    return ngettext($sMsg1, $sMsg2, $iNumber);
}

/**
 * Data URI Function base64.
 *
 * @param string $sFile
 * @return string Returns format: data:[<MIME-type>][;base64],<data>
 */
function base64_data_uri($sFile) {
     // Switch to right MIME-type
     $sExt = strtolower(substr(strrchr($sFile, '.'), 1));

     switch($sExt)
     {
        case 'gif':
        case 'jpg':
        case 'png':
            $sMimeType = 'image/'. $sExt;
        break;

        case 'ico':
            $sMimeType = 'image/x-icon';
        break;

        case 'eot':
            $sMimeType = 'application/vnd.ms-fontobject';
        break;

        case 'otf':
        case 'ttf':
        case 'woff':
            $sMimeType = 'application/octet-stream';
        break;

        default:
            exit('The file format is not supported!');
    }

    $sBase64 = base64_encode(file_get_contents($sFile));
    return "data:$sMimeType;base64,$sBase64";
}

/**
 * Send an email (text and HTML format).
 *
 * @param array $aParams The parameters information to send email.
 * @return boolean Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
 */
function send_mail($aParams) {
    // Frontier to separate the text part and the HTML part.
    $sFrontier = "-----=" . md5(mt_rand());

    // Removing any HTML tags to get a text format.
    // If any of our lines are larger than 70 characterse, we return to the new line.
    $sTextBody =  wordwrap(strip_tags($aParams['body']), 70);

    // HTML format (you can change the layout below).
    $sHtmlBody = <<<EOF
<html>
  <head>
    <title>{$aParams['subject']}</title>
  </head>
  <body>
    <div style="text-align:center">{$aParams['body']}</div>
  </body>
</html>
EOF;

    // If the email sender is empty, we define the server email.
    if(empty($aParams['from']))
        $aParams['from'] = $_SERVER['SERVER_ADMIN'];

    /*** Headers ***/
    // To avoid the email goes in the spam folder of email client.
    $sHeaders = "From: \"{$_SERVER['HTTP_HOST']}\" <{$_SERVER['SERVER_ADMIN']}>\r\n";

    $sHeaders .= "Reply-To: <{$aParams['from']}>\r\n";
    $sHeaders .= "MIME-Version: 1.0\r\n";
    $sHeaders .= "Content-Type: multipart/alternative; boundary=\"$sFrontier\"\r\n";

    /*** Text Format ***/
    $sBody = "--$sFrontier\r\n";
    $sBody .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $sBody .= "Content-Transfer-Encoding: 8bit\r\n";
    $sBody .= "\r\n" . $sTextBody . "\r\n";

    /*** HTML Format ***/
    $sBody .= "--$sFrontier\r\n";
    $sBody .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
    $sBody .= "Content-Transfer-Encoding: 8bit\r\n";
    $sBody .= "\r\n" . $sHtmlBody . "\r\n";

    $sBody .= "--$sFrontier--\r\n";

    /** Send Email ***/
    return mail($aParams['to'], $aParams['subject'], $sBody, $sHeaders);
}
