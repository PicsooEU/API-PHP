<?php

defined('BASEPATH') or exit('No direct script access allowed');

//@ini_set('memory_limit', '512M');
//@ini_set('max_execution_time', 0);
//@ini_set('default_socket_timeout', 6000);

/*
 *  ==============================================================================
 *  Author	: Picsoo.eu (Dominique HUGO)
 *  Email	: dev@picsoo.eu
 *  ==============================================================================
 */
class Picsoo_ws
{
	private $DEMO_VRS						= 0;
	private $DUMP_DBG						= 0;
    /*----------------------------------------------------------------------*/
    private $LIVE                            = 0;  // 1 = LIVE -- 0 = STAGING
    /*----------------------------------------------------------------------*/
    
    private $lang                            = 'en';
    
    private $AuthenticationCode              = '';
    
    private $ApplicationKey                  = 'C5C121CF-8625-4B3C-9D69-E99BA2AA0866';
    private $UserName                        = '';
    private $Password                        = '';

    private $urlpicsoo;
    
    /* URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL URL */
    
    private $URL_baseapi;
    private $URL_LoginApplicationKey;

    private $URL_GetTable_Clients;
    private $URL_GetTable_ClientsByParameter;

    private $URL_GetCompanyInfo;
    private $URL_ClearAllDataForGivenCompany;
    private $URL_SQLStatement;

    private $URL_SaveCustomer;
    private $URL_SaveSupplier;
    private $URL_SaveChartOfAccount;
    private $URL_SaveJournalCode;
    private $URL_SaveTransactionsDetails; //https
    private $URL_SaveItemDetails;
    private $URL_SaveFinancialPeriod;
    private $URL_SaveContactCategory; //https
    private $URL_SaveCustomerContact; //https
    private $URL_SaveItemCategory; //https
    private $URL_SaveItemImageImportData;

    private $URL_GetCustomerSupplierInfo; // https://beapi.mindoo.co/api/Services/GetCompanyInfo?ClientId=99999&Type=&Name=&VAT=&Phone=
    private $URL_GetCompanyList;

    private $URL_SaveCustomerList;
    private $URL_SaveSupplierList;
    private $URL_SaveItemList;
    
	private $URL_StockAdjustement;

	private $URL_GetVatList;

	private $URL_GetContactsList;

	private $URL_GetInvoiceData;

	private $URL_GetDatabaseTable;
	private $URL_GetInvoiceBillDetails;

	private $URL_SaveCustomerContactsList;
    
	private $URL_GetRowsCount;

	private $URL_GetItemImage;

	private $URL_GetListForMultipleTable;

