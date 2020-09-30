<!DOCTYPE html>
<html>
<title>SPH - Bertone</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    html,
    body,
    h1,
    h2,
    h3,
    h4,
    h5 {
        /* font-family: "Raleway", sans-serif */
        font-family: 'Fira Mono', monospace;
    }

    textarea {
        resize: none;
        font-size: 9pt;
    }
</style>

<body class="w3-light-grey">

    <!-- Top container -->
    <div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
        <!-- <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i> Menu</button> -->
        <span class="w3-bar-item w3-right">SPH - Premoldeados Bertone</span>
    </div>

    <!-- Sidebar/menu -->
    <!-- <nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
        <div class="w3-container w3-row">
            <div class="w3-col s4">
                <img src="/w3images/avatar2.png" class="w3-circle w3-margin-right" style="width:46px">
            </div>
            <div class="w3-col s8 w3-bar">
                <span>Welcome, <strong>Mike</strong></span><br>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-envelope"></i></a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-user"></i></a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-cog"></i></a>
            </div>
        </div>
        <hr>
        <div class="w3-container">
            <h5>Dashboard</h5>
        </div>
        <div class="w3-bar-block">
            <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i> Close Menu</a>
            <a href="#" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-users fa-fw"></i> Overview</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-eye fa-fw"></i> Views</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i> Traffic</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bullseye fa-fw"></i> Geo</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-diamond fa-fw"></i> Orders</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bell fa-fw"></i> News</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-bank fa-fw"></i> General</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-history fa-fw"></i> History</a>
            <a href="#" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cog fa-fw"></i> Settings</a><br><br>
        </div>
    </nav> -->


    <!-- Overlay effect when opening sidebar on small screens -->
    <!-- <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div> -->

    <!-- !PAGE CONTENT! -->
    <div class="w3-main" style="margin-left:auto;margin-top:43px;">

        <!-- Header -->
        <header class="w3-container" style="padding-top:22px">
            <h5 style="margin-left:30px;"><b><i class="fa fa-dashboard"></i> Procesamiento de horas</b></h5>
        </header>

        <div class="w3-row-padding w3-margin-bottom">
            <div id="msgResponseError" class="w3-panel w3-red w3-round-large" style="display: none; padding:10px;"></div>
            <div id="msgResponseSuccess" class="w3-panel w3-green w3-round-large" style="display: none; padding:10px;"></div>

            <form action="uploadFile.php" method="POST" id="formUploadFile" enctype="multipart/form-data" class="w3-container w3-light-grey w3-text-blue w3-margin">
                <input class="w3-input w3-border w3-round-large" type="file" id="file" name="file" accept="xlsx"><br>
                <input class="w3-btn w3-light-blue w3-round-large" type="submit" value="Cargar">
                <img id="loadingGif" src="assets/Infinity-1s-200px.gif" style="width:50%;max-width:50px;display: none;" class="w3-round" alt="loading">
                <hr>

                <div id="proccessResult" style="display: none;">
                    <label>Archivo exportado:</label>
                    <textarea class="w3-input w3-border w3-round-large" id="taProccessResult" name="taProccessResult" rows="10" cols="100" readonly="readonly"></textarea>
                </div>

            </form>

        </div>

        <hr>

        <!-- Footer -->
        <!-- <footer class="w3-container w3-padding-16 w3-light-grey">
            <h4>FOOTER</h4>
        </footer> -->

        <!-- End page content -->
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function(e) {

            $("#proccessResult").hide();

            console.debug('ready document');

            $("#formUploadFile").on('submit', (function(e) {
                e.preventDefault();

                console.debug("submit file");

                $("#msgResponseError").html("").hide();
                $("msgResponseSuccess").html("").hide();
                $("#taProccessResult").html("");
                $("#proccessResult").hide();
                $("#progressBar").hide();
                $("#loadingGif").hide();

                $.ajax({
                    url: "fileUpload.php",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        console.debug("before send action");

                        $("#loadingGif").show();

                    },
                    success: function(data) { //TODO: simplificar condicionales para mensajes
                        console.debug(data);

                        data = JSON.parse(data);

                        if (data.status == 0) {

                            console.debug(data.msg);
                            $("#msgResponseError").html(data.msg).fadeIn();

                        } else if (data.status == 2) {

                            console.debug("Error: ocurrio un error al mover el archivo (move_uploaded_file...)");
                            $("#msgResponseError").html(data.msg).fadeIn();

                        } else if (data.status == 3) {

                            console.debug("Extencion de archivo invalida");
                            $("#msgResponseError").html(data.msg).fadeIn();
                            $("#formUploadFile")[0].reset();

                        } else if (data.status == 4) {

                            console.debug("Error en ejecucion de sph.php. Revisar log");
                            $("#msgResponseError").html(data.msg).fadeIn();
                            $("#formUploadFile")[0].reset();

                        } else { //data.status == 1

                            console.debug('Archivo cargado');
                            $("#msgResponseSuccess").html("Archivo procesado!").fadeIn();
                            $("#formUploadFile")[0].reset();

                            $("#taProccessResult").html(data.msg);
                            $("#proccessResult").fadeIn();

                        }

                        $("#loadingGif").hide();
                    },
                    error: function(data) {
                        $("msgResponseError").html(e).fadeIn();
                        $("#loadingGif").hide();
                    }
                });

            }));
        });


        // --------------------------------------------------------------------
        // Get the Sidebar
        // var mySidebar = document.getElementById("mySidebar");

        // Get the DIV with overlay effect
        // var overlayBg = document.getElementById("myOverlay");

        // Toggle between showing and hiding the sidebar, and add overlay effect
        /* function w3_open() {
            if (mySidebar.style.display === 'block') {
                mySidebar.style.display = 'none';
                overlayBg.style.display = "none";
            } else {
                mySidebar.style.display = 'block';
                overlayBg.style.display = "block";
            }
        } */

        // Close the sidebar with the close button
        /* function w3_close() {
            mySidebar.style.display = "none";
            overlayBg.style.display = "none";
        } */
    </script>

</body>

</html>