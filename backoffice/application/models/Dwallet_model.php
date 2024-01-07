<?php

class dwallet_model extends inf_model {

    public function updateBalanceAmount($user_id, $amount) {
        $this->db->set('distribution_wallet', 'ROUND(distribution_wallet +' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_balance_amount');
    }

    public function deductBalanceAmount($user_id, $amount) {
        $this->db->set('distribution_wallet', 'ROUND(distribution_wallet -' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->update('user_balance_amount');
    }

    public function calculateDistribution($user_id, $total_amount, $from_date, $to_date, $binary_config, $from_user = '') {
        $this->load->model('product_model');

        $steps = [];

        $downlines = $this->getTreeDownlines($user_id, 'sponsor');
        foreach($downlines as $downline_user_id) {
            $total_paired = $this->getBinaryPairInPeriod($downline_user_id, $from_date, $to_date);

            if ($total_paired >= 15) {
                $steps[15][] = $downline_user_id;
            } elseif ($total_paired >= 10) {
                $steps[10][] = $downline_user_id;
            } elseif ($total_paired >= 5) {
                $steps[5][] = $downline_user_id;
            }
        }

        foreach ($steps as $steps_achieved => $users) {
            $step_distribution = $binary_config['step_distribution_' . $steps_achieved];
            foreach ($users as $user) {
                $membership_package = $this->validation_model->getProductId($user);
                $pair_price = $this->product_model->getPackagePairPrice($membership_package, $this->MODULE_STATUS, 'registration');

                if ($total_amount > 0) {
                    $amount = $pair_price * $step_distribution;
                    $this->insertInToDwallet($user, $amount, 'binary_distribution', $user_id);

                    $total_amount -= $amount;
                }
            }
        }

        return true;
    }

    public function insertInToDwallet($user_id, $total_amount, $amount_type, $from_user = '') {
        $this->load->model('calculation_model');
        
        $skip_blocked_users_commission = $this->configuration_model->getConfiguration('skip_blocked_users_commission');
        $is_user_active = $this->validation_model->isUserActive($user_id);

        if (!$is_user_active && $skip_blocked_users_commission == 'yes') {
            return true;
        }

        if ($total_amount) {
            $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);

            $tds_amount = ($total_amount * $config_details['tds']) / 100;
            $service_charge = ($total_amount * $config_details['service_charge']) / 100;
            $amount_payable = $total_amount - ($tds_amount + $service_charge);

            $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, date('Y-m-d H:i:s'), 0, $amount_type, $user_id);
            $this->validation_model->addDwalletHistory($user_id, $from_user, 'commission', $total_amount, $amount_type, 'credit');
            $this->updateBalanceAmount($user_id, $total_amount);

            $this->validation_model->addDwalletHistory($from_user, '', 'commission', $total_amount, 'distribution_debit', 'debit');
            $this->deductBalanceAmount($user_id, $total_amount);
        }
        return true;
    }

    /**
     * Get downlines by new tree structure
     * 02-07-2021
     * Renshif
     */
    public function getTreeDownlines($user_id) {
        $downlines = [];
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('sponsor_id', $user_id);
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            $downlines[] = $row['id'];
        }
        
        return $downlines;
    }

    /**
     * Get total binary pairing in time period
     * 02-07-2021
     * Renshif
     */
    public function getBinaryPairInPeriod($user_id, $from_date, $to_date) {
        $this->db->select_sum('total_leg');
        $this->db->where('amount_type', 'leg');
        $this->db->where('user_id', $user_id);
        $this->db->where('date_of_submission >=', $from_date);
        $this->db->where('date_of_submission <=', $to_date);
        return $this->db->get('leg_amount')->row()->total_leg;
    }
}