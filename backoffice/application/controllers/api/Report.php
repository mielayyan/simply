<?php

require 'Inf_Controller.php';
class Report extends Inf_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("report_model");
        $this->load->model("select_report_model");
        $this->LOG_USER_ID = $this->rest->user_id ?? "";
        $this->LOG_USER_TYPE = "user";
    }

    private function setValidation($custom_rules = [])
    {
        $input_array = $this->validation_model->stripTagsPostArray($this->get());
        $this->form_validation->set_data($input_array);
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        if ($this->get("limit_type") == "custom") {
            $rules[] = [
                'field' => 'from_date',
                'label' => lang("from_date"),
                'rules' => 'trim|required'
            ];
            $rules[] = [
                'field' => 'to_date',
                'label' => lang("to_date"),
                'rules' => 'trim|required'
            ];
        }
        if (count($custom_rules)) {
            array_merge($rules, $custom_rules);
        }
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        return $input_array;
    }

    private function getDateLimits()
    {
        switch ($this->get("limit_type")) {
            case 'custom':
                $from_date = date("Y-m-d", strtotime($this->get("from_date")));
                $to_date = date("Y-m-d", strtotime($this->get("to_date")));
                break;
            case 'today':
                $from_date = date("Y-m-d");
                $to_date = date("Y-m-d");
                break;
            case 'this_month':
                $from_date = date("Y-m-01");
                $to_date = date("Y-m-d");
                break;
            case 'this_year':
                $from_date = date("Y-01-01");
                $to_date = date("Y-m-d");
                break;
            default:
                $from_date = "";
                $to_date = "";
                break;
        }
        return compact("from_date", "to_date");
    }

    private function getDateLimitTypes()
    {
        return [
            ['value' => 'overall', 'text' => lang('overall')],
            ['value' => 'custom', 'text' => lang('custom')],
            ['value' => 'today', 'text' => lang('today')],
            ['value' => 'this_month', 'text' => lang('this_month')],
            ['value' => 'this_year', 'text' => lang('this_year')]
        ];
    }

    public function payout_release_report_get()
    {
        extract($this->setValidation()); // $offset, $limit, ...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        $data = array_values($this->report_model->getReleasedPayoutDetails($from_date, $to_date, $offset, $limit));

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('username'),
                "title_colour" => "#000000",
                "value" => $value['paid_user_id'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('name'),
                "title_colour" => "#000000",
                "value" => $value['full_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('total_amount'),
                "title_colour" => "#000000",
                "value" => format_currency($value['paid_amount']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("payout_fee"),
                "title_colour" => "#000000",
                "value" => format_currency($value['payout_fee']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('date'),
                "title_colour" => "#000000",
                "value" => date($this->DATE_FORMAT, strtotime($value['paid_date'])),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('status'),
                "title_colour" => "#000000",
                "value" => ($value['paid_status'] == "yes") ? lang('paid') : lang('not_paid'),
                "value_colour" => "#000000",
            ];

            $details[] = [
                "recyclerArray" => $recyclerArray
            ];
        }

        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes"));
    }

    function commission_report_get()
    {
        $this->load->model('ewallet_model');
        $commission_types = $this->ewallet_model->getEnabledBonusList();
        $received_commission_types = $this->ewallet_model->getReceivedBonusList();
        $commission_types = array_unique(array_merge($commission_types, $received_commission_types));
        $commissionTypesArray = [];
        foreach ($commission_types as $com) {
            $commissionTypesArray[] = ['value' => $com, 'text' => lang($com)];
        }

        $rules = [
            [
                'field' => 'type',
                'label' => lang("commission_type"),
                'rules' => 'trim|greater_than_equal_to[0]|valid_array_element[' . implode(';', $commission_types) . ']'
            ],
        ];
        $type = "";
        extract($this->setValidation($rules)); // $offset, $limit, type...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        $user_id = $this->LOG_USER_ID;
        $data = $this->report_model->getCommisionDetails($type, $from_date, $to_date, $user_id, $limit, $offset);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('username'),
                "title_colour" => "#000000",
                "value" => $value['user_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('name'),
                "title_colour" => "#000000",
                "value" => $value['full_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('amount_type'),
                "title_colour" => "#000000",
                "value" => lang($value['amount_type']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("date"),
                "title_colour" => "#000000",
                "value" => date($this->DATE_FORMAT, strtotime($value['date'])),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('total_amount'),
                "title_colour" => "#000000",
                "value" => format_currency($value['total_amount']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('tds'),
                "title_colour" => "#000000",
                "value" => format_currency($value['tds']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('service_charge'),
                "title_colour" => "#000000",
                "value" => format_currency($value['service_charge']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('amount_payable'),
                "title_colour" => "#000000",
                "value" => format_currency($value['amount_payable']),
                "value_colour" => "#000000",
            ];

            $details[] = [
                "recyclerArray" => $recyclerArray
            ];
        }
        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes", "commissionTypesArray"));
    }

    function sales_report_get()
    {
        $registrationProducts = $this->Api_model->getAllProducts("registration");
        $repurchaseProducts = $this->Api_model->getAllProducts("repurchase");
        $productTypes = [
            ['value' => "registration", 'text' => lang("registration")],
            ['value' => "repurchase", 'text' => lang("repurchase")],
        ];

        $product_ids = [];
        foreach ($registrationProducts as $arr) {
            $product_ids[] = $arr["product_id"];
        }
        foreach ($repurchaseProducts as $arr) {
            $product_ids[] = $arr["product_id"];
        }

        $rules = [
            [
                'field' => 'productType',
                'label' => lang("product_type"),
                'rules' => 'trim|greater_than_equal_to[0]|valid_array_element["registration";"repurchase"]'
            ],
            [
                'field' => 'productId',
                'label' => lang("product"),
                'rules' => 'trim|greater_than_equal_to[0]|valid_array_element[' . implode(';', $product_ids) . ']'
            ],
        ];
        $productType = "repurchase";
        $productId = "all";
        extract($this->setValidation($rules)); // $offset, $limit, $productType, $productId...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        if ($productType == "repurchase") {
            $data = $this->report_model->productRepurchaseSalesReport($productId, $from_date, $to_date, $limit, $offset);
        } else {
            $data = $this->report_model->SalesReport($from_date, $to_date, $productId, $limit, $offset);
        }

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('invoice_no'),
                "title_colour" => "#000000",
                "value" => $value['invoice_no'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('product_name'),
                "title_colour" => "#000000",
                "value" => $value['prod_id'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('username'),
                "title_colour" => "#000000",
                "value" => lang($value['user_id']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("payment_method"),
                "title_colour" => "#000000",
                "value" => lang($value['payment_method']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('amount'),
                "title_colour" => "#000000",
                "value" => format_currency($value['amount']),
                "value_colour" => "#000000",
            ];
            $details[] = [
                "recyclerArray" => $recyclerArray
            ];
        }
        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes", "registrationProducts", "repurchaseProducts", "productTypes"));
    }

    function rank_performance_report_get()
    {
        $user_id = $this->rest->user_id;

        $user_details = $this->validation_model->getAllUserDetails($user_id);

        $full_name = $user_details['user_detail_name'];

        $this->load->model('rank_model');
        $rank_achievement = $this->rank_model->getRankCriteria($user_id);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $rank_achievement);

        $recyclerArray = [];

        $recyclerArray[] = [
            "title" => lang('username'),
            "title_colour" => "#000000",
            "value" => ($rank_achievement['current_rank']['rank_name']) ? $rank_achievement['current_rank']['rank_name'] : lang('NA'),
            "value_colour" => "#000000",
        ];

        $recyclerArray[] = [
            "title" => lang("current_rank"),
            "title_colour" => "#000000",
            "value" => ($rank_achievement['current_rank']['rank_id']) ? $rank_achievement['current_rank']['rank_name'] : lang('NA'),
            "value_colour" => "#000000",
        ];

        $recyclerArray[] = [
            "title" => lang("next_rank"),
            "title_colour" => "#000000",
            "value" => ($rank_achievement['next_rank']['rank_id']) ? $rank_achievement['next_rank']['rank_name'] : lang('NA'),
            "value_colour" => "#000000",
        ];

        $recyclerArray[] = [
            "title" => lang('current_referral_count'),
            "title_colour" => "#000000",
            "value" => "" . $rank_achievement['current_rank']['referal_count'],
            "value_colour" => "#000000",
        ];

        if ($rank_achievement['next_rank']['rank_id']) {
            $recyclerArray[] = [
                "title" => lang('referral_count_for') . " " . $rank_achievement['next_rank']['rank_name'],
                "title_colour" => "#000000",
                "value" => $rank_achievement['next_rank']['referal_count'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('needed_referral_count'),
                "title_colour" => "#000000",
                "value" => "" . max($rank_achievement['next_rank']['referal_count'] - $rank_achievement['current_rank']['referal_count'], 0),
                "value_colour" => "#000000",
            ];
        }

        if ($rank_achievement['criteria']['personal_pv']) {
            $recyclerArray[] = [
                "title" => lang('current_personal_pv'),
                "title_colour" => "#000000",
                "value" => $rank_achievement['current_rank']['personal_pv'],
                "value_colour" => "#000000",
            ];

            if ($rank_achievement['next_rank']['rank_id']) {
                $recyclerArray[] = [
                    "title" => lang('personal_pv_for') . " " . $rank_achievement['next_rank']['rank_name'],
                    "title_colour" => "#000000",
                    "value" => $rank_achievement['next_rank']['personal_pv'],
                    "value_colour" => "#000000",
                ];
                $recyclerArray[] = [
                    "title" => lang('needed_personal_pv'),
                    "title_colour" => "#000000",
                    "value" => max($rank_achievement['next_rank']['personal_pv'] - $rank_achievement['current_rank']['personal_pv'], 0),
                    "value_colour" => "#000000",
                ];
            }

            if ($rank_achievement['criteria']['group_pv']) {
                $recyclerArray[] = [
                    "title" => lang('current_group_pv'),
                    "title_colour" => "#000000",
                    "value" => $rank_achievement['current_rank']['group_pv'],
                    "value_colour" => "#000000",
                ];

                if ($rank_achievement['next_rank']['rank_id']) {
                    $recyclerArray[] = [
                        "title" => lang('gpv_for') . " " . $rank_achievement['next_rank']['rank_name'],
                        "title_colour" => "#000000",
                        "value" => $rank_achievement['next_rank']['group_pv'],
                        "value_colour" => "#000000",
                    ];
                    $recyclerArray[] = [
                        "title" => lang('needed_group_pv'),
                        "title_colour" => "#000000",
                        "value" => "" . max($rank_achievement['next_rank']['group_pv'] - $rank_achievement['current_rank']['group_pv'], 0),
                        "value_colour" => "#000000",
                    ];
                }
            }
        }

        if ($rank_achievement['criteria']['downline_count']) {
            $recyclerArray[] = [
                "title" => lang('current_downline_count'),
                "title_colour" => "#000000",
                "value" => $rank_achievement['current_rank']['downline_count'],
                "value_colour" => "#000000",
            ];
            if ($rank_achievement['next_rank']['rank_id']) {
                $recyclerArray[] = [
                    "title" => lang('downline_count_for') . " " . $rank_achievement['next_rank']['rank_name'],
                    "title_colour" => "#000000",
                    "value" => $rank_achievement['next_rank']['downline_count'],
                    "value_colour" => "#000000",
                ];
                $recyclerArray[] = [
                    "title" => lang('needed_downline_count'),
                    "title_colour" => "#000000",
                    "value" => max($rank_achievement['next_rank']['downline_count'] - $rank_achievement['current_rank']['downline_count'], 0),
                    "value_colour" => "#000000",
                ];
            }
        }

        if ($rank_achievement['criteria']['downline_package_count'] && $rank_achievement['current_rank']['package_name']) {
            foreach ($rank_achievement['current_rank']['package_name'] as $k => $v) {
                $recyclerArray[] = [
                    "title" => lang('current_downline_count'),
                    "title_colour" => "#000000",
                    "value" => $rank_achievement['current_rank']['downline_package_count'][$k],
                    "value_colour" => "#000000",
                ];
                if ($rank_achievement['next_rank']['rank_id']) {
                    $recyclerArray[] = [
                        "title" => lang('downline_count_for') . " " . $rank_achievement['next_rank']['rank_name'],
                        "title_colour" => "#000000",
                        "value" => $rank_achievement['next_rank']['downline_package_count'][$k],
                        "value_colour" => "#000000",
                    ];
                    $recyclerArray[] = [
                        "title" => lang('needed_downline_count'),
                        "title_colour" => "#000000",
                        "value" => max($rank_achievement['next_rank']['downline_package_count'][$k] - $rank_achievement['current_rank']['downline_package_count'][$k], 0),
                        "value_colour" => "#000000",
                    ];
                }
            }
        }
        $this->set_success_response(200, compact("rank_achievement", "recyclerArray"));
    }

    function epin_transfer_report_get()
    {
        $rules = [
            [
                'field' => 'toUserName',
                'label' => lang("username"),
                'rules' => 'trim|user_exists',
                'errors' => array(
                    'user_exists' => lang('invalid_user_name'),
                ),
            ]
        ];
        $toUserId = "";
        $toUserName = "";
        extract($this->setValidation($rules)); // $offset, $limit...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        if ($toUserName)
            $toUserId = $this->validation_model->userNameToID($toUserName);

        $data = $this->report_model->getEpinTransferDetailsForUser($from_date, $to_date, $this->LOG_USER_ID, $toUserId, $offset, $limit);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('name'),
                "title_colour" => "#000000",
                "value" => $value['user_full_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('epin'),
                "title_colour" => "#000000",
                "value" => $value['epin'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('transfer_date'),
                "title_colour" => "#000000",
                "value" => date($this->TIME_FORMAT, strtotime($value['transfer_date'])),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("send_receive"),
                "title_colour" => "#000000",
                "value" => lang($value['type']),
                "value_colour" => "#000000",
            ];
            $details[] = [
                "recyclerArray" => $recyclerArray
            ];
        }
        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes"));
    }

    function repurchase_report_get()
    {
        extract($this->setValidation()); // $offset, $limit...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        $data = $this->report_model->getRepurchaseDetails($from_date, $to_date, $this->LOG_USER_ID, $limit, $offset);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $totalAmount = 0;
        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('invoice_no'),
                "title_colour" => "#000000",
                "value" => $value['invoice_no'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('username'),
                "title_colour" => "#000000",
                "value" => $value['user_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('full_name'),
                "title_colour" => "#000000",
                "value" => $value['full_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('date_of_submission'),
                "title_colour" => "#000000",
                "value" => date($this->TIME_FORMAT, strtotime($value['order_date'])),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("payment_method"),
                "title_colour" => "#000000",
                "value" => lang($value['payment_method']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("total_amount"),
                "title_colour" => "#000000",
                "value" => format_currency($value['amount']),
                "value_colour" => "#000000",
            ];

            $innerRecycler = [];
            foreach ($value["product_details"] as $innerValue) {
                $innerRecycler[] = [
                    "title" => lang("product_name"),
                    "title_colour" => "#000000",
                    "value" => $innerValue['prod_id'],
                    "value_colour" => "#000000",
                ];
                $innerRecycler[] = [
                    "title" => lang("unit_price"),
                    "title_colour" => "#000000",
                    "value" => format_currency($innerValue['unite_price']),
                    "value_colour" => "#000000",
                ];
                $innerRecycler[] = [
                    "title" => lang("quantity"),
                    "title_colour" => "#000000",
                    "value" => $innerValue['quantity'],
                    "value_colour" => "#000000",
                ];
                $innerRecycler[] = [
                    "title" => lang("total_amount"),
                    "title_colour" => "#000000",
                    "value" => format_currency($innerValue['total']),
                    "value_colour" => "#000000",
                ];
            }

            $totalAmount += $value["amount"];

            $actionButton = ["title" => lang("view_products"), "innerRecycler" => $innerRecycler];

            $details[] = compact("recyclerArray", "actionButton");
        }
        $summeryArray = [
            ["title" => lang('total_amount'), "value" => format_currency($totalAmount)],
        ];
        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes", "summeryArray"));
    }

    function payout_pending_report_get()
    {
        extract($this->setValidation()); // $offset, $limit...get params
        extract($this->getDateLimits()); // $from_date, $to_date

        $data = $this->report_model->getPayoutPendingDetails($from_date, $to_date, $limit, $offset);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('username'),
                "title_colour" => "#000000",
                "value" => $value['paid_user_id'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('name'),
                "title_colour" => "#000000",
                "value" => $value['full_name'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("amount"),
                "title_colour" => "#000000",
                "value" => format_currency($value['paid_amount']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('date'),
                "title_colour" => "#000000",
                "value" => date($this->DATE_FORMAT, strtotime($value['paid_date'])),
                "value_colour" => "#000000",
            ];
            $details[] = compact("recyclerArray");
        }
        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes"));
    }

    function fund_transfers_get()
    {
        extract($this->setValidation()); // $offset, $limit...get params
        extract($this->getDateLimits()); // $from_date, $to_date
        $amount_type = $this->get('type') ?: ''; // user_debit|user_credit

        $data = $this->ewallet_model->getUserEwalletDetails($this->LOG_USER_ID, $from_date, $to_date, $amount_type, $offset, $limit);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);

        $details = [];
        foreach ($data as $value) {
            $recyclerArray = [];
            $recyclerArray[] = [
                "title" => lang('transaction_id'),
                "title_colour" => "#000000",
                "value" => $value['transaction_id'],
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang("amount"),
                "title_colour" => "#000000",
                "value" => format_currency($value['total_amount']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('transaction_fee'),
                "title_colour" => "#000000",
                "value" => format_currency($value['trans_fee']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('transaction_type'),
                "title_colour" => "#000000",
                "value" => lang($value['amount_type']),
                "value_colour" => "#000000",
            ];
            $recyclerArray[] = [
                "title" => lang('date'),
                "title_colour" => "#000000",
                "value" => date($this->TIME_FORMAT, strtotime($value['date'])),
                "value_colour" => "#000000",
            ];

            $details[] = compact("recyclerArray");
        }

        $dateTypes = $this->getDateLimitTypes();
        $this->set_success_response(200, compact("details", "dateTypes"));
    }
}
