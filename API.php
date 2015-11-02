<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-type: application/json');
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

define('INSIDE', true);

require_once('Binding.php');
require_once('Tools.php');
require_once('Functions.php');


class API {

  protected $login;
  protected $passwd;
  protected $curl;
  protected $susid;
  protected $action;
  protected $tmp_fname;
  protected $repLog;

  public function __construct($login, $passwd, $action) {
    $this->login = $login;
    $this->passwd = $passwd;
    $this->action = $action;
    $POST_array = array('login' => urlencode($this->login), 'password' => urldecode($this->passwd));
    $this->curl = curl_init('https://intra.epitech.eu/?format=json');
    $this->tmp_fname = tempnam('/tmp', 'COOKIE');
    curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($this->curl, CURLOPT_COOKIE, 'language=fr');
    curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->tmp_fname);
    curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmp_fname);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($this->curl, CURLOPT_POST, true);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $POST_array);
    $this->repLog = curl_exec($this->curl);
  }

  public function getAPI() {
    if ($this->action == 'login')
      $this->checkLogin();
    else if ($this->action == 'home')
      $this->getHome();
    else if ($this->action == 'day')
      $this->getDays(0);
  	else if ($this->action == 'dayPlanning')
      $this->getDaysPlanning(6);
  	else if ($this->action == "nextDayPlanning")
  		$this->getNextDaysPlanning(6);
    else if ($this->action == 'week')
      $this->getDays(6);
    else if ($this->action == 'appointments')
      $this->getDays(30);
    else if ($this->action == 'projects')
      $this->getProjects();
    else if ($this->action == 'project')
      $this->getProject();
    else if ($this->action == 'susies')
      $this->getSusies();
    else if ($this->action == 'susie')
      $this->getSusie();
    else if ($this->action == 'subscribeSusie')
      $this->subscribeSusie(true, NULL);
    else if ($this->action == 'unsubscribeSusie')
      $this->unsubscribeSusie(true, NULL);
    else if ($this->action == 'changeSusie')
      $this->changeSusie();
    else if ($this->action == 'modules')
      $this->getModules();
    else if ($this->action == 'marks')
      $this->getMarks();
    else if ($this->action == 'messages')
      $this->getMessages();
    else if ($this->action == 'alerts')
      $this->getAlerts();
    else if ($this->action == 'eventAlerts')
      $this->getEventsAlerts();
    else if ($this->action == 'profile')
      $this->getProfile();
    else if ($this->action == 'trombi')
      $this->getTrombi();
    else if ($this->action == 'photo')
      $this->getPhoto();
    else if ($this->action == 'token')
      $this->registerToken();
    else if ($this->action == 'aroundEpitech')
      $this->getAroundEpitech();
  	else if ($this->action == 'eventRegister')
  		$this->eventRegister();
  	else if ($this->action == 'eventUnregister')
  		$this->eventUnregister();
    else
    	echo json_encode(array('error' => 'incorrect action'));
  }

  private function getPage($url, $close) {
    curl_setopt($this->curl, CURLOPT_URL, $url);
    $output = curl_exec($this->curl);
    $output = htmlspecialchars_decode($output);
		if ($close)
			curl_close($this->curl);
    return $output;
  }

  private function checkLogin() {
  	if ($this->repLog === FALSE)
  		echo json_encode(array('login' => '0'));
  	else if ((strstr($this->repLog, 'mot de passe sont invalides.') == FALSE) && (strstr($this->repLog, 'Veuillez vous connecter') == FALSE))
      echo json_encode(array('login' => '1'));
    else
      echo json_encode(array('login' => '0'));
  }

	private function getHome() {
		$output = json_decode(clean_json(htmlspecialchars_decode($this->repLog)));
		$name = (isset($output[0]->infos->title) ? $output[0]->infos->title : $this->login);
		$picture = (isset($output[0]->infos->picture) ? 'http://cdn.local.epitech.net/userprofil/profilview/' . str_replace('.bmp', '.jpg', $output[0]->infos->picture) : 'null');
		$my_netsoul = round((isset($output[0]->current->active_log) ? $output[0]->current->active_log : '30'));
		$min_netsoul = (isset($output[0]->current->nslog_min) ? $output[0]->current->nslog_min : '20');
		$max_netsoul = (isset($output[0]->current->nslog_norm) ? $output[0]->current->nslog_norm : '40');
		$netsoul = (($my_netsoul < $min_netsoul) ? $my_netsoul . 'h < ' . $min_netsoul . 'h' : (($my_netsoul < $max_netsoul) ? $my_netsoul . 'h < ' . $max_netsoul . 'h' : $my_netsoul . 'h'));
		$last_mark = (isset($output[0]->board->notes) ? (isset($output[0]->board->notes[0]) ? $output[0]->board->notes[0] : 'null') : 'null');

		$output = $this->getPage('https://intra.epitech.eu', false);
		$fire = 'green';
		$w = "(.*)";
		if (preg_match("/\<span class=\'red alert\' title=\'$w\'\>red alert\<\/span\>/", $output))
			$fire = 'red';
		else if (preg_match("/\<span class=\'orange alert\' title=\'$w\'\>orange alert\<\/span\>/", $output))
			$fire = 'orange';

		$duration = '+30 days';
    $today = strftime('%Y-%m-%d', strtotime('now'));
    $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/planning/load?format=json&start=' . $today . '&end=' . strftime('%Y-%m-%d', strtotime($duration, strtotime($today))), true)));
		if (!isset($output))
			$output = array();
    $events = array();
		foreach ($output as $key => $val) {
			if (isset($val->event_registered)) {
				if ($val->event_registered == 'null') continue;
			}
			else if (isset($val->rdv_group_registered)) {
				if ($val->rdv_group_registered == 'null') continue;
			}
			if (isset($val->calendar_type) || (isset($val->calendar) && isset($val->calendar->type))) {
				if (isset($val->subscribed))
					if ($val->subscribed != 'true') continue;
			}
			$events[] = bind_api(template_days(), $val);
		}
		usort($events, 'sortByStart');
		$next_event = 'null';
		$next_susie = 'null';

		foreach ($events as $val) {
			if (strtotime($val['end']) < strtotime('now'))
				continue;
			if ($val['module'] == 'Susie' && $next_susie == 'null')
				$next_susie = $val;
			else if ($next_event == 'null')
				$next_event = $val;
		}
		echo json_encode(array('login' => $this->login, 'name' => $name, 'picture' => $picture, 'fire' => $fire, 'netsoul' => $netsoul, 'next_event' => $next_event, 'next_susie' => $next_susie, 'last_mark' => $last_mark));
	}

  private function getDays($default_duration) {
    if (isset($_REQUEST['duration']))
      $duration = '+' . $_REQUEST['duration'] . ' day' . ($_REQUEST['duration'] > 1 ? 's' : '');
    else
      $duration = '+' . $default_duration . ' day' . ($default_duration > 1 ? 's' : '');
    if (isset($_REQUEST['start']))
      $today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime($_REQUEST['start'])) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime($_REQUEST['start'])));
    else
      $today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime('now')) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime('now')));
    $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/planning/load?format=json&start=' . $today . '&end=' . strftime('%Y-%m-%d', strtotime($duration, strtotime($today))), true)));
		if (!isset($output))
			$output = array();
    $events = array();
    foreach ($output as $key => $val) {
      if (isset($val->event_registered)) {
				if ($val->event_registered == 'null') continue;
			}
			else if (isset($val->rdv_group_registered)) {
				if ($val->rdv_group_registered == 'null') continue;
			}
			if (isset($val->calendar_type) || (isset($val->calendar) && isset($val->calendar->type))) {
				if (isset($val->subscribed))
					if ($val->subscribed != 'true') continue;
			}
      $events[] = bind_api(template_days(), $val);
    }
    usort($events, 'sortByStart');
    //    $events = array_merge($events, $events);
    echo json_encode($events);
  }

	private function getDaysPlanning($default_duration) {
		if (isset($_REQUEST['duration']))
			$duration = '+' . $_REQUEST['duration'] . ' day' . ($_REQUEST['duration'] > 1 ? 's' : '');
		else
			$duration = '+' . $default_duration . ' day' . ($default_duration > 1 ? 's' : '');
		if (isset($_REQUEST['start']))
			$today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime($_REQUEST['start'])) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime($_REQUEST['start'])));
		else
			$today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime('now')) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime('now')));
		$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/planning/load?format=json&start=' . $today . '&end=' . strftime('%Y-%m-%d', strtotime($duration, strtotime($today))), true)));
		if (!isset($output))
			$output = array();	
		$events = array();
		foreach ($output as $key => $val) {
			if (isset($val->module_registered)) {
				if ($val->module_registered != 1) continue;
			}
			$events[] = bind_api(template_dayPlanning(), $val);
		}
		usort($events, 'sortByStart');
		//    $events = array_merge($events, $events);
		echo json_encode($events);
  }

