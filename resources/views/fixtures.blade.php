<!DOCTYPE html>
<html>
<head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body>
<div class="container">
    <h2>Fixtures</h2>
    <div id="fixtures">

    </div>
    <a id="start" class="waves-effect waves-light btn" href="/table">Start Simulation</a>
</div>


<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    window.addEventListener("load", function(event) {
        const xhr = new XMLHttpRequest();

        xhr.open("GET", "api/tournament/"+localStorage.getItem('tournamentId')+"/fixture", true);

        xhr.onload = function () {
            if(this.status === 200){
                obj = JSON.parse(this.responseText);


                let fixturesList = document.getElementById("fixtures");
                str = '<div class="row">'
                for (key in obj.data) {
                    str += `
                                <div class="col s6 m4">
                                <div class="card blue-grey darken-1">
                                <div class="card-content">
                                    <span class="card-title white-text">Week ` + obj.data[key].week_id + `</span>
                                    <ul class="collection">`;

                    for (matches in obj.data[key].matches){
                        str += '<li class="collection-item">'+obj.data[key].matches[matches].home_club.name + ' vs ' + obj.data[key].matches[matches].away_club.name + '</li>';
                    }

                    str += `        </ul>
                                </div>
                            </div>
                        </div>`;
                }
                str += '</div>'
                fixturesList.innerHTML = str;
            }
        }

        xhr.send();
    });
</script>
</body>
</html>
