#!/usr/bin/env php
<?php

function getUsers($group) {
$json = json_decode(file_get_contents("epitechGroups/".$group.".json"));
if (isset($json->users))
   return json_encode($json->users);
return (json_encode(array()));
}

$users = getUsers("epitech_2016");
echo $users;

?>