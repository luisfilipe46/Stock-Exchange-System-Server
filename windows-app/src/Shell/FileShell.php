<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Error\Debugger;
use Cake\I18n\Time;
use Cake\Network\Http\Client;
//use App\WindowsNotification;

require_once(APP . DS . 'WindowsNotification' . DS . 'WindowsNotification.php');


class FileShell extends Shell
{
    public function authentication() {
        Debugger::dump('starting authentication');

        $token = isset($_GET["token"]) ? $_GET["token"] : null;
        $token = 'aishdasd4564';
        //If token request
        if($token !== null)
        {     //init the WindowsNotification Class
            $Notifier = new WindowsNotification\WindowsNotificationClass();
            $Auth = $Notifier->AuthenticateService();
            if($Auth->response_status == 200)
            {
                Debugger::dump('creating token file GOOD');
                $this->createFile('/home/demo/token/token.txt', $Auth->access_token);
                //Save the token on permanent support (db, file, etc.)
            }
            else
            {
                Debugger::dump('creating token file BAD');
                $this->createFile('/home/demo/token/token.txt', 'token not generated\n'.$token);
                //do stuff for errors
            }
        }
    }
    public function main()
    {
        $this->authentication();

        $http = new Client();
        $this->loadModel('Stocks');
        $this->loadModel('Devices');
        $someStocks = $this->Stocks->find()->distinct(['tick_name'])->toArray();
        for ($i = 0; $i < sizeof($someStocks); $i++) {
            $response = $http->get('http://download.finance.yahoo.com/d/quotes?f=sl1d1t1v&s='.$someStocks[$i]['tick_name']);
            $tick_name = explode(",",$response->body())[0];
            $tick_names_and_values[] = [str_replace("\"", "", $tick_name), explode(",",$response->body())[1]];
        }

        for ($i=0; $i < sizeof($tick_names_and_values); $i++) {
            $stocksAffectedMax = $this->Stocks->find()->where(['maximum <=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
            $stocksAffectedMin = $this->Stocks->find()->where(['minimum >=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
        }

        Debugger::dump($stocksAffectedMax);

        for ($i=0; $i < sizeof($stocksAffectedMax); $i++) {
            $id = $stocksAffectedMax[$i]['device_id'];
            $stuff[] = $this->Devices->get($id, [
                'contain' => []
            ]);
            //SEND MSG
        }
        for ($i=0; $i < sizeof($stocksAffectedMin); $i++) {
            $id = $stocksAffectedMin[$i]['device_id'];
            Debugger::dump($id);
            $stuff[] = $this->Devices->get($id, [
                'contain' => []
            ]);
            //SEND MSG
        }

        $stuff = implode(",", $stuff);
        $now = Time::now();
        $this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);



    }
}
