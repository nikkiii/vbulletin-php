<?php
/**
 * vBulletin-PHP, An easy to use PHP class for providing vBulletin functions
 * Copyright (C) 2012 Nikki <nikki@nikkii.us>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
/**
 * A simple class which contains methods for interacting with the Infernoshout vb shoutbox
 * @author Nikki
 */
class vBShoutFunctions {

	//The vbfunctions instance!
	var $instance;
	//The php file which the shoutbox is installed under, 99% of the time infernoshout.php
	var $shoutfile;
	//The rank colors, may not work on forums without rank colors!
	var $ranks;
	
	/**
	 * Construct a new instance with a vbforumfunctions instance
	 */
	public function __construct($vbinstance, $shoutfile = "infernoshout.php") {
		$this->instance = $vbinstance;
		$this->shoutfile = $shoutfile;
		$this->ranks = load_ranks();
	}
	
	/**
	 * Load the ranks into the array
	 */
	public function load_ranks() {
		return array("red" => "donator", 
				"#1c7abf" => "superdonator", 
				"#02B41D" => "extremedonator", 
				"#6666FF" => "designer",
				"#E18700" => "veteran",
				"#008080" => "respected",
				"#6666c9" => "programmer",
				"#FF9933" => "moderator",
				"#008B00" => "leadmoderator",
				"#2795df" => "executivemoderator",
				"#336699" => "administrator");
	}
	
	/**
	 * Fetch the current shouts, some shoutboxes may have XML around it... no idea how this would work
	 */
	public function fetch_shouts() {
		$shoutdata = $this->instance->request($this->shoutfile, array("do" => "messages"));
		$shoutlist = explode("</div>", $shoutdata);
		
		$shouts = array();
		foreach($shoutlist as $shout) {
			$shout = preg_replace('#<a href="(.*?)" target="_blank">.*?</a>#', '$1', $shout);
			$shout = preg_replace ('/<[^>]*>/', '', $shout);
			$shout = str_replace("\r\n", "", $shout);
			preg_match("/\[(.*?)\]\s(.*?):\s(.*)/im", $shout, $matches);
			if(count($matches) < 3) {
				continue;
			}
			$shouts[] = array("time" => strtotime(trim($matches[1])), "user" => trim($matches[2]), "shout" => trim($matches[3]));
		}
		return $shouts;
	}
	
	/**
	 * Note: NOT GUARANTEED TO GET THE USERS CORRECTLY!
	 * Different forums have different ranks, and different methods of doing rank colors/etc
	 */
	public function fetch_users() {
		global $ranks;
		$userdata = $this->instance->request("infernoshout.php", array("do" => "userlist"));
		$userdata = substr($userdata, strpos($userdata, "<div>")+5);
		$userlist = explode(", ", $userdata);
		$users = array();
		foreach($userlist as $user) {
			//(s= support for logged out, security codes :(
			preg_match("/<a href=\"member.php\?(s=.*?|)u=(\d*)\">(.*)<\/a>/im", trim($user), $matches);
			
			$rank = "member";
			$rankcolor = "#FFFFFF";
			$user_field = trim($matches[3]);
			if(stristr($user_field, "<font")) {
				preg_match("/<font\s*color=(\"|')(.*?)('|\")\s*\/??>(.*?)<\/font>/i", $user_field, $matches2);
				$rankcolor = $matches2[2];
				$rank = $this->ranks[$rankcolor];
				$user_field = strip_tags($matches2[4]);
			} else if(stristr($user_field, "<s>")) {
				$rank = "banned";
				$user_field = strip_tags($user_field);
			} else {
				$user_field = strip_tags($user_field);
			}
			$users[$user_field] = array("uid" => intval($matches[2]), "rank" => $rank, "rankcolor" => $rankcolor);
		}
		return $users;
	}
	
	/**
	 * Send a shout
	 */
	public function send_shout($shout) {
		return $this->instance->request("infernoshout.php", array("do" => "shout", "message" => $shout));
	}
}
?>
