<?php
/**
 * Seedclima Project :: Based on Kite Box CMS engine developed by Jaxolotl Design
 * 
 * File: functions_standard.php  
 * 
 * Kite Box Eukaryotes V6.x: An Evolution Jump on Content Management 
 * Eukaryotes is an open source Advanced Multilanguage Content Manager  
 * 
 * Graphic Design, MySQL & PHP Developer: Copyright (c) 2009-2010 Javier Valderrama
 * 
 * <b>Mainly abstract and helper functions</b> 
 * 
 * @package       seedCore 
 * @subpackage    Libraries
 * @author        Javier Valderrama  <javier@jaxolotl-design.com>  
 * @link          http://www.jaxolotl-design.com
 * @copyright     2009-2010 Javier Valderrama
 * @version       1.0
 * @filesource
 * @todo          document this file 
 */
//$full_uri = dummy_request_uri();


/* * ***
 * **
 * *
 * *   DATA PROCESS
 * *
 * *
 */

#############################################
###       GET GIVEN FILE EXTENSION
#

function get_file_extension($string)
{

    if (preg_match("#\.#", $string)) {
        $pos = strrpos($string, ".");
        $extension = substr($string, $pos + 1);
    } else {
        $extension = "";
    }

    return $extension;
}

#############################################
###       ADD ZERO FIRST
#
//  $number   -> (int)
//  $digits   -> (int)

function add_zero_first($number, $digits)
{
    if (strlen($number) < $digits) {
        $new_number = "0" . $number;
        return add_zero_first($new_number, $digits);
    } else {
        return $number;
    }
}

#############################################
###       RETURNS A DUMPED VAR IN <pre> FROMAT
#
//  $data     -> (array) || (string)

