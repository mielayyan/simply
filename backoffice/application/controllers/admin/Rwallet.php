<?php

require_once 'Inf_Controller.php';

class Rwallet extends Inf_Controller
{

    function __construct() {
        parent::__construct();
    }

    function business_wallet()
    {
        $title = lang('business_wallet');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);
        $help_link = 'my-e-wallet';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('business_wallet');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('business_wallet');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $details = $this->ewallet_model->getEwalletSummary($from_date, $to_date);
        $total = $this->ewallet_model->getEwalletSummaryTotal();
        /*foreach($details as $key=>$row){
            dump($key);
            // dump($row);

        }
        dd('here');*/

        $this->set('details', $details);
        $this->set('total', $total);

        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('daterange', $daterange);

        $this->lang->load('amount_type', $this->LANG_NAME);
        // dump($this->lang);
        $this->setView();
    }

    public function all_transactions()
    {
        $title = lang('all_transactions');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);

        $help_link = 'all-transactions';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('all_transactions');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('all_transactions');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $daterange = $this->input->get('daterange') ?: 'all';
        $cat_type = $this->input->get('cat_type') ?: 'all';
        $category = $this->input->get('category') ?: 'all';
        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!empty($user_name) && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'all_transactions', false);
        }
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        $total = $this->ewallet_model->getEwalletSummaryTotal();
        $categories = $this->ewallet_model->getEnabledEwalletCategories();
        $this->set('total', $total);
        $this->set('categories', $categories);
        $this->set('category', $category);
        $this->set('user_name', $user_name);
        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('daterange', $daterange);
        $this->set('cat_type', $cat_type);
        
        $page = $this->input->get('offset') ?: 0;
        $count = $this->ewallet_model->getAllEwalletTransactionCount($user_id, $cat_type, $category, $from_date, $to_date);
        $all_transaction = $this->ewallet_model->getAllEwalletTransaction($user_id, $cat_type, $category, $from_date, $to_date, $page, $this->PAGINATION_PER_PAGE);
        $this->pagination->set_all('admin/all_transactions', $count);
        
        $this->set('page_id', $page);
        $this->set('all_transaction', $all_transaction);

        $this->lang->load('amount_type', $this->LANG_NAME);

        $this->setView();
    }

    function business_summary()
    {
        $title = lang('business_summary');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);
        $help_link = 'business-summary';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('business_summary');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('business_summary');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $details = $this->ewallet_model->getBusinessWalletDetails($from_date, $to_date);
        $total = $this->ewallet_model->getBusinessWalletTotal();

        $this->set('details', $details);
        $this->set('total', $total);

        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('daterange', $daterange);

        $this->lang->load('amount_type', $this->LANG_NAME);
        $this->setView();
    }

    public function business_transactions()
    {
        $title = lang('business_transactions');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);

        $help_link = 'business-transactions';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('business_transactions');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('business_transactions');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $daterange = $this->input->get('daterange') ?: 'all';
        $cat_type = $this->input->get('cat_type') ?: 'all';
        $category = $this->input->get('category') ?: 'all';
        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!empty($user_name) && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'business_transactions', false);
        }
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        $total = $this->ewallet_model->getBusinessWalletTotal();
        $categories = $this->ewallet_model->getEnabledBusinessCategories();
        $this->set('total', $total);
        $this->set('categories', $categories);
        $this->set('category', $category);
        $this->set('user_name', $user_name);
        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('daterange', $daterange);
        $this->set('cat_type', $cat_type);
        
        $page = $this->input->get('offset') ?: 0;
        $count = $this->ewallet_model->getAllTransactionCount($user_id, $cat_type, $category, $from_date, $to_date);
        $this->pagination->set_all('admin/business_transactions', $count);
        $all_transaction = $this->ewallet_model->getAllTransaction($user_id, $cat_type, $category, $from_date, $to_date, $page, $this->PAGINATION_PER_PAGE);
        
        $this->set('page_id', $page);
        $this->set('all_transaction', $all_transaction);

        $this->lang->load('amount_type', $this->LANG_NAME);

        $this->setView();
    }

    public function balance_report()
    {
        $title = lang('ewallet_balance_report');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);
        $help_link = 'business-summary';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('ewallet_balance_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('ewallet_balance_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!empty($user_name) && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'balance_report', false);
        }
        
        $page = $this->input->get('offset') ?: 0;
        $count = $this->ewallet_model->getEwalletBalanceReportCount($user_id);
        $this->pagination->set_all('admin/balance_report', $count);
        $reportData = $this->ewallet_model->getEwalletBalanceReport($user_id, $page, $this->PAGINATION_PER_PAGE);
        $grand_total_ewallet_balance=$this->ewallet_model->getTotalEwalletBalanceOfAllUser();

        $this->set('user_name', $user_name);
        $this->set('page_id', $page);
        $this->set('grand_total_ewallet_balance',$grand_total_ewallet_balance);
        $this->set('report_data', $reportData);
        
        $this->setView();
    }
    public function validate_member()
    {
        $this->form_validation->set_rules('keyword', lang('keyword'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    //--- New Design ---//

    /**
     * [index description]
     * @return [type] [view]
     */
    function index() {
        $this->set('title', $this->COMPANY_NAME . ' | ' . lang('ewallet'));
        $this->load_langauge_scripts();
        $this->lang->load('category', $this->LANG_NAME);
        
        $tab = $this->input->get('tab') ?: 'tab_summary';
        $active_user_name = $this->validation_model->isUsernameExists($this->input->get('user_name')) ? $this->input->get('user_name') : $this->LOG_USER_NAME;
        
        $this->set('ewallet_categories', $this->ewallet_model->getEwalletCategories());
        $this->set('user_earnigs_categories', $this->ewallet_model->getEnabledBonusCategories());
        $this->set('details', $this->ewallet_model->getEwalletOverview('', ''));
        $this->set('total', $this->ewallet_model->getEwalletOverviewTotal());
        $this->set('purchase_wallet_balance', $this->ewallet_model->purchase_wallet_balance());
        $this->set('commission_earned', $this->ewallet_model->total_commission_earned());
        $this->set('active_tab', $tab);
        $this->set('active_user_name', $active_user_name);

        $this->setView('newui/admin/ewallet/index');
    }
    
    function summary_total()
    {
        $total = $this->ewallet_model->getEwalletOverviewTotal();
        $total['credit_formated'] = thousands_currency_format($total['credit']);
        $total['debit_formated'] = thousands_currency_format($total['debit']);
        $total['balance_formated'] = thousands_currency_format($total['credit'] - $total['debit']);
        echo json_encode($total);
        exit();   
    }

    function summary()
    {
        $this->lang->load('category', $this->LANG_NAME);

        $from_date = $this->input->get('from_date', true);
        $to_date = $this->input->get('to_date', true);
        $details = $this->ewallet_model->getEwalletOverview($from_date, $to_date);

        $debited = $credited = [];
        foreach ($details as $key => $summary) {
            if ($summary['type'] == 'credit') {
                $credited[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
            if ($summary['type'] == 'debit') {
                $debited[] = [
                    'type' => lang($key),
                    'amount' => format_currency($summary['amount'])
                ];
            }
        }

        echo json_encode([
            'credited' => $credited,
            'debited'  => $debited
        ]);
        exit();
    }

    public function transactions()
    {
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
        
        $count = $this->ewallet_model->getEwalletTransactionsCount($user_id, $type, $category, $start_date, $end_date);
        
        $transactions = $this->ewallet_model->getEwalletTransactions($user_id, $type, $category, $start_date, $end_date, $filter);

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

    public function balance()
    {
        $order_columns = [
            0 => 'full_name',
            1 => 'amount',
        ];
        $order = $this->input->get('order', true)[0]['column'] ?? 1;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[1],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $user_name = $this->input->get('user_name', true);

        $user_id = $this->validation_model->usernameToIdList($user_name);
        
        $count = $this->ewallet_model->getEwalletBalanceCount($user_id);
        
        $balances = $this->ewallet_model->getEwalletBalance($user_id, $filter);

        $data = [];
        foreach($balances as $tr) {
            $profile_image = profile_image_path($tr['user_photo']);
            $data[] = [
                'full_name' => $tr['full_name'],
                'user_name' => $tr['user_name'],
                'profile_image' => $profile_image,
                'amount' => format_currency($tr['amount']),
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

    /**
     * User Earnigs table
     * @return [json] data
     */
    public function user_earnigs() {
        $this->lang->load('income_details', $this->LANG_NAME);
        $order_columns = [
            0 => '',
            1 => 'l.amount_payable',
            2 => 'l.date_of_submission',
        ];
        $order = $this->input->get('order', true)[0]['column'] ?? 0;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[1],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $user_name = $this->input->get('user_name', true) ?: $this->LOG_USER_NAME;
        $user_id = $this->validation_model->usernameToId($user_name);
        $category = $this->input->get('category', true) ?: 'all';
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);
       
        $count = $this->ewallet_model->getUserEarnigsCount($user_id, $category, $start_date, $end_date);
        $user_earnigs = $this->ewallet_model->getUserEarnigs($user_id, $category, $start_date, $end_date, $filter);

        $data = [];
        foreach($user_earnigs as $item) {
            if($item['category'] == 'board_commission' && $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status'] == 'yes') {
                $item['category'] = "table_commission";
            }

            if($item['category'] == 'level_commission' || $item['category'] == 'repurchase_level_commission' || $item['category'] =='upgrade_level_commission' || $item['category'] =='xup_commission' || $item['category'] =='xup_repurchase_level_commission' || $item['category'] =='xup_upgrade_level_commission' || $item['category'] =='matching_bonus' || $item['category'] =='matching_bonus_purchase' || $item['category'] == 'matching_bonus_upgrade' || $item['category'] =='sales_commission') {

                $item['category'] = lang($item['category']) . ' '. lang('received_from') . ' ' . $item['user_name'] . ' ' .lang('from_level') . ' '. $item['user_level'];
            } elseif($item['category'] == "referral") {
                $item['category'] = lang($item['category']). ' ' .lang('received_from') . ' ' . $item['user_name'];
            }

            $data[] = [
                'category'         => lang($item['category']),
                'amount'           => format_currency($item['amount']),
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['transaction_date']))
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

    /**
     * Purhase Wallet
     * @return [json] data
     */
    public function purchase_wallet() {
        $order_columns = [
            0 => 'e.amount_type',
            1 => 'e.purchase_wallet',
            2 => '',
            3 => 'e.date'
        ];
        $order = $this->input->get('order', true)[0]['column'] ?? 0;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[1],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $user_name = $this->input->get('user_name', true) ?: $this->LOG_USER_NAME;
        $user_id = $this->validation_model->usernameToId($user_name);

        $count = $this->ewallet_model->getPurchasewalletHistoryCount($user_id);
        $purchase_wallet_history = $this->ewallet_model->purchase_wallet_history($user_id, $filter);
        $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
        ];

        $debit = $credit = 0;
        $data = [];
        $balance = $this->ewallet_model->getPreviousPurchasewalletBalance($user_id, $filter['start']);

        foreach($purchase_wallet_history as $item) {
            $description = "";
            if ($item['amount_type'] == "donation") {
                if ($item['type'] == "debit") {
                    $description = lang('donation_debit'). ' ' . $item['from_user'];
                } else {
                    $description = lang('donation_credit') . ' '. $item['from_user'];
                }
            } elseif ($item['amount_type'] == 'board_commission' && $MODULE_STATUS['table_status'] == 'yes') {
                $description = lang('table_commission');
            } elseif ($item['amount_type'] == "repurchase") {
                $description = lang('deducted_for_repurchase_by') . ' ' . $item['from_user'];
            } elseif ($item['amount_type'] == "purchase_donation") {
                $description = lang('purchase_donation') . ' ' . lang('from') . ' ' . $item['from_user'];
            } elseif (in_array($item['amount_type'], $from_user_amount_types)) {
                $description = lang($item['amount_type']) . ' ' . lang('from') . ' ' . $item['from_user'];
            } else {
                $description = lang($item['amount_type']);
            }
            
            if($item['type'] == 'debit') {
                $balance = $balance - $item['amount'];
                $debit = $debit + $item['amount'];
            }
            if ($item['type'] == 'credit') {
                $balance = $balance + $item['amount'];
                $credit = $credit + $item['amount'];
            }

            $data[] = [
                'description'      => $description,
                'amount'           => format_currency($item['amount'], 2),
                'balance'          => format_currency($balance, 2),
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'type'             => $item['type']
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

     /**
     * Ewallet Statement
     * @return [json] data
     */
    public function ewallet_statement() {
        $order_columns = [
            0 => 'e.amount_type',
            1 => 'e.purchase_wallet',
            2 => '',
            3 => 'e.date'
        ];
        $order = $this->input->get('order', true)[0]['column'] ?? 0;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[1],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $user_name = $this->input->get('user_name', true) ?: $this->LOG_USER_NAME;
        $user_id = $this->validation_model->usernameToId($user_name);

        $count = $this->ewallet_model->getEwalletHistoryCount($user_id);
        $ewallet_statement = $this->ewallet_model->ewallet_history($user_id, $filter);

        $debit = $credit = 0;
        $data = [];
        $balance = $this->ewallet_model->getPreviousEwalletBalance($user_id, $filter['start']);
        $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
        ];
        
        foreach($ewallet_statement as $item) {
            $description = "";
            if ($item['type'] == 'debit' && $item['amount_type'] != 'payout_release') {
                $balance = $balance - $item['amount'] - $item['transaction_fee'];
                $debit = $debit + $item['amount'];
            }
            if ($item['type'] == 'credit') {
                $balance = $balance + $item['amount'] - $item['purchase_wallet'];
                $credit = $credit + $item['amount'] - $item['purchase_wallet'];
            }

            if ($item['ewallet_type'] == "fund_transfer") {
                if ($item['amount_type'] == "user_credit") {
                    $description = lang('transfer_from') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "user_debit") {
                    $description = lang('fund_transfer_to') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "admin_credit") {
                    $description = lang('credited_by') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "admin_debit") {
                    $description = lang('deducted_by') . ' ' . $item['from_user'];
                }
            } elseif ($item['ewallet_type'] == "commission") {
                if ($item['amount_type'] == "donation") {
                    if ($item['type'] == "debit") {
                        $description = lang('donation_debit') . ' ' . $item['from_user'];
                    } else {
                        $description = lang('donation_credit') . ' ' . $item['from_user'];
                    }
                } elseif ($item['amount_type'] == 'board_commission' && $MODULE_STATUS['table_status'] == 'yes') {
                    $description = lang('table_commission');
                } else {
                    if (in_array($item['amount_type'], $from_user_amount_types)) {
                        $description = lang($item['amount_type']) . ' ' . lang('from') . ' ' . $item['from_user'];
                    } else {
                        $description = lang($item['amount_type']);
                    }
                }
            } elseif ($item['ewallet_type'] == "ewallet_payment") {
                if ($item['amount_type'] == "registration") {
                    $description = lang('deducted_for_registration_of') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "repurchase") {
                    $description = lang('deducted_for_repurchase_by') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "package_validity") {
                    $description = lang('deducted_for_membership_renewal_of') . ' '.  $item['from_user'];
                } elseif ($item['amount_type'] == "upgrade") {
                    $description = lang('deducted_for_upgrade_of') .' '. $item['from_user'];
                }
            } elseif ($item['ewallet_type'] == "payout") {
                if ($item['amount_type'] == "payout_request") {
                    $description = lang('deducted_for_payout_request');
                } elseif ($item['amount_type'] == "payout_release") {
                    $description = lang('payout_released_for_request');
                } elseif ($item['amount_type'] == "payout_delete") {
                    $description = lang('credited_for_payout_request_delete');
                } elseif ($item['amount_type'] == "payout_release_manual") {
                    $description = lang('payout_released_by_manual');
                } elseif ($item['amount_type'] == "withdrawal_cancel") {
                    $description = lang('credited_for_waiting_withdrawal_cancel');
                }
            } elseif ($item['ewallet_type'] == "pin_purchase") {
                if ($item['amount_type'] == "pin_purchase") {
                    $description = lang('deducted_for_pin_purchase');
                } elseif ($item['amount_type'] == "pin_purchase_refund") {
                    $description = lang('credited_for_pin_purchase_refund');
                } elseif ($item['amount_type'] == "pin_purchase_delete") {
                    $description = lang('credited_for_pin_purchase_delete');
                }
            } elseif ($item['ewallet_type'] == "package_purchase") {
                if ($item['amount_type'] == "purchase_donation") {
                    $description = lang('purchase_donation') . ' ' . lang('from') . ' ' . $item['from_user'];
                }
            }

            if ($item['pending_id']) {
                $description .= '<span>' .lang('pending') . '</span>';
            }
            if (in_array($item['ewallet_type'], array('fund_transfer', 'payout')) && $item['transaction_fee'] > 0 && $item['type'] == 'debit') {
                $description .= '('.lang('transaction_fee').')';
            }

            $data[] = [
                'description'      => $description,
                'amount'           => format_currency($item['amount']-$item['purchase_wallet'] + $item['transaction_fee']),
                'balance'          => format_currency($balance),
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'type'             => $item['type']
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

    public function user_balance()
    {
        $user_name = $this->input->get('user_name', true);
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id) {
            $balance = $this->ewallet_model->getBalanceAmount($user_id);
            
            echo json_encode([
                'status' => true,
                'data' => convert_currency($balance)
            ]);
            exit();
        }

        echo json_encode([
            'status' => false
        ]);
        exit();
    }
    
    public function fund_transfer_fee()
    {
        echo convert_currency($this->ewallet_model->getTransactionFee());
        exit();
    }

    function fund_transfer_post()
    {
        if ($this->input->post()) {
            $validated = $this->fund_transfer_validation();
            if ($validated['status']) {
                $transfer_post_array = $this->input->post(null, true);
                $trans_fee = $this->ewallet_model->getTransactionFee();
                $tran_pswd = $transfer_post_array['pswd'];
                $from_user = $transfer_post_array['user_name'];
                $from_user_id = $this->ewallet_model->userNameToID($from_user);
                $to_user_name = $transfer_post_array['to_user_name'];
                $to_user_id = $this->ewallet_model->userNameToID($to_user_name);
                $trans_amount = $transfer_post_array['amount'];
                $trans_amount = round($trans_amount / $this->DEFAULT_CURRENCY_VALUE, 8);
                $transaction_concept = $this->validation_model->textAreaLineBreaker($transfer_post_array['transaction_note']);
                $total_req_amount = $trans_amount + $trans_fee;
                $pass = $this->ewallet_model->getUserPassword($from_user_id);
                $balamount = $this->ewallet_model->getBalanceAmount($from_user_id);
                if ($total_req_amount <= $balamount) {
                    if (password_verify($tran_pswd, $pass)) {
                        $this->ewallet_model->begin();
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $up_date1 = $this->ewallet_model->updateBalanceAmountDetailsFrom($from_user_id, round($total_req_amount, 8));
                        $up_date2 = $this->ewallet_model->updateBalanceAmountDetailsTo($to_user_id, round($trans_amount, 8));
                        $this->ewallet_model->insertBalAmountDetails($from_user_id, $to_user_id, round($trans_amount, 8), '', $transaction_concept, $trans_fee, $transaction_id);
                        if ($up_date1 && $up_date2) {
                            $this->ewallet_model->commit();
                            $data = serialize($transfer_post_array);
                            $this->validation_model->insertUserActivity($from_user_id, 'fund transferred', $this->LOG_USER_ID, $data);

                            // Employee Activity History
                            if ($this->LOG_USER_TYPE == 'employee') {
                                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'transfer_fund', 'Fund Transferred');
                            }
                            echo json_encode([
                                'status' => true,
                                'message' => lang('fund_transfered_successfully')
                            ]);
                            exit();
                        } else {
                            $this->ewallet_model->rollback();
                            echo json_encode([
                                'status' => false,
                                'message' => lang('error_on_fund_transfer')
                            ]);
                            exit();
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'message' => lang('invalid_transaction_password')
                        ]);
                        exit();
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => lang('low_balance_please_try_again')
                    ]);
                    exit();
                }
            } else {
                echo json_encode($validated);
                exit();
            }
        }
    }

    protected function fund_transfer_validation()
    {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('user_name', 'lang:user_name', 'trim|required|max_length[50]|user_exists',[
               "required"    => lang('required'),
                "max_length" => sprintf(lang('maxlength'), lang('user_name'), "50"),
               "user_exists" => lang('username_not_available'),
            ]
        );
        
        $this->form_validation->set_rules('to_user_name', 'lang:user_name', 'trim|required|max_length[50]|user_exists|differs[user_name]', [
                "required"    => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('user_name'), "50"),
                "user_exists" => lang('username_not_available'),
                'differs'     => lang('username_not_to_be_same_as_to_username'),
            ]
        );

        $this->form_validation->set_rules('amount', 'lang:amount', 'trim|required|numeric|greater_than_equal_to[0]|max_length[10]|callback_balance_check',[
                "required"              => lang('required'),
                "numeric"               => lang('digits'),
                "greater_than_equal_to" => lang('greater_zero'),
                "max_length"            => sprintf(lang("max_digits"), lang('amount'), "10"),
                "balance_check"         => lang('insufficient_balance')
            ]
        );
        
        $this->form_validation->set_rules('transaction_note', 'lang:transaction_note', 'trim|required|max_length[1000]',[
                "required" => lang('required'),
                "max_length" => sprintf(lang("maxlength"), lang("transaction_note"), "1000")
            ]
        );

        $this->form_validation->set_rules('pswd', lang('transaction_password'), 'trim|required|max_length[100]|callback_check_transaction_password', [
                'required' => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('transaction_password'), "100"),
                'check_transaction_password' => lang('invalid_transaction_password')
            ]
        );

        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ];
        }
        return [
            'status' => true
        ];
    }

    function fund_credit_post()
    {
        if ($this->input->post()) {
            $validated = $this->fund_credit_debit_validation();
            if ($validated['status']) {
                $post_arr = $this->input->post(null, true);
                $userid = $this->LOG_USER_ID;
                $to_user = $post_arr['user_name'];
                $transaction_concept = $this->validation_model->textAreaLineBreaker($post_arr['tran_concept']);
                $to_userid = $this->ewallet_model->userNameToID($to_user);
                $amount = $post_arr['amount'] * (1 / $this->DEFAULT_CURRENCY_VALUE);
                $user_exists = $this->ewallet_model->isUserNameAvailable($to_user);
                if ($user_exists) {
                    if (is_numeric($amount) && $amount > 0) {
                        $this->ewallet_model->begin();
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $up_date = $this->ewallet_model->addUserBalanceAmount($to_userid, round($amount, 8));
                        $this->ewallet_model->insertBalAmountDetails($userid, $to_userid, round($amount, 8), 'admin_credit', $transaction_concept, '0', $transaction_id);
                        if ($up_date) {
                            $this->ewallet_model->commit();
                            $data = serialize($post_arr);
                            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Fund added to ' . $to_user . '`s e-wallet ' . $post_arr['amount'], $this->LOG_USER_ID, $data);

                            // Employee Activity History
                            if ($this->LOG_USER_TYPE == 'employee') {
                                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'add_fund', 'Fund added to e-wallet');
                            }
                            //

                            echo json_encode([
                                'status' => true,
                                'message' => lang('fund_credited_successfully'),
                            ]);
                            exit();
                        } else {
                            $this->ewallet_model->rollback();
                            echo json_encode([
                                'status' => false,
                                'message' => lang('error_on_crediting_fund'),
                            ]);
                            exit();
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'message' => lang('error_on_crediting_fund_please_check_the_amount'),
                        ]);
                        exit();
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => lang('invalid_user_name'),
                    ]);
                    exit();
                }
            } else {
                echo json_encode($validated);
                exit();
            }
        }
    }

    protected function fund_credit_debit_validation()
    {
        $this->lang->load('validation', $this->LANG_NAME);
        
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|max_length[50]|callback_valid_user',[
            "required"   => sprintf(lang('required'), lang('user_name')),
            "max_length" => sprintf(lang('maxlength'), lang('user_name'), "50"),
            "valid_user" => lang('invalid_username'),
        ]);

        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|numeric|max_length[10]|greater_than_equal_to[0]',[
            "required" => sprintf(lang('required'), lang('amount')),
            "numeric"  => lang('digits'),
            'max_length' => sprintf(lang('maxlength_digits'), lang('amount'), "10"),
            'greater_than_equal_to' => sprintf(lang('greater_than'), 0)
        ]);

        $this->form_validation->set_rules('tran_concept', lang('transaction_note'), 'trim|required|max_length[1000]',[
            "required" => sprintf(lang('required'), lang('transaction_note')),
            'max_length' => sprintf(lang('maxlength'), lang('amount'), "1000")
        ]);
        
        $this->form_validation->set_message('max_length', lang('maximum_five_digit'));
        
        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ];
        }
        return [
            'status' => true
        ];
    }

    function fund_debit_post()
    {
        if ($this->input->post()) {
            $validated = $this->fund_credit_debit_validation();
            if ($validated['status']) {
                $post_arr = $this->input->post(null, true);
                $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
                $userid = $this->LOG_USER_ID;
                $to_user = $post_arr['user_name'];
                $transaction_concept = $this->validation_model->textAreaLineBreaker($post_arr['tran_concept']);
                $to_userid = $this->ewallet_model->userNameToID($to_user);
                $amount = $post_arr['amount'] * (1 / $this->DEFAULT_CURRENCY_VALUE);
                $user_exists = $this->ewallet_model->isUserNameAvailable($to_user);
                if ($user_exists) {
                    $bal_amount = $this->ewallet_model->getBalanceAmount($to_userid);
                    if (is_numeric($amount) && $amount > 0 && $bal_amount >= $amount) {
                        $this->ewallet_model->begin();
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $up_date = $this->ewallet_model->deductUserBalanceAmount($to_userid, round($amount, 8));
                        $this->ewallet_model->insertBalAmountDetails($userid, $to_userid, round($amount, 8), 'admin_debit', $transaction_concept, ' ', $transaction_id);

                        if ($up_date) {
                            $this->ewallet_model->commit();
                            $data = serialize($post_arr);
                            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Fund deducted from ' . $to_user . '`s  E-Wallet ' . $amount, $this->LOG_USER_ID, $data);

                            // Employee Activity History
                            if ($this->LOG_USER_TYPE == 'employee') {
                                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'deduct_fund', 'Fund deducted from E-Wallet');
                            }
                            //

                            echo json_encode([
                                'status' => true,
                                'message' => lang('fund_deducted_successfully')
                            ]);
                            exit();
                        } else {
                            $this->ewallet_model->rollback();
                            echo json_encode([
                                'status' => false,
                                'message' => lang('error_on_deducting_fund')
                            ]);
                            exit();
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'message' => lang('error_on_deducting_fund_please_check_the_amount')
                        ]);
                        exit();
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => lang('invalid_user_name')
                    ]);
                    exit();
                }
            } else {
                echo json_encode($validated);
                exit();
            }
        }
    }

    //--- New Design ---//

}