    public function __construct($params='')
    {
        if( $this->LIVE )
        {
            /* live */
            //$this->urlpicsoo                       = "https://cloud.picsoo.eu";
			$this->urlpicsoo                       = "https://be.mindoo.co";
            
			$this->URL_baseapi                     = "https://beapi.mindoo.co/api/Services/";
			$this->URL_LoginApplicationKey         = "https://beapi.mindoo.co/api/Services/LoginApplicationKey";

			$this->URL_GetTable_Clients            = "https://beapi.mindoo.co/api/Services/GetTableData?ClientId=10055&Table=clients"; //https
			$this->URL_GetTable_ClientsByParameter = "https://beapi.mindoo.co/api/Services/GeTableDataByParameter?ClientId=10055&Table=clients";

            $this->URL_GetCompanyInfo              = "https://beapi.mindoo.co/api/Services/GetCompanyInfo"; //https
            $this->URL_ClearAllDataForGivenCompany = "https://beapi.mindoo.co/api/Services/ClearAllDataForGivenCompany";
            $this->URL_SQLStatement                = "https://beapi.mindoo.co/api/Services/SQLStatement"; //https

            $this->URL_SaveCustomer                = "https://beapi.mindoo.co/api/Services/SaveCustomer"; //https
            $this->URL_SaveSupplier                = "https://beapi.mindoo.co/api/Services/SaveSupplier"; //https
            $this->URL_SaveChartOfAccount          = "https://beapi.mindoo.co/api/Services/SaveChartOfAccount"; //https
            $this->URL_SaveJournalCode             = "https://beapi.mindoo.co/api/Services/SaveJournalCode";
            $this->URL_SaveTransactionsDetails     = "https://beapi.mindoo.co/api/Services/SaveTransactionsDetails"; //https
            $this->URL_SaveItemDetails             = "https://beapi.mindoo.co/api/Services/SaveItemDetails";
            $this->URL_SaveFinancialPeriod         = "https://beapi.mindoo.co/api/Services/SaveFinancialPeriod";
            $this->URL_SaveContactCategory         = "https://beapi.mindoo.co/api/Customer/SaveContactCategory"; //https
            $this->URL_SaveCustomerContact         = "https://beapi.mindoo.co/api/Customer/SaveCustomerContact"; //https
            $this->URL_SaveItemCategory            = "https://beapi.mindoo.co/api/Item/SaveItemCategory"; //https
            $this->URL_SaveItemImageImportData     = "https://beapi.mindoo.co/api/Item/SaveItemImageImportData";

			//v1.1.42
            $this->URL_GetCustomerSupplierInfo     = "https://beapi.mindoo.co/api/Services/GetCompanyInfo"; // https://beapi.mindoo.co/api/Services/GetCompanyInfo?ClientId=99999&Type=&Name=&VAT=&Phone=
            $this->URL_GetCompanyList              = "https://beapi.mindoo.co/api/Services/GetCompanyList";

			//v1.1.51
            $this->URL_SaveCustomerList            = "https://beapi.mindoo.co/api/Services/SaveCustomerList";
            $this->URL_SaveSupplierList            = "https://beapi.mindoo.co/api/Services/SaveSupplierList";
            $this->URL_SaveItemList                = "https://beapi.mindoo.co/api/Services/SaveItemList";

			//v1.1.76
            $this->URL_DeleteChartOfAccount        = "https://beapi.mindoo.co/api/Services/DeleteChartOfAccount";

			//v1.2.1
            $this->URL_DeleteCustomerSupplierList  = "https://beapi.mindoo.co/api/Services/DeleteCustomerSupplierList";
			$this->URL_CheckAccountCodeExistOrNot  = "https://beapi.mindoo.co/api/Services/CheckAccountCodeExistOrNot";
			
			$this->URL_StockAdjustement			   = "https://beapi.mindoo.co/api/Services/StockAdjustment";

			$this->URL_GetVatList				   = "https://beapi.mindoo.co/api/Services/GetVatList";

			//v
			$this->URL_GetContactsList			   = "https://beapi.mindoo.co/api/Services/GetContactsList";
			
			$this->URL_GetInvoiceData			   = "https://beapi.mindoo.co/api/Services/GetInvoiceData";

			$this->URL_GetDatabaseTable			   = "https://beapi.mindoo.co/api/Services/GetDatabaseTable";
			$this->URL_GetInvoiceBillDetails	   = "https://beapi.mindoo.co/api/InvoiceBill/InvoiceBillDetails";
			
			$this->URL_SaveCustomerContactsList    = "https://beapi.mindoo.co/api/Services/SaveCustomerContactsList";

			$this->URL_GetRowsCount			  	   = "https://beapi.mindoo.co/api/Services/GetRowsCount";

			$this->URL_GetItemImage			  	   = "https://beapi.mindoo.co/api/Item/GetItemImage";

			$this->URL_GetListForMultipleTable	   = "https://beapi.mindoo.co/api/Services/GetListForMultipleTable";
		}
        else
        {
            /* staging */
			$this->urlpicsoo                       = "https://stagingbe.mindoo.co/";

            $this->URL_baseapi                     = "https://stagingbeapi.mindoo.co/api/Services/";
            $this->URL_LoginApplicationKey         = "https://stagingbeapi.mindoo.co/api/Services/LoginApplicationKey";

            $this->URL_GetTable_Clients            = "https://stagingbeapi.mindoo.co/api/Services/GetTableData?ClientId=10055&Table=clients"; //https
            $this->URL_GetTable_ClientsByParameter = "https://stagingbeapi.mindoo.co/api/Services/GeTableDataByParameter?ClientId=10055&Table=clients";

            $this->URL_GetCompanyInfo              = "https://stagingbeapi.mindoo.co/api/Services/GetCompanyInfo"; //https
            $this->URL_ClearAllDataForGivenCompany = "https://stagingbeapi.mindoo.co/api/Services/ClearAllDataForGivenCompany";
            $this->URL_SQLStatement                = "https://stagingbeapi.mindoo.co/api/Services/SQLStatement"; //https

            $this->URL_SaveCustomer                = "https://stagingbeapi.mindoo.co/api/Services/SaveCustomer"; //https
            $this->URL_SaveSupplier                = "https://stagingbeapi.mindoo.co/api/Services/SaveSupplier"; //https
            $this->URL_SaveChartOfAccount          = "https://stagingbeapi.mindoo.co/api/Services/SaveChartOfAccount"; //https
            $this->URL_SaveJournalCode             = "https://stagingbeapi.mindoo.co/api/Services/SaveJournalCode";
            $this->URL_SaveTransactionsDetails     = "https://stagingbeapi.mindoo.co/api/Services/SaveTransactionsDetails"; //https
            $this->URL_SaveItemDetails             = "https://stagingbeapi.mindoo.co/api/Services/SaveItemDetails";
            $this->URL_SaveFinancialPeriod         = "https://stagingbeapi.mindoo.co/api/Services/SaveFinancialPeriod";
            $this->URL_SaveContactCategory         = "https://stagingbeapi.mindoo.co/api/Customer/SaveContactCategory"; //https
            $this->URL_SaveCustomerContact         = "https://stagingbeapi.mindoo.co/api/Customer/SaveCustomerContactmaybe "; //https
            $this->URL_SaveItemCategory            = "https://stagingbeapi.mindoo.co/api/Item/SaveItemCategory"; //https
            $this->URL_SaveItemImageImportData     = "https://stagingbeapi.mindoo.co/api/Item/SaveItemImageImportData";

			//v1.1.42
            $this->URL_GetCustomerSupplierInfo     = "https://stagingbeapi.mindoo.co/api/Services/GetCompanyInfo"; // https://beapi.mindoo.co/api/Services/GetCompanyInfo?ClientId=99999&Type=&Name=&VAT=&Phone=
            $this->URL_GetCompanyList              = "https://stagingbeapi.mindoo.co/api/Services/GetCompanyList";

			//v1.1.51
            $this->URL_SaveCustomerList            = "https://stagingbeapi.mindoo.co/api/Services/SaveCustomerList";
            $this->URL_SaveSupplierList            = "https://stagingbeapi.mindoo.co/api/Services/SaveSupplierList";
            $this->URL_SaveItemList                = "https://stagingbeapi.mindoo.co/api/Services/SaveItemList";

            $this->URL_GeTClientsByAccountant      = "https://stagingbeapi.mindoo.co//api/Services/GeTClientsByAccountant?accountant_email=";
			$this->URL_GeTXmlTvaFile               = "https://stagingbeapi.mindoo.co/api/Services/GetVATReportXML";

			//v1.1.76
            $this->URL_DeleteChartOfAccount        = "https://stagingbeapi.mindoo.co/api/Services/DeleteChartOfAccount";

			//v1.2.1
            $this->URL_DeleteCustomerSupplierList  = "https://stagingbeapi.mindoo.co/api/Services/DeleteCustomerSupplierList";
			$this->URL_CheckAccountCodeExistOrNot  = "https://stagingbeapi.mindoo.co/api/Services/CheckAccountCodeExistOrNot";
			
			$this->URL_StockAdjustement			   = "https://stagingbeapi.mindoo.co/api/Services/StockAdjustment";

			$this->URL_GetVatList				   = "https://stagingbeapi.mindoo.co/api/Services/GetVatList";
			
			//v
			$this->URL_GetContactsList			   = "https://stagingbeapi.mindoo.co/api/Services/GetContactsList";

			$this->URL_GetInvoiceData			   = "https://stagingbeapi.mindoo.co/api/Services/GetInvoiceData";

			$this->URL_GetDatabaseTable			   = "https://stagingbeapi.mindoo.co/api/Services/GetDatabaseTable";
			$this->URL_GetInvoiceBillDetails	   = "https://stagingbeapi.mindoo.co/api/InvoiceBill/InvoiceBillDetails";

			$this->URL_SaveCustomerContactsList    = "https://stagingbeapi.mindoo.co/api/Services/SaveCustomerContactsList";

			$this->URL_GetRowsCount			  	   = "https://stagingbeapi.mindoo.co/api/Services/GetRowsCount";

			$this->URL_GetItemImage			  	   = "https://stagingbeapi.mindoo.co/api/Item/GetItemImage";

			$this->URL_GetListForMultipleTable	   = "https://stagingbeapi.mindoo.co/api/Services/GetListForMultipleTable";
		}
        //$this->lang = $params['lang'];
        
        //$AuthenticationCode = $this->GetAuthentificationCode();
    }

