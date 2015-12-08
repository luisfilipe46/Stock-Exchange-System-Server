<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Error\Debugger;
use Cake\ORM\TableRegistry;


/**
 * Stocks Controller
 *
 * @property \App\Model\Table\StocksTable $Stocks
 */
class StocksController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Devices']
        ];
        $this->set('stocks', $this->paginate($this->Stocks));
        $this->set('_serialize', ['stocks']);
    }

    /**
     * View method
     *
     * @param string|null $id Stock id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $stock = $this->Stocks->get($id, [
            'contain' => ['Devices']
        ]);
        $this->set('stock', $stock);
        $this->set('_serialize', ['stock']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $channelURI = $this->request->data('channelURI');
            $wp_id = $this->request->data('windowsPhoneID');
            $this->loadModel('Devices');
            $device = $this->Devices->find()->where(['wp_id =' => $wp_id])->toArray();
            $devicesTable = TableRegistry::get('Devices');

            if (empty($device))
            {
                $device = $devicesTable->newEntity();
                $device->name = $channelURI;
                $device->wp_id = $wp_id;

                if ($devicesTable->save($device)) {
                    // The $device entity contains the id now
                    $device_id = $device->id;
                }
            }
            else {
                $device_id = $device[0]['id'];
                $device = $devicesTable->get($device_id);
                $device->name = $channelURI;
                $device->save($device);
            }

            $data['minimum'] = $this->request->data('min');
            $data['maximum'] = $this->request->data('max');
            $data['tick_name'] = $this->request->data('tick_name');
            $data['device_id'] = $device_id;
            $stock = $this->Stocks->newEntity();
            $stock = $this->Stocks->patchEntity($stock, $data);
            if ($this->Stocks->save($stock)) {
                //$this->Flash->success(__('The stock has been saved.'));
                //return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The stock could not be saved. Please, try again.'));
            }

            /*
            $devices = $this->Stocks->Devices->find('list', ['limit' => 200]);
            $this->set(compact('stock', 'devices'));
            $this->set('_serialize', ['stock']);
            */
            $this->set(compact(''));
            $this->set('_serialize', ['']);

        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Stock id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $stock = $this->Stocks->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stock = $this->Stocks->patchEntity($stock, $this->request->data);
            if ($this->Stocks->save($stock)) {
                $this->Flash->success(__('The stock has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The stock could not be saved. Please, try again.'));
            }
        }
        $devices = $this->Stocks->Devices->find('list', ['limit' => 200]);
        $this->set(compact('stock', 'devices'));
        $this->set('_serialize', ['stock']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Stock id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stock = $this->Stocks->get($id);
        if ($this->Stocks->delete($stock)) {
            //$this->Flash->success(__('The stock has been deleted.'));
        } else {
            //$this->Flash->error(__('The stock could not be deleted. Please, try again.'));
        }
        //return $this->redirect(['action' => 'index']);

        $this->set(compact(''));
        $this->set('_serialize', ['']);
    }
}
