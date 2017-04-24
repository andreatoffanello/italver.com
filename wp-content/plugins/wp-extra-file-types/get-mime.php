<?php

$url = 'http://www.freeformatter.com/mime-types-list.html';

$ch = curl_init();
$timeout = 30;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data = curl_exec($ch);
curl_close($ch);

$search_start = '<div id="mime-types-list">';
$search_end   = '</div>';

$start = stripos($data,$search_start)+strlen($search_start);

$data  = substr($data,$start);

$end   = stripos($data,$search_end);

$data  = substr($data,0,$end-1);

$data  = trim($data);
$data  = preg_replace('#\s+#',' ',$data);
$data  = str_replace('> <','><',$data);

$data  = preg_replace('#<h2(.*)</h2>#i','',$data);
$data  = preg_replace('#<table([^>]*)>#i','',$data);
$data  = preg_replace('#<thead([^>]*)>#i','',$data);
$data  = str_ireplace('</thead>','',$data);
$data  = str_ireplace('</table>','',$data);
$data  = str_ireplace('<tbody>','',$data);
$data  = str_ireplace('</tbody','',$data);

$lista = explode('</tr>',$data);
unset($lista[0]);
unset($lista[1]);

$array = array();

$extra = array(
 '.tgz' => array( 'application/x-gzip','Slackware Package TGZ',false),
 '.txz' => array( 'application/x-xz','Slackware Package TXZ',false),
 '.xz'  => array( 'application/x-xz','Xz compressed file',false),
 '.msp' => array( 'application/octet-stream', 'Microsoft Patch File',false),
 '.msu' => array( 'application/octet-stream', 'Microsoft Update File', false),
 '.bld' => array( 'application/octet-stream', 'Energy Pro File', false),
 '.m4r' => array( 'audio/aac', 'iPhone ringtone', false),
 '.dot' => array( 'application/msword', 'Microsoft Word Template', false),
 '.gpx' => array('application/gpx+xml', 'GPS eXchange Format', false),
 '.woff2' => array('application/font-woff2', 'Woff2 Font', false)
);

$exts = array();

foreach ($lista as $elemento) {

	$elemento = str_ireplace('<td>','',$elemento);
	$elemento = str_ireplace('<tr>','',$elemento);
	$elemento = trim($elemento);
	if (!$elemento) {
		continue;
	}
	$parti = explode('</td>',$elemento);
	if (!isset($parti[1])) { continue; }
	$applicazione = trim($parti[0]);
	$tipo         = trim($parti[1]);
	$estensioni   = explode(',',str_replace(' ','',$parti[2]));
	
	$tmp = new \stdClass();
	$tmp->application = $applicazione;
	$tmp->mime_type   = $tipo;
	$tmp->extensions  = $estensioni;
	
	$exts = array_merge($exts,$estensioni);
	
	$array[] = $tmp;
	
}

asort($exts);

foreach ($extra as $ext=>$dati) {
	if (!in_array($ext,$exts)) {
		$tmp = new \stdClass();
		$tmp->application = $dati[1];
		$tmp->mime_type   = $dati[0];
		$tmp->extensions  = array( $ext );
		$array[] = $tmp;
	}
}

function doSort($a,$b) {
		if ($a->application < $b->application) return -1;
		if ($a->application > $b->application) return +1;
		return 0;
}

usort($array,'doSort');


file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'mime-list.txt',serialize($array));

?>