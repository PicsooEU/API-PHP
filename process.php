<?php
define('BASEPATH', __DIR__);
include_once 'libraries/Picsoo_ws.php';
$picsoo_ws = new Picsoo_ws();
$myList = array();
$uniqueValue = strtoupper(uniqid());

// Increment or decrement values based on the selected radio button
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Access the values of clientid
    $clientid = $_POST['clientid'];
	
	$companyname = $_POST['companyname'];
	$customername = $_POST['customername'];
	$customerfirstname = $_POST['customerfirstname'];
	$customerfirstname = $_POST['customerfirstname'];
 	$customervat = $_POST['customervat'];

 	$accountcode = $_POST['accountcode'];
 	$accountname = $_POST['accountname'];

 	$journalcode = $_POST['journalcode'];
 	$journalname = $_POST['journalname'];

 	$itemcode = $_POST['itemcode'];
 	$itemname = $_POST['itemname'];

	$picsoo_ws->SetUsernamePsw( $email, $password );
	if( $picsoo_ws->CheckPicsooAccess()== false )
	{
	    // Prepare the response as JSON
    	$response = array(
        	'message' => 'Echec',
			'option' => 'alert',
        	'data' => "Can't access Picsoo ! (email ? password ? authenticationcode ?)"
    	);

    	// Send the response back to the client
    	echo json_encode($response);
    	exit; // End the script execution
	}

    if (isset($_POST['selectedRadio'])) {
        $selectedRadio = $_POST['selectedRadio'];
		switch( $selectedRadio )
		{
			case "GetAuthentificationCode":
				GetAuthentificationCode();
				break;
			case "GetCompaniesListByEmail":
	            GetCompaniesListByEmail();
				break;
			case "GetCompanyInfo":
				GetCompanyInfo();
				break;
			case "ClearAllDataForGivenCompany":
				ClearAllDataForGivenCompany();
				break;
			case "SaveCustomerSupplier":
				SaveCustomerSupplier();
				break;
			case "SaveChartOfAccount":
				SaveChartOfAccount();
				break;
			case "SaveJournalCode":
				SaveJournalCode();
				break;
			case "SaveItem":
				SaveItem();
				break;
			case "SaveTransaction":
				SaveTransaction();
				break;
			case "GeTClientsByAccountant":
				GeTClientsByAccountant();
				break;
			case "GetCustomerSupplierInfo":
				GetCustomerSupplierInfo();
				break;
			case "CheckAccountCodeExistOrNot":
				CheckAccountCodeExistOrNot();
				break;
			case "GetRowsCount":
				GetRowsCount();
				break;
			case "GetVatList":
				GetVatList();
				break;
			default:
	    		// Prepare the response as JSON
    			$response = array(
        			'message' => 'Echec',
					'option' => 'alert',
        			'data' => "Undefined function !"
        			);
			    // Send the response back to the client
    			echo json_encode($response);
    			exit; // End the script execution
		}
    }
}

function GetVatList()
{
	global $clientid, $picsoo_ws;

	$ret = $picsoo_ws->GetVatList( $clientid );

	echoresponse($ret);
}

function GetRowsCount()
{
	global $clientid, $picsoo_ws, $accountcode;

	$ret = $picsoo_ws->GetRowsCount( $clientid, 'customers', "fk_clients_id='11135' and email_address='12345@test.com'" );

	// Prepare the response as JSON
	$response = array(
		'message' => 'Echec',
			'option' => 'alert',
		'data' => 'Number of rows : ' . $ret
		);
	    // Send the response back to the client
	echo json_encode($response);
	exit; // End the script execution
}

function CheckAccountCodeExistOrNot()
{
	global $clientid, $picsoo_ws, $accountcode;

	$ret = $picsoo_ws->CheckAccountCodeExistOrNot( $clientid, $accountcode );

	echoresponse($ret);
}

function GetAuthentificationCode()
{
	// internal use only.	
}

function GetCustomerSupplierInfo( )
{
	global $clientid, $picsoo_ws, $customervat;

	$ret = $picsoo_ws->GetCustomerSupplierInfo( $clientid, 'C', '', $customervat, '' );

	echoresponse($ret);
}

function GeTClientsByAccountant()
{
	global $email, $picsoo_ws;

	$ret = $picsoo_ws->GeTClientsByAccountant( $email );

	echoresponse($ret);
}

