<?php
use \yii\helpers\Url;
?>
<form class="form-horizontal" role="form" action="<?= Url::to(['/parser/start']) ?>" method="post">
    <div class="form-group">
        <label for="url" class="col-sm-2 control-label">Url</label>
        <div class="col-sm-10">
            <input name="url" type="text" class="form-control" id="url" placeholder="Url">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Парсить</button>
        </div>
    </div>
</form>