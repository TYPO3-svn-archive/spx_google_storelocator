<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Raphael Heilmann <rapha@dmozed.org>
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Store Locator' for the 'spx_google_storelocator' extension.
 *
 * @author	Raphael Heilmann <rapha@dmozed.org>
 * @package	TYPO3
 * @subpackage	tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_spxgooglestorelocator_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_spxgooglestorelocator_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'spx_google_storelocator';	// The extension key.
	var $pi_checkCHash = true;


function after ($this1, $inthat)
   {
       if (!is_bool(strpos($inthat, $this1)))
       return substr($inthat, strpos($inthat,$this1)+strlen($this1));
   }

function after_last ($this1, $inthat)
   {
       if (!is_bool(strrevpos($inthat, $this1)))
       return substr($inthat, strrevpos($inthat, $this1)+strlen($this1));
   }

function before ($this1, $inthat)
   {
       return substr($inthat, 0, strpos($inthat, $this1));
   }

function before_last ($this1, $inthat)
   {
       return substr($inthat, 0, strrevpos($inthat, $this1));
   }

function between ($this1, $that, $inthat)
   {
     return $this->before($that, $this->after($this1, $inthat));
   }

function between_last ($this1, $that, $inthat)
   {
     return $this->after_last($this1, $this->before_last($that, $inthat));
   }

function strrevpos($instr, $needle)
{
 $rev_pos = strpos (strrev($instr), strrev($needle));
 if ($rev_pos===false) return false;
 else return strlen($instr) - $rev_pos - strlen($needle);
}


function get_webpage($url)
{
   global $db;
   if (false)
   {
    $sessions = curl_init();
    curl_setopt($sessions,CURLOPT_URL,$url);
    curl_setopt($sessions, CURLOPT_HEADER , 1);
    curl_setopt($sessions, CURLOPT_RETURNTRANSFER,1);
    $data = curl_exec($sessions);
   }
   else
   {
    $data = file_get_contents($url);
   }
   return $data;
}

