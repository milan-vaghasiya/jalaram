// Variable to store the initial IP address
let initialIP = null;
$(document).ready(function(){
    var lastActivityTime = new Date();

	// Update last activity time on user interaction events //mousemove
	$(document).on('click change keydown', function() {
		var idleTime = 7200; //Session Time
		var currentDateTime = new Date();

		// Calculate the time difference in milliseconds
		var idleThreshold = currentDateTime - lastActivityTime;

		// Convert the time difference to seconds
        var secondsDifference = Math.floor(idleThreshold / 1000);

		if (secondsDifference > idleTime) {
			// Idle time exceeded threshold, perform actions or redirect user
			//console.log('User is idle');
			window.location.reload();
			// Perform any necessary actions or redirect the user
		} else {
			// User is active, perform any necessary actions
			lastActivityTime = new Date();
		}		
	});

	// Check last activity time every second
	setInterval(function() {
		var idleTime = 7200; //Session Time
		var currentDateTime = new Date();

		// Calculate the time difference in milliseconds
		var idleThreshold = currentDateTime - lastActivityTime;

		// Convert the time difference to seconds
        var secondsDifference = Math.floor(idleThreshold / 1000);

		if (secondsDifference > idleTime) {
			// Idle time exceeded threshold, perform actions or redirect user
			//console.log('User is idle');
			autoLogout("Your time is out,");
			// Perform any necessary actions or redirect the user
		} else {
			// User is active, perform any necessary actions
			//console.log('User is active, Seconds : '+ secondsDifference);
		}
	}, 2000); // Check every two second (adjust interval as needed)

    // Check the IP address every 15 seconds
    setInterval(function() {
        getIPAddress(function(currentIP) {
            // If this is the first time, store the initial IP
            if (initialIP === null) {
                initialIP = currentIP;
            } else {
                // Compare current IP with the initial one
                if (currentIP !== initialIP) {
                    console.log('IP address changed. Logging out...');
                    autoLogout("IP address changed,");  // Trigger the logout function
                }
            }
        });
    }, 15000);  // 15000 milliseconds = 15 seconds
});

// Function to get the user's current public IP using ipify API
function getIPAddress(callback) {
    /* $.getJSON('https://api.ipify.org?format=json', function(data) {
        callback(data.ip); // Pass the IP address to the callback
    }); */

    fetch('https://api.ipify.org?format=json').then(response => response.json()).then(data => {
        // Do something with the data
        //console.log(data);
        callback(data.ip);
    }).catch(error => console.error('Error:', error));
}

//Function to check internet connection
function checkConnection() {
    var status = navigator.onLine;
    if (!status) {
        // Show the "No internet connection" popup if it's not already shown
        if(!$(".noInternet").is(':visible')) {
			Swal.close();
            Swal.fire({
                icon: "info",
                title: "No Internet Connection!",
                text: "Please check your network settings.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                backdrop: true,
				customClass: 'noInternet',
				willOpen: () => {
					Swal.showLoading(); // Show a loading indicator
				}
            });
        }
    } else {
        // Close the "No internet connection" popup if internet is back
		if ($(".noInternet").is(':visible')) {
			Swal.close($(this));
		}
    }
}

// Run the checkConnection function every 3 seconds
setInterval(checkConnection, 3000);

function autoLogout(message=""){
    if ($('.jconfirm-open').length === 0) {
        $.confirm({
            title: 'Logout?',
            content: message+' you will be automatically logged out in 10 seconds.',
            autoClose: 'logoutUser|10000',
            type: 'red',
            buttons: {
                logoutUser: {
                    text: 'logout myself',
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    action: function () {
                        window.location.href = base_url + '/logout';
                        //window.location.reload();
                    }
                }
            }
        });
    }
}