function SaveTransaction()
{
	global $clientid, $picsoo_ws, $myList;

	//unset ($myList);
	$myList = array();

    CreateTransactionL1(); // client
    CreateTransactionL2(); // contrepartie
    CreateTransactionL3(); // tva

	//$ret = $picsoo_ws->SaveTransactionsDetails($clientid, $myList);

	file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

// Client
function CreateTransactionL1()
{
	global $clientid, $picsoo_ws, $myList, $uniqueValue;

    //AuthenticationCode = GlobalVar.AuthenticationCode;
    //ClientsId = "11135";

	$datatosend = [
		'AuthenticationCode' => $picsoo_ws->CurrentAuthentificationCode(),
		'ClientsId' => $clientid,
	    'AaccountNameId' => '',
	    'AccountCode' => '400TRUCKCO',
	    'AccountGridType' => '',
	    'AccountGridType2   ' => '',
	    'AccountName' => '',
	    'Amount' => '121',
	    'AmountCurrency' => '0',
	    'Analyt' => 'ANALYT',
	    'BackupId' => '',
	    'BankDescription' => '',
	    'BankRef' => '',
	    'BillSerivesGoodsId ' => '',
	    'CreatedBy  ' => '',
	    'CreatedByName' => 'PICSOO API',
	    'CreatedDate' => '',
	    'Currency' => '',
	    'CurrencyRate' => '0',
	    'CustomerCode' => '',
	    'CustomersId' => '',
	    'DebitCreditType' => 'Debit',
	    'Description' => 'Truck & Co',
	    'Discount' => '',
	    'DueDate' => '25/07/2023 00:00:00',
	    'Employee' => '',
	    'EntityName' => 'Truck & Co',
	    'EuropeanVatNumber' => '',
	    'ExportFrom ' => '',
	    'FinancialPeriodId' => '',
	    'Flag' => '',
	    'FlagDate' => '',
	    'FlagUser' => '',
	    'GVat' => '',
	    'InvoiceJournalCode' => 'V',
	    'IsDeleted' => 'false',
	    'Item' => '',
	    'JournalNumber' => '1',
	    'JournalType' => 'Sale',
	    'MasterBranchCategoryId1' => '',
	    'MasterBranchCategoryId2' => '',
	    'ModifiedBy ' => '',
	    'ModifiedByName' => 'PICSOO API',
	    'ModifiedDate' => '31/07/2023 00:00:00',
	    'Net' => '121',
	    'ProjectId' => '',
	    'Qty' => '0',
	    'ReconcilationId' => '0',
	    'ReconciliationCode ' => '',
	    'ReconciliationDate ' => '25/07/2023',
	    'ReconciliationStatus' => 'N',
	    'Reference' => '1',
	    'ReferenceId' => '',
	    'ReminderLevel' => '',
	    'ReverseReferance' => '',
	    'ReverseTransactions' => '',
	    'ServicesGoods  ' => '',
	    'TrackingNumber ' => '',
	    'TransactionCode' => '1',
	    'TransactionCodeExternal' => '',
	    'TransactionDate' => '25/07/2023 00:00:00',
	    'TransactionType' => 'Sale',
	    'TransactionTypeExternal' => '',
	    'TransactionsId' => '0',
	    'UnitPrice' => '',
	    'VATPercent' => '0',
	    'VATTypeId' => '',
	    'VatCode' => '',
	    'VatPeriod' => '01/07/2023',
	    'VatPeriodId' => '01/07/2023',
	    'VateRate' => '21',
	    'reference_external' => 'PICSOO API_' . $uniqueValue,
	    'vatNumber' => ''
	];
	
    $myList[] = $datatosend;
}

// Contrepartie
function CreateTransactionL2()
{
	global $clientid, $picsoo_ws, $myList, $uniqueValue;

    //AuthenticationCode = GlobalVar.AuthenticationCode;
    //ClientsId' => '11135',

	$datatosend = [
		'AuthenticationCode' => $picsoo_ws->CurrentAuthentificationCode(),
		'ClientsId' => $clientid,
	    'AaccountNameId ' => '',
	    'AccountCode' => '701000',
	    'AccountGridType' => '03',
	    'AccountGridType2' => '',
	    'AccountName' => '',
	    'Amount' => '-100',
	    'AmountCurrency' => '0',
	    'Analyt' => 'ANALYT',
	    'BackupId' => '',
	    'BankDescription' => '',
	    'BankRef' => '',
	    'BillSerivesGoodsId' => '',
	    'CreatedBy' => '',
	    'CreatedByName' => 'PICSOO API',
	    'CreatedDate' => '',
	    'Currency' => '',
	    'CurrencyRate' => '0',
	    'CustomerCode' => '400TRUCKCO',
	    'CustomersId' => '',
	    'DebitCreditType' => 'Credit',
	    'Description' => 'Ventes produits finis en Belgique',
	    'Discount' => '',
	    'DueDate' => '30/12/1899 00:00:00',
	    'Employee' => '',
	    'EntityName' => 'Ventes produits finis en Belgique',
	    'EuropeanVatNumber' => '',
	    'ExportFrom' => '',
	    'FinancialPeriodId' => '',
	    'Flag' => '',
	    'FlagDate' => '',
	    'FlagUser' => '',
	    'GVat' => '',
	    'InvoiceJournalCode' => 'V',
	    'IsDeleted' => 'false',
	    'Item' => '',
	    'JournalNumber' => '1',
	    'JournalType' => 'Sale',
	    'MasterBranchCategoryId1' => '',
	    'MasterBranchCategoryId2 ' => '',
	    'ModifiedBy' => '',
	    'ModifiedByName' => 'PICSOO API',
	    'ModifiedDate' => '31/07/2023 00:00:00',
	    'Net' => '100',
	    'ProjectId' => '',
	    'Qty' => '0',
	    'ReconcilationId' => '0',
	    'ReconciliationCode' => '',
	    'ReconciliationDate ' => '25/07/2023',
	    'ReconciliationStatus' => 'N',
	    'Reference' => '1',
	    'ReferenceId' => '',
	    'ReminderLevel' => '',
	    'ReverseReferance' => '',
	    'ReverseTransactions' => '',
	    'ServicesGoods' => '',
	    'TrackingNumber' => '',
	    'TransactionCode' => '1',
	    'TransactionCodeExternal' => '',
	    'TransactionDate' => '25/07/2023 00:00:00',
	    'TransactionType' => 'Sale',
	    'TransactionTypeExternal' => '',
	    'TransactionsId ' => '0',
	    'UnitPrice' => '',
	    'VATPercent' => '21',
	    'VATTypeId' => '',
	    'VatCode' => '21',
	    'VatPeriod' => '01/07/2023',
	    'VatPeriodId' => '01/07/2023',
	    'VateRate' => '0',
	    'reference_external' => 'PICSOO API_' . $uniqueValue,
	    'vatNumber' => ''
	];
	
    $myList[] = $datatosend;
}

// TVA
function CreateTransactionL3()
{
	global $clientid, $picsoo_ws, $myList, $uniqueValue;

    //AuthenticationCode = GlobalVar.AuthenticationCode;
    //ClientsId' => '11135',

	$datatosend = [
		'AuthenticationCode' => $picsoo_ws->CurrentAuthentificationCode(),
		'ClientsId' => $clientid,
	    'AaccountNameId' => '',
	    'AccountCode' => '451540',
	    'AccountGridType' => '',
	    'AccountGridType2' => '',
	    'AccountName' => '',
	    'Amount' => '-21',
	    'AmountCurrency' => '0',
	    'Analyt' => 'ANALYT',
	    'BackupId' => '',
	    'BankDescription' => '',
	    'BankRef' => '',
	    'BillSerivesGoodsId' => '',
	    'CreatedBy' => '',
	    'CreatedByName' => 'PICSOO API',
	    'CreatedDate' => '',
	    'Currency' => '',
	    'CurrencyRate' => '0',
	    'CustomerCode' => '400TRUCKCO',
	    'CustomersId' => '',
	    'DebitCreditType' => 'Credit',
	    'Description' => 'Tva due sur ventes',
	    'Discount' => '',
	    'DueDate' => '30/12/1899 00:00:00',
	    'Employee' => '',
	    'EntityName' => 'Tva due sur ventes',
	    'EuropeanVatNumber' => '',
	    'ExportFrom ' => '',
	    'FinancialPeriodId' => '',
	    'Flag' => '',
	    'FlagDate' => '',
	    'FlagUser' => '',
	    'GVat' => '54',
	    'InvoiceJournalCode' => 'V',
	    'IsDeleted' => 'false',
	    'Item' => '',
	    'JournalNumber' => '1',
	    'JournalType' => 'Sale',
	    'MasterBranchCategoryId1' => '',
	    'MasterBranchCategoryId2' => '',
	    'ModifiedBy' => '',
	    'ModifiedByName' => 'PICSOO API',
	    'ModifiedDate' => '31/07/2023 00:00:00',
	    'Net' => '21',
	    'ProjectId' => '',
	    'Qty' => '0',
	    'ReconcilationId' => '0',
	    'ReconciliationCode' => '',
	    'ReconciliationDate' => '25/07/2023',
	    'ReconciliationStatus' => 'N',
	    'Reference' => '1',
	    'ReferenceId' => '',
	    'ReminderLevel' => '',
	    'ReverseReferance' => '',
	    'ReverseTransactions' => '',
	    'ServicesGoods' => '',
	    'TrackingNumber' => '',
	    'TransactionCode' => '1',
	    'TransactionCodeExternal' => '',
	    'TransactionDate' => '25/07/2023 00:00:00',
	    'TransactionType' => 'Sale',
	    'TransactionTypeExternal' => '',
	    'TransactionsId' => '0',
	    'UnitPrice' => '',
	    'VATPercent' => '21',
	    'VATTypeId' => '',
	    'VatCode' => '21',
	    'VatPeriod' => '01/07/2023',
	    'VatPeriodId' => '01/07/2023',
	    'VateRate' => '21',
	    'reference_external' => 'PICSOO API_' . $uniqueValue,
	    'vatNumber' => ''
	];
	
    $myList[] = $datatosend;
}

function SaveItem()
{
	global $clientid, $picsoo_ws, $itemcode, $itemname;

	if( $itemcode=='' || $itemname=='' )
	{
		    // Prepare the response as JSON
	    	$response = array(
	        	'message' => 'Echec',
				'option' => 'alert',
	        	'data' => "Item name and/or item code cannot be empty !"
	    );

	    // Send the response back to the client
	    echo json_encode($response);
	    exit; // End the script execution
	}

	$datatosend = [
		'Mode' => '',
        'UserId' => '',
        'ItemName' => $itemcode,
        'FRItemDescription' => $itemname . ' FR',
        'NLItemDescription' => $itemname . ' NL',
        'ItemDescription' => '',
        'ItemType' => '',
        'ItemImage' => '',
        'SaleQty' => '10', // Stock réel
        'SaleUnitPrice' => '130', // prix vente de l'article
        'SaleVatRate' => '21',
        'SaleAccountName' => '701000', // Compte de vente
        'PurQty' => '0',
        'PurUnitPrice' => '100',
        'PurVatRate' => '21',
        'AdjustAccountName' => '',
        'OpeningBalance' => '',
        'OpeningQty' => '',
        'PurchaseBalance' => '',
        'PurhcasePrice' => '100',
        'IsStockManage' => 'False', // I track this item
        'IsSale' => 'True', // I sale this item
        'IsPurchase' => 'False', // I purchase this item
        'CategoryName' => '',
        'ProjectName' => '',
        'PurAccountName' => '601000',
        'TaxRatesClientsName' => '',
        'PurTaxRatesClientsName' => '',
        'IsVatmarginEnable' => '',
        'HSNCode' => '',
        'SupplierCode' => '',
        'SalesVatCode' => '21',
        'PurchaseVatCode' => '21',
        'PackageQty' => '1',
        'NetWeight' => '',
        'BrutoWeight' => '',
        'MPN' => '',
        'ISBN' => '',
        'EAN' => '', // Code barre
        'CPU' => '',
        'PAMP' => '',
        'Substitue1Name' => '',
        'Substitue2Name' => '',
        'Waranty' => '',
        'Brand' => '',
        'Analytic' => '',
        'Unit' => '', // unité de vente
        'ItemCategoryType' => '',
        'CreatedBy' => '',
        'ModifiedBy' => '',
        'WarehouseName' => '',
        'InventoryAssetAccountName' => '',
        'IsVatOnly' => '',
        'IsDeleted' => 'False',
        'VATCodePurchaseCredit' => '21',
        'VATCodesalesCredit' => '21',
        'ItemNomianlCode' => '',
        'Barcode' => '',
        'BarCodeTypeId' => '1', // ?? 
        'Multiplier' => '1', // marge
        'CostPrice' => '',

        'AccountName' => '',
        'EcoParticiPationItem' => '',

        'ItemGoodServiceType' => '1',

        'SaleUnitPriceVat' => '',

        'ReferenceExternal' => 'PICSOO API'
	];
	
	$ret = $picsoo_ws->SaveItem($clientid, $datatosend);

	//file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

function SaveJournalCode()
{
	global $clientid, $picsoo_ws, $journalcode, $journalname;

	if( $journalcode=='' || $journalname=='' )
	{
		    // Prepare the response as JSON
	    	$response = array(
	        	'message' => 'Echec',
				'option' => 'alert',
	        	'data' => "Journal name and/or journal code cannot be empty !"
	    );

	    // Send the response back to the client
	    echo json_encode($response);
	    exit; // End the script execution
	}

	$JournalCodeType = '1'; // Ventes
	//$JournalCodeType = '2'; // Achats
	//$JournalCodeType = '25'; // A-Nouveaux - OD
	//$JournalCodeType = '13'; // Trésorerie - banque - caisse
	//$JournalCodeType = '4';  // crédits / achats
	//$JournalCodeType = '3'; // crédits / ventes
	//$JournalCodeType = '18'; // amortissements automatique
	//$JournalCodeType = '2'; // archivage fournisseurs
	//$JournalCodeType = '9'; // paiements automatiques
	//$JournalCodeType = '25'; // non défini

	$datatosend = [
        'JournalCode' => $journalcode,
        'Description' => $journalname,
        'LastReferenceNumber' => '1',
        'JournalCodeType' => $JournalCodeType,
        'NominalCode' => '701000',
        'DescriptionFR' => $journalname,
        'DescriptionNL' => $journalname
	];
	
	$ret = $picsoo_ws->SaveJournalCode($clientid, $datatosend);

	//file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

//{"IsSuccess":true,"Message":"Account added.","Data":1118376,"AdditionalMessage":null}

function SaveChartOfAccount()
{
	global $clientid, $picsoo_ws, $accountcode, $accountname;

	if( $accountcode=='' || $accountname=='' )
	{
		    // Prepare the response as JSON
	    	$response = array(
	        	'message' => 'Echec',
				'option' => 'alert',
	        	'data' => "Account name and/or account code cannot be empty !"
	    );

	    // Send the response back to the client
	    echo json_encode($response);
	    exit; // End the script execution
	}

	$AccountTypeId = '';
    if (substr($accountcode, 0, 1) == '1') // 1 - capital
		$AccountTypeId = '2';
    if (substr($accountcode, 0, 1) == '2') // 2 - formation expenses
		$AccountTypeId = '5';
    if (substr($accountcode, 0, 1) == '3') // 3 - stocks
		$AccountTypeId = '7';
    if (substr($accountcode, 0, 1) == '4') // 4 - amount receivable and payable
		$AccountTypeId = '1';
    if (substr($accountcode, 0, 1) == '5') // 5 - current investments
		$AccountTypeId = '3';
    if (substr($accountcode, 0, 1) == '6') // 6 - expenditures
		$AccountTypeId = '4';
    if (substr($accountcode, 0, 1) == '7') // 7 - income
		$AccountTypeId = '6';
    //if (substr($accountcode, 0, 1) == '8') // 8 - other accounts
		//$AccountTypeId = '?';
    //if (substr($accountcode, 0, 1) == '9') // 9 - inactive
		//$AccountTypeId = '?';
	$IsPurchase = 'False';
	$IsSale = 'False';
    if (substr($accountcode, 0, 1) == '6')
        $IsPurchase = 'True';
    else
        $IsPurchase = 'False';
    if (substr($accountcode, 0, 1) == '7')
        $IsSale = 'True';
    else
        $IsSale = 'False';

	$datatosend = [
        'Name' => $accountname,
        'Account_group_name' => '',
        'Account_name' => $accountname,
        'Account_name_French' => $accountname,
        'Account_name_Dutch' => $accountname,
        'display_code' => $accountcode,
        'IsLocked' => 'False',
		'AccountTypeId' => $AccountTypeId,
        'VATCode' => '',
        'IsActive' => 'True',
        'Category' => '',
        'CategoryEN' => '',
        'IsMaster' => '',
        'Vat_rate' => '',
        'vattype' => '',
        'IsPurchase' => $IsPurchase,
        'IsSale' => $IsSale,
        'IsBudget' => '',
        'fk_tax_rates_clients_id' => '',
        'Budget' => '',
        'Analytic' => '',
        'AccountActifPassifId' => '',

        'ReferenceExternal' => 'PICSOO API', // this is a marker to know from where data are coming from
	];

	$ret = $picsoo_ws->SaveChartOfAccount( $clientid, $datatosend );

	//file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

function SaveCustomerSupplier()
{
	global $clientid, $picsoo_ws, $companyname, $customerfirstname, $customername, $customervat;

	if( $customerfirstname=='' || $customername=='' )
	{
		    // Prepare the response as JSON
	    	$response = array(
	        	'message' => 'Echec',
				'option' => 'alert',
	        	'data' => "Customer name and/or firstname cannot be empty !"
	    );

	    // Send the response back to the client
	    echo json_encode($response);
	    exit; // End the script execution
	}

	$datatosend = [
		'Mode' => '0', // Mode - 0 To Insert And 1 To Update
     	'UserID' => '',
        'CompanyName' => $companyname,
        'CustomerCode' => '400' . $companyname,
        'Title' => '',
        'FirstName' => $customerfirstname,
        'LastName' => $customername,
        'Email' => '12345@test.com',
        'PrimaryPhone' => '',
        'ContactType' => '',
        'BillingAddressLine1' => '',
        'BillingAddressLine2' => '',
        'BillingCity' => '',
        'BillingState' => '',
        'BillingCountry' => '',
        'BillingCountry' => '',
        'DeliveryAddressLine1' => '',
        'DeliveryAddressLine2' => '',
        'DeliveryCity' => '',
        'DeliveryState' => '',
        'DeliveryCountry' => '',
        'DeliveryCountry' => '',
        'Website' => '',
        'BankName' => '',
        'AccountName' => '',
        'AccountNumber' => '',
        'IBAN' => '',
        'BIC' => '',
        'AccountCode' => '',
        'OtherDetail' => '',
        'SecondaryPhone' => '',
        'vatNumber' => '',
        'Skype' => '',
        'Facebook' => '',
        'Twitter' => '',
        'Reconciliation' => '',
        'TaxReport' => '',
        'Reminder' => '',
        'EuropeanVatNo' => '666217180', // $customervat,
        'Vatcode' => '666217180', // $customervat,
        'DueDate' => '',
        'BusinesType' => '',
        'Category' => '',
        'Discount' => '',
        'Notes' => '',
        'VatCodePurchaseCredit' => '',
        'VatCodeSalesCredit' => '',
        'DeliveryPostalCode' => '',
        'BillingPostalCode' => '',
        'Item' => '',
        'Languages' => '',
        'Deleted' => '',

        'ReferenceExternal' => 'PICSOO_API_DEMO',
	];

	$customertype = 'C'; // Customer
	//$customertype = 'S'; // Supplier

	$ret = $picsoo_ws->SaveCustomerSupplier($clientid, $datatosend, $customertype);

	//file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

function ClearAllDataForGivenCompany()
{
    // Prepare the response as JSON
    $response = array(
        'message' => 'Echec',
		'option' => 'alert',
        'data' => 'This function cannot be performed for security reasons !'
    );

    // Send the response back to the client
    echo json_encode($response);
    exit; // End the script execution

}
function GetCompanyInfo()
{
	global $clientid, $picsoo_ws;

	$companyinfo = $picsoo_ws->GetCompanyInfo( $clientid );
	//file_put_contents('output.txt', print_r($companyinfo, true));

	echoresponse($companyinfo);
}

function GetCompaniesListByEmail()
{
	global $email, $picsoo_ws;

	$cpylist = $picsoo_ws->GetCompaniesListByEmail( $email );
	//file_put_contents('output.txt', print_r($cpylist, true));

	echoresponse($cpylist);
}

function echoresponse($msg)
{
    // Prepare the response as JSON
    $response = array(
        'message' => ( isset($msg) ? 'Success' : 'Echec'),
		'option' => 'alert',
        'data' => ( isset($msg) ? json_encode($msg) : 'No data!' )
    );

    // Send the response back to the client
    $ret = json_encode($response);
    echo $ret;
    exit; // End the script execution
}
?>
