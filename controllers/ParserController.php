<?php namespace app\controllers;

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


        # Определение функции, вызываемой при завершении потока
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
            // Здесь необходимо не забывать проверять целостность и валидность возвращаемых данных, о чём писалось выше.
        }

        $AC = new AngryCurl('callback_function', false);
# Включаем принудительный вывод логов без буферизации в окно браузера
        $AC->init_console();

        $AC->post($url);
        $AC->execute(200);
        //AngryCurl::print_debug();
        //return $this->render('index');
    }

    public function actionSimple()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '128M');
        ini_set( 'default_charset', 'UTF-8' );
        //header('Content-Type: text/html; charset=UTF-8');


        $arrResult = [];
        $arrResult2 = [];
        $html = new simple_html_dom();
        $html->load_file('http://politrussia.com/news/');

        // Find all links
        foreach ($html->find('a.overlink') as $element) {
            //echo $element->href. '<br>';
            $arrResult[] = $element->href;
        }
        //vd($arrResult);


        $i = 0;
        foreach ($arrResult as $key => $link) {
            $html->load_file('http://politrussia.com' . $link);
            $i++;
            $content = $html->find('div[class="news_text"]', 0)->plaintext;
            $title = $html->find('h1[itemprop="name"]', 0)->plaintext;

            foreach ($html->find('img[itemprop="contentUrl"]') as $element) {
                $img2 = 'http://politrussia.com' . $element->src;
            }

            $content2 = mb_convert_encoding($content, "UTF-8", "Windows-1251");
            $title2 = mb_convert_encoding($title, "UTF-8", "Windows-1251");

            $arrResult2[$key]['title'] = $title2;
            $arrResult2[$key]['content'] = $content2;
            $arrResult2[$key]['img'] = $img2;
        }

        foreach($arrResult2 as $key => $row){
            $modelBlog = new Blog();
            $modelBlog->title = $row['title'];
            $modelBlog->content = $row['content'];
            $modelBlog->created_at = time();
            $modelBlog->updated_at = time();
            $modelBlog->author = Yii::$app->user->id;
            $modelBlog->image = $row['img'];

            $dublicate = Blog::getDublicateByTitle($row['title']);
            if(!$dublicate){
                $modelBlog->save();
            }


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
