<?php

namespace app\models;

use Yii;
use yii\web\Controller;

class CoreController extends Controller
{
    public function init()
    {
        $this->enableCsrfValidation = false;
    }


}
