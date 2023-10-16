<?php

// acces à tout : p.druez@ciel-software.be psw : PHD123456 - vérifié le 7/11/2019
// acces à tout : druezphilippe@yopmail.com psw : Pic0206! - vérifié le 14/1/2021

defined('BASEPATH') or exit('No direct script access allowed');

@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', 600);

class Struct_XImport
{
	public $_MVTS; // N° Mouvement
	public $_ID; // Identification dans la base des sma_sales
	public $_JOUR; // Code journal
	public $_DECR; // Date écriture
	public $_DECH; // Date échéance
	public $_NUMP; // N° de pièce
	public $_COMP; // N° de compte
	public $_LIBE; // Libellé écriture
	public $_MONT; // Montant
	public $_SENS; // Sens montant
	public $_LETT; // Ref. pointage / lettrage
	public $_ANAL; // Code analytique
	public $_MTVA; // Montant TVA
	public $_TTVA; // Taux de TVA
	public $_CTVA; // Code TVA
	public $_GTVA; // grille TVA
	public $_GBAS; // grille Base
	//DateTime _DARE; // Date réelle
	//string _QUI1; //
}

class Struct_BadTrans
{
	public $_MVTS;
	public $_ID;
	public $_DEBIT;
	public $_CREDIT;
}

class Picsoo extends AdminController
{
	private $clientsid;
	public $clientsid_list;
	private $vatlist; // liste des paramètres de tva pour le client sélectionné
	private $ximportslist; // liste des données comptables
	private $journallist; // liste des journaux de vente

	private $ie_clientsid;
	private $ie_category;
	private $ie_startdate;
	private $ie_enddate;
	private $ie_reference;
	private $ie_updexisting;
	private $ie_typetft;
	private $ie_datatft;
	//private $ie_mytype;
	//private $ie_status;
	private $ie_tft_articles;
	private $ie_tft_clients;
	private $ie_tft_fournisseurs;
	private $ie_tft_transactions;

	private $reference_external;
	private $toexcel;

	private $dbg;
	private $gen_error_msg;
		
    public function __construct()
    {
        parent::__construct();
        
		// réduire l'accès à l'admin seulement
		/*if (!is_admin()) 
		{
            access_denied('Picsoo');
        }*/
        
        $this->load->model('staff_model');

		$this->load->model('picsoo_model');
		$this->load->model('invoice_items_model');
		$this->load->model('invoices_model');
		$this->load->model('clients_model'); // clients / fournisseurs
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
		//$this->load->model('settings_model'); // pour le AddCategory
		//$this->load->admin_model('pos_model'); // pour l'arrondi $this->sma->roundNumber($grand_total, $this->pos_settings->rounding); (pas utilisé)
		//$this->pos_settings = $this->pos_model->getSetting();

		$this->load->library('picsoo_ws', ['lang' => 'tata']); //OK

		$dbg = get_staff_user_id();
		$staff_member = $this->staff_model->get(get_staff_user_id());
		
        //$clientsid = $this->picsoo_ws->GetCompaniesList(); // toutes la liste
		if ( $this->picsoo_ws->IsDemoVersion() && $this->picsoo_ws->IsStaging() )
			$this->clientsid = $this->picsoo_ws->GetCompaniesListByEmail( 'demotic@gmail.com' ); // juste la liste associée à l'email
		else
			$this->clientsid = $this->picsoo_ws->GetCompaniesListByEmail( /*'druezphilippe@yopmail.com'*/ $staff_member->email ); // juste la liste associée à l'email
        
        foreach ($this->clientsid as $key => $value )
        {
            $var1 = $key;
            $var2 = $value;
            //$clt[] = array_combine($key, $value);
            //$clt[] = array_combine($value['clients_id'], $value['clients_id']." ".$value['organisation_name']);
            $this->clientsid_list[ $key ] = $value['clients_id']." ".$value['organisation_name'];
            //echo $clients_id['clients_id']." ".$clients_id['organisation_name']."<br>";
        }

        $this->data['staff_member_email']	= $staff_member->email;
        $this->data['clientspicsoo_list']   = $this->clientsid_list;
        $this->data['groups']         		= $this->clients_model->get_groups();
        //$this->data['categories']           = $this->site->getAllCategories();
        //$this->data['start_date']           = '01/01/2020' /*date('d-m-Y')*/ . ' 00:00'; 
        //$this->data['end_date']             = '31/12/' . date('Y') . ' 23:59'; 
        //$date       = date('Y-m-d', strtotime('-1 day'));
        $date       = date('Y-m-d', time());
        $sdate      = $date; // . ' 00:00:00';
        $edate      = $date; // . ' 23:59:59';
        $this->data['start_date']           = $sdate; //date('d/m/Y 00:00:00');
        $this->data['end_date']             = $edate; //date('d/m/Y 23:59:59');        
        $this->data['reference_no']         = 'SEQUOIA_xxxxxxxxx'; // . date('Y-m-d-His'); 
        $this->data['version']              = '0.08';
        $this->data['picsoourl']            = $this->picsoo_ws->GetPicsooURL();
		$this->data['upd_existing']			= '1'; // mettre '1' si checked ou '' si un-checked
		$this->data['version_type']			= $this->picsoo_ws->IsStaging() ? "* STAGING *" : "";
        
        $this->reference_external           = 'POS';
        $this->toexcel                      = false;
        
        $this->data['id'] = 0;
		$this->data['title'] = _l('import_export_picsoo');

        $this->dbg = "1";
        
		//log_message('debug', 'In picsoo ... ');        
    }
    
    public function picsoo($id = '')
    {
    }
    
    /*public function testXX()
    {
    	echo '<script>alert("Welcome to Geeks for Geeks")</script>';
    	ob_flush();
    	flush();
    	
    	redirect(admin_url('picsoo/picsoo'));
    }*/
    
    public function test()
    {
		/*
		//$this->db->empty_table('items');
		$this->db->empty_table('clients');
        ?>
    	<script>
        	alert("clients table truncated.");
        	window.location.href = "<?php echo admin_url('picsoo/picsoo'); ?>";
    	</script>
    	<?php
		*/
		return;
		
    	$StartKey = "1234";
    	$EndKey = "5678";
    	$run = "AFILE";
    	
		//echo 'Treating records ' . $StartKey . ' to ' . $EndKey .  "<br>";
		$back_url = 'http://www.picsoocloud.com/sequoia/modules/picsoo/async/back_sequoia.php?StartKey=' . $StartKey . '&EndKey=' . $EndKey . '&filename=data' . $run . '.txt';
		//echo $back_url . "<br>";
		$this->curlPostAsync($back_url, array('var' => 'content'));
    	
        ?>
    	<script>
        	alert("Welcome to Picsoo");
        	window.location.href = "<?php echo admin_url('picsoo/picsoo'); ?>";
    	</script>
    	<?php
	}
    
