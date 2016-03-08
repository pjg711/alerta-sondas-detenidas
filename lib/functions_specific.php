<?php
/**
 * Seedclima Project :: Based on Kite Box CMS engine developed by Jaxolotl Design
 * 
 * File: functions_specifics.php  
 * 
 * Kite Box Eukaryotes V6.x: An Evolution Jump on Content Management 
 * Eukaryotes is an open source Advanced Multilanguage Content Manager  
 * 
 * Graphic Design, MySQL & PHP Developer: Copyright (c) 2009-2010 Javier Valderrama
 * 
 * <b>Site specific general purpose functions</b> 
 * 
 * @package       seedCore 
 * @subpackage    Libraries
 * @author        Javier Valderrama  <javier@jaxolotl-design.com>  
 * @link          http://www.jaxolotl-design.com
 * @copyright     2009-2010 Javier Valderrama
 * @version       1.0
 * @filesource
 */

/**
 * Admin Permissions
 * 
 * Performs a control against the database if current Admin credentials sessions belong to an existent administrator.
 * Also defines admin superadmin  wrights if not defined.
 * 
 * On failure destroys current session, echoes Admin login form and returns false.
 * 
 * @uses req()
 * @return bool|string 
 */
function admin_permissions()
{
    if((isset($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_NAME'])) && (isset($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_PASS'])))
    {
        $query = "  
            SELECT
                `username`,
                `password`,
                `superadmin`
            FROM `" . DBT_PREFIX . "administrators`
            WHERE
                `username` ='" . process_plain_text($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_NAME']) . "'
                AND `password` ='" . process_plain_text($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_PASS']) . "'
                LIMIT 1";
        sql_select($query, $results);
        if(mysql_num_rows($results) > 0)
        {
            $row = mysql_fetch_array($results);
            if(!defined(LOCAL_IDENTIFIER . '_SUPERADMIN'))
            {
                define(LOCAL_IDENTIFIER . '_SUPERADMIN', $row['superadmin']);
            }
            return true;
        }else
        {
            $_SESSION = array();
            session_destroy();
            echo ADMIN_LOGIN_FORM;
            return false;
        }
    }else
    {
        if((isset($_POST['login_name'])) || (isset($_POST['login_pass'])))
        {
            if((req("login_name", "POST", $sanitise = false) == "exit") && (req("login_pass", "POST", $sanitise = false) == "exit"))
            {
                echo "<br> <h2>" . translate("logged_out") . "</strong></h2>";
            }else
            {
                echo "<br>" . ALERT_BOX_OPEN . translate("error_username_password") . " " . translate("ammend") . ALERT_BOX_CLOSE;
            }
            echo ADMIN_LOGIN_FORM;
            return false;
        }else
        {
            echo ADMIN_LOGIN_FORM;
            return false;
        }
    }
}

/**
 * Translate
 * 
 * The function takes 1 argument and performs the following steps<br>
 * 1 - Check if the translation has been retrieved before, if it does the funct returns the defined translation, otherwise..
 * 2 - Take $string as a key to retrieve the translation stored on the database tranlations table and returns it
 * 3 - If no key was found on the database it returns the string as is. 
 * 
 * @param  string $string
 * @return string 
 */
function translate($string)
{
    // Check if translation has been performed before
    if(defined('DEFINED_TRANSLATION_TERM_' . strtoupper($string)))
    {
        return constant('DEFINED_TRANSLATION_TERM_' . strtoupper($string));
    }
    // Query the database for a translation
    else
    {
        $query = "
            SELECT
                `lang_" . CURRENT_LANGUAGE . "`,
                `lang_" . DEFAULT_LANGUAGE . "`
            FROM `" . DBT_PREFIX . "translations`
            WHERE `label` ='" . $string . "' ";
        sql_select($query, $results);
        
        if(mysql_num_rows($results) > 0)
        {
            $row = mysql_fetch_row($results);
            if($row['0'])
            {
                define('DEFINED_TRANSLATION_TERM_' . strtoupper($string), $row['0']);
                return $row['0'];
            }else
            {
                define('DEFINED_TRANSLATION_TERM_' . strtoupper($string), $row['1']);
                return ((DEFINED_REMOTE_ADDR === "127.0.0.1") ? "!&thorn; " : "") . $row['1'];
            }
        }else
        {
            define('DEFINED_TRANSLATION_TERM_' . strtoupper($string), ((DEFINED_REMOTE_ADDR === "127.0.0.1") ? "!&para; " : "") . ucfirst(str_replace("_", " ", $string)));
            return ((DEFINED_REMOTE_ADDR === "127.0.0.1") ? "!&para; " : "") . ucfirst(str_replace("_", " ", $string));
        }
    }
}