function geocode_address($complete_address)
{
   //$err = error_reporting(0);
   //This is where we add different geocoding sites for each country.
   //Currently supported:
   //US: Zipcode and Addresses
   //CA: Zipcode and Addresses
   //UK: Postal Code and Addresses
   //International: City names with Country

   //Test Addresses for debugging purposes and accuracy testing
   //US: 7997 S Brook Forest Rd, Evergreen, CO 80439 US
   //CA: 10115 97a Ave, Edmonton, AB T6E4T2 CA
   //CA: 180 boulevard Omer-Marcil ,Saint-Jean-sur-Richelieu,QC CA
   //UK: 48 Leicester Square, London UK SW11
   //AU: 201 sussex, sydney, NSW au
   //NZ: 1200 glenmore st, Wellington, NZ
   //International: Tokyo, JP

   //Replace spaces in the address with + signs to be used in the urls.  Most, if not all geocoders will support
   //+ symbols as spaces since it's a standard URI symbol for space
   //Addresses come in as an array parse it out.

    $address = trim(str_replace(' ', '+', $complete_address['address']));
    $city   = trim(str_replace(' ', '+', $complete_address['city']));
    $state  = trim(str_replace(' ', '+', $complete_address['state']));
    $country= trim(str_replace(' ', '+', $complete_address['country']));
    $zipcode= trim(str_replace(' ', '+', $complete_address['zipcode']));
    $key = $_EXTCONF['enable.']['GoogleAPIKey'];

   ######################################Main Geocoders#####################################

   ###############################United States of America##################################
   if ($country == 'us')
   {
      //Google
      $apiURL = "http://maps.google.com/maps/geo?q=$address,$city,$state,$zipcode,$country&key=$key&output=xml";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',',between("<coordinates>", "</coordinates>", $addressData));
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];

      //Backup (Yahoo)
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      //Yahoo chokes if it encounters 2 , symbols in a row, so we have to hand feed it the address.
      if ($address)   {$addr .= "$address";}
      if ($city)     {$addr .= ",$city";}
      if ($state)    {$addr .= ",$state";}
      if ($zipcode)  {$addr .= ",$zipcode";}
      if ($country)  {$addr .= ",$country";}
      $apiURL = "http://api.local.yahoo.com/MapsService/V1/geocode?appid=YahooDemo&location=$addr";
      $addressData = $this->get_webpage($apiURL);
      $lat_lon->lat = $this->between("<Latitude>", "</Latitude>", $addressData);
      $lat_lon->lon = $this->between("<Longitude>", "</Longitude>", $addressData);
      }

      //Backup (LocalSearchMaps)
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://geo.localsearchmaps.com/?street=$address&city=$city&state=$state&zip=$zipcode&country=$country&cb=coordinates";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = $this->between('coordinates(', ');', $addressData);
      $coordinates = explode(',', $coordinates);
      $lat_lon->lat = $coordinates[0];
      $lat_lon->lon = $coordinates[1];

      }

      //Backup (geocoder.us)  Just zipcodes OR addresses, not both.  Also quite slow.
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
         if ($zipcode)
         {
         $apiURL = "http://rpc.geocoder.us/service/csv?zip=$zipcode";
         }
         else
         {
         $apiURL = "http://rpc.geocoder.us/service/csv?address=$address,$city,$state";
         }
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',', $addressData);
      $lat_lon->lat = $coordinates[0];
      $lat_lon->lon = $coordinates[1];
      }
   }

   #######################################Canada############################################
   elseif ($country == 'ca')
   {

      //geocoder.ca
      $address = "$address,$city,$state,$zipcode,$country";
      $address = trim(str_replace(' ', '+', $address));
      $apiURL = "http://geocoder.ca/?locate=$address&city=$city&prov=$state&postal=$zipcode&geoit=XML";
      $addressData = $this->get_webpage($apiURL);
      $lat_lon->lat = $this->between("<latt>", "</latt>", $addressData);
      $lat_lon->lon = $this->between("<longt>", "</longt>", $addressData);

     //Backup (LocalSearchMaps)
     if (!$lat_lon->lat && !$lat_lon->lon)
     {
     $apiURL = "http://geo.localsearchmaps.com/?street=$address&city=$city&state=$state&country=$country&zip=$zipcode&cb=coordinates";
     $addressData = $this->get_webpage($apiURL);
     $coordinates = $this->between('coordinates(', ');', $addressData);
     $coordinates = explode(',', $coordinates);
     $lat_lon->lat = $coordinates[0];
     $lat_lon->lon = $coordinates[1];
     }

     //Backup (Google) It is not very good with canada believe it or not.
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://maps.google.com/maps/geo?q=$address,$city,$state,$zipcode,$country&key=$key&output=xml";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',',between("<coordinates>", "</coordinates>", $addressData));
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];
      }

      //Backup (Yahoo)
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      //Yahoo chokes if it encounters 2 , symbols in a row, so we have to hand feed it the address.
      if ($address)   {$addr .= "$address";}
      if ($city)     {$addr .= ",$city";}
      if ($state)    {$addr .= ",$state";}
      if ($zipcode)  {$addr .= ",$zipcode";}
      if ($country)  {$addr .= ",$country";}
      $apiURL = "http://api.local.yahoo.com/MapsService/V1/geocode?appid=YahooDemo&location=$addr";
      $addressData = $this->get_webpage($apiURL);
      $lat_lon->lat = $this->between("<Latitude>", "</Latitude>", $addressData);
      $lat_lon->lon = $this->between("<Longitude>", "</Longitude>", $addressData);
      }
   }

   ####################################United Kingdom#######################################
   if ($country == 'uk')
   {
     //LocalSearchMaps
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://geo.localsearchmaps.com/?street=$address&city=$city&country=UK";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = $this->between('map.centerAndZoom(new GPoint(', '),', $addressData);
      $coordinates = explode(',', $coordinates);
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];
      }

      //Brainoff
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://brainoff.com/geocoder/rest/?post=$zipcode";
      $addressData = $this->get_webpage($apiURL);
      $lat_lon->lat = $this->between('<geo:lat>', '</geo:lat>', $addressData);
      $lat_lon->lon = $this->between('<geo:long>', '</geo:long>', $addressData);
      }

   }

   ####################################Australia############################################
   if ($country == 'au')
   {
    //Google
    if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://maps.google.com/maps/geo?q=$address,$city,$state,$zipcode,$country&key=$key&output=xml";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',',between("<coordinates>", "</coordinates>", $addressData));
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];
      }
   }

   ####################################New Zealand##########################################
   if ($country == 'nz')
   {
    //Google
    if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://maps.google.com/maps/geo?q=$address,$city,$state,$zipcode,$country&key=$key&output=xml";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',',between("<coordinates>", "</coordinates>", $addressData));
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];
      }
   }

   ####################################International########################################

   //If there is no lon/lat yet, we assume it's international and attempt to find it based on just the
   //city and country.

   //Google
   if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://maps.google.com/maps/geo?q=$address,$city,$state,$zipcode,$country&key=$key&output=xml";
      $addressData = $this->get_webpage($apiURL);
      $coordinates = explode(',',$this->between("<coordinates>", "</coordinates>", $addressData));
      $lat_lon->lat = $coordinates[1];
      $lat_lon->lon = $coordinates[0];
      }

   //Yahoo
    if (!$lat_lon->lat && !$lat_lon->lon)
      {
      //Yahoo chokes if it encounters 2 , symbols in a row, so we have to hand feed it the address.
      if ($address)     {$addr .= "$address,";}
      if ($city)     {$addr .= "$city,";}
      if ($country)  {$addr .= "$country";}
      $apiURL = "http://api.local.yahoo.com/MapsService/V1/geocode?appid=YahooDemo&location=$addr";
      $addressData = $this->get_webpage($apiURL);
      $lat_lon->lat = $this->between("<Latitude>", "</Latitude>", $addressData);
      $lat_lon->lon = $this->between("<Longitude>", "</Longitude>", $addressData);
      }

      //LocalSearchMaps (Backup)
      if (!$lat_lon->lat && !$lat_lon->lon)
      {
      $apiURL = "http://geo.localsearchmaps.com/?street=$address&city=$city&state=$state&country=$country&zip=$zipcode&cb=coordinates";
      //$addressData = $this->get_webpage($apiURL);
      $coordinates = $this->between('coordinates(', ');', $addressData);
      $coordinates = explode(',', $coordinates);
      $lat_lon->lat = $coordinates[0];
      $lat_lon->lon = $coordinates[1];
      }

