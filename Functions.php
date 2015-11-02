<?php

if (!defined('INSIDE'))
  exit;

define ('NULL_STRING', 'null');

function days_event_registered($item){
  return($item->event_registered);
}
function days_title($item) {
  return (is_susie($item) ? $item->title : (isset($item->acti_title) ? $item->acti_title : NULL_STRING));
}

function days_location($item) {
  return (is_susie($item) ? str_replace('susie', 'Susie', (isset($item->calendar_type) ? $item->calendar_type : ((isset($item->calendar) && isset($item->calendar->type)) ? $item->calendar->type : NULL_STRING))) : (isset($item->room) ? (isset($item->room->code) ? substr($item->room->code, (strrpos($item->room->code, '/') + 1)) : NULL_STRING) : NULL_STRING));
}

function days_module($item) {
  return (is_susie($item) ? str_replace('perso', 'Perso', str_replace('susie', 'Susie', (isset($item->calendar_type) ? $item->calendar_type : ((isset($item->calendar) && isset($item->calendar->type)) ? $item->calendar->type : NULL_STRING)))) : (isset($item->titlemodule) ? $item->titlemodule : NULL_STRING));
}

function days_genre($item) {
  return (is_susie($item) ? $item->type : (isset($item->type_title) ? $item->type_title : NULL_STRING));
}

function days_start($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return ((is_soutenance($item) && isset($f[0]) && $f[0] != "null") ? $f[0] : (isset($item->start) ? $item->start : NULL_STRING));
}

function days_end($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return ((is_soutenance($item) && isset($f[1])) ? $f[1] : (isset($item->end) ? $item->end : NULL_STRING));
}

function days_duration($item) {
  return ((($start = days_start($item) == NULL_STRING) || ($end = days_end($item) == NULL_STRING)) ? NULL_STRING : ($end - $start));
}

function days_description($item) {
  return (is_susie($item) ? $item->description : NULL_STRING);
}

function days_nb_places($item) {
  return (is_susie($item) ? $item->nb_place : NULL_STRING);
}

function days_nb_registered($item) {
  return (is_susie($item) ? $item->registered : NULL_STRING);
}

function days_id($item) {
  return (is_susie($item) ? $item->id : NULL_STRING);
}

function days_teacher_name($item) {
  return (is_susie($item) ? $item->maker->title : NULL_STRING);
}

function days_token($item) {
  return (is_susie($item) ? "false" : (isset($item->allow_token) ? (($item->allow_token) ? ((isset($item->event_registered) && isset($item->type_title)) ? (($item->event_registered == "present" || $item->type_title == "Soutenance") ? "false" : "true") : "true") : "false") : "false"));
}

function days_scolaryear($item) {
  return (isset($item->scolaryear) ? $item->scolaryear : NULL_STRING);
}

function days_codemodule($item) {
  return (isset($item->codemodule) ? $item->codemodule : NULL_STRING);
}

function days_codeinstance($item) {
  return (isset($item->codeinstance) ? $item->codeinstance : NULL_STRING);
}

function days_codeacti($item) {
  return (isset($item->codeacti) ? $item->codeacti : NULL_STRING);
}

function days_codeevent($item) {
  return (isset($item->codeevent) ? $item->codeevent : NULL_STRING);
}

function susies_title($item) {
  return (is_susie($item) ? $item->title : (isset($item->acti_title) ? $item->acti_title : NULL_STRING));
}

function susies_location($item) {
  return (is_susie($item) ? 'Susie spot' : (isset($item->room) ? (isset($item->room->code) ? substr($item->room->code, (strrpos($item->room->code, '/') + 1)) : NULL_STRING) : NULL_STRING));
}

function susies_module($item) {
  return (is_susie($item) ? 'Susie' : (isset($item->titlemodule) ? $item->titlemodule : NULL_STRING));
}

function susies_genre($item) {
  return (is_susie($item) ? $item->type : (isset($item->type_title) ? $item->type_title : NULL_STRING));
}