/**
 * Language properties
 * 
 * Returns a Lang selector, a language image or a language name depending on $output param request
 * 
 * @param  string    $output               String to switch output this way: <br> 'selector'->A Select Form  <br> 'flag'->Flag img  <br>'name'->Translated lang name                                                                                                                                                                                                                                                                                                                                                                                           
 * @param  int       $selected_lang_id     The available Current selected language id 
 * @param  bool      $show_disabled_mess   If optional value is 0 no "disabled" message will be added 
 * @param  bool      $show_disabled_langs  If optional value is 0 no "disabled" languages wil be included on query
 * 
 * 
 * @return string  
 */
function language_properties($output, $selected_lang_id, $show_disabled_mess = 1, $show_disabled_langs = 1)
{
    $query = "
        SELECT
            `language_id`,
            `label`,
            `name`,
            `image`,
            `code`,
            `enable`
        FROM `" . DBT_PREFIX . "languages`"
            . (($show_disabled_langs == 0) ? (" WHERE `enable` = '1' ") : "")
            . "ORDER BY `ordine`,`enable` DESC,`language_id`";
    sql_select($query, $results);

    switch ($output):
        case'selector':
        case'editor':   // Depricate
        case'editor2':  // Depricate
            settype($lang_select_options, "string");
            while (($row = mysql_fetch_array($results)))
            {
                $lang_select_options .= "\n<option value=\"" . $row['language_id'] . "\" "
                        . is_selected_option((($selected_lang_id) ? ($selected_lang_id) : DEFAULT_LANGUAGE), $row['language_id'])
                        . ">"
                        . translate($row['label'])
                        . ((($row['enable'] <> 1) && ($show_disabled_mess == 1)) ? (" [" . translate("disabled") . "] ") : "")
                        . "</option>";
            }
            return $lang_select_options;
            break;
        case'flag':
        case'lang_flags': // Depricate
            while ($row = mysql_fetch_array($results))
            {
                if($selected_lang_id == $row['language_id'])
                {
                    $current_lang_flag = LANGUAGE_FLAGS . $row['image'];
                }
            }
            return $current_lang_flag;
            break;

        case'name':
            while ($row = mysql_fetch_array($results)) {

                if ($selected_lang_id == $row['language_id']) {
                    $current_lang_name = translate($row['label']);
                }
            }
            return $current_lang_name;
            break;

    endswitch;
}

/**
 * Number range form selctor
 * 
 * Helper to create a select form with stepped number sequences
 * 
 * @param  string    $field_name     The <select> name attribute
 * @param  int       $start          Starting number
 * @param  int       $stop           Last value
 * @param  int       $range          Scalable value Exmpl. $range = 5 -> count by five steps 5 - 10 -....
 * @param  string    $available      The selected $_POST/$row['db_value']
 * @param  bool      $add_null       Add a "select" option with no value at the beginning of the form?
 * 
 * @return string    Returns a <select> form
 */
function number_range_form($field_name, $start, $stop, $range, $available, $add_null = true)
{

    $start = (int) $start;
    $stop = (int) $stop;
    $range = (int) $range;

    $return_string = (($add_null) ? "<option value=\"\">" . translate("select") . "</option>\n" : "");
    $count = $start;
    while ($count <= $stop) {
        $return_string .= "\n<option value=\"" . $count . "\" " . is_selected_option($available, $count) . ">" . $count . "</option>\n";
        $count = $count + $range;
    }
    return "
    <select name=\"" . $field_name . "\">
    "
    . $return_string . "
    </select>";
}