    public function testtest($string)
    {
        return 'testtest';
    }

    public function GetPicsooURL()
    {
        return $this->urlpicsoo;
    }

	public function IsStaging()
	{
		if ( $this->LIVE )
			return false;
		else
			return true;		
	}

	public function IsDemoVersion()
	{
		return $this->DEMO_VRS;
	}
	
	public function IsDebug()
	{
		return $this->DUMP_DBG;
	}

	public function CheckPicsooAccess()
	{
		if( $this->AuthenticationCode == "na" )
			return false;

		return true;
	}
    
    public function SetUsernamePsw( $_usr, $_psw )
    {
		if( $this->DEMO_VRS == 1 )
			return; 
		
    	$this->UserName = $_usr;
    	$this->Password = $_psw;
    	
    	$this->AuthenticationCode = $this->GetAuthentificationCode();
    }
    
    public function PicsooEMail()
    {
    	return $this->UserName;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetCompanyInfo( $clientid )
    {
		$param = array();
		
		if ( $clientid == '' )
			return '';

		//$this->AuthenticationCode = $this->GetAuthentificationCode();
		$param += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$param += [ 'ClientId' => $clientid ];

		$url = $this->URL_GetCompanyInfo;
		$json_data = json_encode($param, true);

		$json = $this->file_post_contents( $url, $json_data );
		$json_data = json_decode($json, true);

		return $json_data;
	}



	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
    private function GetAuthentificationCode()
    {
        $aut = array
        (
            'AppicationKey' => $this->ApplicationKey,
            'Password' => $this->Password,
            'UserName' => $this->UserName
        );
       
        $response = $this->HttpRequest( $aut );
        $json_data = json_decode( $response, true );
        $this->AuthenticationCode = $json_data["Data"];
        
		return $this->AuthenticationCode;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la table des invoices SANS LE DETAIL pour un fk_clients_id et de date à date
	//
	// Request Type = GET
	//
	// Parameter:
	// {
	//	"ClientId":"10568",
	//	"ItemId":"157054",
  	// 	"AuthenticationCode": "Uhjzggj8xsE7CDSnsWHYykrAqBg08iGm"
	// }
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetInvoicesList( $clientid = '', $startdate = '', $enddate = '' )
    {
		$param = array();
		
		if ( $clientid == '' )
			return '';

		$url = $this->URL_GetListForMultipleTable . "?ClientId=" . $clientid . "&Table=invoice" . "&AuthenticationCode=" . $this->AuthenticationCode . "&StartDate=" . $startdate . "&EndDate=" . $enddate ;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getinvoiceslist_" . $table . "_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"]["RecordList"];

		return $data_list;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la table des clients pour un fk_clients_id
	//
	// Request Type = GET
	//
	// Parameter:
	// {
	//	"ClientId":"10568",
	//	"ItemId":"157054",
  	// 	"AuthenticationCode": "Uhjzggj8xsE7CDSnsWHYykrAqBg08iGm"
	// }
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetCustomersList( $clientid = '', $customerid = '' )
    {
		$param = array();
		
		if ( $clientid == '' )
			return '';

		$url = $this->URL_GetListForMultipleTable . "?ClientId=" . $clientid . "&Table=customers" . "&Id=" . $customerid . "&AuthenticationCode=" . $this->AuthenticationCode;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getcustomerslist_" . $table . "_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"]["RecordList"];

		return $data_list;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne des Table
	// PLUS UTILISE
	//
	// Request Type = GET
	//
	// Parameter:
	// {
	//	"ClientId":"10568",
	//	"ItemId":"157054",
  	// 	"AuthenticationCode": "Uhjzggj8xsE7CDSnsWHYykrAqBg08iGm"
	// }
	// ----------------------------------------------------------------------------------------------------------------------
    private function GetListForMultipleTable( $clientid = '', $table = '', $customerid = '' )
    {
		$param = array();
		
		if ( $clientid == '' || $table == '' )
			return '';

		//$this->AuthenticationCode = $this->GetAuthentificationCode();

		$url = $this->URL_GetListForMultipleTable . "?ClientId=" . $clientid . "&Table=" . $table . "&Id=" . $customerid . "&AuthenticationCode=" . $this->AuthenticationCode;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getlistformultipletable_" . $table . "_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"]["RecordList"];

		return $data_list;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne l'image d'un item en passant le client id et l'item id'
	//
	// Request Type = GET
	//
	// Parameter:
	// {
	//	"ClientId":"10568",
	//	"ItemId":"157054",
  	// 	"AuthenticationCode": "Uhjzggj8xsE7CDSnsWHYykrAqBg08iGm"
	// }
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetItemImage( $clientid = '', $itemid = '' )
    {
		$param = array();
		
		if ( $clientid == '' || $itemid == '' )
			return '';

		//$this->AuthenticationCode = $this->GetAuthentificationCode();

		$url = $this->URL_GetItemImage . "?ClientId=" . $clientid . "&ItemId=" . $itemid . "&AuthenticationCode=" . $this->AuthenticationCode;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getimageitem_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"];

		return $json_data;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne le nombre de rows d'une table en passant des infos where
	//
	// Request Type = POST
	//
	// Parameter:
	// {
    // "Table":"Invoice",
    // "where":"fk_clients_id='10568' and invoice_date ='2020-04-21'"
	// }
	// $data = $this->picsoo_ws->GetRowsCount( "transactions", "fk_clients_id='10929' and transaction_type='Cash'");
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetRowsCount( $tablename = '', $sqlstatement = '' )
    {
		$param = array();
		
		if ( $sqlstatement == '' || $tablename == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$param += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$param += [ 'Table' => $tablename ];
		$param += [ 'Where' => $sqlstatement ];

		$url = $this->URL_GetRowsCount;
		$json_data = json_encode($param, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getrowscount_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getrowscountresult_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
		$data = $json_data["Data"];

		return $data[0][Column1];
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne le contenu d'une table en passant des infos where
	// post example :
	// {
	// "AuthenticationCode":"EZGm6sx9fKtNaYxxiI5VstnihCbw6Nws7+wu9TmcTV0=",
	// "ClientId":"10929",
	// "Table":"invoice",
	// "Where":"is_deleted = '0' and (invoice_id = 21347 or invoice_id = 21346) order by invoice_id desc"
	// }
	// {
	// "AuthenticationCode":"EZGm6sx9fKtNaYxxiI5VstnihCbw6Nws7+wu9TmcTV0=",
	// "ClientId":"10929",
	// "Table":"invoice_datail",
	// "Where":"is_deleted = '0' and fk_invoice_id = 82183"
	// }
	// $this->picsoo_ws->GetDatabaseTable( $this->ie_clientsid, 'invoice', "is_deleted = '0' and (invoice_id = 21347 or invoice_id = 21346) order by invoice_id desc" );
	// $this->picsoo_ws->GetDatabaseTable( $this->ie_clientsid, 'invoice_datail', "is_deleted = '0' and fk_invoice_id = 82183");
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetDatabaseTable( $clientid = '', $tablename = '', $sqlstatement = '' )
    {
		$param = array();
		
		if ( $clientid == '' || $tablename == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$param += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$param += [ 'ClientId' => $clientid ];
		$param += [ 'Table' => $tablename ];
		$param += [ 'Where' => $sqlstatement ];

		$url = $this->URL_GetDatabaseTable;
		$json_data = json_encode($param, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getdatabasetable_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_databasetable_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
		$data = $json_data["Data"];

		return $data;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne les paiements pour un InvoiceId
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetInvoiceBillDetails( $clientid = '', $invoiceid = '' )
    {
		$param = array();
		
		if ( $clientid == '' || $invoiceid == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$param += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$param += [ 'ClientId' => $clientid ];
		$param += [ 'InvoiceId' => $invoiceid ];

		$url = $this->URL_GetInvoiceBillDetails;
		$json_data = json_encode($param, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getinvoicebilldetails_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_invoicebilldetails_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
		$data = $json_data["Data"]["PaymentHistory"];

		return $data;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne les invoices pour un customer code entre fourchette de data
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetInvoiceData( $clientid = '', $customercode = '', $startdate = '', $enddate = '' )
    {
		$param = array();
		
		if ( $clientid == '' || $customercode == '' || $startdate == '' || $enddate == '' || $this->AuthenticationCode == '' )
			return '';
		
		if( strlen($startdate) == 11 )
			$startdate .= "00:00:00";
		if( strlen($enddate) == 11 )
			$startdate .= "23:59:59";

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$param += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$param += [ 'CustomerCode' => $customercode ];
		$param += [ 'ClientId' => $clientid ];
		$param += [ 'StartDate' => $startdate ];
		$param += [ 'EndDate' => $enddate ];

		$url = $this->URL_GetInvoiceData;
		$json_data = json_encode($param, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_getinvoicedata_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_invoices_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
		$data_invoices = $json_data["Data"];

		return $data_invoices;
    }
        
	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste compléte de toutes les compagnies
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetCompaniesList()
    {
        $json = file_get_contents($this->URL_GetTable_Clients);
        $json_data = json_decode($json, true);

        $data_clients = $json_data["Data"];

        //echo ( "Decoded"."<br>" );

        //echo "IsSuccess: ". $json_data["IsSuccess"]."<br>";
        //echo "Message: ". $json_data["Message"]."<br>"."<br>"."<br>";

        //foreach ($data_clients as $clients_id)
        //{
        //    echo $clients_id['clients_id']." ".$clients_id['organisation_name']."<br>";
        //}

        //echo $json_data->IsSuccess;

        // https://stackoverflow.com/questions/48095298/php-json-get-key-and-value
        //foreach($json_data as $key => $val) 
        //{
            //echo "KEY IS: $key<br/>";
            //foreach(((array)$json_data)[$key] as $val2) 
            //{
                //echo "VALUE IS: $val2<br/>";
            //}
        //}
        return $data_clients;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste de toutes les compagnies associées à une adresse email
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetCompaniesListByEmail( $email = '' )
    {
        $url = $this->URL_GetTable_ClientsByParameter . "&email=" . $email;
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_clients.txt", "w") or die("Unable to open file!");
			foreach ($json_data["Data"] as $key => $value )
			{
				$var1 = $key;
				$var2 = $value;
				fwrite($myfile, $value['clients_id']." ".$value['organisation_name'] . PHP_EOL);
			}
			fclose($myfile);
		}
        
        $data_clients = $json_data["Data"];
        
        return $data_clients;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste compléte des paramètres TVA pour un client_id
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetValListByWS( $clientid = '' )
	{
		if ( $clientid == '' || $this->AuthenticationCode == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$itemslist += [ 'AuthenticationCode' => $this->AuthenticationCode ];

		$url = $this->URL_GetVatList;
		$json_data = json_encode($itemslist, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_vatlist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		//$json = file_get_contents($url, false, $json_data);
		$json_data = json_decode($json, true);

		//$data_items = $json_data["Data"];

		return $json_data;

		/*
		if ( $clientid == '' || $categoryid == '' || $this->AuthenticationCode == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$url = $this->URL_SQLStatement;
		$url .= "?AuthenticationCode=" . $this->AuthenticationCode;
		$url .= "&RequestedStatement=" . 'SELECT [item_category_id],[name],[fk_client_id] FROM [EasyBelgium].[dbo].[item_category] where fk_client_id=' . $clientid . 'and item_category_id=' . $categoryid;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		$data_items = $json_data["Data"];

		return $data_items;
		*/
	}

	public function GetValList( $clientid = '' )
	{
		if ( $clientid == '' )
			return '';

		$url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=VATRateMaster";
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_vatlist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"];

		return $data_list;
	}
	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste compléte des paramètres journaux pour un client_id
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetJournalList( $clientid = '' )
	{
		if ( $clientid == '' )
			return '';

		$url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=JournalCode";
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_journallist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$data_list = $json_data["Data"];

		return $data_list;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste compléte des catégories/familles d'items/articles pour un client_id
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCategoriesList( $clientid = '' )
	{
		if ( $clientid == '' )
			return '';

		$url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=item_category";
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_categorylist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}

		$data_list = $json_data["Data"];

		return $data_list;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la liste des items/articles pour un client_id
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetItemsList( $clientid = '' )
    {
        if( $clientid == '' )
            return '';
        
        $url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=Items";
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_items_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
        
        $data_items = $json_data["Data"];

        return $data_items;
    }
    
	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function SaveItems( $clientid = '', $itemslist = '' )
    {
        if( $clientid == '' || $itemslist == '' || $this->AuthenticationCode == '' )
            return '';
        
		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$itemslist += [ 'AuthenticationCode' => $this->AuthenticationCode ];
        
        $url = $this->URL_SaveItemDetails;
        $json_data = json_encode($itemslist, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_items_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, print_r($json_data,true));
			fclose($myfile);
		}
        
        $json = $this->file_post_contents( $url, $json_data );
        //$json = file_get_contents($url, false, $json_data);
        $json_data = json_decode($json, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("result_items_log.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, print_r($json_data, true));
			fwrite($myfile, date("h:i:s") . " - " . $clientid . ' msg : [' . $json_data['Message'] . '] - ' . $ie_reference . '_' . $mvts . "\n" );
			fclose($myfile);
		}
		
        return $json_data;
    }    

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function StockAdjustement( $clientid = '', $quantity = '')
	{
		if ( $clientid == '' || $quantity == '' || $this->AuthenticationCode == '' )
			return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$quantity += [ 'AuthenticationCode' => $this->AuthenticationCode ];
		$quantity += [ 'ClientId' => $clientid ];

		$url = $this->URL_StockAdjustement;
		$json_data = json_encode($quantity, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_StockAdjustement_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

		$json = $this->file_post_contents( $url, $json_data );
		//$json = file_get_contents($url, false, $json_data);
		$json_data = json_decode($json, true);

		return $json_data;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
    public function SaveItemsList( $clientid = '', $itemslist = '')
    {
        if( $clientid == '' || $itemslist == '' || $this->AuthenticationCode == '' )
            return '';
        
        //$customerslist = [ 'AuthenticationCode' => $this->AuthenticationCode ] + [ 'ClientId' => $clientid ] + array('List'=>$customerslist);
        
        $url = $this->URL_SaveItemList;
            
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("url_saveitemslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $url);
			fwrite($myfile, "\n");
			fclose($myfile);
		}
		
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_items_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
			//file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
		}
        
        $json_data = json_encode([ 'AuthenticationCode' => $this->AuthenticationCode ] + [ 'ClientId' => $clientid ] + [ 'List' => $itemslist ] );
        
        /* fonctionne !!!
        $value = array(
            "AuthenticationCode"=>$this->AuthenticationCode,
            "ClientId"=>$clientid,
            "List"=>$customerslist);
        $json_data = json_encode($value); */
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_items_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
			//file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
		}

        
        $json = $this->file_post_contents( $url, $json_data );
        //$json = file_get_contents($url, false, $json_data);
        $json_data = json_decode($json, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("result_items_log.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, print_r($json_data, true));
			fwrite($myfile, date("h:i:s") . " - " . $clientid . ' msg : [' . $json_data['Message'] . '] - ' . $ie_reference . '_' . $mvts . "\n" );
			fclose($myfile);
		}
		
		//$data_items = $json_data["Data"];

        return $json_data;
    } 
    
	// ----------------------------------------------------------------------------------------------------------------------
	// retourne la table des customers pour un client fk_clients_id
	// DEPRECIATED - remplacé avec store procedure du côté de Picsoo
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomersListOLD( $clientid = '' )
    {
        if( $clientid == '' )
            return '';
        
        $url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=Customer";
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

        $data_items = $json_data["Data"];

        return $data_items;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomerAddress( $clientid = '', $customerid = '' )
    {
        if( $clientid == '' || $customerid == '' )
            return '';

        $url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=CustomerAddress&Id=" . $customerid;
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

        $data_items = $json_data["Data"];

		if( isset($data_items[0]))
        	return $data_items[0];
		
		return '';
    }

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomerBelgiumInformation( $clientid = '', $customerid = '' )
    {
        if( $clientid == '' || $customerid == '' )
            return '';

        $url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=customersbelgiuminformation&Id=" . $customerid;
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

        $data_items = $json_data["Data"];

		if( isset($data_items[0]))
	        return $data_items[0];
		
		return '';
    }

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// select * from CustomerContacts
	// where
	// ParentId in (select customers_id from customers where customer_company_name='@ Green for ever' and fk_clients_id=10568)
	//
	// select cc.* from CustomerContacts cc
	// inner join customers c on c.customers_id=cc.ParentId
	// where c.fk_clients_id=10776
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomerContactsList( $clientid = '', $customerid = '' )
	{
		if ( $clientid == '' || $customerid == '' )
			return '';

        $url = $this->URL_GetContactsList . "?ParentID=" . $customerid;
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_contacts.txt", "w") or die("Unable to open file!");
			foreach ($json_data["Data"] as $key => $value )
			{
				$var1 = $key;
				$var2 = $value;
				fwrite($myfile, $value['Id']." ".$value['ParentId']." ".$value['FirstName']." ".$value['LastName'] . PHP_EOL);
			}
			fclose($myfile);
		}
        
        $data_contacts = $json_data["Data"];
        
        return $data_contacts;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomerTaxInformation( $clientid = '', $customerid = '' )
	{
		if ( $clientid == '' || $customerid == '' )
			return '';

		$url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=CustomerTaxInformation&Id=" . $customerid;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		$data_items = $json_data["Data"];

		return $data_items;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function GetCustomerBankInformation( $clientid = '', $customerid = '' )
	{
		if ( $clientid == '' || $customerid == '' )
			return '';

		$url = $this->URL_baseapi . "GetTableData?ClientId=" . $clientid . "&Table=CustomerBankInformation&Id=" . $customerid;
		$json = file_get_contents($url);
		$json_data = json_decode($json, true);

		$data_items = $json_data["Data"];

		return $data_items;
	}

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function SaveChartOfAccount( $clientid = '', $accountdata = '' )
    {
        if( $clientid == '' || $accountdata == '' || $this->AuthenticationCode == '' )
            return '';

        $accountdata += [ 'AuthenticationCode' => $this->AuthenticationCode ];
        $accountdata += [ 'ClientId' => $clientid ];

		$url = $this->URL_SaveChartOfAccount;
            
        $json_data = json_encode($accountdata, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_customers_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}
        
        $json = $this->file_post_contents( $url, $json_data );
        $json_data = json_decode($json, true);
        
        return $json_data;
    }    

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function SaveCustomers( $clientid = '', $customerdata = '', $customerstype = '' )
    {
        if( $clientid == '' || $customerdata == '' || $customerstype == '' || $this->AuthenticationCode == '' )
            return '';

        $customerdata += [ 'AuthenticationCode' => $this->AuthenticationCode ];
        $customerdata += [ 'ClientId' => $clientid ];

        if( $customerstype == 'C' )
            $url = $this->URL_SaveCustomer;
        else
            $url = $this->URL_SaveSupplier;
            
        $json_data = json_encode($customerdata, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_customers_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}
        
        $json = $this->file_post_contents( $url, $json_data );
        $json_data = json_decode($json, true);
        
        return $json_data;
    }    
    
	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function SaveCustomersSuppliersList( $clientid = '', $customerslist = '', $customerstype = '' )
    {
        if( $clientid == '' || $customerslist == '' || $customerstype == '' || $this->AuthenticationCode == '' )
            return '';
        
        if( $customerstype == 'C' )
            $url = $this->URL_SaveCustomerList;
        else
            $url = $this->URL_SaveSupplierList;

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("url_customerslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $url);
			fwrite($myfile, "\n");
			fclose($myfile);
		}

        $json_data = json_encode([ 'AuthenticationCode' => $this->AuthenticationCode ] + [ 'ClientId' => $clientid ] + [ 'List' => $customerslist ] );
        
        /* fonctionne !!!
        $value = array(
            "AuthenticationCode"=>$this->AuthenticationCode,
            "ClientId"=>$clientid,
            "List"=>$customerslist);
        $json_data = json_encode($value); */
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_customerslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}
        
        $json = $this->file_post_contents( $url, $json_data );
        //$json = file_get_contents($url, false, $json_data);
        $json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("result_customers_log.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, print_r($json_data, true));
			fwrite($myfile, date("h:i:s") . " - " . $clientid . ' msg : [' . $json_data['Message'] . '] - ' . $ie_reference . '_' . $mvts . "\n" );
			fclose($myfile);
		}

        return $json_data;
    } 

    // ----------------------------------------------------------------------------------------------------------------------
    // Skype 4/5/2023 @14:55
	// Type : "POST"
	// Request paramter:
	//
	// {
	// "ClientId": 10568,
    // "AuthenticationCode": "Z0yW0jofRnUS36XYPk8DDUrAqBg08iGm",
    // "list": [
    //     {
    //         "ParentId": "",
    //         "Id": "",
    //         "FirstName": "ABCddd",
    //         "LastName": "Defdddd",
    //         "Email": "hhh@gmail.com",
    //         "ContactNumber": "12545",
    //         "Title": "mr",
    //         "CustomerCode": "400Divers"
    //     },
    //     {
    //         "ParentId": "",
    //         "Id": "",
    //         "FirstName": "A",
    //         "LastName": "B",
    //         "Email": "hhh@gmail.com",
    //         "ContactNumber": "12545",
    //         "Title": "mr",
    //         "CustomerCode": "400Divers"
    //    }
    // ]
    // }
	// ----------------------------------------------------------------------------------------------------------------------
	// A TESTER !!!!!!!
	public function SaveCustomersContactsList( $clientid = '', $contactslist = '' )
    {
        if( $clientid == '' || $customerslist == '' || $this->AuthenticationCode == '' )
            return '';
        
        //$customerslist = [ 'AuthenticationCode' => $this->AuthenticationCode ] + [ 'ClientId' => $clientid ] + array('List'=>$customerslist);
        
        $url = $this->URL_SaveContactsList;

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("url_contactsslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $url);
			fwrite($myfile, "\n");
			fclose($myfile);
		}

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_contactslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
			//file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
		}
        
        $json_data = json_encode([ 'AuthenticationCode' => $this->AuthenticationCode ] + [ 'ClientId' => $clientid ] + [ 'List' => $contactslist ] );
        
        /* fonctionne !!!
        $value = array(
            "AuthenticationCode"=>$this->AuthenticationCode,
            "ClientId"=>$clientid,
            "List"=>$customerslist);
        $json_data = json_encode($value); */
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_contactslist_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fclose($myfile);
		}

        //file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
        /*DBG*/

		//$json_data = ''; // generate an error
        
        $json = $this->file_post_contents( $url, $json_data );
        //$json = file_get_contents($url, false, $json_data);
        $json_data = json_decode($json, true);

		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("result_contactslists_log.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, print_r($json_data, true));
			fwrite($myfile, date("h:i:s") . " - " . $clientid . ' msg : [' . $json_data['Message'] . '] - ' . $ie_reference . '_' . $mvts . "\n" );
			fclose($myfile);
		}

        //$data_items = $json_data["Data"];

        return $json_data;
    } 

	// ----------------------------------------------------------------------------------------------------------------------
	// https://beapi.mindoo.co/api/Services/SQLStatement?AuthenticationCode=s/H1LMfJtozj/2Ue/Rgl+FblFJ5kaoIC&RequestedStatement=SELECT [item_category_id],[name],[fk_client_id] FROM [EasyBelgium].[dbo].[item_category] where fk_client_id=10113 and item_category_id=1891
	// ----------------------------------------------------------------------------------------------------------------------
    public function GetCategoryName( $clientid = '', $categoryid = '' )
    {
        if( $clientid == '' || $categoryid == '' || $this->AuthenticationCode == '' )
            return '';

		$this->AuthenticationCode = $this->GetAuthentificationCode();
        $url = $this->URL_SQLStatement;
        $url .= "?AuthenticationCode=" . $this->AuthenticationCode;
        $url .= "&RequestedStatement=" . 'SELECT [item_category_id],[name],[fk_client_id] FROM [EasyBelgium].[dbo].[item_category] where fk_client_id=' . $clientid . 'and item_category_id=' . $categoryid;
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

        $data_items = $json_data["Data"];

        return $data_items;
    }

	// ----------------------------------------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------------------------------------
	public function SaveTransactionsDetails( $clientid = '', $transactionslist = '', $ie_reference = '', $mvts = 0 )
    {
        if( $clientid == '' || $transactionslist == '' || $this->AuthenticationCode == '' || $ie_reference == '' )
            return '';
        
        //if( $mvts != 2 && $mvts != 3 ) //DBG
        //    return ''; //DBG
        
        //$transactionslist += [ 'AuthenticationCode' => $this->AuthenticationCode ];
         
        $json_data = json_encode($transactionslist, true);
        
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("json_transactions_log.txt", "a") or die("Unable to open file!");
			fwrite($myfile, $json_data);
			fwrite($myfile, "\n");
			fclose($myfile);
			//file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
		}

		$this->AuthenticationCode = $this->GetAuthentificationCode();
		$url = $this->URL_SaveTransactionsDetails . "?AuthenticationCode=" . $this->AuthenticationCode;
		
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("url_transactions_log.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $url);
			fwrite($myfile, "\n");
			fclose($myfile);
		}

        $json = $this->file_post_contents( $url, $json_data );
        //$json = file_get_contents($url, false, $json_data);
        $json_data = json_decode($json, true);
        
        //$data_items = $json_data["Data"];

        //log_message($logtype, 'Export transactions to Picsoo (' . $this->ie_clientsid . ') - msg : ' . $result['Message'] . ' - ' . $result['Data']);
		if ( $this->DUMP_DBG )
		{
			$myfile = fopen("result_transactions_log.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, print_r($json_data, true));
			fwrite($myfile, date("h:i:s") . " - " . $clientid . ' msg : [' . $json_data['Message'] . '] - ' . $ie_reference . '_' . $mvts . "\n" );
			fclose($myfile);
			//file_put_contents('dbg.txt', date("h:i:s") . " - " . $json_data ."\n", FILE_APPEND);
		}

        return $json_data;
    }    
    
	// ----------------------------------------------------------------------------------------------------------------------
	// https://www.codexworld.com/post-receive-json-data-using-php-curl/#:~:text=Send%20JSON%20data%20via%20POST%20with%20PHP%20cURL&text=Initiate%20new%20cURL%20resource%20using,json%20using%20the%20CURLOPT_HTTPHEADER%20option.
	// ----------------------------------------------------------------------------------------------------------------------
    private function HttpRequest( $dataarray )
    {
        $jsondata = json_encode($dataarray);
        
        // API URL
        $url = $this->URL_LoginApplicationKey;

        // Create a new cURL resource
        $ch = curl_init($url);

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $result = curl_exec($ch);

        // Close cURL resource
        curl_close($ch);    
        
        return $result;
    }
    
	// ----------------------------------------------------------------------------------------------------------------------
	// DH : not used but I keep it instead of ...
	// Method: POST, PUT, GET etc
	// Data: array("param" => "value") ==> index.php?param=value
	// https://stackoverflow.com/questions/9802788/call-a-rest-api-in-php
	// ----------------------------------------------------------------------------------------------------------------------
    public function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
    
	// ----------------------------------------------------------------------------------------------------------------------
	/**
     * DH : not used but I keep it instead of ...
     * wget, the curl alternative
     * 
     * @author  Mohammed Al Ashaal <is.gd/alash3al>, <fb.com/alash3al>
     * @version 2.0.0
     * @license MIT License
     * 
     * @param   string  $url        example 'http://site.com/xxx?k=v'
     * @param   string  $method     example 'GET'
     * @param   array   $headers    example array( 'cookie' => 'k=v; x=y' )
     * @param   string  $body       only if the $methd is not GET
     * 
     * @return  object "success" | string "failure"
     */
	 // ----------------------------------------------------------------------------------------------------------------------
	 function wget($url, $method = 'GET', array $headers = array(), $body = '')
    {
        // get the url components
        $url = (object) array_merge(array(
            'scheme'    =>  'http',
            'host'      =>  '',
            'port'      =>  80,
            'path'      =>  '/',
            'query'     =>  ''
        ), parse_url($url));

        // PHP sets the host empty and the path to host 
        // only if the given url is [just.host]
        // so we must fix it .
        if ( empty($url->host) ) {
            $url->host = $url->path;
            $url->path = '/';
        }

        // the scheme
        if ( $url->scheme )
        {
            if ( strtolower($url->scheme) == 'http' ) {
                $url->scheme = 'tcp';
                $url->port   = 80;
            }
            elseif ( strtolower($url->scheme) == 'https' ) {
                $url->scheme = 'ssl';
                $url->port   = 443;
            }
        }

        // open socket connection
        $fp = $socket =   fsockopen(($url->scheme ? $url->scheme . '://' : '') . $url->host, $url->port, $errno, $errstr, 10);

        // if there is any error 
        // exit and print its string
        if ( $errno )
            return $errstr;

        // generate the headers
        $headers = array_merge(array
        (
            sprintf('%s %s%s HTTP/1.1', strtoupper($method), $url->path, $url->query ? ('?' . $url->query) : null),
            'host'              =>  $url->host . ':' . $url->port,
            'user-agent'        =>  'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Wget/2.0 Safari/537.36 (Mohammed Al Ashaal <fb.com/alash3al>)',
            'accept'            =>  '*/*',
            'accept-language'   =>  'en-US,en;q=0.8',
            'connection'        =>  'Close',
            'x-real-client'     =>  'MohammedAlashaal/Wget'
        ), array_change_key_case($headers, CASE_LOWER));

        // coninue for non-get methods
        if ( strtolower($method) !== 'get' )
        {
            $headers['content-length']  =   strlen($body);
            $headers[]  =   '';
            $headers[]  =   '';
            $headers[]  =   $body;
        }
        else $headers[] = '';

        // the headers string
        $h = '';

        // generate the headers string
        foreach ( $headers as $k => $v )
        {
            if ( is_int($k) )
                $h .= $v . PHP_EOL;
            else
                $h .= sprintf('%s: %s', str_replace(' ', '-', ucwords(strtolower(str_replace('-', ' ', $k)))), $v) . PHP_EOL;
            unset($headers[$k]);
        }

        // write the headers to the target
        fwrite($socket, $h);

        // headers and body from the server
        $headers = ''; $body = '';

        // generating the headers and the body
        if ( ($pos = strpos($response = stream_get_contents($socket), PHP_EOL . PHP_EOL)) !== FALSE ) {
            $headers = substr($response, 0, $pos);
            $body = substr($response, $pos + 1);
        } else $headers = $response;

        // close the socket connection
        fclose($socket);

        // tokenize
        $k = strtok($headers, PHP_EOL);

        // headers array
        $headers = array();

        // the status line
        @list(, $headers['status_code'], ) = explode(' ', $k, 3);
        $k = strtok(PHP_EOL);

        // decode-chunked
        if ( !empty($headers['transfer_encoding']) && $headers['transfer_encoding'] == 'chunked' )
        {
            $pos    =   0;
            $len    =   strlen($body);                        
            while ( $pos < $len )
            {
                $chuncked = substr($body, $pos, strpos($body, PHP_EOL));
                if ( ctype_xdigit($chuncked) )
                    die("chuncked");
                var_dump($chuncked, $body);exit;                                                    
                            
            }            
        }

        // some default header fields
        $headers['status_code'] =   (int) $headers['status_code'];
        $headers['url']         =   $url;

        // return the result
        return (object) array( 'headers' => (object) $headers, 'body' => trim($body) );
    }
    
	// ----------------------------------------------------------------------------------------------------------------------
	// https://stackoverflow.com/questions/11319520/php-posting-json-via-file-get-contents
	// ----------------------------------------------------------------------------------------------------------------------
    function file_post_contentsX($url, $data, $username = null, $password = null)
    {
        //$postdata = http_build_query($data); //DH en comment, sais pas à quoi servait cette ligne mais générait des erreurs

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => "Content-type: application/json\r\n",
                'content' => $data
            )
        );

        if($username && $password)
        {
            $opts['http']['header'] .= ("Authorization: Basic " . base64_encode("$username:$password")); // .= to append to the header array element
        }

        $context = stream_context_create($opts);

        //file_put_contents('dbg.txt', date("h:i:s") . " - IN\n", FILE_APPEND);   // DEBUG PURPOSE #1     
        $ret = file_get_contents($url, false, $context);
        //file_put_contents('dbg.txt', date("h:i:s") . " - OUT\n", FILE_APPEND);  // DEBUG PURPOSE #2      
        //sleep(1);
        //usleep(250000);
        
        return $ret;
    }
    
	// ----------------------------------------------------------------------------------------------------------------------
	// https://www.codexworld.com/post-receive-json-data-using-php-curl/
	// ----------------------------------------------------------------------------------------------------------------------
    function file_post_contents($url, $data, $username = null, $password = null)
    {
        // API URL
        //dh $url = 'http://www.example.com/api';

        // Create a new cURL resource
        $ch = curl_init($url);

        // Setup request to send json via POST
        /*dh $data = array(
            'username' => 'codexworld',
            'password' => '123456'
        );
        $payload = json_encode(array("user" => $data));*/
        $payload = $data;

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $result = curl_exec($ch);

        // Close cURL resource
        curl_close($ch);    
        
        return $result;
    }

	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// https://java2blog.com/extract-numbers-from-string-php/#:~:text=Use%20preg_match_all()%20method%20with,numbers%20from%20String%20in%20PHP.&text=Here%2C%20output%20is%20array%20of,the%20numbers%20from%20the%20string.
	// pour la tva
	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    function ExtractNumberFromString( $input )
    {
		$matches = '';
		$rt = '';
		
		if( empty($input) )
			return '';

		preg_match_all('/[0-9]+/', $input, $matches);
 
 		if( !isset($matches))
 			return '';
 		
		$rt = array_values($matches)[0];
	
		if( isset($rt[0]))
			return $rt[0];    
		
		return '';
	}

	// --------------------------------------------------------------------------------------------------------------------------------
 	// Function for basic field validation (present and neither empty nor only white space
	// https://stackoverflow.com/questions/381265/better-way-to-check-variable-for-null-or-empty-string
	// --------------------------------------------------------------------------------------------------------------------------------
	function IsNullOrEmptyString($str)
	{
    	return ($str === null || trim($str) === '');
	}

	// --------------------------------------------------------------------------------------------------------------------------------
	// Function to remove the special
	// https://www.geeksforgeeks.org/how-to-remove-special-character-from-string-in-php/
	// --------------------------------------------------------------------------------------------------------------------------------
  	function RemoveSpecialChar( $str ) 
  	{
       // Using str_replace() function
      // to replace the word
      $res = str_replace( array( '\'', '"','=' , '&', '<', '>', '@', '[', ']' ), ' ', $str);
 
      // Returning the result
      return $res;
    }
}