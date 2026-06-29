<!DOCTYPE html>
<html>
    <title>Web - Encryption & Decryption</title>
    <head>
        <meta charset="utf-8">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

        <script type="text/javascript">
            function fnEncrypt(){
                var str = document.getElementById('encrypt').value;
                jQuery.ajax({
                    type: "POST",
                    url: 'js/function.php',
                    data: {functionname: 'encrypt', string:str },
                    success:function(data) {
                    document.getElementById('encryptData').innerHTML = data;
                    }
                });
            }
            function fnDecrypt(){
                var str = document.getElementById('decrypt').value;
                jQuery.ajax({
                    type: "POST",
                    url: 'js/function.php',
                    data: {functionname: 'decrypt', string:str },
                    success:function(data) {
                    // document.getElementById('decryptData').innerHTML = data;
                    let toDisplay = document.getElementsByClassName('decryptData');
                    toDisplay[0].innerHTML = data;
                    toDisplay[1].innerHTML = data;
                    }
                });
            }
        </script>
    </head>

    <body>
        <div style="width:1440px; margin:auto;">
        <h1>Web - Encryption & Decryption</h1>

        <h2>Encryption</h2>
        <textarea name="encrypt" id="encrypt" rows="10" style="width:100%"></textarea><br><br>
        <button onclick="fnEncrypt();">Encrypt</button><br><br>
        <div style="width:100%;word-break: break-word;" id="encryptData"></div>

        <h2>Decryption</h2>

        <textarea name="decrypt" id="decrypt" rows="10" style="width:100%"></textarea><br><br>
        <button onclick="fnDecrypt();">Decrypt</button><br><br>
        <!-- <div style="width:100%;word-break: break-word;" id="decryptData"></div> -->
        <div style="width:100%;word-break: break-word;" id="decryptData" class="decryptData"></div>
        <pre>
            <div style="width:100%;word-break: break-word;" class="decryptData"></div>
        </pre>
        </div>
    </body>
</html>
