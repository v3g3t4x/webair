<?php
include '../../config.php';
require '../common.php';

$Common = new Common();
if (!$Common->Is_User_Logged()) {
    header("Location: ../");
    die();
}

include $Common->Get_Language();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Console | Webair</title>
    <?php echo $Common->Get_Header(); ?>
</head>
<body>

<nav class="navbar">
    <div class="container-fluid page-header">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <p class="navbar-brand welcome">
                <?php
                echo $Common->Get_Login_Data()->message;
                $a = $Common->Get_Login_Data()->resultObj->latestAccess;
                echo '<br><small> Ultimo accesso: ' . date('d/m/Y', $a) . ' alle ' . date('H:s', $a) . '</small>';
                ?>
            </p>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <!--<md-button class="md-raised">IMPOSTAZIONI</md-button>-->
                </li>
                <li id="logout">
                    <md-button class="md-raised md-warn" id="logoutB">ESCI</md-button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid" id="home-device-list">
    <div class="row">
        <div class="col-xs-12 text-center" id="main-progress">
            <md-progress-circular class="md-hue-2" md-diameter="100px" id="progress"></md-progress-circular>
        </div>
    </div>
</div>
<!--<div style="margin-top: 100px; box-shadow: 0 10px 100px #888888;text-align: center;">
    <p style="padding: 20px;font-size: 18px;">&copy; 2016 WebPellet</p>
</div>-->

<?php echo $Common->getFooterScript(); ?>

