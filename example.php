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

require_once "vbfunctions.php";

$vbff = new vBForumFunctions("http://avbforum.com/");

if(!$vbff->login("username", "password")) {
	die("Unable to login!");
}
echo "Logged in!\n";

//Set your signature!
$vbff->setSignature("This was set by vBulletin-PHP!");

//Can have up to the max forum limit of tags.
$turl = $vbff->postThread(1, "Subject", "Message here", array('tag1', 'tag2'));

//Give reputation to a post
$vbff->reputation(1, "Repped by vBulletin-PHP");

//Give a 'thanks' to a post, requires the thanks mod
$vbff->thankPost(1);

//If you have more than 1 usergroup, you can use this to set them!
$vbff->setUsergroup(2);

//using vBShoutFunctions
$vbsf = new vBShoutFunctions($vbff);

//Send a shout!
$vbsf->send_shout("Hello world!");

//Get the shouts!
$shouts = $vbsf->fetch_shouts();
foreach($shouts as $shout) {
	//Do whatever!
}

//There will be more!
?>