<?php


function lang($phrase)  {
    static $lang=array(
        // NAV BAR LINKS
        'ADMIN-HOME' => "Admin",
        "CATEGORIES"=>"Categories",
        "EDIT-PROFILE"=>"Edit Profile",
        "SETTING"=>"Settings",
        "LOG-OUT"=>"Log Out",
        "ITEMS"=>"Items",
        "MEMBERS"=>"Members",
        "COMMENTS"=>"Comments",
        "STATISTICS"=>"Statistics",
        "LOGS"=>"Logs",
    
    );
    return $lang[$phrase];
}




?>