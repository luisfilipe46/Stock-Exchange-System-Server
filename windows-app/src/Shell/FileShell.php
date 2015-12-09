<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Error\Debugger;
//use Cake\I18n\Time;
use Cake\Network\Http\Client;
use Cake\Filesystem\File;
use App\WindowsNotification\WindowsNotificationClass;
//use App\WindowsNotification;

require_once(APP . DS . 'WindowsNotification' . DS . 'WindowsNotification.php');


class FileShell extends Shell
{
    public function authentication() {

        //init the WindowsNotification Class
        $Notifier = new WindowsNotificationClass();
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
            Debugger::dump($Auth);
            //$this->createFile('/home/demo/token/token.txt', 'token not generated - '.' - '.$Auth->response_status);
        }
    }
    public function main()
    {
        $this->authentication();

        $http = new Client();
        $this->loadModel('Stocks');
        $this->loadModel('Devices');


        $token_file = new File("/home/demo/token/token.txt");
        $token = $token_file->read();
        $token_file->close();

        $MyAuthObject = new OAuthObject(array("token_type"=>"Bearer", "access_token" => $token));
        $OptionsToast = new WNSNotificationOptions();
        $OptionsToast->SetAuthorization($MyAuthObject);
        $OptionsToast->SetX_WNS_REQUESTFORSTATUS(X_WNS_RequestForStatus::Request);

        $NotifierToast = new WindowsNotificationClass($OptionsToast);

        $OptionsTile = new WNSNotificationOptions();
        $OptionsTile->SetAuthorization($MyAuthObject);
        $OptionsTile->SetX_WNS_REQUESTFORSTATUS(X_WNS_RequestForStatus::Request);
        //NOTE: Set the Tile type
        $OptionsTile->SetX_WNS_TYPE(X_WNS_Type::Tile);
        $NotifierTile = new WindowsNotificationClass($OptionsTile);



        $allStocks = $this->Stocks->find('all')->toArray();
        $someStocks = $this->Stocks->find()->distinct(['tick_name'])->toArray();
        for ($i = 0; $i < sizeof($someStocks); $i++) {
            $response = $http->get('http://download.finance.yahoo.com/d/quotes?f=sl1d1t1v&s=' . $someStocks[$i]['tick_name']);
            $tick_name = explode(",", $response->body())[0];
            $tick_names_and_values[] = [str_replace("\"", "", $tick_name), explode(",", $response->body())[1]];
        }

        $this->sendAllStocksNotificationsInTileNotifications($NotifierToast, $tick_names_and_values, $allStocks);
        $this->checkMinMaxValuesAndSendToastNotifications($NotifierTile, $tick_names_and_values);

        //$stuff = implode(",", $stuff);
        //$now = Time::now();
        //$this->createFile('/home/demo/files_created_each_minute/'.$now->i18nFormat('yyyy-MM-dd HH:mm:ss').'.txt', $stuff);
    }

    /**
     * @param $http
     * @param $Notifier
     */
    private function sendAllStocksNotificationsInTileNotifications($Notifier, $tick_names_and_values, $allStocks)
    {

        for ($i = 0; $i < sizeof($allStocks); $i++) {
            $id = $allStocks[$i]['device_id'];
            $tick_name = $allStocks[$i]['tick_name'];
            $value = 0.0;
            for ($a=0; $a < sizeof($tick_names_and_values); $a++) {
                if ($tick_names_and_values[$a][0] == $tick_name)
                    $value = $tick_names_and_values[$a][1];
            }
            $device = $this->Devices->get($id, [
                'contain' => []
            ]);
            $channelURI = $device['name'];
            $MyTileXML = '<tile>
  <visual>
    <binding template="TileSquareText01">
      <text id="1">\'.$tick_name.\' (larger text)</text>
      <text id="2">\'.$value.\'</text>
    </binding>
  </visual>
</tile>

<tile>
  <visual version="2">
    <binding template="TileSquare150x150Text01" fallback="TileSquareText01">
      <text id="1">\'.$tick_name.\' (larger text)</text>
      <text id="2">\'.$value.\'</text>
    </binding>
  </visual>
</tile>';
            $Notifier->Send($channelURI,$MyTileXML);
        }

    }

    /**
     * @param $http
     * @param $Notifier
     */
    private function checkMinMaxValuesAndSendToastNotifications($Notifier, $tick_names_and_values)
    {

        for ($i = 0; $i < sizeof($tick_names_and_values); $i++) {
            $stocksAffectedMax = $this->Stocks->find()->where(['maximum <=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
            $stocksAffectedMin = $this->Stocks->find()->where(['minimum >=' => $tick_names_and_values[$i][1], 'tick_name =' => $tick_names_and_values[$i][0]])->toArray();
        }

        for ($i = 0; $i < sizeof($stocksAffectedMax); $i++) {
            $id = $stocksAffectedMax[$i]['device_id'];
            $device = $this->Devices->get($id, [
                'contain' => []
            ]);
            $channelURI = $device['name'];

            //$Notifier->Send($channelURI,TemplateToast::ToastText02($stocksAffectedMax[$i]['tick_name']." atingiu máximo!","Valor ".TOCOMPLET,TemplateToast::NotificationMail));
            $Notifier->Send($channelURI, TemplateToast::ToastText01($stocksAffectedMax[$i]['tick_name'] . " atingiu máximo!"));
        }

        for ($i = 0; $i < sizeof($stocksAffectedMin); $i++) {
            $id = $stocksAffectedMin[$i]['device_id'];
            $device = $this->Devices->get($id, [
                'contain' => []
            ]);
            $channelURI = $device['name'];
            $Notifier->Send($channelURI, TemplateToast::ToastText01($stocksAffectedMin[$i]['tick_name'] . " atingiu mínimo!"));
        }
    }
}