	public function index()
	{
        if (!has_permission('picsoo', '', 'view') /*&& !has_permission('picsoo', '', 'create')*/) 
        {
            access_denied('Picsoo');
        }

		/*if ( $this->picsoo_ws->IsStaging() )
			set_alert('warning', "Picsoo version STAGING connector !");
		else
			set_alert('danger', "Picsoo version LIVE connector !");*/
		
		if ($this->input->post()) 
		{
            $data = $this->input->post();
            
            $dbg = html_purify($this->input->post('picsoo_company', false));
		
            $var = $this->clientsid_list[$this->input->post('picsoo_company')];
            $this->ie_clientsid = strtok($var,' ');
            //$this->ie_clientsid = '10580'; // DEBUG -- LIVE -- My Template DA
            //$this->ie_clientsid = '10929'; // DEBUG -- STAGING -- @Domi Test Webservices
            $this->ie_clientsid = '10776'; // DEBUG -- LIVE -- Business Software
            //$this->session->set_flashdata('message', '$this->ie_clientsid');
            $this->ie_category = $this->input->post('category');
            $this->ie_startdate = $this->input->post('start_date');
            $this->ie_enddate = $this->input->post('end_date');
            $this->ie_reference = $this->input->post('reference_no');
            $this->ie_updexisting = $this->input->post('upd_existing');
            $this->ie_typetft = $this->input->post('typetft');
            $this->ie_datatft = $this->input->post('tft_data');
            //$this->ie_mytype = $this->input->post('mytype');
            //$this->ie_status = $this->input->post('user_status');
            //$this->ie_tft_articles = $this->input->post('tft_articles');
            //$this->ie_tft_clients = $this->input->post('tft_clients');
            //$this->ie_tft_fournisseurs = $this->input->post('tft_fournisseurs');
            //$this->ie_tft_transactions = $this->input->post('tft_transactions');

			$this->picsoo_model->Init($this->ie_clientsid);
            
            //$this->picsoo_model->TestModel();
            if ($this->picsoo_ws->IsDemoVersion()) 
            {
                set_alert('warning', lang('disabled_in_demo'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            
            //if( 1==0 ) //DEMO
            //{
            //    $this->session->set_flashdata('message', 'Fonction impossible en démo !');
            //}
            //else
            //{
                if($this->ie_datatft == null )
                //if( $this->ie_tft_articles == null && $this->ie_tft_clients == null && $this->ie_tft_fournisseurs == null && $this->ie_tft_transactions == null)
                {
                    set_alert('danger', 'Veuillez choisir au moins un élément à transférer.');
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $err = $this->ProcessDispatcher();

                if( $this->ie_typetft == 'import' )
                {   
                    $msg = lang('import_finished');
                    if( $err != 0 )
                        $msg .= ' (' . lang('errors_detected') . ')';
                    set_alert('success', $msg);
                }
                /*else
                {
                    $msg = lang('export_finished');
                    if( $err != 0 )
                        $msg .= ' (' . lang('errors_detected') . ')';
                    $this->session->set_flashdata('message', $msg);
                }*/
            //}
	        $data['title'] = lang('Picsoo');
    	    //$this->load->view('picsoo', $data);
			redirect(admin_url('picsoo/'));            
			
            //echo( 'Good !');
        } 
        else 
        {
	        $this->load->view('picsoo', $this->data);
		}
    }

	public function import_export()
	{
		//$msg = "<script type='text/javascript'>alert ('debug : import_export()');</script>";
		//echo $msg;

		//$this->session->set_flashdata('message', 'import_export() - '.$this->dbg);
		$this->dbg = "2";
		// check user permissions
		$this->sma->checkPermissions();
		$this->load->helper('security');

		// validate rules http://www.codeigniter.com/userguide3/libraries/form_validation.html#rulereference
		//$this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
		$this->form_validation->set_rules('start_date', lang('start_date'), 'required');
		$this->form_validation->set_rules('end_date', lang('end_date'), 'required');


		// Check validation
		if ($this->form_validation->run() == true) 
		{
			$var = $this->clientsid_list[$this->input->post('picsoo_company')];
			$this->ie_clientsid = strtok($var,' ');
			//$this->ie_clientsid = '10580'; // DEBUG
			//$this->session->set_flashdata('message', '$this->ie_clientsid');
			$this->ie_category = $this->input->post('category');
			$this->ie_startdate = $this->input->post('start_date');
			$this->ie_enddate = $this->input->post('end_date');
			$this->ie_reference = $this->input->post('reference_no');
			$this->ie_updexisting = $this->input->post('upd_existing');
			$this->ie_typetft = $this->input->post('typetft');
			$this->ie_datatft = $this->input->post('tft_data');
			//$this->ie_mytype = $this->input->post('mytype');
			//$this->ie_status = $this->input->post('user_status');
			//$this->ie_tft_articles = $this->input->post('tft_articles');
			//$this->ie_tft_clients = $this->input->post('tft_clients');
			//$this->ie_tft_fournisseurs = $this->input->post('tft_fournisseurs');
			//$this->ie_tft_transactions = $this->input->post('tft_transactions');

			//$this->picsoo_model->TestModel();
			if ($this->picsoo_ws->IsDemoVersion()) 
			{
				set_alert('warning', lang('disabled_in_demo'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//if( 1==0 ) //DEMO
			//{
			//    $this->session->set_flashdata('message', 'Fonction impossible en démo !');
			//}
			//else
			//{
			if ($this->ie_datatft == null )
			//if( $this->ie_tft_articles == null && $this->ie_tft_clients == null && $this->ie_tft_fournisseurs == null && $this->ie_tft_transactions == null)
			{
				set_alert('danger', 'Veuillez choisir au moins un élément à transférer.');
				redirect($_SERVER['HTTP_REFERER']);
			}

			$err = $this->ProcessDispatcher();

			if ( $this->ie_typetft == 'import' ) 
			{
				$msg = lang('import_finished');
				if ( $err != 0 )
				{
					$msg .= ' (' . lang('errors_detected') . ')';
					set_alert('warning', $msg);
				}
				else
				{
					set_alert('success', $msg);
				}
			}
			admin_redirect('picsoo/import_export');

			//echo( 'Good !');
		} else {
			// validation failed or model returned false
			$this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$bc     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('import_export_picsoo')]];
			$meta   = ['page_title' => lang('import_export_picsoo'), 'bc' => $bc];
			$this->page_construct('picsoo/import_export', $meta, $this->data);
		}
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessDispatcher()
	{
		$err = 0;

		if (file_exists("json_transactions_log.txt"))
			unlink("json_transactions_log.txt"); //DBG
		if (file_exists("result_transactions_log.txt"))
			unlink("result_transactions_log.txt"); //DBG

		// import
		if ( $this->ie_typetft == 'import' ) {
			if ($this->ie_datatft == '1')
				//if( $this->ie_tft_articles == '1' )
				$err = $this->ProcessImportItems();
			if ($this->ie_datatft == '2')
				//if( $this->ie_tft_clients == '1' )
				$err = $this->ProcessImportCustomers('C');
			if ($this->ie_datatft == '3')
				//if( $this->ie_tft_fournisseurs == '1' )
				$err = $this->ProcessImportCustomers('S');
			if ($this->ie_datatft == '4')
				//if( $this->ie_tft_fournisseurs == '1' )
				$err = $this->ProcessImportInvoices('S');
		} 
		else // export
		{
			if ($this->ie_datatft == '1')
				//if( $this->ie_tft_articles == '1' )
				$err = $this->ProcessExportItems();
			if ($this->ie_datatft == '2')
				//if( $this->ie_tft_clients == '1' )
				$err = $this->ProcessExportCustomers('C');
			if ($this->ie_datatft == '3')
				//if( $this->ie_tft_fournisseurs == '1' )
				$err = $this->ProcessExportCustomers('S');
			if ($this->ie_datatft == '4')
				//if( $this->ie_tft_transactions == '1' )
				$err = $this->ProcessExportTransactions();
			if ($this->ie_datatft == '5') {
				//if( $this->ie_tft_transactions == '1' )
				$this->toexcel = true;
				$err = $this->ProcessExportTransactions();
			}
		}

		return $err;
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessImportItems()
	{
		$err = 0;
		$nbcount = 0;

		$typearticle = 'standard';
		$category_code = $this->input->post('category');
		//$category_name = $this->products_model->getCategoryByCode($category_code);
		//$category_name = $this->site->getCategoryByID($category_code);

		$itemslist = $this->picsoo_ws->GetItemsList($this->ie_clientsid);
		if( count($itemslist) == 0 )
			return -1;

		// empty the picsoo items table
		//$savedbprefix = $this->db->dbprefix;
		//$this->db->dbprefix = '';
		//$this->db->empty_table('picsoo_items');
		//$this->db->dbprefix = $savedbprefix;
		// empty the items table
		//$this->db->empty_table('items');
		//return $err;
		
		//$itemscount = $this->picsoo_ws->GetRowsCount( "Item", "fk_client_id=" . $this->ie_clientsid );
		/*if( count( $itemslist ) > 150 ) // si trop, on fait en background
		{
			$this->ProcessImportItemsBackground( $itemslist );
			set_alert('warning', "Trop d'articles à importer ! le process va s'exécuter en backgroud, veuillez patienter ...");
			redirect(admin_url('picsoo/'));            
			return $err;
		}*/

		//$titles  = array_shift($arrResult);
		$updated = 0;
		$items   = [];

		foreach ($itemslist as $picsooitem) 
		{
			if ( $picsooitem['is_deleted'] == true )
				continue;
			
			//if( $nbcount == 200 )
			//	break;

			//if( $picsooitem['item_name'] != '701998')
			//	continue;
			//if( $picsooitem['item_name'] != 'TESTDOMI')
			//	continue;

			//$supplier_name = $picsooitem['Fksupplierid']; //OK!
			//$supplier_name = $picsooitem['suppliercode']; // à voir lequel des 2

			//$supplier      = $supplier_name ? $this->products_model->getSupplierByName($supplier_name) : false; // OK!
			//$category_name = $this->picsoo_ws->GetCategoryName($this->ie_clientsid, '1891');
			//$category_name = $picsooitem['name']; // OK!
			//$category_id = $picsooitem['fk_category_id'];

			// si pas de catégorie dans Picsoo, on prend le défaut choisi par l'utilisateur
			/*if ( $category_name == '' ) 
			{
				$var = $this->picsoo_model->getCategoryById( $this->ie_category);
				$category_name = $var->name;
				$category_id = $var->id;
			}*/ // OK!

			$taxid = $this->picsoo_model->GetTaxeId($picsooitem['salesvatcode1']);
			$catid = $this->picsoo_model->GetCategoryId($picsooitem['fk_category_id']);

			$item = [
				'picsoo_item_id'		=> isset($picsooitem['item_id']) ? trim($picsooitem['item_id']) : '',
                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
				'description'			=> isset($picsooitem['item_name']) ? trim($picsooitem['item_name']) : '',
				'long_description'  	=> trim($picsooitem['FRItemDescription']) . '<br>' . trim($picsooitem['NLItemDescription']) .'<br>' . trim($picsooitem['item_description']) ,
				'rate'              	=> isset($picsooitem['sale_unit_price']) ? trim($picsooitem['sale_unit_price']) : '',
				'tax'					=> $taxid,
				'tax2'					=> '',
				'unit'					=> isset($picsooitem['unit']) ? trim($picsooitem['unit']) : '1',
				'group_id'				=> $catid,
				'rate_currency_2'		=> 0.00,
			];

			//$dbg = $item['description'];
			// C:\xampp\htdocs\sequoia\project\application\controllers\admin\Invoice_items.php
			$itemid = $this->picsoo_model->IsItemExists( $picsooitem );
			if ( $itemid == 0 ) // article n'existe pas' 
			{
                //$id      = $this->invoice_items_model->add($item);
		        $this->db->insert(db_prefix() . 'items', $item);
		        $id = $this->db->insert_id();
			} 
			else // update
			{
				$this->db->where('id', $itemid);
        		$this->db->update(db_prefix() . 'items', $item);
        		//$ra = $this->db->affected_rows();
				//$success = $this->invoice_items_model->edit($item);
				//$dbg = $this->db->last_query(); //DH DBG
			}
		$nbcount++;
		} // foreach ($itemslist as $picsooitem)

		return $err;
	}

	private function ProcessImportItemsBackground( $itemslist )
	{	
		$recordscnt = count ( $itemslist ); 
		// Remove the last x elements from the array
		//array_splice($itemslist, -($cnt-1)); // pour debug, on ne garde que x records
		//$cnt = count ( $itemslist ); 

		$savedbprefix = $this->db->dbprefix;
		$this->db->dbprefix = '';

		$this->db->empty_table('picsoo_items');

		foreach ($itemslist as $picsooitem) 
		{
			$taxid = $this->picsoo_model->GetTaxeId($picsooitem['salesvatcode1']);
			$catid = $this->picsoo_model->GetCategoryId($picsooitem['fk_category_id']);

			$item = [
				//'id'					=> '';
				'picsoo_item_id'		=> isset($picsooitem['item_id']) ? trim($picsooitem['item_id']) : '',
                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
				'description'			=> isset($picsooitem['item_name']) ? trim($picsooitem['item_name']) : '',
				'long_description'  	=> trim($picsooitem['FRItemDescription']) . '<br>' . trim($picsooitem['NLItemDescription']) .'<br>' . trim($picsooitem['item_description']) ,
				'rate'              	=> isset($picsooitem['sale_unit_price']) ? trim($picsooitem['sale_unit_price']) : '',
				'tax'					=> $taxid,
				'tax2'					=> '',
				'unit'					=> isset($picsooitem['unit']) ? trim($picsooitem['unit']) : '1',
				'group_id'				=> $catid,
				'rate_currency_2'		=> 0.00,
			];

			$insert_id = $this->db->insert(/*db_prefix() .*/ 'picsoo_items', $item );
			$msg = $this->db->last_query();

			if ($this->db->affected_rows() > 0) 
			{
	    		// Insert succeeded
	    		$msg = "Insert successful!";
			}
			else
			{
	    		// Insert failed
	    		$msg = "Insert failed!";
			}
		} // foreach ($itemslist as $picsooitem)

		$this->db->dbprefix = $savedbprefix;

		$StartKey = 0;
		$increment = 150;
		$run = 0;

		while ($StartKey <= $recordscnt) 
		{
    		$EndKey = $StartKey + $increment;
    		if ($EndKey > $recordscnt) 
    		{
        		$EndKey = $recordscnt;
    		}

    		//echo "$start to $end<br>";
			$back_url = 'http://www.picsoocloud.com/sequoia/modules/picsoo/async/items_back.php?StartKey=' . $StartKey . '&EndKey=' . $EndKey . '&filename=data' . $run . '.txt';
			$this->curlPostAsync($back_url, array('var' => 'content'));

    		$StartKey = $EndKey + 1;
    		$run++;
		}
	}

	private function curlPostAsync($url, $data) 
	{
		$post_string = json_encode($data);

		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json' . "\r\n" .
							'Content-Length: ' . strlen($post_string) . "\r\n",
				'content' => $post_string
			)
		);

		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		if ($result === false) 
		{
			$error = error_get_last();
			//echo "Failed to send HTTP request<br>\n";
			return false;
		} 
		else 
		{
			//echo "HTTP request sent successfully<br>\n";
			return true;
		}
	}

	public function execute_external_php( $back_url )
    {
        $phpFilePath = $back_url; //'/path/to/your/external/file.php';
        $command = /*'php ' .*/ $phpFilePath;

        // Execute the command
        exec($command, $output, $returnStatus);

        // Check the return status
        if ($returnStatus !== 0) 
		{
            // An error occurred
            // Handle the error or display a message
            echo 'Error executing PHP file: ' . $phpFilePath;
        } 
		else 
		{
            // The PHP file executed successfully
            // Process the output or do something else
            var_dump($output);
        }
    }

	function url_get_contents ($Url) 
	{
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $Url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}
	
	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessImportCustomers( $customertype = '' )
	{
		$err = 0;
		$nbcount = 0;
		$fndbg = "elapsed.txt";

		//$this->db->empty_table('tblclients');

		$customerslist = $this->picsoo_ws->GetCustomersList( $this->ie_clientsid, "" );
		if( count($customerslist) == 0 )
			return -1;

		//$clientscount = $this->picsoo_ws->GetRowsCount( "Item", "fk_client_id=" . $this->ie_clientsid );
		/*if( count( $customerslist ) > 150 ) // si trop, on fait en background
		{
			$this->ProcessImportCutomersBackground( $customerslist );
			set_alert('warning', "Trop de client à importer ! le process va s'exécuter en backgroud, veuillez patienter ...");
			redirect(admin_url('picsoo/'));            
			return $err;
		}*/

		$updated 		= 0;
		$rw      		= 1;
		$items   		= [];

		$customer_group = ''; //$this->site->getCustomerGroupByID($this->Settings->customer_group);
		$price_group    = ''; //$this->site->getPriceGroupByID($this->Settings->price_group);

		$status = set_time_limit(0); 
		
		foreach ($customerslist as $picsoocustomer)
		{
			if ( $picsoocustomer['is_deleted'] == true )
			{
				continue;
			} 
			
			//if( $nbcount == 50 )
			//	break;

			//if( $picsoocustomer['customers_id'] != 152795 && $picsoocustomer['customers_id'] != 154320 )
			//if( $picsoocustomer['Customercode'] != '400HUGODOMINIQUE' )
			//{
			//	continue;
			//}
			
			//if( $picsoocustomer['customer_company_name'] == "LAURENT Benoît (Ultimat'Home)" )
			//	$dbg = 'rrr';
			
			$adresses = $picsoocustomer['adresses']; //$this->picsoo_ws->GetCustomerAddress($this->ie_clientsid, $picsoocustomer['customers_id']);
			$address = $adresses[0];
			$belgiuminfos = $picsoocustomer['belgiuminfo']; //$this->picsoo_ws->GetCustomerBelgiumInformation($this->ie_clientsid, $picsoocustomer['customers_id']);
			$belgiuminfo = $belgiuminfos[0];
			$contactslist = $picsoocustomer['contactslist']; //$this->picsoo_ws->GetCustomerContactsList($this->ie_clientsid, $picsoocustomer['customers_id']);

			if ( $picsoocustomer['EnumCustomerTypeID'] == '2' )
			{
				$nbcount++;
				continue;
			}

			$companyname = isset($picsoocustomer['customer_company_name']) ? trim($this->picsoo_ws->RemoveSpecialChar($picsoocustomer['customer_company_name'])) : '';

            $customer = [
            	//'firstname'				=> '',
            	//'lastname'				=> '',
                //'email'               	=> isset($picsoocustomer['email_address']) ? trim($picsoocustomer['email_address']) : '',
                'picsoo_customers_id' 	=> isset($picsoocustomer['customers_id']) ? trim($picsoocustomer['customers_id']) : '',
                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
                'phonenumber'			=> isset($picsoocustomer['primary_contact']) ? trim($picsoocustomer['primary_contact']) : '',
                //'title'				=> '',
                'company'             	=> $companyname,
                'picsoo_Customercode'  	=> isset($picsoocustomer['Customercode']) ? trim($picsoocustomer['Customercode']) : '',
                'vat' 	             	=> isset($belgiuminfo['VATNumber']) ? trim($belgiuminfo['VATNumber']) : '',
                'country'             	=> 0, //?? isset($adresses['country']) ? trim($adresses['country']) : '',
                'city'                	=> isset($address['ccity']) ? trim($address['ccity']) : '',
                'zip'		         	=> isset($address['cpostcode']) ? trim($address['cpostcode']) : '',
                'state'               	=> isset($address['county']) ? trim($address['county']) : '',
                'address'             	=> isset($address['address_1']) ? trim($address['address_1']) : '',
				'website'               => '',
				'billing_street'		=> '',
				'billing_city'			=> '',
				'billing_state'			=> '',
				'billing_zip'			=> '',
				'billing_country'		=> '',
				'shipping_street'		=> '',
				'shipping_city'			=> '',
				'shipping_state'		=> '',
				'shipping_zip'			=> '',
				'shipping_country'		=> '',
				'longitude'				=> '',
				'stripe_id'				=> '',
				'active'				=> '1',
				'registration_confirmed'=> '1',
				'country'				=> '22',
				'addedfrom'				=> '0', // 0 = n'existe pas = from picsoo
            ];
            $var1 = $customer['vat'];

			if ( empty($customer['address']))
				$customer['address'] = "NOT SET";
			if ( empty($customer['city']))
				$customer['city'] = "NOT SET";
			if ( empty($customer['zip']))
				$customer['zip'] = "NOT SET";

			$picsoovat = $this->picsoo_ws->ExtractNumberFromString( trim($belgiuminfo['VATNumber']) );

			$clientid = $this->picsoo_model->IsCustomerExists( $picsoocustomer, $picsoovat ); // test dans la base Sequoia

			/* par le model
	        if ( $clientid == 0 ) // nouveau client, on ajoute
	        {
		        $clientid = $this->clients_model->add($customer);
		        $this->db->insert(db_prefix() . 'clients', $data);
		        $clientid = $this->db->insert_id();
	    	} 
	    	else // le client existe, on l'update
	    	{
		        $success = $this->clients_model->update( $customer , $clientid );
			} // if( $clientid == 0 )
			*/

			// access direct à la base
			if ( $clientid == 0 ) // client n'existe pas' 
			{
				$customer['datecreated'] = date('Y-m-d H:i:s');
                //$id      = $this->invoice_items_model->add($item);
		        $this->db->insert(db_prefix() . 'clients', $customer);
		        $clientid = $this->db->insert_id();
				if( $clientid == 0 )
					continue; // in case of ....
			} 
			else // update
			{
				$this->db->where('userid', $clientid);
        		$this->db->update(db_prefix() . 'clients', $customer);
        		//$ra = $this->db->affected_rows();
				//$success = $this->invoice_items_model->edit($item);
				//$dbg = $this->db->last_query(); //DH DBG
			}

			// les contacts --------------------------------------------------------------------------------------------------------
			
			if( empty( $contactslist ) )
			{
				$nbcount++;
				continue;
			}
			
			foreach ($contactslist as $index => $picsoocontact)
			{
				//if( empty( $picsoocontact['Email'] ) ) // pas d'email, pas de clé ...
				//	continue;
				
				$title = '';
				/*switch ( trim($picsoocontact['EnumTitleId'] ))
				{
					case '3':
						$title = '';
						break;
				}*/

				$contact_data = [
					'userid'				=> isset($clientid) ? trim($clientid) : '',
					'picsoo_Id'				=> isset($picsoocontact['Id']) ? trim($picsoocontact['Id']) : '',
					'picsoo_ParentId'		=> isset($picsoocontact['ParentId']) ? trim($picsoocontact['ParentId']) : '',
	                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
					'firstname'				=> isset($picsoocontact['FirstName']) ? trim($picsoocontact['FirstName']) : '',
					'lastname'           	=> isset($picsoocontact['LastName']) ? trim($picsoocontact['LastName']) : '',
					'title'              	=> $title,
					'email'              	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'phonenumber'        	=> isset($picsoocontact['ContactNumber']) ? trim($picsoocontact['ContactNumber']) : '',
					'direction'          	=> '',
					'invoice_emails'     	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'credit_note_emails' 	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'estimate_emails'    	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'ticket_emails'      	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'contract_emails'    	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'project_emails'     	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'task_emails'        	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'active'				=> '1',
					'is_primary'			=> ( $index==0 ) ? '1' : '0',
				];

				$contactid = $this->picsoo_model->IsContactExists( $picsoocontact );

				/* par le model
				if ( $contactid == 0 ) // le contact n'existait pas, on le crée
				{
					$success = $this->clients_model->add_contact($contact_data, $clientid, true);
					if ($success == true)
					{
						//    set_alert('success', _l('updated_successfully', _l('client')));
					}
					//redirect(admin_url('clients/client/' . $id));
				}
				else // le contact existait, on l'update
				{
					$success = $this->clients_model->update_contact($contact_data, $contactid, true);

					if ($success == true)
					{
						//set_alert('success', _l('clients_contact_updated'));
					}
					else
					{
						//set_alert('success', _l('clients_contact_updated'));
					}
				}*/
				// access direct à la base
				if ( $contactid == 0 ) // contact n'existe pas' 
				{
					$contact_data['datecreated'] = date('Y-m-d H:i:s');
	                //$id      = $this->invoice_items_model->add($item);
			        $this->db->insert(db_prefix() . 'contacts', $contact_data);
			        $id = $this->db->insert_id();
				} 
				else // update
				{
					$this->db->where('id', $contactid);
	        		$this->db->update(db_prefix() . 'contacts', $contact_data);
	        		//$ra = $this->db->affected_rows();
					//$success = $this->invoice_items_model->edit($item);
					//$dbg = $this->db->last_query(); //DH DBG
				}
			} // foreach ($contactslist as $picsoocontact)

		//fwrite($myfiledbg, '2> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);
		$nbcount++;
		} // foreach ($customerslist as $picsoocustomer)

		return $err;
	}

	private function ProcessImportCustomersBackground( $customerslist )
	{	
		$recordscnt = count ( $customerslist ); 
		// Remove the last x elements from the array
		//array_splice($customerslist, -($cnt-1)); // pour debug, on ne garde que x records
		//$cnt = count ( $customerslist ); 

		$savedbprefix = $this->db->dbprefix;
		$this->db->dbprefix = '';

		$this->db->empty_table('picsoo_items');

		foreach ($customerslist as $picsoocustomer) 
		{
			$taxid = $this->picsoo_model->GetTaxeId($picsooitem['salesvatcode1']);
			$catid = $this->picsoo_model->GetCategoryId($picsooitem['fk_category_id']);

			$item = [
				//'id'					=> '';
				'picsoo_item_id'		=> isset($picsooitem['item_id']) ? trim($picsooitem['item_id']) : '',
                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
				'description'			=> isset($picsooitem['item_name']) ? trim($picsooitem['item_name']) : '',
				'long_description'  	=> trim($picsooitem['FRItemDescription']) . '<br>' . trim($picsooitem['NLItemDescription']) .'<br>' . trim($picsooitem['item_description']) ,
				'rate'              	=> isset($picsooitem['sale_unit_price']) ? trim($picsooitem['sale_unit_price']) : '',
				'tax'					=> $taxid,
				'tax2'					=> '',
				'unit'					=> isset($picsooitem['unit']) ? trim($picsooitem['unit']) : '1',
				'group_id'				=> $catid,
				'rate_currency_2'		=> 0.00,
			];

			$insert_id = $this->db->insert(/*db_prefix() .*/ 'picsoo_items', $item );
			$msg = $this->db->last_query();

			if ($this->db->affected_rows() > 0) 
			{
	    		// Insert succeeded
	    		$msg = "Insert successful!";
			}
			else
			{
	    		// Insert failed
	    		$msg = "Insert failed!";
			}
		} // foreach ($itemslist as $picsooitem)

		$this->db->dbprefix = $savedbprefix;

		$StartKey = 0;
		$increment = 150;
		$run = 0;

		while ($StartKey <= $recordscnt) 
		{
    		$EndKey = $StartKey + $increment;
    		if ($EndKey > $recordscnt) 
    		{
        		$EndKey = $recordscnt;
    		}

    		//echo "$start to $end<br>";
			$back_url = 'http://www.picsoocloud.com/sequoia/modules/picsoo/async/items_back.php?StartKey=' . $StartKey . '&EndKey=' . $EndKey . '&filename=data' . $run . '.txt';
			$this->curlPostAsync($back_url, array('var' => 'content'));

    		$StartKey = $EndKey + 1;
    		$run++;
		}
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessImportCustomersOLD( $customertype = '' )
	{
		$err = 0;
		$fndbg = "elapsed.txt";

		$customerslist = $this->picsoo_ws->GetCustomersList($this->ie_clientsid);
		
		/*$filename = "customerslist.txt";
		if(file_exists($filename))
        	$status  = unlink($filename) ? 'The file '.$filename.' has been deleted' : 'Error deleting '.$filename;
		$myfile = fopen($filename, "w") or die("Unable to open file!");
		foreach ($customerslist as $key => $value )
		{
			$var1 = $key;
			$var2 = $value;
			fwrite($myfile, $value['customers_id']." [".$value['customer_company_name']."] ".$value['Customercode'] . PHP_EOL);
		}
		fwrite($myfile, date('m/d/Y h:i:s a', time()) . PHP_EOL);
		fclose($myfile);*/

		//$titles  = array_shift($arrResult);
		$updated 		= 0;
		$rw      		= 1;
		$items   		= [];
		$index 			= 0;

		$customer_group = ''; //$this->site->getCustomerGroupByID($this->Settings->customer_group);
		$price_group    = ''; //$this->site->getPriceGroupByID($this->Settings->price_group);

		$status = set_time_limit(0); 
		
		/*if(file_exists($fndbg))
        	$status  = unlink($fndbg) ? 'The file '.$fndbg.' has been deleted' : 'Error deleting '.$fndbg;
		$myfiledbg = fopen($fndbg, "w") or die("Unable to open file!");

		fwrite($myfiledbg, 'A> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);*/

		foreach ($customerslist as $picsoocustomer)
		{
			if ( $picsoocustomer['is_deleted'] == true )
			{
				$index++;
				continue;
			} 

		//fwrite($myfiledbg, '1> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);
			
			if( $index == 500 )
				break;
			
			/*if( $picsoocustomer['Customercode'] != '400EnoliaSRL' )
			{
				$index++;
				continue;
			}*/
			
			//if( $picsoocustomer['customer_company_name'] == "LAURENT Benoît (Ultimat'Home)" )
			//	$dbg = 'rrr';

			//$dbg1 = $picsoocustomer['Customercode'];
			//$dbg2 = $picsoocustomer['customer_company_name'];
			//$dbg3 = $picsoocustomer['customers_id'];

			//log_message('debug', $dbg1 . ' - ' . $dbg2 . ' - ' . $dbg3 . ' - ' . $index ); 
			
		//fwrite($myfiledbg, date('m/d/Y h:i:s a', time()) . PHP_EOL);
			$adresses = $this->picsoo_ws->GetCustomerAddress($this->ie_clientsid, $picsoocustomer['customers_id']);
			$belgiuminfo = $this->picsoo_ws->GetCustomerBelgiumInformation($this->ie_clientsid, $picsoocustomer['customers_id']);
			$contactslist = $this->picsoo_ws->GetCustomerContactsList($this->ie_clientsid, $picsoocustomer['customers_id']);
		//fwrite($myfiledbg, date('m/d/Y h:i:s a', time()) . PHP_EOL . PHP_EOL);
			
			//if( empty($picsoocustomer['email_address']) )
			//if ( empty($picsoocustomer['Customercode']) /*&& empty($belgiuminfo['VATNumber'])*/ || empty($picsoocustomer['customer_company_name']) ) // on ne sait jamais !
			//{
			//  $index++;
			//	continue;
			//}

			if ( $picsoocustomer['EnumCustomerTypeID'] == '2' )
			{
				$index++;
				continue;
			}
			
			//if( !trim($belgiuminfo['VATNumber']))
			//{
			//	continue;
			//}			

			//if( $picsoocustomer['customer_company_name'] != 'BELGOMEX' )
			//{
			//	$index++;
			//  continue;
			//}

			//$myfile = fopen("customers_log.txt", "w") or die("Unable to open file!");
			//$results = print_r($picsoocustomer, true); // $results now contains output from print_r
			//fwrite($myfile, $results);
			//fclose($myfile);

			//if( $picsoocustomer['customer_company_name']=='DOMI') // DBG
			//	$dbg = 1;

			/*$cc = '';
			if ( isset($picsoocustomer['Customercode']) )
				$cc = trim($picsoocustomer['Customercode']);
			else 
			{
				if ( $customertype == 'C' )
					$cc = "400000";
				else
					$cc = "440000";
			}*/

			/*$title = '';
			switch ( trim($picsoocustomer['EnumTitleId'] )) 
			{
				case '3':
					$title = '';
					break;
			}*/

			$companyname = isset($picsoocustomer['customer_company_name']) ? trim($this->picsoo_ws->RemoveSpecialChar($picsoocustomer['customer_company_name'])) : '';

            $customer = [
            	//'firstname'				=> '',
            	//'lastname'				=> '',
                //'email'               	=> isset($picsoocustomer['email_address']) ? trim($picsoocustomer['email_address']) : '',
                'picsoo_customers_id' 	=> isset($picsoocustomer['customers_id']) ? trim($picsoocustomer['customers_id']) : '',
                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
                'phonenumber'			=> isset($picsoocustomer['primary_contact']) ? trim($picsoocustomer['primary_contact']) : '',
                //'title'				=> '',
                'company'             	=> $companyname,
                'picsoo_Customercode'  	=> isset($picsoocustomer['Customercode']) ? trim($picsoocustomer['Customercode']) : '',
                'vat' 	             	=> isset($belgiuminfo['VATNumber']) ? trim($belgiuminfo['VATNumber']) : '',
                'country'             	=> 0, //?? isset($adresses['country']) ? trim($adresses['country']) : '',
                'city'                	=> isset($adresses['ccity']) ? trim($adresses['ccity']) : '',
                'zip'		         	=> isset($adresses['cpostcode']) ? trim($adresses['cpostcode']) : '',
                'state'               	=> isset($adresses['county']) ? trim($adresses['county']) : '',
                'address'             	=> isset($adresses['address_1']) ? trim($adresses['address_1']) : '',
				'website'               => '',
				'billing_street'		=> '',
				'billing_city'			=> '',
				'billing_state'			=> '',
				'billing_zip'			=> '',
				'billing_country'		=> '',
				'shipping_street'		=> '',
				'shipping_city'			=> '',
				'shipping_state'		=> '',
				'shipping_zip'			=> '',
				'shipping_country'		=> '',
				'longitude'				=> '',
				'stripe_id'				=> '',
            ];
            $var1 = $customer['vat'];

			//if ( empty($customer['firstname']))
			//	$customer['firstname'] = "NOT SET";
			//if ( empty($customer['lastname']))
			//	$customer['lastname'] = "NOT SET";
			//if ( empty($customer['email']))
			//	$customer['email'] = "not_set@company.com";
			if ( empty($customer['address']))
				$customer['address'] = "NOT SET";
			if ( empty($customer['city']))
				$customer['city'] = "NOT SET";
			if ( empty($customer['zip']))
				$customer['zip'] = "NOT SET";

			$picsoovat = $this->picsoo_ws->ExtractNumberFromString( trim($belgiuminfo['VATNumber']) );

			$clientid = $this->picsoo_model->IsCustomerExists( $picsoocustomer, $picsoovat );

	        if ( $clientid == 0 ) // nouveau client, on ajoute
	        {
		        //if (!has_permission('customers', '', 'create')) 
		        //{
	    	    //    access_denied('customers');
	        	//}

		        $clientid = $this->clients_model->add($customer);
	        	if ($clientid) 
	        	{
		            //set_alert('success', _l('added_successfully', _l('client')));
	    	        //if ($save_and_add_contact == false) 
	    	        //{
		            //    redirect(admin_url('clients/client/' . $id));
	    	        //} 
	    	        //else 
	    	        //{
		            //    redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));
	            	//}
	        	}
	    	} 
	    	else // le client existe, on l'update
	    	{
		        //if (!has_permission('customers', '', 'create')) 
		        //{
	    	    //    access_denied('customers');
	        	//}

		        $success = $this->clients_model->update( $customer , $clientid );
			} // if( $clientid == 0 )
			
			// les contacts --------------------------------------------------------------------------------------------------------
			if( empty( $contactslist ) )
			{
				$index++;
		//fwrite($myfiledbg, '3> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);
				continue;
			}
			
			foreach ($contactslist as $picsoocontact)
			{
				//if( empty( $picsoocontact['Email'] ) ) // pas d'email, pas de clé ...
				//	continue;
				
				$title = '';
				/*switch ( trim($picsoocontact['EnumTitleId'] ))
				{
					case '3':
						$title = '';
						break;
				}*/

				$contact_data = [
					'picsoo_Id'				=> isset($picsoocontact['Id']) ? trim($picsoocontact['Id']) : '',
					'picsoo_ParentId'		=> isset($picsoocontact['ParentId']) ? trim($picsoocontact['ParentId']) : '',
	                'picsoo_fk_clients_id' 	=> $this->ie_clientsid,
					'firstname'				=> isset($picsoocontact['FirstName']) ? trim($picsoocontact['FirstName']) : '',
					'lastname'           	=> isset($picsoocontact['LastName']) ? trim($picsoocontact['LastName']) : '',
					'title'              	=> $title,
					'email'              	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'phonenumber'        	=> isset($picsoocontact['ContactNumber']) ? trim($picsoocontact['ContactNumber']) : '',
					'direction'          	=> '',
					'invoice_emails'     	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'credit_note_emails' 	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'estimate_emails'    	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'ticket_emails'      	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'contract_emails'    	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'project_emails'     	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'task_emails'        	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
					'custom_fields'      	=> isset($picsoocontact['Email']) ? trim($picsoocontact['Email']) : '',
				];

				$contactid = $this->picsoo_model->IsContactExists( $picsoocontact );

				if ( $contactid == 0 ) // le contact n'existait pas, on le crée
				{
					$success = $this->clients_model->add_contact($contact_data, $clientid, true);
					if ($success == true)
					{
						//    set_alert('success', _l('updated_successfully', _l('client')));
					}
					//redirect(admin_url('clients/client/' . $id));
				}
				else // le contact existait, on l'update
				{
					$success = $this->clients_model->update_contact($contact_data, $contactid, true);

					if ($success == true)
					{
						//set_alert('success', _l('clients_contact_updated'));
					}
					else
					{
						//set_alert('success', _l('clients_contact_updated'));
					}
				}
			} // foreach ($contactslist as $picsoocontact)

			$index++;
		//fwrite($myfiledbg, '2> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);
		} // foreach ($customerslist as $picsoocustomer)

		//fwrite($myfiledbg, 'B> ' . date('m/d/Y h:i:s a', time()) . PHP_EOL);

		/*$myfile = fopen($filename, "a") or die("Unable to open file!");
		fwrite($myfile, date('m/d/Y h:i:s a', time()) . PHP_EOL);
		fwrite($myfile, "Process ended." . PHP_EOL);
		fclose($myfile);*/

		//fclose($myfiledbg);

		return $err;
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessImportInvoices()
	{
		$err = 0;
		$ccount = 0;
		$ErrNumber = 0;
		$PassNumber = 1;
		$myList = array();
		$arrLength = count($myList);
		$nbcompanies = 0;

		$logtype = 'info';
		
		$this->journallist = $this->picsoo_model->GetJournalList( $this->ie_clientsid, 1 ); // JournalType = 1 = Ventes
		
		// Convert the input date string to a DateTime object
		$date = DateTime::createFromFormat("d/m/Y" /*"d/m/Y H:i:s"*/, $this->ie_startdate);
		// Format the date as "day - month name on 3 characters - year"
		$fmtStartDate = $date->format('d-M-Y');
		$date = DateTime::createFromFormat("d/m/Y" /*"d/m/Y H:i:s"*/, $this->ie_enddate);
		// Format the date as "day - month name on 3 characters - year"
		$fmtEndDate = $date->format('d-M-Y');

		$invoiceslist = $this->picsoo_ws->GetInvoicesList( $this->ie_clientsid, $fmtStartDate, $fmtEndDate );

		if( $invoiceslist == null )
			return;
		if( count($invoiceslist) == 0 )
			return;

		// -------------------------------------------------------------------------------------------------------------------
		// boucle sur les invoices
		// -------------------------------------------------------------------------------------------------------------------
		//$this->picsoo_model->DeleteInvoicesFromPicsoo( $seqcompany['userid'] );

		foreach ($invoiceslist as $picsooinvoice) 
		{
			//$dt = $this->picsoo_model->PicsooDate2Sequoia($picsooinvoice['invoice_date']);
			//if( $dt < $this->ie_startdate || $this->ie_enddate )
			//	continue;

			if( $picsooinvoice['sub_total'] == 0 )
				continue;

			if( !$this->KeepInvoice($picsooinvoice['InvoiceJournalCode']))
				continue;
			
			if( $picsooinvoice['invoice_id']=='418297')
			{
				$dbg = 1;				
			}
			
	        $invoice_data = [];
            $invoice_data['picsoo_due_amount']			= $picsooinvoice['due_amount'];
	        $invoice_data['clientid']   				= $this->picsoo_model->GetSequoiaClientID($picsooinvoice['fk_customer_id']);
	        $invoice_data['picsoo_invoice_id']   		= $picsooinvoice['invoice_id'];
	        $invoice_data['picsoo_fk_customer_id']   	= $picsooinvoice['fk_customer_id'];
            $invoice_data['picsoo_fk_clients_id']		= $this->ie_clientsid;
	        $invoice_data['picsoo_invoice_number']   	= $picsooinvoice['invoice_number'];
	        $invoice_data['project_id'] 				= 0;
	        $invoice_data['number']     				= get_option('next_invoice_number');
	        $invoice_data['date']       				= $this->picsoo_model->PicsooDate2Sequoia($picsooinvoice['invoice_date']);
	        $invoice_data['duedate']    				= $this->picsoo_model->PicsooDate2Sequoia($picsooinvoice['invoice_due_date']);

	        $invoice_data['show_quantity_as'] 			= '1'; // ??
	        $invoice_data['currency']         			= '2'; // ??
	        $invoice_data['subtotal']         			= $picsooinvoice['net'];
	        $invoice_data['total_tax']         			= $picsooinvoice['vat_rate'];
	        $invoice_data['total']            			= $picsooinvoice['sub_total'];
	        $invoice_data['adjustment']       			= '0.00';
	        $invoice_data['discount_percent'] 			= $picsooinvoice['invoicediscount_percent'];
	        $invoice_data['discount_total']   			= $picsooinvoice['invoicediscount_amount'];
	        $invoice_data['discount_type']    			= ''; // ??
	        $invoice_data['sale_agent']       			= '0'; // ??
	        // Since version 1.0.6
	        $invoice_data['billing_street']   			= ''; //$seqcompany['billing_street'];
	        $invoice_data['billing_city']     			= ''; //$seqcompany['billing_city'];
	        $invoice_data['billing_state']    			= ''; //$seqcompany['billing_state'];
	        $invoice_data['billing_zip']      			= ''; //$seqcompany['billing_zip'];
	        $invoice_data['billing_country']  			= ''; //$seqcompany['billing_country'];
	        $invoice_data['shipping_street']  			= ''; //$seqcompany['shipping_street'];
	        $invoice_data['shipping_city']    			= ''; //$seqcompany['shipping_city'];
	        $invoice_data['shipping_state']   			= ''; //$seqcompany['shipping_state'];
	        $invoice_data['shipping_zip']     			= ''; //$seqcompany['shipping_zip'];
	        $invoice_data['shipping_country'] 			= ''; //$seqcompany['shipping_country'];
            $invoice_data['include_shipping'] 			= 0; // ??
	        $invoice_data['show_shipping_on_invoice']	= 1; // ??
	        $invoice_data['terms']                   	= get_option('predefined_terms_invoice');
	        //$invoice_data['clientnote']              	= get_option('predefined_clientnote_invoice');
	        //$invoice_data['clientnote']              	= $seqcompany['invoice_quotes'];
	        //$invoice_data['clientnote']              	= $seqcompany['FreeDescription'];
	        //$invoice_data['clientnote']              	= $seqcompany['VoucherNote'];
	        $invoice_data['clientnote']              	= $picsooinvoice['invoice_quotes'] . ' ' . $picsooinvoice['FreeDescription'] . '<br>' . $picsooinvoice['VoucherNote'];
	        $invoice_data['adminnote'] 					= '';
	        // Set to unpaid status automatically
	        $invoice_data['status']    					= 1;

	        $modes = $this->payment_modes_model->get('', [
	            'expenses_only !=' => 1,
	        ]);
	        $temp_modes = [];
	        foreach ($modes as $mode) {
	            if ($mode['selected_by_default'] == 0) {
	                continue;
	            }
	            $temp_modes[] = $mode['id'];
	        }
	        $invoice_data['allowed_payment_modes'] 		= $temp_modes;
	        $invoice_data['newitems']              		= [];
	        $custom_fields_items                   		= get_custom_fields('items');
	        $key                                   		= 1;

			// on teste si le picsoo_invoice_id est déjà dans la base; si oui, update; si non, nouveau
			$invoiceid = $this->picsoo_model->IsInvoiceExists( $picsooinvoice['invoice_id'] );
			if( $invoiceid == 0 )
			{
		        $invoiceid = $this->picsoo_model->add_invoice($invoice_data);
		        if ($invoiceid) 
				{
	            }
			}
			else // update
			{
	            unset($invoice_data['save_as_draft']);
		        //if (!has_permission('customers', '', 'create')) 
		        //{
	    	    //    access_denied('customers');
	        	//}

		        $success = $this->picsoo_model->update_invoice( $invoice_data, $invoiceid );
			}

			// -------------------------------------------------------------------------------------------------------------------
			// les paiements
			// -------------------------------------------------------------------------------------------------------------------
			//if( $invoice_data['picsoo_due_amount'] == 0 || $invoice_data['picsoo_due_amount'] < $invoice_data['total'] )	// si le due = 0 ou < total, il y a eu paiement même partiel			
			{
			/* pas pour le moment ...
				$payementslist = $this->picsoo_ws->GetInvoiceBillDetails( $this->ie_clientsid, $invoice_data['picsoo_invoice_id']);
				if( $payementslist == null )
					continue;
				if( count($payementslist) == 0 )
					continue;
				
				$this->picsoo_model->DeletePaiementsFromPicsoo( $invoice_data['picsoo_invoice_id'] );
				
				foreach ($payementslist as $payement) // boucle sur les paiements
				{
					$payment_data = [];
                    $payment_data['paymentmode']   				= $payement['Method_Name'] //'Entity'
                	$payment_data['amount']        				= $payement['Amount'];
    	            $payment_data['invoiceid']     				= $invoiceid;
    	            $payment_data['picsoo_invoice_id']    		= $picsooinvoice['invoice_id'];
    	            $payment_data['picsoo_BankAccountsId']		= $payement['BankAccountsId'];
    	            $payment_data['picsoo_fk_clients_id']		= $this->ie_clientsid;
    	            //$payment_data['do_not_send_email_template']	= 1; // sinon, le client va recevoir un email à chaque import
    	            //$payment_data['do_not_send_email_template']	= 1; // sinon, le client va recevoir un email à chaque import
        	        //$payment_data['transactionid'] = $invoice->charge;

					$id = $this->picsoo_model->add_payement( $payment_data );
				} // foreach ($payementslist as $payement)
				*/
			} // if( $invoice_data['picsoo_due_amount'] == 0 || $invoice_data['picsoo_due_amount'] < $invoice_data['total'] )
		} // foreach ($invoiceslist as $picsooinvoice)

		return $err;
	}
	
	private function KeepInvoice( $journalcode )
	{
		//if( $journalcode == 'V' || $journalcode == 'VS' )
		//	return true;
		
		foreach( $this->journallist as $jnl )
		{
			if( $jnl['JournalCode'] == $journalcode)			
				return true;
		}
		
		return false;
	}

	private function ProcessImportInvoicesOLD()
	{
		$err = 0;
		$ccount = 0;
		$ErrNumber = 0;
		$PassNumber = 1;
		$myList = array();
		$arrLength = count($myList);
		$nbcompanies = 0;

		$logtype = 'info';

		$invoiceslist = $this->picsoo_ws->GetListForMultipleTable( $this->ie_clientsid, "invoice", "" );
		return;

		//log_message($logtype, 'Export suppliers to Picsoo starts (' . $this->ie_clientsid . ')' );
		$companieslist = $this->clients_model->get();

		if( count($companieslist) <= 0 )
			return;

		//$dbg = $companieslist[3427];

		// -------------------------------------------------------------------------------------------------------------------
		// boucle sur les clients
		// -------------------------------------------------------------------------------------------------------------------
		foreach ($companieslist as $seqcompany) 
		{
			if( $seqcompany['active'] == false )
				continue;

			//if ( $seqcompany['picsoo_Customercode'] != '400FOURMANOIR' )
			//{
			//	continue;
			//}

	/* DH DEBUG */
			//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!");
			//fwrite($myfile, serialize($seqcompany));
			//fclose($myfile);

			if ( empty(trim($seqcompany['picsoo_Customercode'])) ) 
			{
				continue;
			}

			$sdt = substr( $this->ie_startdate, 6, 4) . '-' . substr( $this->ie_startdate, 3, 2) . '-' . substr( $this->ie_startdate, 0, 2) . 'T' . substr( $this->ie_startdate, 11, 8);
			$edt = substr( $this->ie_enddate, 6, 4) . '-' . substr( $this->ie_enddate, 3, 2) . '-' . substr( $this->ie_enddate, 0, 2) . 'T' . substr( $this->ie_enddate, 11, 8);
			$dbg = $seqcompany['picsoo_Customercode'];
			$invoiceslist = $this->picsoo_ws->GetInvoiceData($this->ie_clientsid,$seqcompany['picsoo_Customercode'],$sdt, $edt);
			if( $invoiceslist == null )
				continue;
			if( count($invoiceslist) == 0 )
				continue;

			// -------------------------------------------------------------------------------------------------------------------
			// boucle sur les invoices
			// -------------------------------------------------------------------------------------------------------------------
			$this->picsoo_model->DeleteInvoicesFromPicsoo( $seqcompany['userid'] );

			foreach ($invoiceslist as $invoice) 
			{
		        $invoice_data = [];
	            $invoice_data['picsoo_due_amount']			= $invoice['due_amount'];
		        $invoice_data['clientid']   				= $seqcompany['userid'];
		        $invoice_data['picsoo_invoice_id']   		= $invoice['invoice_id'];
                $invoice_data['picsoo_fk_clients_id']		= $this->ie_clientsid;
		        $invoice_data['project_id'] 				= 0;
		        $invoice_data['number']     				= get_option('next_invoice_number');
		        $invoice_data['date']       				= $this->picsoo_model->PicsooDate2Sequoia($invoice['invoice_date']);
		        $invoice_data['duedate']    				= $this->picsoo_model->PicsooDate2Sequoia($invoice['invoice_due_date']);;

		        $invoice_data['show_quantity_as'] 			= '1'; // ??
		        $invoice_data['currency']         			= '2'; // ??
		        $invoice_data['subtotal']         			= $invoice['net'];
		        $invoice_data['total_tax']         			= $invoice['vat_rate'];
		        $invoice_data['total']            			= $invoice['sub_total'];
		        $invoice_data['adjustment']       			= '0.00';
		        $invoice_data['discount_percent'] 			= $invoice['invoicediscount_percent'];
		        $invoice_data['discount_total']   			= $invoice['invoicediscount_amount'];
		        $invoice_data['discount_type']    			= ''; // ??
		        $invoice_data['sale_agent']       			= '0'; // ??
		        // Since version 1.0.6
		        $invoice_data['billing_street']   			= $seqcompany['billing_street'];
		        $invoice_data['billing_city']     			= $seqcompany['billing_city'];
		        $invoice_data['billing_state']    			= $seqcompany['billing_state'];
		        $invoice_data['billing_zip']      			= $seqcompany['billing_zip'];
		        $invoice_data['billing_country']  			= $seqcompany['billing_country'];
		        $invoice_data['shipping_street']  			= $seqcompany['shipping_street'];
		        $invoice_data['shipping_city']    			= $seqcompany['shipping_city'];
		        $invoice_data['shipping_state']   			= $seqcompany['shipping_state'];
		        $invoice_data['shipping_zip']     			= $seqcompany['shipping_zip'];
		        $invoice_data['shipping_country'] 			= $seqcompany['shipping_country'];
	            $invoice_data['include_shipping'] 			= 0; // ??
		        $invoice_data['show_shipping_on_invoice']	= 1; // ??
		        $invoice_data['terms']                   	= get_option('predefined_terms_invoice');
		        //$invoice_data['clientnote']              	= get_option('predefined_clientnote_invoice');
		        //$invoice_data['clientnote']              	= $seqcompany['invoice_quotes'];
		        //$invoice_data['clientnote']              	= $seqcompany['FreeDescription'];
		        //$invoice_data['clientnote']              	= $seqcompany['VoucherNote'];
		        $invoice_data['clientnote']              	= $invoice['invoice_quotes'] . ' ' . $invoice['FreeDescription'] . '<br>' . $invoice['VoucherNote'];
		        $invoice_data['adminnote'] 					= '';
		        // Set to unpaid status automatically
		        $invoice_data['status']    					= 1;

		        $modes = $this->payment_modes_model->get('', [
		            'expenses_only !=' => 1,
		        ]);
		        $temp_modes = [];
		        foreach ($modes as $mode) {
		            if ($mode['selected_by_default'] == 0) {
		                continue;
		            }
		            $temp_modes[] = $mode['id'];
		        }
		        $invoice_data['allowed_payment_modes'] 		= $temp_modes;
		        $invoice_data['newitems']              		= [];
		        $custom_fields_items                   		= get_custom_fields('items');
		        $key                                   		= 1;

				// on teste si le picsoo_invoice_id est déjà dans la base; si oui, update; si non, nouveau
				$invoiceid = $this->picsoo_model->IsInvoiceExists( $invoice['invoice_id'] );
				if( $invoiceid == 0 )
				{
			        $invoiceid = $this->picsoo_model->add_invoice($invoice_data);
			        if ($invoiceid) 
					{
		            }
				}
				else // update
				{
		            unset($invoice_data['save_as_draft']);
			        //if (!has_permission('customers', '', 'create')) 
			        //{
		    	    //    access_denied('customers');
		        	//}

			        $success = $this->picsoo_model->update_invoice( $invoice_data, $invoiceid );
				}

				// -------------------------------------------------------------------------------------------------------------------
				// les paiements
				// -------------------------------------------------------------------------------------------------------------------
				//if( $invoice_data['picsoo_due_amount'] == 0 || $invoice_data['picsoo_due_amount'] < $invoice_data['total'] )	// si le due = 0 ou < total, il y a eu paiement même partiel			
				{
					$payementslist = $this->picsoo_ws->GetInvoiceBillDetails( $this->ie_clientsid, $invoice_data['picsoo_invoice_id']);
					if( $payementslist == null )
						continue;
					if( count($payementslist) == 0 )
						continue;
					
					$this->picsoo_model->DeletePaiementsFromPicsoo( $invoice_data['picsoo_invoice_id'] );
					
					foreach ($payementslist as $payement) // boucle sur les paiements
					{
						$payment_data = [];
	                    $payment_data['paymentmode']   				= $payement['Method_Name'/*'Entity'*/];
    	                $payment_data['amount']        				= $payement['Amount'];
        	            $payment_data['invoiceid']     				= $invoiceid;
        	            $payment_data['picsoo_invoice_id']    		= $invoice['invoice_id'];
        	            $payment_data['picsoo_BankAccountsId']		= $payement['BankAccountsId'];
        	            $payment_data['picsoo_fk_clients_id']		= $this->ie_clientsid;
        	            //$payment_data['do_not_send_email_template']	= 1; // sinon, le client va recevoir un email à chaque import
        	            //$payment_data['do_not_send_email_template']	= 1; // sinon, le client va recevoir un email à chaque import
            	        //$payment_data['transactionid'] = $invoice->charge;

						$id = $this->picsoo_model->add_payement( $payment_data );
					} // foreach ($payementslist as $payement)
				} // if( $invoice_data['picsoo_due_amount'] == 0 || $invoice_data['picsoo_due_amount'] < $invoice_data['total'] )
			} // foreach ($invoiceslist as $invoice)
		} // foreach ($companieslist as $seqcompany)

		return $err;
	}
	
	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessExportItems()
	{
		set_alert('warning', 'Fonction non disponible');
		return;
		
		$err = 0;
		$ccount = 0;
		$ErrNumber = 0;
		$PassNumber = 1;
		$myList = array();
		$arrLength = count($myList);

		$logtype = 'info';
		log_message($logtype, 'Export products to Picsoo starts (' . $this->ie_clientsid . ')' );

		$itemslist = $this->products_model->getAllProducts();

		foreach ($itemslist as $positem) {
			$product   = $this->products_model->getProductDetail($positem->id);
			$brand     = $this->site->getBrandByID($product->brand);
			$base_unit = $sale_unit = $purchase_unit = '';
			//$image     = $this->products_model->getProductPhotos($positem->id);

			//$image_path = base_url('assets/uploads/thumbs/') .$positem->image;
			$image_path = './assets/uploads/thumbs/' .$positem->image;
			if (file_exists($image_path)) {
				// https://stackoverflow.com/questions/3967515/how-to-convert-an-image-to-base64-encoding
				$image_name = $positem->image;
				//$type = pathinfo($path, PATHINFO_EXTENSION);
				$image_data = file_get_contents($image_path);
				//$image_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				$image_base64 = base64_encode($image_data);
			} else {
				$image_base64 = '';
				$image_name = '';
			}
			if ($units = $this->site->getUnitsByBUID($product->unit)) {
				foreach ($units as $u) {
					if ($u->id == $product->unit) {
						$base_unit = $u->code;
					}
					if ($u->id == $product->sale_unit) {
						$sale_unit = $u->code;
					}
					if ($u->id == $product->purchase_unit) {
						$purchase_unit = $u->code;
					}
				}
			}
			$variants         = $this->products_model->getProductOptions($id);
			$product_variants = '';
			if ($variants) {
				$i = 1;
				$v = count($variants);
				foreach ($variants as $variant) {
					$product_variants .= trim($variant->name) . ($i != $v ? '|' : '');
					$i++;
				}
			}
			$quantity = $product->quantity;
			if ($wh) {
				if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
					$quantity = $wh_qty['quantity'];
				} else {
					$quantity = 0;
				}
			}
			$supplier = false;
			if ($product->supplier1) {
				$supplier = $this->site->getCompanyByID($product->supplier1);
			}

			$tax_details = $this->site->getTaxRateByID($product->tax_rate);
			$ctax        = $this->site->calculateTax($product, $tax_details/*, $unit_price*/);
			$item_tax    = $this->sma->formatDecimal($ctax['amount']);
			$SaleUnitPriceVat = $product->price + $item_tax;

			$datatosend = [
				'ItemName' => isset($product->code) ? trim($product->code) : '',
				'ItemNomianlCode' => isset($product->code) ? trim($product->code) : '',
				'FRItemDescription' => isset($product->name) ? trim($product->name) : '',
				'NLItemDescription' => isset($product->name) ? trim($product->name) : '',
				'ItemDescription' => isset($product->name) ? trim($product->name) : '',
				'WarehouseName' => '',
				'CategoryName' => '',
				'ItrackthisItem' => true,
				'IPuchasethisItem' => true,
				'ISalethisItem' => true,
				'SaleAccountName' => '700000',
				'SaleUnitPrice' => isset($product->price) ? trim($product->price) : '',
				'SaleUnitPriceVat' => '',
				'SalesVatCode' => '21',
				'VATCodesalesCredit' => '21',
				'PurAccountName' => '600000',
				'PurhcasePrice' => isset($product->cost) ? trim($product->cost) : '',
				'Multiplier' => '',
				'CostPrice' => '',
				'PurchaseVatCode' => '21',
				'VATCodePurchaseCredit' => '21',
				'SupplierName' => '',
				'MPN' => '',
				'ISBN' => '',
				'EAN' => '',
				'brand' => '',
				'netweight' => '',
				'brutoweight' => '',
				'CPU' => '',
				'StockMinimum' => '',
				'StockMaximum' => '',
				'Picture' => $image_base64,
				'FileName' => $image_name,
				'ReferenceExternal' => $this->ie_reference,
				'SaleUnitPriceVat' => $SaleUnitPriceVat,

			];

	/*
	$this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
	$this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
	$this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->barcode_symbology);
	$this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
	$this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->category_code);
	$this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
	$this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
	$this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
	if ($this->Owner || $this->Admin || $this->session->userdata('show_cost'))
	{
	$this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->cost);
	}
	if ($this->Owner || $this->Admin || $this->session->userdata('show_price'))
	{
	$this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->price);
	}
	$this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->alert_quantity);
	$this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
	$this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
	$this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->image);
	$this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->subcategory_code);
	$this->excel->getActiveSheet()->SetCellValue('P' . $row, $product_variants);
	$this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
	$this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
	$this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
	$this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
	$this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
	$this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
	$this->excel->getActiveSheet()->SetCellValue('W' . $row, $product->hsn_code);
	$this->excel->getActiveSheet()->SetCellValue('X' . $row, $product->second_name);
	$this->excel->getActiveSheet()->SetCellValue('Y' . $row, $supplier ? $supplier->name : '');
	$this->excel->getActiveSheet()->SetCellValue('Z' . $row, $supplier ? $product->supplier1_part_no : '');
	$this->excel->getActiveSheet()->SetCellValue('AA' . $row, $supplier ? $product->supplier1price : '');
	$this->excel->getActiveSheet()->SetCellValue('AB' . $row, $quantity);
	$this->excel->getActiveSheet()->SetCellValue('AC' . $row, $product->details);
	$this->excel->getActiveSheet()->SetCellValue('AD' . $row, $product->product_details);*/
			//$row++;

			// ==============================================================================================================================================
			// envoi vers Picsoo web ========================================================================================================================
			// ==============================================================================================================================================

	/* DH DEBUG */
			//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!");
			//$results = print_r($datatosend, true); // $results now contains output from print_r
			//fwrite($myfile, $results /*serialize($datatosend)*/);
			//fclose($myfile);

			$myList[] = $datatosend;

			$ccount++;
			if ( $ccount == 1000 ) {
				$arrLength = count($myList);
				//fwrite($myfile, $myList); //DBG
				//if( $CurrentMvt==2 ) //DBG
				$result = $this->picsoo_ws->SaveItemsList($this->ie_clientsid, $myList);
				if ( $result['IsSuccess'] != true && $result['IsSuccess']!='' )
					$ErrNumber++;

				$logtype = 'info';
				log_message($logtype, 'Export products to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ' - msg : ' . $result['Message'] . ' - ' . $result['Data'] );

				if ( $result['IsSuccess'] == false ) {
					if ( $result['Message'] == 'Authcode Expired' ) {
						$msg = lang('export_finished') . ' - ' . lang('try_later') . ' (AE).';
						set_alert('danger', $msg );
						$logtype = 'error';

						log_message($logtype, 'Export products to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						return;
					}
				}
				$myList = array();
				$arrLength = count($myList);
				$PassNumber++;
				//if( $PassNumber == 2 )
				//    break;
			}

			//$this->picsoo_ws->SaveItems($this->ie_clientsid, $datatosend);
		}

		$arrLength = count($myList);
		if ( $arrLength != 0 ) {
			$result = $this->picsoo_ws->SaveItemsList($this->ie_clientsid, $myList);

			$logtype = 'info';
			if ( $customertype == 'C' )
				log_message($logtype, 'Export customers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ' - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
			else
				log_message($logtype, 'Export suppliers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
		}

		$msg = lang('export_finished');
		if ( $result['IsSuccess'] != true ) 
		{
			$msg .= ' (' . lang('errors_detected') . ') - [' . $result['Message'] . ']';
			set_alert('danger', $msg );
			$logtype = 'error';
		} 
		else 
		{
			$msg .= lang('awaiting_email');
			set_alert('success', $msg );
			$logtype = 'info';
		}

		log_message($logtype, 'Export products to Picsoo (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );

		$logtype = 'info';
		log_message($logtype, 'Export products to Picsoo ends (' . $this->ie_clientsid . ')' );

		return $err;
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessExportCustomers( $customertype = ''  )
	{
		$err = 0;
		$ccount = 0;
		$ErrNumber = 0;
		$PassNumber = 1;
		$myList = array();
		$arrLength = count($myList);
		$nbcompanies = 0;

		$logtype = 'info';

		if ( $customertype == 'C' ) 
		{
			//log_message($logtype, 'Export customers to Picsoo starts (' . $this->ie_clientsid . ')' );
			$companieslist = $this->clients_model->get();
		} 
		else 
		{
			//log_message($logtype, 'Export suppliers to Picsoo starts (' . $this->ie_clientsid . ')' );
			$companieslist = $this->clients_model->get();
		}

		if( count($companieslist) <= 0 )
			return;

		//$dbg = $companieslist[3427];

		foreach ($companieslist as $seqcompany) 
		{
			if( $seqcompany['active'] == false )
				continue;
			
			//if ( $seqcompany->company == 'A000' )
			//{
			//	$err = 0;
			//}

	/* DH DEBUG */
			//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!");
			//fwrite($myfile, serialize($seqcompany));
			//fclose($myfile);

			// on test si la company est utilisées dans les transactions; si pas, on ne la prend pas !
			/*if ( $customertype == 'C' ) 
			{
				if ( !$this->picsoo_model->IsCompanyInSales( $seqcompany->id ))
					continue;
			} else {
				if ( !$this->picsoo_model->IsCompanyInPurchases( $seqcompany->id ))
					continue;
			}*/

			if ( empty(trim($seqcompany['picsoo_Customercode'])) ) 
			{
				//$err++;
				continue;
				/*if ( $customertype == 'C' )
					$seqcompany->picsoo_Customercode = "400000";
				else
					$seqcompany->picsoo_Customercode = "440000";*/
			}

			$nbcompanies++;

			//if( $seqcompany->company != "PHILIPPE" )
			//    continue;

			$datatosend = [
			
				//'ClientId'                  => $this->ie_clientsid,
				'CompanyName'               => $seqcompany['company'],
				'AccountCode'               => $seqcompany['picsoo_Customercode'],
				'Title'                     => '',
				'FirstName'                 => '',
				'LastName'                  => '',
				'Email'                     => '',
				'PrimaryPhone'              => $seqcompany['phonenumber'],
				'ContactType'               => '',
				'BillingAddressLine1'		=> $seqcompany['billing_street'],
				'BillingAddressLine2'		=> '',
				'BillingPostalCode'         => $seqcompany['billing_zip'],
				'BillingCity'               => $seqcompany['billing_city'],
				'BillingState'              => $seqcompany['billing_state'],
				'BillingCountry'            => $seqcompany['billing_country'],
				'DeliveryAddressLine1'		=> $seqcompany['shipping_street'],
				'DeliveryAddressLine2'		=> '',
				'DeliveryPostalCode'		=> $seqcompany['shipping_zip'],
				'DeliveryCity'              => $seqcompany['shipping_city'],
				'DeliveryState'             => $seqcompany['shipping_state'],
				'DeliveryCountry'           => $seqcompany['shipping_country'],
				'Website'                   => $seqcompany['website'],
				'BankName'                  => '',
				'AccountName'               => '',
				'AccountNumber'             => '',
				'OtherDetail'               => '',
				'SecondaryPhone'            => '',
				'vatNumber'                 => '',
				'Skype'                     => '',
				'Facebook'                  => '',
				'Twitter'                   => '',
				'Reconciliation'            => '',
				'TaxReport'                 => '',
				'Reminder'                  => '',
				'EuropeanVatNo'             => '',
				'Vatcode'                   => '',
				'DueDate'                   => '',
				'BusinesType'               => '',
				'Category'                  => '',
				'Discount'                  => '',
				'Notes'                     => '',
				'VatCodePurchaseCredit'		=> '',
				'VatCodeSalesCredit'		=> '',
				'Item'                      => '',
				'Languages'                 => '',
				'Type'                      => '',
				'Deleted'                   => '',
				'IBAN'                      => '',
				'BIC'                       => '',
				// vrs 1.1.27 - ajout de ce champs
				'ReferenceExternal'         => $this->ie_reference,
			];

			// ==============================================================================================================================================
			// dump vers excel ==============================================================================================================================
			// ==============================================================================================================================================

			/*$excel = false;
			if( $excel == true )
			{
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle(lang('company'));
			$this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
			$this->excel->getActiveSheet()->SetCellValue('B1', lang('email'));

			$row = 2;
			foreach ($_POST['val'] as $id) {
			$customer = $this->site->getCompanyByID($id);
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, $datatosend->CompanyName);
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $datatosend->Email);
			$row++;
			}

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
			$filename = 'company_' . date('Y_m_d_H_i_s');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
			return;
			}*/

			// ==============================================================================================================================================
			// envoi vers Picsoo web ========================================================================================================================
			// ==============================================================================================================================================

	/* DH DEBUG */
			//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!");
			//$results = print_r($datatosend, true); // $results now contains output from print_r
			//fwrite($myfile, $results /*serialize($datatosend)*/);
			//fclose($myfile);

			$myList[] = $datatosend;

			$ccount++;
			if ( $ccount == 1000 ) 
			{
				$arrLength = count($myList);
				//fwrite($myfile, $myList); //DBG
				//if( $CurrentMvt==2 ) //DBG
				$result = $this->picsoo_ws->SaveCustomersSuppliersList($this->ie_clientsid, $myList, $customertype);
				if ( $result['IsSuccess'] != true && $result['IsSuccess']!='' )
					$ErrNumber++;

				$logtype = 'info';
				if ( $customertype == 'C' )
					log_message($logtype, 'Export customers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ' - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
				else
					log_message($logtype, 'Export suppliers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );

				if ( $result['IsSuccess'] == false ) 
				{
					if ( $result['Message'] == 'Authcode Expired' ) 
					{
						$msg = lang('export_finished') . ' - ' . lang('try_later') . ' (AE).';
						set_alert('danger', $msg );
						$logtype = 'error';

						if ( $customertype == 'C' )
							log_message($logtype, 'Export customers to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						else
							log_message($logtype, 'Export suppliers to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						return;
					}
				}
				$myList = array();
				$arrLength = count($myList);
				$PassNumber++;
				//if( $PassNumber == 2 )
				//    break;
			}

			//$this->picsoo_ws->SaveCustomers($this->ie_clientsid, $datatosend, $customertype);
		} // foreach ($companieslist as $seqcompany)

		$arrLength = count($myList);
		if ( $arrLength != 0 ) 
		{
			$result = $this->picsoo_ws->SaveCustomersSuppliersList($this->ie_clientsid, $myList, $customertype);

			$logtype = 'info';
			if ( $customertype == 'C' )
				log_message($logtype, 'Export customers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ' - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
			else
				log_message($logtype, 'Export suppliers to Picsoo (' . $this->ie_clientsid . ') - Pass# : ' . $PassNumber . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
		}

		$msg = lang('export_finished');
		if ( $result['IsSuccess'] != true ) 
		{
			$msg .= ' (' . lang('errors_detected') . ') - [' . $result['Message'] . ']';
			set_alert('danger', $msg );
			$logtype = 'error';
		}
		else 
		{
			$msg .= lang('awaiting_email');
			$msg .= ' <br>(INFO : ' . $nbcompanies . ' clients/fournisseurs à transférer = ' . ((($nbcompanies / 100 ) * 10) + 10) . ' minutes d\'attente)';
			set_alert( 'success', $msg );
			$logtype = 'info';
		}

		if ( $customertype == 'C' )
			log_message($logtype, 'Export customers to Picsoo (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
		else
			log_message($logtype, 'Export suppliers to Picsoo (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );

		$logtype = 'info';

		if ( $customertype == 'C' ) 
		{
			log_message($logtype, 'Export customers to Picsoo ends (' . $this->ie_clientsid . ')' );
		}
		else 
		{
			log_message($logtype, 'Export suppliers to Picsoo ends (' . $this->ie_clientsid . ')' );
		}

		return $err;
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------------------------------------------------------
	private function ProcessExportTransactions( )
	{
		set_alert('warning', 'Fonction non disponible');
		return;
		
		$err = 0;

	/*
	select sma_sales.id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, CONCAT(grand_total, '__', rounding, '__', paid) as balance, sale_status, payment_status, sma_companies.email as cemail
	from sma_sales
	left join sma_companies on sma_companies.id = sma_sales.customer_id
	where warehouse_id = 1
	group by sma_sales.id
	*/

	/*$this->load->admin_model('sales_model');

	$q = $this->db->get('sales');
	$cnt = $q->num_rows();
	if ($q->num_rows() > 0)
	{
	foreach (($q->result()) as $row)
	{
	$data[] = $row;
	$itemsrows = $this->sales_model->getSaleItemByID( $row->id );

	}
	//return $data;
	}
	//return false;
	*/
		//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!");
		//fwrite($myfile, $this->ie_startdate);
		//fclose($myfile);

		if ($this->ie_startdate) {
			$this->ie_startdate = $this->sma->fld($this->ie_startdate);
			$this->ie_enddate   = $this->sma->fld($this->ie_enddate);
		}
		$this->load->admin_model('pos_model');
		$this->load->library('datatables');
		//$this->db->save_queries = TRUE; //DBG
		//$this->db->db_debug = TRUE; //DBG

		// old: "SELECT `sma_sales`.`id` as `id`, `date`, `reference_no`, `customer`, (grand_total+COALESCE(rounding, 0)), `paid`, CONCAT(grand_total, '__', `rounding`, '__', paid) as balance, `sale_status`, `payment_status` FROM `sma_sales` WHERE date(sma_sales.date) BETWEEN "2021-02-05 " and "2021-02-05 " GROUP BY `sma_sales`.`id` ORDER BY `date` ASC"
		// new: "SELECT `sma_sales`.`id` as `id`, `date`, `reference_no`, `customer`, `customer_id`, `paid`, `sale_status`, `total_tax`, `payment_status`, `due_date`, `total`, `grand_total`
		//      FROM `sma_sales`
		//      WHERE date(sma_sales.date) BETWEEN "01-01-2021 00:00:00" and "31-12-2021 23:59:59"
		//      GROUP BY `sma_sales`.`id`
		//      ORDER BY `date` ASC"
		$this->db
		->select($this->db->dbprefix('sales') . ".id as id, date, reference_no, customer, customer_id, paid, sale_status, total_tax, payment_status, due_date, total, grand_total, total_discount, order_discount")
		->from('sales')
		->group_by('sales.id')
		->order_by('date', 'asc');

		if ($this->ie_startdate) {
			$this->db->where('date(' . $this->db->dbprefix('sales') . '.date) BETWEEN "' . $this->ie_startdate . '" and "' . $this->ie_enddate . '"');
		}

		$q = $this->db->get();

		//$msg = $this->db->last_query(); //DBG
		//$cnt = $q->num_rows(); //DBG
		$mvts = 1;
		$this->ximportslist = array();
		$xbadtrans = array(); // contient les transactions dont le débit<>crédit

		if ($q->num_rows() <= 0) 
		{
			$msg = lang('no_transaction');
			set_alert('danger', $msg );
			$logtype = 'error';

			return $err;
		}

		$this->vatlist = $this->picsoo_ws->GetValList($this->ie_clientsid);
		if ( $this->vatlist == null ) 
		{
			$msg = lang('no_vatlist');
			set_alert('danger', $msg );
			$logtype = 'error';

			return $err;
		}
		$this->CleanVatList();

		//$result = $this->picsoo_ws->GetJournalList($this->ie_clientsid);

		foreach (($q->result()) as $header) {
			//if( $mvts > 2 ) // DBG
			//{
			//$message = "STOP!";
			//echo "<script type='text/javascript'>alert('$message');</script>";
			//    break;
			//}
			//$this->ximportslist = array(); // ré-initialisation du tableau à chaque transaction.

			$saleid = $header->id;
			$saleslist = $this->pos_model->getAllInvoiceItems($header->id); // < sma_sale_items
			$saleslist_cnt = count($saleslist);
			$customer_details = $this->companies_model->getCompanyByID($header->customer_id);

			$total_debit = 0.00;
			$total_credit = 0.00;

			//if( $saleid != 244 /*235*/ ) // DBG
			//    continue;

			// le compte client ==============================================================================================================================
			$obj = new Struct_XImport();
			$obj->_MVTS = $mvts; // numéro de mouvement
			$obj->_ID = $saleid;
			$obj->_JOUR = 'VT'; // Code journal
			$obj->_DECR = $this->sma->hrld($header->date); // Date écriture
			$obj->_DECH = $this->sma->hrld($header->due_date); // Date échéance
			$obj->_NUMP = $header->reference_no; // N° de pièce
			$obj->_COMP = $customer_details->picsoo_Customercode; // N° de compte
			$obj->_LIBE = $customer_details->name; // Libellé écriture
			$obj->_MONT = $header->grand_total; // Montant
			$obj->_SENS = 'D'; // Sens montant
			$obj->_LETT = ''; // Ref. pointage / lettrage
			$obj->_ANAL = ''; // Code analytique
			$obj->_MTVA = $header->total_tax; // Montant total TVA
			$obj->_TTVA = 0.00; // Taux de TVA
			$obj->_CTVA = ''; // Code TVA
			$obj->_GTVA = '';
			$obj->_GBAS = '';

			$this->ximportslist[] = $obj;

			$total_debit += $obj->_MONT;

			// articles / contreparties ======================================================================================================================

			$current_cnt = 1;
			$gtotal_tva = 0.00;  // total général de tva pour gestion arrondi final
			$gtotal_htva = 0.00; // total général htva pour gestion arrondi final

			foreach ($saleslist as $saleline ) {
				$product_details = $this->products_model->getProductByCode( $saleline->product_code );
				$tax_rate = $this->site->getTaxRateByID($saleline->tax_rate_id);

				// contrepartie ----------------------------------------------------------------------------

				$obj = new Struct_XImport();
				$obj->_MVTS = $mvts;
				$obj->_ID = $saleid;
				$obj->_JOUR = 'VT'; // Code journal
				$obj->_DECR = $this->sma->hrld($header->date); // Date écriture
				$obj->_DECH = $this->sma->hrld($header->due_date); // Date échéance
				$obj->_NUMP = $header->reference_no; // N° de pièce
				$obj->_COMP = '700000'; // N° de compte
				$obj->_LIBE = $customer_details->name; // Libellé écriture
				$montant_htva = $saleline->net_unit_price * $saleline->unit_quantity;
				//$montant_htva = $saleline->real_unit_price * $saleline->unit_quantity;
				$obj->_MONT = $montant_htva; // Montant
				$gtotal_htva += $obj->_MONT;
				$obj->_SENS = 'C'; // Sens montant
				$obj->_LETT = ''; // Ref. pointage / lettrage
				$obj->_ANAL = ''; // Code analytique
				$obj->_MTVA = 0.00; // Montant TVA
				$obj->_TTVA = $tax_rate->rate; // Taux de TVA
				$obj->_CTVA = $tax_rate->code; // Code TVA
				$obj->_GTVA = '';
				$obj->_GBAS = '3'; // accountgrid - 3 : cf Philippe 4/10/2022 skype

				if ( $obj->_MONT != 0.00 ) {
					$total_credit += $obj->_MONT;
					$this->ximportslist[] = $obj;
				}

				// tva -------------------------------------------------------------------------------------

				foreach ($this->vatlist as $vatlistline) {
					if ( $saleline->tax_code == $vatlistline['CODE'] ) {
						$obj = new Struct_XImport();
						$obj->_MVTS = $mvts;
						$obj->_ID = $saleid;
						$obj->_JOUR = 'VT'; // Code journal
						$obj->_DECR = $this->sma->hrld($header->date); // Date écriture
						$obj->_DECH = $this->sma->hrld($header->due_date); // Date échéance
						$obj->_NUMP = $header->reference_no; // N° de pièce
						$obj->_COMP = '451540'; // N° de compte
						$obj->_LIBE = $customer_details->name; // Libellé écriture
						//$montant_tva = $saleline->item_tax * $saleline->quantity;
						$obj->_MONT = ($saleline->item_tax * ($vatlistline['RepartPercent'] / 100)); // Montant
						$gtotal_tva += $obj->_MONT;
						$obj->_SENS = 'C'; // Sens montant
						$obj->_LETT = ''; // Ref. pointage / lettrage
						$obj->_ANAL = ''; // Code analytique
						//$obj->_MTVA = $montant_tva; // Montant TVA
						$obj->_TTVA = $vatlistline['VATPercent']; //$tax_rate->rate; // Taux de TVA
						$obj->_CTVA = $vatlistline['CODE']; //$tax_rate->code; // Code TVA
						$obj->_GTVA = $vatlistline['VATGrid'];
						$obj->_GBAS = '';

						//if ( $obj->_MONT != 0.00 )
						//{
						$total_credit += $obj->_MONT;
						$this->ximportslist[] = $obj;
						//}
					}
				} // foreach ($this->vatlist as $vatlistline)

				// remise globale --------------------------------------------------------------------------

				if ( $header->order_discount != 0.00 ) {
					$obj = new Struct_XImport();
					$obj->_MVTS = $mvts;
					$obj->_ID = $saleid;
					$obj->_JOUR = 'VT'; // Code journal
					$obj->_DECR = $this->sma->hrld($header->date); // Date écriture
					$obj->_DECH = $this->sma->hrld($header->due_date); // Date échéance
					$obj->_NUMP = $header->reference_no; // N° de pièce
					$obj->_COMP = '451540'; // N° de compte
					$obj->_LIBE = $customer_details->name; // Libellé écriture
					//$montant_tva = $saleline->item_tax * $saleline->quantity;
					$tva = ($total_htva * $tax_rate->rate) / 100;
					$obj->_MONT = $this->sma->roundMoney($tva,0.01); // Montant
					$gtotal_tva -= $obj->_MONT;
					if ( $saleslist_cnt == $current_cnt )
						// on est a la toute dernière ligne, on check si difference d'arrondi
					{
						$total_calcule = $gtotal_htva + $gtotal_tva;
						$val = floatval($header->grand_total);
						// https://stackoverflow.com/questions/3148937/compare-floats-in-php
						// if (abs(($total_calcule - $val)/ $val) < 0.00001)
						if ( abs($total_calcule - $val) < PHP_FLOAT_EPSILON )
							// difference d'arrondi, on met sur la dernière ligne de tva
						{
							$total_arrondi = $obj->_MONT + ($val - $total_calcule);
							$obj->_MONT = $this->sma->roundMoney($total_arrondi,0.01);
						}
					}
					$obj->_SENS = 'D'; // Sens montant
					$obj->_LETT = ''; // Ref. pointage / lettrage
					$obj->_ANAL = ''; // Code analytique
					$obj->_MTVA = $montant_tva; // Montant TVA
					$obj->_TTVA = $tax_rate->rate; // Taux de TVA
					$obj->_CTVA = $tax_rate->code; // Code TVA
					$obj->_GTVA = '';
					$obj->_GBAS = '';

					if ( $obj->_MONT != 0.00 ) {
						$total_debit += $obj->_MONT;
						$this->ximportslist[] = $obj;
					}
				}
				$current_cnt++;
			} // foreach($saleslist as $saleline )

			// on exporte ximport vers picsoo
			if ( $this->sma->roundMoney($total_debit,0.01) != $this->sma->roundMoney($total_credit,0.01) && $this->toexcel == true )
				// uniquement si excel est sélectionné par économie de mémoire
			{
				$bo = new Struct_BadTrans();
				$bo->_MVTS = $mvts;
				$bo->_ID = $saleid;
				$bo->_DEBIT = $total_debit;
				$bo->_CREDIT = $total_credit;
				$xbadtrans[] = $bo;
			}

			$mvts++;
		}

		//return; //DBG

		// ==============================================================================================================================================
		// dump vers excel ==============================================================================================================================
		// ==============================================================================================================================================
		if ( $this->toexcel == true ) {
			$tot_DC = 0.00;
			$tot_TOTAL = 0.00;
			$tot_MontTVA = 0.00;
			$tot_TVA_0 = 0.00;
			$tot_TVA_6 = 0.00;
			$tot_TVA_12 = 0.00;
			$tot_TVA_21 = 0.00;
			$tot_TVA_inco = 0.00;
			$tot_TVA_hcee = 0.00;
			$tot_TVA_autre = 0.00;

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('transactions');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
			$this->excel->getActiveSheet()->SetCellValue('B1', lang('email'));

			// données ximport
			$this->excel->getActiveSheet()->SetCellValue('A1', 'MVTS');
			$this->excel->getActiveSheet()->SetCellValue('B1', 'ID (sma_sales)');
			$this->excel->getActiveSheet()->SetCellValue('C1', 'JOUR');
			$this->excel->getActiveSheet()->SetCellValue('D1', 'DECR');
			$this->excel->getActiveSheet()->SetCellValue('E1', 'DECH');
			$this->excel->getActiveSheet()->SetCellValue('F1', 'NUMP');
			$this->excel->getActiveSheet()->SetCellValue('G1', 'COMP');
			$this->excel->getActiveSheet()->SetCellValue('H1', 'LIBE');
			$this->excel->getActiveSheet()->SetCellValue('I1', 'MONT');
			$this->excel->getActiveSheet()->SetCellValue('J1', 'SENS');
			$this->excel->getActiveSheet()->SetCellValue('K1', 'MONT SIGNE');
			$this->excel->getActiveSheet()->SetCellValue('L1', 'LETT');
			$this->excel->getActiveSheet()->SetCellValue('M1', 'ANAL');
			$this->excel->getActiveSheet()->SetCellValue('N1', 'MVTA');
			$this->excel->getActiveSheet()->SetCellValue('O1', 'TTVA');
			$this->excel->getActiveSheet()->SetCellValue('P1', 'CTVA');

			// données TVA
			$this->excel->getActiveSheet()->SetCellValue('Q1', 'TOTAL');
			$this->excel->getActiveSheet()->SetCellValue('R1', 'Mont.TVA');
			$this->excel->getActiveSheet()->SetCellValue('S1', '0');
			$this->excel->getActiveSheet()->SetCellValue('T1', '6');
			$this->excel->getActiveSheet()->SetCellValue('U1', '12');
			$this->excel->getActiveSheet()->SetCellValue('V1', '21');
			$this->excel->getActiveSheet()->SetCellValue('W1', 'inco');
			$this->excel->getActiveSheet()->SetCellValue('X1', 'hcee');
			$this->excel->getActiveSheet()->SetCellValue('Y1', 'autre');

			$row = 2;
			foreach ($this->ximportslist  as $ximport_line) {
				// données ximport
				//$dbg1 = $ximport_line->_DECR;
				//$dbg2 = strtotime($ximport_line->_DECR);
				//$dbg3 = date('d/m/Y',$ximport_line->_DECR);
				//$dbg4 = date('d/m/Y',$ximport_line->_DECR);
				//$dbg5 = new DateTime($ximport_line->_DECR);
				//$dbg6 = $dbg5->format('Y-m-d H:i:s');
				//$delivery = $this->sales_model->getDeliveryByID($id);
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $ximport_line->_MVTS);
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $ximport_line->_ID);
				$this->excel->getActiveSheet()->SetCellValue('C' . $row, $ximport_line->_JOUR);
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, strtok($ximport_line->_DECR,' '));
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, strtok($ximport_line->_DECH,' '));
				$this->excel->getActiveSheet()->SetCellValue('F' . $row, $ximport_line->_NUMP);
				$this->excel->getActiveSheet()->SetCellValue('G' . $row, $ximport_line->_COMP);
				$this->excel->getActiveSheet()->SetCellValue('H' . $row, $ximport_line->_LIBE);
				$this->excel->getActiveSheet()->SetCellValue('I' . $row, $ximport_line->_MONT);
				$this->excel->getActiveSheet()->SetCellValue('J' . $row, $ximport_line->_SENS);
				if ( $ximport_line->_SENS == 'D' )
					$mnt = $ximport_line->_MONT;
				else
					$mnt = $ximport_line->_MONT * (-1);
				$tot_DC += $mnt;
				$this->excel->getActiveSheet()->SetCellValue('K' . $row, $mnt);
				$this->excel->getActiveSheet()->SetCellValue('L' . $row, $ximport_line->_LETT);
				$this->excel->getActiveSheet()->SetCellValue('M' . $row, $ximport_line->_ANAL);
				$this->excel->getActiveSheet()->SetCellValue('N' . $row, $ximport_line->_MTVA);
				$this->excel->getActiveSheet()->SetCellValue('O' . $row, $ximport_line->_TTVA);
				$this->excel->getActiveSheet()->SetCellValue('P' . $row, $ximport_line->_CTVA);
				// données TVA
				$this->excel->getActiveSheet()->SetCellValue('Q' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('R' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('S' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('T' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('U' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('V' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('W' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('X' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('Y' . $row, '');

				if ( substr($ximport_line->_COMP, 0, 3) == '400') {
					$this->excel->getActiveSheet()->SetCellValue('Q' . $row, $ximport_line->_MONT);
					$tot_TOTAL += $ximport_line->_MONT;
				} else
					$this->excel->getActiveSheet()->SetCellValue('Q' . $row, '');

				if ( substr($ximport_line->_COMP, 0, 3) == '451') {
					if ( $ximport_line->_SENS == 'C' ) {
						$this->excel->getActiveSheet()->SetCellValue('R' . $row, $ximport_line->_MONT);
						$tot_MontTVA += $ximport_line->_MONT;
					} else {
						$this->excel->getActiveSheet()->SetCellValue('R' . $row, ($ximport_line->_MONT * -1));
						$tot_MontTVA -= $ximport_line->_MONT;
					}
				}
				if ( substr($ximport_line->_COMP, 0, 1) == '7') {
					switch ( $ximport_line->_CTVA ) {
						case '0':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('S' . $row, $ximport_line->_MONT);
								$tot_TVA_0 += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('S' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_0 -= $ximport_line->_MONT;
							}
							break;
						case '6':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('T' . $row, $ximport_line->_MONT);
								$tot_TVA_6 += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('T' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_6 -= $ximport_line->_MONT;
							}
							break;
						case '12':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('U' . $row, $ximport_line->_MONT);
								$tot_TVA_12 += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('U' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_12 -= $ximport_line->_MONT;
							}
							break;
						case '21':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('V' . $row, $ximport_line->_MONT);
								$tot_TVA_21 += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('V' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_21 -= $ximport_line->_MONT;
							}
							break;
						case 'inco':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('W' . $row, $ximport_line->_MONT);
								$tot_TVA_inco += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('W' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_inco -= $ximport_line->_MONT;
							}
							break;
						case 'hcee':
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('X' . $row, $ximport_line->_MONT);
								$tot_TVA_hcee += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('X' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_hcee -= $ximport_line->_MONT;
							}
							break;
						default :
							if ( $ximport_line->_SENS == 'C' ) {
								$this->excel->getActiveSheet()->SetCellValue('Y' . $row, $ximport_line->_MONT);
								$tot_TVA_autre += $ximport_line->_MONT;
							} else {
								$this->excel->getActiveSheet()->SetCellValue('Y' . $row, ($ximport_line->_MONT * -1));
								$tot_TVA_autre -= $ximport_line->_MONT;
							}
							break;
					}
				} else {
					//$this->excel->getActiveSheet()->SetCellValue('P' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('Q' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('R' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('S' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('T' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('U' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('V' . $row, '');
					//$this->excel->getActiveSheet()->SetCellValue('W' . $row, '');
				}

				$row++;
			}
			// totaux en bas de page
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('C' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('D' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('E' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('F' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('G' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('H' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('I' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('J' . $row, 'TOTAL GENERAL >>');
			$this->excel->getActiveSheet()->SetCellValue('K' . $row, $tot_DC);
			$this->excel->getActiveSheet()->SetCellValue('L' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('M' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('N' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('O' . $row, '');
			$this->excel->getActiveSheet()->SetCellValue('P' . $row, 'TOTAUX TVA >>');
			// données TVA
			$this->excel->getActiveSheet()->SetCellValue('Q' . $row, $tot_TOTAL);
			$this->excel->getActiveSheet()->SetCellValue('R' . $row, $tot_MontTVA);
			$this->excel->getActiveSheet()->SetCellValue('S' . $row, $tot_TVA_0);
			$this->excel->getActiveSheet()->SetCellValue('T' . $row, $tot_TVA_6);
			$this->excel->getActiveSheet()->SetCellValue('U' . $row, $tot_TVA_12);
			$this->excel->getActiveSheet()->SetCellValue('V' . $row, $tot_TVA_21);
			$this->excel->getActiveSheet()->SetCellValue('W' . $row, $tot_TVA_inco);
			$this->excel->getActiveSheet()->SetCellValue('X' . $row, $tot_TVA_hcee);
			$this->excel->getActiveSheet()->SetCellValue('Y' . $row, $tot_TVA_autre);

			$row++;
			$arrLength = count($xbadtrans);
			if ( $arrLength > 0 ) {
				$total_debit = 0.00;
				$total_credit = 0.00;
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, 'MVTS');
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, 'ID (sma_sales)');
				$this->excel->getActiveSheet()->SetCellValue('C' . $row, 'DEBIT');
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, 'CREDIT');
				$row++;
				foreach ($xbadtrans  as $xbadtrans_line) {
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $xbadtrans_line->_MVTS);
					$this->excel->getActiveSheet()->SetCellValue('B' . $row, $xbadtrans_line->_ID);
					$this->excel->getActiveSheet()->SetCellValue('C' . $row, $xbadtrans_line->_DEBIT);
					$this->excel->getActiveSheet()->SetCellValue('D' . $row, $xbadtrans_line->_CREDIT);
					$total_debit += $xbadtrans_line->_DEBIT;
					$total_credit += $xbadtrans_line->_CREDIT;
					$row++;
				}
				$this->excel->getActiveSheet()->SetCellValue('C' . $row, $total_debit);
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, $total_credit);
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $total_debit - $total_credit);
			}

			// https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // MVTS
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10); // ID (sma_)
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(10); // JOUR
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(18); // DECR
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25); // NUMP
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25); // COMP
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25); // LIBE
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); // MONT
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20); // SENS + total
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // MONT SIGNE
			$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(15); // CTVA + total

			$row--;
			$sele = 'Q1:Y' . $row;
			//$styleArray = array(
			//    'font'  => array(
			//      //  'bold'  => true,
			//        'color' => array('rgb' => 'FF0000'),
			//    ));

			$this->excel->getActiveSheet()->getStyle( $sele )->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()->setARGB('F2E4C7');

			$this->excel->getActiveSheet()->setSelectedCell('A2'); // on met le focus sur la cell 'A2'
			$this->excel->getActiveSheet()->freezePane('A2'); // freeze title line

			//$this->excel->getActiveSheet()
			//        ->getStyle( $sele )
			//        ->applyFromArray($styleArray);
			//         ->getFill()
			//        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			//        ->getStartColor()
			//        ->setARGB('ffffff');

			//$this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);

			$this->excel->getProperties()
			->setCreator("http://www.picsoo.be")
			->setLastModifiedBy("Picsoo POS")
			->setTitle("Picsoo POS exportation comptable détaillée")
			->setSubject("Picsoo POS exportation comptable détaillée")
			->setDescription( "Picsoo POS exportation comptable détaillée avec contrôle TVA, édité le " . date('d/m/Y') )
			->setKeywords("Picsoo Pos ventes tva")
			->setCategory("Fichier Picsoo POS");

			$filename = 'transactions_' . $this->reference_external .'_' . date('d_m_Y'); //date('Y_m_d_H_i_s');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);

			return;
		}

		// ==============================================================================================================================================
		// dump vers Picsoo web =========================================================================================================================
		// ==============================================================================================================================================
		//				Net		Amount	DebitCreditType
		// client 400	+		+		D
		// 7...			+		-		C
		// TVA 45..		+		-		C

		$recordcount = 0; //DBG
		$CleMvt = 1;
		$myList = array();
		$CurrentMvt = $this->ximportslist[0]->_MVTS;
		$ErrNumber = 0;
		$r_e = '';
		//$myfile = fopen("dbg.txt", "w") or die("Unable to open file!"); // DBG

		foreach ($this->ximportslist  as $ximport_line) {
	/*if( $ximport_line->_MVTS != 177 ) //DBG
	{
	$CurrentMvt = $ximport_line->_MVTS;
	continue;
	}*/

			if ( $CurrentMvt != $ximport_line->_MVTS ) {
				//fwrite($myfile, $myList); //DBG
				//if( $CurrentMvt==2 ) //DBG
				$result = $this->picsoo_ws->SaveTransactionsDetails($this->ie_clientsid, $myList, $r_e, $CurrentMvt);
				if ( $result['IsSuccess'] != true && $result['IsSuccess']!='' )
					$ErrNumber++;

				if ( $result['IsSuccess'] == false ) {
					if ( $result['Message'] == 'Authcode Expired' ) 
					{
						$msg = lang('export_finished') . ' - ' . lang('try_later') . ' (AE).';
						set_alert('danger', $msg );
						$logtype = 'error';
						log_message($logtype, 'Export transactions to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						return;
					} else if ( $result['Message'] == 'Data already exist.' ) {
						$msg = lang('export_finished') . ' - ' . lang('data_already_exist');
						set_alert('danger', $msg );
						$logtype = 'error';
						log_message($logtype, 'Export transactions to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						return;
					} else {
						$msg = lang('export_finished') . ' - ' . $result['Message'];
						set_alert('danger', $msg );
						$logtype = 'error';
						log_message($logtype, 'Export transactions to Picsoo aborted (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );
						return;
					}
				}

				$CurrentMvt = $ximport_line->_MVTS;
				$myList = array();
			}

			$ty = '2'; //$this->GetTypeJournal( $ximport_line->_MVTS );
			$transtype = '';
			switch ($ty) {
				case '1':
					$transtype = 'Purchase'; //"ACHATS";
					break;
				case '5':
					$transtype = 'Purchase'; //"CRACHATS";
					break;
				case '2':
					$transtype = 'Sale'; //"VENTES";
					break;
				case '6':
					$transtype = 'Sale'; //"CRVENTES";
					break;
				case '4':
					$transtype = 'Bank'; //"FINANCIERS";
					break;
				case '3':
					$transtype = 'Journal'; //"OD";
					break;
				default:
					$transtype = 'Journal';
					break;
			}

			$debit = 0.00;
			$credit = 0.00;
			$sens_montant = '';
			if ($ximport_line->_SENS == 'D') {
				$debit = $ximport_line->_MONT;
				$credit = 0.00;
				$sens_montant = 'Debit';
				$montant = $ximport_line->_MONT;
			} else {
				$credit = $ximport_line->_MONT;
				$debit = 0.00;
				$sens_montant = 'Credit';
				$montant = ($ximport_line->_MONT * -1);
			}

			$dt = substr( $ximport_line->_DECR, 0, 10); //new DateTime( $ximport_line->_DECR );
			$r_e = $this->reference_external . '_' . $dt /*->format('Y-m-d')*/ . '_' . $ximport_line->_NUMP;

			$datatosend = [
				'TransactionsId'			=> '0',
				'ClientsId'					=> $this->ie_clientsid,
				'FinancialPeriodId'			=> '',
				'AaccountNameId'			=> '',
				'CustomersId'				=> '',
				'EntityName'				=> $ximport_line->_LIBE,
				'TransactionCode'			=> $ximport_line->_NUMP,
				'TransactionType'			=> '', // ACHATS - VENTES - FINANCIERS - OD - CRACHATS - CRVENTES
				'JournalType'				=> $transtype,
				// transaction type (philippe 27/11/2019 @ 10:30) :
				// journal vente et crédit : Sale
				// achats et crédit : Purchase
				// OD : Journal
				// BQ : Cash
				// caisse : Cash
				'ReferenceId'				=> '',
				'TransactionDate'			=> $ximport_line->_DECR,
				'Description'				=> $ximport_line->_LIBE,
				'VateRate'					=> 0.00, // ??????????????????????????????????????????,
				'Net'						=> $debit - $credit,
				'Amount'					=> $montant,
				'DebitCreditType'			=> $sens_montant,
				'IsDeleted'					=> 'false',
				'ModifiedDate'				=> $ximport_line->_DECR,
				'BackupId'					=> '',
				'ReverseReferance'			=> '',
				'ReverseTransactions'		=> '',
				'MasterBranchCategoryId1'	=> '',
				'MasterBranchCategoryId2'	=> '',
				'CreatedDate'				=> '',
				'ModifiedBy'				=> '',
				'CreatedBy'					=> '',
				'VATTypeId'					=> '',
				'AccountName'				=> '',
				'CustomerCode'				=> '', //$ximport_line->_COMP28150,
				'InvoiceJournalCode'		=> $ximport_line->_JOUR,
				'BillSerivesGoodsId'		=> '',
				'JournalNumber'				=> $ximport_line->_NUMP,
				'VatPeriodId'				=> '',
				'Reference'					=> '',
				'DueDate'					=> '',
				'CurrencyRate'				=> '',
				'Currency'					=> '',
				'AmountCurrency'			=> '',
				'ProjectId'					=> '',
				'VatPeriod'					=> '',
				'Item'						=> '',
				'Qty'						=> '',
				'UnitPrice'					=> '',
				'Discount'					=> '',
				'ServicesGoods'				=> '',
				'vatNumber'					=> '',
				'EuropeanVatNumber'			=> '',
				'Employee'					=> '',
				'AccountCode'				=> $ximport_line->_COMP,
				'Flag'						=> '',
				'FlagDate'					=> '',
				'FlagUser'					=> '',
				'ExportFrom'				=> '',
				'TrackingNumber'			=> '',
				'BankDescription'			=> '',
				'BankRef'					=> '',
				'AccountGridType2'			=> '',
				'CreatedByName'				=> 'POS',
				'ModifiedByName'			=> 'POS',
				'Analyt'					=> 'ANALYT',
				'reference_external'		=> $r_e,  //$this->ie_reference . '_' .$ximport_line->_MVTS,
				'JournalType'				=> '',
				'TransactionCodeExternal'	=> '',
				'TransactionTypeExternal'	=> '',
				'ReconciliationCode'		=> '',

				'ReconciliationStatus'		=> '', // "Y" = reconcilié si LETT<>""; "N" = non reconcilié; "P" réconcilié partiellement
				'ReconciliationDate'		=> '',
				'ReconcilationId'			=> '',

				'ReminderLevel'				=> '',

				'VatCode'					=> $ximport_line->_CTVA,
				'VATPercent'				=> $ximport_line->_TVAT,
				'AccountGridType'			=> $ximport_line->_GBAS,
				'GVat'						=> $ximport_line->_GTVA,
			];

			$myList[] = $datatosend;
			$recordcount++;
		}

		//fclose($myfile);

		//if( $CurrentMvt==2 ) //DBG
		$result = $this->picsoo_ws->SaveTransactionsDetails($this->ie_clientsid, $myList, $r_e, $CurrentMvt); // le dernier

	/*if ( $this->picsoo_ws->IsDebug() )
	{
	$myfile = fopen("result_transactions.txt", "w") or die("Unable to open file!");
	fwrite($myfile, $result);
	fclose($myfile);
	}*/

		if ( $result['IsSuccess'] != true /*&& $result['IsSuccess']!=''*/ )
			$ErrNumber++;

		$msg = lang('export_finished');
		if ( $ErrNumber != 0 ) 
		{
			$msg .= ' (' . lang('errors_detected') . ' - ' . $ErrNumber . ' )';
			set_alert('danger', $msg );
			$logtype = 'error';
		} 
		else 
		{
			//$msg .= lang('awaiting_email');
			set_alert('success', $msg );
			$logtype = 'info';
		}

		log_message($logtype, 'Export transactions to Picsoo (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data'] );

		return $err;
	}

	private function GetTypeJournal( $_jour = '' )
	{
		$ret = '-1';

		return $ret;
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	// on ne retient dans vat list que ce qui concerne les ventes
	// --------------------------------------------------------------------------------------------------------------------------------
	private function CleanVatList()
	{
		foreach ($this->vatlist as $key => $value) {

			if ($value['Type'] != 'P') {

				unset($this->vatlist[$key]);
			}
		}
		Sort($this->vatlist);
		$cnt = count($this->vatlist);
		return;
	}
	
	// ===========================================================================================================================================================
	//
	// ===========================================================================================================================================================
	private function add_customers($customers = [])
	{
		$user_password = '12345';
		
		if (!empty($customers))
		{
			foreach ($customers as $picsoocustomer)
			{
				$adresses = $this->picsoo_ws->GetCustomerAddress($this->ie_clientsid, $picsoocustomer['customers_id']);
				$belgiuminfo = $this->picsoo_ws->GetCustomerBelgiumInformation($this->ie_clientsid, $picsoocustomer['customers_id']);

				$cc = '';
				if ( isset($picsoocustomer['Customercode']) )
					$cc = trim($picsoocustomer['Customercode']);
				else
				{
					if ( $customertype == 'C' )
						$cc = "400000";
					else
						$cc = "440000";
				}
				
	            $customer = [
	            	'firstname'				=> '',
	            	'lastname'				=> '',
	                'email'               	=> isset($picsoocustomer['email_address']) ? trim($picsoocustomer['email_address']) : '',
	                'contact_phonenumber'	=> isset($picsoocustomer['primary_contact']) ? trim($picsoocustomer['primary_contact']) : '',
	                'title'					=> '',
	                'company'             	=> isset($picsoocustomer['customer_company_name']) ? trim($picsoocustomer['customer_company_name']) : '',
	                'vat' 	             	=> isset($belgiuminfo['VATNumber']) ? trim($belgiuminfo['VATNumber']) : '',
	                'phonenumber'			=> isset($picsoocustomer['primary_contact']) ? trim($picsoocustomer['primary_contact']) : '',
	                'country'             	=> isset($adresses['country']) ? trim($adresses['country']) : '',
	                'city'                	=> isset($adresses['ccity']) ? trim($adresses['ccity']) : '',
	                'zip'		         	=> isset($adresses['cpostcode']) ? trim($adresses['cpostcode']) : '',
	                'state'               	=> isset($adresses['state_name']) ? trim($adresses['state_name']) : '',
	                'address'             	=> isset($adresses['address_1']) ? trim($adresses['address_1']) : '',
					'website'               => '',
					'billing_street'		=> '',
					'billing_city'			=> '',
					'billing_state'			=> '',
					'billing_zip'			=> '',
					'billing_country'		=> '',
					'shipping_street'		=> '',
					'shipping_city'			=> '',
					'shipping_state'			=> '',
					'shipping_zip'			=> '',
					'shipping_country'		=> '',
					'longitude'				=> '',
					'stripe_id'				=> '',
	            ];
	            $var1 = $customer['vat'];
				
                $exists = (bool) (total_rows(db_prefix() . 'clients', ['company' => trim($picsoocustomer['company'])]) > 0);
				
		        if ( $exists == FALSE) 
		        {
			        //if (!has_permission('customers', '', 'create')) 
			        //{
		    	    //    access_denied('customers');
		        	//}

			        $id = $this->clients_model->add($customer);
		        	if ($id) 
		        	{
			            //set_alert('success', _l('added_successfully', _l('client')));
		    	        //if ($save_and_add_contact == false) 
		    	        //{
			            //    redirect(admin_url('clients/client/' . $id));
		    	        //} 
		    	        //else 
		    	        //{
			            //    redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));
		            	//}
		        	}
		    	} 
		    	else 
		    	{
			        //if (!has_permission('customers', '', 'edit')) 
			        //{
			        //    if (!is_customer_admin($id)) 
			        //    {
			        //        access_denied('customers');
		            //	}
		        	//}
		        	$success = $this->clients_model->update($customer, $id);
		        	if ($success == true) 
		        	{
			        //    set_alert('success', _l('updated_successfully', _l('client')));
		        	}
		        	//redirect(admin_url('clients/client/' . $id));
		    	}
			}
		}
	}
}