<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/16/13
 * Time: 8:53 AM
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(300);
include "init.php";
define('SOURCE_DIR', ROOT . '/source');
define('TMP_DIR', ROOT . '/tmp/updates');
define('MODULE_DIR', ROOT . '/module');
chdir(ROOT);
include ROOT . '/init_autoloader.php';
include ROOT . '/module/System/src/System/IO/Directory.php';
include ROOT . '/library/Updater.php';
include ROOT . '/module/Application/src/Application/API/App.php';

$updater = new Updater();
$updater->processRequest();
if (isset($_GET['ajax'])) {
    $updater->output();
} else {
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <title>IPT System AutoUpdater</title>
        <style>
            *{ line-height:20px; font-family:'Courier New', Monospace; font-size:9pt; }
            body{ background:#f8f8ff; max-width:700px; margin:auto; }
            img{ vertical-align:middle; float:right; }
        </style>

        <script type="text/javascript">
            <?=file_get_contents(MODULE_DIR.'/JQuery/public/js/jquery-1.10.2.min.js')?>
        </script>
        <script type="text/javascript">
            var loadingImg = new Image();
            loadingImg.src = "/images/ajax_loader_tiny.gif";

            var okImg = new Image();
            okImg.src = "/images/done.png";

            var nokImg = new Image();
            nokImg.src = "/images/dialog-error.png";

            var url = '/update.php?ajax&op=';
            var step = 'downloadUpdateInfo';
            $(document).ready(function () {
                $('body').on('click', 'a.continue', function (e) {
                    e.preventDefault();
                    step = $(this).data('step');
                    var msg = $(this).data('msg');
                    var loading = "<img class='loading' src='/images/ajax_loader_tiny.gif'>";
                    $(this).replaceWith(msg + loading);
                    update(step);
                });
                update(step);
            });
            function update(step) {
                $.ajax({
                    url: url + step,
                    success: function (data) {
                        if (data[0] != '{') {
                            $('#errors').prepend(data);
                            $('img.loading').removeClass('loading').attr('src', nokImg.src);
                        }
                        else {
                            data = JSON.parse(data);

                            if (data.status == 'OK') {
                                $('img.loading').removeClass('loading').attr('src', okImg.src);
                                setTimeout(function () {
                                    if (data.hasOwnProperty('step'))
                                        update(data.step);
                                }, 1000);
                            }

                            if (data.error)
                                $('#errors').prepend(data.error);

                            $('img.loading').removeClass('loading').attr('src', nokImg.src);

                            if (data.messages)
                                $('#messages').append(data.messages);
                        }
                        window.scrollTo(0, $(window).height());
                    }
                });
            }
        </script>
    </head>
    <body>
    <div id="messages">
        <?= $updater->messages() ?>
    </div>
    <fieldset id="errors">
        <legend>Errors :</legend>
    </fieldset>
    </body>
    </html>
<? } ?>