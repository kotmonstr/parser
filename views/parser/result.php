<?php
foreach($modelBlog as $blog){
    echo '<h3>'.$blog->title.'</h3><br />';
    echo isset($blog->image) ? '<img src="'.$blog->image.'" alt="" width="500px"><br />' : '<br />';
    echo $blog->content.'<br />';
    echo '<hr>';
 }
?>