<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\I18n\Time;

class FileShell extends Shell
{
    public function main()
    {
	$stuff = "stuf";
	$now = Time::now();
        //$this->createFile('/home/demo/files_created_each_minute/'.$now->year.'-'.$now->month.'-'.$now->day.' '.$now->hour.':'.$now->minute.':'.$now->second.'.txt', $stuff);
        $this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);



    }
}
