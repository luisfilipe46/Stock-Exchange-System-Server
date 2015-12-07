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
        $this->loadModel('Devices');
        $someStocks = $this->Stocks->find()->distinct(['tick_name'])->toArray();
        for ($i = 0; $i < sizeof($someStocks); $i++) {
            $response = $http->get('http://download.finance.yahoo.com/d/quotes?f=sl1d1t1v&s='.$someStocks[$i]['tick_name']);
            $tick_name = explode(",",$response->body())[0];
            $tick_names_and_values[] = [str_replace("\"", "", $tick_name), explode(",",$response->body())[1]];
            //$tick_names[] = str_replace("\"", "", $tick_name);
            //$actualValue[] = explode(",",$response->body())[1];
        }

        //$stuff = implode(",",$tick_names);
        //$stuff = implode(",",$tick_names_and_values);
        for ($i=0; $i < sizeof($tick_names_and_values); $i++) {
            $stocksAffectedMax = $this->Stocks->find()->where(['maximum <=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
            $stocksAffectedMin = $this->Stocks->find()->where(['minimum >=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
        }

        Debugger::dump($stocksAffectedMax);

        for ($i=0; $i < sizeof($stocksAffectedMax); $i++) {
            $id = $stocksAffectedMax[$i]['device_id'];
            Debugger::dump($id);
            $stuff[] = $this->Devices->get($id, [
                'contain' => []
            ]);
            //$stuff[] = $this->Devices->find()->where(['id =' => $stocksAffectedMax[$i]['device_id']]);
        }
        for ($i=0; $i < sizeof($stocksAffectedMin); $i++) {
            $id = $stocksAffectedMin[$i]['device_id'];
            Debugger::dump($id);
            $stuff[] = $this->Devices->get($id, [
                'contain' => []
            ]);
        }

        $stuff = implode(",", $stuff);
        $now = Time::now();
        $this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);



    }
}
