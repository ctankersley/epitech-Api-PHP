<?php

if (!defined('INSIDE'))
  exit;

function clean_json($output) {
  $output = str_replace('// epitech JSON webservice ...', '', $output);
  $output = str_replace('// Epitech JSON webservice ...', '', $output);
  $output = str_replace('null', '"null"', $output);
	$output = trim($output);
	if (strpos($output, '[') != 0)
		$output = '['.$output;
	if (strrpos($output, ']') != (strlen($output) - 1))
		$output = $output.']';
  return $output;
}

function sortByStart($a, $b) {
  if (strtotime($a['start']) == strtotime($b['start']))
    return 0;
  else if (strtotime($a['start']) > strtotime($b['start']))
    return 1;
  else
    return -1;
}

function sortByEnd($a, $b) {
	if (strtotime($a['end']) == strtotime($b['end']))
	  return 0;
	else if (strtotime($a['end']) > strtotime($b['end']))
		return 1;
	else
		return -1;
}

function getModuleName($title_module) {
	$module = $title_module;
	$module = (strstr($title_module, "C++") != FALSE ? "C++" : $module);
	$module = (strstr($title_module, "Mathematics") != FALSE ? "Mathematics" : $module);
	$module = (strstr($title_module, "Functional programming") != FALSE ? "Ocaml" : $module);
	return $module;
}

function getProjectMaster($item) {
	$item->picture = (isset($item->picture) ? str_replace('.bmp', '.jpg', str_replace('/userprofil/', '/userprofil/profilview/', $item->picture)) : array());
	return $item;
}

function getProjectMembers($item) {
	foreach ($item as $key => $val) {
		$val->picture = (isset($val->picture) ? str_replace('.bmp', '.jpg', str_replace('/userprofil/', '/userprofil/profilview/', $val->picture)) : array());
	}
	return $item;
}

function getProjectFiles($root, $curl) {
	for ($i = 0; $i < count($root); $i++) {
		if ($root[$i]->type == 'd') {
			curl_setopt($curl, CURLOPT_URL, 'https://intra.epitech.eu' . $root[$i]->fullpath . '?format=json');
    	$output = curl_exec($curl);
    	$output = json_decode(clean_json($output));
    	$root[$i]->subfiles = getProjectFiles($output, $curl);
		}
		else
			$root[$i]->subfiles = array();
	}
	return $root;
}

function isMyProjectGroup($item, $login) {
	if ($item->master->login == $login)
		return true;
	foreach ($item->members as $key => $val) {
		if ($val->login == $login)
			return true;
	}
	return false;
}

function is_susie($item) {
  if (!isset($item->title)) return false;
  if (!isset($item->type)) return false;
  if (!isset($item->start)) return false;
  if (!isset($item->end)) return false;
  if (!isset($item->description)) return false;
  if (!isset($item->nb_place)) return false;
  if (!isset($item->id)) return false;
  if (!isset($item->maker)) return false;
  if (!isset($item->maker->title)) return false;
	if (!isset($item->calendar_type)) {
		if (!isset($item->calendar) || !isset($item->calendar->type))
			return false;
		else
			return true;
	}
  return true;
}

function is_soutenance($item) {
  if (!isset($item->rdv_group_registered)) return false;
  return (isset($item->type_title) ? ($item->type_title == 'Soutenance' ? true : false) : false);
}

function template_days() {
  $binding = array(
		   array('title' => 'days_title'),
		   array('module' => 'days_module'),
		   array('genre' => 'days_genre'),
		   array('location' => 'days_location'),
		   array('start' => 'days_start'),
		   array('end' => 'days_end'),
		   array('duration' => 'days_duration'),
		   array('description' => 'days_description'),
		   array('nb_places' => 'days_nb_places'),
		   array('nb_registered' => 'days_nb_registered'),
		   array('id' => 'days_id'),
		   array('teacher_name' => 'days_teacher_name'),
		   array('token' => 'days_token'),
		   array('scolaryear' => 'days_scolaryear'),
		   array('codemodule' => 'days_codemodule'),
		   array('codeinstance' => 'days_codeinstance'),
		   array('codeacti' => 'days_codeacti'),
		   array('codeevent' => 'days_codeevent')
	);
  return $binding;
}

function template_dayPlanning() {
	$binding = array(
		   array('title' => 'days_title'),
		   array('module' => 'days_module'),
		   array('genre' => 'days_genre'),
		   array('location' => 'days_location'),
		   array('start' => 'days_start'),
		   array('end' => 'days_end'),
		   array('duration' => 'days_duration'),
		   array('description' => 'days_description'),
		   array('nb_places' => 'days_nb_places'),
		   array('nb_registered' => 'days_nb_registered'),
		   array('event_registered' => 'days_event_registered'),
		   array('id' => 'days_id'),
		   array('teacher_name' => 'days_teacher_name'),
		   array('token' => 'days_token'),
		   array('scolaryear' => 'days_scolaryear'),
		   array('codemodule' => 'days_codemodule'),
		   array('codeinstance' => 'days_codeinstance'),
		   array('codeacti' => 'days_codeacti'),
		   array('codeevent' => 'days_codeevent')
	);
  return $binding;
}
function template_susies() {
  $binding = array(
		   array('title' => 'susies_title'),
		   array('module' => 'susies_module'),
		   array('genre' => 'susies_genre'),
		   array('location' => 'susies_location'),
		   array('start' => 'susies_start'),
		   array('end' => 'susies_end'),
		   array('duration' => 'susies_duration'),
		   array('description' => 'susies_description'),
		   array('nb_places' => 'susies_nb_places'),
		   array('nb_registered' => 'susies_nb_registered'),
		   array('id' => 'susies_id'),
		   array('subscribed' => 'susies_subscribed'),
		   array('teacher_name' => 'susies_teacher_name'),
		   array('already_subscribed' => 'susies_already_subscribed'),
		   array('id_already_subscribed' => 'susies_id_already_subscribed'),
		   array('event_same_time' => 'susies_event_same_time')
	);
  return $binding;
}