/**
 * Get Administrator's name
 * 
 * Retrieves the admin Real Name related to the given ID
 * 
 * @param  int   $adm_id   The current admin id session
 * 
 * @return string  
 */
function get_admin_name($adm_id)
{

    $query = "SELECT
    `rowid`,
    `real_name`
    FROM `" . DBT_PREFIX . "administrators`
    WHERE `rowid` ='" . $adm_id . "'";

    sql_select($query, $results);

    if (mysql_num_rows($results) > 0) {
        $row = mysql_fetch_array($results);
        return $row['real_name'] . "(admin)";
    }
}

#############################################
###   GEO LOCATE BY IP
#

/**
 * Geo Locate by IP address
 * 
 * Get visitor's country by it's IPv4 address
 * 
 * The function takes 1 argument and performs the following steps<br>
 * 1 - Check if the Geolocation has been retrieved before, if it does the funct returns the defined Geolocation, otherwise..
 * 2 - Take $ipnumber to retrieve the  Geolocation on the database xip2country table and returns it
 * 
 * @param  string  $ipnumber  IPv4 format
 * 
 * @return string  Country flag and name from Db Country table
 */
function geolocate_by_ip($ipnumber)
{

    $ip_converted = sprintf("%u", ip2long($ipnumber));

    // Check if Geolocation has been performed before
    if (defined('DEFINED_GEOLOCATION_' . strtoupper($ip_converted))) {
        return constant('DEFINED_GEOLOCATION_' . strtoupper($ip_converted));
    }

    // Query the database for a translation
    else {

        // CHECK IF IP2COUNTRY DB TABLE IS INSTALLED
        $query = "SHOW TABLES FROM `" . DB_DATABASE . "` LIKE '" . DBT_PREFIX . "xip2country'";
        sql_select($query, $results);

        if (mysql_num_rows($results) > 0) {

            $query = "SELECT `iso`
        FROM `" . DBT_PREFIX . "xip2country`
        WHERE '" . $ip_converted . "' BETWEEN `ip_start` AND `ip_end`
        ";

            sql_select($query, $results);

            if (mysql_num_rows($results) > 0) {
                $cc = mysql_fetch_assoc($results);

                $geolocation = "<img src=\"" . MAIN_DOMAIN . IMAGES_MAIN_FOLDER . LANGUAGE_FLAGS . "country_flags/" . strtolower($cc['iso']) . ".png\" border=\"0\" alt=\"" . $cc['iso'] . "\" title=\"" . alt_text(create_country_options($cc['iso'], $cc['iso'], "text", $status = 1)) . "\" >";

                define('DEFINED_GEOLOCATION_' . strtoupper($ip_converted), $geolocation);

                // RETURN COUNTRY FLAG AND COUNTRY NAME
                return $geolocation;
            }
        }
    }
}

/**
 * Flash Object
 * 
 * Get a given file, check if it is swf movie an renders it with the selected parameters
 * 
 * @param  string    $swf_file_path    The Path and filename to the SWF file with no file extension
 * @param  int       $width            The SWF file width (px) 
 * @param  int       $height           The SWF file height (px) 
 * @param  string    $bgcolor          The SWF default bgcolor (hex-dex html color code
 * @param  string    $flashvars        An alternative string to feed the swf file with variables 
 * @param  string    $wmode            An Optional value for swf rendering mode Window/Opaque/Transparent 
 * @uses    get_file_extension()
 * 
 * @return string    Returns AC_FL_RunContent javascript flash movie render
 */
