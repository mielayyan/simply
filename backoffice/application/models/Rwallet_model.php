<?php

class rwallet_model extends inf_model {

    public function updateBalanceAmount($user_id, $amount) {
        $this->db->set('reserve_wallet', 'ROUND(reserve_wallet +' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_balance_amount');
    }

    public function deductBalanceAmount($user_id, $amount) {
        $this->db->set('reserve_wallet', 'ROUND(reserve_wallet -' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_balance_amount');
    }

    public function insertInToRwallet($user_id, $total_amount, $amount_type, $from_user = '') {

        $skip_blocked_users_commission = $this->configuration_model->getConfiguration('skip_blocked_users_commission');
        $is_user_active = $this->validation_model->isUserActive($user_id);

        if (!$is_user_active && $skip_blocked_users_commission == 'yes') {
            return true;
        }

        if ($total_amount) {
            $this->validation_model->addRwalletHistory($user_id, $from_user, 'commission', $total_amount, $amount_type, 'credit');
            $this->updateBalanceAmount($user_id, $total_amount);
        }
        return true;
    }

    public function deductFromRwallet($user_id, $total_amount, $amount_type, $from_user = '') {
        $this->load->model('calculation_model');
        if ($total_amount) {
            $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);

            $tds_amount = ($total_amount * $config_details['tds']) / 100;
            $service_charge = ($total_amount * $config_details['service_charge']) / 100;
            $amount_payable = $total_amount - ($tds_amount + $service_charge);

            $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, date('Y-m-d H:i:s'), 0, $amount_type, $user_id);
            $this->validation_model->addRwalletHistory($user_id, $from_user, 'commission', $total_amount, $amount_type, 'debit');
            $this->deductBalanceAmount($user_id, $total_amount);
        }
        return true;
    }
}