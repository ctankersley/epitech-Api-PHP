#!/usr/bin/env php
<?php

exec("cat group | cut -d ':' -f 1", $groupsToFetch);
$groupsAndGid = array();

foreach ($groupsToFetch as $group) {
	unset($output);
	exec("grep " . $group . " group", $output);
	$gid = explode(':', $output[0]);
	$gid = $gid[sizeof($gid) - 2];
	$row = array("gid" => $gid);
	$groupsAndGid[$group] = $row;
	array_push($groupsAndGid, $row);
}
foreach ($groupsAndGid as $key => $val) {
	$group = $groupsAndGid[$key]["gid"];
	unset($users);
	exec("grep " . $group . " passwd", $users);
	$usersParsed = array();
	foreach ($users as $user) {
		$line = explode(':', $user);
		$photo = "http://cdn.local.epitech.net/userprofil/profilview/".$line[0].".jpg";
		$entry = array("login" => $line[0], "name" => $line[4], "photo" => $photo);
		array_push($usersParsed, $entry);
	}
	$groupsAndGid[$key]["users"] = $usersParsed;
}

for ($i = 0; $i < sizeof($groupsAndGid); $i++) {
    unset($groupsAndGid[$i]);
}

exec("mkdir epitechGroups");
foreach ($groupsAndGid as $groupName => $users) {
  file_put_contents("epitechGroups/".$groupName.".json", json_encode($users));
}
?>