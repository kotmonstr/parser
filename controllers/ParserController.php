<?php

namespace app\controllers;

use app\models\Blog;
use Yii;
use app\models\CoreController;
use yii\web\Controller;
use app\models\simple_html_dom;
use app\models\RollingCurl;
use app\models\AngryCurl;

class ParserController extends CoreController
{


    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStart()
    {
        //http://stupid.su/php-curl_multi/
        $url = Yii::$app->request->post('url');

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '128M');


        # ����������� �������, ���������� ��� ���������� ������
        function callback_function($response, $info, $request)
        {
            if ($info['http_code'] !== 200) {
                AngryCurl::add_debug_msg(
                    "->\t" .
                    $request->options[CURLOPT_PROXY] .
                    "\tFAILED\t" .
                    $info['http_code'] .
                    "\t" .
                    $info['total_time'] .
                    "\t" .
                    $info['url']
                );
                return;
            } else {
                AngryCurl::add_debug_msg(
                    "->\t" .
                    $request->options[CURLOPT_PROXY] .
                    "\tOK\t" .
                    $info['http_code'] .
                    "\t" .
                    $info['total_time'] .
                    "\t" .
                    $info['url']
                );
                return;
            }
            // ����� ���������� �� �������� ��������� ����������� � ���������� ������������ ������, � ��� �������� ����.
        }

        $AC = new AngryCurl('callback_function', false);
# �������� �������������� ����� ����� ��� ����������� � ���� ��������
        $AC->init_console();

        $AC->get($url, 'GET');
        $AC->execute(200);
        //AngryCurl::print_debug();
        //return $this->render('index');
    }

    public function actionSimple()
    {
        $html = new simple_html_dom();
        $html->load_file('http://nnm.me');

        $items = $html->find('div.article');
        //$subtitle = $html->find('subtitle');
        $i = 0;
        foreach ($items as $names){

                $i++;
                $modelBlog = new Blog();
                $modelBlog->title = (string)$i;
                $modelBlog->content = (string)$names;
                $modelBlog->created_at = time();
                $modelBlog->updated_at = time();
                $modelBlog->author = Yii::$app->user->id;
                $modelBlog->image = 'empty';
                $modelBlog->save();

            }
        return $this->render('/site/index');
    }

    public function actionResult()
    {
        $modelBlog = Blog::find()->all();
        return $this->render('/parser/result', ['modelBlog' => $modelBlog]);
    }
    public function actionJade()
    {
        //$layout = 'main.jade';
        return $this->render('/parser/jade.jade');
    }

}
