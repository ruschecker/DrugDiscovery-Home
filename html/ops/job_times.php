<?php
// This file is part of BOINC.
// http://boinc.berkeley.edu
// Copyright (C) 2008 University of California
//
// BOINC is free software; you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License
// as published by the Free Software Foundation,
// either version 3 of the License, or (at your option) any later version.
//
// BOINC is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with BOINC.  If not, see <http://www.gnu.org/licenses/>.

// tool for estimating FLOPS per job for a given app

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("../inc/db.inc");
require_once("../inc/util_ops.inc");

db_init();

$hist = array();
$quantum = 1e10;
$mean = 0;
$count = 0;
$varsum = 0;

function handle_result($result) {
    global $hist;
    global $quantum;
    global $mean;
    global $count;
    global $varsum;

    $flops = $result->cpu_time * $result->p_fpops;
    //printf("%e<br>\n", $flops);
    $n = (int) ($flops/$quantum);
    if (!array_key_exists($n, $hist)) {
        $hist[$n] = 0;
    }
    $hist[$n]++;
    $count++;
    $delta = $flops - $mean;
    $mean += $delta/$count;
    $varsum += $delta*($flops-$mean);
}

function show_stats() {
    global $mean;
    global $count;
    global $varsum;
    global $sum;

    $stdev = sqrt($varsum/($count-1));
    printf("mean: %e<br>stdev: %e", $mean, $stdev);
}

function show_stats_hist() {    //deprecated
    global $hist;
    global $quantum;

    $sum = 0;
    $n = 0;
    foreach ($hist as $i=>$v) {
        $sum  += $quantum*$i*$v;
        $n += $v;
    }
    $mean = $sum/$n;
    echo "mean: ";
    printf("%e", $mean);

    $sum = 0;
    foreach ($hist as $i=>$v) {
        $d = ($mean - $quantum*$i);
        $sum  += $d*$d*$v;
    }
    $stdev = sqrt($sum/$n);
    echo "<br>stdev: ";
    printf("%e", $stdev);
}

function show_as_xml() {
    global $hist;
    global $quantum;
    echo "<pre>";
    foreach ($hist as $i=>$v) {
        echo "&lt;bin>
    &lt;value>";
        printf("%e", $quantum*$i);
        echo "&lt;/value>
    &lt;count>$v&lt;/count>
&lt;/bin>
";
    }
    echo "</pre>";
}

function show_as_table() {
    global $quantum;
    global $hist;

    echo "<table width=600 border=0 cellborder=0 cellpadding=0>";
    $keys = array_keys($hist);
    $start = reset($keys);
    $end = end($keys);

    $max = $hist[$start];
    foreach ($hist as $v) {
        if ($v > $max) $max = $v;
    }

    for ($i=$start; $i<=$end; $i++) {
        if (array_key_exists($i, $hist)) {
            $w = 600*$hist[$i]/$max;
        } else {
            $w = 0;
        }
        $f = $i*$quantum;
        echo "<tr><td><font size=-2>";
        printf("%e ", $f);
        echo "</font></td><td><img vspace=0 src=http://boinc.berkeley.edu/colors/000000.gif height=10 width=$w></td></tr>\n";
    }
    echo "</table>";
}

function show_apps() {
    echo "<p>Apps:";
    $r = mysql_query("select * from app");
    while ($p = mysql_fetch_object($r)) {
        echo "<br> $p->id $p->user_friendly_name\n";
    }
}

function show_platforms() {
    echo "<p>Platforms:
        <br>0 All
        <br>1 Windows only
        <br>2 Darwin only
        <br>3 Linux only
    ";

}

function analyze($appid, $platformid, $nresults) {
    global $hist;

    $clause = "";
    switch ($platformid) {
    case 0: $clause = ""; break;
    case 1: $clause = " and locate('Windows', os_name)"; break;
    case 2: $clause = " and locate('Darwin', os_name)"; break;
    case 3: $clause = " and locate('Linux', os_name)"; break;
    }

    $query = "select server_state, outcome, cpu_time, p_fpops from result, host where server_state=5 and appid=$appid and host.id = result.hostid $clause limit $nresults";
    $r = mysql_query($query);

    $n = 0;
    while ($result = mysql_fetch_object($r)) {
        switch ($result->outcome) {
        case 1:     // success
            handle_result($result);
            $n++;
            break;
        case 2:     // couldn't send
        case 3:     // client error
        case 4:     // no reply
        case 5:     // didn't need
        case 6:     // validate error
        case 7:     // client detached
        }
    }

    if (!$n) {
        echo "No done results for that app";
        exit;
    }

    ksort($hist);
    show_stats($hist);
    echo "<hr>\n";
    show_as_table();
    echo "<hr>\n";
    show_as_xml();
}

function show_form() {
    echo "
        <form method=get action=job_times.php>
        App ID: <input name=appid>
        <br>platform ID (0 for all): <input name=platformid value=0>
        <br>FLOP quantum: <input name=quantum value=1e12>
        <br># of results: <input name=nresults value=1000>
        <br><input type=submit name=submit value=OK>
        </form>
    ";
    show_platforms();
    show_apps();
}

if ($_GET['submit']=='OK') {
    set_time_limit(0);
    $appid = $_GET['appid'];
    if (!$appid) {
        echo "Must supply an appid";
        exit;
    }
    $platformid = $_GET['platformid'];
    $quantum = $_GET['quantum'];
    $nresults = $_GET['nresults'];
    analyze($appid, $platformid, $nresults);
} else {
    show_form();
}

?>
