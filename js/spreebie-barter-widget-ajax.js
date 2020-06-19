/*------------------------------------------------------------------*
 * The 'spreebie-barter-widget-ajax' file: Spreebie Barter widget 
 * ajax code
 * @author Thabo David Nyakallo Klass
/*------------------------------------------------------------------*/

// The location of the 'admin-ajax.php' file
var ajaxurl = spreebie_barter_ajax_params.spreebie_barter_ajax_url;

// The token nonce
var spreebie_barter_get_details_results_nonce = spreebie_barter_ajax_data.spreebie_barter_get_details_results_nonce;

// The update payment nonce
var spreebie_barter_update_payment_settled_results_nonce = spreebie_barter_ajax_data.spreebie_barter_update_payment_settled_results_nonce;

// The website owner's ethereum address
var spreebieBarterOwnerEtheruemAddress = spreebie_barter_ajax_data.spreebie_barter_owner_etheruem_address;

// The ethereum price in US Dollars
var spreebieBarterEthereumPriceUSD;

// The ethereum price in Japanese Yen
var spreebieBarterEthereumPriceJPY;

// The ethereum price in Euros
var spreebieBarterEthereumPriceEUR;

// The ethereum price in Canadian Dollars
var spreebieBarterEthereumPriceCAD;

// The ethereum price in Australian Dollars
var spreebieBarterEthereumPriceAUD;

// The ethereum price in Brazillian Real
var spreebieBarterEthereumPriceBRL;

// The ethereum price in British Pounds
var spreebieBarterEthereumPriceGBP;

// The ethereum price in Russian Rubles
var spreebieBarterEthereumPriceRUB;

// The ethereum price in South Korean Won
var spreebieBarterEthereumPriceKRW;

// The transaction hash
var transcationHash;

// The transaction receipt
var transcationReceipt;

// The post id of the current transaction
var spreebieBarterPostID;

// The customer name of the current transaction
var spreebieBarterCustomerName;

// The customer email of the current transaction
var spreebieBarterCustomerEmail;

// The payment title of the current transaction
var spreebieBarterPaymentTitle;

// The payment currency of the current transaction
var spreebieBarterPaymentCurrency;

// The payment amount of the current transaction
var spreebieBarterPaymentAmount;

// If the transaction is a payment
var isPayment = false;

// Has the payment been settled?
var hasThePaymentBeenSettled = "No";

// Check whether the user is on the main network or not
// Set this to true when in development mode in order to
// test on Ethereum test networks
var onMainNetwork = false;


/**
 * Checks if the input is empty
 *
 * @param	none
 * @return	none
*/

function isEmpty(text) {
    return !text.replace(/^\s+/g, '').length; // boolean (`true` if field is empty)
}