return $lat_lon;
}

	function update_lat_lon () {
   		$query = "SELECT * from tx_spxgooglestorelocator_locations WHERE lat = '' AND lon = '' ";
   		$result = mysql(TYPO3_db,$query);
   		echo mysql_error();
   		while ($rows = mysql_fetch_array($result))
      		{
         		if ($rows['uid'])
         		{
         			$zip = $rows['zip'];
         			$complete_address['address'] = $rows['address'];
         			$complete_address['city'] = $rows['city'];
         			$complete_address['state'] = $rows['state'];
         			$complete_address['zip'] = $rows['zip'];
         			$complete_address['country'] = $rows['country'];
         			$lat_lon = $this->geocode_address($complete_address, $country);
         			$lat = $lat_lon->lat;
         			$lon = $lat_lon->lon;
         			$uid = $rows[uid];
         			$query = "UPDATE tx_spxgooglestorelocator_locations SET   lat = '$lat', lon = '$lon' WHERE uid = '$uid' ";
         			$results = mysql(TYPO3_db,$query);
         		}
       		}
	}

	function get_categories() {
		global $_EXTCONF, $TYPO3_CONF_VARS;


								$query = "SELECT * FROM  tx_spxgooglestorelocator_categories WHERE 1 ORDER BY sorting";

								$res = mysql(TYPO3_db,$query);

								$out=array();

								while($row = mysql_fetch_assoc($res)){

									$out[]=$row['name'];

								}

								return $out;

	}

	function inradius($complete_address, $radius) {
		        global $TYPO3_CONF_VARS;
		        $_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['spx_google_storelocator']);

            //If there are any limitations on the results set, get them here.
            $results_limit = $_EXTCONF['enable.']['ResultsLimit'];

            if ($results_limit > 0) { $results_limit = "LIMIT 0,$results_limit"; }

            //Check to see if we already have starting lat/lon so we dont do double geocoding
            if (!$complete_address['lat_lon']->lat && !$complete_address['lat_lon']->lon)
            {
              $lat_lon = $this->geocode_address($complete_address);
            }
            else
            {
              $lat_lon = $complete_address['lat_lon'];
            }
            $lat    = trim($lat_lon->lat);
            $lon    = trim($lat_lon->lon);

            //Add in categories to the search criteria
            //If they are not an array, make them an array.
            $categories = $complete_address['categories'];
            if (!is_array($categories))
            {
              $categories = explode(',', $categories);
            }

            //Loop through and add each category to the query.
            $category_search = "(";
            foreach ($categories AS $cat)
            {
             $cat = trim($cat);
               if ($counter == 0)
               {
                $category_search .= " categories LIKE '%$cat%' ";
               }
               else
               {
                  if ($cat != '')
                  {
                    $category_search .= " OR categories LIKE '%$cat%' ";
                  }
               }
               $counter++;
            }
            $category_search .= ")";
            if (sizeof($categories) == 1 && $categories[0] == '')
            {
             $category_search = "categories != ''";
            }

            $pi = M_PI;
            $query="SELECT *,(((acos(sin(($lat*$pi/180)) * sin((lat*$pi/180)) + cos(($lat*$pi/180)) *  cos((lat*$pi/180)) * cos((($lon - lon)*$pi/180))))*180/$pi)*60*1.423) as distance FROM tx_spxgooglestorelocator_locations HAVING distance <= $radius AND $category_search AND deleted!='1' ORDER BY distance ASC $results_limit";



            $result = mysql(TYPO3_db,$query);
            //echo mysql_error();
            $i = 0;
            if ($result)
            {
               return $result;
            }
    } // end func


	function prepare_map_results ($mysql_results) {
      $retval ='';
     //If the seek line is missing, then the results will never show.  Do not remove the seek line.
     mysql_data_seek($mysql_results, 0);
     while ($rows = mysql_fetch_array($mysql_results))
     {
        $lat        = $rows['lat'];
        $lon        = $rows['lon'];
        $address    = addslashes($rows['address']);
        $city       = addslashes($rows['city']);
        $state      = addslashes($rows['state']);
        $zip        = addslashes($rows['zip']);
        $country    = addslashes($rows['country']);
        $storename  = addslashes($rows['storename']);
        $hours      = addslashes($rows['hours']);
        $url        = addslashes($rows['url']);
        $image      = addslashes($rows['image']);

        $icon      = "uploads/tx_spxgooglestorelocator/icons/".addslashes($rows['icon']);
          if (!is_file($icon))
            $icon = "uploads/tx_spxgooglestorelocator/icons/starlarge_black.gif";

        $notes      = addslashes(wordwrap($rows['notes'], 80, "<br>"));
        $phone      = addslashes($rows['phone']);
        $id         = $rows['id'];
        $image_url  = addslashes($rows['image_url']);
        $use_coordinate  = addslashes($rows['use_coordinate']);
        $distance   = number_format($rows['distance'],2, '.', '');
           //If the location is blank, skip it.  Otherwise, setup a bullet on the map for it.
           if ($lat != 0 or $lon != 0)
              {
              $retval .= "
              lon = ".floatval($lon).";
              lat = ".floatval($lat).";
              point = new GPoint(lon,lat);
              //Create the custom Green icon.  Google only provides red, so I've modified it with a different color.
              icon = new GIcon();
              //icon.shadow = \"http://www.google.com/mapfiles/shadow50.png\";";

              // Get the size of the image so we can pass it to GSize.
              // Leaving GSize blank will break the map, so we have to pass
              // these sizes or things dont work right :/
              $mapicon = $icon;
              list($width, $height, $type, $attr) = getimagesize("$mapicon");
              //list($width, $height, $type, $attr) = getimagesize(locations_marker_icon);
              $retval .= "icon.iconSize = new GSize(".$width.", ".$height.");
              //icon.shadowSize = new GSize(37, 34);
              icon.iconAnchor = new GPoint(9, 34);
              icon.infoWindowAnchor = new GPoint(9, 2);
              icon.infoShadowAnchor = new GPoint(18, 25);
              var address = ";

              //This little snippet makes the balloon "to here, from here" features use the same logic as the results page
              //when determining whether to send google a coordinate or address to get driving directions.
                 if ($use_coordinate == 'on')
                    {
                    $retval .= "'$lat,$lon'";
                    }
                    else
                    {
                    $retval .= "'$address,$city,$state $zip $country'";
                    }
              $retval .= "//Setup the custom Icons
              icon.image = \"".$mapicon."\";
              marker = createMarker(point, name, '<div class=phpGoogleStoreLocator_map_balloon_body>";

                 if ($image != '' && display_image_in_balloon == 'on')
                    {
                    if ($image_url != '')
                       {
                       $retval .= "<a href=\"$image_url\"><img style=\"float: left\" src=\"$image\" width=104 height=124></a>";
                       }
                    else
                       {
                       $retval .= "<img style=\"float: left\" src=\"$image\" width=104 height=124>";
                       }
                    }

              if ($url == '')
                 {
                 $retval .= "<b>$storename</b><br>$address<br>$city, $state $zip $country<br>$phone";
                 }
              else
                 {
                 $retval .= "<b><a href=\"$url\">$storename</a></b><br>$address<br>$city, $state $zip $country<br>$phone";
                 }

                 $retval .= "</div>', '".$storename."', icon);
              marker.id = marker.id +1;
              map.addOverlay(marker);";

              }//end if
     }//end of while loop

     return ($retval);

  }

	function show_form ($config) {
	   $returnval = "
      <form method=\"post\" action=\"".$this->pi_getPageLink($GLOBALS['TSFE']->id)."\">
	<table class=\"tx_spxgooglestorelocator_searchform\">
		<tr><td>".htmlspecialchars($this->pi_getLL('zip_label'))."</td>
			<td><input type=\"text\" name=\"zipcode\"></td><td rowspan=\"4\">
      ".htmlspecialchars($this->pi_getLL('categories_label'))." <br>
      <select name=\"categories[]\" multiple style=\"width:200px;height:50px\" >";

      $categories_list = $this->get_categories();

      foreach ($categories_list AS $cat)
         {
          $cat = trim($cat);
          $returnval .= "<option>".$cat."</option>";
         }

      $returnval .= "</select></td></tr>
      <tr><td>".htmlspecialchars($this->pi_getLL('street_label'))."</td>
			<td><input type=\"text\" name=\"address\"></td></tr>
      <tr><td>".htmlspecialchars($this->pi_getLL('city_label'))."</td>
			<td><input type=\"text\" name=\"city\"></td></tr>
      <tr><td>".htmlspecialchars($this->pi_getLL('country_label'))."</td>
			<td><select name=\"country\">";

      $country_array = split('[,]', $config['enable.']['Countrycodes']);
      foreach ($country_array AS $country_iso)
         {
	 $result = mysql(TYPO3_db,"SELECT cn_short_de FROM static_countries WHERE cn_iso_2 = '$country_iso';");
	 $row = mysql_fetch_array ($result);
         $returnval .= "<option value=\"".$country_iso."\">".$row['cn_short_de']."</option>";
       }
      $returnval .= "</select></td></tr>
      <tr><td colspan=\"3\">".htmlspecialchars($this->pi_getLL('radius_label'))."
      <select name=\"radius\">";

      $radius_array = split('[,]', $config['enable.']['Radius']);
      foreach ($radius_array AS $radius)
         {
         $returnval .= "<option>".$radius."</option>";
       }
      $returnval .= "</select>
      <input type=submit name=\"submit\" value=\"".htmlspecialchars($this->pi_getLL('submit_button_label'))."\">
      </td></tr></table>
      </form>";

  return($returnval);
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		global $TYPO3_CONF_VARS, $HTTP_POST_VARS;
		$_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['spx_google_storelocator']);
    $content = '';
    //error_reporting(E_ALL);

		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$TCE = t3lib_div::makeInstance('t3lib_TCEmain');
    $TCE->admin = 1;
    $TCE->clear_cacheCmd('pages');
    $TCE->clear_cacheCmd($GLOBALS['TSFE']->id);

		$this->update_lat_lon();
		if ($HTTP_POST_VARS['submit']=='') {
		  $content = $this->show_form($_EXTCONF);
		}
		else {
      $zoom = 5; //get_setting("zoom", $db);//0 to 17, 17 being Country view, 0 being Street closeup
      $google_key = $_EXTCONF['enable.']['GoogleAPIKey'];//Google API Key
      define("display_image_in_results", $_EXTCONF['enable.']['DisplayImageInResults']);//This will display the image in the results table.  yes/no
      define("display_image_in_balloon", $_EXTCONF['enable.']['DisplayImageInBalloon']);//This will display the image in the little red balloons on the map. yes/no

      //Set the center lat/lon before we enter inradius so we dont do double geocoding.  This speeds things up significantly.
      $center_lat_lon = $this->geocode_address($HTTP_POST_VARS);
      $HTTP_POST_VARS['lat_lon'] = $center_lat_lon;

      $mysql_results = $this->inradius($HTTP_POST_VARS, $HTTP_POST_VARS['radius']);

      $center_lat = $center_lat_lon->lat;
      $center_lon =$center_lat_lon->lon;
      $center_lat_float = floatval($center_lat);
      $center_lon_float = floatval($center_lon);

      $content .= "<div id=\"map\" class=\"tx_spxgooglestorelocator_map\"></div>
                   <div id=\"results\" class=\"tx_spxgooglestorelocator_results\">";

      $content .= "Searched for stores near ".$HTTP_POST_VARS['address']." ".$HTTP_POST_VARS['zipcode']."
      ".$HTTP_POST_VARS['city']." (".$HTTP_POST_VARS['country'].")<br/>\n";

      if (mysql_num_rows($mysql_results) == 0) {
        $content .= $this->pi_getLL('no_results_found');
      }
      else {
        $content .= "Results found.<br/>\n";
        while ($rows = mysql_fetch_array($mysql_results)) {

          $counter++;
          $lat        = $rows['lat'];
          $lon        = $rows['lon'];
          $address    = $rows['address'];
          $city       = $rows['city'];
          $state      = $rows['state'];
          $zip        = $rows['zip'];
          $country    = $rows['country'];
          $storename  = $rows['storename'];
          $phone      = $rows['phone'];
          $hours      = $rows['hours'];

          $url        = $rows['url'];
             if ($url == 'http://') $url = '';

          $image      = $rows['image'];
          $icon      = $rows['icon'];
          $notes      = $rows['notes'];
          $phone      = $rows['phone'];
          $id         = $rows['id'];
          $image_url  = $rows['image_url'];
          $distance   = number_format($rows['distance'],2, '.', '');

          $content .= 'in '.$distance.': '.$storename."<br/> \n";
        }

        $content .= "</div>";

        $map_results = $this->prepare_map_results($mysql_results);

        $mapicon_you = "uploads/tx_spxgooglestorelocator/icons/starlarge_black.gif";
        list($mapicon_you_width, $mapicon_you_height, $type, $attr) = getimagesize("$mapicon_you");

        $jsfile = addslashes(implode ('', @file('uploads/tx_spxgooglestorelocator/map.js')));
        eval("\$javascript = \"$jsfile\";");
        $content .= stripslashes($javascript);
      }

    }

		/* $content .= '
			<strong>This is a few paragraphs:</strong><br />
			<p>This is line 1</p>
			'.$conf['ResultLimit'].'
			<p>This is line 2</p>'.$conf.'<p>bla '.$_EXTCONF['ResultLimit'].'</p>
			<p>'.$_EXTCONF['enable.']['GoogleAPIKey'].'</p>

			<h3>This is a form:</h3>
			<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'" method="POST">
				<input type="hidden" name="no_cache" value="1">
				<input type="text" name="'.$this->prefixId.'[input_field]" value="'.htmlspecialchars($this->piVars['input_field']).'">
				<input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
			</form>
			<br />
			<p>You can click here to '.$this->pi_linkToPage('get to this page again',$GLOBALS['TSFE']->id).'</p>
		'; */

		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1.php']);
}

?>