<script type="text/javascript">

    var counter;
    var PLATFORM = "WEB";
    var DEVICES;
    function setupAngular1() {
        angular
            .module("Webair_1", ["ngMaterial", "ngMessages"]);
        angular.bootstrap($('#logout'), ['Webair_1']);
        angular.bootstrap($('#main-progress'), ['Webair_1']);

    }
    function setupAngular2() {

        angular
            .module("Webair_2", ["ngMaterial", "ngMessages"])
            .controller('DIALOG', function ($scope, $mdDialog, $mdMedia) {
                $scope.customFullscreen = $mdMedia('xs') || $mdMedia('sm');

                $scope.showStatusDialog = function (ev, index) {
                    $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'status.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        onComplete: getDeviceStatus(DEVICES[index].id),
                        onRemoving: function () {
                            clearInterval(counter);
                        }
                    });
                };

                $scope.showTelecomandoDialog = function (ev, index) {
                    $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'telecomando.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        onComplete: getDeviceTelecomando(DEVICES[index].id, DEVICES[index].nameDevice)
                    });
                };

                $scope.showWebcamDialog = function (ev, index) {
                    $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'webcam.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        onComplete: getDeviceWebcam(DEVICES[index].id, DEVICES[index].nameDevice)
                    });
                };

                $scope.showSchedulazioniDialog = function (ev, index) {
                    $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'schedulazioni.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        onComplete: getDeviceSchedulazione(DEVICES[index].id, DEVICES[index].nameDevice)
                    });
                };
            });

        angular.bootstrap($('#pippo'), ['Webair_2']);
    }

    function DialogController($scope, $mdDialog) {
        $scope.hide = function () {
            $mdDialog.hide();
        };
        $scope.cancel = function () {
            $mdDialog.cancel();
        };
        $scope.pippo = function () {
            createNewSchedulation(DEVICES[0].id, $("#schedulazione-add-button"), $mdDialog);
        };
        $scope.pluto = function () {
            $mdDialog.cancel();
            $mdDialog.show({
                controller: DialogController,
                templateUrl: 'schedulazioniAdd.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                onComplete: addSchedulazione(DEVICES[0].id, DEVICES[0].nameDevice),
                onRemoving: function () {
                    $mdDialog.show({
                        controller: DialogController,
                        templateUrl: 'schedulazioni.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: true,
                        onComplete: getDeviceSchedulazione(DEVICES[0].id, DEVICES[0].nameDevice)
                    });
                }
            });
        };
    }

    setupAngular1();

    $(window).load(function () {
        getDeviceList();
    });

    function getDeviceList() {
        $.ajax({
            type: "POST",
            url: "<?=$api_path?>/GetUserDeviceList.php",
            data: {platform: PLATFORM},
            dataType: 'text',

            success: function (response) {

                var body = $("#home-device-list");
                var spinner = $("#main-progress");

                //alert(response);
                var json;
                try {
                    json = JSON.parse(response);
                }
                catch (e) {
                    spinner.remove();
                    var d = document.createElement("div");
                    d.setAttribute("class", "row col-xs-12 text-center");
                    d.setAttribute("style", "margin-top: 50px;");
                    d.appendChild(document.createTextNode("Non hai dispositivi associati"));
                    body.append(d);
                    return;
                }

                if (json.resultCode != 'OK') {
                    if (json.errorDescription == "KO_LOGIN") window.location.replace("../index.php");
                    else {
                        spinner.remove();
                        var d = document.createElement("div");
                        d.setAttribute("class", "row col-xs-12 text-center");
                        d.setAttribute("style", "margin-top: 50px;");
                        d.appendChild(document.createTextNode("Errore: " + json.message));
                        body.append(d);
                    }
                    return;
                }

                spinner.remove();

                if (json.resultObj.length <= 0) {
                    var d = document.createElement("div");
                    d.setAttribute("class", "row col-xs-12 text-center");
                    d.setAttribute("style", "margin-top: 50px;");
                    d.appendChild(document.createTextNode("Non hai dispositivi associati"));
                    body.append(d);
                    return;
                }

                DEVICES = json.resultObj;

                for (var i = 0; i < DEVICES.length; i++) {
                    var row = DEVICES[i];

                    var d1 = document.createElement("div");
                    d1.setAttribute("class", "row");
                    var d2 = document.createElement("div");
                    d2.setAttribute("class", "col-xs-12 col-sm-6 col-sm-offset-6 col-md-3 col-md-offset-9 text-center");
                    var p1 = document.createElement("p");
                    p1.setAttribute("style", "margin-right: 30px;margin-left: 30px;border-bottom: 1px solid #dddddd;padding: 10px;");
                    p1.setAttribute("id", "getConnection" + i);
                    $(p1).html("<span class='connection check-connected'>&nbsp;</span> In collegamento...");
                    d2.appendChild(p1);
                    d1.appendChild(d2);
                    body.append(d1);

                    var d11 = document.createElement("div");
                    d11.setAttribute("class", "row text-center");

                    var d22 = document.createElement("div");
                    d22.setAttribute("class", "col-xs-12 col-sm-6 col-md-3");
                    var d222 = document.createElement("div");
                    d222.setAttribute("class", "col-xs-12 col-sm-6 col-md-3");
                    var d2222 = document.createElement("div");
                    d2222.setAttribute("class", "col-xs-12 col-sm-6 col-md-3");
                    var d22222 = document.createElement("div");
                    d22222.setAttribute("class", "col-xs-12 col-sm-6 col-md-3");

                    var p2 = document.createElement("p");
                    p2.setAttribute("class", "device-data");
                    p2.setAttribute("style", "background-color: beige;");
                    p2.appendChild(document.createTextNode("ID: " + row.id));
                    var p22 = document.createElement("p");
                    p22.setAttribute("class", "device-data");
                    p22.setAttribute("style", "background-color: aliceblue;");
                    p22.appendChild(document.createTextNode("NOME: " + row.nameDevice));
                    var p222 = document.createElement("p");
                    p222.setAttribute("class", "device-data");
                    p222.setAttribute("style", "background-color: lavender;");
                    p222.appendChild(document.createTextNode("" + row.description));
                    var p2222 = document.createElement("p");
                    p2222.setAttribute("class", "device-data");
                    p2222.setAttribute("style", "background-color: whitesmoke;");
                    p2222.appendChild(document.createTextNode("ID UNICO: " + row.uniqueId));

                    d22.appendChild(p2);
                    d222.appendChild(p22);
                    d2222.appendChild(p222);
                    d22222.appendChild(p2222);

                    d11.appendChild(d22);
                    d11.appendChild(d222);
                    d11.appendChild(d2222);
                    d11.appendChild(d22222);

                    body.append(d11);

                    var d111 = document.createElement("div");
                    d111.setAttribute("class", "row col-xs-12");
                    d111.setAttribute("style", "height: 50px;");
                    d111.innerHTML = '&nbsp;';
                    body.append(d111);

                    var d1111 = document.createElement("div");
                    d1111.setAttribute("class", "row");
                    d1111.setAttribute("id", "pippo");
                    d1111.setAttribute("ng-controller", "DIALOG");
                    
                    var d3 = document.createElement("div");
                    d3.setAttribute("class", "col-xs-6 col-sm-4 col-md-3 text-center");
                    $(d3).html("<md-button class='md-raised md-primary' ng-click='showStatusDialog($event," + i + ")'>STATUS</md-button>");
                    var d33 = document.createElement("div");
                    d33.setAttribute("class", "col-xs-6 col-sm-4 col-md-3 text-center");
                    $(d33).html("<md-button class='md-raised md-warn' ng-click='showTelecomandoDialog($event," + i + ")'>TELECOMANDO</md-button>");
                    var d333 = document.createElement("div");
                    d333.setAttribute("class", "col-xs-6 col-sm-4 col-md-3 text-center");
                    $(d333).html("<md-button class='md-raised md-warn' ng-click='showWebcamDialog($event," + i + ")'>WEBCAM</md-button>");
                    var d3333 = document.createElement("div");
                    d3333.setAttribute("class", "col-xs-6 col-sm-4 col-md-3 text-center");
                    $(d3333).html("<md-button class='md-raised md-primary' ng-click='showSchedulazioniDialog($event," + i + ")'>SCHEDULAZIONE </md-button>");
                    var d33333 = document.createElement("div");
                    d33333.setAttribute("class", "col-xs-6 col-sm-4 col-md-3 text-center");
                    $(d33333).html("<md-button class='md-raised md-warn' onclick='alert(\"Sezione in fase di sviluppo.\")'>IMPOSTAZIONI</md-button>");
                    //var d333333 = document.createElement("div");
                    //d333333.setAttribute("class", "col-xs-6 col-sm-4 col-md-2 text-center");
                    //$(d333333).html("<md-button class='md-raised md-warn'>AMMINISTRA</md-button>");

                    d1111.appendChild(d3);
                    d1111.appendChild(d33);
                    d1111.appendChild(d333);
                    d1111.appendChild(d3333);
                    //d1111.appendChild(d33333);
                    //d1111.appendChild(d333333);
                    body.append(d1111);

                    getConnection(i);
                }

                setupAngular2();
            }
        });
    }

    function getConnection(pos) {

        $.ajax({
            type: "POST",
            url: "<?=$api_path?>/GetConnection.php",
            data: {platform: PLATFORM, deviceId: DEVICES[pos].id},
            dataType: 'text',

            success: function (response) {
                //alert(response);
                var json;
                try {
                    json = JSON.parse(response);
                }
                catch (e) {
                    return;
                }
                if (json.resultCode != 'OK') {
                    if (json.errorDescription == "KO_LOGIN") {
                        window.location.replace("../index.php");
                        return;
                    }
                    return;
                }

                var obj = json.resultObj;

                if (obj.established) {
                    $("#getConnection" + pos).html('<span class="connection connected">&nbsp;</span> In attesa di comandi...');
                    return;
                }

                $("#getConnection" + pos).html('<span class="connection not-connected">&nbsp;</span> Nessuna connessione...');
            }
        });

    }

    function getDeviceStatus(id) {
        $(".status-content").css("padding-top", "50px");//.css("text-align", "center");
        $("#status-progress").show();
        $("#status-work").show();
        $("#status-error").hide();
        $("#status-errorM").hide();
        $("#status-tabs").hide();
        $("#status-update").hide().html("Mi aggiorno fra 40 s");
        setTimeout(function () {

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/GetStatus.php",
                data: {platform: PLATFORM, deviceId: id},
                dataType: 'text',

                success: function (response) {
                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $("#status-work").hide();
                        $("#status-error").show();
                        return;
                    }
                    if (json.resultCode != 'OK') {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }
                        $("#status-work").hide();
                        $("#status-errorM").html("<b>Errore</b>: " + json.message).show();
                        return;
                    }

                    var obj = json.resultObj;
                    $("#status-device-name").html(obj.deviceName);

                    $("#status-device-status").html(obj.status);
                    $("#status-device-desc").html(obj.description);
                    $("#status-device-id").html(obj.deviceId);
                    $("#status-device-idunico").html(obj.uniqueId);
                    $("#status-device-nome").html(obj.deviceName);
                    $("#status-device-luogo").html(obj.geolocation);
                    $("#status-device-ippr").html(obj.privateIp);
                    $("#status-device-ippu").html(obj.publicIp);
                    $("#status-device-tempin").html(obj.internalTemp);
                    $("#status-device-tempes").html(obj.externalTemp);
                    $("#status-device-umin").html(obj.internalHum);
                    $("#status-device-umes").html(obj.externalHum);

                    var count = 40;

                    counter = setInterval(timer, 1000);

                    function timer() {

                        count = count - 1;
                        if (count <= 0) {

                            clearInterval(counter);
                            getDeviceStatus(id);
                        }
                        else $("#status-update").html("Mi aggiorno fra " + count + " s");
                    }

                    $(".status-content").css("padding-top", "");//.css("text-align", "");
                    $("#status-progress").hide();
                    $("#status-work").hide();
                    $("#status-error").hide();
                    $("#status-errorM").hide();
                    $("#status-tabs").show();
                    $("#status-update").show();

                }
            });
        }, 500);
    }

    function getDeviceTelecomando(id, name) {
        setTimeout(function () {

            $("#telecomando-device-name").text(name);

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/GetControllerDevice.php",
                data: {platform: PLATFORM, deviceId: id},
                dataType: "text",

                success: function (response) {

                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $("#telecomando-work").hide();
                        $("#telecomando-error").show();
                        return;
                    }

                    if (json.resultCode != 'OK') {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }
                        $("#telecomando-work").hide();
                        $("#telecomando-errorM").html("<b>Errore</b>: " + json.message).show();
                        return;
                    }

                    var row = document.createElement("div");
                    row.setAttribute("class", "row");

                    for (var i = 0; i < json.resultObj.length; i++) {
                        var btn = json.resultObj[i];

                        var di = document.createElement("div");
                        di.setAttribute("class", "col-xs-12 col-sm-4 col-md-3");

                        var div = document.createElement("div");
                        div.setAttribute("style", "cursor:pointer;");
                        div.setAttribute("onClick", "telecomandoSendCommand('" + id + "', '" + btn.idCommand + "', '" + btn.description + "', '" + btn.privateIp + "')");
                        var img = document.createElement("img");
                        img.setAttribute("style", "max-width: 40%;");
                        img.setAttribute("src", "/console" + btn.iconUrl);
                        var p = document.createElement("p");
                        p.appendChild(document.createTextNode(btn.description));
                        p.setAttribute("style", "padding:5px;font-size:21px;");

                        div.appendChild(img);
                        div.appendChild(p);
                        di.appendChild(div);
                        row.appendChild(di);
                    }

                    document.getElementById("telecomando").appendChild(row);
                    $(".telecomando-content").css("padding", "10px");//.css("text-align", "");
                    $("#telecomando-progress").hide();
                    $("#telecomando-work").hide();
                    $("#telecomando-error").hide();
                    $("#telecomando-errorM").hide();
                    $("#telecomando").show();
                }
            });

        }, 500);
    }

    function telecomandoSendCommand(deviceId, commandId, desc, ip) {

        $("#telecomando").hide();
        $("#telecomando-progress").show();
        $("#telecomando-work").html("Eseguo il comando: " + desc).show();
        $("#telecomando-work").show();

        /*$.ajax({
         type: "POST",
         url: "https://2.236.99.236/lib/api/SendCommand.php",
         data: {platform: PLATFORM, commandId: commandId, deviceId: deviceId},
         dataType: "text",
         //timeout: 5000,

         success: function (response) {

         },
         error: function (xhr, status, err) {
         //status === 'timeout' if it took too long.
         //handle that however you want.
         console.log(status, err);
         }
         });
         return;*/
        $.ajax({
            type: "POST",
            url: "<?=$api_path?>/SendCommand.php",
            data: {platform: PLATFORM, commandId: commandId, deviceId: deviceId},
            dataType: "text",

            success: function (response) {

                //alert(response);
                var json;
                try {
                    json = JSON.parse(response);
                }
                catch (e) {
                    $("#telecomando-work").hide();
                    $("#telecomando-error").show();
                    return;
                }

                if (json.resultCode != "OK") {
                    if (json.errorDescription == "KO_LOGIN") {
                        window.location.replace("../index.php");
                        return;
                    }

                    $("#telecomando-work").hide();
                    $("#telecomando-errorM").html("<b>Errore</b>: " + json.message).show();
                    return;
                }

                $("#telecomando-progress").hide();
                $("#telecomando-work").html("Comando eseguito: " + desc).show();
                setTimeout(function () {
                    $("#telecomando-work").hide();
                }, 3000);
                $("#telecomando-error").hide();
                $("#telecomando-errorM").hide();
                $("#telecomando").show();
            }
        });
    }

    function getDeviceWebcam(id, name) {
        setTimeout(function () {

            $("#webcam-device-name").text(name);

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/GetWebcam.php",
                data: {platform: PLATFORM, deviceId: id},
                dataType: 'text',

                success: function (response) {
                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $("#webcam-work").hide();
                        $("#webcam-error").show();
                        return;
                    }
                    if (json.resultCode != 'OK') {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }
                        $("#webcam-work").hide();
                        $("#webcam-errorM").html("<b>Errore</b>: " + json.message).show();
                        return;
                    }

                    var obj = json.resultObj;
                    $("#webcam-device-name").html(obj.deviceName);

                    var foto = 0;
                    var video = 0;

                    for (var i = 0; i < json.resultObj.length; i++) {
                        var row = json.resultObj[i];
                       
                        var tab = row.mediaType == "WEBCAM_PHOTO" ? "webcam-tab-photo" : "webcam-tab-video";
                        row.mediaType == "WEBCAM_PHOTO" ? foto++ : video++;
                        //var tab = "WEBCAM_PHOTO";
                        
                        var li = document.createElement("li");
                        li.setAttribute("class", "list-group-item");
                        var a = document.createElement("a");
                        a.setAttribute("href", row.mediaUrl + "");
                        a.setAttribute("target", "_blank");
                        var img = document.createElement("img");
                        img.setAttribute("style", "max-width:90%");
                        img.setAttribute("src", row.mediaThumbnail + "");
                        a.appendChild(img);
                        li.appendChild(a);
                        var p = document.createElement("p");
                        p.appendChild(document.createTextNode(row.mediaLabel));
                        p.appendChild(document.createElement("br"));
                        p.appendChild(document.createTextNode("(" + row.mediaTimeStamp + ")"));

                        li.appendChild(p);
                        
                        try {
                            document.getElementById(tab).appendChild(li);
                        }
                        catch (e) {
                            continue;
                        }
                    }
                    
                    if (foto == 0) {
                        var li = document.createElement("li");
                        li.setAttribute("class", "list-group-item");
                        var h4 = document.createElement("h4");
                        li.setAttribute("class", "list-group-item-heading");
                        h4.appendChild(document.createTextNode("Non ci sono foto"));
                        li.appendChild(h4);

                        document.getElementById("webcam-tab-photo").appendChild(li);
                    }
               
                    if (video == 0) {
                        
                        var li = document.createElement("li");
                        li.setAttribute("class", "list-group-item");
                        var h4 = document.createElement("h4");
                        li.setAttribute("class", "list-group-item-heading");
                        h4.appendChild(document.createTextNode("Non ci sono video"));
                        li.appendChild(h4);

                        document.getElementById("webcam-tab-video").appendChild(li);
                    }

                    $(".webcam-content").css("padding-top", "");//.css("text-align", "");
                    $("#webcam-progress").hide();
                    $("#webcam-work").hide();
                    $("#webcam-error").hide();
                    $("#webcam-errorM").hide();
                    $("#webcam-tabs").show();

                }
            });
        }, 500);
    }

    function getDeviceSchedulazione(id, name) {

        setTimeout(function () {

            $("#schedulazioni-device-name").text(name);

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/GetScheduling.php",
                data: {platform: PLATFORM, deviceId: id},
                dataType: 'text',

                success: function (response) {
                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $("#schedulazioni-work").hide();
                        $("#schedulazioni-error").show();
                        return;
                    }
                    if (json.resultCode != 'OK') {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }
                        $("#schedulazioni-work").hide();
                        $("#schedulazioni-errorM").html("<b>Errore</b>: " + json.message).show();
                        return;
                    }

                    var obj = json.resultObj;
                    $("#schedulazioni-device-name").html(obj.deviceName);

                    if (json.resultObj.length <= 0) {
                        var tr = document.createElement("tr");
                        var td = document.createElement("td");
                        td.setAttribute("colspan", "6");
                        td.setAttribute("align", "center");
                        td.setAttribute("style", "padding-top: 20px;font-size:large;");
                        var p = document.createElement("p");
                        p.appendChild(document.createTextNode("Non ci sono schedulazioni attive, creane una."));
                        td.appendChild(p);
                        tr.appendChild(td);
                        document.getElementById("schedulazioni-tab").appendChild(tr);
                        $("#schedulazioni-tab-h").hide();
                    }
                    else {
                        for (var i = 0; i < json.resultObj.length; i++) {
                            var row = json.resultObj[i];

                            var orario;
                            var periodo;

                            if (row.startTimeStamp == row.endTimeStamp) orario = row.startTimeStamp;
                            else orario = row.startTimeStamp + " - " + row.endTimeStamp;

                            var startS = row.validityRangeStart.split(" ")[0].split("-");
                            var startN = startS[2] + "." + startS[1] + "." + startS[0];

                            var endS = row.validityRangeEnd.split(" ")[0].split("-");
                            var endN = endS[2] + "." + endS[1] + "." + endS[0];

                            if (startN == endN) periodo = startN;
                            else periodo = startN + " - " + endN;

                            var tr = document.createElement("tr");

                            var td1 = document.createElement("td");
                            var td2 = document.createElement("td");
                            var td3 = document.createElement("td");
                            var td4 = document.createElement("td");
                            var td5 = document.createElement("td");
                            var td6 = document.createElement("td");

                            td1.appendChild(document.createTextNode(row.description));

                            td6.appendChild(document.createTextNode(row.commandDescription));

                            var TYPE = row.schedulingTypeDescription;
                            if (row.schedulingType == "WEEKLY") TYPE += " (" + row.schedulingValue.replace(/:/gi, ",") + ")";
                            td2.appendChild(document.createTextNode(TYPE));

                            td3.appendChild(document.createTextNode(orario));

                            td4.appendChild(document.createTextNode(periodo));

                            var cancel = document.createElement("button");
                            cancel.setAttribute("type", "button");
                            cancel.setAttribute("class", "btn btn-danger");
                            cancel.setAttribute("onclick", "confirmCancelScheduling(" + row.schedulingId + ", '" + id + "', this);");
                            cancel.appendChild(document.createTextNode("Rimuovi"));
                            td5.appendChild(cancel);

                            tr.appendChild(td1);
                            tr.appendChild(td6);
                            tr.appendChild(td2);
                            tr.appendChild(td3);
                            tr.appendChild(td4);
                            tr.appendChild(td5);

                            document.getElementById("schedulazioni-tab").appendChild(tr);
                        }
                    }

                    $(".schedulazioni-content").css("padding", "10px");//.css("text-align", "");
                    $("#schedulazioni-progress").hide();
                    $("#schedulazioni-work").hide();
                    $("#schedulazioni-error").hide();
                    $("#schedulazioni-errorM").hide();
                    $("#schedulazioni-add").show();
                    $("#schedulazioni-tab").show();

                }
            });
        }, 500);

    }

    function confirmCancelScheduling(id, dev_id, button) {
        if (confirm("Vuoi cancellare la schedulazione?")) {

            $(button).attr("disabled", "disabled");
            $(button).addClass("disabled");
            $(button).text("Elimino...");

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/DeleteScheduling.php",
                data: {platform: PLATFORM, deviceId: dev_id, schedulingId: id},
                dataType: "text",

                success: function (response) {

                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $(button).removeAttr("disabled");
                        $(button).removeClass("disabled");
                        $(button).text("Errore!");
                        return;
                    }

                    if (json.resultCode != "OK") {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }

                        $(button).removeAttr("disabled");
                        $(button).removeAttr("onClick");
                        $(button).removeClass("disabled");
                        $(button).text("Errore!");
                        return;
                    }

                    $(button).removeAttr("disabled");
                    $(button).removeAttr("onClick");
                    $(button).removeClass("disabled");
                    $(button).removeClass("btn-danger");
                    $(button).addClass("btn-success");
                    $(button).text("Rimossa");
                }
            });
        }
    }

    function addSchedulazione(id, name) {

        setTimeout(function () {
            resetForm();

            $("#schedulazioni-add-device-name").text(name);

            $.ajax({
                type: "POST",
                url: "<?=$api_path?>/GetSchedulingType.php",
                data: {platform: PLATFORM, deviceId: id},
                dataType: "text",

                success: function (response) {

                    //alert(response);
                    var json;
                    try {
                        json = JSON.parse(response);
                    }
                    catch (e) {
                        $("#schedulazioni-add-work").hide();
                        $("#schedulazioni-add-error").show();
                        return;
                    }

                    if (json.resultCode != "OK") {
                        if (json.errorDescription == "KO_LOGIN") {
                            window.location.replace("../index.php");
                            return;
                        }
                        $("#schedulazioni-add-work").hide();
                        $("#schedulazioni-add-errorM").html("<b>Errore</b>: " + json.message).show();
                        return;
                    }

                    for (var i = 0; i < json.resultObj.length; i++) {
                        var row = json.resultObj[i];

                        var option = document.createElement("option");
                        option.appendChild(document.createTextNode(row.description));
                        option.setAttribute("type", row.schedulingType);
                        //option.setAttribute("class", "removeThis");
                        document.getElementById("ricorrenze-list").appendChild(option);
                    }

                    ricorrenzaChangeAction(json.resultObj[0].schedulingType);

                    $.ajax({
                        type: "POST",
                        url: "<?=$api_path?>/GetControllerDevice.php",
                        data: {platform: PLATFORM, deviceId: id},
                        dataType: "text",

                        success: function (response2) {

                            //alert(response2);
                            var json2;
                            try {
                                json2 = JSON.parse(response2);
                            }
                            catch (e) {
                                $("#schedulazioni-add-work").hide();
                                $("#schedulazioni-add-error").show();
                                return;
                            }

                            if (json2.resultCode != 'OK') {
                                if (json.errorDescription == "KO_LOGIN") {
                                    window.location.replace("../index.php");
                                    return;
                                }
                                $("#schedulazioni-add-work").hide();
                                $("#schedulazioni-add-errorM").html("<b>Errore</b>: " + json.message).show();
                                return;
                            }

                            for (var i = 0; i < json2.resultObj.length; i++) {
                                var row = json2.resultObj[i];

                                var option = document.createElement("option");
                                option.appendChild(document.createTextNode(row.description));
                                option.setAttribute("type", row.commandType);
                                option.setAttribute("id_command", row.idCommand);
                                //option.setAttribute("class", "removeThis");
                                document.getElementById("command-list").appendChild(option);
                            }

                            commandChangeAction(json2.resultObj[0].commandType);

                            var days = ["Lunedi", "Martedi", "Mercoledi", "Giovedi", "Venerdi", "Sabato", "Domenica"];
                            var attr = ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"];

                            //if (!document.getElementById("days-list").hasChildNodes()) {
                            for (var i = 0; i < days.length; i++) {
                                var d = document.createElement("d");
                                d.setAttribute("class", "col-xs-12 col-sm-6 col-md-4");
                                var check = document.createElement("input");
                                check.setAttribute("type", "checkbox");
                                check.setAttribute("attr", attr[i]);
                                check.setAttribute("class", "giorno_check");
                                check.setAttribute("style", "margin: 10px;cursor: pointer;");
                                d.appendChild(check);
                                d.appendChild(document.createTextNode(days[i]));
                                document.getElementById("days-list").appendChild(d);
                            }
                            //}

                            $(".schedulazioni-add-content").css("padding", "10px");
                            $(".schedulazioni-add-content").css("text-align", "left");
                            $("#schedulazioni-add-progress").hide();
                            $("#schedulazioni-add-work").hide();
                            $("#schedulazioni-add-error").hide();
                            $("#schedulazioni-add-errorM").hide();
                            $("#schedulazione-add").show();
                            $("#schedulazione-add-button").show();

                        }
                    });// End getController
                }
            });// End getRicorrenza
        }, 500);
    }

    function ricorrenzaChange(select) {
        var type = select.options[select.selectedIndex].getAttribute("type");
        resetForm();
        ricorrenzaChangeAction(type);
        commandChangeAction($('#command-list').find(":selected").attr("type"));
    }

    function ricorrenzaChangeAction(type) {
        if (type == "ONE_SHOT") {
            $("#giorno").show();
            $("#giorni").hide();
            $("#periodo").hide();
        }
        else {
            $("#giorno").hide();
            $("#giorni").show();
            $("#periodo").show();

            if (type == "ALWAYS") {
                document.getElementById('date-end').setAttribute("disabled", "");
                $('#date-end').val('31/12/2999');
            }
        }
    }

    function commandChange(select) {
        var type = select.options[select.selectedIndex].getAttribute("type");
        resetForm();
        commandChangeAction(type);
    }

    function commandChangeAction(type) {
        if (type == "SWITCH_OFF") {
            $("#shoutdown-end").hide();
        }
        else if (type == "SWITCH_ON") {
            $("#shoutdown-end").show();
            document.getElementById('time-end').removeAttribute("disabled");
        }
        else if (type == "SINGLE_COMMAND") {
            $("#shoutdown-end").show();
        }

        if (type == "SWITCH_OFF" || type == "SINGLE_COMMAND" || (type == "SWITCH_ON" && !$("#check-off").is(':checked'))) {
            document.getElementById('time-end').setAttribute("disabled", "");
            $('#time-start').on('changeTime', function () {
                $('#time-end').timepicker('setTime', $(this).val());
            });
        }
    }

    function resetForm() {
        $('#time-end-first').hide();
        $('#time-start').val('');
        $('#time-end').val('');
        $('#time-start').timepicker({'timeFormat': 'H:i', 'step': 15, 'scrollDefault': 'now'});
        $('#time-start').off('changeTime');
        $('#time-end').timepicker({'timeFormat': 'H:i', 'step': 15, 'scrollDefault': 'now'});

        $('#date-start').val('');
        $('#date-end').val('');
        $('#date-single').val('');
        $('#date-start').datepicker({
            startView: 1,
            language: "it",
            autoclose: true
        });
        $('#date-end').datepicker({
            startView: 1,
            language: "it",
            autoclose: true
        });
        $('#date-single').datepicker({
            startView: 1,
            language: "it",
            autoclose: true
        });

        document.getElementById('date-end').removeAttribute("disabled");
        ////////////////////END DATE /////////////////
        $('#description').val('');
        $(".giorno_check").prop('checked', false);
        $("#check-off").prop('checked', false);
        $("#check-off").on('change', function () {
            if ($('#command-list').find(":selected").attr("type") == "SWITCH_ON" && !$(this).is(':checked')) {
                document.getElementById('time-end').setAttribute("disabled", "");
                $('#time-start').on('changeTime', function () {
                    $('#time-end').timepicker('setTime', $(this).val());
                });
                $('#time-end').timepicker('setTime', $('#time-start').val());
            }
            else {
                document.getElementById('time-end').removeAttribute("disabled");
                $('#time-start').off('changeTime');
                $('#time-end').val('');
                $('#time-end').on('changeTime', function () {
                    if ($('#time-end').timepicker('getTime', new Date()) < $('#time-start').timepicker('getTime', new Date())) $('#time-end-first').show();
                    else $('#time-end-first').hide();
                });
            }
        });
    }

    function createNewSchedulation(id, button, p) {

        var scheduling_type = $('#ricorrenze-list').find(":selected").attr("type");

        var description = $('#description').val();
        if (description.length == 0) {
            alert("Devi inserire una breve descrizione per la programmazione");
            return;
        }

        var command_id = $('#command-list').find(":selected").attr("id_command");
        //var command_type = $('#command-list').find(":selected").attr("type");

        var scheduling_value = "";
        if (scheduling_type == "ONE_SHOT") scheduling_value = "SINGLE";
        else {
            var days = document.getElementsByClassName("giorno_check");
            for (var i = 0; i < days.length; i++) {
                if ($(days[i]).is(':checked')) {
                    var attr = days[i].getAttribute("attr");
                    scheduling_value += scheduling_value.length == 0 ? attr : ":" + attr;
                }
            }
        }

        if (scheduling_type != "ONE_SHOT" && scheduling_value.length == 0) {
            alert("Devi selezionare almeno 1 giorno");
            return;
        }

        var shutdown = $("#check-off").is(':checked');

        var time_start = $('#time-start').val();
        if (time_start.length == 0) {
            alert("Devi inserire un orario di inizio programmazione");
            return;
        }

        var time_end = $('#time-end').val();
        if (time_end.length == 0) {
            alert("Devi inserire un orario di fine programmazione");
            return;
        }

        if (shutdown && time_start == time_end) {
            alert("Non puoi inserire lo stesso orario per l'inizio e la fine della programmazione");
            return;
        }

        var date_start = "";
        var date_end = "";

        if (scheduling_type == "ONE_SHOT") {

            if ($('#date-single').val().length == 0) {
                alert("Devi inserire un giorno per la programmazione");
                return;
            }

            var d = $('#date-single').datepicker('getDate');

            var m = (d.getMonth() + 1) + "";
            if (m.length == 1) m = "0" + m;

            var day = d.getDate() + "";
            if (day.length == 1) day = "0" + day;

            date_start = d.getFullYear() + "-" + m + "-" + day + " 00:00:00";
            date_end = date_start;
        }
        else if (scheduling_type == "WEEKLY") {

            if ($('#date-start').val().length == 0) {
                alert("Devi inserire il giorno di inizio del periodo di programmazione");
                return;
            }

            if ($('#date-end').val().length == 0) {
                alert("Devi inserire il giorno di fine del periodo di programmazione");
                return;
            }

            var dS = $('#date-start').datepicker('getDate');
            var dE = $('#date-end').datepicker('getDate');

            if (dS.getTime() >= dE.getTime()) {
                alert("Il giorno di fine del periodo di programmazione non pu� essere uguale o precedente rispetto al giorno di inizio");
                return;
            }

            var mS = (dS.getMonth() + 1) + "";
            if (mS.length == 1) mS = "0" + mS;

            var dayS = dS.getDate() + "";
            if (dayS.length == 1) dayS = "0" + dayS;

            date_start = dS.getFullYear() + "-" + mS + "-" + dayS + " 00:00:00";

            /////////////////////////////////////////////////////////////////////////
            var mE = (dE.getMonth() + 1) + "";
            if (mE.length == 1) mE = "0" + mE;

            var dayE = dE.getDate() + "";
            if (dayE.length == 1) dayE = "0" + dayE;

            date_end = dE.getFullYear() + "-" + mE + "-" + dayE + " 00:00:00";
        }
        else {
            if ($('#date-start').val().length == 0) {
                alert("Devi inserire il giorno di inizio del periodo di programmazione");
                return;
            }

            var dS = $('#date-start').datepicker('getDate');

            if (dS.getTime() >= 32503590000000) {
                alert("Il giorno di fine del periodo di programmazione non pu� essere uguale o precedente rispetto al giorno di inizio");
                return;
            }

            var mS = (dS.getMonth() + 1) + "";
            if (mS.length == 1) mS = "0" + mS;

            var dayS = dS.getDate() + "";
            if (dayS.length == 1) dayS = "0" + dayS;

            date_start = dS.getFullYear() + "-" + mS + "-" + dayS + " 00:00:00";

            date_end = "2999-12-31 00:00:00";
        }

        $(button).attr("disabled", "disabled");
        $(button).addClass("disabled");
        $(button).text("Creo...");

        $.ajax({
            type: "POST",
            url: "<?=$api_path?>/SetScheduling.php",
            data: {
                platform: PLATFORM,
                deviceId: id,
                commandId: command_id,
                schedulingType: scheduling_type,
                schedulingValue: scheduling_value,
                startTime: time_start,
                endTime: time_end,
                priority: 1,
                validityStart: date_start,
                validityEnd: date_end,
                description: description,
                shutdown: shutdown
            },
            dataType: "text",
            success: function (response) {

                //alert(response);
                var json;
                try {
                    json = JSON.parse(response);
                }
                catch (e) {
                    alert("Errore");
                    //$("#schedulazione-add-loading").hide();
                    //$("#schedulazione-add-table").show();

                    $(button).removeAttr("disabled");
                    $(button).removeClass("disabled");
                    $(button).text("Crea");
                    return;
                }

                if (json.resultCode != "OK") {
                    if (json.errorDescription == "KO_LOGIN") window.location.replace("../index.php");
                    alert("Errore: " + json.message);

                    $(button).removeAttr("disabled");
                    $(button).removeClass("disabled");
                    $(button).text("Crea");

                    //$("#schedulazione-add-loading").hide();
                    //$("#schedulazione-add-table").show();
                    return;
                }

                //$("#schedulazione-add").hide();

                p.cancel();
            }
        })
        ;
    }

    document.getElementById('logout').addEventListener('click', function () {

        document.getElementById('logoutB').setAttribute("disabled", "disabled");

        $.ajax({
            type: "POST",
            url: "<?=$api_path?>/LogoutAPI.php",
            data: {platform: PLATFORM},
            dataType: 'text',

            success: function (response) {

                //alert(response);
                /*var json;
                 try {
                 json = JSON.parse(response);
                 }
                 catch (e) {
                 alert("Errore");
                 return;
                 }

                 if (json.resultCode != 'OK') {
                 alert("Errore: " + json.message);
                 return;
                 }*/

                window.location.reload();
            }
        });
    });
</script>
</body>
</html>