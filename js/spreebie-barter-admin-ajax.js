/*------------------------------------------------------------------*
 * The 'spreebie-barter-admin-ajax.js' file: Admin ajax, web3,
 * firebase and validation
 * @author Thabo David Nyakallo Klass
/*------------------------------------------------------------------*/

// The location of the 'admin-ajax.php' file
var ajaxurl = spreebie_barter_ajax_params.spreebie_barter_ajax_url;

// The send receipt via email nonce
var spreebie_barter_send_receipt_via_email_results_nonce = spreebie_barter_admin_ajax_data.spreebie_barter_send_receipt_via_email_results_nonce;

// Initialize Firebase application with configuration
// variables from the backend.
const config = {
    apiKey: "AIzaSyAs6C4MsnmV1XEWa66lzfz5Qkyy_UxIrK4",
    authDomain: "spreebiebarter.firebaseapp.com",
    databaseURL: "https://spreebiebarter.firebaseio.com",
    projectId: "spreebiebarter",
    storageBucket: "spreebiebarter.appspot.com",
    messagingSenderId: "144338937134"
};

// Initialize the application.
firebase.initializeApp(config);

// Get the reference to the database.
var database = firebase.database();

// The current user.
var currentUser;

// Check whether the user is on the main network or not
// Set this to true when in development mode in order to
// test on Ethereum test networks
var onMainNetwork = false;

// Listen for authentication state changes
firebase.auth().onAuthStateChanged(function(user) {
    if (user) {
        // Assign the new user as the current user
        currentUser = user;
    } else {
        // If the user is not logged in, sign them in anonymously
        firebase.auth().signInAnonymously().catch(function(error) {
            console.log("Error signing user in anonymously:", error);
        });
    }
});


/**
 * Save data after successful donation
 *
 * @param	none
 * @return	none
*/

function saveDonation(name, email, receiptNumber) {
    // Create a dBase reference based on the newly generated
    // user's ID
    const dbRefDonation = database.ref().child('donations');
    
    // Create a new donation key
    var donationID = dbRefDonation.push().key;

    // Create a reference to the full donation path
    const dbRefDonationComplete = database.ref().child('donations/' + donationID);
    
    // Get the current time 
    var time = Math.round((new Date()).getTime() / 1000);

    // The user data
    var donationData = {
        email: email,
        createdAt: time,
        name: name,
        receiptNumber: receiptNumber
    };

    // Save the new user's data
    dbRefDonationComplete.set(donationData).then(() => {
        console.log("Success! Donation data saved.");
    });
}

/**
* Validate input
*
* This validates the data that an
* admin enters in the plugin
* backend
*
*/