function flash_object($swf_file_path, $width, $height, $bgcolor, $flashvars, $wmode = 'transparent', $movie_id = '')
{

    if (strtolower(get_file_extension($swf_file_path)) == "swf") {
        return
        "<script type=\"text/javascript\">
      AC_FL_RunContent(
       'id','" . $movie_id . "',
       'name','" . $movie_id . "',
      'codebase','http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=" . FLASH_VERSION . ",0,0,0',
      'width','" . $width . "',
      'height','" . $height . "',
      'src','" . str_replace(".swf", "", $swf_file_path) . "',
      'quality','best',
      'pluginspage','http://www.macromedia.com/go/getflashplayer',
      'movie','" . str_replace(".swf", "", $swf_file_path) . "',
      'allowScriptAccess','sameDomain',
      'wmode','" . $wmode . "',
      'bgcolor','" . $bgcolor . "',
      'Flashvars',\"" . $flashvars . "\"
      ); //end AC code
      </script>";
    }
}

#############################################
###   COUNTRY OPTIONS
#
//  $posted  ->  (string)  Available $_POST value
//  $rowed   ->  (string)  Available $dbrow value
//  $type    ->  (string)  Switch case value
// case'select'  returns a country list in format <option value='ISO CODE'>Country name</option>
// case'text'    returns the available $posted/$rowed iso code Country Name
//  Output (string)

/**
 * Create Country options
 * 
 * Get a given file, check if it is swf movie an renders it with the selected parameters
 * 
 * @param  string    $posted    Selected $_POST value
 * @param  string    $rowed     Selected $dbrow value  
 * @param  string    $type      Switch output type value (select|text))
 *
 * @uses    available_posted_rowed()
 * @uses    is_selected_option()
 * @uses    alt_text()
 * 
 * @return string    Returns a country select element or a country Name related to the given output $type selection
 */
function create_country_options($posted, $rowed, $type)
{

    //Initialize var
    settype($return_string, "string");

    switch ($type):
        case'select':

            $query = "SELECT
      `iso`,
      `lang1`,
      `lang2`,
      `lang3`,
      `lang4`,
      `lang5`,
      `lang6`,
      `lang7`,
      `lang8`,
      `lang9`,
      `lang10`,
      `lang11`,
      `lang12`,
      `lang13`,
      `lang14`,
      `lang15`
      FROM `" . DBT_PREFIX . "location_countries`
      ORDER BY `lang" . CURRENT_LANGUAGE . "`";

            sql_select($query, $results);

            $return_string = "\n <option value=\"\" style=\"font-weight:bold;\">" . translate("select_country") . "</option>";

            while ($row = mysql_fetch_array($results)) {

                $return_string .= "\n <option value=\"" . $row['iso'] . "\" " . is_selected_option($row['iso'], available_posted_rowed($posted, $rowed)) . " >" . alt_text((($row['lang' . CURRENT_LANGUAGE]) ? ($row['lang' . CURRENT_LANGUAGE]) : ($row['lang' . DEFAULT_LANGUAGE]))) . "</option>";
            }
            break;

        case'text':

            $query = "SELECT
      `iso`,
      `lang1`,
      `lang2`,
      `lang3`,
      `lang4`,
      `lang5`,
      `lang6`,
      `lang7`,
      `lang8`,
      `lang9`,
      `lang10`,
      `lang11`,
      `lang12`,
      `lang13`,
      `lang14`,
      `lang15`
      FROM `" . DBT_PREFIX . "location_countries`
      WHERE `iso` ='" . available_posted_rowed($posted, $rowed) . "'";

            sql_select($query, $results);

            while ($row = mysql_fetch_array($results)) {
                $return_string .= alt_text((($row['lang' . CURRENT_LANGUAGE]) ? ($row['lang' . CURRENT_LANGUAGE]) : ($row['lang' . DEFAULT_LANGUAGE])));
            }
            break;
    endswitch;

    return $return_string;
}



/**
 * Create Admin options
 * 
 * Creates an HTML select form of the stored Administrator accounts 
 * 
 * @param    int   $available_id  Current selected admin
 * @uses     is_selected_option()
 * 
 * @returns  string  html select form with the stored Administrator accounts list 
 * 
 */
