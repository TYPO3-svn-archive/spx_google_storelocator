<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2013 Raphael Heilmann <rh@stuttgartmedia.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once(PATH_tslib . 'class.tslib_pibase.php');


/**
 * Plugin 'Store Locator' for the 'spx_google_storelocator' extension.
 *
 * @author    Raphael Heilmann <rh@stuttgartmedia.de>
 * @package    TYPO3
 * @subpackage    tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_pi1 extends tslib_pibase {
    var $prefixId = 'tx_spxgooglestorelocator_pi1'; // Same as class name
    var $scriptRelPath = 'pi1/class.tx_spxgooglestorelocator_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 'spx_google_storelocator'; // The extension key.
    var $pi_checkCHash = true;

    var $last_address = '';


    function after($this1, $inthat) {
        if (!is_bool(strpos($inthat, $this1))) {
            return substr($inthat, strpos($inthat, $this1) + strlen($this1));
        }
    }

    function after_last($this1, $inthat) {
        if (!is_bool($this->strrevpos($inthat, $this1))) {
            return substr($inthat, $this->strrevpos($inthat, $this1) + strlen($this1));
        }
    }

    function before($this1, $inthat) {
        return substr($inthat, 0, strpos($inthat, $this1));
    }

    function before_last($this1, $inthat) {
        return substr($inthat, 0, $this->strrevpos($inthat, $this1));
    }

    function between($this1, $that, $inthat) {
        return $this->before($that, $this->after($this1, $inthat));
    }

    function between_last($this1, $that, $inthat) {
        return $this->after_last($this1, $this->before_last($that, $inthat));
    }

    function strrevpos($instr, $needle) {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        }
        else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    function get_webpage($url) {
        global $db;
        if (false) {
            $sessions = curl_init();
            curl_setopt($sessions, CURLOPT_URL, $url);
            curl_setopt($sessions, CURLOPT_HEADER, 1);
            curl_setopt($sessions, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($sessions);
        }
        else {
            $data = file_get_contents($url);
        }
        return $data;
    }

    function geocode_address($complete_address) {
        $address = $complete_address['address'];
        $city = $complete_address['city'];
        $state = $complete_address['state'];
        $country = $complete_address['country'];
        $zipcode = $complete_address['zipcode'];

        $apiURL = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode("$address,$city,$state,$zipcode,$country").'&sensor=false&language=de';
        $addressData = $this->get_webpage($apiURL);
        $adr = json_decode($addressData);
        $this->last_address = $adr->results[0]->formatted_address;

        return ($adr->results[0]->geometry->location);
    }

    function update_lat_lon() {
        $query = "SELECT * FROM tx_spxgooglestorelocator_locations WHERE lat = '' OR lon = '' ";
        $result = $GLOBALS['TYPO3_DB']->sql_query($query);
        //echo mysql_error();
        while ($rows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
            if ($rows['uid']) {
                $zip = $rows['zip'];
                $complete_address['address'] = $rows['address'];
                $complete_address['city'] = $rows['city'];
                $complete_address['state'] = $rows['state'];
                $complete_address['zip'] = $rows['zip'];
                $complete_address['country'] = $rows['country'];
                $lat_lon = $this->geocode_address($complete_address);
                $lat = $lat_lon->lat;
                $lon = $lat_lon->lng;
                $uid = $rows['uid'];
                $query = "UPDATE tx_spxgooglestorelocator_locations SET   lat = '$lat', lon = '$lon' WHERE uid = '$uid' ";
                $GLOBALS['TYPO3_DB']->sql_query($query);
            }
        }
    }

    function get_categories() {
        $query = "SELECT * FROM  tx_spxgooglestorelocator_categories WHERE 1 ORDER BY sorting";
        $res = $GLOBALS['TYPO3_DB']->sql_query($query);
        $out = array();
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $out[] = $row['name'];
        }
        return $out;
    }

    function inradius($complete_address, $radius) {
        global $TYPO3_CONF_VARS;
        $_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['spx_google_storelocator']);

        //If there are any limitations on the results set, get them here.
        $results_limit = $_EXTCONF['enable.']['ResultsLimit'];

        if ($results_limit > 0) {
            $results_limit = "LIMIT 0,$results_limit";
        }

        //Check to see if we already have starting lat/lon so we dont do double geocoding
        if (!$complete_address['lat_lon']->lat && !$complete_address['lat_lon']->lng) {
            $lat_lon = $this->geocode_address($complete_address);
        }
        else {
            $lat_lon = $complete_address['lat_lon'];
        }
        $lat = trim($lat_lon->lat);
        $lon = trim($lat_lon->lng);

        //Add in categories to the search criteria
        //If they are not an array, make them an array.
        $categories = $complete_address['categories'];
        if (!is_array($categories)) {
            $categories = explode(',', $categories);
        }

        //Loop through and add each category to the query.
        $category_search = "(";
        $counter = 0;
        foreach ($categories AS $cat) {
            $cat = trim($cat);
            if ($counter == 0) {
                $category_search .= " categories LIKE '%$cat%' ";
            }
            else {
                if ($cat != '') {
                    $category_search .= " OR categories LIKE '%$cat%' ";
                }
            }
            $counter++;
        }
        $category_search .= ")";
        if (sizeof($categories) == 1 && $categories[0] == '') {
            $category_search = "categories != ''";
        }

        $pi = M_PI;
        $query = "SELECT *,(((acos(sin(($lat*$pi/180)) * sin((lat*$pi/180)) + cos(($lat*$pi/180)) *  cos((lat*$pi/180)) * cos((($lon - lon)*$pi/180))))*180/$pi)*60*1.423) as distance FROM tx_spxgooglestorelocator_locations HAVING distance <= $radius AND $category_search AND deleted!='1' ORDER BY distance ASC $results_limit";

        return ($GLOBALS['TYPO3_DB']->sql_query($query));
    }

    function prepare_map_results($mysql_results) {
        $retval = '';
        //If the seek line is missing, then the results will never show.  Do not remove the seek line.
        mysql_data_seek($mysql_results, 0);
        while ($rows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($mysql_results)) {;
            $lat = floatval($rows['lat']);
            $lon = floatval($rows['lon']);
            $address = addslashes($rows['address']);
            $city = addslashes($rows['city']);
            $state = addslashes($rows['state']);
            $zip = addslashes($rows['zip']);
            $country = addslashes($rows['country']);
            $storename = addslashes($rows['storename']);
            $hours = addslashes($rows['hours']);
            $url = addslashes($rows['url']);
            $image = addslashes($rows['image']);

            $icon = "uploads/tx_spxgooglestorelocator/icons/" . addslashes($rows['icon']);
            if (!is_file($icon)) {
                $icon = "uploads/tx_spxgooglestorelocator/icons/starlarge_black.gif";
            }

            $phone = addslashes($rows['phone']);
            $image_url = addslashes($rows['image_url']);
            $use_coordinate = addslashes($rows['use_coordinate']);
            $distance = number_format($rows['distance'], 2, '.', '');

            if ($lat != 0 && $lon != 0) {
                if ($use_coordinate == 'on') {
                    $address1 = "$lat,$lon";
                }
                else {
                    $address1 = "$address,$city,$state $zip $country";
                }

                $html = '';

                if ($image != '' && display_image_in_balloon == 'on') {
                    if ($image_url != '') {
                        $html .= "<a href=\"$image_url\"><img style=\"float: left\" src=\"$image\" width=104 height=124></a>";
                    }
                    else {
                        $html .= "<img style=\"float: left\" src=\"$image\" width=104 height=124>";
                    }
                }

                if ($url == '') {
                    $html .= "<b>$storename</b><br>$address<br>$city, $state $zip $country<br>$phone";
                }
                else {
                    $html .= "<b><a href=\"$url\">$storename</a></b><br>$address<br>$city, $state $zip $country<br>$phone";
                }


                $retval .= "createMarker('$lat', '$lon', '$storename', '$html', '$storename', '$icon', '$address1');\n";

            }
        }

        return ($retval);

    }

    function show_form($config) {
        $returnval = "
      <form method=\"post\" action=\"" . $this->pi_getPageLink($GLOBALS['TSFE']->id) . "\">
	<table class=\"tx_spxgooglestorelocator_searchform\">
		<tr><td>" . htmlspecialchars($this->pi_getLL('zip_label')) . "</td>
			<td><input type=\"text\" name=\"zipcode\"></td><td rowspan=\"4\">
      " . htmlspecialchars($this->pi_getLL('categories_label')) . " <br>
      <select name=\"categories[]\" multiple style=\"width:200px;height:50px\" >";

        $categories_list = $this->get_categories();

        foreach ($categories_list AS $cat) {
            $cat = trim($cat);
            $returnval .= "<option>" . $cat . "</option>";
        }

        $returnval .= "</select></td></tr>
      <tr><td>" . htmlspecialchars($this->pi_getLL('street_label')) . "</td>
			<td><input type=\"text\" name=\"address\"></td></tr>
      <tr><td>" . htmlspecialchars($this->pi_getLL('city_label')) . "</td>
			<td><input type=\"text\" name=\"city\"></td></tr>
      <tr><td>" . htmlspecialchars($this->pi_getLL('country_label')) . "</td>
			<td><select name=\"country\">";

        $country_array = split('[,]', $config['enable.']['Countrycodes']);
        foreach ($country_array AS $country_iso) {
            $result = $GLOBALS['TYPO3_DB']->sql_query("SELECT cn_short_de FROM static_countries WHERE cn_iso_2 = '$country_iso';");
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
            $returnval .= "<option value=\"" . $country_iso . "\">" . $row['cn_short_de'] . "</option>";
        }
        $returnval .= "</select></td></tr>
      <tr><td colspan=\"3\">" . htmlspecialchars($this->pi_getLL('radius_label')) . "
      <select name=\"radius\">";

        $radius_array = split('[,]', $config['enable.']['Radius']);
        foreach ($radius_array AS $radius) {
            $returnval .= "<option>" . $radius . "</option>";
        }
        $returnval .= "</select>
      <input type=submit name=\"submit\" value=\"" . htmlspecialchars($this->pi_getLL('submit_button_label')) . "\">
      </td></tr></table>
      </form>";

        return ($returnval);
    }

    /**
     * The main method of the PlugIn
     *
     * @param    string $content: The PlugIn content
     * @param    array $conf: The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    function main($content, $conf) {
        global $TYPO3_CONF_VARS, $_POST;
        $_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['spx_google_storelocator']);
        $content = '';

        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        $TCE = t3lib_div::makeInstance('t3lib_TCEmain');
        $TCE->admin = 1;
        $TCE->clear_cacheCmd('pages');
        $TCE->clear_cacheCmd($GLOBALS['TSFE']->id);

        $this->update_lat_lon();
        if ($_POST['submit'] == '') {
            $content = $this->show_form($_EXTCONF);
        }
        else {
            // Variables used in eval'd js code
            $zoom = 5; //get_setting("zoom", $db);//0 to 17, 17 being Country view, 0 being Street closeup
            $google_key = $_EXTCONF['enable.']['GoogleAPIKey']; //Google API Key
            define("display_image_in_results", $_EXTCONF['enable.']['DisplayImageInResults']); //This will display the image in the results table.  yes/no
            define("display_image_in_balloon", $_EXTCONF['enable.']['DisplayImageInBalloon']); //This will display the image in the little red balloons on the map. yes/no

            //Set the center lat/lon before we enter inradius so we dont do double geocoding.  This speeds things up significantly.
            $center_lat_lon = $this->geocode_address($_POST);
            $searchAddress = $this->last_address;
            $_POST['lat_lon'] = $center_lat_lon;

            $mysql_results = $this->inradius($_POST, $_POST['radius']);

            $center_lat = $center_lat_lon->lat;
            $center_lon = $center_lat_lon->lng;
            // Variables used in eval'd js code
            $center_lat_float = floatval($center_lat);
            $center_lon_float = floatval($center_lon);

            $content .= "<div id=\"map\" class=\"tx_spxgooglestorelocator_map\"></div>
                   <div id=\"results\" class=\"tx_spxgooglestorelocator_results\">";

            $content .= $this->pi_getLL('searched_near').' '.$searchAddress."<br />\n";

            if ($GLOBALS['TYPO3_DB']->sql_num_rows($mysql_results) == 0) {
                $content .= $this->pi_getLL('no_results_found');
            }
            else {
                $content .= $this->pi_getLL('results_found')."<br />\n";
                $counter = 0;
                while ($rows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($mysql_results)) {
                    $counter++;
                    $storename = $rows['storename'];
                    $distance = number_format($rows['distance'], 2, '.', '');
                    $content .= 'in ' . $distance . ': ' . $storename . "<br /> \n";
                }
                $content .= "</div>";

                // Variable used in eval'd js code
                $map_results = $this->prepare_map_results($mysql_results);

                $mapicon_you = "uploads/tx_spxgooglestorelocator/icons/starlarge_black.gif";
                // Variables used in eval'd js code
                list($mapicon_you_width, $mapicon_you_height, $type, $attr) = getimagesize("$mapicon_you");

                $jsfile = addslashes(file_get_contents(t3lib_extMgm::extPath('spx_google_storelocator','map.js')));
                $javascript = '';
                eval("\$javascript = \"$jsfile\";");
                $content .= '<script src="https://maps.googleapis.com/maps/api/js?key='.$google_key.'&sensor=false&language=de" type="text/javascript"></script>
                <script type="text/javascript">'.stripslashes($javascript).'</script>';
            }
        }

        return $this->pi_wrapInBaseClass($content);
    }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1.php']);
}

?>