jQuery(document).ready(function($) {
    $('#spreebie_barter_main_content').fadeIn('slow');

    $('#spreebie_barter_input_contains_quotes').hide();;

    var spreebieBarterSaveChangesForm = $("#spreebie_barter_save_changes_form");
    var spreebieBarterEthereumKey = $("#spreebie_barter_ethereum_address");
    var spreebieBarterApiKey = $("#spreebie_barter_api_key");
    var spreebieBarterAuthDomain = $("#spreebie_barter_auth_domain");
    var spreebieBarterDatabaseUrl = $("#spreebie_barter_database_url");
    var spreebieBarterProjectId = $("#spreebie_barter_project_id");
    var spreebieBarterStorageBucket = $("#spreebie_barter_storage_bucket");
    var spreebieBarterMessagingSenderId = $("#spreebie_barter_messaging_sender_id");

    var spreebieBarterPaymentForm = $("#spreebie_barter_payment_form");
    var spreebieBarterPaymentTitle = $("#spreebie_barter_payment_title");
    var spreebieBarterCustomerName = $("#spreebie_barter_customer_name");
    var spreebieBarterCustomerEmail = $("#spreebie_barter_customer_email");
    var spreebieBarterPaymentAmount = $("#spreebie_barter_payment_amount");
    var spreebieBarterPaymentDescription = $("#spreebie_barter_payment_description");

    var spreebieBarterDonationForm = $("#spreebie_barter_donation_form");
    var spreebieBarterDonationTitle = $("#spreebie_barter_donation_title");
    var spreebieBarterDonationDescription = $("#spreebie_barter_donation_description");

    var spreebieBarterEmailForm = $("#spreebie_barter_email_form");
    var spreebieBarterErrorFromEmail = $("#spreebie_barter_error_from_email");
    var spreebieBarterErrorDescription = $("#spreebie_barter_error_description");

    var spreebieBarterSupportDonationForm = $("#spreebie_barter_support_donation_form");
    var spreebieBarterDonationFullName = $("#spreebie_barter_donation_full_name");
    var spreebieBarterDonationEmail = $("#spreebie_barter_donation_email");
    var spreebieBarterDonationAmount = $("#spreebie_barter_donation_amount");

    // The ethereum price in US Dollars
    var spreebieBarterEthereumPriceUSD;

    // The transaction hash
    var transcationHash;

    // The transaction receipt
    var transcationReceipt;

    // Hide warning when the apiKey field is clicked
    spreebieBarterApiKey.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Hide warning when the authDomain field is clicked
    spreebieBarterAuthDomain.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Hide warning when the databaseUrl field is clicked
    spreebieBarterDatabaseUrl.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Hide warning when the projectId field is clicked
    spreebieBarterProjectId.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Hide warning when the storageBucket field is clicked
    spreebieBarterStorageBucket.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Hide warning when the messagingSenderID field is clicked
    spreebieBarterMessagingSenderId.focus(function(e) {
        $('#spreebie_barter_input_contains_quotes').hide();
    });

    // Prevents saving if one of the inputs has quotes
    spreebieBarterSaveChangesForm.submit(function(e) {
        if (containsQuotes(spreebieBarterEthereumKey.val())) {
            e.preventDefault();
            $('#spreebie_barter_input_contains_quotes').show();
        }
    });

  
    /**
     * This checks if a piece of text contain
     * a single or double quote
     *
     * @param	text
     * @return	none
    */

    function containsQuotes(text) {
        if ((text.indexOf('\'') > -1) || (text.indexOf('"') >= 0)) {
            return true;
        }

        return false;
    }


    // checks for empty input
    function checkForEmptyInput(inputField) {
        if (inputField.val().length < 1) {
            return false;
        } else {
            return true;
        }
    }

    // Check if input value is not a number
    function isInputANumber(inputField) {
        var value = inputField.val();

        if (isNaN(value)) {
            return false;
        }
        return true;
    }

    // Validate the email for donation
    function validateEmailForDonation(emailField) {
        if (checkForEmptyInput(emailField)) {
            var email = emailField.val();
            
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
        
        return true;
    }

    // Validate the email
    function validateEmail(email) {
        console.log("The email is: " + email);
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    spreebieBarterPaymentTitle.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    spreebieBarterCustomerName.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    spreebieBarterCustomerEmail.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    spreebieBarterPaymentAmount.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    spreebieBarterPaymentDescription.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    //// validation function for all payment form input
    spreebieBarterPaymentForm.submit(function(e) {
        if (!checkForEmptyInput(spreebieBarterPaymentTitle) ||
            !checkForEmptyInput(spreebieBarterCustomerName) ||
            !checkForEmptyInput(spreebieBarterCustomerEmail) ||
            !checkForEmptyInput(spreebieBarterPaymentAmount) ||
            !checkForEmptyInput(spreebieBarterPaymentDescription) ||
            $('select[name=spreebie_barter_payment_currency]').val() == "Pick a currency") {
            e.preventDefault();
            $('#spreebie_barter_incomplete_dialog').show();
        }

        if (!validateEmail(spreebieBarterCustomerEmail.val())) {
            e.preventDefault();
            $('#spreebie_barter_incomplete_dialog').show();
        }

        if (!isInputANumber(spreebieBarterPaymentAmount)) {
            e.preventDefault();
            $('#spreebie_barter_incomplete_dialog').show();
        }
    });

    spreebieBarterDonationTitle.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });
    
    spreebieBarterDonationDescription.focus(function(e) {
        $('#spreebie_barter_incomplete_dialog').hide();
    });

    //// validation function for all donation form input
    spreebieBarterDonationForm.submit(function(e) {
        if (!checkForEmptyInput(spreebieBarterDonationTitle) ||
            !checkForEmptyInput(spreebieBarterDonationDescription) ||
            $('select[name=spreebie_barter_donation_currency]').val() == "Pick a currency") {
            e.preventDefault();
            $('#spreebie_barter_incomplete_dialog').show();
        }
    });

    spreebieBarterErrorFromEmail.focus(function(e) {
        $('#spreebie_barter_support_fields_not_filled').hide();
    });
    
    spreebieBarterErrorDescription.focus(function(e) {
        $('#spreebie_barter_support_fields_not_filled').hide();
    });

    //// validation function for support form input
    spreebieBarterEmailForm.submit(function(e) {
        if (!checkForEmptyInput(spreebieBarterErrorFromEmail) ||
            !checkForEmptyInput(spreebieBarterErrorDescription)) {
            e.preventDefault();
            $('#spreebie_barter_support_fields_not_filled').show();
        }

        if (!validateEmail(spreebieBarterErrorFromEmail.val())) {
            e.preventDefault();
            $('#spreebie_barter_support_fields_not_filled').show();
        }
    });


    /**
     * This loads the web3 instances either injected or running
     * on a local server
     *
     * @param	none
     * @return	none
    */

    function loadWeb3() {
        if (typeof web3 !== 'undefined') {
            web3 = new Web3(web3.currentProvider);
        } else {
            // set the provider you want from Web3.providers
            web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
        }

        web3.eth.defaultAccount = web3.eth.accounts[0];

        var network = web3.version.network;

        if (network === '1') {
            onMainNetwork = true;
        } 
    }

    // Load the Web3
    loadWeb3();


    /**
     * This gets the receipt from the transcation hash
     * using a three second waiting period
     *
     * @param	none
     * @return	none
    */

    function getTheReceipt() {
        web3.eth.getTransactionReceipt(transcationHash, function(error, result) {
            if(!error) {
                console.log(result);
                transcationReceipt = result;
            } else
                console.error(error);
        });

        if (!transcationReceipt) {
            setTimeout(getTheReceipt, 3000);
        } else {
            $('#spreebie_barter_donation_information').empty();
            $('#spreebie_barter_donation_information').append("<p>Thank you very much! Your transaction has been processed.</p>");
            $('#spreebie_barter_donation_information').show();

            // The data that is to be passed as post data
            // to a php callback called spreebie_barter_update_payment_settled_ajax()
            data = {
                action: 'spreebie_barter_send_receipt_via_email_results',
                spreebie_barter_donator_email: spreebieBarterDonationEmail.val(),
                spreebie_barter_donator_name: spreebieBarterDonationFullName.val(),
                spreebie_barter_send_receipt_via_email_results_nonce: spreebie_barter_send_receipt_via_email_results_nonce
            };
            
            // This section empties an existing section of HTML
            // and replaces it with HTML from the afformentioned
            // callback
            $.post(ajaxurl, data, function(response) {
                // Save puchase data
                saveDonation(spreebieBarterDonationFullName.val(), spreebieBarterDonationEmail.val(), response);
            });
        }
    }

    spreebieBarterDonationFullName.focus(function(e) {
        $('#spreebie_barter_donation_fields_not_filled').hide();
    });
    
    spreebieBarterDonationEmail.focus(function(e) {
        $('#spreebie_barter_donation_fields_not_filled').hide();
    });

    spreebieBarterDonationAmount.focus(function(e) {
        $('#spreebie_barter_donation_fields_not_filled').hide();
    });

    //// validation function for donation form input
    spreebieBarterSupportDonationForm.submit(function(e) {
        e.preventDefault();
        
        if (!checkForEmptyInput(spreebieBarterDonationAmount)) {
            $('#spreebie_barter_donation_fields_not_filled').show();
        } else {
            if (validateEmailForDonation(spreebieBarterDonationEmail)) {
                if (isInputANumber(spreebieBarterDonationAmount) == true) {
                    var etherPrice = parseFloat(spreebieBarterEthereumPriceUSD);
                    var donationAmount = parseFloat(spreebieBarterDonationAmount.val());

                    // If on the main network
                    if (onMainNetwork == true) {
                        // Check that neither of the inputs are empty
                        if (etherPrice) {
                            console.log("The price is: " + etherPrice);
                            console.log("The donation amount is: " + donationAmount);
                            var paymentAmountInEther = donationAmount / etherPrice;

                            var send = web3.eth.sendTransaction({
                                from: web3.eth.defaultAccount,
                                to: '0x00Da1fcc334d3C5fB0Fcc599B7E644a1a4fdbAef',
                                value: web3.toWei(paymentAmountInEther, "ether")
                            },  function(error, result) {
                                if(!error) {
                                    console.log(result);

                                    // Get the transaction hash
                                    transcationHash = result;

                                    $('#spreebie_barter_donation_information').empty();
                                    $('#spreebie_barter_donation_information').append("<p>Please wait - the transaction is being processed. IMPORTANT: DO NOT CLOSE THIS PAGE.</p>");
                                    $('#spreebie_barter_donation_information').show();

                                    // Now that you have the transaction hash, get the receipt
                                    getTheReceipt();
                                } else
                                    console.error(error);
                            });
                        } else {
                            $('#spreebie_barter_donation_information').empty();
                            $('#spreebie_barter_donation_information').append("<p>Sorry! Something went wrong. Please refresh the page and restart the donation process.</p>");
                            $('#spreebie_barter_donation_information').show();
                        }
                    } else {
                        $('#spreebie_barter_donation_information').empty();
                        $('#spreebie_barter_donation_information').append("<p>Please switch to the main network to process your donation.</p>");
                        $('#spreebie_barter_donation_information').show();
                    }
                } else {
                    $('#spreebie_barter_donation_information').empty();
                    $('#spreebie_barter_donation_information').append("<p>Please enter a number in the amount field.</p>");
                    $('#spreebie_barter_donation_information').show();
                }
            } else {
                $('#spreebie_barter_donation_information').empty();
                $('#spreebie_barter_donation_information').append("<p>Please enter a correct email address.</p>");
                $('#spreebie_barter_donation_information').show();
            }
        }
    });

    // Load prices from external provider - USD
    function loadEthereumPriceUSD() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=USD", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_usd") {
                    spreebieBarterEthereumPriceUSD = val;
                }
            });
        });
    }

    loadEthereumPriceUSD();
});