function create_admin_options($available_id)
{

    $query = "SELECT
    `rowid`,
    `real_name`
    FROM `" . DBT_PREFIX . "administrators`
    ORDER BY `rowid`";

    sql_select($query, $results);

    if (mysql_num_rows($results) > 0) {
        settype($return_string, "string");

        while ($row = mysql_fetch_array($results)) {

            $return_string .= "\n <option value=\"" . $row['rowid'] . "\" " . is_selected_option($row['rowid'], $available_id) . ">"
                    . $row['real_name']
                    . "</option>";
        }

        return $return_string;
    }
}

/**
 * Safe e-mail
 * 
 * Kinda protect e-mail addresses from spam bots potting it into a javascript procedure
 * 
 * @param    string    $email_string  the e-mail address to protect
 * 
 * @returns  string    html javascript code
 * 
 */
function safe_email($email_string)
{
    if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", trim($email_string))) {
        $email = explode("@", trim($email_string));

        return"
      <script language=\"JavaScript\" type=\"text/javascript\">
      <!--
      var name = \"" . $email['0'] . "\";
      var domain = \"" . $email['1'] . "\";
      document.write('<a  href=\"mailto:' + name + '@' + domain + '\" title=\"' + name + '@' + domain + '\" >');
      document.write('' + name + '@' + domain + '</' + 'a>');
      // -->
      </script>
      ";
    } else {
        return "";
    }
}

/**
 * CMS Control Panel Boxes
 * 
 * Helper to contructs table containers for data visualization
 * 
 * @param    string    $switch   values (open|close) to switch the part of the html table to render
 * @param    string    $label    The title label of the table to translate
 * @uses     translate()
 * 
 * @returns  string    html table
 * 
 */
function cms_cp_boxes($switch, $label = "label")
{
    switch ($switch) {
        case'open':
            return "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" class=\"form_table\">
        <tr valign=\"top\" class=\"header\">
        <td><h3 >" . translate($label) . "</h3></td>
        </tr>
        <tr valign=\"top\">
        <td height=\"35\">";

            break;

        case'close':
            return "
        </td>
        </tr>
        </table>";
            break;
    }
}

/**
 * Multi page link box pagination function
 * 
 * Creates a pagination helper in  link format or form select format
 * 
 * @param    int       $tot_pages                 Total page found
 * @param    int       $offset                    Internal offset counter
 * @param    int       $p_offset                  Currently browsed offset
 * @param    int       $limit                     Results per page
 * @param    string    $initial_var_name          The first query string var name
 * @param    array     $included_vars_names[]     All the other query string var names needed
 * @param    string    $list_stile                values(plain_text|select)Switched between html tabl with links and Select form
 * 
 * @uses     req()
 * @uses     translate()
 * 
 * @returns  string   html pagination nav
 * 
 * @todo     rewrite the function to allow page range instead of returning all pages 
 * 
 */