function susies_start($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return (is_soutenance($item) ? $f[0] : (isset($item->start) ? $item->start : NULL_STRING));
}

function susies_end($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return (is_soutenance($item) ? $f[1] : (isset($item->end) ? $item->end : NULL_STRING));
}

function susies_duration($item) {
  return ((($start = susies_start($item) == NULL_STRING) || ($end = susies_end($item) == NULL_STRING)) ? NULL_STRING : (strftime('%H:%M:%S', (strtotime($end) - strtotime($start)))));
}

function susies_description($item) {
  return (is_susie($item) ? $item->description : NULL_STRING);
}

function susies_nb_places($item) {
  return (is_susie($item) ? $item->nb_place : NULL_STRING);
}

function susies_nb_registered($item) {
  return (isset($item->registered) ? $item->registered : NULL_STRING);
}

function susies_id($item) {
  return (is_susie($item) ? $item->id : NULL_STRING);
}

function susies_subscribed($item) {
  return (isset($item->subscribed) ? $item->subscribed : NULL_STRING);
}

function susies_teacher_name($item) {
  return (is_susie($item) ? $item->maker->title : NULL_STRING);
}

function susies_already_subscribed($item) {
  return (isset($item->already_subscribed) ? $item->already_subscribed : NULL_STRING);
}

function susies_id_already_subscribed($item) {
  return (isset($item->id_already_subscribed) ? $item->id_already_subscribed : NULL_STRING);
}

function susies_event_same_time($item) {
  return (isset($item->event_same_time) ? $item->event_same_time : NULL_STRING);
}

function susie_title($item) {
  return (is_susie($item) ? $item->title : (isset($item->acti_title) ? $item->acti_title : NULL_STRING));
}

function susie_location($item) {
  return (is_susie($item) ? 'Susie spot' : (isset($item->room) ? (isset($item->room->code) ? substr($item->room->code, (strrpos($item->room->code, '/') + 1)) : NULL_STRING) : NULL_STRING));
}

function susie_module($item) {
  return (is_susie($item) ? 'Susie' : (isset($item->titlemodule) ? $item->titlemodule : NULL_STRING));
}

function susie_genre($item) {
  return (is_susie($item) ? $item->type : (isset($item->type_title) ? $item->type_title : NULL_STRING));
}

function susie_start($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return (is_soutenance($item) ? $f[0] : (isset($item->start) ? $item->start : NULL_STRING));
}

function susie_end($item) {
  $f = (is_soutenance($item) ? explode('|', $item->rdv_group_registered) : array());
  return (is_soutenance($item) ? $f[1] : (isset($item->end) ? $item->end : NULL_STRING));
}

function susie_duration($item) {
  return ((($start = susies_start($item) == NULL_STRING) || ($end = susies_end($item) == NULL_STRING)) ? NULL_STRING : (strftime('%H:%M:%S', (strtotime($end) - strtotime($start)))));
}

function susie_description($item) {
  return (is_susie($item) ? $item->description : NULL_STRING);
}

function susie_nb_places($item) {
  return (is_susie($item) ? $item->nb_place : NULL_STRING);
}

function susie_nb_registered($item) {
  return (isset($item->logins) ? count($item->logins) : NULL_STRING);
}

function susie_id($item) {
  return (is_susie($item) ? $item->id : NULL_STRING);
}

function susie_subscribed($item) {
  foreach ($item->logins as $user)
    $user->picture = (isset($user->picture) ? str_replace('.bmp', '.jpg', str_replace('/userprofil/', '/userprofil/profilview/', $user->picture)) : NULL_STRING);
  return (isset($item->logins) ? $item->logins : NULL_STRING);
}

function susie_teacher_name($item) {
  return (is_susie($item) ? $item->maker->title : NULL_STRING);
}

function projects_title($item) {
  return (isset($item->acti_title) ? $item->acti_title : NULL_STRING);
}

