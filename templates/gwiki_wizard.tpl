<{if isset($err_message)}>
    <hr/>
    <div class="errorMsg"><{$err_message}></div>
    <hr/>
<{/if}>
<{if isset($message)}>
    <hr/>
    <div class="resultMsg"><{$message}></div>
    <hr/>
<{/if}>
<div id="gwikieditor" class="wikipage">
    <div id="wikiwizard">
        <div class="wikiwizard_title">
            <{if isset($gwizardform)}><{$gwizardform.title}><{else}><{$title}><{/if}>
        </div>
        <table>
            <tr>
                <td class="hidden-xs" style="vertical-align:top;">
                    <div class="wikiwizard_logo"><img src="assets/images/wizardlogo.png" alt="logo"/></div>
                </td>
                <td>
                    <div class="wikiwizard_content">
                        <{if isset($body)}>
                            <{$body}>
                        <{/if}>
                        <{if isset($gwizardform)}>
                            <form id="<{$gwizardform.name}>" name="<{$gwizardform.name}>"
                                  action="<{$gwizardform.action}>"
                                  method="<{$gwizardform.method}>" <{$gwizardform.extra}>>
                                <!-- start of form elements loop -->
                                <{foreach item=element from=$gwizardform.elements}>
                                    <{if $element.hidden != true}>
                                        <h3><{$element.caption}></h3>
                                        <{$element.body}>
                                    <{else}>
                                        <{$element.body}>
                                    <{/if}>
                                <{/foreach}>
                                <!-- end of form elements loop -->
                            </form>
                        <{/if}>
                    </div>
                </td>
            </tr>
        </table>
        <br clear="all">
    </div>
    <{include file="db:gwiki_page_info.tpl"}>
</div>
<{if empty($hideInfoBar)}>
<{/if}>
<script>
    function setRadioButton(id) {
        var radiobtn = document.getElementById(id);
        radiobtn.checked = true;
    }

    /*
     taken from
     filedrag.js - HTML5 File Drag & Drop demonstration
     Featured on SitePoint.com
     Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
     */

    (function () {

        function Output(msg) {
            var m = document.getElementById("wikieditimg_dd_msg");
            //m.innerHTML = msg;
            m.innerHTML = msg + "  " + m.innerHTML;
        }


        function ClearOutput() {
            var m = document.getElementById("wikieditimg_dd_msg");
            if (window.wikisaysdumbbrowser == true) {
                m.innerHTML = '';
            }
            else {
                m.innerHTML = "<{$smarty.const._MD_GWIKI_IMAGES_DROPHERE}>";
            }
//      m = document.getElementById("wikieditimg_progress");
//      m.innerHTML = '';
        }

        function serializeImageDetail(name) {
            var image = new Object;

            window.fileUpCount = window.fileUpCount + 1;
            var fc = "000" + window.fileUpCount;
            fc = fc.substr(fc.length - 2) + ' ';

            var imgname = name.substr(0, name.lastIndexOf('.'));
            imgname = imgname.replace(/[\.\\\/_-]/g, ' ');
            imgname = fc + imgname.replace(/^\s+|\s+$/g, "");
            image.page = document.getElementById("page").value;
            image.image_id = 0;
            image.image_name = imgname;
            image.image_alt_text = '';
            image.use_to_represent = false;
            var jsonimg = JSON.stringify(image);
            return jsonimg;
        }

        // file drag hover
        function FileDragHover(e) {
            e.stopPropagation();
            e.preventDefault();
            e.target.className = (e.type == "dragover" ? "hover" : "");
        }


        // file selection
        function FileSelectHandler(e) {

            // cancel event and hover styling
            FileDragHover(e);

            // fetch FileList object
            var files = e.target.files || e.dataTransfer.files;

            // process all File objects
            for (var i = 0, f; f = files[i]; i++) {
                ParseFile(f);
                UploadFile(f);
            }

        }


        // output file information
        function ParseFile(file) {
//      ClearOutput();

            // display an image
            if (file.type.indexOf("image") == 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    Output(
                            '<img src="' + e.target.result + '" /></p>' +
                            "<p><strong>" + file.name +
                            "</strong> : <strong>" + file.type +
                            "</strong> : <strong>" + file.size +
                            "</strong> bytes</p>"
                    );
                };
                reader.readAsDataURL(file);
            }

        }

        // upload JPEG files
        function UploadFile(file) {
            // create progress bar
            var o = document.getElementById("wikieditimg_progress");
            var progress = o.appendChild(document.createElement("p"));
            progress.appendChild(document.createTextNode(file.name));

            if (file.size > document.getElementById("MAX_FILE_SIZE").value) {
                progress.className = "toobig";
                return;
            }
            var x_jsondata = serializeImageDetail(file.name);

            var xhr = new XMLHttpRequest();
            if (xhr.upload && (file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/gif")) {
                progress.className = "sending";
                // progress bar
                xhr.upload.addEventListener("progress", function (e) {
                    var pc = parseInt(100 - (e.loaded / e.total * 100));
                    progress.style.backgroundPosition = pc + "% 0";
                }, false);

                // file received/failed
                xhr.onreadystatechange = function (e) {
                    if (xhr.readyState == 4) {
                        progress.className = (xhr.status == 200 ? "success" : "failed");
                        try {
                            var ajaxreturn = JSON.parse(xhr.responseText);
                            if (typeof(ajaxreturn.message) != 'undefined') {
                                /* Output(ajaxreturn.message); */
                                progress.appendChild(document.createTextNode(' ' + ajaxreturn.message));
                            }
                        }
                        catch (err) {
                            Output(err.message);
                        }
                    }
                };

                // start upload
                xhr.open("POST", document.getElementById("wikieditimg_form").action, true);
                xhr.setRequestHeader("GW-FILENAME", file.name);
                xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
                xhr.send(file);
            }
        }

        // initialize
        function Init() {

            var fileselect = document.getElementById("wikieditimg_fileselect"),
                    filedrag = document.getElementById("wikieditimg_dd"),
                    submitbutton = document.getElementById("wikieditimg_submitbutton");
            // if this runs without the right form displayed, quietly die
            if (fileselect == null) return;
            // file select
            fileselect.addEventListener("change", FileSelectHandler, false);

            // is XHR2 available?
            var xhr = new XMLHttpRequest();

            if (xhr.upload) {
                // file drop
                filedrag.addEventListener("dragover", FileDragHover, false);
                filedrag.addEventListener("dragleave", FileDragHover, false);
                filedrag.addEventListener("drop", FileSelectHandler, false);
                filedrag.style.display = "block";
                window.wikisaysdumbbrowser = false;
            } else {
                document.getElementById("gwikiimgform_nofiledrag").style.display = 'none';
                document.getElementById("wikieditimg_dd_msg").innerHTML = "";
            }
        }

        // call initialization file
        if (document.getElementById("gwikiimgform_nofiledrag")) {
            window.wikisaysdumbbrowser = true; // set to false when proven otherwise
            window.fileUpCount = 0;
            if (window.File && window.FileList && window.FileReader) {
                Init();
            } else {
                document.getElementById("gwikiimgform_nofiledrag").style.display = 'none';
                document.getElementById("wikieditimg_dd_msg").innerHTML = "";
            }
        }

    })();
</script>