function multipage_link_box($tot_pages, $offset, $p_offset, $limit, $initial_var_name, $included_vars_names = array(), $list_stile = "plain_text")
{

    switch ($list_stile):
        case'select':
            if ($tot_pages > 1) {
                $page_text = ucfirst(translate("page"));

                ////////// CREATE MULTI PAGE LINKS

                $page_numbers = "
        \n<table width=\"100%\" border=\"0\" cellpadding=\"10\" cellspacing=\"1\" class=\"highlighted\" style=\"margin-top:5px; margin-bottom:5px;\">
        \n<tr  >
        <td align=\"left\" >
        <select name=\"page_selector\" onchange=\"go(this)\">";
                for ($n_page = 1; $n_page <= $tot_pages; $n_page++) {

                    $page_numbers .= "\n<option value=\"" . DEFINED_SCRIPT_FILENAME;
                    $page_numbers .="?" . $initial_var_name . "=" . req($initial_var_name, "REQUEST", true);

                    if ((in_array("p_offset", $included_vars_names))) {
                        $page_numbers .= "&amp;p_offset_2=" . $offset;
                    } else {
                        $page_numbers .= "&amp;p_offset=" . $offset;
                    }

                    foreach ($included_vars_names as $key => $val) {
                        $page_numbers .= req($val, "REQUEST", true) ? ("&amp;" . $val . "=" . req($val, "REQUEST", true)) : "";
                    }
                    $page_numbers .= "\" " . ((ceil($p_offset / $limit) == $n_page) ? " SELECTED " : "") . ">" . $page_text . " " . $n_page . "</option>\n";

                    $offset = $offset + $limit;
                }
                $page_numbers .= "\n</select>
        </td>
        </tr>
        </table>";

                return $page_numbers;
            } else {
                return "\n";
            }
            break;

        default:
            if ($tot_pages > 1) {
                $page_text = ucfirst(translate("page"));
                ////////// CREATE MULTI PAGE LINKS

                $page_numbers = "\n<table width=\"100%\" border=\"0\" cellpadding=\"10\" cellspacing=\"1\" class=\"highlighted\" style=\"margin-top:5px; margin-bottom:5px;\">
        \n<tr  >
        <td align=\"left\" >
        <span class=\"middle\">";
                for ($n_page = 1; $n_page <= $tot_pages; $n_page++) {
                    if ($n_page > 1) {
                        $page_numbers .= " | ";
                    }
                    if (ceil($p_offset / $limit) == $n_page) {
                        $page_numbers .= "\n<strong>" . $page_text . " " . $n_page . "</strong>";
                    } else {
                        $page_numbers .= "\n<a href=\"" . DEFINED_SCRIPT_FILENAME;
                        $page_numbers .="?" . $initial_var_name . "=" . req($initial_var_name, "REQUEST", true);

                        if ((in_array("p_offset", $included_vars_names))) {
                            $page_numbers .= "&amp;p_offset_2=" . $offset;
                        } else {
                            $page_numbers .= "&amp;p_offset=" . $offset;
                        }

                        foreach ($included_vars_names as $key => $val) {
                            $page_numbers .= req($val, "REQUEST", true) ? ("&amp;" . $val . "=" . req($val, "REQUEST", true)) : "";
                        }
                        $page_numbers .= "#plist\" class=\"green\">" . $n_page . "</a>\n";
                    }
                    $offset = $offset + $limit;
                }
                $page_numbers .= "\n</span>
        </td>
        </tr>
        </table>";

                return $page_numbers;
            } else {
                return "\n";
            }
            break;
    endswitch;
}

/**
 * Create Admin IP access options
 * 
 * Creates an HTML select form of the stored Administrators ip access location 
 * 
 * @param    int   $available_ip  Current selected ip
 * @param    int   $type           Switch between remote_addr or x_forwarded_for
 * @uses     is_selected_option()
 * 
 * @returns  string  html select form with the stored Administrator access db table 
 * 
 */
function create_admin_ipaccess_options($available_ip, $type)
{

    $field_requested = ($type === 'x_forwarded_for') ? ('x_forwarded_for') : ('remote_addr');

    $query = "SELECT DISTINCT
    `" . $field_requested . "`
    FROM `" . DBT_PREFIX . "administrators_access`
    WHERE  `" . $field_requested . "` <> 'unavailable'
    ORDER BY `" . $field_requested . "`";

    sql_select($query, $results);

    if (mysql_num_rows($results) > 0) {
        settype($return_string, "string");

        while ($row = mysql_fetch_array($results)) {
            $current_value = ip2long($row[$field_requested]);


            $return_string .= "\n <option value=\"" . $current_value . "\" " . is_selected_option($current_value, $available_ip) . ">"
                    . $row[$field_requested]
                    . "</option>";
        }

        return $return_string;
    }
}

/**
 * Convert degrees into cardinal point direction
 * 
 * @param    float   $degrees      Direction degrees
 * 
 * @returns  string  html Cardinal coordinates 
 * 
 */
