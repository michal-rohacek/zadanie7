// function processIPLocationData(callback) {
//     var city;
//     var result;
//
//     $.ajax('http://api.ipapi.com/check?access_key=7a34148692bffe806756b18984af5e2d')
//     // $.ajax('https://api.ipdata.co?api-key=3dfbc87d56935473fcf3b68c6e599cfad8a25a017284d304912b550f')
//         .then(
//             function success(response) {
//                 console.log('User\'s Location Data is ', response);
//                 callback(filterResult(response));
//             },
//             function fail(data, status) {
//                 console.log('Request failed.  Returned status of', status);
//             }
//         );
//
// }

function logUserData() {
    $.ajax({
        url: "actions.php",
        type: "post",
        data: { action: "process_visit" },
        success: function (response) {
            console.log("data from php: " + JSON.stringify(response));
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}


// function filterResult(response) {
//
//     var city = (response.city == null) ? 'unknown' : response.city;
//
//     var result = {
//         ip: response.ip,
//         latitude: response.latitude,
//         longitude: response.longitude,
//         city: city,
//         country: response.country_name,
//         capital: response.location.capital,
//         country_code: response.country_code
//     };
//
//     return result;
// }

// function printData(input) {
//
//     var NEWLINE = "<br>";
//     var data = input;
//     var city = (data.city == 'unknown') ? "Mesto sa nedá lokalizovať, alebo sa nachádzate na vidieku" : data.city;
//     document.getElementById("output").innerHTML =
//         "IP adresa: " + data.ip + NEWLINE +
//         "GPS súradnice: " + data.latitude + " " + data.longitude + NEWLINE +
//         "Miesto: " + city + NEWLINE +
//         "Štát: " + data.country + NEWLINE +
//         "Hlavné mesto: " + data.capital;
// }

function incrementCounter() {
    var pageToIncrement = window.location.href.split("/").pop().split(".").shift();
    $.ajax({
        url: "actions.php",
        type: "post",
        data: { action: "increment_counter", page_name: pageToIncrement },
        success: function (response) {
            console.log("data from php (increment counter): " + response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function showDialogTable(countryCode) {
    $( function() {
        $( "#dialog" ).dialog({
            open: function(event, ui)
            {
                $.ajax({
                    url: "actions.php",
                    type: "post",
                    data: { action: "get_cities", country_code: countryCode },
                    success: function (response) {
                        console.log("data from php (cities query show dialog table): " + response);
                        drawTable("dialog", response, JSON.parse);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            },

        });
    } );
}

function drawTable(targetId, data, callback = false) {
    var responseDiv = document.getElementById(targetId);

    var table = document.createElement("table");
    var json = (callback) ? callback(data) : data;
    var index = 0;
    for(var k in json) {
        console.log(k);
        console.log(json[k]);
        var row = table.insertRow(index);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);

        cell1.innerHTML = k;
        cell2.innerHTML = json[k];
        index += 1;

    }
    responseDiv.innerHTML = "";
    responseDiv.appendChild(table);
}


function GetMap() {
    var map = new Microsoft.Maps.Map('#myMap', {
        credentials: 'AtDxCI05N6CVUwNCvfkqkfQEpEs1eIiIrseigcRoXi2pimCBCGClRmOT0fwN-uFA',
        center: new Microsoft.Maps.Location(48.15, 17.1078)
    });

    for(var i = 0; i < locationsData.length; i++) {
        var row = locationsData[i];
        addCustomPin(map, row.latitude, row.longitude, row.count, row.city);
    }
}

function addCustomPin(map, latitude, longitude, count, text) {
    var location = new Microsoft.Maps.Location(latitude, longitude);
    var pin = new Microsoft.Maps.Pushpin(location, {
        icon: 'images/poi-custom.png',
        title: text,
        text: count
    });

    map.entities.push(pin);
}