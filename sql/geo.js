const { Pool, Client } = require('pg');
const { exit } = require('process');
const NodeGeocoder = require('node-geocoder');


// Posgress connection
const connectionString = 'postgresql://doadmin:u3ue7itosyz79gqt@db-postgresql-sfo3-67017-do-user-441183-0.a.db.ondigitalocean.com:25060/defaultdb?sslmode=require?sslmode=require&ssl=true&rejectUnauthorized: false'
process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

fixAddresses();

function handle_hdn_geocode(iid, address, callback) {
  var geooptions = {
    provider: "google",

    // Optional depending on the providers
    httpAdapter: "https", // Default
    apiKey: "AIzaSyDboeuOSWLj-JMv5xse26DSxRb_8Xb7RI4", // for Mapquest, OpenCage, Google Premier
    formatter: null, // 'gpx', 'string', ...
  };

  var geocoder = NodeGeocoder(geooptions);

  geocoder.geocode(address, function (error, response) {
    console.log("Error: " + error);
    console.log("Response: "  + response);

    if ((response) && (response.length > 0)) {
      var place = response[0];
      console.log(place);
      callback({ "id": iid, "latitude":place.latitude, "longitude":place.longitude });
    } else {
      callback({ "id": 0, "latitude": 0, "longitude":0});
    }
    //callback(JSON.parse({"id":iid,"streetNumber":place.streetNumber + " " + place.streetName }));
  });
}

function fixAddresses() {

  const pool = new Pool({
    connectionString: connectionString,
    ssl: true,
  })


  console.log("select * from locations where id > 0 and status = 1 and  lat IS NULL limit 1");
  pool.query("select * from locations where id > 0 and status = 1 and lat IS NULL limit 1", (error, results) => {

    if (error) {
      console.log(error);

      return;
    }

    console.log("results.rows: " + results.rows.length);


    if (results.rows.length > 0) {

      for (var i = 0; i < results.rows.length; i++) {
        var address = results.rows[i].address + "," + results.rows[i].city + "," + results.rows[i].state + "," + results.rows[i].postal;
console.log(address);

        if (address.match(/^\d/)) {
          console.log(address);

          // Get data from google
          handle_hdn_geocode(results.rows[i].id, address, function (results) {

            console.log(results);

            if ((results["latitude"] > 0)) {
                console.log("update locations set lat = " + results["latitude"] +",lng = "+  results["latitude"] + " where id = " + results["id"]);
                pool.query("update locations set lat = " + results["latitude"] +",lng = "+  results["latitude"] + " where id = " + results["id"], (error, results) => {
                  process.exit(1);
                });
            }

          });
        }

      }
    } else {
      console.log("failed!1");
    }


  });

}
