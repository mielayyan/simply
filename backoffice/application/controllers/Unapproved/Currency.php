<?php

require_once 'Inf_Controller.php';

class currency extends Inf_Controller {

    public function change_currency($id = '') {
        $user_id = $this->LOG_USER_ID;
        echo $this->currency_model->updateUserCurrencyForUnapproved($id, $user_id);
    }

}