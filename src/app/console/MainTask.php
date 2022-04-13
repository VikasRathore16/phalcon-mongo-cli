<?php

namespace App\Console;

use Phalcon\Cli\Task;


class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action asddas' . PHP_EOL;
    }
    public function regenerateAction(int $count = 0)
    {
        echo 'This is the retenerate action ' . $count . PHP_EOL;
    }
    public function insertAction($username = '', $email = '', $name = '')
    {
        $collection = $this->mongo->test->users;

        $insertOneResult = $collection->insertOne([
            'username' => $username,
            'email' => $email,
            'name' => $name,
        ]);

        printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    }
    public function deleteAction($email)
    {
        $collection = $this->mongo->test->users;
        $deleteResult = $collection->deleteOne(['email' => $email]);
        printf("Deleted %d document(s)\n", $deleteResult->getDeletedCount());
    }
}
// php cli.php main delete vikas@cedcoss.com 