function projects_module($item) {
  return (isset($item->title_module) ? $item->title_module : NULL_STRING);
}

function projects_start($item) {
  return (isset($item->begin_acti) ? $item->begin_acti : NULL_STRING);
}

function projects_end($item) {
  return (isset($item->end_acti) ? $item->end_acti : NULL_STRING);
}

function projects_subscribed($item) {
  return (isset($item->registered) ? ($item->registered == 1 ? 'true' : 'false') : NULL_STRING);
}

function projects_scolaryear($item) {
  return (isset($item->scolaryear) ? $item->scolaryear : NULL_STRING);
}

function projects_codemodule($item) {
  return (isset($item->codemodule) ? $item->codemodule : NULL_STRING);
}

function projects_codeinstance($item) {
  return (isset($item->codeinstance) ? $item->codeinstance : NULL_STRING);
}

function projects_codeacti($item) {
  return (isset($item->codeacti) ? $item->codeacti : NULL_STRING);
}

function project_scolaryear($item) {
  return (isset($item->scolaryear) ? $item->scolaryear : NULL_STRING);
}

function project_codemodule($item) {
  return (isset($item->codemodule) ? $item->codemodule : NULL_STRING);
}

function project_codeinstance($item) {
  return (isset($item->codeinstance) ? $item->codeinstance : NULL_STRING);
}

function project_codeacti($item) {
  return (isset($item->codeacti) ? $item->codeacti : NULL_STRING);
}

function project_instance_location($item) {
  return (isset($item->instance_location) ? $item->instance_location : NULL_STRING);
}

function project_module_title($item) {
  return (isset($item->module_title) ? $item->module_title : NULL_STRING);
}

function project_id_activite($item) {
  return (isset($item->id_activite) ? $item->id_activite : NULL_STRING);
}

function project_project_title($item) {
  return (isset($item->project_title) ? $item->project_title : NULL_STRING);
}

function project_type_title($item) {
  return (isset($item->type_title) ? $item->type_title : NULL_STRING);
}

function project_type_code($item) {
  return (isset($item->type_code) ? $item->type_code : NULL_STRING);
}

function project_register($item) {
  return (isset($item->register) ? $item->register : NULL_STRING);
}

function project_register_prof($item) {
  return (isset($item->register_prof) ? $item->register_prof : NULL_STRING);
}

function project_nb_min($item) {
  return (isset($item->nb_min) ? $item->nb_min : NULL_STRING);
}

function project_nb_max($item) {
  return (isset($item->nb_max) ? $item->nb_max : NULL_STRING);
}

function project_begin($item) {
  return (isset($item->begin) ? $item->begin : NULL_STRING);
}

function project_end($item) {
  return (isset($item->end) ? $item->end : NULL_STRING);
}

function project_end_register($item) {
  return (isset($item->end_register) ? $item->end_register : NULL_STRING);
}

function project_is_rdv($item) {
  return (isset($item->is_rdv) ? $item->is_rdv : NULL_STRING);
}

function project_instance_allowed($item) {
  return (isset($item->instance_allowed) ? $item->instance_allowed : NULL_STRING);
}

function project_title($item) {
  return (isset($item->title) ? $item->title : NULL_STRING);
}

function project_description($item) {
  return (isset($item->description) ? $item->description : NULL_STRING);
}

function project_closed($item) {
  return (isset($item->closed) ? $item->closed : NULL_STRING);
}

function project_over($item) {
  return (isset($item->over) ? $item->over : NULL_STRING);
}

function project_date_access($item) {
  return (isset($item->date_access) ? $item->date_access : NULL_STRING);
}

function project_instance_registered($item) {
  return (isset($item->instance_registered) ? $item->instance_registered : NULL_STRING);
}

function project_user_project_status($item) {
  return (isset($item->user_project_status) ? $item->user_project_status : NULL_STRING);
}

function project_note($item) {
  return (isset($item->note) ? $item->note : NULL_STRING);
}

