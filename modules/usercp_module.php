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

class Module_usercp extends vBulletinModule {
	/**
	 * Set your display usergroup
	 */
	public function setUsergroup($usergroupid) {
		$postfields = $this->getParams();
		$postfields['do'] = "updatedisplaygroup";
		$postfields['usergroupid'] = $usergroupid;
		$this->request("profile.php", $postfields);
	}
	
	/**
	 * Set your signature
	 */
	public function setSignature($contents) {
		$postfields = $this->getParams();
		$postfields['do'] = "updatesignature";
		$postfields['message'] = $contents;
		$this->request("profile.php?do=updatesignature", $postfields);
	}
}
?>