<?php

namespace Controllers;

use Core\Controller;
use Core\View;
use Models\WelcomeModel;

class Welcome extends Controller
{
    public function test()
    {
        $welcomeObj = new WelcomeModel();
        print_r($welcomeObj->getAllData(array('id>'=>1), 'id'));
        View::render('test', array('title'=>'Test Title'));
    }

    public function testAdd()
    {
        $welcomeObj = new WelcomeModel();
        var_dump($welcomeObj->addData(array('num'=>100, 'pid'=>101, 'testnull'=>'asdf')));
    }
}