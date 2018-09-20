<?php

namespace dadinugroho\ajaxcrud;

use yii\base\Widget;

class BulkButtonWidget extends Widget {

    public $buttons;

    public function init() {
        parent::init();
    }

    public function run() {
        $content = '<div class="pull-left">' .
                '<span class="glyphicon glyphicon-arrow-right"></span>&nbsp;&nbsp;' .
                $this->buttons .
                '</div>';
        return $content;
    }

}

?>
