<?php

if (!defined('INSIDE'))
  exit;

function bind_api($bindings, $json_row)
{
  $return = array();
  foreach ($bindings as $binding)
    {
      foreach ($binding as $key => $item)
	$return[$key] = $item($json_row);
    }
  return $return;
}

?>
