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

require_once("../inc/util.inc");
require_once("../inc/uotd.inc");
require_once("../inc/profile.inc");

db_init();

$profile = get_current_uotd();
if (!$profile) {
    echo "No user of the day has been chosen.";
} else {
    $d = date("d F Y", time());
    $user = lookup_user_id($profile->userid);
    page_head("for $d: $user->name");
    //start_table();
    //show_profile($user, get_logged_in_user(false));
    //show_uotd($profile)
    //end_table();
    $profile = get_current_uotd();
    if ($profile) {
/*        echo "
            <td id=\"uotd\">
            <h2>".tra("User of the day")."</h2>
        ";*/
        show_uotd($profile);
        echo "</td></tr>\n";
    }

}

//page_tail();
?>