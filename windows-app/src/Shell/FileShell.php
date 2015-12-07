<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Error\Debugger;
use Cake\I18n\Time;
use Cake\Network\Http\Client;

class FileShell extends Shell
{
    public function main()
    {
        $http = new Client();
        $this->loadModel('Stocks');
        $tick_names = $this->Stocks->find()->distinct(['tick_name'])->toArray();
        for ($i = 0; $i < sizeof($tick_names); $i++) {
            $response = $http->get('http://finance.yahoo.com/d/quotes?f=sl1d1t1v&s='.$tick_names[$i]['tick_name']);
            Debugger::dump($response);
            $responses[] = $response.'\n';
        }

        $stuff = implode(",",$responses);;
        $now = Time::now();
        $this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);



    }
}
