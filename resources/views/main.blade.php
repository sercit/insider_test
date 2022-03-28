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
        <h2>Tournament Teams</h2>
        <table class="teams">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Power</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <a id="generate" class="waves-effect waves-light btn">Generate Fixtures</a>
    </div>


    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        function generateFixtures(tournamentId)
        {
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "api/tournament/"+tournamentId+"/generate", true);

            xhr.onload = function () {
                if(this.status === 200) {
                    document.location.replace("/fixtures");
                }
            }

            xhr.send()
        }

        function getClubs(tournamentId)
        {
            const xhr = new XMLHttpRequest();

            xhr.open("GET", "api/tournament/"+tournamentId+"/clubs", true);

            xhr.onload = function () {
                if(this.status === 200){
                    obj = JSON.parse(this.responseText);

                    let teamsList = document.getElementsByTagName("tbody")[0];
                    str = ""
                    for (key in obj.data) {
                        str +=
                            `<tr>
                                <td>
                                    ${obj.data[key].name}
                                </td>
                                <td>
                                    ${obj.data[key].power}
                                </td>
                            </tr>`;
                    }
                    teamsList.innerHTML = str;
                }
            }

            xhr.send();
        }
        function getActiveTournament() {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "api/tournament", true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        data = JSON.parse(this.responseText);
                        localStorage.setItem('tournamentId', data.data.id);
                        resolve(data.data.id)
                    }
                }
                xhr.onerror = reject
                xhr.send();
            })
        }

        function isGeneratedFixtures()
        {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "api/tournament/"+localStorage.getItem('tournamentId')+"/fixture/active", true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        data = JSON.parse(this.responseText);
                        resolve(data.data.length)
                    }
                    reject()
                }
                xhr.onerror = reject
                xhr.send();
            })
        }

        document.getElementById("generate").addEventListener('click', function (event) {
            isGeneratedFixtures()
                .then(() => {
                    console.log(1)
                    document.location.replace("/fixtures");
                }, () =>{
                    console.log(2)
                    generateFixtures(localStorage.getItem('tournamentId'))
                }
            )


        });

        window.addEventListener("load", function () {
            if (!localStorage.getItem('tournamentId')) {
                getActiveTournament()
                    .then(tournamentId=>{
                        getClubs(tournamentId)
                    })
            }else {
                getClubs(localStorage.getItem('tournamentId'))
            }
        });
    </script>
</body>
</html>