function deg2cpd($degrees, $returnVectorDegrees = false)
{

    if (($degrees > 11.25) && ($degrees <= 33.75)) {
        $return_string = "&deg; NNE";
        $vectorDegrees = '22.5';
    } elseif (($degrees > 33.75) && ($degrees <= 56.25)) {
        $return_string = "&deg; NE";
        $vectorDegrees = '45';
    } elseif (($degrees > 56.25) && ($degrees <= 78.75)) {
        $return_string = "&deg; NEE";
        $vectorDegrees = '67.5';
    } elseif (($degrees > 78.75) && ($degrees <= 101.25)) {
        $return_string = "&deg; E";
        $vectorDegrees = '90';
    } elseif (($degrees > 101.25) && ($degrees <= 123.75)) {
        $return_string = "&deg; SEE";
        $vectorDegrees = '112.5';
    } elseif (($degrees > 123.75) && ($degrees <= 146.25)) {
        $return_string = "&deg; SE";
        $vectorDegrees = '135';
    } elseif (($degrees > 146.25) && ($degrees <= 168.75)) {
        $return_string = "&deg; SSE";
        $vectorDegrees = '157.5';
    } elseif (($degrees > 168.75) && ($degrees <= 191.25)) {
        $return_string = "&deg; S";
        $vectorDegrees = '180';
    } elseif (($degrees > 191.25) && ($degrees <= 213.75)) {
        $return_string = "&deg; SSO";
        $vectorDegrees = '202.5';
    } elseif (($degrees > 213.75) && ($degrees <= 236.25)) {
        $return_string = "&deg; SO";
        $vectorDegrees = '225';
    } elseif (($degrees > 236.25) && ($degrees <= 258.75)) {
        $return_string = "&deg; SOO";
        $vectorDegrees = '247.5';
    } elseif (($degrees > 258.75) && ($degrees <= 281.25)) {
        $return_string = "&deg; O";
        $vectorDegrees = '270';
    } elseif (($degrees > 281.25) && ($degrees <= 303.75)) {
        $return_string = "&deg; NOO";
        $vectorDegrees = '292.5';
    } elseif (($degrees > 303.75) && ($degrees <= 326.25)) {
        $return_string = "&deg; NO";
        $vectorDegrees = '315';
    } elseif (($degrees > 326.25) && ($degrees <= 348.75)) {
        $return_string = "&deg; NNO";
        $vectorDegrees = '337.5';
    } else {
        $return_string = "&deg; N";
        $vectorDegrees = '360';
    }

    switch ($returnVectorDegrees) {
        case true:
            return $vectorDegrees;
            break;
        default:
            return $return_string;
            break;
    }
}

/**
 * Convert degrees into cardinal point direction
 *
 * @param    float   $degrees      Direction degrees
 *
 * @returns  string  html Cardinal coordinates
 *
 */
function deg2cpd2img($degrees)
{
    $direction = trim(str_replace('&deg;', '', deg2cpd($degrees)));
    $image = MAIN_DOMAIN . IMAGES_MAIN_FOLDER . THEMES_FOLDER . CURRENT_SEEDCLIMA_THEME . SENSOR_ICONS_FOLDER . 'wind_direction_' . strtolower($direction) . '.png';

    return '<img src="' . $image . '" alt="' . $direction . '" border="0" class="center_img"> ' . $direction;
}

/**
 * Convert Lat/Long  DMS(deg)<=>MinDec Decimal
 * 
 * @param    array     $dms    Lat / Long in DMS Sexagesimal notation
 * @param    float     $dec    Lat / Long in MinDec Decimal notation
 * @param    string    $type   possible values lat|long
 * 
 * @returns  mixed  convertion switch  deg2dec -> string | dec2deg -> array
 * 
 */
