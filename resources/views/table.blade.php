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
    <h2>Simulation</h2>
    <div class="container">
        <div class="row">
            <div class="col s12 m6">
                <table class="table">
                    <thead class="blue-grey white-text">
                        <th>#</th>
                        <th>Team name</th>
                        <th>P</th>
                        <th>W</th>
                        <th>D</th>
                        <th>L</th>
                        <th>GD</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col s12 m3">
                <div class="row">
                    <div class="prev-fixtures blue-grey darken-1">
                        <div class="card-content">
                            <span class="card-title white-text">Week</span>
                            <ul class="collection">

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="curr-fixtures blue-grey darken-1">
                        <div class="card-content">
                            <span class="card-title white-text">Week</span>
                            <ul class="collection">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
            </div>
        </div>
    </div>

    <a id="play-next" class="waves-effect waves-light btn">Play next week</a>
    <a id="play-all" class="waves-effect waves-light btn">Play all</a>
    <a id="reset" class="waves-effect waves-light btn red">Reset data</a>
</div>


<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    function playNext(fixtureId)
    {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "api/fixture/" + fixtureId, true);
            xhr.onload = function () {
                resolve(JSON.parse(this.responseText).data)
            }
            xhr.onerror = reject

            xhr.send()
        });
    }

    function playAll()
    {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "api/tournament/"+localStorage.getItem('tournamentId')+"/fixture/playAll", true);
            xhr.onload = function () {
                resolve(JSON.parse(this.responseText).data)
            }
            xhr.onerror = reject

            xhr.send()
        });
    }

    function resetData()
    {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "api/tournament/"+localStorage.getItem('tournamentId')+"/reset", true);
            xhr.onload = function () {
                data = JSON.parse(this.responseText).data
                resolve(data)
            }
            xhr.onerror = reject

            xhr.send()
        });
    }



    function getTable()
    {
        xhr = new XMLHttpRequest();
        xhr.open("GET", "api/tournament/"+localStorage.getItem('tournamentId')+"/table", true);

        xhr.onload = function () {
            if(this.status === 200) {
                data = JSON.parse(this.responseText);
                let tableList = document.querySelector(".table tbody")
                tableList.innerHTML = "";
                for(team in data.data){
                    tableList.insertAdjacentHTML("beforeend", "<td>"+ (parseInt(team) + 1) +"</td><td>" + data.data[team].name + "</td><td>" + data.data[team].points + "</td><td>" + data.data[team].wins + "</td><td>" + data.data[team].draws + "</td><td>" + data.data[team].loses + "</td><td>" + data.data[team].gd + "</td>")
                }
            }
        }


        xhr.send();
    }

    function refreshFixtures(fixtureId, loadPrev = true)
    {
        getFixtures(fixtureId)
            .then(
                currFixtures => {
                    document.querySelector(".curr-fixtures .card-title").innerHTML = "Week";
                    document.querySelector(".curr-fixtures .card-title").insertAdjacentHTML("beforeend", " " + currFixtures.week_id);
                    document.querySelector(".curr-fixtures .collection").innerHTML = "";
                    for (match in currFixtures.matches) {
                        document.querySelector(".curr-fixtures .collection").insertAdjacentHTML("beforeend", "<li>" + currFixtures.matches[match].home_club.name + " vs " + currFixtures.matches[match].away_club.name + "</li>");
                    }
                },
                reject => {
                    document.querySelector(".curr-fixtures .collection").innerHTML = "";
                    document.querySelector(".curr-fixtures .card-title").innerHTML = "All the games have been played";
                }
            )
        if(loadPrev) {
            getFixtures(fixtureId - 1)
                .then(
                    prevFixtures => {
                        document.querySelector(".prev-fixtures .card-title").innerHTML = "Week";
                        document.querySelector(".prev-fixtures .card-title").insertAdjacentHTML("beforeend", " " + prevFixtures.week_id);
                        document.querySelector(".prev-fixtures .collection").innerHTML = "";
                        for (match in prevFixtures.matches) {

                            document.querySelector(".prev-fixtures .collection")
                                .insertAdjacentHTML(
                                    "beforeend",
                                    "<li>" + prevFixtures.matches[match].home_club.name + " " + prevFixtures.matches[match].home_club_goals
                                    + " - "
                                    + prevFixtures.matches[match].away_club_goals + " " + prevFixtures.matches[match].away_club.name
                                    + "</li>");
                        }
                    },
                    reject => {
                        alert(reject);
                    }
                )
        }else{
            document.querySelector(".prev-fixtures .card-title").innerHTML = "This tournament doesnt have previous matches";
        }
    }

    function getFixtures(fixtureId)
    {
        return new Promise((resolve, reject) => {
            xhr = new XMLHttpRequest();
            xhr.open("GET", "api/fixture/"+fixtureId, true);

            xhr.onload = function () {
                if(this.status === 200){
                    data = JSON.parse(this.responseText);
                    resolve(data.data)
                }
            }

            xhr.onerror = reject
            xhr.send()
        })

    }

    function getActiveFixture()
    {
        return new Promise((resolve, reject) => {
            xhr = new XMLHttpRequest();
            xhr.open("GET", "api/tournament/"+localStorage.getItem("tournamentId")+"/fixture/active", true);
            xhr.onload = function () {
                if(this.status === 200){
                    data = JSON.parse(this.responseText);
                    localStorage.setItem('fixtureId', data.data.id)
                    resolve(data.data)
                }
                reject()
            }
            xhr.onerror = reject

            xhr.send()
        });
    }

    window.addEventListener("load", function(event) {

        if(!localStorage.getItem('tournamentId')){
            alert("No active tournament. Redirect to main page")
            document.location.replace('/')
        }else{
            getTable()
            document.getElementById("reset").addEventListener('click', function (event) {
                resetData()
                    .then(tournament => {
                        localStorage.removeItem('fixtureId')
                        localStorage.setItem('tournamentId', tournament.id)
                        document.location.replace('/')
                    }, () => {
                        alert('Something wrong was happen. Please make another try')
                    })

            });
            getActiveFixture()
                .then(
                    fixture => {
                        refreshFixtures(fixture.id, false)

                        document.getElementById("play-next").addEventListener('click', function (event) {
                            playNext(localStorage.getItem('fixtureId'))
                                .then(() => {
                                    getActiveFixture().then(
                                        fixture => {
                                            getTable()
                                            refreshFixtures(localStorage.getItem('fixtureId'))
                                        },
                                        () => {
                                            getTable()
                                            document.querySelector(".curr-fixtures .collection").innerHTML = "";
                                            document.querySelector(".curr-fixtures .card-title").innerHTML = "All the games have been played";
                                            getFixtures(localStorage.getItem('fixtureId'))
                                                .then(
                                                    prevFixtures => {
                                                        document.querySelector(".prev-fixtures .card-title").innerHTML = "Week";
                                                        document.querySelector(".prev-fixtures .card-title").insertAdjacentHTML("beforeend", " " + prevFixtures.week_id);
                                                        document.querySelector(".prev-fixtures .collection").innerHTML = "";
                                                        for (match in prevFixtures.matches) {

                                                            document.querySelector(".prev-fixtures .collection")
                                                                .insertAdjacentHTML(
                                                                    "beforeend",
                                                                    "<li>" + prevFixtures.matches[match].home_club.name + " " + prevFixtures.matches[match].home_club_goals
                                                                    + " - "
                                                                    + prevFixtures.matches[match].away_club_goals + " " + prevFixtures.matches[match].away_club.name
                                                                    + "</li>");
                                                        }
                                                    }
                                                )
                                        }
                                    )

                                }, () => {
                                    alert('Something wrong was happen. Please make another try')
                                })

                        });

                        document.getElementById("play-all").addEventListener('click', function (event) {
                            playAll()
                                .then(fixtures => {
                                        getTable()
                                        document.querySelector(".curr-fixtures .collection").innerHTML = "";
                                        document.querySelector(".curr-fixtures .card-title").innerHTML = "All the games have been played";
                                        prevFixtures = fixtures.pop()
                                        localStorage.setItem('fixtureId', prevFixtures.id + 1)
                                        document.querySelector(".prev-fixtures .card-title").innerHTML = "Week";
                                        document.querySelector(".prev-fixtures .card-title").insertAdjacentHTML("beforeend", " " + prevFixtures.week_id);
                                        document.querySelector(".prev-fixtures .collection").innerHTML = "";
                                        for (match in prevFixtures.matches) {

                                            document.querySelector(".prev-fixtures .collection")
                                                .insertAdjacentHTML(
                                                    "beforeend",
                                                    "<li>" + prevFixtures.matches[match].home_club.name + " " + prevFixtures.matches[match].home_club_goals
                                                    + " - "
                                                    + prevFixtures.matches[match].away_club_goals + " " + prevFixtures.matches[match].away_club.name
                                                    + "</li>");
                                        }
                                }, () => {
                                    alert('Something wrong was happen. Please make another try')
                            })

                        });


                    },
                    () => {
                        document.querySelector(".curr-fixtures .collection").innerHTML = "";
                        document.querySelector(".curr-fixtures .card-title").innerHTML = "All the games have been played";
                        getFixtures(localStorage.getItem('fixtureId') - 1)
                            .then(
                                prevFixtures => {
                                    document.querySelector(".prev-fixtures .card-title").innerHTML = "Week";
                                    document.querySelector(".prev-fixtures .card-title").insertAdjacentHTML("beforeend", " " + prevFixtures.week_id);
                                    document.querySelector(".prev-fixtures .collection").innerHTML = "";
                                    for (match in prevFixtures.matches) {

                                        document.querySelector(".prev-fixtures .collection")
                                            .insertAdjacentHTML(
                                                "beforeend",
                                                "<li>" + prevFixtures.matches[match].home_club.name + " " + prevFixtures.matches[match].home_club_goals
                                                + " - "
                                                + prevFixtures.matches[match].away_club_goals + " " + prevFixtures.matches[match].away_club.name
                                                + "</li>");
                                    }
                                }
                            )
                    }
                )
        }
    });
</script>
</body>
</html>
