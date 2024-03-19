<?php

return [
    'LIFE_INSURANCE_PAYMENT_MODE' => [
        ['id' => 'Single Premium', 'name' => 'Single Premium', 'multiply_by' => 1],
        ['id' => 'Yearly', 'name' => 'Yearly', 'multiply_by' => 1],
        ['id' => 'Half Yearly', 'name' => 'Half Yearly', 'multiply_by' => 2],
        ['id' => 'Quarterly', 'name' => 'Quarterly', 'multiply_by' => 4],
        ['id' => 'Monthly', 'name' => 'Monthly', 'multiply_by' => 12],
    ],
    'REPORTS' => [
        'insurance_detail' => 'Insurance Policy Details',
        'due_policy_detail' => 'Due Policy Details'
    ],
    'INSURANCE_DETAIL' => [
        [
            'table_column_name' => 'id',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'Yes',
            'selected_column' => 'Yes',
            'display_name' => 'ID'
        ],
        [
            'table_column_name' => 'customer_id',
            'relation_model' => 'Customer',
            'relation_model_column' => 'name',
            'default_visible' => 'Yes',
            'selected_column' => 'Yes',
            'display_name' => 'Customer'
        ],
        [
            'table_column_name' => 'branch_id',
            'relation_model' => 'Branch',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Branch'
        ],
        [
            'table_column_name' => 'broker_id',
            'relation_model' => 'Broker',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Broker'
        ],
        [
            'table_column_name' => 'relationship_manager_id',
            'relation_model' => 'RelationshipManager',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Relationship Manager'
        ],
        [
            'table_column_name' => 'premium_type_id',
            'relation_model' => 'PremiumType',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Premium Type'
        ],
        [
            'table_column_name' => 'policy_type_id',
            'relation_model' => 'PolicyType',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Policy Type Name'
        ],
        [
            'table_column_name' => 'insurance_company_id',
            'relation_model' => 'InsuranceCompany',
            'relation_model_column' => 'name',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Insurance Company Name'
        ],
        [
            'table_column_name' => 'fuel_type_id',
            'relation_model' => 'FuelType',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Fuel Type'
        ],
        [
            'table_column_name' => 'reference_by',
            'relation_model' => 'ReferenceUser',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Reference By'
        ],
        [
            'table_column_name' => 'actual_earnings',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'My Earning'
        ],
        [
            'table_column_name' => 'approx_maturity_amount',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Maturity Amount'
        ],
        [
            'table_column_name' => 'gst',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'GST'
        ],
        [
            'table_column_name' => 'sgst1',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'SGST 1'
        ],
        [
            'table_column_name' => 'cgst1',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'CGST 1'
        ],
        [
            'table_column_name' => 'sgst2',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'SGST 2'
        ],
        [
            'table_column_name' => 'cgst2',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'CGST2'
        ],
        [
            'table_column_name' => 'commission_on',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Commission Count On'
        ],
        [
            'table_column_name' => 'cheque_no',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Cheque Number'
        ],

        [
            'table_column_name' => 'expired_date',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Expired Date'
        ],
        [
            'table_column_name' => 'final_premium_with_gst',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Final Premium With GST'
        ],
        [
            'table_column_name' => 'gross_vehicle_weight',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Gross Vehicle Weight'
        ],
        [
            'table_column_name' => 'insurance_status',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Insurance Status'
        ],
        [
            'table_column_name' => 'issue_date',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Issued On'
        ],
        [
            'table_column_name' => 'make_model',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Make & Model'
        ],
        [
            'table_column_name' => 'mfg_year',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'MFG. Year'
        ],
        [
            'table_column_name' => 'mode_of_payment',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Mode of Payment'
        ],
        [
            'table_column_name' => 'my_commission_amount',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'My Commission Amount'
        ],
        [
            'table_column_name' => 'my_commission_percentage',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'My Commission Percentage'
        ],
        [
            'table_column_name' => 'ncb_percentage',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'NCB Percentage'
        ],
        [
            'table_column_name' => 'net_premium',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Net Premium'
        ],
        [
            'table_column_name' => 'od_premium',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'OD Premium'
        ],
        [
            'table_column_name' => 'pension_amount_yearly',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Pension Amount Yearly'
        ],
        [
            'table_column_name' => 'plan_name',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Pane Name'
        ],
        [
            'table_column_name' => 'policy_no',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Policy Number'
        ],
        [
            'table_column_name' => 'policy_term',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Policy Term'
        ],
        [
            'table_column_name' => 'premium_amount',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Premium Amount'
        ],
        [
            'table_column_name' => 'premium_paying_term',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Premium Paying Term'
        ],
        [
            'table_column_name' => 'reference_commission_amount',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Reference Commission Amount'
        ],
        [
            'table_column_name' => 'reference_commission_percentage',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Reference Commission Percentage'
        ],
        [
            'table_column_name' => 'registration_no',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Registration No'
        ],
        [
            'table_column_name' => 'remarks',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Remarks'
        ],
        [
            'table_column_name' => 'rto',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'RTO'
        ],
        [
            'table_column_name' => 'start_date',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Start Date'
        ],
        [
            'table_column_name' => 'sum_insured',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Sum Insured'
        ],
        [
            'table_column_name' => 'tp_expiry_date',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'TP EXpiry Date'
        ],
        [
            'table_column_name' => 'tp_premium',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'TP Premium'
        ],
        [
            'table_column_name' => 'transfer_commission_amount',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Transfer Commission Amount'
        ],
        [
            'table_column_name' => 'transfer_commission_percentage',
            'relation_model' => '',
            'relation_model_column' => '',
            'default_visible' => 'No',
            'selected_column' => '',
            'display_name' => 'Transfer Commission Percentage'
        ],
    ]
];
