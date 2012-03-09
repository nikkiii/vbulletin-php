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
 * A module which contains methods for interacting with members
 * @author Nikki
 */
class Module_members extends vBulletinModule {
	
	/**
	 * Add reputation to a post, with an optional comment/derep if applicable
	 */
	public function reputation($postid, $comment="", $neg=false) {
		$postfields = $this->getParams();
		$postfields['do'] = "addreputation";
		$postfields['p'] = $postid;
		$postfields['reputation'] = $neg ? "neg" : "pos";
		$postfields['reason'] = $commennt;
		$this->request("reputation.php?do=addreputation&p=$postid", $postfields);
		return true;
	}
	
	
	/**
	 * Send a user a visitor message
	 */
	public function sendVisitorMessage($userid, $message) {
		$postfields = $this->getParams();
		$postfields['do'] = "message";
		$postfields['u'] = $userid;
		$postfields['fromquickcomment'] = 1;
		$postfields['message'] = $message;
		$this->request("visitormessage.php?do=message", $postfields);
		return true;
	}
	
	/**
	 * Send a user or multiple users a private message
	 * Note: This function uses usernames for $user, you can use getNameFromUserId to get it, or pass a single userid
	 */
	public function sendPrivateMessage($user, $subject, $message) {
		$postfields = $this->getParams();
		$postfields['do'] = "insertpm";
		if(is_array($user)) {
			$postfields['recipients'] = implode(";", $user);
		} else if(is_string($user)) {
			$postfields['recipients'] = $user;
		} else if(is_numeric($user)) {
			$postfields['recipients'] = $this->getNameFromUserId($user);
		}
		$postfields['title'] = $subject;
		$postfields['message'] = $message;
		
		$this->request("private.php", $postfields);
	}
	
	/**
	 * Get a member's name from their userid, may not work on some themes...
	 */
	public function getNameFromUserId($userid) {
		$resp = $this->request("member.php?u=$userid");
		if(preg_match("#<legend>Send a Message to (.*?)</legend>#", $resp, $matches)) {
			return $matches[1];
		}
		return false;
	}
}
?>
