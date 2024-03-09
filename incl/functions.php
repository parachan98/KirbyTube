<?php

function getIP() {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function timetostr($timestamp) {
   $timestamp = strtotime($timestamp);	
	   
   $strTime = array("second", "minute", "hour", "day", "month", "year");
   $length = array("60","60","24","30","12","10");

   $currentTime = time();
   if($currentTime >= $timestamp) {
		$diff     = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
		$diff = $diff / $length[$i];
	}
		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "s ago";
	}
}

function commentTimeAgo($timestamp) {
	$timestamp = strtotime($timestamp);
    $currentTimestamp = time();
    $timeAgo = $currentTimestamp - $timestamp;

    $days = floor($timeAgo / (60 * 60 * 24));
    $hours = floor(($timeAgo % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($timeAgo % (60 * 60)) / 60);

    $result = '';
    if ($days > 0) {
        $result .= $days . ' day' . ($days != 1 ? 's' : '') . ', ';
    }
    if ($hours > 0) {
        $result .= $hours . ' hour' . ($hours != 1 ? 's' : '') . ', ';
    }
    $result .= $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';

    return $result;
}

function randstr($len, $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-"){
    return substr(str_shuffle($charset),0,$len);
}

function array_has_dupes($array) {
   return count($array) !== count(array_unique($array));
}

function error($msg) {
	echo '<table width="790" align="center" bgcolor="#C00" cellpadding="6" cellspacing="3" border="0">
	<tr>
		<td align="center" bgcolor="#FFFFFF">
			<p class="error">
				' . $msg . '
			</p>
		</td>
	</tr>
</table><br>
';
}

?>