function template_susie() {
  $binding = array(
		   array('title' => 'susie_title'),
		   array('module' => 'susie_module'),
		   array('genre' => 'susie_genre'),
		   array('location' => 'susie_location'),
		   array('start' => 'susie_start'),
		   array('end' => 'susie_end'),
		   array('duration' => 'susie_duration'),
		   array('description' => 'susie_description'),
		   array('nb_places' => 'susie_nb_places'),
		   array('nb_registered' => 'susie_nb_registered'),
		   array('id' => 'susie_id'),
		   array('subscribed' => 'susie_subscribed'),
		   array('teacher_name' => 'susie_teacher_name')
	);
  return $binding;
}

function template_projects() {
  $binding = array(
		   array('title' => 'projects_title'),
		   array('module' => 'projects_module'),
		   array('start' => 'projects_start'),
		   array('end' => 'projects_end'),
		   array('subscribed' => 'projects_subscribed'),
		   array('scolaryear' => 'projects_scolaryear'),
		   array('codemodule' => 'projects_codemodule'),
		   array('codeinstance' => 'projects_codeinstance'),
		   array('codeacti' => 'projects_codeacti')
	);
  return $binding;
}

function template_project() {
  $binding = array(
		   array('scolaryear' => 'project_scolaryear'),
			 array('codemodule' => 'project_codemodule'),
			 array('codeinstance' => 'project_codeinstance'),
			 array('codeacti' => 'project_codeacti'),
			 array('instance_location' => 'project_instance_location'),
			 array('module_title' => 'project_module_title'),
			 array('id_activite' => 'project_id_activite'),
			 array('project_title' => 'project_project_title'),
			 array('type_title' => 'project_type_title'),
			 array('type_code' => 'project_type_code'),
			 array('register' => 'project_register'),
			 array('register_prof' => 'project_register_prof'),
			 array('nb_min' => 'project_nb_min'),
			 array('nb_max' => 'project_nb_max'),
			 array('begin' => 'project_begin'),
			 array('end' => 'project_end'),
			 array('end_register' => 'project_end_register'),
			 array('is_rdv' => 'project_is_rdv'),
			 array('instance_allowed' => 'project_instance_allowed'),
			 array('title' => 'project_title'),
			 array('description' => 'project_description'),
			 array('closed' => 'project_closed'),
			 array('over' => 'project_over'),
			 array('date_access' => 'project_date_access'),
			 array('instance_registered' => 'project_instance_registered'),
			 array('user_project_status' => 'project_user_project_status'),
			 array('note' => 'project_note'),
			 array('root_slug' => 'project_root_slug'),
			 array('forum_path' => 'project_forum_path'),
			 array('user_project_master' => 'project_user_project_master'),
			 array('user_project_code' => 'project_user_project_code'),
			 array('user_project_title' => 'project_user_project_title'),
			 array('registered_instance' => 'project_registered_instance')
	);
  return $binding;
}

function template_modules() {
  $binding = array(
		   array('title' => 'modules_title'),
		   array('id' => 'modules_id'),
		   array('date' => 'modules_date'),
		   array('scolaryear' => 'modules_scolaryear'),
		   array('grade' => 'modules_grade'),
		   array('credits' => 'modules_credits'),
		   array('barrage' => 'modules_barrage'),
		   array('rating' => 'modules_rating'),
		   array('instance_semester' => 'modules_instance_semester'),
		   array('semesters' => 'modules_semesters'),
		   array('semester' => 'modules_semester')
	);
  return $binding;
}

function template_mark() {
  $binding = array(
		   array('title' => 'mark_title'),
		   array('module' => 'mark_module'),
		   array('date' => 'mark_date'),
		   array('mark' => 'mark_mark'),
		   array('scolaryear' => 'mark_scolaryear'),
		   array('corrector' => 'mark_corrector'),
		   array('comment' => 'mark_comment')
	);
  return $binding;
}

function template_messages() {
  $binding = array(
		   array('title' => 'messages_title'),
		   array('user' => 'messages_user'),
		   array('content' => 'messages_content'),
		   array('date' => 'messages_date')
	);
  return $binding;
}

function template_alerts() {
  $binding = array(
		   array('title' => 'alerts_title')
	);
  return $binding;
}

function template_photo() {
  $binding = array(
		   array('photo' => 'photo_photo')
	);
  return $binding;
}

?>