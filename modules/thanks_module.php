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
 * A module which contains methods for interacting with the post thanks hack
 * @author Nikki
 */
class Module_thanks extends vBulletinModule {
	
	/**
	 * Thank a post, works even when you don't have access to that section
	 */
	public function thankPost($postid) {
		$postfields = $this->getParams();
		$postfields['do'] = "post_thanks_add";
		$postfields['using_ajax'] = 1;
		$postfields['p'] = $postid;
		$this->request("post_thanks.php", $postfields);
		//If nothing went wrong bla bla bla
		return true;
	}
}
?>