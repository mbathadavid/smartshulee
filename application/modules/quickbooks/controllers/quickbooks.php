<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quickbooks extends Public_Controller
{

    private $dsn;
    private $batch;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('quickbooks_m');
        $this->load->library('Qbwc');
        $this->load->config('quickbooks');
        $this->load->library('Dates');
        $this->dsn = 'mysqli://' . $this->db->username . ':' . $this->db->password . '@' . $this->db->hostname . '/' . $this->db->database;
        //set constants
        $this->set_constants();
        ini_set('memory_limit', '700M');

        $this->batch = 20;
    }

    /**
     * SOAP endpoint for the Web Connector to connect to
     */
    public function qbwc()
    {
        $user = 'admin';
        $pass = '12345678qq';

        // Memory limit
        //ini_set('memory_limit', $this->config->item('quickbooks','memorylimit'));
        //set correct timezone, or some PHP installations will complain
        if (function_exists('date_default_timezone_set'))
        {
            date_default_timezone_set('Africa/Nairobi');
        }
        // Map QuickBooks actions to handler functions
        $map = [
            QUICKBOOKS_ADD_CUSTOMER => [[$this, '_add_customer_req'], [$this, '_add_customer_resp']],
            QUICKBOOKS_MOD_CUSTOMER => [[$this, '_mod_customer_req'], [$this, '_mod_customer_resp']],
            QUICKBOOKS_QUERY_CUSTOMER => [[$this, '_query_customer_req'], [$this, '_query_customer_resp']],
            QUICKBOOKS_IMPORT_INVOICE => [[$this, '_fetch_invoices_req'], [$this, '_fetch_invoices_resp']],
            QUICKBOOKS_IMPORT_CUSTOMER => [[$this, '_fetch_customer_req'], [$this, '_fetch_customer_resp']],
            QUICKBOOKS_IMPORT_RECEIVEPAYMENT => [[$this, '_fetch_payments_req'], [$this, '_fetch_payments_resp']],
            QUICKBOOKS_ADD_INVOICE => [[$this, '_add_invoice_req'], [$this, '_add_invoice_resp']],
            QUICKBOOKS_MOD_INVOICE => [[$this, '_fetch_payments_req'], [$this, '_fetch_payments_resp']],
            QUICKBOOKS_ADD_RECEIVE_PAYMENT => [[$this, '_add_payment_req'], [$this, '_add_payment_resp']],
            QUICKBOOKS_MOD_RECEIVE_PAYMENT => [[$this, '_edit_payment_req'], [$this, '_edit_payment_resp']],
            QUICKBOOKS_ADD_SALESRECEIPT => [[$this, '_fetch_payments_req'], [$this, '_fetch_payments_resp']],
            QUICKBOOKS_ADD_CREDITMEMO => [[$this, '_add_credit_memo_req'], [$this, '_add_credit_memo_resp']],
            QUICKBOOKS_DEL_TXN => [[$this, '_del_payment_req'], [$this, '_del_payment_resp']]
        ];
        // Catch all errors that QuickBooks throws with this function 
        $errmap = [
            3070 => [$this, '_error_too_long'],
            500 => [$this, '_error_500'],
            1 => [$this, '_error_500'],
            '*' => [$this, '_error_catchall']
        ];

        // An array of callback hooks
        $hooks = [
            QuickBooks_WebConnector_Handlers::HOOK_LOGINSUCCESS => [[$this, '_hook_login_success']] // call on successful login
        ];

        // An array of callback options
        $callback_options = [];
        //Logging level
        //$log_level = $this->config->item('quickbooks', 'loglevel');
        //$log_level = QUICKBOOKS_LOG_NORMAL;
        //$log_level = QUICKBOOKS_LOG_DEBUG;
        //$log_level = QUICKBOOKS_LOG_DEVELOP;
        $log_level = QUICKBOOKS_LOG_DEBUG;
        // What SOAP server you're using
        //$soapserver = QUICKBOOKS_SOAPSERVER_PHP;     // The PHP SOAP extension, see: www.php.net/soap
        $soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;  // A pure-PHP SOAP server (no PHP ext/soap extension required, also makes debugging easier)

        $soap_options = []; // See http://www.php.net/soap
        $handler_options = [
            'authenticate' => [$this, 'custom_auth'],
            'deny_concurrent_logins' => false,
            'deny_reallyfast_logins' => false
        ];
        $driver_options = [
            'max_log_history' => 4000, // Limit the no. of quickbooks_log entries to 2048/16
            'max_queue_history' => 1024, // Limit the no. of *successfully processed* quickbooks_queue entries to 128
        ];
        // Check to make sure our database is set up 
        if (!QuickBooks_Utilities::initialized($this->dsn))
        {
            // Initialize & create the neccessary database schema for queueing up requests and logging
            QuickBooks_Utilities::initialize($this->dsn);
            // This creates a username and password which is used by the Web Connector to authenticate
            QuickBooks_Utilities::createUser($this->dsn, $user, $pass);
        }
        // Set up our queue singleton
        QuickBooks_WebConnector_Queue_Singleton::initialize($this->dsn);

        // Create a new server 
        $Server = new QuickBooks_WebConnector_Server($this->dsn, $map, $errmap, $hooks, $log_level, $soapserver, QUICKBOOKS_WSDL, $soap_options, $handler_options, $driver_options, $callback_options);
        //tell it to handle the requests
        $response = $Server->handle(true, true);
    }

    /**
     * Custom AUTH Function
     * 
     * @param type $username
     * @param type $password
     * @param type $company_file
     * @param type $wait_before_next_update
     * @param type $min_run_every_n_seconds
     * @param type $params
     * @return boolean
     */
    function custom_auth($username, $password, &$company_file, &$wait_before_next_update, &$min_run_every_n_seconds, $params)
    {
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming Login Request # ' . $username . ': ' . print_r($params, true));
        //$company_file = 'C:/path/to/quickbooks/file.QBW';
        $valid = 1;
        if ($valid)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * QBWC Support Page
     * 
     */
    function support()
    {
        
    }

    /**
     * Login success hook - perform an action when a user logs in via the Web Connector
     * 
     * @param type $requestID
     * @param type $user
     * @param type $hook
     * @param type $err
     * @param type $hook_data
     * @param type $callback_config
     */
    function _hook_login_success($requestID, $user, $hook, &$err, $hook_data, $callback_config)
    {
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Make Callback Request #: _hook_login_success ');
        // Fetch the queue instance
        $Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
        $date = date('Y-m-d H:i:s');
        // Set up the qb data imports
        if (!$this->_get_last_run($user, QUICKBOOKS_IMPORT_INVOICE))
        {
            // And write the initial sync time
            $this->_set_last_run($user, QUICKBOOKS_IMPORT_INVOICE, $date);
        }
        if (!$this->_get_last_run($user, QUICKBOOKS_IMPORT_RECEIVEPAYMENT))
        {
            // And write the initial sync time
            $this->_set_last_run($user, QUICKBOOKS_IMPORT_RECEIVEPAYMENT, $date);
        }

        if (!$this->_get_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER))
        {
            $this->_set_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER, $date);
        }
        if (!$this->_get_last_run($user, QUICKBOOKS_ADD_CUSTOMER))
        {
            $this->_set_last_run($user, QUICKBOOKS_ADD_CUSTOMER, $date);
        }
        if (!$this->_get_last_run($user, QUICKBOOKS_ADD_INVOICE))
        {
            $this->_set_last_run($user, QUICKBOOKS_ADD_INVOICE, $date);
        }
        if (!$this->_get_last_run($user, QUICKBOOKS_MOD_CUSTOMER))
        {
            $this->_set_last_run($user, QUICKBOOKS_MOD_CUSTOMER, $date);
        }
        if (!$this->_get_last_run($user, QUICKBOOKS_QUERY_CUSTOMER))
        {
            $this->_set_last_run($user, QUICKBOOKS_QUERY_CUSTOMER, $date);
        }
        //Queue up Requests 
        //HIGHER PRIORITIES EXECUTE FIRST
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Queue up Requests ...: CUSTOMERS, INVOICES & RECEIVEPAYMENT ');
        //$Queue->enqueue(QUICKBOOKS_ADD_CREDITMEMO, 3, QB_PRIORITY_CREDITMEMO);
        //$Queue->enqueue(QUICKBOOKS_MOD_CUSTOMER, 4, QB_PRIORITY_CUSTOMER);
        //$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, 5, QB_PRIORITY_CUSTOMER);
        //$Queue->enqueue(QUICKBOOKS_QUERY_CUSTOMER, 5, QB_PRIORITY_CUSTOMER);
        //$Queue->enqueue(QUICKBOOKS_DEL_TXN, 5, QB_PRIORITY_CUSTOMER);

        $flist = $this->quickbooks_m->populate('fee_extras', 'id', 'qb_name');
        /**
         * 
         * Queue pending Students
         */
        $students = $this->quickbooks_m->get_students_pending($this->batch);
        if (count($students))
        {
            $p = 2000;
            foreach ($students as $s)
            {
                $bal = $this->quickbooks_m->get_arrears($s->id);
                $form = [
                    'first_name' => $s->first_name,
                    'last_name' => $s->middle_name . ' ' . $s->last_name,
                    'email' => $s->email,
                    'phone' => $s->phone,
                    'bal' => empty($bal) ? 0 : number_format($bal, 2),
                    'reg' => $s->admission_number,
                    'company' => ''
                ];

                $Queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, $s->id, $p, $form);
                $p--;
            }
        }
        else
        {
            /**
             * 
             * Queue pending Invoices - tuition
             * /
              $tt_invoices = $this->quickbooks_m->get_tuition_pending($this->batch);
              $ct = 1100; //enforce higher priority
              foreach ($tt_invoices as $t)
              {
              $rw = $this->quickbooks_m->get_st($t->student_id);
              $t->st_list_id = $rw->list_id;
              $t->type = 1;
              $Queue->enqueue(QUICKBOOKS_ADD_INVOICE, $t->id, $ct, (array) $t);
              $ct--;
              }
             */
            /**
             * Queue pending Invoices - extras
             */
            $extras = $this->quickbooks_m->get_extras_pending($this->batch);

            $j = 500;
            foreach ($extras as $xt)
            {
                $rw = $this->quickbooks_m->get_st($xt->student);
                $xt->st_list_id = $rw->list_id;
                $xt->type = 2;
                $xt->desc = isset($flist[$xt->fee_id]) ? $flist[$xt->fee_id] : '';
                $Queue->enqueue(QUICKBOOKS_ADD_INVOICE, $xt->id, $j, (array) $xt);
                $j--;
            }

            /**
             * 
             * Queue pending payments
             * /
              $paid = $this->quickbooks_m->get_payments_pending($this->batch);
              $methods = [
              'Bank Slip' => 'BANK SLIP',
              'Cash' => 'Cash',
              'MPESA' => 'MPESA',
              'Mpesa' => 'MPESA',
              'Cheque' => 'Check',
              'Paybill' => 'MPESA',
              'Ag' => 'Agent Deposit',
              'EFT' => 'EFT'
              ];
              $j = 500;
              foreach ($paid as $pay)
              {
              $rw = $this->quickbooks_m->get_st($pay->reg_no);
              $pay->st_list_id = $rw->list_id;
              $pay->transaction_no = str_replace('Dpst ', '', $pay->transaction_no);

              $pay->method = isset($methods[$pay->payment_method]) ? $methods[$pay->payment_method] : '';
              $Queue->enqueue(QUICKBOOKS_ADD_RECEIVE_PAYMENT, $pay->id, $j, (array) $pay);
              $j--;
              }
             */
            /**
             * 
             * Queue Modify payments
             * /
              $all_paid = $this->quickbooks_m->get_all_payments($this->batch);

              $x = 2700;
              foreach ($all_paid as $fp)
              {
              $rw = $this->quickbooks_m->get_st($fp->reg_no);
              $fp->st_list_id = $rw->list_id;
              //$pay->transaction_no = str_replace('Dpst ', '', $pay->transaction_no);

              //$pay->method = isset($methods[$fp->payment_method]) ? $methods[$fp->payment_method] : '';
              $Queue->enqueue(QUICKBOOKS_MOD_RECEIVEPAYMENT, $fp->id, $x, (array) $fp);
              $x--;
              }
             */
            /**
             * 
             * Queue voided payments
             * /
              $voided = $this->quickbooks_m->get_voided_pay($this->batch);

              $k = 200;
              foreach ($voided as $pv)
              {
              $rw = $this->quickbooks_m->get_st($pv->reg_no);
              $pv->st_list_id = $rw->list_id;
              $pv->type = 1;
              $pv->transaction_no = str_replace('Dpst ', '', $pv->transaction_no);

              $Queue->enqueue(QUICKBOOKS_DEL_TXN, $pv->id, $k, (array) $pv);
              $k--;
              }
             */
            /**
             * 
             * Queue Flagged payments
             */
            $flagged = $this->quickbooks_m->get_flagged_pay($this->batch);

            $b = 100;
            foreach ($flagged as $pf)
            {
                $rw = $this->quickbooks_m->get_st($pf->reg_no);
                $pf->st_list_id = $rw->list_id;
                $pf->type = 2;
                $pf->transaction_no = str_replace('Dpst ', '', $pf->transaction_no);

                $Queue->enqueue(QUICKBOOKS_DEL_TXN, $pf->id, $b, (array) $pf);
                $b--;
            }
            /**
             * 
             * Queue Flagged Invoices - Tuition
             */
            $flagged_inv = $this->quickbooks_m->get_flagged_invoices($this->batch);

            $x = 400;
            foreach ($flagged_inv as $fv)
            {
                $fv->student = $fv->student_id;
                $rw = $this->quickbooks_m->get_st($fv->student);
                $fv->st_list_id = $rw->list_id;
                $fv->item = 'Tuition Fee';
                $fx->cat = 1;

                $Queue->enqueue(QUICKBOOKS_ADD_CREDITMEMO, $fv->id, $x, (array) $fv);
                $x--;
            }
            /**
             * 
             * Queue Flagged Invoices - Extras
             */
            $flagged_ex = $this->quickbooks_m->get_flagged_extras($this->batch);

            $n = 600;
            foreach ($flagged_ex as $fx)
            {
                $rw = $this->quickbooks_m->get_st($fx->student);
                $fx->st_list_id = $rw->list_id;
                $fx->item = isset($flist[$fx->fee_id]) ? $flist[$fx->fee_id] : '';
                $fx->cat = 2;
                
                $Queue->enqueue(QUICKBOOKS_ADD_CREDITMEMO, $fx->id, $n, (array) $fx);
                $n--;
            }
        }
    }

    /**
     * Get the last date/time the QuickBooks sync ran
     * 
     * @param string $user	The web connector username 
     * @return string	A date/time in this format: "yyyy-mm-dd hh:ii:ss"
     */
    function _get_last_run($user, $action)
    {
        $type = null;
        $opts = null;
        return QuickBooks_Utilities::configRead(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $type, $opts);
    }

    /**
     * Set the last date/time the QuickBooks sync ran to NOW
     * 
     * @param string $user
     * @return boolean
     */
    function _set_last_run($user, $action, $force = null)
    {
        $value = date('Y-m-d') . 'T' . date('H:i:s');

        if ($force)
        {
            $value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
        }

        return QuickBooks_Utilities::configWrite(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $value);
    }

    /**
     * _get_current_run
     * 
     * @param type $user
     * @param type $action
     * @return type
     */
    function _get_current_run($user, $action)
    {
        $type = null;
        $opts = null;
        return QuickBooks_Utilities::configRead(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $type, $opts);
    }

    /**
     * _set_current_run
     * 
     * @param type $user
     * @param type $action
     * @param type $force
     * @return type
     */
    function _set_current_run($user, $action, $force = null)
    {
        $value = date('Y-m-d') . 'T' . date('H:i:s');

        if ($force)
        {
            $value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
        }

        return QuickBooks_Utilities::configWrite(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $value);
    }

    /**
     * Build a request to import invoices already in QuickBooks into our application
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $version
     * @param type $locale
     * @return string
     */
    function _fetch_invoices_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $version = '4.0';
        // Iterator support (break the result set into small chunks)
        $attr_iteratorID = '';
        $attr_iterator = ' iterator="Start" ';
        if (empty($extra['iteratorID']))
        {
            // This is the first request in a new batch
            $last = $this->_get_last_run($user, $action);
            $this->_set_last_run($user, $action);   // Update the last run time to NOW()
            // Set the current run to $last
            $this->_set_current_run($user, $action, $last);
        }
        else
        {
            // This is a continuation of a batch
            $attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
            $attr_iterator = ' iterator="Continue" ';
            $last = $this->_get_current_run($user, $action);
        }
        $last = '2016-10-01T00:00:00'; //overide last
        //$to = '2016-06-01T00:00:00'; //overide last
        $to = date('Y-m-d\TH:i:s');
        // Build the request
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                   <?qbxml version="13.0"?>
                           <QBXML>
                           <QBXMLMsgsRq onError="stopOnError">
		       <InvoiceQueryRq requestID="' . $requestID . '">
                                        <ModifiedDateRangeFilter>
                                                <FromModifiedDate>' . $last . '</FromModifiedDate>
                                                <ToModifiedDate>' . $to . '</ToModifiedDate>
                                        </ModifiedDateRangeFilter>
                                <IncludeLineItems>true</IncludeLineItems>
                                <OwnerID>0</OwnerID>
  		                </InvoiceQueryRq>
                        </QBXMLMsgsRq>
                        </QBXML>';

        return $xml;
    }

    /**
     * Handle a response from QuickBooks 
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $xml
     * @param type $idents
     * @return boolean
     */
    function _fetch_invoices_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        if (!empty($idents['iteratorRemainingCount']))
        {
            // Queue up another request
            $Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
            $Queue->enqueue(QUICKBOOKS_IMPORT_INVOICE, null, QB_PRIORITY_INVOICE, array('iteratorID' => $idents['iteratorID']));
        }

        /*
         *  Use the built-in XML parser to parse the response and stuff it into a database.
         * 
         */
        $errnum = 0;
        $errmsg = '';

        $Parser = new QuickBooks_XML_Parser($xml);
        if ($Doc = $Parser->parse($errnum, $errmsg))
        {
            $Root = $Doc->getRoot();
            $List = $Root->getChildAt('QBXML/QBXMLMsgsRs/InvoiceQueryRs');
            //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'IMPORTED INVOICES XML Request #: ' . print_r($xml, true));
            foreach ($List->children() as $Invoice)
            {
                $sub = $Invoice->getChildDataAt('InvoiceRet Subtotal');
                $tax = $Invoice->getChildDataAt('InvoiceRet SalesTaxTotal');
                $arr = array(
                    'TxnID' => $Invoice->getChildDataAt('InvoiceRet TxnID'),
                    'TimeCreated' => $Invoice->getChildDataAt('InvoiceRet TimeCreated'),
                    'TimeModified' => $Invoice->getChildDataAt('InvoiceRet TimeModified'),
                    'RefNumber' => $Invoice->getChildDataAt('InvoiceRet RefNumber'),
                    'Customer_ListID' => $Invoice->getChildDataAt('InvoiceRet CustomerRef ListID'),
                    'Customer_FullName' => $Invoice->getChildDataAt('InvoiceRet CustomerRef FullName'),
                    'BalanceRemaining' => $Invoice->getChildDataAt('InvoiceRet BalanceRemaining'),
                    'amount' => $sub + $tax,
                    'seen' => 0,
                    'created_on' => time()
                );

                $ex = $this->quickbooks_m->qb_invoice_exists($arr['TxnID']);
                if (!$ex)
                {
                    $this->quickbooks_m->save_invoice_list($arr);
                    // Remove any old line items
                    $this->quickbooks_m->clear_invoice_lines($arr['TxnID']);

                    // Process the line items
                    foreach ($Invoice->children() as $Child)
                    {
                        if ($Child->name() == 'InvoiceLineRet')
                        {
                            $InvoiceLine = $Child;

                            $lineitem = array(
                                'TxnID' => $arr['TxnID'],
                                'TxnLineID' => $InvoiceLine->getChildDataAt('InvoiceLineRet TxnLineID'),
                                'Item_ListID' => $InvoiceLine->getChildDataAt('InvoiceLineRet ItemRef ListID'),
                                'Item_FullName' => $InvoiceLine->getChildDataAt('InvoiceLineRet ItemRef FullName'),
                                'Descrip' => $InvoiceLine->getChildDataAt('InvoiceLineRet Desc'),
                                //'Quantity' => $InvoiceLine->getChildDataAt('InvoiceLineRet Quantity'),
                                'Amount' => (float) $InvoiceLine->getChildDataAt('InvoiceLineRet Amount'),
                                'Rate' => (float) $InvoiceLine->getChildDataAt('InvoiceLineRet Rate'),
                                'created_on' => time(),
                            );
                            $this->quickbooks_m->save_invoice_line($lineitem);
                        }
                    }
                }
            }
        }
        //parse actual
        //$this->process_invoices();
        return true;
    }

    function _edit_payment_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                    <QBXML>
                    <QBXMLMsgsRq onError="stopOnError">
                        <ReceivePaymentModRq requestID="' . $requestID . '">
                            <ReceivePaymentMod>
                            <TxnID>' . $ex->txn_id . '</TxnID>
                            <EditSequence>' . $ex->edit_sequence . '</EditSequence>
                            <CustomerRef>
                                 <ListID>' . $ex->st_list_id . '</ListID>
                            </CustomerRef>
                             <DepositToAccountRef>
                                  <FullName>Equity</FullName>
                            </DepositToAccountRef>
                          </ReceivePaymentMod>
                       </ReceivePaymentModRq>
                      </QBXMLMsgsRq>
                   </QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action          The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                The unique identifier of the record
     * @param array $extra
     * @param string $err              An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _edit_payment_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        // Great, payment $ID has been added to QuickBooks with a QuickBooks
        // ListID value of: $idents['ListID']

        $this->quickbooks_m->set_seen($ID, 'fee_payment', ['modified_on' => time()], true);
        return true;
    }

    /**
     * Generate a qbXML response to add a payment to QuickBooks
     *
     * Our response function will in turn receive a qbXML response from QuickBooks
     * which contains all of the data stored for that customer within QuickBooks.
     *
     * @param string $requestID					You should include this in your qbXML request (it helps with debugging later)
     * @param string $action					The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID							The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra						Any extra data you included with the queued item when you queued it up
     * @param string $err						An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version					The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string							A valid qbXML request
     */
    function _add_payment_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        $tx_date = $ex->payment_date > 10000 ? date('Y-m-d', $ex->payment_date) : date('Y-m-d', $ex->created_on);
        $reff = explode(' ', $ex->transaction_no);
        $txno = isset($reff[0]) ? $reff[0] : '';
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                    <QBXML>
                    <QBXMLMsgsRq onError="stopOnError">
                        <ReceivePaymentAddRq requestID="' . $requestID . '">
                            <ReceivePaymentAdd>
                            <CustomerRef>
                                 <ListID>' . $ex->st_list_id . '</ListID>
                            </CustomerRef>
                            <TxnDate>' . $tx_date . '</TxnDate>                                
                            <RefNumber>' . $txno . '</RefNumber>
                            <TotalAmount>' . number_format($ex->amount, 2, '.', '') . '</TotalAmount>
                            <PaymentMethodRef>
                                <FullName>' . $ex->method . '</FullName>
                            </PaymentMethodRef>                            
                            <Memo>Fee Payment</Memo>
                            <DepositToAccountRef>
                                  <FullName>KCB</FullName>
                            </DepositToAccountRef>
                            <IsAutoApply>true</IsAutoApply>
                         </ReceivePaymentAdd>
                       </ReceivePaymentAddRq>
                      </QBXMLMsgsRq>
                   </QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action          The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                The unique identifier of the record
     * @param array $extra
     * @param string $err              An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _add_payment_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        // Great, payment $ID has been added to QuickBooks with a QuickBooks
        //	ListID value of: $idents['ListID']

        $this->quickbooks_m->set_seen($ID, 'fee_payment', ['txn_id' => $idents['TxnID'], 'list_id' => $idents['ListID'], 'qb_status' => 1, 'edit_sequence' => $idents['EditSequence'], 'modified_on' => time()], true);
        file_put_contents(FCPATH . 'paid.txt', print_r($idents, 1) . "\r\n----" . $extra['reg_no'] . "---\r\n" . print_r($xml, 1) . "-\r\n", 8);
        return true;
    }

    function _add_credit_memo_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        $tx_date = $ex->created_on > 10000 ? date('Y-m-d', $ex->created_on) : date('Y-m-d');
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                     <QBXML>
                        <QBXMLMsgsRq onError="stopOnError">
                            <CreditMemoAddRq requestID="' . $requestID . '">
                                <CreditMemoAdd>
                                     <CustomerRef>
                                        <ListID>' . $ex->st_list_id . '</ListID>
                                    </CustomerRef>
                                     <TxnDate>' . $tx_date . '</TxnDate>
                                    <RefNumber>CN-' . $ID . '</RefNumber>
                                     <Memo>Reversal - Invoice ' . $ex->invoice_no . '</Memo>
                                     <CreditMemoLineAdd>
                                        <ItemRef>
                                            <FullName>' . $ex->item . '</FullName>
                                        </ItemRef>
                                        <Desc>Credit Memo - Flagged Invoice </Desc>
                                        <Quantity>1</Quantity>
                                        <Rate>' . $ex->amount . '</Rate>
                                     </CreditMemoLineAdd>
                                 </CreditMemoAdd>
                             </CreditMemoAddRq>
                          </QBXMLMsgsRq>
                        </QBXML>
                   ';

        return $xml;
    }

    function _add_credit_memo_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        $ex = (object) $extra;
        // ListID value of: $idents['ListID']
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, ' Credit Memo Response  #: ' . print_r($idents, true));

        if ($ex->cat == 1)
        {
            $type = 'Invoice';
            $this->quickbooks_m->set_seen($ID, 'invoices', ['qb_status' => 4, 'modified_on' => time()], false);
        }
        else
        {
            $type = 'Extras';
            $this->quickbooks_m->set_seen($ID, 'fee_extra_specs', ['qb_status' => 4, 'modified_on' => time()], true);
        }

        $this->quickbooks_m->update_rv($ID, ['r_status' => 1, 'description' => 'Flagged - QB Deleted', 'modified_on' => time()], $type);
        
        $this->quickbooks_m->insert_s('qb_credit_notes', [
            'TxnID' => $idents['TxnID'],
            'student' => $ex->student,
            'item_id' => $ID,
            'item' => $ex->item,
            'amount' => $ex->amount,
            'RefNumber' => $idents['RefNumber'],
            'EditSequence' => $idents['EditSequence'],
            'TxnLineID' => $idents['TxnLineID'],
            'created_on' => time()]
        );
        file_put_contents(FCPATH . 'memo.txt', print_r($idents, 1) . "\r\n----" . $extra['student'] . "---\r\n" . "-\r\n", 8);
        return true;
    }

    /**
     * Generate a qbXML response to Query a customer
     *
     * Our response function will in turn receive a qbXML response from QuickBooks
     * which contains all of the data stored for that customer within QuickBooks.
     *
     * @param string $requestID					You should include this in your qbXML request (it helps with debugging later)
     * @param string $action					The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID							The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra						Any extra data you included with the queued item when you queued it up
     * @param string $err						An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version					The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string							A valid qbXML request
     */
    function _query_customer_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                    <QBXML>
                        <QBXMLMsgsRq onError="stopOnError">
                            <CustomerQueryRq requestID="' . $requestID . '">
                                <FullName>KISNAZION KARIITHI NGURE</FullName>
                            </CustomerQueryRq>
                        </QBXMLMsgsRq>
                    </QBXML>
                   ';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action          The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                The unique identifier of the record
     * @param array $extra
     * @param string $err              An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _query_customer_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        //ListID value of: $idents['ListID']
        //$this->quickbooks_m->set_seen($ID, 'admission', ['list_id' => $idents['ListID'], 'edit_sequence' => $idents['EditSequence'], 'modified_on' => time()], true);
        file_put_contents(FCPATH . 'customer.txt', print_r($idents, 1) . "\r\n----" . $extra['reg_no'] . "---\r\n" . "-\r\n", 8);
        return true;
    }

    /**
     * Generate a qbXML response to add an invoice to QuickBooks
     *
     * @param string $requestID		You should include this in your qbXML request (it helps with debugging later)
     * @param string $action			The QuickBooks action being performed
     * @param mixed $ID				The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra				Any extra data you included with the queued item when you queued it up
     * @param string $err				An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time	A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version			The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string					A valid qbXML request
     */
    function _add_invoice_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        $desc = $ex->type == 1 ? 'Tuition Fee' : $ex->desc;

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                    <QBXML>
                    <QBXMLMsgsRq onError="continueOnError">
                        <InvoiceAddRq requestID="' . $requestID . '">
                        <InvoiceAdd>
                                    <CustomerRef>
                                        <ListID>' . $ex->st_list_id . '</ListID>
                                    </CustomerRef>
                                    <TxnDate>' . date('Y-m-d', $ex->created_on) . '</TxnDate>
                                    <RefNumber>' . $ex->invoice_no . '</RefNumber>
                                    <Memo>' . $desc . '</Memo>
                                    <InvoiceLineAdd>
                                    <ItemRef>
                                        <FullName>' . $desc . '</FullName>
                                    </ItemRef>
                                    <Desc>' . $desc . ' - Term:' . $ex->term . ' Year ' . $ex->year . '</Desc>
                                    <Quantity>1</Quantity>
                                    <Rate>' . $ex->amount . '</Rate>
                                    </InvoiceLineAdd>
                            </InvoiceAdd>
                        </InvoiceAddRq>
                    </QBXMLMsgsRq>
                    </QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action          The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                The unique identifier of the record
     * @param array $extra
     * @param string $err              An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _add_invoice_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        $ex = (object) $extra;
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Invoices  Response : ' . print_r($xml, true));
        // Great, invoice $ID has been added to QuickBooks with a QuickBooks
        //ListID value of: $idents['ListID']

        if ($ex->type == 1)
        {
            $this->quickbooks_m->set_seen($ID, 'invoices', ['txn_id' => $idents['TxnID'], 'list_id' => $idents['ListID'], 'qb_status' => 1, 'edit_sequence' => $idents['EditSequence'], 'modified_on' => time()], false);
        }
        else
        {
            $this->quickbooks_m->set_seen($ID, 'fee_extra_specs', ['txn_id' => $idents['TxnID'], 'list_id' => $idents['ListID'], 'qb_status' => 1, 'edit_sequence' => $idents['EditSequence'], 'modified_on' => time()], true);
        }

        file_put_contents(FCPATH . 'invoices.txt', "\r\n" . (int) $ID . "\r\n-------\r\n" . print_r($xml, 1) . "-\r\n", 8);
        return true;
    }

    /**
     * Generate a qbXML response to add a particular customer to QuickBooks
     * 
     * Our response function will in turn receive a qbXML response from QuickBooks 
     * which contains all of the data stored for that customer within QuickBooks. 
     * 
     * @param string $requestID					You should include this in your qbXML request (it helps with debugging later)
     * @param string $action					The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID							The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra						Any extra data you included with the queued item when you queued it up
     * @param string $err						An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version					The max qbXML version your QuickBooks version supports
     * @param string $locale					
     * @return string							A valid qbXML request
     */
    function _add_customer_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <?qbxml version="13.0"?>
		<QBXML>
                        <QBXMLMsgsRq onError="stopOnError">
			<CustomerAddRq requestID="' . $requestID . '">
                                <CustomerAdd>
                                <Name>' . strtoupper(strtolower($ex->first_name . ' ' . $ex->last_name)) . '</Name>
                                <CompanyName>' . $ex->company . '</CompanyName>
                                <FirstName>' . strtoupper(strtolower($ex->first_name)) . '</FirstName>
                                <LastName>' . strtoupper(strtolower($ex->last_name)) . '</LastName>
                                <Phone>' . $ex->phone . '</Phone>
                                <Email>' . $ex->email . '</Email>';
        if ($ex->bal != 0)
        {
            $xml .= '<OpenBalance>' . $ex->bal . '</OpenBalance>';
        }
        $xml .= '<AccountNumber>' . $ex->reg . '</AccountNumber>
                                 </CustomerAdd>
                        </CustomerAddRq>
                    </QBXMLMsgsRq>
		</QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks   
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action          The action that was performed (CustomerAdd in this case)
     * @param mixed $ID                The unique identifier of the record
     * @param array $extra	
     * @param string $err              An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _add_customer_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        // Great, customer $ID has been added to QuickBooks with a QuickBooks ListID value of: $idents['ListID']

        $this->quickbooks_m->set_seen($ID, 'admission', ['list_id' => $idents['ListID'], 'qb_status' => 1, 'edit_sequence' => $idents['EditSequence'], 'modified_on' => time()], true);
        file_put_contents(FCPATH . 'quickbooks.txt', $idents['ListID'] . "\r\n-------\r\n" . print_r($idents, 1) . "-\r\n", 8);
        return true;
    }

    /**
     * Generate a qbXML response to Modify a particular customer in QuickBooks
     *
     *
     * @param string $requestID					You should include this in your qbXML request (it helps with debugging later)
     * @param string $action					The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID							The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra						Any extra data you included with the queued item when you queued it up
     * @param string $err						An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version					The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string							A valid qbXML request
     */
    function _del_payment_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $ex = (object) $extra;

        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Delete  Request : ' . print_r($extra, true));
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                    <?qbxml version="13.0"?>
                       <QBXML>
                           <QBXMLMsgsRq onError="stopOnError">
                             <TxnDelRq requestID="' . $requestID . '">
                                <TxnDelType>ReceivePayment</TxnDelType>
                               <TxnID>' . $ex->txn_id . '</TxnID>
                             </TxnDelRq>
                       </QBXMLMsgsRq>
                      </QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action        The action that was performed (CustomerAdd in this case)
     * @param mixed $ID             The unique identifier of the record
     * @param array $extra
     * @param string $err           An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _del_payment_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        $ex = (object) $extra;

        $type = $ex->type == 1 ? 'Voided' : 'Flagged';
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, $ex->txn_id . ' Payment removed  ');
        $this->quickbooks_m->update_rv($ID, ['r_status' => 1, 'description' => $type . '  - QB Deleted', 'modified_on' => time()], 'Payment');
        $this->quickbooks_m->set_seen($ID, 'fee_payment', ['qb_status' => 4, 'modified_on' => time()], true);
        return true;
    }

    /**
     * Generate a qbXML response to Modify a particular customer in QuickBooks
     *
     *
     * @param string $requestID					You should include this in your qbXML request (it helps with debugging later)
     * @param string $action					The QuickBooks action being performed (CustomerAdd in this case)
     * @param mixed $ID							The unique identifier for the record (maybe a customer ID number in your database or something)
     * @param array $extra						Any extra data you included with the queued item when you queued it up
     * @param string $err						An error message, assign a value to $err if you want to report an error
     * @param integer $last_action_time			A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param integer $last_actionident_time	A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param float $version					The max qbXML version your QuickBooks version supports
     * @param string $locale
     * @return string							A valid qbXML request
     */
    function _mod_customer_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <?qbxml version="8.0"?>
                <QBXML>
                <QBXMLMsgsRq onError="stopOnError">
                    <CustomerModRq requestID="' . $requestID . '">
                    <CustomerMod>
                        <ListID>80000076-1608046669</ListID>
                        <EditSequence>1608046669</EditSequence>
                        <OpenBalance>
                          <Amount>-3000.00</Amount>
                        </OpenBalance>
                     </CustomerMod>
                    </CustomerModRq>
                </QBXMLMsgsRq>
                </QBXML>';

        return $xml;
    }

    /**
     * Receive a response from QuickBooks
     * @param string $requestID     The requestID you passed to QuickBooks previously
     * @param string $action        The action that was performed (CustomerAdd in this case)
     * @param mixed $ID             The unique identifier of the record
     * @param array $extra
     * @param string $err           An error message, assign a valid to $err if you want to report an error
     * @param int $last_action_time A unix timestamp (seconds) indicating when the last action of this type was dequeued (i.e.: for CustomerAdd, the last time a customer was added, for CustomerQuery, the last time a CustomerQuery ran, etc.)
     * @param int $last_actionident_time A unix timestamp (seconds) indicating when the combination of this action and ident was dequeued (i.e.: when the last time a CustomerQuery with ident of get-new-customers was dequeued)
     * @param string $xml                   	The complete qbXML response
     * @param array $idents		An array of identifiers that are contained in the qbXML response
     * @return void
     */
    function _mod_customer_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        // Great, customer $ID has been added to QuickBooks with a QuickBooks
        //	ListID value of: $idents['ListID']

        /*
          mysql_query("UPDATE your_customer_table SET quickbooks_listid = '" . mysql_escape_string($idents['ListID']) . "' WHERE your_customer_ID_field = " . (int) $ID);
         */
        file_put_contents(FCPATH . 'quickbooks.txt', "\r\n " . $idents['ListID'] . "\r\n", 8);
        return true;
    }

    /**
     * Build a request to import customers already in QuickBooks into our application
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $version
     * @param type $locale
     * @return string
     */
    function _fetch_customer_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Fetch Students Request: with QBXML V. ' . $version);

        // Iterator support (break the result set into small chunks)
        $attr_iteratorID = '';
        $attr_iterator = ' iterator="Start" ';
        if (empty($extra['iteratorID']))
        {
            // This is the first request in a new batch
            $last = $this->_get_last_run($user, $action);
            $this->_set_last_run($user, $action);   // Update the last run time to NOW()
            // Set the current run to $last
            $this->_set_current_run($user, $action, $last);
        }
        else
        {
            // This is a continuation of a batch
            $attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
            $attr_iterator = ' iterator="Continue" ';
            $last = $this->_get_current_run($user, $action);
        }
        $last = '2022-04-01T00:00:00'; //overide last
        $to = date('Y-m-d\TH:i:s');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
                  <?qbxml version="13.0"?>
                      <QBXML>
                          <QBXMLMsgsRq onError="continueOnError">
                              <CustomerQueryRq requestID="' . $requestID . '">
                                   <FromModifiedDate>' . $last . '</FromModifiedDate>
                                   <ToModifiedDate>' . $to . '</ToModifiedDate>
                              </CustomerQueryRq>
                      </QBXMLMsgsRq>
                  </QBXML>';

        return $xml;
    }

    /**
     * Handle a response from QuickBooks 
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $xml
     * @param type $idents
     * @return boolean
     */
    function _fetch_customer_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        if (!empty($idents['iteratorRemainingCount']))
        {
            // Queue up another request
            $Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
            $Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, null, QB_PRIORITY_CUSTOMER, array('iteratorID' => $idents['iteratorID']));
        }

        /**
         * Response from QuickBooks is now stored in $xml. 
         * Use the built-in XML parser to parse the response and stuff it into a database.
         */
        $errnum = 0;
        $errmsg = '';
        $Parser = new QuickBooks_XML_Parser($xml);
        if ($Doc = $Parser->parse($errnum, $errmsg))
        {
            $b = 0;
            $k = 0;
            $u = 0;
            $Root = $Doc->getRoot();
            $List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerQueryRs');

            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming XML Response : ' . print_r($xml, true));

            foreach ($List->children() as $customer)
            {
                $k++;
                $name = $customer->getChildDataAt('CustomerRet FullName');
                $acc = $customer->getChildDataAt('CustomerRet AccountNumber');
                $seq = $customer->getChildDataAt('CustomerRet EditSequence');
                $lst = $customer->getChildDataAt('CustomerRet ListID');
                $balance = $customer->getChildDataAt('CustomerRet TotalBalance');
                $bal = (float) $balance;

                //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming XML Response : ' . print_r($name . ' - ' . $acc . ' - ' . ' - ' . $lst, true));
                $st = $this->quickbooks_m->get_by_adm($acc);
                if (!empty($st))
                {
                    $ss = $this->quickbooks_m->get_bal($st->id);
                    $arr = [
                        'list_id' => $lst,
                        'qb_name' => $name,
                        'student' => $st->id,
                        'qb_account' => $acc,
                        's_bal' => $ss->balance,
                        'status' => $customer->getChildDataAt('CustomerRet IsActive') == 'true' ? 1 : 0,
                        'q_bal' => $customer->getChildDataAt('CustomerRet Balance'),
                        'created_on' => time()
                    ];
                    $u++;
                    $ex = $this->quickbooks_m->qb_compare_exists($lst);
                    if ($ex)
                    {
                        $row = $this->quickbooks_m->get_compare($lst);
                        $this->quickbooks_m->set_seen($row->id, 'qb_compare', ['s_bal' => $ss->balance, 'q_bal' => $bal, 'modified_on' => time()], FALSE);

                        //get ss bal
                        if ($bal != (float) $ss->balance)
                        {
                            $b++;
                            file_put_contents(__DIR__ . '/bal.csv', $st->id . ',' . $acc . ',' . $lst . ',' . $name . ',' . $ss->balance . ',' . $bal . "\n", FILE_APPEND);
                        }
                    }
                    else
                    {
                        $this->quickbooks_m->save_compare($arr);
                    }
                }
            }

            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Diff Balance  : ' . $b);
            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Total Import  : ' . $k . 'Total found  : ' . $u);
        }

        return true;
    }

    function _fetch_customer_resp_v1($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        if (!empty($idents['iteratorRemainingCount']))
        {
            // Queue up another request
            $Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
            $Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, null, QB_PRIORITY_CUSTOMER, array('iteratorID' => $idents['iteratorID']));
        }

        /**
         * Response from QuickBooks is now stored in $xml. 
         * Use the built-in XML parser to parse the response and stuff it into a database.
         */
        $errnum = 0;
        $errmsg = '';
        $Parser = new QuickBooks_XML_Parser($xml);
        if ($Doc = $Parser->parse($errnum, $errmsg))
        {
            $b = 0;
            $k = 0;
            $u = 0;
            $Root = $Doc->getRoot();
            $List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerQueryRs');

            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming XML Response : ' . print_r($xml, true));

            foreach ($List->children() as $customer)
            {
                $k++;
                $name = $customer->getChildDataAt('CustomerRet FullName');
                $acc = $customer->getChildDataAt('CustomerRet AccountNumber');
                $seq = $customer->getChildDataAt('CustomerRet EditSequence');
                $lst = $customer->getChildDataAt('CustomerRet ListID');
                $balance = $customer->getChildDataAt('CustomerRet TotalBalance');
                $bal = (float) $balance;
                //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming XML Response : ' . print_r($name . ' - ' . $acc . ' - ' . ' - ' . $lst, true));
                $arr = [
                    'list_id' => $lst,
                    'acc_no' => $acc,
                    'edit_sequence' => $seq,
                    'time_created' => $customer->getChildDataAt('CustomerRet TimeCreated'),
                    'name' => $customer->getChildDataAt('CustomerRet Name'),
                    'full_name' => $name,
                    'status' => $customer->getChildDataAt('CustomerRet IsActive') == 'true' ? 1 : 0,
                    'balance' => $customer->getChildDataAt('CustomerRet Balance'),
                    'email' => $customer->getChildDataAt('CustomerRet Email'),
                    'seen' => 0,
                    'created_on' => time()
                ];
                $rw = $this->quickbooks_m->get_by_adm($acc);
                if ($rw)
                {
                    $u++;
                    $this->quickbooks_m->update_rw($rw->id, ['list_id' => $lst, 'qb_status' => 1, 'edit_sequence' => $seq, 'modified_on' => time()]);
                }


                $ex = $this->quickbooks_m->qb_customer_exists($lst);
                if ($ex)
                {
                    $row = $this->quickbooks_m->get_customer($lst);
                    if ($row->seen == 0)
                    {
                        $upd = ['acc_no' => $acc, 'edit_sequence' => $seq, 'seen' => 1, 'qb_status' => 1, 'balance' => $bal, 'modified_on' => time()];
                    }
                    else
                    {
                        //$this->quickbooks_m->set_seen($ss->id, 'qb_customers', ['seen' => 1], FALSE);
                        $upd = ['balance' => $bal, 'seen' => 1, 'modified_on' => time()];
                    }
                    $upp = $this->quickbooks_m->update_customer($row->id, $upd);
                    if ($upp)
                    {
                        /*  $adm = $this->quickbooks_m->get_by_adm($acc);
                          if (!empty($adm))
                          {
                          //get ss bal
                          $ss = $this->quickbooks_m->get_bal($adm->id);
                          if ($bal != (float) $ss->balance)
                          {
                          $b++;
                          file_put_contents(__DIR__ . '/bal.csv', $adm->id . ',' . $acc . ',' . $lst . ',' . $name . ',' . $ss->balance . ',' . $bal . "\n", FILE_APPEND);
                          }
                          } */
                    }
                }
                else
                {
                    $this->quickbooks_m->save_customer_list($arr);
                }
            }
            //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Diff Balance  : ' . $b);
            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Total Import  : ' . $k . 'Total Linked  : ' . $u);
        }
        //update New Students
        //$this->link_students();
        return true;
    }

    /**
     * Fetch Payments Request
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $version
     * @param type $locale
     * @return string
     */
    function _fetch_payments_req($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
    {
        $version = '4.0';
        $last = $this->_get_last_run($user, $action);
        $to = date('Y-m-d\TH:i:s');
        $this->_set_last_run($user, $action);

        $last = '2013-01-01T00:00:00'; //overide last
        //$to = '2016-06-01T00:00:00'; //overide to
        // Build the request
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                                 <?qbxml version="9.0"?>
                                <QBXML>
                                  <QBXMLMsgsRq onError="stopOnError">
                                    <ReceivePaymentQueryRq requestID="' . $requestID . '">
                                       <ModifiedDateRangeFilter>
                                        <FromModifiedDate>' . $last . '</FromModifiedDate>
                                        <ToModifiedDate>' . $to . '</ToModifiedDate>
                                      </ModifiedDateRangeFilter> 
                                    </ReceivePaymentQueryRq>
                                 </QBXMLMsgsRq>
                                </QBXML>';

        return $xml;
    }

    /**
     * Handle a response from QuickBooks 
     * 
     * @param type $requestID
     * @param type $user
     * @param type $action
     * @param type $ID
     * @param type $extra
     * @param type $err
     * @param type $last_action_time
     * @param type $last_actionident_time
     * @param type $xml
     * @param type $idents
     * @return boolean
     */
    function _fetch_payments_resp($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
    {
        /*
         * Parse the response and stuff it into db
         */
        $errnum = 0;
        $errmsg = '';

        $Parser = new QuickBooks_XML_Parser($xml);
        if ($Doc = $Parser->parse($errnum, $errmsg))
        {
            $Root = $Doc->getRoot();
            $List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ReceivePaymentQueryRs');

            //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming XML Response : ' . print_r($xml, true));
            $sn = 0;
            $un = 0;
            foreach ($List->children() as $paid)
            {
                $ammt = (float) $paid->getChildDataAt('ReceivePaymentRet TotalAmount');
                $txn = $paid->getChildDataAt('ReceivePaymentRet TxnID');
                $lst = $paid->getChildDataAt('ReceivePaymentRet CustomerRef ListID');
                $arr = array(
                    'payment_date' => $paid->getChildDataAt('ReceivePaymentRet TxnDate'),
                    'reg_no' => $lst,
                    'amount' => $ammt,
                    'bank_id' => $paid->getChildDataAt('ReceivePaymentRet DepositToAccountRef ListID'),
                    'payment_method' => $paid->getChildDataAt('ReceivePaymentRet PaymentMethodRef FullName'),
                    'ac_account' => $paid->getChildDataAt('ReceivePaymentRet ARAccountRef ListID'),
                    'description' => 'Fee Payment',
                    'status' => 1,
                    'seen' => 0,
                    'transaction_no' => $txn,
                    'refno' => $paid->getChildDataAt('ReceivePaymentRet RefNumber'),
                    'created_on' => $paid->getChildDataAt('ReceivePaymentRet TimeCreated'),
                    'modified_on' => $paid->getChildDataAt('ReceivePaymentRet TimeModified')
                );
                if (isset($arr['transaction_no']) && isset($arr['reg_no']))
                {
                    if ($this->quickbooks_m->qb_payment_exists($arr['transaction_no'], $arr['reg_no']))
                    {
                        $psr = $this->quickbooks_m->qb_payment_row($arr['transaction_no'], $arr['reg_no']);
                        if ($ammt != (float) $psr->amount)
                        {
                            if ($psr->seen == 1)
                            {
                                $sn++;
                                //file_put_contents(__DIR__ . '/log.csv', $psr->id . ',' . $lst . ',' . $psr->amount . ',' . $ammt . "\n", FILE_APPEND);
                                //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, $txn . ' : was' . $psr->amount . '   now ' . $ammt);
                            }
                            else
                            {
                                $un++;
                            }
                        }
                    }
                    else
                    {
                        $this->quickbooks_m->save_payments_list($arr);
                    }
                }
            }
            QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Edit Results: seen : ' . $sn . '   not seen ' . $un);
        }
        //now parse the imported Rows
        $this->process_payments();
        return true;
    }

    /**
     * Catch and handle a "that string is too long for that field" error (err no. 3070) from QuickBooks
     * 
     * @param string $requestID			
     * @param string $action
     * @param mixed $ID
     * @param mixed $extra
     * @param string $err
     * @param string $xml
     * @param mixed $errnum
     * @param string $errmsg
     * @return void
     */
    function _error_too_long($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
    {
        //mail('be@your-domain.com', 'QuickBooks error occured!', 'QuickBooks thinks that ' . $action . ': ' . $ID . ' has a value which will not fit in a QuickBooks field...');
    }

    /**
     * Handle a 500 not found error from QuickBooks
     * 
     * Instead of returning empty result sets for queries that don't find any 
     * records, QuickBooks returns an error message. This handles those error 
     * messages, and acts on them by adding the missing item to QuickBooks. 
     */
    function _error_500($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
    {
        // $Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Error 500: Action # ' . $action . ': ' . print_r($xml, true));

        if ($action == QUICKBOOKS_IMPORT_INVOICE)
        {
            return true;
        }
        else if ($action == QUICKBOOKS_IMPORT_CUSTOMER)
        {
            return true;
        }
        else if ($action == QUICKBOOKS_IMPORT_RECEIVEPAYMENT)
        {
            return true;
        }
        else if ($action == QUICKBOOKS_IMPORT_ITEM)
        {
            return true;
        }
        else if ($action == QUICKBOOKS_IMPORT_PURCHASEORDER)
        {
            return true;
        }

        return false;
    }

    /**
     * Catch any errors that occur
     * 
     * @param string $requestID			
     * @param string $action
     * @param mixed $ID
     * @param mixed $extra
     * @param string $err
     * @param string $xml
     * @param mixed $errnum
     * @param string $errmsg
     * @return void
     */
    function _error_catchall($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
    {
        $message = '';
        $message .= '## Request ID: ' . $requestID . "\r\n";
        $message .= 'User: ' . $user . "\r\n";
        $message .= 'Action: ' . $action . "\r\n" . print_r($xml, true);
        $message .= 'ID: ' . $ID . "\r\n";
        $message .= 'Extra: ' . print_r($extra, true) . "\r\n";
        $message .= 'Error: ' . $err . "\r\n";
        $message .= 'Error number: ' . $errnum . "\r\n";
        $message .= 'Error message: ' . $errmsg . "\r\n";

        //QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming Error Action # ' . $action . ': ' . $message . print_r($xml, true));
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Incoming Error Action # ' . $action . ': ' . $message);
    }

    /**
     * Init Constants
     * 
     */
    function set_constants()
    {
        /**
         * Configuration parameter for the quickbooks_config table, used to keep track of the last time the QuickBooks sync ran
         */
        define('QB_QUICKBOOKS_CONFIG_LAST', 'last');

        /**
         * Configuration parameter for the quickbooks_config table, used to keep track of the timestamp for the current iterator
         */
        define('QB_QUICKBOOKS_CONFIG_CURR', 'curr');

        /**
         * Maximum number of customers/invoices returned at a time when doing the import
         */
        define('QB_QUICKBOOKS_MAX_RETURNED', 200);
        define('QB_PRIORITY_PURCHASEORDER', 4);

        /**
         * Request priorities, items sync first
         */
        define('QB_PRIORITY_ITEM', 3);

        /**
         * Request priorities, customers
         */
        define('QB_PRIORITY_CUSTOMER', 1);

        /**
         * Request priorities, salesorders
         */
        define('QB_PRIORITY_SALESORDER', 1);

        /**
         * Request priorities, invoices last... 
         */
        define('QB_PRIORITY_INVOICE', 3);
        define('QB_PRIORITY_RECEIPT', 2);
        define('QB_PRIORITY_CREDITMEMO', 2);

        /**
         * Send error notices to this e-mail address
         */
        define('QB_QUICKBOOKS_MAILTO', 'sanepen@gmail.com');
        define('QB_QUICKBOOKS_DSN', $this->dsn);
    }

    /**
     * Just a script to create student from customer imported from QB
     */
    function process_students()
    {
        $list = $this->quickbooks_m->fetch_qb_list();
        echo '<pre>';
        print_r($list);
        echo '</pre>';
        die();
        $i = 0;
        foreach ($list as $ss)
        {
            $first_name = $ss->FirstName;
            $last_name = $ss->LastName;
            $email = $ss->Email;
            $form = array(
                'list_id' => $ss->ListID,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'status' => 1,
                'campus_id' => 1,
                'created_by' => 000,
                'created_on' => strtotime($ss->TimeCreated)
            );

            $sid = $this->quickbooks_m->add_student($form);
            if ($sid)
            {
                $i++;
                $this->quickbooks_m->update_rw($sid, array('reg_no' => 'RN/' . date('y') . '/' . str_pad($sid, 4, '0', 0)));
                $username = $first_name . '.' . $last_name;

                if ($email == "")
                {
                    $cinfo = explode('@', $this->school->email);
                    $mail = $cinfo[1];
                    $email = strtolower(str_replace(' ', '_', $username) . '@' . $mail);
                }
                $pass = '123456';

                $additional = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone' => '',
                    'me' => 000
                );
                $ssid = $this->ion_auth->register($username, $pass, $email, $additional);
                //add to Parents group
                if ($ssid)
                {
                    $this->ion_auth->add_to_group(8, $ssid);
                    $this->quickbooks_m->update_rw($sid, array('user_id' => $ssid, 'modified_by' => 000, 'modified_on' => time()));
                }
            }
        }

        //echo 'found ' . count($list) . ' total, put ' . $i . 'new';
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'found ' . count($list) . ' Students From Quickbooks, put ' . $i . ' new');
    }

    function link_students()
    {
        $list = $this->quickbooks_m->fetch_customer_list();

        $i = 0;
        foreach ($list as $ss)
        {
            $adm = $this->quickbooks_m->get_by_adm($ss->acc_no);
            if (!empty($adm))
            {
                $this->quickbooks_m->link_student($adm->id, ['list_id' => $ss->list_id]);
                $this->quickbooks_m->set_seen($ss->id, 'qb_customers', ['seen' => 1], FALSE);
                $i++;
            }
        }
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Found ' . count($list) . ' Students, Linked ' . $i . ' ');
        // echo 'Found ' . count($list) . ' Students, Linked ' . $i . ' ';
    }

    /**
     * update student link by list_id
     */
    function update_link()
    {
        $list = $this->quickbooks_m->fetch_seen_customers();

        $i = 0;
        foreach ($list as $ss)
        {
            $adm = $this->quickbooks_m->get_by_adm($ss->acc_no);
            if (!empty($adm))
            {
                echo '<pre>';
                echo $this->quickbooks_m->link_raw($adm->id, ['list_id' => "'" . $ss->list_id . "'"]);
                echo '</pre>';
                $this->quickbooks_m->set_seen($ss->id, 'qb_customers', ['seen' => 1], FALSE);
                $i++;
                exit();
            }
        }
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Found ' . count($list) . ' Students, Linked ' . $i . ' ');
        // echo 'Found ' . count($list) . ' Students, Linked ' . $i . ' ';
    }

    /**
     * Process Student Invoices From Quickbooks
     * 
     */
    function process_invoices()
    {
        $list = $this->quickbooks_m->fetch_invoice_list();

        $i = 0;
        foreach ($list as $iv)
        {
            $student = $this->quickbooks_m->get_regno($iv->Customer_ListID);
            if (isset($student->id) && !empty($student->id))
            {
                $form = array(
                    'txn_id' => $iv->TxnID,
                    'refno' => $iv->RefNumber,
                    'reg_no' => $student->id,
                    'amount' => $iv->amount,
                    'balance' => $iv->BalanceRemaining,
                    'created_on' => strtotime($iv->TimeCreated),
                    'created_by' => 000,
                    'modified_on' => strtotime($iv->TimeModified)
                );

                $ivid = $this->quickbooks_m->put_imported_invoice($form);
                if ($ivid)
                {
                    $i++;
                    $lines = $this->quickbooks_m->get_invoice_lines($iv->TxnID);
                    foreach ($lines as $ln)
                    {
                        $item = array(
                            'txn_line_id' => $ln->TxnLineID,
                            'invoice_id' => $ivid,
                            'txn_id' => $ln->TxnID,
                            'item_list_id' => $ln->Item_ListID,
                            'Item_name' => $ln->Item_FullName,
                            'descrip' => $ln->Descrip == '0' ? 'Invoice' : $ln->Descrip,
                            'date' => strtotime($iv->TimeCreated),
                            //'quantity' => $ln->Quantity ? $ln->Quantity : 1,
                            'rate' => $ln->Rate,
                            'amount' => $ln->Amount,
                            'created_on' => time(),
                            'created_by' => 000,
                        );

                        $this->quickbooks_m->put_invoice_item($item);
                    }
                }
                $this->quickbooks_m->set_seen($iv->id, 'qb_invoice', ['seen' => 1], FALSE);
            }
        }
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'found ' . count($list) . ' Invoices From Quickbooks, put ' . $i . ' new');
    }

    /**
     * Process Student Payments From Quickbooks
     * 
     */
    function process_payments()
    {
        $list = $this->quickbooks_m->fetch_payments_list();

        $i = 0;
        foreach ($list as $py)
        {
            $student = $this->quickbooks_m->get_regno($py->reg_no);
            if (isset($student->id) && !empty($student->id))
            {
                $i++;
                $receipt = array(
                    'total' => $py->amount,
                    'student' => $student->id,
                    'pay_ref' => $py->id,
                    'created_by' => 00,
                    'created_on' => time()
                );

                $rec_id = $this->quickbooks_m->insert_rec($receipt);
                if ($rec_id)
                {
                    $paym = array(
                        'payment_date' => strtotime($py->created_on),
                        'reg_no' => $student->id,
                        'amount' => $py->amount,
                        'payment_method' => $py->payment_method,
                        'transaction_no' => $py->refno,
                        'bank_id' => $py->bank_id,
                        'receipt_id' => $rec_id,
                        'status' => 1,
                        'description' => $py->description,
                        'created_by' => 000,
                        'created_on' => time()
                    );
                    $this->quickbooks_m->create_rec($paym);
                }

                $this->quickbooks_m->set_seen($py->id, 'qb_payments', ['seen' => 1]);
            }
        }

        echo 'found ' . count($list) . ' Payments from Quickbooks, put ' . $i . ' new';
        QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'found ' . count($list) . ' Payments from Quickbooks, put ' . $i . ' new');
    }

    /**
     * Fix Payments Whose Link not found in Admission Table
     * by Looking for Link in qb_customers table
     * 
     */
    function fix_pays()
    {
        $list = $this->quickbooks_m->fetch_payments_list();

        $i = 0;
        $x = 0;
        $y = 0;
        $z = 0;
        $b = 0;
        foreach ($list as $py)
        {
            $st = $this->quickbooks_m->get_regno($py->reg_no);
            if (isset($st->id) && !empty($st->id))
            {
                $i++;
            }
            else
            {
                $found = $this->quickbooks_m->search_reg($py->reg_no);
                if (count($found))
                {
                    foreach ($found as $f)
                    {
                        if ($f->acc_no == '0')
                        {
                            //can't help you. Go to QB and Put the Acc. No. for this Customer!!
                            $y++;
                        }
                        else
                        {
                            $student = $this->quickbooks_m->get_by_adm($f->acc_no);
                            if (isset($student->id) && !empty($student->id))
                            {
                                $b++;
                                $receipt = array(
                                    'total' => $py->amount,
                                    'student' => $student->id,
                                    'pay_ref' => $py->id,
                                    'created_by' => 00,
                                    'created_on' => time()
                                );

                                $rec_id = $this->quickbooks_m->insert_rec($receipt);
                                if ($rec_id)
                                {
                                    $paym = array(
                                        'payment_date' => strtotime($py->created_on),
                                        'reg_no' => $student->id,
                                        'amount' => $py->amount,
                                        'payment_method' => $py->payment_method,
                                        'transaction_no' => $py->refno,
                                        'bank_id' => $py->bank_id,
                                        'receipt_id' => $rec_id,
                                        'status' => 1,
                                        'description' => $py->description,
                                        'created_by' => 000,
                                        'created_on' => time()
                                    );
                                    $this->quickbooks_m->create_rec($paym);
                                }

                                $this->quickbooks_m->set_seen($py->id, 'qb_payments', ['seen' => 1]);
                                $x++;
                            }
                        }
                    }
                }
                else
                {
                    //Not Found Anywhere 
                    $z++;
                }
            }
        }

        echo 'Found ' . count($list) . ' Found ' . $i . ' Set' . ' Found ' . $x . ' handles ' . $b . ' to fix' . ' Cant help ' . $y . ' cases';
    }

    /**
     * Fix Invoices Whose Link not found in Admission Table
     * by Looking for Link in qb_customers table
     * 
     */
    function fix_ivs()
    {
        $list = $this->quickbooks_m->fetch_invoice_list();

        $i = 0;
        $x = 0;
        $y = 0;
        $z = 0;
        $b = 0;
        foreach ($list as $iv)
        {
            $st = $this->quickbooks_m->get_regno($iv->Customer_ListID);
            if (isset($st->id) && !empty($st->id))
            {
                $i++;
            }
            else
            {
                $found = $this->quickbooks_m->search_reg($iv->Customer_ListID);
                if (count($found))
                {
                    foreach ($found as $f)
                    {
                        if ($f->acc_no == '0')
                        {
                            //can't help you. Go to QB and Put the Acc. No. for this Customer!!
                            $y++;
                        }
                        else
                        {
                            $student = $this->quickbooks_m->get_by_adm($f->acc_no);
                            if (isset($student->id) && !empty($student->id))
                            {
                                $b++;
                            }
                            else
                            {
                                
                            }
                            $x++;
                        }
                    }
                }
                else
                {
                    //Not Found Anywhere 
                    $z++;
                }
            }
        }

        echo 'Found ' . count($list) . ' Found ' . $i . ' Set' . ' Found ' . $x . ' handles ' . $x . ' to fix' . ' Cant help ' . $y . ' cases';
    }

    function fixarr($id, $amt)
    {
        return $this->quickbooks_m->fx_arrear($id, $amt);
    }

    /**
     * Fix Customer name in imported Data
     * 
     */
    function fix_name()
    {
        $ivs = $this->quickbooks_m->fetch_invoice_list();

        $i = 0;
        foreach ($ivs as $cs)
        {
            $i++;
            $name = $cs->Customer_FullName;
            $nw_name = explode(':', $name, 2);
            $form = array(
                'Customer_FullName' => $nw_name[0]
            );

            $this->quickbooks_m->update_ivs($cs->TxnID, $form);
        }
    }

    function fix_pds()
    {
        die('not found');
        $list = $this->quickbooks_m->fetch_payments_list();
        $i = 0;
        foreach ($list as $p)
        {
            $i++;
            $fullname = $this->quickbooks_m->fetch_full_names($p->reg_no);
            echo $i . '<pre>';
            print_r($fullname);
            $nw_name = explode(':', $fullname, 2);
            $new_lis = $this->quickbooks_m->get_list_id($nw_name[0]);
            echo '  nw:-  ' . $new_lis . ' ';
            echo ' </pre>';
            $form = array('reg_no' => $new_lis);

            $this->quickbooks_m->update_pay($p->id, $form);
        }
    }

    function clear()
    {
        $this->quickbooks_m->clear_log();
        redirect('admin/quickbooks/log');
    }

}
