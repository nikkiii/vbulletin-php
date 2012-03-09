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

//using the posts module

//Can have up to the max forum limit of tags, or remove the last argument for no tags.
$turl = $vbff->posts->postThread(1, "Subject", "Message here", array('tag1', 'tag2'));

//Simple, 2 arguments, can use the last argument if you want a title
$vbff->posts->postReply(1, "Reply!");

// using the members module

//Give reputation to a post
$vbff->members->reputation(1, "Repped by vBulletin-PHP");

//Send a visitor message to a member
$vbff->members->sendVisitorMessage(1, "Message!");

//Send a private message to a member (using names)
$vbff->members->sendPrivateMessage("Member Name", "Subject", "Message");

//Send a private message to multiple members (No userids unless you fetch them yourself)
$vbff->members->sendPrivateMessage(array("Member1", "Member2"), "Subject", "Message");

//Or use the userid
$vbff->members->sendPrivateMessage(1, "Subject", "Message");


//using the vbshout module

//Send a shout!
$vbff->vbshout->sendShout("Hello world!");

//Get active users!
$users = $vbff->vbshout->fetchUsers();

//Get the shouts!
$shouts = $vbff->vbshout->fetchShouts();
foreach($shouts as $shout) {
	//Do whatever!
}

//using the usercp module

//Set your signature!
$vbff->usercp->setSignature("This was set by vBulletin-PHP!");

//If you have more than 1 usergroup, you can use this to set them!
$vbff->usercp->setUsergroup(2);

//Set your avatar (URL only)
$vbff->usercp->setAvatar("Avatar URL");

//Remove your avatar, also works with setAvatar("") or setAvatar(0)
$vbff->usercp->setAvatar(false);

//using the thanks module

//Give a 'thanks' to a post, requires the thanks mod
$vbff->thanks->thankPost(1);

//There will be more!
?>