function convert_lat_long($dms = array(), $dec = '', $type = '')
{
    $dms_assigned = ((isset($dms) && (count($dms) == 3)) ? ("assigned") : (""));
    if (trim($dms_assigned . $dec . $type)) {
        bcscale(10);
        $convertion = ((trim($dms_assigned)) ? ("deg2dec") : ("dec2deg"));

        switch ($convertion):
            case'deg2dec':

                if (preg_match("#-#", $dms['deg'])) {
                    $dms['deg'] = str_replace("-", "", $dms['deg']);
                    $cardinal = "-";
                } else {
                    $dms['deg'] = $dms['deg'];
                    $cardinal = "";
                }


                $return_value = $cardinal . ($dms['deg'] + ($dms['min'] * 1 / 60) + ($dms['sec'] * 1 / 60 * 1 / 60));

                // (DEG + (MIN * 1/60) + (SEC * 1/60 * 1/60))

                break;

            case'dec2deg':
                if (preg_match("#-#", $dec)) {
                    $dec = str_replace("-", "", $dec);
                    $cardinal = (($type == 'lat') ? (translate("south")) : (translate("west")));
                    $sign = "-";
                } else {
                    $dec = $dec;
                    $cardinal = (($type == 'lat') ? (translate("north")) : (translate("east")));
                    $sign = "";
                }

                $degrees = (int) $dec;

                $minutes = (bcsub($dec, $degrees) * 60);

                $seconds = ((bcsub($minutes, (number_format($minutes, 0))))) * 60;


                //$return_value =  $degrees . "° " . number_format($minutes,0) . "' " . $seconds . "'' ". $cardinal;

                $return_value = array('deg' => $sign . $degrees, 'min' => number_format($minutes, 0), 'sec' => $seconds, 'cardinal' => $cardinal);

                break;
        endswitch;

        return $return_value;
    } else {
        return "wrong parameters";
    }
}

function recoverLatLongData($value, $type)
{
    if (!is_numeric($value)) {
        if (preg_match("#W|O|S#i", $value)) {
            $value = abs(preg_replace("#[[:alpha:]]#i", "", $value));
            $sign = "-";
        } else {
            $sign = '';
        }
        $value = $sign . $value;

        if (!is_numeric($value)) {
            return '';
        }

    }
    
    $sign = ((($value + abs($value)) == 0) ? "-" : "");
    $value = abs($value);
    $splitted = str_split($value);
    $splitted[3] = (isset($splitted[3]) ? $splitted[3] : '');
    $splitted[4] = (isset($splitted[4]) ? $splitted[4] : 0);
    $splitted[5] = (isset($splitted[5]) ? $splitted[5] : 0);
    switch ($type):
        case'latitude':
            $value = (($value > 90) ? convert_lat_long($dms = array('deg' => $splitted[0] . $splitted[1], 'min' => $splitted[2] . $splitted[3], 'sec' => $splitted[4] . $splitted[5]), $dec = '', $type = 'lat') : $value);
            break;
        case'longitude':
            $value = (($value > 180) ? convert_lat_long($dms = array('deg' => $splitted[0] . $splitted[1], 'min' => $splitted[2] . $splitted[3], 'sec' => $splitted[4] . $splitted[5]), $dec = '', $type = 'long') : $value);
            break;
    endswitch;


    return $sign . round($value, 6);
}

#############################################
###   USER AUTHORIZATION Radio Selector
#
//  Output (string)

function user_authorization($string)
{
    $return_string = "
    <input type=\"radio\" name=\"authorization\" value=\"accept\" " . is_checked_option("accept", $string) . "> " . translate("accept") . " &nbsp; &nbsp;
    <input type=\"radio\" name=\"authorization\" value=\"deny\" " . is_checked_option("", $string) . "> " . translate("not_accept") . " &nbsp; &nbsp;
    ";
    return $return_string;
}

#############################################
###   USER GENDER Radio Selector
#
//  Output (string)

function user_gender($string)
{
    $return_string = "
    <input type=\"radio\" name=\"gender\" value=\"m\" " . is_checked_option("m", $string) . "> " . translate("male") . " &nbsp; &nbsp;
    <input type=\"radio\" name=\"gender\" value=\"f\" " . is_checked_option("f", $string) . "> " . translate("female") . " &nbsp; &nbsp;
    ";
    return $return_string;
}