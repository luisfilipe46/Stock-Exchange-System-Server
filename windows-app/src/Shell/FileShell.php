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
        $someStocks = $this->Stocks->find()->distinct(['tick_name'])->toArray();
        for ($i = 0; $i < sizeof($someStocks); $i++) {
            $response = $http->get('http://download.finance.yahoo.com/d/quotes?f=sl1d1t1v&s='.$someStocks[$i]['tick_name']);
            $tick_name = explode(",",$response->body())[0];
            $tick_names[] = str_replace("\"", "", $tick_name);
            $actualValue[] = explode(",",$response->body())[1];
        }

        $stuff = implode(",",$tick_names);;
        $now = Time::now();
        $this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);



    }
}
