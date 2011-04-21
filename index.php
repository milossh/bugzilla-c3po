<?php

include_once 'controller.inc.php';

?>

<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"><!--  -->
        <link rel="icon" href="https://l10n-stage-sj.mozilla.org/favicon.ico" type="image/x-icon">

        <title>L10n Bug helper</title>
        <script type="text/javascript" src="<?=$bugzilla_url?>config.cgi"></script>
        <script>
            var selectedProduct;
            function toggleLocales(chk) {
                if (chk.checked == 1)
                {document.getElementById('locales').style.display = 'none';}
                else {document.getElementById('locales').style.display = 'block';}
            }
            function updateProduct() {
                var blah = document.getElementById('product-select').selectedIndex;
                selectedProduct = document.getElementById('product-select').options[blah].text;
                updateComponent();
                updateVersion();
            }
                            
            function updateComponent() {
                var tempArray = component[selectedProduct];
                var compSelect = document.getElementById('component-select');

                for(var i=0;i<tempArray.length;i++){
                    var optionItem = document.createElement("option");
                    optionItem.text = tempArray[i];
                    optionItem.value = tempArray[i];
                    compSelect.appendChild(optionItem);
                }
            }
            function updateVersion() {
                var tempArray = version[selectedProduct];
                var verSelect = document.getElementById('version-select');

                for(var i=0;i<tempArray.length;i++){
                    var optionItem = document.createElement("option");
                    optionItem.text = tempArray[i];
                    optionItem.value = tempArray[i];
                    verSelect.appendChild(optionItem);
                }
            }
        </script>
        <link href="style/main.css" media="all" type="text/css" rel="stylesheet">

    </head>
    <body>
        <div id="menu">
            <div id="auth"><!--  -->
                <a onclick="$('#site_login').show();$(this).hide(); $('#id_username').focus(); return false;" href="">Log in</a>

                <form style="display: none;" onsubmit="return submit_site_login();" id="site_login" method="POST" action="/accounts/login"><input name="next" value="" type="hidden"><ul><li><label for="id_username">Username:</label> <input id="id_username" name="username" maxlength="75" type="text"></li>
                <li><label for="id_password">Password:</label> <input name="password" id="id_password" type="password"></li><li><input value="OK" type="submit"></li></ul></form>

            </div>
            <ul>
                <li>Mozilla Localization</li>
                <li><a href="https://l10n-stage-sj.mozilla.org/">Home</a></li>
                <li><a href="https://l10n-stage-sj.mozilla.org/teams/">Teams</a></li>
                <li><a href="https://wiki.mozilla.org/L10n">Documentation</a></li>
            </ul>
            <br>
        </div>
        
        <div id="main-content">
            <div class="main-input" style="width: 550px;">
                <h2>Bugzilla C3PO</h2>
                <form name="bugzilla" action="bugsfiled.php" method="post">
                    <div><span class="text">Username:</span><span><input type="text" name="username" /></span></div>
                    <div><span class="text">Password:</span><span><input type="password" name="pwd" /></span></div>
                        
                    <div>
                        <span class="text">Product:</span>
                        <span><select id="product-select" name="product" onchange="updateProduct()">
                            <option default>Select a product</option>
                            <script>
                                for(var product in component) {
                                    document.write('<option value=' + product + '>' + product + '</option>');
                                }
                            </script>
                        </select></span>
                    </div>
                    <div>
                        <span class="text">Component:</span>
                        <span><select id="component-select" name="component">
                            <option value="null">Select component</option>                                
                        </select></span>
                    </div>
                    <div>
                        <span class="text">Version:</span>
                        <span><select id="version-select" name="version">
                            <option value="null">Select version</option>                                
                        </select></span>
                    </div>

                    <div><span class="text">Assign to:</span><span><input type="text" name="assign_to" /></span></div>
                    <div><span class="text">Blocks:</span><span><input type="text" name="blocked" /></span></div>
                    <div><span class="text">Whiteboard:</span><span><input type="text" name="whiteboard" /></span></div>
                    <div><span class="text">URL:</span><span><input type="text" name="url" /></span></div>
                    <div>
                        <span class="text">List of locales:</span>
                        <span><input id="locales" type="text" name="locales" /></span>
                        <span><input style="text-align: right;" onclick="toggleLocales(this);" type="checkbox" name="all-locales" value="all" /> All locales</span>
                    </div>
                    <div><span class="text">Summary:</span><span><input type="text" name="summary" /></span></div>
                    <span class="text">Description:</span>
                    <div>
                        <textarea name="description" rows="10" cols="80" />Write in your bug description here...</textarea>
                    </div>
                    <div class="submit"><input type="submit" value="Submit" /></div>

                </form>
            </div>
        </div>
        <div id="page_footer">
            <a href="https://l10n-stage-sj.mozilla.org/privacy/">Privacy policy</a>
        </div>
    </body>

    
</html>