function dump($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

#############################################
###       RETURNS A STRING IF LANGUAGE GIVEN IS DEFAULT LANGUAGE
#
//  $value      -> (int) is the lang value you want to check
//  $default    -> (int) is the default language value

function check_default_lang($value, $default)
{
    return (($value == $default) ? ("<span class=\"grey_middle\">[Default]</span>") : "");
}

#############################################
###       IS EVEN?
#
//  $code     -> (int) || (float)

function is_even($code)
{
    return ($code & 1) ? false : true;
}

#############################################
###       SPLIT SOMETHING AND RETURNS ASKED KEY
#
//  $string     -> (string) complete string
//  $boundary   -> (string) The boundary string
//  $show       -> (string) array key
//  Splits the string into an array using $offset ant returns $show array key value

function split_selection($string, $boundary, $show)
{
    $splited = split($boundary, $string);
    return $splited[$show];
}

#############################################
###       AVAILABLE TEXT OR DEFAULT
#
//  $text       -> (string)
//  $default    -> (string)

function available_text($text, $default)
{
    return (strlen($text) >= 1) ? $text : $default;
}

#############################################
###       DOUBLE ESCAPES
#
//  $string    -> (string)
//  prevent &amp; to & conversion on CMS control panel submition

function double_escape($string)
{
    $patterns = array(
            '#&(?!\#)#is' // ampersand that is NOT followed by # decimal notation
    );
    $substitutions = array('&amp;');
    $string = preg_replace($patterns, $substitutions, $string); //replace ampensands and spaces

    return $string;
}

#############################################
###
#         DEPRICATE

function reduce_text($string)
{
    return extract_text($string, 200, "CHECK DEPRICATE FUNTION");
}

#############################################
###       TEXT REDUCE
#
//  $string             -> (string)   This is the input string to be reduced
//  $limit              -> (int)      An integer limit of max characters to output, default is 100
//  $replacement        -> (string)   The string to add at the end of a replaced text
//  $boundary           -> (string)   The boundary to find to chop the text after the limit
//                                    you may use a character if needed, for exp.the letter "a" the script will finde the next "a" after limit to chop the text
//  $force_replacement  -> (bool)     If text is shorter than limit set this to true if you need to add the replacement anyway after the text

function extract_text($string, $limit = 200, $replacement = "", $boundary = " ", $force_replacement = false)
{

    $string = strip_tags($string);

    $string = preg_replace("#[\n\t\r]{2,}(\s{2,})#", "\n\n", $string); // void multi breaks

    $string = str_replace("<br />", "<br>", mail_text($string));


    $string_length = strlen($string);
    $limit = (int) $limit;
    $force_replacement = (bool) $force_replacement;

    //Search the end of a word after [, int offset] and set the result as limit
    if (($limit >= $string_length) && ($force_replacement === true)) {
        $limit = @strpos($string, $boundary, $string_length - 1);
        if (!$limit) {
            $limit = $string_length;
        }
    } else {
        $limit = @strpos($string, $boundary, $limit);
        if (!$limit && ($force_replacement === true)) {
            $limit = $string_length;
        }
    }

    if ($limit) {
        //Use $limit to replace width ...Text
        return substr_replace($string, $replacement, $limit);
    } else {
        return $string;
    }
}

#############################################
###       DECODE HTML ENTITIES INTO HEX NOTATION
#
//  $text    -> (string)

function decode_entities($text)
{
    $text = html_entity_decode($text, ENT_QUOTES, "ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text = preg_replace('/&#(\d+);/me', "chr(\\1)", $text); #decimal notation
    $text = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text);  #hex notation
    return $text;
}

#############################################
###     RETURNS DUMMY STRING FOR W3C Compliant LINKS
#
//  $string    -> (string)

function dummy($string)
{
    if (DEFAULT_CHARSET <> "iso-8859-1") {
        return dummy_encoded($string);
    } else {
        global $special_chars_2, $special_chars_dummy;

        $special_chars_dummy = array_merge($special_chars_dummy); // to prevent warning message
        $special_chars = array_merge($special_chars_2); // to prevent warning message

        $patterns = array(
                '#&(?!.{1,6};)#is', // ampersand that is NOT followed by 1-6 characters + ; (prevent special characters code replacement)
                '#\s{1,}|%3b|%0a|%0D#is', // any [\r\n\t]
                '#\’#',
                '#–#',
                '#\s*?|\"*|\<.*?\>|\>|\<|\\{1,}#'
        );
        $substitutions = array('&amp;', '_', '\'', '-', '');
        $dummy = preg_replace($patterns, $substitutions, $string); //replace ampensands and spaces
    }

    $dummy = strtr($dummy, $special_chars);
    $dummy = strtr($dummy, $special_chars_dummy);
    return $dummy; //replace special characters and convert them into html entities
}

#############################################
###     RETURNS DUMMY STRING FOR W3C Compliant LINKS UTF-8 url-encoded
#
//  $string    -> (string)

function dummy_encoded($string)
{
    global $special_chars_2, $special_chars_dummy;

    $special_chars_dummy = array_merge($special_chars_dummy); // to prevent warning message
    $special_chars = array_merge($special_chars_2); // to prevent warning message
    //$special_chars = array_flip($special_chars); // to use the html entities as keys

    $patterns = array(
            '#&(?!.{1,6};)#is', // ampersand that is NOT followed by 1-6 characters + ; (prevent special characters code replacement)
            '#\s{1,}#is', // any [\r\n\t]
            '#\s*?|\"*|\<.*?\>|\>|\<|\%3b|\%0a|\%0D#'
    );
    $substitutions = array('&amp;', '_', '');
    $dummy = preg_replace($patterns, $substitutions, $string); //replace ampensands and spaces
    $dummy = strtr($dummy, $special_chars);
    $dummy = strtr($dummy, $special_chars_dummy);

    return urlencode($dummy); //replace html entities and convert them into utf-8 url-encoded
}

#############################################
###     RETURNS DUMMY STRING FOR W3C Compliant on REQUEST_URI
#

function dummy_request_uri()
{
    global $HTTP_GET_VARS;
    if (@phpversion() < '4.1.0') {
        $_GET = $HTTP_GET_VARS;
    }
    $count_vars = 0;

    //INITIALIZE $dummy_request_uri
    settype($dummy_request_uri, "string");

    foreach ($_GET as $key => $value) {
        if ($count_vars > 0) {
            $dummy_request_uri .= "&amp;";
        }
        $dummy_request_uri .= dummy(str_replace(array("--", "'", "<", ">", "\/"), "", $key)) . "=" . dummy(str_replace(array("--", "'", "<", ">"), "", $value));
        $count_vars++;
    }
    return DEFINED_SCRIPT_FILENAME . "?" . $dummy_request_uri;
}

#############################################
###     DEPRICATE
#

function translate_breaks($string)
{
    return mail_text($string);
}

#############################################
###    RETURNS AN HTML STRING FORMAT IF PLAIN ON INPUT
#
//  $string    -> (string)

function mail_text($string)
{
    $patterns = array(
            '#align=" "#is',
            '#hspace=" "#is',
            '#vspace=" "#is',
            '#alt=" "#is',
            '#class=" "#is'
    );
    $substitutions = array('');
    $string = preg_replace($patterns, $substitutions, $string);
    return (preg_match('#\<.*?\>#s', $string)) ? $string : nl2br(trim($string));
}

#############################################
###    RETURNS AN HTML Unordered LIST FROM PLAIN TEXT INPUT using \n as boundary
#
//  $string    -> (string)

function translate_to_list($string)
{
    if (!preg_match('#\<.*?\>#s', $string)) { // only plain text
        $string = trim($string);
        $string = str_replace("\n", "<split>", $string); //split on newline
        $string = split('<split>', $string);
        $output_string = "\n<ul>";
        foreach ($string as $key => $val) {
            if (preg_match('#[[:graph:]]#', $val)) { // if printable text
                $output_string .= "\n\t<li>\n\t\t" . $val . "\n\t</li>";
            }
        }
        $output_string .= "\n</ul>";
        return $output_string;
    } else {
        return $string;
    }
}

#############################################
###   TEXT FOR ALT or TITLE ATTRIBUTES
#
//  $string    -> (string)

function alt_text($string)
{
    $string = trim(stripslashes(strip_tags($string)));

    $patterns = array(
            '#\>#',
            '#\<#',
            '#\"#',
            '#\’|\'#',
            '#–#',
            '#\'#',
            '#&(?!.{1,6};)#is' //Ampresand not follower by html special char definition
    );
    $substitutions = array(
            '&gt;',
            '&lt;',
            '&quot;',
            '&acute;',
            '-',
            '&amp;'
    );

    $string = preg_replace($patterns, $substitutions, strip_new_line($string));

    $string = preg_replace("# {2,}#", " ", $string);

    return $string;
}

#############################################
###   STRIP <a> TAG
#
//  $string    -> (string)

function remove_links($string, $add_empty_link = false)
{
    if ($add_empty_link == true) {
        $string = preg_replace('#target="_blank"#is', '', $string);
        $string = preg_replace('#\s*href\=\s*.*?"\s*#is', ' href="#" title="LINK REMOVED - ', $string);
    } else {
        $string = preg_replace('#<\s*a\s.*?\>|</\s*a\s*>#is', '', $string);
    }
    return $string;
}

#############################################
###   STRIP new line definition
#
//  $string    -> (string)

function strip_new_line($string)
{
    $string = ereg_replace("[\n\r\t]", " ", $string);
    return $string;
}

#############################################
###   STRIP <embed> Tag
#
//  $string    -> (string)

function kill_embed($string)
{
    $killed = preg_replace('#<\s*embed\s.*?</\s*embed\s*>#is', '', $string);
    return $killed;
}

#############################################
###   RETURNS A W3C VALID FLASH STRING
#
//  $string    -> (string)

function agent_selector($string)
{

    if (!preg_match("#W3C_Validator#i", DEFINED_HTTP_USER_AGENT)) {
        if ((!preg_match("#MSIE#i", DEFINED_HTTP_USER_AGENT)) && (preg_match("#Windows#i", DEFINED_HTTP_USER_AGENT))) {
            return $string;
        } elseif (!preg_match("#Windows#i", DEFINED_HTTP_USER_AGENT)) {
            return $string;
        } else {
            return mail_text(kill_embed($string));
        }
    } else {
        return mail_text(kill_embed($string));
    }
}

#############################################
###   RETURNS DATA FOR INPUT TEXT FIELD that doesn't need ampersand escape - See double_escape() Function
#
//  $string    -> (string)

function strip_double_escape($string)
{

    return str_replace("&amp;", "&", trim(stripslashes($string)));
}

#############################################
###   Remove Default DEFINED folders from string
#
//  $string    -> (string)

function strip_default_folders($string)
{
    global $folders_to_strip;
    $substitutions = array(
            ""
    );
    $output_string = preg_replace($folders_to_strip, $substitutions, trim(stripslashes($string)));
    return $output_string;
}

#############################################
###   Format date to european style dd-mm-yyyy
#
//  $string    -> (string)
//  $output    -> (string)

function date_to_european($string, $output ="", $from_unixtimestamp = false)
{

    if(trim($string))
    {
        if($from_unixtimestamp)
        {
            $date = getdate($string);
            $string = $date['year'] . "-" . add_zero_first($date['mon'], 2) . "-" . add_zero_first($date['mday'], 2) . " <strong>" . add_zero_first($date['hours'], 2) . ":" . add_zero_first($date['minutes'], 2) . ":" . add_zero_first($date['seconds'], 2) . "</strong>";
        }

        if(preg_match('#(\d{4})-(\d{2})-(\d{2})#ix', $string))
        {
            $split = split(" ", $string);
            $return_string = split("-", $split['0']);
        }

        $time = ((isset($split['1']) && ($split['1'] > "00:00:00")) ? " - " . $split['1'] : "");

        switch($output)
        {
            case'translated':
                return $return_string['2'] . " " . dateTranslated($return_string['1']) . ", " . $return_string['0'] . $time;
                break;
            case'translated_add_day':
                return dayTranslated(date("w", mktime(0, 0, 0, $return_string['1'], $return_string['2'], $return_string['0']))) . " " . $return_string['2'] . " " . dateTranslated($return_string['1']) . ", " . $return_string['0'] . $time;
                break;

            case'translate_month_year':
                return ucfirst(dateTranslated($return_string['1'])) . " " . $return_string['0'];
                break;

            case'translate_month':
                return ucfirst(dateTranslated($return_string['1']));
                break;

            case'date':
                return $return_string['2'] . "/" . $return_string['1'] . "/" . $return_string['0'] . $time;
                break;

            case'time':
                return str_replace("-", "", $time);
                break;

            case'date_raw':
                return $return_string['0'] . "-" . $return_string['1'] . "-" . $return_string['2'];
                break;

            case'date_strip_time':
                return $return_string['2'] . "/" . $return_string['1'] . "/" . $return_string['0'];
                break;

            default:
                return $return_string['2'] . "/" . $return_string['1'] . "/" . $return_string['0'] . $time;
                break;
        }
    }else
    {
        return "";
    }
}

#############################################
###   translate Month name from DB
#
//  $int    -> (int)

function dateTranslated($int)
{
    switch ($int) {
        case'01':
        case'1':
            return translate("gennaio");
            break;

        case'02':
        case'2':
            return translate("febbraio");
            break;

        case'03':
        case'3':
            return translate("marzo");
            break;

        case'04':
        case'4':
            return translate("aprile");
            break;

        case'05':
        case'5':
            return translate("maggio");
            break;

        case'06':
        case'6':
            return translate("giugno");
            break;

        case'07':
        case'7':
            return translate("luglio");
            break;

        case'08':
        case'8':
            return translate("agosto");
            break;

        case'09':
        case'9':
            return translate("settembre");
            break;

        case'10':
            return translate("ottobre");
            break;

        case'11':
            return translate("novembre");
            break;

        case'12':
            return translate("dicembre");
            break;

        default:
            return "";
            break;
    }
}

#############################################
###   translate DAY name from DB
#
//  $int    -> (int)

function dayTranslated($int)
{
    switch ($int) {
        case'00':
        case'0':
            return translate("day_00");
            break;
        case'01':
        case'1':
            return translate("day_01");
            break;

        case'02':
        case'2':
            return translate("day_02");
            break;

        case'03':
        case'3':
            return translate("day_03");
            break;

        case'04':
        case'4':
            return translate("day_04");
            break;

        case'05':
        case'5':
            return translate("day_05");
            break;

        case'06':
        case'6':
            return translate("day_06");
            break;

        default:
            return "";
            break;
    }
}

#############################################
###   Return IMAGE W or H if file is image adn exists
#
//  $path               -> (string)
//  $requested_info     -> (string)

function image_w_h($path, $requested_info)
{
    if (is_image($path)) {
        $image_data = getimagesize($path);
        switch ($requested_info):
            case'width':
                return $image_data['0'];
                break;
            case'height':
                return $image_data['1'];
                break;
        endswitch;
    }
}

#############################################
###   Reduce Thumbnail size adapted to DB settings
#
//  $width               -> (int)
//  $height              -> (int)
//  $maxwidth            -> (int)

function adapt_thumbnail_height($width, $height, $maxwidth)
{
    if (($width > 0) && ($width < $maxwidth)) {
        $maxwidth = $width;
    }
    if (($width < 1)) {
        $width = $maxwidth;
    }
    /*
      $maxwidth / $width * 100 = %
      ($height / 100) * % = $adapted_height
      formula is (H/100) * ((MaxW/W)*100)
     */
    //if($width && $height && $maxwidth){
    return @round(($height / 100) * (($maxwidth / $width) * 100));
    //}
}

#############################################
###   RETURN HTML Table with file "weight" and download time
#
//  $file_location       -> (string)

function file_size($file_location)
{

    // dimensione in bytes del file
    $size_byte = @filesize($file_location);
    $size_Kbyte = ceil(($size_byte / 1024)); // arrotonda il numero a un decimale
    $size_Mbyte = round(($size_byte / 1048576), 1); // arrotonda il numero a un decimale
    //download time
    $size_bits = $size_byte * 8;
    $download_modem = floor(($size_bits / 56000 / 60));  //min
    $download_modem_2 = ceil(($size_bits / 56000)); //sec
    $download_adsl256 = floor(($size_bits / 256000 / 60)); //min
    $download_adsl256_2 = ceil(($size_bits / 256000)); //sec
    $download_fibra = floor(($size_bits / 1048576 / 60)); //min
    $download_fibra_2 = ceil(($size_bits / 1048576)); //sec


    $return_string = "
    <table align=\"center\" width=\"100%\" cellpadding=\"1\" cellspacing=\"1\">
    <tr>
    <td style=\" border-bottom:1px dashed #D3D3D3;\" align=\"left\" class=\"small\">
    <span><span class=\"red\">" . preg_replace("#-|_#", " ", @substr($file_location, strrpos($file_location, "/") + 1)) . "</span></span><br>
    " . translate("file_size") . " = <strong>";
    if ($size_Kbyte >= 1024) {
        $return_string .= $size_Mbyte . "Mb";
    } else {
        $return_string .= $size_Kbyte . "Kb";
    }
    $return_string .= "</strong>
    </td>
    </tr>
    <tr>
    <td class=\"box_grey\" align=\"left\"><span  class=\"small\">";
    if ($download_modem >= 1) {
        $return_string .= "Modem 56 kb = " . $download_modem . " <strong class=\"red\">min.</strong><br>";
    } else {
        $return_string .= "Modem 56 kb = " . $download_modem_2 . " <strong class=\"green\">sec.</strong><br>";
    }
    if ($download_adsl256 >= 1) {
        $return_string .= "ADSL (256 Kb) = " . $download_adsl256 . " <strong class=\"red\">min.</strong><br>";
    } else {
        $return_string .= "ADSL (256 Kb) = " . $download_adsl256_2 . " <strong class=\"green\">sec.</strong><br>";
    }
    /* if($download_fibra >= 1){
      $return_string .= "Fiber = ".$download_fibra." <strong class=\"red\">min.</strong><br>";
      }
      else{
      $return_string .= "Fiber = ".$download_fibra_2." <strong class=\"green\">sec.</strong><br>";
      } */
    $return_string .= "</span>
    </td>
    </tr>
    </table>";


    return $return_string;
}

#############################################
###   CREATE OPTIONS FROM ENUM DB FIELD
#
// $table         ->  (string) the db table
// $field         ->  (string) the table field i'd like to get the enum values
// $select_name   ->  (string) the name of the select form (html output)
// $posted        ->  (string) the $_POST informations if exists
// $rowed         ->  (string) the current db stored information if exits
// $add_null      ->  (bool)   add an empty intitial value
// $o_type        ->  (string) switch the output message

function enum_values_to_options($table, $field, $select_name, $posted, $rowed, $add_null, $o_type, $add_onchange_submit = false)
{
    $query = "SHOW COLUMNS FROM `" . DBT_PREFIX . str_replace("`", "", $table) . "` LIKE '" . $field . "'";
    sql_select($query, $results);

    settype($options, "string");

    while($line = mysql_fetch_array($results))
    {
        $open = strpos($line['Type'], '(');
        $close = strrpos($line['Type'], ')');
        // check if there are a string with () and if it conttains "enum" string at first
        if(!$open || !$close || !preg_match("#^enum#", $line['Type']))
        {
            return FALSE;
        }
        $options_array = substr($line['Type'], $open + 2, $close - $open - 3);
        $options_array = explode("','", $options_array);
        sort($options_array);

        $available_value = available_posted_rowed($posted, $rowed);

        if(count($options_array) > 1)
        {
            if($add_null == 1)
            {
                $options = "\n<option value=\"\">" . ucfirst(translate("select")) . "</option>";
                $selected_value = $available_value;
            }else
            {
                $selected_value = (($available_value) ? ($available_value) : $line['Default']);
            }
            switch ($o_type):
                /////// YES - NO
                case'yn':
                    foreach($options_array as $key => $val)
                    {
                        $options .="\n<option value=\"" . $val . "\" "
                                . is_selected_option($val, $selected_value);
                        if($val == 0)
                        {
                            $options .=" style=\"background-color:#CC0000; font-weight: bold; color:#FFFFFF; \">" . translate("no");
                        }else
                        {
                            $options .=" style=\"background-color:#399D2F; font-weight: bold; color:#FFFFFF; \">" . translate("yes");
                        }
                        $options .="</option>";
                    }//end foreach
                    return "\n<select name=\"" . $select_name . "\">" . $options . "\n</select>";
                    break;

                /////// ONLINE STATUS
                case'o_status':
                    foreach($options_array as $key => $val)
                    {
                        $options .="\n<option value=\"" . $val . "\" "
                                . is_selected_option($val, $selected_value);
                        if($val == 0)
                        {
                            $options .=" style=\"background-color:#CC0000; font-weight: bold; color:#FFFFFF; \">" . translate("offline");
                        }else
                        {
                            $options .=" style=\"background-color:#399D2F; font-weight: bold; color:#FFFFFF; \">" . translate("online");
                        }
                        $options .="</option>";
                    }//end foreach
                    return "\n<select name=\"" . $select_name . "\">" . $options . "\n</select>";
                    break;

                /////// RADIO
                case'radio':
                    foreach($options_array as $key => $val)
                    {
                        $options .="<input type=\"radio\" name=\"" . $select_name . "\" value=\"" . $val . "\" " . is_checked_option($val, $selected_value) . "><" . (($selected_value == $val) ? "strong class=\"red\"" : "span") . "> " . ((!is_numeric($val)) ? translate($val) : $val) . " </" . (($selected_value == $val) ? "strong" : "span") . "><br>";
                    }//end foreach
                    return "\n" . $options;
                    break;

                /////// SELECT
                default:
                    foreach($options_array as $key => $val)
                    {
                        $options .="\n<option value=\"" . $val . "\" "
                                . is_selected_option($val, $selected_value);
                        if(!is_numeric($val))
                        {
                            $options .=" >" . translate($val);
                        }else
                        {
                            $options .=" >" . $val;
                        }
                        $options .="</option>";
                    }//end foreach
                    return "\n<select name=\"" . $select_name . "\" " . (($add_onchange_submit === true) ? ("onChange=\"submit();\"") : "") . ">" . $options . "\n</select>";
                    break;
            endswitch;


            $options = "";
        } //end if($count)
    } //end while
}

#############################################
###   IS HEX_DEC Color?
#
// check if the string is a valid hexdex html color code. 6 char format after # are required

function is_hexdec_color($string)
{
    return (preg_match('/(^\#)+([a-f]|[A-F]|[0-9]){6}/', $string)) ? true : false;
}

#############################################
###   IS it an existant IMAGE?
#
// Check if file exists and if has a common image type extension (no mime check)
// Only Local file will return true

function is_image($string)
{
    return ((file_exists("$string")) && (is_image_file($string))) ? true : false;
}

#############################################
###   IS it a common IMAGE?
#
// Check if file exists and if has a common image type extension (no mime check)

function is_image_file($string)
{
    return (preg_match('#(\.jpg|\.jpeg|\.gif|\.png|\.bmp)$#i', $string)) ? true : false;
}

#############################################
###   IMAGE EXITS
#
// $string    ->  (string)  The desired image string to render
// $default   ->  (string)  A default image to render if $string doesn't exists or is not an image

function image_exists($string, $default)
{
    return (is_image($string)) ? ("$string") : ("$default");
}

#############################################
###   MISSING FILE ALERT
#
//  $string    ->  (string) The File path string to check
//  If ((!file_exists($string))||(is_dir($string))) an alert message is returned
//  Mostly usend on the public site area

function missing_file($string)
{
    if (trim($string)) {
        if ((!file_exists($string)) || (is_dir($string))) {
            return "\n<br><br>" . translate("exclaim_image") . "&nbsp;&nbsp;<strong class=\"red\">" . translate("missing_file") . "</strong>";
        } else {
            return "";
        }
    }
}

#############################################
###   MISSING FILE ALERT TO HELP EDITOR on CMS
#
//  $folder         ->  (string)  The directory path string location to the file
//  $file_string    ->  (string)  The File name string to check
//  1 check if there is a string to use
//  2 Strip the common folder location stored on the db from $file_string to prevent wrong path assignment
//  3 check if file exists and is not a folder
//  4 return an alert message if check fails

function missing_file_assigned($folder, $file_string)
{
    if (trim($file_string)) {
        $file_string = strip_default_folders(trim($file_string));
        if ((!file_exists($folder . $file_string)) || (is_dir($folder . $file_string))) {
            return "\n<br><br>" . translate("exclaim_image") . "&nbsp;&nbsp;<span class=\"middle_text\"><strong class=\"red\">" . translate("missing_file") . "</strong></span><br><br>";
        } else {
            return "";
        }
    }
}

#############################################
###   MISSING FILE CMS STOP TO HELP EDITOR on CMS
#
//  $folder         ->  (string)  The directory path string location to the file
//  $file_string    ->  (string)  The File name string to check
//  1 Strip the common folder location stored on the db from $file_string to prevent wrong path assignment
//  2 check if file exists and is not a folder
//  4 return true if check pass or false otherwise
// This function is used on CMS to stop saving setting whenever an existant image is mandatory

function this_file_exists($string, $folder)
{
    $string = strip_default_folders(trim($string));
    return ((!file_exists($folder . $string)) || (is_dir($folder . $string))) ? false : true;
}

#############################################
###   Returns SELECTED if option value == $_POST['value']
#
//  $id_string       -> (string)
//  $id_posted       -> (string)

function is_selected_option($id_string, $id_posted)
{
    if (strlen($id_string) > 0 && strlen($id_posted) > 0) {
        return (($id_string == $id_posted) ? " selected " : "");
    }
    return '';
}

#############################################
###   Returns CHECKED if radio/checkbox value == $_POST['value']
#
//  $id_string       -> (string)
//  $id_posted       -> (string)

function is_checked_option($id_string, $id_posted)
{
    if (strlen($id_string) > 0 && strlen($id_posted) > 0) {
        return (($id_string == $id_posted) ? " checked " : "");
    }
    return '';

}

#############################################
###    Available Posted <-> Rowed
#
//  If $posted is a non empty string
//  returns it's string otherwise returns $rowed
//  E.g. <input type=\"text\" name=\"pizza\"value=\"".available_posted_rowed($_POST['pizza'],$row['pizza'])."\">
//  $posted       -> (string)
//  $rowed        -> (string)

function available_posted_rowed($posted, $rowed)
{
    return (strlen(trim($posted)) > 0) ? trim($posted) : trim($rowed);
}

#############################################
###    PREVENT AJAX HTMLENTITIES DECODING
#

function recode_entities($string)
{
    global $special_chars_2;

    $special_chars_x = array_flip($special_chars_2);

    $string = strip_double_escape($string);

    return strtr($string, $special_chars_x);
}

#############################################
###    PROCESS plain text for db query
#
//  $string   ->  (string)  Intput string to process
//  $limit    ->  (int)     truncate input to $limit characters, default is a false bool
//  replace < and > to avoid HTML tags
//  without data loss (like strip_tags())
//  And prevent Single quot missescaping for db queries

function process_plain_text($string, $limit = false)
{   
    //process_input_text
    global $special_chars_2;
    $string = trim(stripslashes(strip_tags($string)));
    $string = str_replace("\\", "", $string);
    $string = ((is_numeric($limit)) ? (substr($string, 0, $limit)) : ($string));
    $patterns = array(
            '#&(?!.{1,6};)#is', // ampersand that is NOT followed by 1-6 characters + ; (prevent special characters code replacement)
            "#'|’#",
            "#“|”|\"#",
            "#>#",
            "#<#",
            //"#\s{2,}#" // moved out because of strange output when à á is present
    );
    $substitutions = array(
            "&amp;",
            "\'",
            "&quot;",
            "&gt;",
            "&lt;",
            //" "
    ); //&rsquo;
    $output_string = preg_replace($patterns, $substitutions, $string);
    $output_string = strtr($output_string, $special_chars_2);
    $output_string = preg_replace("# {2,}|\t{1,}(\s{1,})|\s{2,}#", " ", $output_string);
    return $output_string;
}

#############################################
###    PROCESS HTML for db query
#
//  Prevent Single quot missescaping for db queries
//  And recover MASKED <textarea> changed by return_textarea_text() for CMS Textarea fields usign HTML. See below

function process_html_text($string)
{
    $patterns = array(
            '#&(?!.{1,6};)#is', // ampersand that is NOT followed by 1-6 characters + ; (prevent special characters code replacement)
            "#'|’#",
            "#“|”#",
            '#\[\s{0,}textarea#is', //OPEN TEXTAREA
            '#\[\s{0,}/\s{0,}textarea\s{0,}\]#is'//CLOSE
    );
    $substitutions = array(
            "&amp;",
            "\'",
            "\"",
            '<textarea',
            '</textarea>'
    );
    $output_string = preg_replace($patterns, $substitutions, trim(stripslashes($string)));
    return "$output_string";
}

#############################################
###    RETURNS DATA FOR TEXTAREA
#
//  $string       -> (string) the text string to render as value on the Textarea
//  $allow_html   -> (string) switch html/plain returned content
//  Prevent NESTED <textarea> for CMS Textarea fields usign HTML
//  double_escape() mantains the HTML entities appearance on a <textarea> box instead of interpretation

function return_textarea_text($string, $allow_html)
{
    switch ($allow_html):

        case'html':
            $patterns = array(//prevent nested textarea
                    '#<\s{0,}textarea#is', //OPEN TEXTAREA
                    '#</\s{0,}textarea\s{0,}>#is'   //CLOSE
            );
            $substitutions = array('[textarea', '[/textarea]');

            return preg_replace($patterns, $substitutions, double_escape(trim(stripslashes($string))));
            break;

        case'plain':
            $patterns = array('"', ">", "<");
            $substitutions = array("&quot;", "&gt;", "&lt;"); //&rsquo;

            return double_escape(str_replace($patterns, $substitutions, trim(stripslashes($string))));
            break;

    endswitch;
}

#############################################
###   RETURNS DATA FOR INPUT TEXT FIELD
#
//  $string       -> (string) the text string to render as value on the <input type="text">
//  $limit        ->  (int)     truncate input to $limit characters, default is a false bool
//  Prevent new line for CMS Textarea fields usign HTML
//  replace < and > to avoid HTML tags
//  without data loss (like strip_tags())
//  And prevent Single quot missescaping for db queries
//  double_escape() mantains the HTML entities appearance on a <textarea> box instead of interpretation

function return_input_text($string, $limit = false)
{

    $string = ((is_numeric($limit)) ? (substr($string, 0, $limit)) : ($string));

    $patterns = array('"', ">", "<");
    $substitutions = array("&quot;", "&gt;", "&lt;"); //&rsquo;

    return double_escape(str_replace($patterns, $substitutions, strip_new_line(trim(stripslashes($string)))));
}

/* * ***
 * **
 * *
 * *   DB PROCESS
 * *
 * *
 */

/**
 * SMART SINGLE FIELD DB QUERY RETURN  VALUE
 * @param <type> $table       Database.Table
 * @param <type> $field_1     Table.Field for WHERE condition to compare with value
 * @param <type> $return_requested_field Table.Field for return value
 * @param <type> $where_extended add a WHERE condition.      E.g. (id IS NOT NULL) AND ...]
 * @param <type> $value VALUE for WHERE condition
 * @param <type> $where_condition_closing if need for example an ending Parenthesis ")" to complete the $where_extended code
 * @return <type>
 * null value will send if no results found
 *
 * expl.       $query = "SELECT  $field_1 ,  $return_requested_field FROM `DBT_PREFIX.str_replace("`","",$table)` WHERE  $where_extended $field_1='$value '  $where_condition_closing ;
 */
function smart_recall($table, $field_1, $return_requested_field, $where_extended, $value, $where_condition_closing)
{
    if (trim($value) && trim($field_1) && trim($return_requested_field))
    {
        $query = "SELECT " . $field_1 . ", "
                . $return_requested_field
                . " FROM `" . DBT_PREFIX . str_replace("`", "", $table) . "`"
                . " WHERE " . $where_extended
                . " " . $field_1 . "='" . $value . "' "
                . $where_condition_closing
                . "";
        sql_select($query, $results);
        if(mysql_num_rows($results) > 0)
        {
            while($row = mysql_fetch_array($results))
            {
                return $row[$return_requested_field];
            }
        }else
        {
            return NULL;
        }
    }else
    {
        return NULL;
    }
}


/**
 * SMART SINGLE FIELD DB VALUE INSERT
 * @param string $table Database.Table
 * @param mixed $field Table.Field destination data
 * @param mixed $value VALUE to insert on Database.Table.Field
 * @param string $where_extended add a WHERE condition.      E.g. (id IS NOT NULL) AND ...]
 * @param string $explained Explained field name for empty input error message
 * @return <type>
 * Check if $value already exits on Database.Table.Field
 * 
 * Example = $query = "SELECT $field FROM $table WHERE $where_extended `$field` ='$value';
 */
function smart_insert($table, $field, $value, $where_extended, $explained)
{
    if(trim($value))
    {
        $table = "`" . DBT_PREFIX . str_replace("`", "", $table) . "`";
        // CHECK if value already exists
        $query = "SELECT " . $field
                . " FROM " . $table
                . " WHERE " . $where_extended
                . " `" . $field . "` ='" . $value . "'";

        sql_select($query, $results);
        if (mysql_num_rows($results) > 0)
        {
            echo translate("exclaim_image")
            . " <strong> <span class=\"red\">\" The value <span class=\"green\">"
            . $value . "</span> for -&gt; <span class=\"green\">"
            . $explained . "</span> already exists !!\"</span>" . translate("ammend") . ".</strong><br><br>";
            return false;
        }
        // do INSERT
        else 
        {
            $query = "INSERT INTO " . $table
                    . " (" . $field . ") VALUES ('" . $value . "')";

            sql_select($query, $results);
            // returns last inserted value id
            if($results)
            {
                $query = "SELECT `rowid`, " . $field
                        . " FROM " . $table
                        . " ORDER BY `rowid` DESC limit 1";
                sql_select($query, $results);
                while($row = mysql_fetch_array($results))
                {
                    $control_value = $row[$field];
                    if($control_value == $value)
                    {
                        return $row['rowid'];
                    }
                    // error message on insert error
                    else 
                    {
                        echo translate("exclaim_image")
                            . "<strong><span class=\"red\">\" DB error !!\"</span>" 
                            . translate("ammend") . ".</strong>
                            <br><br>";
                        return false;
                    }
                }
            }
            // error message on insert error
            else 
            {
                echo translate("exclaim_image")
                    . "<strong><span class=\"red\">\" DB error !!\"</span>" 
                    . translate("ammend") . ".</strong><br><br>";
                return false;
            }
        }
    }
    // error message on empty input value
    else
    {
        echo translate("exclaim_image")
        . "<strong>
      <span class=\"red\">\" The value for <span class=\"green\">Custom " . $explained . "</span> is empty !!\"</span>
      " . translate("ammend") . ".</strong>
      <br><br>";
        return false;
    }
}

#############################################
###   CHECK NULL VAL
#
// Used on db queries wherever a field could be a NULL value
// if $string return '$string'
// else return NULL

function check_null_val($string)
{
    return (strlen($string) > 0 && $string !== "" && $string !== null) ? "'$string'" : "NULL";
}

#############################################
###   RETURNS on/off STATE
#
// Used on db queries wherever a field could be a 1/0 value
// if $string return '1'
// else return '0'

function check_on_off($string)
{
    if(trim($string))
    {
        return "1";
    }else
    {
        return "0";
    }
}

#############################################
###   VALIDATE DD-MM-YYYY date format
#

function is_valid_date_format($string)
{
    //checkdate(int $month , int $day , int $year )
    $date_splitted = @explode("/", $string);
    return ((preg_match('#(\d{2})/(\d{2})/(\d{4})#ix', $string)) //VALID DD-MM-YYYY
    &&
    (@count($date_splitted) == 3)
    &&
    (is_int($date_splitted[1] . $date_splitted[0] . $date_splitted[2]))
    &&
    (checkdate($date_splitted[1], $date_splitted[0], $date_splitted[2]))
    ) ? true : false;
}

##########################################  VALIDATE DATES
// ACCEPT 2 Arguments  FIRST (value to check)  SECCOND (field name to translate)
// $string        ->  (string)  value to check for valid date format
// $field_name    ->  (string)  field name to return translated message

function validate_date($string, $field_name)
{
    if(is_valid_date_format($string))
    {
        return true;
    }else
    {
        echo "<br>" . ALERT_BOX_OPEN . " <strong>[ " . translate($field_name) . " ]</strong><br>" . translate("is_not_a_valid_date") . " (" . translate("valid_date_format") . "). " . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }
}

#############################################
###         VALIDATE REFERER
#
// ACCEPT 2 Arguments  FIRST (true/false)  SECCOND (string/false)
// if $return_message = true, returns an error message on failure, else just return FALSE
// if $message_content <> 0, use the argument input value for the message, otherwise use the default
// Check IF there is a referer, IF referer contains the HTTP_HOST AND IF referer has the MAIN_DOMAIN as starting of the string
//  $return_message       -> (bool)
//  $message_content      -> (bool)

function check_referer($return_message = 0, $message_content = 0)
{
    if((DEFINED_HTTP_REFERER)
            && (preg_match("#^" . MAIN_DOMAIN . "#is", DEFINED_HTTP_REFERER))
            && (preg_match("#" . DEFINED_HTTP_HOST . "#is", DEFINED_HTTP_REFERER)))
    {
        return true;
    }else
    {
        if($return_message == 1)
        {
            echo (($message_content) ? $message_content : ("<br>" . ALERT_BOX_OPEN . "<strong class=\"red\">" . translate("email_send_error") . "</strong>" . ALERT_BOX_CLOSE . ""));
        }
        return false;
    }
}

#############################################
###         VALIDATE EMAIL ADDRESS
#
// Check if e-mail string is a common e-mail valid address

function validate_email($email_string)
{
    if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", trim($email_string)))
    {
        return true;
    }else
    {
        echo ALERT_BOX_OPEN . translate("error_email_not_valid") . " (" . strip_double_escape(return_input_text($email_string, $limit = true)) . ")<br>" . translate("ammend") . ALERT_BOX_CLOSE . "<br>";
        return false;
    }
}

#############################################
###         VALIDATE EMAIL ADDRESS ON CMS EDITOR
#
// Alias of validate_email() adding return true on empty e-mail string

function validate_email_editor($email_string)
{
    if($email_string)
    {
        return validate_email($email_string);
    }else
    {
        return true;
    }
}

#############################################
###      VALIDATE USER NAME
#

function namevalid($string)
{
    // strspn() string parameter to compare
    $span_str = "abcdefghijklmnopqrstuvwxyz" .
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";

    // Must contain at least one $span_str character
    if(strspn($string, $span_str) == 0)
    {
        echo "<br>" . ALERT_BOX_OPEN . "(" . strip_double_escape(return_input_text($string)) . ") "
            . translate("error_name_not_allowed") . "<br>" . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }
    // Must contains all valid characters
    elseif(!strspn($string, $span_str))
    {
        echo "<br>" . ALERT_BOX_OPEN . "(" . strip_double_escape(return_input_text($string)) . ") "
        . translate("error_name_not_allowed") . "<br>" . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }

    // Check for forbidden names
    elseif(eregi("^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$", $string))
    {
        echo "<br>" . ALERT_BOX_OPEN . "(" . strip_double_escape(return_input_text($string)) . ") "
        . translate("error_name_not_allowed") . "<br>" . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }elseif(eregi("^(anoncvs_)", $string))
    {
        echo "<br>" . ALERT_BOX_OPEN . "(" . strip_double_escape(return_input_text($string)) . ") "
        . translate("error_name_not_allowed") . "<br>" . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }else
    {
        return true;
    }
}

#############################################
###      VALIDATE POST MIN LENGTH
#
// MIN_PASS_CHR_REQUIRED is a defined constant available from DB table administration_defaults
//  $field_name             -> (string)
//  $field_value            -> (string)
//  $min_length_required    -> (int)

function validate_min_length($field_name, $field_value, $min_length_required = MIN_PASS_CHR_REQUIRED)
{
    if(strlen(trim($field_value)) < $min_length_required)
    {
        echo "<br>" . ALERT_BOX_OPEN . translate("the_field") . " <strong>[ " . translate($field_name) . " ]</strong> "
        . translate("must_contains_at_least") . " " . $min_length_required . " " . translate("charcters") . "<br>" . translate("ammend") . ALERT_BOX_CLOSE;
        return false;
    }else
    {
        return true;
    }
}

/* * ***
 * **
 * *
 * *   USEFULL TOOLS
 * *
 * *
 */

#############################################
###      ORDER BY QUERY STRING VALIDATION
#
// 	validate &_GET order_by value to avoid SQL Injection or restuls errors by maipulation of query string
//  $accept_fields   -> (string) will be converted to array to accept values on public site

function validate_order_by($accept_fields = "")
{

    if (req("order_by")) {
        $accept_fields = explode(",", $accept_fields);

        $order_split = explode("-", req("order_by"));
        $order_split['0'] = str_replace(array("'", "(", ")", "|", "$"), '', $order_split['0']);

        // ADMINISTRATION PERMISSION VOID THIS CONTROL ( to skip excesive coding on Control Panel)
        if (isset($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_ID']) && isset($_SESSION[LOCAL_IDENTIFIER . '_ADMIN_NAME'])) {
            return "`" . (eregi("[_a-z0-9]+$", $order_split['0']) ? $order_split['0'] : "") . "` "
            . (preg_match('#^(ASC|DESC)$#i', $order_split['1']) ? strtoupper($order_split['1']) : "") . " ";
        }
        // COMMON USERS HAS LIMITS
        else {
            if (isset($accept_fields) && is_array($accept_fields) && isset($order_split['0']) && in_array($order_split['0'], $accept_fields)) {
                return "`" . (eregi("[_a-z0-9]+$", $order_split['0']) ? $order_split['0'] : "") . "` "
                . (preg_match('#^(ASC|DESC)$#i', $order_split['1']) ? strtoupper($order_split['1']) : "") . " ";
            }
        }
    }
}

#############################################
###      ORDER BY FORM GENERATION
#
//  $order_list         ->  List of ordering fields definitions             E.g.  "rowid,title,parent,language_id,show_in_home_page"
//  $text               ->  Rendered text on options                        E.g.  "rowid,title,parent_category,language,show_in_home_page"
//  $starting_value     ->  starting value on query string  MANDATORY       E.g.  "section,".req("section").""
//  $included_vars      ->  optional included vars to add on query string   E.g.  "p_offset,filter"
//   Full E.g. order_by("rowid,title,parent,language_id,show_in_home_page","ID,Title,Parent category,Language,Show in home page","section,".req("section")."","p_offset,filter");
//  $order_list and $text will be splitted into arrays and must have the same quantity of elements
//  $order_list will be used as option values
//  $text will be used as a key for translation translate() and then rendered as option text
//  This function is almost exclusive for the CMS control panel, but could be used (with carefull) on public site

function order_by($order_list, $text, $starting_value, $included_vars)
{

    $order_list = explode(",", $order_list);
    $order_list_text = explode(",", $text);
    $included_vars_list = explode(",", $included_vars);
    $starting_value = explode(",", $starting_value);

    $starting_value_string = DEFINED_SCRIPT_FILENAME . "?" . $starting_value['0'] . "=" . $starting_value['1'];

    $included_vars_chain = $starting_value_string;

    foreach ($included_vars_list as $key => $val) {
        if (req($val)) {
            $included_vars_chain .= "&amp;" . $val . "=" . urlencode(req($val));
        }
    }

    $return_string = "\n<select name=\"quick_nav\" onchange=\"go(this)\" title=\"set_order\">";
    $return_string .= "\n<option value=\"\">" . translate("order_by") . ":</option>";


    for ($counter = 0; $counter < count($order_list); $counter++) {

        if (is_even($counter)) {
            $style = " style=\"background-color:#EEEBDD;\" ";
        } else {
            $style = "";
        }

        //DESC
        $return_string .= "
      \n<option $style value=\""
                . $included_vars_chain
                . "&amp;order_by=" . $order_list[$counter] . "-DESC";


        $return_string .= "\"";
        $return_string .= is_selected_option($order_list[$counter] . "-DESC", req("order_by"));
        $return_string .= ">" . translate($order_list_text[$counter]) . " Desc [+ -]</option>";

        //ASC
        $return_string .= "
      \n<option  $style  value=\""
                . $included_vars_chain
                . "&amp;order_by=" . $order_list[$counter] . "-ASC";


        $return_string .= "\"";
        $return_string .= is_selected_option($order_list[$counter] . "-ASC", req("order_by"));
        $return_string .= ">" . translate($order_list_text[$counter]) . " Asc [- +]</option>";
    }

    $return_string .= "\n<option style=\"background-color:#FFCC00;font-weight: bold;\" value=\""
            . $starting_value_string
            . "\">RESET</option>
    </select>";

    return $return_string;
}

#############################################
###      CREATE OPTIONS SELECTOR
#
//  $database_table     ->  The database table to retrive information from        E.g.  "user_preferred_colours"
//  $selector_name      ->  The form field name to use                            E.g.  "user_preferred_colours"
//  $order_by           ->  The field table fieldname to use to order items       E.g.  "`ordine`"
//  $output_type        ->  The output type we want to obtain                     E.g.  "checkbox"
//  $current_selection  ->  The current selected value/values                     E.g.  "array("red","blue")"
//  $current_selection will be exploded from arrays whenever needed

function create_options_selector($database_table, $selector_name, $order_by, $output_type, $current_selection)
{
    $query = "SELECT * FROM `" . DBT_PREFIX . $database_table . "` ORDER BY " . (($order_by) ? $order_by : (" `rowid` ")) . " ";

    sql_select($query, $results);

    if (mysql_num_rows($results) > 0) {

        switch ($output_type):
            case'checkbox':

                settype($return_string, "string");
                $return_string = "";

                $current_selection = ((is_array($current_selection) && isset($current_selection)) ? $current_selection : "");

                while ($row = mysql_fetch_assoc($results)) {

                    $return_string .= "\n<input name=\"" . $selector_name . "[]\" value=\"" . $row['rowid'] . "\" type=\"checkbox\" ";
                    $return_string .= ( (is_array($current_selection) && in_array($row['rowid'], $current_selection)) ? (" checked ") : (""));
                    $return_string .= ">  &nbsp; \n";
                    $return_string .= "\n" . (($row['lang_' . CURRENT_LANGUAGE]) ? ($row['lang_' . CURRENT_LANGUAGE]) : ($row['lang_' . DEFAULT_LANGUAGE])) . "<br>";
                }

                break;


            case'message':

                settype($return_string, "string");
                $return_string = "";

                $current_selection = ((is_array($current_selection) && isset($current_selection)) ? $current_selection : "");

                while ($row = mysql_fetch_assoc($results)) {
                    if (is_array($current_selection) && in_array($row['rowid'], $current_selection)) {
                        $return_string .= "\n&middot; " . (($row['lang_' . CURRENT_LANGUAGE]) ? ($row['lang_' . CURRENT_LANGUAGE]) : ($row['lang_' . DEFAULT_LANGUAGE])) . "<br>";
                    }
                }

                break;

        endswitch;

        return $return_string;
    } else {
        return;
    }
}

//////////////////////////////////////

function resample_picfile($src, $dst, $w, $h)
{
    // If distortion stretching is within the range below,
    // then let image be distorted.
    $lowend = 0.8;
    $highend = 1.25;

    $src_img = imagecreatefromjpeg($src);
    if ($src_img) {
        $dst_img = ImageCreateTrueColor($w, $h);
        /*
          if you don't want aspect-preserved images
          to have a black bkgnd, fill $dst_img with the color of your choice here.
         */

        if ($dst_img) {
            $src_w = imageSX($src_img);
            $src_h = imageSY($src_img);

            $scaleX = (float) $w / $src_w;
            $scaleY = (float) $h / $src_h;
            $scale = min($scaleX, $scaleY);

            $dstW = $w;
            $dstH = $h;
            $dstX = $dstY = 0;

            $scaleR = $scaleX / $scaleY;
            if ($scaleR < $lowend || $scaleR > $highend) {
                $dstW = (int) ($scale * $src_w + 0.5);
                $dstH = (int) ($scale * $src_h + 0.5);

                // Keep pic centered in frame.
                $dstX = (int) (0.5 * ($w - $dstW));
                $dstY = (int) (0.5 * ($h - $dstH));
            }

            imagecopyresampled(
                    $dst_img, $src_img, $dstX, $dstY, 0, 0,
                    $dstW, $dstH, $src_w, $src_h);
            imagejpeg($dst_img, $dst);
            imagedestroy($dst_img);
        }
        imagedestroy($src_img);
        return file_exists($dst);
    }
    return false;
}

//////////////////////////////////////
function add_watermark($source_img, $watermark_img, $destination_folder, $filename)
{
    $watermark = new watermark();

    if ($source_img && $watermark_img) {

        $main_img_obj = imagecreatefromjpeg($source_img);
        $watermark_img_obj = imagecreatefrompng($watermark_img);

        # create our watermarked image
        $return_img_obj = $watermark->create_watermark($main_img_obj, $watermark_img_obj, $alpha_level = 100, $watermark_v_position = WATERMARK_V_POSITION, $watermark_h_position = WATERMARK_H_POSITION);

        # create watermarked image
        imagejpeg($return_img_obj, $destination_folder . $filename, 80);
    }
}

/**
 * This function converts m/sec into other speed messure units 
 * 
 * @param mixed $data        Incoming value
 * @param mixed $new_unit    Convert to  (kph/fps/mph)
 */
function mps_speed_convertion($data, $new_unit)
{

    switch ($new_unit):
        case'm/s':
            return $data;
            break;
        case'km/h':
            return $data * 3.6;
            break;
        case'ft/s':
            return $data * 3.2808;
            break;
        case'mph':
            return $data * 2.2369;
            break;
    endswitch;
}
/**
 * 
 * @param type $texto
 * @param type $enca
 * @param type $tipo
 */
//function mensaje($texto,$enca="",$tipo="success",$redireccionar="")
function mensaje($texto,$enca="",$tipo="success")
{
    if($tipo=="") $tipo="success";
    echo "<script type=\"text/javascript\">";
    if($enca=="")
    {
        echo "toastr.{$tipo}(\"".$texto."\");";
    }else
    {
        echo "toastr.{$tipo}(\"".$texto."\",\"".$enca."\");";
    }
    /*
    if(!empty($redireccionar))
    {
        echo "window.location=\"".$redireccionar."\";";
    }
     * 
     */
    echo "</script>";
}
/**
 * 
 * @param type $texto
 */
function mensaje2($texto="")
{
    echo "<script type=\"text/javascript\">";
    echo "  alert('{$texto}')";
    echo "</script>";
}
/**
 * 
 * @param type $pagina
 */
function redireccionar($pagina="/")
{
    echo "<script type=\"text/javascript\">";
    echo "  window.location=\"".$pagina."\";";
    //echo "      location.reload();";
    echo "</script>";
}
/**
 * 
 * @return string
 */
function get_current_url()
{
    $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
    $complete_url = $base_url . $_SERVER["REQUEST_URI"];
    return $complete_url;
}
/**
 * 
 * @param type $varreq
 * @param type $method
 * @param type $sanitise
 * @return type
 */
function req($varreq, $method = "REQUEST", $sanitise = true)
{
    if((isset($_REQUEST[$varreq])) && ($_REQUEST[$varreq] <> ""))
    {
        // PREVENT SQL INJECTION
        switch ($method):
            case'POST':
                $return_string = ((isset($_POST[$varreq])) ? trim($_POST[$varreq]) : NULL);
                break;

            case'GET':
                $return_string = ((isset($_GET[$varreq])) ? trim($_GET[$varreq]) : NULL);
                break;

            default: //REQUEST
                $return_string = trim($_REQUEST[$varreq]);
                break;
        endswitch;
    }else
    {
        $return_string = null;
    }
    return (($sanitise === true) ? (str_replace(array("--", "'", "\'", "`", "<", ">"), "", $return_string . '')) : $return_string . '');
}

function validate_two_dates($date1="",$date2="") //fecha inicial , fecha final
{
    if(is_valid_date_format($date1) AND is_valid_date_format($date2))
    {
        $date1a= date_to_mktime($date1);
        $date2a= date_to_mktime($date2);
        if($data1a<$date2a) return true;
    }
    return false;
}

function date_to_mktime($date1="")
{
    $date2=explode("/",$date1);
    if(count($date2)==3)
    {
        return mktime(0,0,0,$date2[1],$date2[0],$date2[2]);
    }
    return false;
}
?>