<!DOCTYPE html>
<html>
<head>
    <title>Picsoo API Demo Platform (staging) v0.01</title>
    <style>
        /* Styling for the sections and line separator */
        .section {
            margin-bottom: 20px;
            border-bottom: 1px solid black;
            padding-bottom: 20px;
        }
        .section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        /* Styling for the first section */
        h1 {
            color: blue;
            font-weight: bold;
        }
        h2 {
            color: #55ff00ff;
            font-weight: normal;
			font-size: small;
        }

        /* Styling for the second section */
        .textbox-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
        }
        label[for="email"],
        label[for="password"],
        label[for="clientid"],

        label[for="companyname"],
		label[for="name"],
        label[for="firstname"],
		label[for="vat"],

		label[for="accountcode"],
		label[for="accountname"] {
            color: black;
        }
        label[for="bbbb"] {
            color: black;
        }

        /* Styling for the third section */
        .radio-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Updated to 4 columns */
            gap: 5px;
        }
        label[for="GetCompaniesListByEmail"],
        label[for="GetCompanyInfo"],
        label[for="ClearAllDataForGivenCompany"],
        label[for="SaveCustomer"],
		label[for="SaveChartOfAccount"] {
            color: blue;
        }
        label[for="radio4"] {
            color: blue;
        }
        .radio-label {
            display: flex;
            align-items: center;
        }

        /* Styling for the fourth section */
        a {
            color: blue;
            text-decoration: underline;
        }
        button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <form id="myForm">
		<!-- TITLE ------------------------------------------------------------------------------------------------------------------------------------- -->
        <div class="section">
            <h1>Picsoo API Demo Platform (staging) v0.01</h1>
        </div>

		<!-- GROUP #1 INPUT TEXT BOXES ----------------------------------------------------------------------------------------------------------------- -->
        <div class="section">
			<!-- general ---->
			<h2>General :</h2>
            <div class="textbox-container">
                <div>
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="dev@picsoo.eu" placeholder="Email used to create de company">
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" value="Dev@1234!" placeholder="Password used to create de company">
                </div>
                <div>
                    <label for="clientid">ClientID:</label>
                    <input type="text" id="clientid" name="clientid" value="11135" placeholder="client id of your company">
                </div>
                <div>
                    <label for="bbbb">Not used:</label>
                    <input type="text" id="bbbb" name="bbbb" placeholder="Leave empty">
                </div>
			</div>
			<!-- customers ---->
			<h2>Customers :</h2>
            <div class="textbox-container">
                <div>
                    <label for="companyname">Company name:</label>
                    <input type="text" id="companyname" name="companyname" value="TruckCo" placeholder="company name">
                </div>
                <div>
                    <label for="name">Customer name:</label>
                    <input type="text" id="name" name="name" value="McNee" placeholder="A name">
                </div>
                <div>
                    <label for="firstname">Customer firstname:</label>
                    <input type="text" id="firstname" name="firstname" value="Patrick" placeholder="A firstname">
                </div>
                <div>
                    <label for="vat">VAT:</label>
                    <input type="text" id="vat" name="vat" value="666217180" placeholder="An european VAT">
                </div>
            </div>
			<!-- chart of account ---->
			<h2>Chart of account :</h2>
            <div class="textbox-container">
                <div>
                    <label for="accountcode">Account code:</label>
                    <input type="text" id="accountcode" name="accountcode" value="711111" placeholder="account code">
                </div>
                <div>
                    <label for="accountname">Account name:</label>
                    <input type="text" id="accountname" name="accountname" value="Ventes X" placeholder="account name">
                </div>
            </div>
        </div>

		<!-- GROUP #2 API LIST ------------------------------------------------------------------------------------------------------------------------- -->
        <div class="section">
            <div class="radio-container">
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="GetCompaniesListByEmail" checked="checked" onclick="handleRadioSelection(this)">
                    <label for="GetCompaniesListByEmail">GetCompaniesListByEmail</label>
                </div>
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="GetCompanyInfo" onclick="handleRadioSelection(this)">
                    <label for="GetCompanyInfo">GetCompanyInfo</label>
                </div>
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="ClearAllDataForGivenCompany" onclick="handleRadioSelection(this)">
                    <label for="ClearAllDataForGivenCompany">ClearAllDataForGivenCompany</label>
                </div>
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="SaveCustomer" onclick="handleRadioSelection(this)">
                    <label for="SaveCustomer">SaveCustomer (-Supplier)</label>
                </div>
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="SaveChartOfAccount" onclick="handleRadioSelection(this)">
                    <label for="SaveCustomer">SaveChartOfAccount</label>
                </div>
                <div class="radio-label">
                    <input type="radio" name="radioGroup" value="Not used" onclick="handleRadioSelection(this)">
                    <label for="radio4">Not used</label>
                </div>
            </div>
        </div>

		<!-- GROUP # FONCTIONS & BUTTONS --------------------------------------------------------------------------------------------------------------- -->
        <div class="section">
            <button id="okButton" type="button" onclick="handleOKButtonClick()">EXECUTE SELECTED API</button>
            &nbsp;&nbsp;
			<button type="button" onclick="handleCancelButtonClick()">CANCEL</button>
			<br><br>
            <a href="http://www.picsoo.eu">picsoo.eu</a>
			&nbsp;&nbsp;
            <a href="https://stagingbe.mindoo.co/">staging</a>
			&nbsp;&nbsp;
            <a href="http://picsoocloud.com/picsooapidoc/">api documentation</a>
			&nbsp;&nbsp;
            <a href="https://github.com/PicsooEU/API-PHP/">GitHub repositionary</a>
			&nbsp;&nbsp;
        </div>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Function to handle radio button selection
        function handleRadioSelection(radioButton) {
            //console.log("handleRadioSelection() " + radioButton.value);
        }

        function handleOKButtonClick() {
            //console.log("handleOKButtonClick()");

            // Retrieve the values of the fields
            var emailValue = document.getElementById("email").value;
            var passwordValue = document.getElementById("password").value;
            var clientidValue = document.getElementById("clientid").value;
            
			var companynameValue = document.getElementById("companyname").value;
			var customernameValue = document.getElementById("name").value;
            var customerfirstnameValue = document.getElementById("firstname").value;
            var customervatValue = document.getElementById("vat").value;

			var accountcodeValue = document.getElementById("accountcode").value;
			var accountnameValue = document.getElementById("accountname").value;
			//alert("handleOKButtonClick()");

            // Send the values to the server using AJAX
            $.ajax({
                type: "POST",
                url: "process.php", // Separate PHP file to handle the request
                data: {
                    selectedRadio: $('input[name="radioGroup"]:checked').val(),
                    email: emailValue,
                    password: passwordValue,
					clientid: clientidValue,

					companyname: companynameValue,
					customername: customernameValue,
					customerfirstname: customerfirstnameValue,
					customervat: customervatValue,

					accountcode: accountcodeValue,
					accountname: accountnameValue
                },
				dataType: "json", // Expect JSON response
                success: function(response) {
					console.log(response);
                    // Handle the success response if needed
                    console.log(response.message); // Access the response properties as needed
                    console.log(response.option); // Access the response properties as needed
                    console.log(response.data); // Access the response properties as needed
					if( response.option=='alert' )
						alert("Result :\n" + response.data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle the error if needed
                    console.log("AJAX Error: " + textStatus + " " + errorThrown);
                }
            });
        }

        function handleCancelButtonClick() {
            //console.log("handleCancelButtonClick()");
            // Reset the form or perform any other action
            document.getElementById("myForm").reset();
        }
    </script>
</body>
</html>