private function getNextDaysPlanning($default_duration) {
		if (isset($_REQUEST['duration']))
			$duration = '+' . $_REQUEST['duration'] . ' day' . ($_REQUEST['duration'] > 1 ? 's' : '');
		else
			$duration = '+' . $default_duration . ' day' . ($default_duration > 1 ? 's' : '');
		if (isset($_REQUEST['start']))
			$today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime($_REQUEST['start'])) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime($_REQUEST['start'])));
		else
			$today = strftime('%Y-%m-%d', strtotime(((strftime('%u', strtotime('+1 week')) == 1 || $duration != '+6 days') ? 'now' : 'last monday'), strtotime('+1 week')));
		$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/planning/load?format=json&start=' . $today . '&end=' . strftime('%Y-%m-%d', strtotime($duration, strtotime($today))), true)));
		if (!isset($output))
			$output = array();	
		$events = array();
		foreach ($output as $key => $val) {
			if (isset($val->module_registered)) {
				if ($val->module_registered != 1) continue;
			}
			$events[] = bind_api(template_dayPlanning(), $val);
		}
		usort($events, 'sortByStart');
		//    $events = array_merge($events, $events);
		echo json_encode($events);
  }
  private function eventRegister() {
		if (!isset($_REQUEST['scolaryear']) || !isset($_REQUEST['codemodule']) || !isset($_REQUEST['codeinstance']) || !isset($_REQUEST['codeacti']) || !isset($_REQUEST['codeevent']))
			echo json_encode(array('error' => 'missing arguments'));
		else {
			$scolaryear = $_REQUEST['scolaryear'];
			$codemodule = $_REQUEST['codemodule'];
			$codeinstance =	$_REQUEST['codeinstance'];
			$codeacti = $_REQUEST['codeacti'];
			$codeevent = $_REQUEST['codeevent'];
			$output = clean_json($this->getPage('https://intra.epitech.eu/module/' . $scolaryear . '/' . $codemodule . '/' . $codeinstance . '/' . $codeacti . '/' . $codeevent . '/register?format=json', true));
		if (strstr(json_encode($output), 'error') === FALSE)
			echo 'ok';
		else
			echo json_encode($output);
  	//https://intra.epitech.eu/module/2013/G-EPI-007/PAR-0-1/acti-148682/event-139709/unregister?format=json
		}
  }

  private function eventUnregister() {
  	if (!isset($_REQUEST['scolaryear']) || !isset($_REQUEST['codemodule']) || !isset($_REQUEST['codeinstance']) || !isset($_REQUEST['codeacti']) || !isset($_REQUEST['codeevent']))
			echo json_encode(array('error' => 'missing arguments'));
  	else {
			$scolaryear = $_REQUEST['scolaryear'];
			$codemodule = $_REQUEST['codemodule'];
			$codeinstance =	$_REQUEST['codeinstance'];
			$codeacti = $_REQUEST['codeacti'];
			$codeevent = $_REQUEST['codeevent'];
			$output = clean_json($this->getPage('https://intra.epitech.eu/module/' . $scolaryear . '/' . $codemodule . '/' . $codeinstance . '/' . $codeacti . '/' . $codeevent . '/unregister?format=json', true));
		if (strstr(json_encode($output), 'error') === FALSE)
			echo 'ok';
		else
			echo json_encode($output);
		//https://intra.epitech.eu/module/2013/G-EPI-007/PAR-0-1/acti-148682/event-139709/unregister?format=json
		}
	}

	private function getSusie() {
		if (!isset($_REQUEST['id']))
			echo json_encode(array('error' => 'no susie id found'));
		else {
			$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/planning/587/' . $_REQUEST['id'] . '/?format=json', true)));
			if (count($output) > 0)
				echo json_encode(bind_api(template_susie(), $output[0]));
			else
				echo json_encode(array('error' => 'susie not found'));
		}
	}

  private function getSusies() {
    $startDate = strftime('%Y-%m-%d', strtotime('now'));
    $endDate = strftime('%Y-%m-%d', strtotime('next sunday', strtotime($startDate)));
    if (!isset($_REQUEST['get']))
      echo json_encode(array('error' => 'no get parameter found'));
    else {
		  if ($_REQUEST['get'] == 'registered')
				$endDate = strftime('%Y-%m-%d', strtotime('+2 years'));
			else if ($_REQUEST['get'] == 'all' || $_REQUEST['get'] == 'free') {
				$startDate = strftime('%Y-%m-%d', strtotime((strftime('%u', strtotime($startDate)) == 1 ? 'now' : 'last monday')));
				$endDate = strftime('%Y-%m-%d', strtotime('+7 days', strtotime($startDate)));
			}
      if (isset($_REQUEST['start'])) {
				$startDate = $_REQUEST['start'];
				$endDate = strftime('%Y-%m-%d', strtotime('next sunday', strtotime($startDate)));
      }
      if (isset($_REQUEST['end']))
				$endDate = $_REQUEST['end'];
      if (isset($_REQUEST['duration']))
				$endDate = strftime('%Y-%m-%d', strtotime('+'.$_REQUEST['duration'].' day'.($_REQUEST['duration'] > 1 ? 's' : ''), strtotime($startDate)));
      $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/planning/load?format=json&start=' . $startDate . '&end=' . $endDate, true)));
			if (!isset($output))
				$output = array();
			$eventsRegistered = array();
      $susiesRegistered = array();
      foreach ($output as $key => $val) {
				if (isset($val->event_registered)) {
					if ($val->event_registered == 'null') continue;
				}
				else if (isset($val->rdv_group_registered)) {
					if ($val->rdv_group_registered == 'null') continue;
				}
				if (isset($val->id) && isset($val->calendar_type) && isset($val->subscribed)) {
					if ($val->calendar_type == 'susie' && $val->subscribed == 'true') {
						$susiesRegistered[] = array('week' => strftime('%V', strtotime($val->start)), 'id' => $val->id);
						$eventsRegistered[] = bind_api(template_days(), $val);
					}
					else continue;
				}
				$eventsRegistered[] = bind_api(template_days(), $val);
      }
      $events = array();
      foreach ($output as $key => $val) {
				if (isset($val->id) && isset($val->calendar_type) && isset($val->subscribed)) {
					if (($_REQUEST['get'] == 'free' && $val->calendar_type == 'susie' && $val->subscribed != 'true' && $val->registered < $val->nb_place && strtotime($val->start) >= strtotime('now'))
							|| ($_REQUEST['get'] == 'all' && $val->calendar_type == 'susie')
							|| ($_REQUEST['get'] == 'registered' && $val->calendar_type == 'susie' && $val->subscribed == 'true')) {
						$val->already_subscribed = 'false';
						$val->id_already_subscribed = 0;
						$val->event_same_time = 'null';
						for ($i = 0; $i < count($susiesRegistered); $i++) {
							if ($susiesRegistered[$i]['week'] == strftime('%V', strtotime($val->start)))
								$val->already_subscribed = 'true';
							$val->id_already_subscribed = $susiesRegistered[$i]['id'];
						}
						for ($i = 0; $i < count($eventsRegistered); $i++) {
							if ($val->id != $eventsRegistered[$i]['id']
									&& ((strtotime($eventsRegistered[$i]['start']) >= strtotime($val->start) && strtotime($eventsRegistered[$i]['start']) < strtotime($val->end))
									|| (strtotime($eventsRegistered[$i]['end']) > strtotime($val->start) && strtotime($eventsRegistered[$i]['end']) <= strtotime($val->end))))
								$val->event_same_time = $eventsRegistered[$i]['title'];
						}
						if (($_REQUEST['get'] == 'free' && $val->event_same_time == 'null') || ($_REQUEST['get'] != 'free'))
							$events[] = bind_api(template_susies(), $val);
					}
				}
      }
     	if ($events != null)
      	usort($events, 'sortByStart');
      echo json_encode($events);
    }
  }

  	private function getSusieId(){
  		$output = json_decode(clean_json($this->getPage("https://intra.epitech.eu/planning/my-schedules?format=json", false)));
  		foreach ($output as $key => $value) {
  			if(!strcmp($value->type, "susie"))
  				return $value->id;
  		}
  	}
	private function subscribeSusie($close, $id) {
		$showSuccess = ($id == NULL ? true : false);
		$id = ($id == NULL ? (isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL) : $id);
		if ($id == NULL) {
			echo json_encode(array('message' => 'no susie id found'));
			return false;
		}	
		$susid = $this->getSusieId();
		curl_setopt($this->curl, CURLOPT_URL, 'https://intra.epitech.eu/planning/' . $susid . '/' . $id . '/subscribe');
		$POST_array = array('format' => 'json');
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $POST_array);
		$output = curl_exec($this->curl);
		if ($close)
			curl_close($this->curl);
		$output = str_replace(' ', '', str_replace('\u00e0', 'a', str_replace('\u00e9', 'e', str_replace('\'', '', $output))));
		if (strstr($output, 'VousnepouvezvousinscrirequaunevenementSUSIEparsemaine')) {
			echo json_encode(array('message' => 'already subscribed to an other susie'));
			return false;
		}
		else if (strstr($output, 'Ilestimpossibledesinscrireaunevenementpasse')) {
			echo json_encode(array('message' => 'Cette susie est deja passe.'));
			return false;
		}
		else if (strstr($output,'Vous\u00eatesdejainscritacetevenement')) {
			echo json_encode(array('message' => 'already subscribed for this susie'));
			return false;
		}
		else if (strstr($output, 'Vousneposs\u00e8dezpaslesdroitssuffisantspoureffectuercetteaction')) {
			echo json_encode(array('message' => 'no right'));
			return false;
		}
		else if (strstr($output, 'Allslotsaretaken')) {
			echo json_encode(array('message' => 'full'));
			return false;
		}
		if ($showSuccess)
			echo json_encode(array('message' => 'Inscription reussie'));
		return true;
	}
	private function unsubscribeSusie($close, $id) {
		$showSuccess = ($id == NULL ? true : false);
		$id = ($id == NULL ? (isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL) : $id);
		if ($id == NULL) {
			echo json_encode(array('message' => 'no susie id found'));
			return false;
		}
		$susid = $this->getSusieId();
		curl_setopt($this->curl, CURLOPT_URL, 'https://intra.epitech.eu/planning/' . $susid . '/' . $id . '/unsubscribe');	
		$POST_array = array('format' => 'json');
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $POST_array);
		$output = curl_exec($this->curl);
		if ($close)
			curl_close($this->curl);
		$output = str_replace(' ', '', str_replace('\u00e0', 'a', str_replace('\u00e9', 'e', str_replace('\'', '', $output))));
		if (strstr($output, 'Youdidnotsubscribetothisevent')) {
			echo json_encode(array('message' => 'not subscribed to this susie'));
			return false;
		}
		else if (strstr($output, 'Ilestimpossibledesedesinscriredunesessionmoinsde24heuresavantladatededebut')) {
			echo json_encode(array('message' => 'forbidden to unsubscribe 24h before'));
			return false;
		}
		else if (strstr($output, 'Ilestimpossibledesedesinscriredunevenementpasse')) {
			echo json_encode(array('message' => 'Cette susie est deja passe.'));
			return false;
		}
		if ($showSuccess)
			echo json_encode(array('message' => 'successfully unsubscribed'));
		return true;
	}

	private function changeSusie() {
		if (!isset($_REQUEST['newid']))
			echo json_encode(array('message' => 'no susie newid found'));
		else if (!isset($_REQUEST['oldid']))
			echo json_encode(array('message' => 'no susie oldid found'));
		else {
			if ($this->unsubscribeSusie(false, $_REQUEST['oldid'])) {
				if ($this->subscribeSusie(true, $_REQUEST['newid']))
					echo json_encode(array('message' => 'successfully changed'));
			}
		}
	}

  private function getEventsAlerts() {
		$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/module/board/?format=json&start='.strftime('%Y-%m-%d', strtotime('now')).'&end='.strftime('%Y-%m-%d', strtotime('+1 year')), true)));
		$events = array();
		foreach ($output as $key => $val) {
			$begin_time = (isset($val->begin_acti) ? strtotime($val->begin_acti) : 0);
			$end_time = (isset($val->end_acti) ? strtotime($val->end_acti) : 0);
			$now_time = time();
			if (!strstr($val->type_acti, 'Projet') && !strstr($val->type_acti, 'Soutenance'))
				continue;
			if (strstr($val->type_acti, 'Projet') && !($val->registered == 0 && $now_time <= $end_time && $now_time >= $begin_time))
				continue;
			if (strstr($val->type_acti, 'Soutenance') && !($val->registered == 0 && $now_time <= $end_time && $now_time >= $begin_time))
				continue;
			$events[] = bind_api(template_project(), $val);
		}
		if ($events != null)
			usort($events, 'sortByEnd');
		echo json_encode($events);
	}

	private function getProject() {
		if (!isset($_REQUEST['scolaryear']) || !isset($_REQUEST['codemodule']) || !isset($_REQUEST['codeinstance']) || !isset($_REQUEST['codeacti']))
			echo json_encode(array('error' => 'those parameters are required : scolaryear, codemodule, codeinstance, codeacti'));
		else {
			$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/module/'.$_REQUEST['scolaryear'].'/'.$_REQUEST['codemodule'].'/'.$_REQUEST['codeinstance'].'/'.$_REQUEST['codeacti'].'/project/?format=json', false)));
			if (count($output) > 0) {
				$project = bind_api(template_project(), $output[0]);
				$files = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/module/'.$_REQUEST['scolaryear'].'/'.$_REQUEST['codemodule'].'/'.$_REQUEST['codeinstance'].'/'.$_REQUEST['codeacti'].'/project/file/?format=json', false)));
				$project['files'] = getProjectFiles($files, curl_copy_handle($this->curl));
				foreach ($output[0]->registered as $key => $val) {
					if (isMyProjectGroup($val, $this->login)) {
						$project['master'] = getProjectMaster($val->master);
						$project['members'] = getProjectMembers($val->members);
					}
				}
				echo json_encode($project);
			}
			else
				echo json_encode(array('error' => 'project not found'));
		}
	}

  private function getProjects() {
    if (!isset($_REQUEST['get']))
      echo json_encode(array('error' => 'no get parameter found'));
    else {
      $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/module/board/?format=json&start='.strftime('%Y-%m-%d', strtotime('now')).'&end='.strftime('%Y-%m-%d', strtotime('+1 year')), true)));
      $events = array();
      foreach ($output as $key => $val) {
        $begin_time = (isset($val->begin_acti) ? strtotime($val->begin_acti) : 0);
        $end_time = (isset($val->end_acti) ? strtotime($val->end_acti) : 0);
        $now_time = time();
        if (!strstr($val->type_acti, 'Projet'))
          continue;
        if ($_REQUEST['get'] == 'registered' && $val->registered != 1)
          continue;
        if (($_REQUEST['get'] == 'free') && !($val->registered == 0 && $now_time <= $end_time && $now_time >= $begin_time))
          continue;
        $events[] = bind_api(template_projects(), $val);
      }
      if ($events != null)
        usort($events, 'sortByEnd');
      echo json_encode($events);
    }
  }

  private function getModules() {
    $user = $this->login;
    $output = $this->getPage('https://intra.epitech.eu/user/'.urlencode($user).'/', true);
    if (strstr($output, "User « ".$user." » not found") != FALSE || strstr($output, "L'utilisateur « ".$user." » n'a pas été trouvé") != FALSE)
      echo json_encode(array('error' => 'user not found'));
    else {
      $pos = strpos($output, 'window.user = $.extend(window.user || {}, {');
      $pos = strpos($output, 'modules', $pos);
      $pos2 = strpos($output, 'notes: [');
      $output = substr($output, $pos, $pos2 - $pos);
      $output = str_replace('modules:', '', $output);
      $end = strrpos($output, ',');
      $output = substr($output, 0, $end);
      $output = array_reverse(json_decode($output));
      $events = array();
      foreach ($output as $key => $val) {
        $events[] = bind_api(template_modules(), $val);
      }
      echo json_encode($events);
    }
  }

  private function getMarks() {
  	$user = $this->login;
		if (isset($_REQUEST['user'])) {
			if ($_REQUEST['user'] != "")
				$user = $_REQUEST['user'];
		}
		$output = $this->getPage('https://intra.epitech.eu/user/'.urlencode($user).'/', true);
		if (strstr($output, "User « ".$user." » not found") != FALSE || strstr($output, "L'utilisateur « ".$user." » n'a pas été trouvé") != FALSE)
			echo json_encode(array("error" => "user not found"));
		else {
			$pos = strpos($output, 'notes: [');
			$pos2 = strpos($output, '});', $pos);
			$output = substr($output, $pos, $pos2 - $pos);
			$output = substr($output, 6);
			$output = json_decode($output);
			$output = array_reverse($output);
			$events = array();
			foreach ($output as $key => $val) {
				$events[] = bind_api(template_mark(), $val);
			}
			echo json_encode($events);
		}
	}

  private function getMessages() {
    $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/user/notification/message?format=json', true)));
    $events = array();
    $output = $output[0]->msgs;
    foreach ($output as $key => $val) {
      $events[] = bind_api(template_messages(), $val);
    }
    echo json_encode($events);
  }

  private function getAlerts() {
    $output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/intra/user/notification/alert?format=json', true)));
    $events = array();
    foreach ($output as $key => $val) {
      $events[] = bind_api(template_alerts(), $val);
    }
    echo json_encode($events);
  }

  private function getProfile() {
		if (isset($_REQUEST['user']))
			$login_rapport = $_REQUEST['user'];
		else
			$login_rapport = $this->login;
    $output_user = json_decode(clean_json($this->getPage('https://intra.epitech.eu/user/' . $login_rapport . '/?format=json', false)));
		$output_binome = json_decode(clean_json($this->getPage('https://intra.epitech.eu/user/' . $login_rapport . '/binome/?format=json', true)));
		$output = '[' . json_encode($output_user[0]) . ',' . json_encode($output_binome[0]) . ']';
    echo $output;
  }

	private function getTrombi() {
		if (!isset($_REQUEST['group']))
			echo json_encode(array('error' => 'group parameter is required'));
		else {
			$group = $_REQUEST['group'];
			if (file_exists('./Trombi/Groups/' . $group . '.json') == FALSE)
				echo json_encode(array('error' => 'this group does not exist'));
			else {
				$json = json_decode(file_get_contents('./Trombi/Groups/' . $group . '.json'));
				echo json_encode($json->users);
			}
		}
	}

  private function getPhoto() {
		if (isset($_REQUEST['user'])) {
			$output = json_decode(clean_json($this->getPage('https://intra.epitech.eu/user/' . $_REQUEST['user'] . '/?format=json', true)));
			if (count($output) > 0 && isset($output[0]->picture))
				echo json_encode(bind_api(template_photo(), $output[0]));
			else
				echo json_encode(array('error' => 'user not found'));
		}
		else
			echo json_encode(array('photo' => 'http://cdn.local.epitech.net/userprofil/profilview/' . $this->login . '.jpg'));
  }

	private function registerToken() {
		if (!isset($_REQUEST['token']) || !isset($_REQUEST['scolaryear']) || !isset($_REQUEST['codemodule']) || !isset($_REQUEST['codeinstance']) || !isset($_REQUEST['codeacti']) || !isset($_REQUEST['codeevent']))
			echo json_encode(array('error' => 'those parameters are required : token, scolaryear, codemodule, codeinstance, codeacti, codeevent'));
		else {
			$POST_array = array('token' => htmlentities($_REQUEST['token']), 'rate' => '1', 'comment' => '');
			curl_setopt($this->curl, CURLOPT_URL, 'https://intra.epitech.eu/intra/module/' . $_REQUEST['scolaryear'] . '/' . $_REQUEST['codemodule'] . '/' . $_REQUEST['codeinstance'] . '/' . $_REQUEST['codeacti'] . '/' . $_REQUEST['codeevent'] . '/token?format=json');
			curl_setopt($this->curl, CURLOPT_POST, true);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $POST_array);
			curl_exec($this->curl);
			curl_setopt($this->curl, CURLOPT_URL, 'https://intra.epitech.eu/module/' . $_REQUEST['scolaryear'] . '/' . $_REQUEST['codemodule'] . '/' . $_REQUEST['codeinstance'] . '/' . $_REQUEST['codeacti'] . '/?format=json');
			$output = curl_exec($this->curl);
			curl_close($this->curl);
			$output = clean_json(htmlspecialchars_decode($output));
			$output = json_decode($output);
			if (!isset($output[0]) || !isset($output[0]->events))
				echo json_encode(array('status' => 'ko'));
			else {
				$output = $output[0]->events;
				foreach ($output as $key => $val) {
					if ($val->code == $_REQUEST['codeevent']) {
						if ($val->allow_token == '1' && $val->user_status != 'null')
							echo json_encode(array('status' => 'ok'));
						else
							echo json_encode(array('status' => 'ko'));
						continue;
					}
				}
			}
		}
	}

	private function getAroundEpitech() {
		$bordeaux = array('name' => 'Bordeaux', 'phone' => '01.44.08.00.14', 'address' => '85, rue du Jardin Public\n33000 BORDEAUX', 'mail' => 'bordeaux@epitech.eu', 'website' => 'http://www.bordeaux.epitech.eu', 'image' => 'https://epiglab.fr/images/bordeaux_epitech.jpg');
		$lille = array('name' => 'Lille', 'phone' => '01.44.08.00.10', 'address' => '5-9, rue du Palais Rihour\n59000 LILLE', 'mail' => 'lille@epitech.eu', 'website' => 'http://www.lille.epitech.eu', 'image' => 'https://epiglab.fr/images/lille_epitech.jpg');
		$lyon = array('name' => 'Lyon', 'phone' => '01.44.08.00.13', 'address' => '156, rue Paul Bert\n69003 LYON', 'mail' => 'lyon@epitech.eu', 'website' => 'http://www.lyon.epitech.eu', 'image' => 'https://epiglab.fr/images/lyon_epitech.jpg');
		$marseille = array('name' => 'Marseille','phone' => '01.44.08.01.37', 'address' => '21, rue Mires\n13002 MARSEILLE', 'mail' => 'marseille@epitech.eu', 'website' => 'http://www.marseille.epitech.eu', 'image' => 'https://epiglab.fr/images/marseille_epitech.jpg');
		$montpellier = array('name' => 'Montpellier','phone' => '01.44.08.00.75', 'address' => '16, boulevard du Jeu de Paume\n34000 MONTPELLIER', 'mail' => 'montpellier@epitech.eu', 'website' => 'http://www.montpellier.epitech.eu', 'image' => 'https://epiglab.fr/images/montpellier_epitech.jpg');
		$nancy = array('name' => 'Nancy','phone' => '01.44.08.00.36', 'address' => '113, rue Saint Georges\n54000 NANCY', 'mail' => 'nancy@epitech.eu', 'website' => 'http://www.nancy.epitech.eu', 'image' => 'https://epiglab.fr/images/nancy_epitech.jpg');
		$nantes = array('name' => 'Nantes','phone' => '01.44.08.00.11', 'address' => '16bis-18, rue Flandres Dunkerque\n44100 NANTES', 'mail' => 'nantes@epitech.eu', 'website' => 'http://www.nantes.epitech.eu', 'image' => 'https://epiglab.fr/images/nantes_epitech.jpg');
		$nice = array('name' => 'Nice','phone' => '01.44.08.00.26', 'address' => '6, rue Desboutin\n06300 NICE', 'mail' => 'nice@epitech.eu', 'website' => 'http://www.nice.epitech.eu', 'image' => 'https://epiglab.fr/images/nice_epitech.jpg');
		$paris = array('name' => 'Paris','phone' => '01.44.08.00.50', 'address' => '24, rue Pasteur\n94270 Le Kremlin Bicêtre', 'mail' => 'paris@epitech.eu', 'website' => 'http://www.paris.epitech.eu', 'image' => 'https://epiglab.fr/images/paris_epitech.jpg');
		$rennes = array('name' => 'Rennes','phone' => '01.80.51.71.10', 'address' => '12, square Vercingétorix\n35000 RENNES', 'mail' => 'rennes@epitech.eu', 'website' => 'http://www.rennes.epitech.eu', 'image' => 'https://epiglab.fr/images/rennes_epitech.jpg');
		$strasbourg = array('name' => 'Strasbourg','phone' => '01.44.08.00.12', 'address' => '4, rue du Dôme\n67000 STRASBOURG', 'mail' => 'strasbourg@epitech.eu', 'website' => 'http://www.strasbourg.epitech.eu', 'image' => 'https://epiglab.fr/images/strasbourg_epitech.jpg');
		$toulouse = array('name' => 'Toulouse','phone' => '01.44.08.00.15', 'address' => '19, rue Bayard\n31000 TOULOUSE', 'mail' => 'toulouse@epitech.eu', 'website' => 'http://www.toulouse.epitech.eu', 'image' => 'https://epiglab.fr/images/toulouse_epitech.jpg');
		echo json_encode(array('countries' => array($bordeaux, $lille, $lyon, $marseille, $montpellier, $nancy, $nantes, $nice, $paris, $rennes, $strasbourg, $toulouse)));
  }

}

if (!isset($_REQUEST['login']) || !isset($_REQUEST['password']) || !isset($_REQUEST['action'])) {
  echo json_encode(array('error' => 'login, password and action required'));
  exit;
}

$Api = new API($_REQUEST['login'], urlencode($_REQUEST['password']), $_REQUEST['action']);
$Api->getApi();
?>