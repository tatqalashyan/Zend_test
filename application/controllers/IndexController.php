<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
       $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction()
    {
        $cache = $this->rateCacheRun();
        $history = $this->db->query('SELECT * FROM history  ORDER BY id DESC LIMIT 5')->fetchAll();
        $this->view->history = $history;
        if($cache->load('EUR')){
            $rates['rates'] = $cache->load('EUR');
            $this->view->curr = $rates['rates'];
            return;
        }
        $ch = curl_init('http://api.fixer.io/latest');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $rates = json_decode($json, true);
        $cache->save($rates['rates'], 'EUR', array('tagA', 'tagB', 'tagC'));
        $this->view->curr = $rates['rates'];
    }
    public function ajaxAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $cache = $this->rateCacheRun();
                if($cache->load($data['currency_in'])){
                    $rates['rates'] = $cache->load($data['currency_in']);

                }else{
                    $ch = curl_init('http://api.fixer.io/latest?base='.$data['currency_in']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $json = curl_exec($ch);
                    curl_close($ch);
                    $rates = json_decode($json, true);
                    $cache->save($rates['rates'], $data['currency_in'], array('tagA', 'tagB', 'tagC'));
                    $rates['rates'];
                }
                if($data['currency_out'] == $data['currency_in']){
                    $data['result'] = $data['amount'];
                }else{
                    $data['result'] = $data['amount']*$rates['rates'][$data['currency_out']];
                }

                $this->db->insert('history',$data);
                echo json_encode($data);exit;


            }
        }
        else {
            echo 'Not Ajax';

        }

    }
    private function rateCacheRun(){
        $frontendOptions = array(
            'lifetime' =>  3600 , // cache lifetime of 1 h
            'automatic_serialization' => true
        );

        $backendOptions = array(
            // Directory where to put the cache files
            'cache_dir' => APPLICATION_PATH .'/tmp'
        );


        $cache = Zend_Cache::factory('Core',
            'File',
            $frontendOptions,
            $backendOptions);

        return $cache;
    }


}

