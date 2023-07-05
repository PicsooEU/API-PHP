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
	$customername = $_POST['customername'];
	$customerfirstname = $_POST['customerfirstname'];

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
		}
    }
}

function SaveCustomer()
{
	global $clientid, $picsoo_ws, $customerfirstname, $customername;

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
	
		//'ClientId'                  => $this->ie_clientsid,
		'CompanyName'               => $customerfirstname . ' ' . $customername,
		'AccountCode'               => '400' . $customername,
		'Title'                     => '',
		'FirstName'                 => '',
		'LastName'                  => '',
		'Email'                     => '',
		'PrimaryPhone'              => '',
		'ContactType'               => '',
		'BillingAddressLine1'		=> '',
		'BillingAddressLine2'		=> '',
		'BillingPostalCode'         => '',
		'BillingCity'               => '',
		'BillingState'              => '',
		'BillingCountry'            => '',
		'DeliveryAddressLine1'		=> '',
		'DeliveryAddressLine2'		=> '',
		'DeliveryPostalCode'		=> '',
		'DeliveryCity'              => '',
		'DeliveryState'             => '',
		'DeliveryCountry'           => '',
		'Website'                   => '',
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
		'ReferenceExternal'         => 'PICSOO_API_DEMO',
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
