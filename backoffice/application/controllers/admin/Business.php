<?php

require_once 'Inf_Controller.php';

class Business extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('ewallet_model');
    }

    //--- New Design ---//

    function index()
    {
        $this->lang->load('ewallet', $this->LANG_NAME);

        $this->lang->load('category', $this->LANG_NAME);
        
        $this->set('title', $this->COMPANY_NAME . ' | ' . lang('business'));
        
        $this->load_langauge_scripts();
        
        $this->lang->load('category', $this->LANG_NAME);

        $this->set('business_categories', $this->ewallet_model->getBusinessCategories());
        
        $this->set('details', $this->ewallet_model->getBusinessOverview('', ''));
        $this->set('total', $this->ewallet_model->getBusinessOverviewTotal());

        $this->setView('newui/admin/business/index');
    }

    function summary()
    {
        $this->lang->load('category', $this->LANG_NAME);

        $from_date = $this->input->get('from_date', true);
        $to_date = $this->input->get('to_date', true);
        $details = $this->ewallet_model->getBusinessOverview($from_date, $to_date);

        $income = $bonus = $paid = $pending = [];
        foreach ($details as $key => $summary) {
            if ($summary['type'] == 'income') {
                $income[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
            if ($summary['type'] == 'bonus') {
                $bonus[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
            if ($summary['type'] == 'paid') {
                $paid[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
            if ($summary['type'] == 'pending') {
                $pending[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
        }

        echo json_encode([
            'income' => $income,
            'bonus' => $bonus,
            'paid' => $paid,
            'pending' => $pending,
        ]);
        exit();
    }

    public function transactions()
    {
        $this->lang->load('category', $this->LANG_NAME);
        
        $order_columns = [
            0 => 'full_name',
            2 => 'amount',
            3 => 'date_added',
        ];
        $order = $this->input->get('order', true)[0]['column'] ?? 3;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[3],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $user_name = $this->input->get('user_name', true);
        $category = $this->input->get('category', true);
        $type = $this->input->get('type', true);
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);

        $user_id = $this->validation_model->usernameToIdList($user_name);
        
        $count = $this->ewallet_model->getBusinessTransactionsCount($user_id, $type, $category, $start_date, $end_date);
        
        $transactions = $this->ewallet_model->getBusinessTransactions($user_id, $type, $category, $start_date, $end_date, $filter);

        $data = [];
        foreach($transactions as $tr) {
            $profile_image = profile_image_path($tr['user_photo']);
            $data[] = [
                'full_name' => $tr['full_name'],
                'user_name' => $tr['user_name'],
                'profile_image' => $profile_image,
                'amount_type' => lang($tr['amount_type']),
                'type' => $tr['type'],
                'amount' => format_currency($tr['amount']),
                'date_added' => date("F j, Y, g:i a",strtotime($tr['date_added'])),
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get("draw")),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
        exit();
    }

    //--- New Design ---//

}
