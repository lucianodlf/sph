<!DOCTYPE html>
<html lang="es">
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
        <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" style="margin-top: 5px;" onclick="w3_open();"><i class="fa fa-bars"></i> Menu</button>
        <img src="/assets/logo-bertone-v10.png" class="w3-bar-item w3-right" style="height: 60px;">
    </div>

    <!-- Sidebar/menu -->
    <nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:200px;" id="mySidebar"><br>
        <!-- <div class="w3-container w3-row">
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
        <hr> -->
        <div class="w3-container">
            <h5>Menu</h5>
        </div>
        <div class="w3-bar-block">
            <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i> Cerrar Menu</a>
            <a href="#" id="menuItemProcess" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-tasks fa-fw"></i> Procesamiento</a>
            <a href="#" id="menuItemConfig" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cog fa-fw"></i> Configuracion</a>
        </div>
    </nav>


    <!-- Overlay effect when opening sidebar on small screens -->
    <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

    <!-- !PAGE CONTENT! -->
    <div class="w3-main" style="margin-left:200px;margin-top:43px;">

        <section id="processFile">
            <!-- Header -->
            <header class="w3-container" style="padding-top:22px">
                <h5 style="margin-left:30px;"><b><i class="fa fa-tasks"></i> Procesamiento de horas</b></h5>
            </header>

            <div class="w3-row-padding w3-margin-bottom">
                <div id="msgResponseError" class="w3-panel w3-red w3-round-large" style="display: none; padding:10px; margin:30px;"></div>
                <div id="msgResponseSuccess" class="w3-panel w3-green w3-round-large" style="display: none; padding:10px; margin:30px;"></div>

                <form lang="es" action="uploadFile.php" method="POST" id="formUploadFile" enctype="multipart/form-data" class="w3-container w3-light-grey w3-margin">

                    <input class="w3-input w3-border w3-round-large" lang="es" type="file" id="file" name="file" accept="xlsx"><br>
                    <input class="w3-btn w3-light-blue w3-round-large" type="submit" value="Procesar">
                    <img id="loadingGif" src="assets/Infinity-1s-200px.gif" style="width:50%;max-width:50px;display: none;" class="w3-round" alt="loading">
                    <hr>

                    <div id="proccessResult" style="display: none;">
                        <label>Archivo exportado:</label>
                        <textarea class="w3-input w3-border w3-round-large" id="taProccessResult" name="taProccessResult" rows="10" cols="100" readonly="readonly"></textarea>
                    </div>

                </form>

            </div>


        </section>

        <section id="config" style="display: none;">
            <!-- Header -->
            <header class="w3-container" style="padding-top:22px">
                <h5 style="margin-left:30px;"><b><i class="fa fa-cog"></i> Configuracion</b></h5>
            </header>

            <div class="w3-row-padding w3-margin-bottom">
                <div id="msgCfgResponseError" class="w3-panel w3-red w3-round-large" style="display: none; padding:10px; margin:30px;"></div>
                <div id="msgCfgResponseSuccess" class="w3-panel w3-green w3-round-large" style="display: none; padding:10px; margin:30px;"></div>

                <form lang="es" action="#" method="POST" id="formSaveConfig" enctype="multipart/form-data" class="w3-container w3-light-grey w3-margin">
                    <label for="taFeriados">Feriados</label>
                    <textarea class="w3-input w3border w3-round-large" id="taFeriados" name="taFeriados" rows="5" cols="20" placeholder="Fechas separadas por ,(coma) en formato dd/mm/yyyy: 01/01/2020,24/02/2020,..."></textarea>
                    <p style="font-size: 8pt; margin-top:2px;" class="w3-text-dark-gray">Fechas separadas por ,(coma) en formato dd/mm/yyyy: 01/01/2020,24/02/2020,...</p>

                    <hr>

                    <label for="taEmployees">Empleados</label>
                    <textarea class="w3-input w3border w3-round-large" id="taEmployees" name="taEmployees" rows="5" cols="20" placeholder="Códigos de empleados separados por ,(coma): 1,4,109,..."></textarea>
                    <p style="font-size: 8pt; margin-top:2px;" class="w3-text-dark-gray">Códigos de empleados separados por ,(coma): 1,4,109,...</p>

                    <hr>
                    <input class="w3-btn w3-light-blue w3-round-large" type="submit" value="Guardar">
                    <img id="loadingGif" src="assets/Infinity-1s-200px.gif" style="width:50%;max-width:50px;display: none;" class="w3-round" alt="loading">
                    <hr>
                </form>

            </div>
        </section>

        <!-- Footer -->
        <!-- <footer class="w3-container w3-padding-16 w3-light-grey">
            <h4>FOOTER</h4>
        </footer> -->

        <!-- End page content -->
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function(e) {

            console.debug('ready document');

            $("#formSaveConfig").on('submit', (function(e) {
                e.preventDefault();

                console.debug('submit save config');

                $("#msgCfgResponseError").html("").hide();
                $("#msgCfgResponseSuccess").html("").hide();
                $("#taFeriados").html("");
                $("#loadingGif").hide();

                $.ajax({
                    url: "saveConfig.php",
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
                            $("#msgCfgResponseError").html(data.msg).fadeIn(1000);

                        } else {

                            console.debug('Configuracion guardada');
                            $("#msgCfgResponseSuccess").html(data.msg).fadeIn(1000);
                            $("#formSaveConfig")[0].reset();

                            loadConfig();

                        }

                        $("#loadingGif").hide();
                    },
                    error: function(data) {
                        $("msgResponseError").html(e).fadeIn();
                        $("#loadingGif").hide();
                    }
                });

            }));

            $("#formUploadFile").on('submit', (function(e) {
                e.preventDefault();

                console.debug("submit file");

                $("#msgResponseError").html("").hide();
                $("msgResponseSuccess").html("").hide();
                $("#taProccessResult").html("");
                $("#proccessResult").hide();
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

                            window.location.href = data.serverpath;

                            $("#taProccessResult").html(data.localpath);
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


            $("#menuItemConfig").click(function() {
                console.debug('menuItemConfig click');
                $("#processFile").hide();
                $("#config").show();

                $(this).addClass('w3-blue');
                $("#menuItemProcess").removeClass('w3-blue');

                $("#msgCfgResponseError").html("").hide();
                $("#msgCfgResponseSuccess").html("").hide();

                loadConfig();

            });

            $("#menuItemProcess").click(function() {
                console.debug('menuItemProcess click');
                $("#processFile").show();
                $("#config").hide();

                $(this).addClass('w3-blue');
                $("#menuItemConfig").removeClass('w3-blue');

                $("#msgResponseError").html("").hide();
                $("#msgResponseSuccess").html("").hide();
            });
        });


        function loadConfig() {
            console.debug('Load data config')

            $.ajax({
                url: "loadConfig.php",
                type: "POST",
                data: {
                    code: 'load_config'
                },
                success: function(data) {
                    console.debug('Success load data');
                    // console.debug(data);

                    data = JSON.parse(data);

                    // console.debug(data);

                    if(data.status == 1){
                        //TODO: Mostrar y cargar feriados por año, se debe quitar el año hardcode aqui y en todos lados.
                        $("#taFeriados").html(data.data.feriados['2020']);
                        $("#taEmployees").html(data.data.empleados.empleados);
                    }

                },
                error: function(data) {
                    console.debug('Ocurrio un error al cargar datos de configuracion');
                    console.error(data);

                }
            });
        }


        // --------------------------------------------------------------------
        // Get the Sidebar
        var mySidebar = document.getElementById("mySidebar");

        // Get the DIV with overlay effect
        var overlayBg = document.getElementById("myOverlay");

        // Toggle between showing and hiding the sidebar, and add overlay effect
        function w3_open() {
            if (mySidebar.style.display === 'block') {
                mySidebar.style.display = 'none';
                overlayBg.style.display = "none";
            } else {
                mySidebar.style.display = 'block';
                overlayBg.style.display = "block";
            }
        }

        // Close the sidebar with the close button
        function w3_close() {
            mySidebar.style.display = "none";
            overlayBg.style.display = "none";
        }
    </script>

</body>

</html>