jQuery(document).ready(function($) {
    // Check if input value is not a number
    function isInputANumber(inputField) {
        var value = inputField.val();

        if (isNaN(value)) {
            return false;
        }
        return true;
    }


    /**
     * This minimizes the chat box
     *
     * @param	none
     * @return	none
    */

    function minimize() {
        // Hide the minimized state ui
        $("#spreebie_barter_payments_donations_minimized").hide();

        $(".spreebie_barter_button_close").on("click", function () {
            $("#spreebie_barter_payments_donations_maximized").hide();
            $("#spreebie_barter_payments_donations_minimized").show();

            // Add listener to the minimized state ui
            $(".spreebie_barter_button_open").on("click", function () {
                $("#spreebie_barter_payments_donations_maximized").show();
                $("#spreebie_barter_payments_donations_minimized").hide();
            });
        });
    }

    minimize();


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
            if (isPayment == true) {
                // The data that is to be passed as post data
                // to a php callback called spreebie_barter_update_payment_settled_ajax()
                data = {
                    action: 'spreebie_barter_update_payment_settled_results',
                    spreebie_barter_post_id: spreebieBarterPostID,
                    spreebie_barter_customer_email: spreebieBarterCustomerEmail,
                    spreebie_barter_customer_name: spreebieBarterCustomerName,
                    spreebie_barter_payment_title: spreebieBarterPaymentTitle,
                    spreebie_barter_payment_currency: spreebieBarterPaymentCurrency,
                    spreebie_barter_payment_amount: spreebieBarterPaymentAmount,
                    spreebie_barter_update_payment_settled_results_nonce: spreebie_barter_update_payment_settled_results_nonce
                };
                
                // This section empties an existing section of HTML
                // and replaces it with HTML from the afformentioned
                // callback
                $.post(ajaxurl, data, function(response) {
                    // Do nothing
                });
            }

            $('#spreebie_barter_details').empty();
            $('#spreebie_barter_details').append("<p>Success! Your transaction has been processed.</p>");
        }
    }

    // Pay button clicked
    $("body").on("click", "#spreebie_barter_payment_button", function() {
        isPayment = true;

        spreebieBarterCustomerEmail = $('#spreebie_barter_customer_email').text();

        spreebieBarterCustomerName = $('#spreebie_barter_customer_name').text();

        spreebieBarterPaymentTitle = $('#spreebie_barter_payment_title').text();

        hasThePaymentBeenSettled = $('#spreebie_barter_payment_settled').text();

        spreebieBarterPostID = $('#spreebie_barter_post_id').text();

        spreebieBarterPaymentCurrency = $('#spreebie_barter_payment_currency').text();

        spreebieBarterPaymentAmount = $('#spreebie_barter_payment_amount').text();

        var paymentAmount = parseFloat(spreebieBarterPaymentAmount);
        var etherPrice;
        
        if (spreebieBarterPaymentCurrency == "AUD") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceAUD);
        }

        if (spreebieBarterPaymentCurrency == "BRL") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceBRL);
        }

        if (spreebieBarterPaymentCurrency == "CAD") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceCAD);
        }

        if (spreebieBarterPaymentCurrency == "EUR") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceEUR);
        }

        if (spreebieBarterPaymentCurrency == "GBP") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceGBP);
        }

        if (spreebieBarterPaymentCurrency == "JPY") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceJPY);
        }

        if (spreebieBarterPaymentCurrency == "RUB") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceRUB);
        }

        if (spreebieBarterPaymentCurrency == "USD") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceUSD);
        }

        if (spreebieBarterPaymentCurrency == "KRW") {
            etherPrice = parseFloat(spreebieBarterEthereumPriceKRW);
        }

        if (hasThePaymentBeenSettled == "Yes") {
            $('#spreebie_barter_details').empty();
            $('#spreebie_barter_details').append("<p>This payment has already been settled.</p>");
        } else {
            // If on the main network
            if (onMainNetwork == true) {
                // Check that neither of the inputs are empty
                if (paymentAmount && etherPrice) {
                    var paymentAmountInEther = paymentAmount / etherPrice;

                    var send = web3.eth.sendTransaction({
                        from: web3.eth.defaultAccount,
                        to: spreebieBarterOwnerEtheruemAddress,
                        value: web3.toWei(paymentAmountInEther, "ether")
                    },  function(error, result) {
                        if(!error) {
                            console.log(result);

                            // Get the transaction hash
                            transcationHash = result;

                            $('#spreebie_barter_details').empty();
                            $('#spreebie_barter_details').append("<p>Please wait, this may take a few minutes - the transaction is being processed.</p>");

                            // Now that you have the transaction hash, get the receipt
                            getTheReceipt();
                        } else
                            console.error(error);
                    });
                } else {
                    $('#spreebie_barter_details').empty();
                    $('#spreebie_barter_details').append("<p>Sorry! Something went wrong. Please refresh the page and restart the payment process.</p>");
                }
            } else {
                $('#spreebie_barter_details').empty();
                $('#spreebie_barter_details').append("<p>Please switch to the main network to process your transaction.</p>");
            }
        }
    });

    // Donate button clicked
    $("body").on("click", "#spreebie_barter_donation_button", function() {
        var currency = $('#spreebie_barter_donation_currency').text();

        if (isInputANumber($('#spreebie_barter_donation_amount_field'))) {
            var paymentAmountString = $('#spreebie_barter_donation_amount_field').val();

            // Sanitize the user input
            var sanitizedPaymentAmountString = DOMPurify.sanitize(paymentAmountString);

            var paymentAmount = parseFloat(sanitizedPaymentAmountString);
            var etherPrice;
            
            if (currency == "AUD") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceAUD);
            }

            if (currency == "BRL") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceBRL);
            }

            if (currency == "CAD") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceCAD);
            }

            if (currency == "EUR") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceEUR);
            }

            if (currency == "GBP") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceGBP);
            }

            if (currency == "JPY") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceJPY);
            }

            if (currency == "RUB") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceRUB);
            }

            if (currency == "USD") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceUSD);
            }

            if (currency == "KRW") {
                etherPrice = parseFloat(spreebieBarterEthereumPriceKRW);
            }

            // Check that neither of the inputs are empty
            if (paymentAmount && etherPrice) {
                var paymentAmountInEther = paymentAmount / etherPrice;

                var send = web3.eth.sendTransaction({
                    from: web3.eth.defaultAccount,
                    to: spreebieBarterOwnerEtheruemAddress,
                    value: web3.toWei(paymentAmountInEther, "ether")
                },  function(error, result) {
                    if(!error) {
                        console.log(result);

                        // Get the transaction hash
                        transcationHash = result;

                        $('#spreebie_barter_details').empty();
                        $('#spreebie_barter_details').append("<p>Please wait a few minutes - the transaction is being processed.</p>");

                        // Now that you have the transaction hash, get the receipt
                        getTheReceipt();
                    } else
                        console.error(error);
                });
            } else {
                $('#spreebie_barter_details').empty();
                $('#spreebie_barter_details').append("<p>Sorry! Something went wrong. Please refresh the page and restart the payment process.</p>");
            }
        } else {
            $('#spreebie_barter_details').empty();
            $('#spreebie_barter_details').append("<p>Sorry! It appears you didn't enter a number. Please refresh the page and try again.</p>");
        }
    });


    // This responds the 'Get' click
    $("#spreebie_barter_get_details").click(function() {
        var buttonText = $(this).val();

        if ($('#spreebie_barter_token_field').val() != "") {

            var token = $('#spreebie_barter_token_field').val();

            // Sanitize the user input
            var sanitizedToken = DOMPurify.sanitize(token);

            // The data that is to be passed as post data
            // to a php callback called spreebie_barter_get_details_ajax()
            data = {
                action: 'spreebie_barter_get_details_results',
                spreebie_barter_token: sanitizedToken,
                spreebie_barter_get_details_results_nonce: spreebie_barter_get_details_results_nonce
            };
            
            // This section empties an existing section of HTML
            // and replaces it with HTML from the afformentioned
            // callback
            $.post(ajaxurl, data, function(response) {
                $('#spreebie_barter_results').empty();
                $('#spreebie_barter_results').hide();
                $('#spreebie_barter_results').append(response);
                $('#spreebie_barter_results').fadeIn('slow')
            });
        } else {
            $('#spreebie_barter_token_field').attr("placeholder", "PLEASE enter the token...");
        }
        
        return false;
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


    // Load prices from external provider - JPY
    function loadEthereumPriceJPY() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=JPY", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_jpy") {
                    spreebieBarterEthereumPriceJPY = val;
                }
            });
        });
    }

    loadEthereumPriceJPY();


    // Load prices from external provider - EUR
    function loadEthereumPriceEUR() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=EUR", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_eur") {
                    spreebieBarterEthereumPriceEUR = val;
                }
            });
        });
    }

    loadEthereumPriceEUR();


    // Load prices from external provider - CAD
    function loadEthereumPriceCAD() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=CAD", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_cad") {
                    spreebieBarterEthereumPriceCAD = val;
                }
            });
        });
    }

    loadEthereumPriceCAD();


    // Load prices from external provider - AUD
    function loadEthereumPriceAUD() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=AUD", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_aud") {
                    spreebieBarterEthereumPriceAUD = val;
                }
            });
        });
    }

    loadEthereumPriceAUD();


    // Load prices from external provider - BRL
    function loadEthereumPriceBRL() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=BRL", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_brl") {
                    spreebieBarterEthereumPriceBRL = val;
                }
            });
        });
    }

    loadEthereumPriceBRL();


    // Load prices from external provider - GBP
    function loadEthereumPriceGBP() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=GBP", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_gbp") {
                    spreebieBarterEthereumPriceGBP = val;
                }
            });
        });
    }

    loadEthereumPriceGBP();


    // Load prices from external provider - RUB
    function loadEthereumPriceRUB() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=RUB", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_rub") {
                    spreebieBarterEthereumPriceRUB = val;
                }
            });
        });
    }

    loadEthereumPriceRUB();

    // Load prices from external provider - RUB
    function loadEthereumPriceKRW() {
        $.getJSON("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=KRW", function(data) {
            $.each(data[0], function(key, val) {
                if (key == "price_krw") {
                    spreebieBarterEthereumPriceKRW = val;
                }
            });
        });
    }

    loadEthereumPriceKRW();
});