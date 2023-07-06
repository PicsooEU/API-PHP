<?php
define('BASEPATH', __DIR__);
include_once 'libraries/Picsoo_ws.php';
$picsoo_ws = new Picsoo_ws();

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
			case "GetCompaniesListByEmail":
	            GetCompaniesListByEmail();
				break;
			case "GetCompanyInfo":
				GetCompanyInfo();
				break;
			case "ClearAllDataForGivenCompany":
				ClearAllDataForGivenCompany();
				break;
			case "SaveCustomer":
				SaveCustomer();
				break;
			case "SaveChartOfAccount":
				SaveChartOfAccount();
				break;
		}
    }
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

        'ReferenceExternal' => 'PICSOO API',
	];

	$ret = $picsoo_ws->SaveChartOfAccount( $clientid, $datatosend );

	//file_put_contents('output.txt', print_r($ret, true));

	echoresponse($ret);
}

function SaveSupplier()
{
	// see SavecCustomer(), change "$picsoo_ws->SaveCustomers()" into "$picsoo_ws->SaveSuppliers()""
}

function SaveCustomer()
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
	$customertype = 'C';

	$ret = $picsoo_ws->SaveCustomers($clientid, $datatosend, $customertype);

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
    echo json_encode($response);
    exit; // End the script execution
}
?>