function project_root_slug($item) {
  return (isset($item->root_slug) ? $item->root_slug : NULL_STRING);
}

function project_forum_path($item) {
  return (isset($item->forum_path) ? $item->forum_path : NULL_STRING);
}

function project_user_project_master($item) {
  return (isset($item->user_project_master) ? $item->user_project_master : NULL_STRING);
}

function project_user_project_code($item) {
  return (isset($item->user_project_code) ? $item->user_project_code : NULL_STRING);
}

function project_user_project_title($item) {
  return (isset($item->user_project_title) ? $item->user_project_title : NULL_STRING);
}

function project_registered_instance($item) {
  return (isset($item->registered_instance) ? $item->registered_instance : NULL_STRING);
}

function project_registered($item) {
  return (isset($item->registered) ? $item->registered : NULL_STRING);
}

function project_notregistered($item) {
  return (isset($item->notregistered) ? $item->notregistered : NULL_STRING);
}

function project_urls($item) {
  return (isset($item->urls) ? $item->urls : NULL_STRING);
}

function modules_title($item) {
  return (isset($item->title) ? $item->title : NULL_STRING);
}

function modules_id($item) {
  return (isset($item->id_instance) ? $item->id_instance : NULL_STRING);
}

function modules_date($item) {
  return (isset($item->date_ins) ? $item->date_ins : NULL_STRING);
}

function modules_scolaryear($item) {
  return (isset($item->scolaryear) ? $item->scolaryear : NULL_STRING);
}

function modules_grade($item) {
  return (isset($item->grade) ? $item->grade : NULL_STRING);
}

function modules_credits($item) {
  return (isset($item->credits) ? $item->credits : NULL_STRING);
}

function modules_barrage($item) {
  return (isset($item->barrage) ? $item->barrage : NULL_STRING);
}

function modules_rating($item) {
  return (isset($item->module_rating) ? $item->module_rating : NULL_STRING);
}

function modules_instance_semester($item) {
  return (isset($item->instance_semester) ? $item->instance_semester : NULL_STRING);
}

function modules_semesters($item) {
  return (isset($item->semesters) ? $item->semesters : NULL_STRING);
}

function modules_semester($item) {
  return (isset($item->semester) ? $item->semester : NULL_STRING);
}

function mark_title($item) {
  return (isset($item->title) ? $item->title : NULL_STRING);
}

function mark_module($item) {
  return (isset($item->titlemodule) ? $item->titlemodule : NULL_STRING);
}

function mark_date($item) {
  return (isset($item->date) ? $item->date : NULL_STRING);
}

function mark_mark($item) {
  return (isset($item->final_note) ? ((string)$item->final_note) : NULL_STRING);
}

function mark_scolaryear($item) {
  return (isset($item->scolaryear) ? $item->scolaryear : NULL_STRING);
}

function mark_corrector($item) {
  return (isset($item->corrector) ? $item->corrector : NULL_STRING);
}

function mark_comment($item) {
  return (isset($item->comment) ? $item->comment : NULL_STRING);
}

function messages_title($item) {
  return (isset($item->title) ? str_replace('a href="', 'a href="https://intra.epitech.eu', $item->title) : NULL_STRING);
}

function messages_user($item) {
	$photo = str_replace('.bmp', '.jpg', str_replace('/userprofil/', '/userprofil/profilview/', $item->user->picture));
	$item->user->picture = $photo;
  return (isset($item->user) ? $item->user : NULL_STRING);
}

function messages_content($item) {
  return (isset($item->content) ? str_replace('a href="', 'a href="https://intra.epitech.eu', $item->content) : NULL_STRING);
}

function messages_date($item) {
  return (isset($item->date) ? $item->date : NULL_STRING);
}

function alerts_title($item) {
  return (isset($item->title) ? $item->title : NULL_STRING);
}

function photo_photo($item) {
	return (isset($item->picture) ? $item->picture : NULL_STRING);
}

?>
