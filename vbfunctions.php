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
 * A simple class which contains POST/GET/login functions for a vBulletin forum
 * @author Nikki
 */
class vBForumFunctions {

	/**
	 * Variables, like the base forum URL, whether we're logged in and the security token
	 */
	var $url;
	var $loggedin;
	var $cookiefile;
	var $securitytoken;
	
	/**
	 * Construct a new instance with the URL
	 */
	public function __construct($url, $cookiefile = "jar.txt") {
		$this->url = $url;
		$this->loggedin = file_exists($cookiefile); //TODO some kind of verification of the cookie file...
		$this->cookiefile = $cookiefile;
	}
	
	/**
	 * Request a page, simple CURL
	 */
	public function request($page, $data=array(), $overridelogin=false, $info=false) {
		$ch = curl_init($this->url.$page);
		curl_setopt($ch, CURLOPT_HEADER, $info);
		//I will not deny the forums the right to block this, it can be done easily through settings.
		//You are however free to change this.
		curl_setopt($ch, CURLOPT_USERAGENT, 'vBulletin PHP Web API 1.0');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//If we are logged in/need to login, use the cookie file
		if($this->loggedin || $overridelogin) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
		}
		//Check for POST data
		if(!empty($data)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		//Execute and get the response
		$response = curl_exec($ch);
		if(!$response) {
			echo "cURL threw an error: ".curl_error($ch)." (url: $page, params: ".implode(",", $data).")\n";
		}
		//If we want the information, including the header
		if($info) {
			$ret = array();
			//Get the request info, like the header size
			$info = curl_getinfo($ch);
			//Split out the header
			$ret['header'] = substr($response, 0, $info['header_size']);
			//Split out the body
			$ret['response'] = substr($response, $info['header_size']+1);
			//Close our resource
			curl_close($ch);
			return $ret;
		}
		//Close our resource
		curl_close($ch);
		return $response;
	}
	
	/**
	 * Login, simple isn't it?
	 */
	public function login($username, $password) {
		$logindetails = array(
								"do" => "login",
								"vb_login_username" => $username, 
								"vb_login_password" => $password,
								"vb_login_md5password" => md5($password),
								"vb_login_md5password_utf" => "",
								"securitytoken" => "guest",
								"cookieuser" => "1");
								
		$resp = $this->request("login.php?do=login", $logindetails, true);
		
		$this->loggedin = (stristr($resp, "Invalid") === false);
		
		if(preg_match("/var SECURITYTOKEN = \"(.*?)\"/", $resp, $t)) {
			$this->securitytoken = $t[1];
		} else {
			echo "WARNING: No security token\n";
		}
		return $this->loggedin;
	}
	
	/**
	 * Add reputation to a post, with an optional comment/derep if applicable
	 */
	public function reputaton($postid, $comment="", $neg=false) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "addreputation";
		$postfields['p'] = $postid;
		$postfields['reputation'] = $neg ? "neg" : "pos";
		$postfields['reason'] = $commennt;
		$this->request("reputation.php?do=addreputation&p=$postid", $postfields);
		//Heh
		return true;
	}
	
	/**
	 * Thank a post, works even when you don't have access to that section
	 */
	public function thankPost($postid) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "post_thanks_add";
		$postfields['p'] = $postid;
		$this->request("post_thanks.php", $postfields);
		//If nothing went wrong bla bla bla
		return true;
	}
	
	/**
	 * Set your display usergroup
	 */
	public function setUsergroup($usergroupid) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "updatedisplaygroup";
		$postfields['usergroupid'] = $usergroupid;
		$this->request("profile.php", $postfields);
	}
	
	/**
	 * Set your signature
	 */
	public function setSignature($contents) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "updatesignature";
		$postfields['vB_Editor_001_mode'] = 'wysiwyg';
		$postfields['message'] = $contents;
		$this->request("profile.php?do=updatesignature", $postfields);
	}
	
	/**
	 * Post a new thread
	 * @param forumid  The forum id
	 * @param title  The thread title
	 * @param message  The message
	 * @param tags  The thread tags
	 */
	public function postThread($forumid, $title, $message, $tags=array()) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "postthread";
		$postfields['f'] = $forumid;
		$postfields['subject'] = $title;
		$postfields['message'] = $message;
		$postfields['vB_Editor_001_mode'] = 'wysiwyg';
		$postfields['taglist'] = implode(",", $tags);
		$resp = $this->request("newthread.php", $postfields, false, true);
		if(preg_match("#Location:\s*(.*)#", $resp['header'], $matches)) {
			return trim($matches[1]);
		}
		return false;
	}
	
	/**
	 * Post a reply on a thread
	 * @param thread  The thread id
	 * @param reply  The reply
	 */
	public function postReply($thread, $reply) {
		if(empty($this->securitytoken)) {
			return false;
		}
		$postfields = $this->getParams();
		$postfields['do'] = "postreply";
		$postfields['t'] = $thread;
		$postfields['message'] = $reply;
		$resp = $this->request("newreply.php", $postfields, false, true);
		if(preg_match("#Location:\s*(.*)#", $resp['header'], $matches)) {
			return trim($matches[1]);
		}
		return false;
	}
	
	/**
	 * Get the basic params, securitytoken is used in most functions
	 */
	private function getParams() {
		return array("securitytoken" => $this->securitytoken);
	}
}
?>
