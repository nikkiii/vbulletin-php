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
 * A module which contains methods for interacting with the Infernoshout vb shoutbox
 * @author Nikki
 */
class Module_vbshout extends vBulletinModule {
	
	/**
	 * Fetch the current shouts, some shoutboxes may have XML around it... no idea how this would work
	 */
	public function fetchShouts() {
		$shoutdata = $this->request($this->shoutfile, array("do" => "messages"));
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
	public function fetchUsers() {
		global $ranks;
		$userdata = $this->request("infernoshout.php", array("do" => "userlist"));
		$userdata = substr($userdata, strpos($userdata, "<div>")+5);
		$userlist = explode(", ", $userdata);
		$users = array();
		foreach($userlist as $user) {
			//(s= support for logged out, security codes :(
			preg_match("/<a href=\"member.php\?(s=.*?|)u=(\d*)\">(.*)<\/a>/im", trim($user), $matches);

			$users[] = array("uid" => intval($matches[2]), "username" => strip_tags(trim($matches[3])));
		}
		return $users;
	}
	
	/**
	 * Send a shout
	 */
	public function sendShout($shout) {
		return ($this->request("infernoshout.php", array("do" => "shout", "message" => $shout)) == "completed");
	}
}
?>
