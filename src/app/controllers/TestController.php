<?php

use Phalcon\Mvc\Controller;

/**
 * IndexController class
 * 
 * @package Spotify Main
 *  * 
 */
class TestController extends Controller
{
    public function indexAction()
    {

      

        $collection = $this->mongo->test->users;

        $insertOneResult = $collection->insertOne([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());

        // var_dump($insertOneResult->getInsertedId());
    }
}
