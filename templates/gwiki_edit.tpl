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
<{$gwikiform.javascript}>
<div id="gwikieditor" class="wikieditor">
    <form id="<{$gwikiform.name}>" name="<{$gwikiform.name}>" action="<{$gwikiform.action}>"
          method="<{$gwikiform.method}>" <{$gwikiform.extra}>>
        <table class="outer" cellspacing="1">
            <tr>
                <th colspan="2"><{$gwikiform.title}></th>
            </tr>
            <!-- start of form elements loop -->
            <tbody id="gwikiformbodyedit">
            <{foreach item=element from=$gwikiform.elements}>
            <{if $element.hidden != true}>
                <tr>
                    <td class="head"><{$element.caption}></td>
                    <td class="<{cycle values="even,odd"}>">
                        <{if $element.name == 'gwikieditbuttons'}>
                            <div class="wikieditor-buttontray" id="wikieditor-buttons-reg">
                                <a onclick="addSimpleMarkup('**','**'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BOLD}>"
                                   class="wikieditor-button wikieditor-button-bold">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('//','//'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_ITALIC}>"
                                   class="wikieditor-button wikieditor-button-italic">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('__','__'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_UNDERLINE}>"
                                   class="wikieditor-button wikieditor-button-underline">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('--','--'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_STRIKE}>"
                                   class="wikieditor-button wikieditor-button-strike">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup(',,',',,'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_SUBSCRIPT}>"
                                   class="wikieditor-button wikieditor-button-subscript">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('^^','^^'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_SUPERSCRIPT}>"
                                   class="wikieditor-button wikieditor-button-superscript">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('##','##'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_CODE}>"
                                   class="wikieditor-button wikieditor-button-code">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="buttonGetParams(event,'wikieditor-popform-color'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_COLOR}>"
                                   class="wikieditor-button wikieditor-button-color">
                                    <div>&nbsp;</div>
                                </a>
                            </div>
                            <div class="wikieditor-buttontray">
                                <a onclick="addSimpleMarkup('[[',']]'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_LINK}>"
                                   class="wikieditor-button wikieditor-button-link">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('{{','}}'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_IMAGE}>"
                                   class="wikieditor-button wikieditor-button-image">
                                    <div>&nbsp;</div>
                                </a>
                            </div>
                            <div class="wikieditor-buttontray">
                                <a onclick="buttonGetParams(event,'wikieditor-popform-header'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_HEADER}>"
                                   class="wikieditor-button wikieditor-button-header">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\\\\',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_LINEBREAK}>"
                                   class="wikieditor-button wikieditor-button-linebreak">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\n> ',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_QUOTE}>"
                                   class="wikieditor-button wikieditor-button-quote">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="buttonGetParams(event,'wikieditor-popform-indent'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_INDENT}>"
                                   class="wikieditor-button wikieditor-button-indent">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\n----\n',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_HORIZONTALRULE}>"
                                   class="wikieditor-button wikieditor-button-horizontalrule">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\n* ',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BULLETLIST}>"
                                   class="wikieditor-button wikieditor-button-bulletlist">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\n# ',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_ORDEREDLIST}>"
                                   class="wikieditor-button wikieditor-button-orderedlist">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('\n++ ',''); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_CENTER}>"
                                   class="wikieditor-button wikieditor-button-center">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="addSimpleMarkup('{{{','}}}'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_NOWIKI}>"
                                   class="wikieditor-button wikieditor-button-nowiki">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="buttonGetParams(event,'wikieditor-popform-boxes'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BOXES}>"
                                   class="wikieditor-button wikieditor-button-boxes">
                                    <div>&nbsp;</div>
                                </a>
                                <a onclick="buttonGetParams(event,'wikieditor-popform-ref'); return false;"
                                   title="<{$smarty.const._MD_GWIKI_EDIT_BTN_REFERENCE}>"
                                   class="wikieditor-button wikieditor-button-ref">
                                    <div>&nbsp;</div>
                                </a>
                            </div>
                        <{else}>
                            <{$element.body}>
                        <{/if}>
                    </td>
                </tr>
            <{else}>
                <{$element.body}>
            <{/if}>
            <{if $element.name == 'gwikiformpage1'}>
            </tbody>
            <tbody id="gwikiformmetaedit">
            <{/if}>
            <{/foreach}>
            </tbody>
            <!-- end of form elements loop -->
        </table>
        <div id="bigeditdiv" style="z-index:3000; display:none; background-color:white;">
            <div style="height:36px; width:100%; background-color:grey; text-align: right; border:0; margin-right: 1em;">
                <!--  full window editor Toolbar goes here -->
                <span style="float: left; display: inline-block; vertical-align: middle; margin-top: 6px; margin-left: 1em;">
<div class="wikieditor-buttontray" id="wikieditor-buttons-big">
    <a onclick="addSimpleMarkup('**','**'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BOLD}>"
       class="wikieditor-button wikieditor-button-bold"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('//','//'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_ITALIC}>"
       class="wikieditor-button wikieditor-button-italic"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('__','__'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_UNDERLINE}>"
       class="wikieditor-button wikieditor-button-underline"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('--','--'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_STRIKE}>"
       class="wikieditor-button wikieditor-button-strike"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup(',,',',,'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_SUBSCRIPT}>"
       class="wikieditor-button wikieditor-button-subscript"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('^^','^^'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_SUPERSCRIPT}>"
       class="wikieditor-button wikieditor-button-superscript"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('##','##'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_CODE}>"
       class="wikieditor-button wikieditor-button-code"><div>&nbsp;</div></a>
    <a onclick="buttonGetParams(event,'wikieditor-popform-color'); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_COLOR}>" class="wikieditor-button wikieditor-button-color"><div>&nbsp;</div></a>
</div>

<div class="wikieditor-buttontray">
    <a onclick="addSimpleMarkup('[[',']]'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_LINK}>"
       class="wikieditor-button wikieditor-button-link"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('{{','}}'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_IMAGE}>"
       class="wikieditor-button wikieditor-button-image"><div>&nbsp;</div></a>
</div>

<div class="wikieditor-buttontray">
    <a onclick="buttonGetParams(event,'wikieditor-popform-header'); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_HEADER}>" class="wikieditor-button wikieditor-button-header"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\\\\',''); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_LINEBREAK}>"
       class="wikieditor-button wikieditor-button-linebreak"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\n> ',''); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_QUOTE}>"
       class="wikieditor-button wikieditor-button-quote"><div>&nbsp;</div></a>
    <a onclick="buttonGetParams(event,'wikieditor-popform-indent'); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_INDENT}>" class="wikieditor-button wikieditor-button-indent"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\n----\n',''); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_HORIZONTALRULE}>"
       class="wikieditor-button wikieditor-button-horizontalrule"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\n* ',''); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BULLETLIST}>"
       class="wikieditor-button wikieditor-button-bulletlist"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\n# ',''); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_ORDEREDLIST}>"
       class="wikieditor-button wikieditor-button-orderedlist"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('\n++ ',''); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_CENTER}>"
       class="wikieditor-button wikieditor-button-center"><div>&nbsp;</div></a>
    <a onclick="addSimpleMarkup('{{{','}}}'); return false;" title="<{$smarty.const._MD_GWIKI_EDIT_BTN_NOWIKI}>"
       class="wikieditor-button wikieditor-button-nowiki"><div>&nbsp;</div></a>
    <a onclick="buttonGetParams(event,'wikieditor-popform-boxes'); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_BOXES}>" class="wikieditor-button wikieditor-button-boxes"><div>&nbsp;</div></a>
    <a onclick="buttonGetParams(event,'wikieditor-popform-ref'); return false;"
       title="<{$smarty.const._MD_GWIKI_EDIT_BTN_REFERENCE}>" class="wikieditor-button wikieditor-button-ref"><div>&nbsp;</div></a>
</div>
</span>
                <span style="float:right; display: inline-block; vertical-align: middle; margin-top: 6px; margin-right: 2em;">
   <a onclick="toggleDiv('wikiimageedit'); return false;"><img src="<{$gwiki.modurl}>/assets/images/imageicon.png"
                                                               alt="<{$smarty.const._MD_GWIKI_IMAGES}>"
                                                               title="<{$smarty.const._MD_GWIKI_IMAGES}>"/></a>
   <a onclick="toggleDiv('wikifileedit'); return false;"><img src="<{$gwiki.modurl}>/assets/images/attachicon.png"
                                                              alt="<{$smarty.const._MD_GWIKI_ATTACHMENT_EDIT}>"
                                                              title="<{$smarty.const._MD_GWIKI_ATTACHMENT_EDIT}>"/></a>
   <a onclick="helpwindow();return false;"><img src="<{$gwiki.modurl}>/assets/images/helpicon.png"
                                                alt="<{$smarty.const._MD_GWIKI_WIKI_EDIT_HELP}>"
                                                title="<{$smarty.const._MD_GWIKI_WIKI_EDIT_HELP}>"/></a>
   <a onclick="bigWindow(0); return false;"><img src="<{$gwiki.modurl}>/assets/images/closeicon.png"
                                                 alt="<{$smarty.const._MD_GWIKI_FULLSCREEN_EXIT}>"
                                                 title="<{$smarty.const._MD_GWIKI_FULLSCREEN_EDIT}>"/></a>
   </span>
            </div>
            <textarea id="editfield" onclick="setWikiChanged();"
                      style="width:100%; height:94%; overflow:auto; border:0; margin-right: 1em;"> </textarea>
        </div>
    </form>
</div>
<{include file="db:gwiki_page_info.tpl"}>

<{if $gwiki.preview}><p class="itemInfo"><{$smarty.const._MD_GWIKI_PAGE}>:
    <strong><{$gwiki.keyword}></strong>
    - <{$smarty.const._MD_GWIKI_PREVIEW}></p>
    <div id="wikipage" class="wikipage">
        <h1 class="wikititle" id="toc0"><{$gwiki.title}></h1>
        <{$gwiki.body}>
    </div>
    <br clear="all"/>
    <a href="#<{$gwikiform.name}>"><img src="<{$gwiki.modurl}>/assets/images/upicon.png"
                                        alt="<{$smarty.const._MD_GWIKI_BACK_TO_TOP}>"/> <{$smarty.const._MD_GWIKI_BACK_TO_TOP}>
    </a>
<{/if}>

<div id="wikiimageedit" class="wikiimageedit">
    <div class="wikiimageedit_title"><{$smarty.const._MD_GWIKI_IMAGES_TITLE}></div>
    <table>
        <tr>
            <td width="10%">
                <h2><{$smarty.const._MD_GWIKI_IMAGES_LIST}></h2>

                <div id="wikiimgliblist" class="wikiimagelist">
                    <br><{$smarty.const._MD_GWIKI_IMAGES_LIBRARY}><br><select id="imagelib" name="imagelib"
                                                                              onchange="clearImageDetail(); fetchImageList();">
                        <{foreach item=imglib from=$gwiki.imglib}>
                            <option value="<{$imglib}>"><{$imglib}></option>
                        <{/foreach}>
                    </select>
                </div>
                <div id="wikiimagelist" class="wikiimagelist">
                    <{$imagelist}>
                </div>
            </td>
            <td width="90%">
                <h2><{$smarty.const._MD_GWIKI_IMAGES_DETAIL}></h2>

                <div class="wikiimagedetail">
                    <form id="wikieditimg_form" action="ajaximgedit.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<{$gwiki.maxsize}>"/>
                        <input type="hidden" id="page" name="page" value="<{$gwiki.keyword}>"/>
                        <input type="hidden" id="wikiimage_id" name="image_id" value="0"/>

                        <div id="wikieditimg_dd">
                            <img name="wikieditimg_img" id="wikieditimg_img" class="wikieditimg"
                                 src="assets/images/blank.png"/>
                            <br><span id="wikieditimg_dd_msg"><{$smarty.const._MD_GWIKI_IMAGES_DROPHERE}></span>

                            <div id="gwikiimgform_nofiledrag"><{$smarty.const._MD_GWIKI_IMAGES_PICKFILE}><input
                                        type="file" id="wikieditimg_fileselect" name="fileselect[]"/></div>
                            <div id="wikieditimg_progress"></div>
                        </div>
                        <span class="wikieditimg_field"><{$smarty.const._MD_GWIKI_IMAGES_NAME}></span>
                        <input type="text" id="wikieditimg_name" name="wikieditimg_name" size="30" value=""
                               onchange="fieldEventHandler('change','wikieditimg_name');"
                               onfocus="fieldEventHandler('focus','wikieditimg_name');"/>
                        <br><span class="wikieditimg_field"><{$smarty.const._MD_GWIKI_IMAGES_ALTTEXT}></span>
                        <input type="text" id="wikieditimg_alttext" name="wikieditimg_alttext" size="30" value=""
                               onchange="fieldEventHandler('change','wikieditimg_alttext');"
                               onfocus="fieldEventHandler('focus','wikieditimg_alttext');"/>
                        <br><span class="wikieditimg_field"> </span>
                        <input type="checkbox" id="wikieditimg_represent" name="wikieditimg_represent" value="represent"
                               onchange="fieldEventHandler('change','wikieditimg_represent');"
                               onfocus="fieldEventHandler('focus','wikieditimg_represent');"/> <{$smarty.const._MD_GWIKI_IMAGES_REPRESENT}>
                        <br><br>

                        <div id="wikiedit_buttons">
                            <span id="wikieditimg_submitbutton"><button type="button"
                                                                        onclick="updateImageDetail();"><{$smarty.const._MD_GWIKI_IMAGES_UPDATE}></button></span>
                            <span id="wikieditimg_newbutton"><button type="button"
                                                                     onclick="newImageDetail();"><{$smarty.const._MD_GWIKI_IMAGES_NEW}></button></span>
                            <span id="wikieditimg_deletebutton"><button type="button"
                                                                        onclick="deleteImageDetail();"><{$smarty.const._MD_GWIKI_IMAGES_DELETE}></button></span>
                        </div>
                    </form>
                </div>
                <div class="wikiimagedetail">
                    <form id="insertimage">
                        <{$smarty.const._MD_GWIKI_IMAGES_INSERT_TITLE}><br>
                        <{$smarty.const._MD_GWIKI_IMAGES_ALIGN}><select id="wikiimage_align" name="align">
                            <option value=""><{$smarty.const._MD_GWIKI_IMAGES_ALIGN_NONE}></option>
                            <option value="left"><{$smarty.const._MD_GWIKI_IMAGES_ALIGN_LEFT}></option>
                            <option value="center"><{$smarty.const._MD_GWIKI_IMAGES_ALIGN_CENTER}></option>
                            <option value="right"><{$smarty.const._MD_GWIKI_IMAGES_ALIGN_RIGHT}></option>
                        </select>
                        <{$smarty.const._MD_GWIKI_IMAGES_MAX_WIDTH}><input id="wikiimage_maxwidth" name='maxwidth'
                                                                           type="text" size="3">
                        <a onclick="addImageMarkup();" title="<{$smarty.const._MD_GWIKI_IMAGES_INSERT_TIP}>"><img
                                    src="assets/images/insimgicon.png"/></a>
                        <a onclick="toggleDiv('wikiimageedit'); return false;"><img src="assets/images/closeicon.png"
                                                                                    title="<{$smarty.const._MD_GWIKI_IMAGES_CLOSE}>"
                                                                                    align="right"/></a>
                    </form>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="wikifileedit" class="wikifileedit">
    <div class="wikifileedit_title"><{$smarty.const._MD_GWIKI_FILES_TITLE}></div>
    <table>
        <tr>
            <td width="10%">
                <h2><{$smarty.const._MD_GWIKI_FILES_LIST}></h2>

                <div id="wikifilelist" class="wikifilelist">
                </div>
            </td>
            <td width="90%">
                <h2><{$smarty.const._MD_GWIKI_FILES_DETAIL}></h2>

                <div class="wikifiledetail">
                    <form id="wikieditfile_form" action="ajaxfileedit.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<{$gwiki.maxsize}>"/>
                        <input type="hidden" id="page" name="page" value="<{$gwiki.keyword}>"/>
                        <input type="hidden" id="wikifile_id" name="file_id" value="0"/>

                        <div id="wikieditfile_dd">
                            <img name="wikieditfile_img" id="wikieditfile_img" class="wikieditfile"
                                 src="assets/images/blank.png"/>
                            <br><span id="wikieditfile_dd_msg"><{$smarty.const._MD_GWIKI_FILES_DROPHERE}></span>

                            <div id="wikifileform_nofiledrag"><{$smarty.const._MD_GWIKI_FILES_PICKFILE}><input
                                        type="file" id="wikieditfile_fileselect" name="fileselect[]"/></div>
                            <div id="wikieditfile_progress"></div>
                        </div>
                        <span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_NAME}></span><span
                                id="wikieditfile_name" class="wikieditfile_field"/> </span>
                        <br><span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_TYPE}></span><span
                                id="wikieditfile_type" class="wikieditfile_field"/> </span>
                        <br><span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_SIZE}></span><span
                                id="wikieditfile_size" class="wikieditfile_field"/> </span>
                        <br><span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_DATE}></span><span
                                id="wikieditfile_date" class="wikieditfile_field"/> </span>
                        <br><span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_DESCRIPTION}></span>
                        <span class="wikieditfile_field" style="display: inline-block; vertical-align: top;"> <textarea
                                    id="wikieditfile_description" name="wikieditfile_description"
                                    rows="3" cols="40"
                                    onchange="fileFieldEventHandler('change','wikieditfile_description');"
                                    onfocus="fileFieldEventHandler('focus','wikieditfile_description');"> </textarea></span>
                        <br><span class="wikieditfile_fieldname"><{$smarty.const._MD_GWIKI_FILES_USER}></span><span
                                id="wikieditfile_userlink" class="wikieditfile_field"/> </span>
                        <br><br>

                        <div id="wikiedit_buttons">
                                                        <span id="wikieditfile_submitbutton"><button type="button"
                                                                                                     onclick="updateFileDetail();"><{$smarty.const._MD_GWIKI_FILES_UPDATE}></button></span>
                            <span id="wikieditfile_newbutton"><button type="button"
                                                                      onclick="newFileDetail();"><{$smarty.const._MD_GWIKI_FILES_NEW}></button></span>
                            <span id="wikieditfile_deletebutton"><button type="button"
                                                                         onclick="deleteFileDetail();"><{$smarty.const._MD_GWIKI_FILES_DELETE}></button></span>
                        </div>
                    </form>
                </div>
                <div>
                    <a onclick="toggleDiv('wikifileedit'); return false;"><img src="assets/images/closeicon.png"
                                                                               title="<{$smarty.const._MD_GWIKI_FILES_CLOSE}>"
                                                                               align="right"
                                                                               style="margin:0.5em;"/></a>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="wikieditor-popform-color" class="wikieditor-param-container">
    <div class="wikieditor-param-top"><{$smarty.const._MD_GWIKI_EDIT_BTN_COLOR}></div>
    <form>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_COLOR}></span>
        <span class="wikieditor-param-value">
<select name="popformcolor">
    <option value=""><{$smarty.const._MD_GWIKI_EDPOP_CLR_DEFAULT}></option>
    <option value="aqua"><{$smarty.const._MD_GWIKI_EDPOP_CLR_AQUA}></option>
    <option value="black"><{$smarty.const._MD_GWIKI_EDPOP_CLR_BLACK}></option>
    <option value="blue"><{$smarty.const._MD_GWIKI_EDPOP_CLR_BLUE}></option>
    <option value="fuchsia"><{$smarty.const._MD_GWIKI_EDPOP_CLR_FUCHSIA}></option>
    <option value="gray"><{$smarty.const._MD_GWIKI_EDPOP_CLR_GRAY}></option>
    <option value="green"><{$smarty.const._MD_GWIKI_EDPOP_CLR_GREEN}></option>
    <option value="lime"><{$smarty.const._MD_GWIKI_EDPOP_CLR_LIME}></option>
    <option value="maroon"><{$smarty.const._MD_GWIKI_EDPOP_CLR_MAROON}></option>
    <option value="navy"><{$smarty.const._MD_GWIKI_EDPOP_CLR_NAVY}></option>
    <option value="olive"><{$smarty.const._MD_GWIKI_EDPOP_CLR_OLIVE}></option>
    <option value="orange"><{$smarty.const._MD_GWIKI_EDPOP_CLR_ORANGE}></option>
    <option value="purple"><{$smarty.const._MD_GWIKI_EDPOP_CLR_PURPLE}></option>
    <option value="red"><{$smarty.const._MD_GWIKI_EDPOP_CLR_RED}></option>
    <option value="silver"><{$smarty.const._MD_GWIKI_EDPOP_CLR_SILVER}></option>
    <option value="teal"><{$smarty.const._MD_GWIKI_EDPOP_CLR_TEAL}></option>
    <option value="white"><{$smarty.const._MD_GWIKI_EDPOP_CLR_WHITE}></option>
    <option value="yellow"><{$smarty.const._MD_GWIKI_EDPOP_CLR_YELLOW}></option>
</select>
</span></br>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_BGCOLOR}></span>
        <span class="wikieditor-param-value">
<select name="popformbgcolor">
    <option value=""><{$smarty.const._MD_GWIKI_EDPOP_CLR_DEFAULT}></option>
    <option value="aqua"><{$smarty.const._MD_GWIKI_EDPOP_CLR_AQUA}></option>
    <option value="black"><{$smarty.const._MD_GWIKI_EDPOP_CLR_BLACK}></option>
    <option value="blue"><{$smarty.const._MD_GWIKI_EDPOP_CLR_BLUE}></option>
    <option value="fuchsia"><{$smarty.const._MD_GWIKI_EDPOP_CLR_FUCHSIA}></option>
    <option value="gray"><{$smarty.const._MD_GWIKI_EDPOP_CLR_GRAY}></option>
    <option value="green"><{$smarty.const._MD_GWIKI_EDPOP_CLR_GREEN}></option>
    <option value="lime"><{$smarty.const._MD_GWIKI_EDPOP_CLR_LIME}></option>
    <option value="maroon"><{$smarty.const._MD_GWIKI_EDPOP_CLR_MAROON}></option>
    <option value="navy"><{$smarty.const._MD_GWIKI_EDPOP_CLR_NAVY}></option>
    <option value="olive"><{$smarty.const._MD_GWIKI_EDPOP_CLR_OLIVE}></option>
    <option value="orange"><{$smarty.const._MD_GWIKI_EDPOP_CLR_ORANGE}></option>
    <option value="purple"><{$smarty.const._MD_GWIKI_EDPOP_CLR_PURPLE}></option>
    <option value="red"><{$smarty.const._MD_GWIKI_EDPOP_CLR_RED}></option>
    <option value="silver"><{$smarty.const._MD_GWIKI_EDPOP_CLR_SILVER}></option>
    <option value="teal"><{$smarty.const._MD_GWIKI_EDPOP_CLR_TEAL}></option>
    <option value="white"><{$smarty.const._MD_GWIKI_EDPOP_CLR_WHITE}></option>
    <option value="yellow"><{$smarty.const._MD_GWIKI_EDPOP_CLR_YELLOW}></option>
</select>
</span></br>
        <span class="wikieditor-param-title"> </span>
        <span class="wikieditor-param-value"><button type="button"
                                                     onclick="processPopFormColor(this.form);"><{$smarty.const.MD_GWIKI_EDIT_BTN_INSERT}></button>
<button type="button" onclick="closeAllParams();"><{$smarty.const.MD_GWIKI_EDIT_BTN_CANCEL}></button>
</span><br>
        <span class="wikieditor-param-bottom"></span>
    </form>
</div>

<div id="wikieditor-popform-header" class="wikieditor-param-container">
    <div class="wikieditor-param-top"><{$smarty.const._MD_GWIKI_EDIT_BTN_HEADER}></div>
    <form>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_HEADER}></span>
        <span class="wikieditor-param-value">
<select name="popformheader">
    <option value="1"><{$smarty.const._MD_GWIKI_EDPOP_HEAD_1}></option>
    <option value="2"><{$smarty.const._MD_GWIKI_EDPOP_HEAD_2}></option>
    <option value="3"><{$smarty.const._MD_GWIKI_EDPOP_HEAD_3}></option>
    <option value="4"><{$smarty.const._MD_GWIKI_EDPOP_HEAD_4}></option>
    <option value="5"><{$smarty.const._MD_GWIKI_EDPOP_HEAD_5}></option>
</select>
</span></br>
        <span class="wikieditor-param-title"> </span>
        <span class="wikieditor-param-value"><button type="button"
                                                     onclick="processPopFormHeader(this.form);"><{$smarty.const.MD_GWIKI_EDIT_BTN_INSERT}></button>
<button type="button" onclick="closeAllParams();"><{$smarty.const.MD_GWIKI_EDIT_BTN_CANCEL}></button>
</span><br>
    </form>
    <span class="wikieditor-param-bottom"></span>
</div>

<div id="wikieditor-popform-indent" class="wikieditor-param-container">
    <div class="wikieditor-param-top"><{$smarty.const._MD_GWIKI_EDIT_BTN_INDENT}></div>
    <form>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_INDENT}></span>
        <span class="wikieditor-param-value">
<select name="popformindent">
    <option value="1"><{$smarty.const._MD_GWIKI_EDPOP_INDENT_1}></option>
    <option value="2"><{$smarty.const._MD_GWIKI_EDPOP_INDENT_2}></option>
    <option value="3"><{$smarty.const._MD_GWIKI_EDPOP_INDENT_3}></option>
    <option value="4"><{$smarty.const._MD_GWIKI_EDPOP_INDENT_4}></option>
    <option value="5"><{$smarty.const._MD_GWIKI_EDPOP_INDENT_5}></option>
</select>
</span></br>
        <span class="wikieditor-param-title"> </span>
        <span class="wikieditor-param-value"><button type="button"
                                                     onclick="processPopFormIndent(this.form);"><{$smarty.const.MD_GWIKI_EDIT_BTN_INSERT}></button>
<button type="button" onclick="closeAllParams();"><{$smarty.const.MD_GWIKI_EDIT_BTN_CANCEL}></button>
</span><br>
    </form>
    <span class="wikieditor-param-bottom"></span>
</div>

<div id="wikieditor-popform-boxes" class="wikieditor-param-container">
    <div class="wikieditor-param-top"><{$smarty.const._MD_GWIKI_EDIT_BTN_BOXES}></div>
    <form action="#" onsubmit="return false;">
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_BXTYPE}></span>
        <span class="wikieditor-param-value">
<select name="popformboxtype">
    <option value="code"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_CODE}></option>
    <option value="folded"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_FOLD}></option>
    <option value="info"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_INFO}></option>
    <option value="note"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_NOTE}></option>
    <option value="tip"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_TIP}></option>
    <option value="warning"><{$smarty.const._MD_GWIKI_EDPOP_BXTYPE_WARN}></option>
</select>
</span></br>
        <span class="wikieditor-param-title">Title:</span> <span class="wikieditor-param-value"><input
                    name="popformboxtitle" type="text" size="12">
</span><br>
        <span class="wikieditor-param-title"> </span>
        <span class="wikieditor-param-value"><button type="submit"
                                                     onclick="processPopFormBoxes(this.form);"><{$smarty.const.MD_GWIKI_EDIT_BTN_INSERT}></button>
<button type="button" onclick="closeAllParams();"><{$smarty.const.MD_GWIKI_EDIT_BTN_CANCEL}></button>
</span><br>
    </form>
    <span class="wikieditor-param-bottom"></span>
</div>

<div id="wikieditor-popform-ref" class="wikieditor-param-container">
    <div class="wikieditor-param-top"><{$smarty.const._MD_GWIKI_EDIT_BTN_REFERENCE}></div>
    <form action="#" onsubmit="return false;">
        <div class="wikieditor-param-prompt"><{$smarty.const._MD_GWIKI_EDPOP_XPRMT_REF1}></div>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_REF_ID}></span> <span
                class="wikieditor-param-value"><input name="popformrefid" type="text" size="12">
</span><br>

        <div class="wikieditor-param-prompt"><{$smarty.const._MD_GWIKI_EDPOP_XPRMT_REF2}></div>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_REF_FIRST}></span> <span
                class="wikieditor-param-value"><input name="popformreffirst" type="text" size="12">
</span><br>
        <span class="wikieditor-param-title"><{$smarty.const._MD_GWIKI_EDPOP_PRMT_REF_REPEAT}></span> <span
                class="wikieditor-param-value"><input name="popformrefrepeat" type="text" size="12">
</span><br>
        <span class="wikieditor-param-title"> </span>
        <span class="wikieditor-param-value"><button type="submit"
                                                     onclick="processPopFormRef(this.form);"><{$smarty.const.MD_GWIKI_EDIT_BTN_INSERT}></button>
<button type="button" onclick="closeAllParams();"><{$smarty.const.MD_GWIKI_EDIT_BTN_CANCEL}></button>
</span><br>
    </form>
    <span class="wikieditor-param-bottom"></span>
</div>

<script type="text/javascript" language="javascript">
    // some misc script things
    function helpwindow() {
        window.open('<{$gwiki.modurl}>/language/<{$smarty.const._MD_GWIKI_WIKI_HELP_DIR}>/help/wikihelp.html', '1359403524804', 'width=400,height=680,toolbar=0,menubar=1,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
    }

    function buttonGetParams(trigger, formname) {
        closeAllParams();
        var theEvent = trigger ? trigger : window.event;
        var web = idEditButtons();
        var rect = web.getBoundingClientRect();
        var formwin = document.getElementById(formname);
        formwin.style.position = "fixed";
        formwin.style.left = (theEvent.clientX - 128) + "px";
        formwin.style.top = rect.bottom + "px";
        formwin.style.display = "block";
    }

    function closeAllParams() {
        document.getElementById('wikieditor-popform-color').style.display = "none";
        document.getElementById('wikieditor-popform-header').style.display = "none";
        document.getElementById('wikieditor-popform-indent').style.display = "none";
        document.getElementById('wikieditor-popform-boxes').style.display = "none";
        document.getElementById('wikieditor-popform-ref').style.display = "none";
    }

    function processPopFormColor(form) {
        var fg = form.popformcolor.value;
        var bg = form.popformbgcolor.value;
        var openmarkup = "!!" + fg;
        if (bg != "") openmarkup = openmarkup + "," + bg;
        openmarkup = openmarkup + ":";
        var closemarkup = "!!";
        addSimpleMarkup(openmarkup, closemarkup);
    }

    function processPopFormHeader(form) {
        var openmarkup = "\n=";
        for (var i = 0; i < (form.popformheader.value - 1); i++) {
            openmarkup = openmarkup + "=";
        }
        openmarkup = openmarkup + " ";
        var closemarkup = "";
        addSimpleMarkup(openmarkup, closemarkup);
    }

    function processPopFormIndent(form) {
        var openmarkup = "\n:";
        for (var i = 0; i < (form.popformindent.value - 1); i++) {
            openmarkup = openmarkup + ":";
        }
        openmarkup = openmarkup + " ";

        var closemarkup = "";
        addSimpleMarkup(openmarkup, closemarkup);
    }

    function processPopFormBoxes(form) {
        var openmarkup = "{" + form.popformboxtype.value + " " + form.popformboxtitle.value + "}";
        var closemarkup = "{end" + form.popformboxtype.value + "}";
        addSimpleMarkup(openmarkup, closemarkup);
        form.popformboxtitle.value = '';
    }

    function processPopFormRef(form) {
        var openmarkup = "{ref";
        if (form.popformrefid.value != "") {
            openmarkup = openmarkup + " " + form.popformrefid.value;
        }
        if (form.popformreffirst.value != "") {
            if (form.popformrefid.value == "") {
                openmarkup = openmarkup + " ";
            }
            openmarkup = openmarkup + "|" + form.popformreffirst.value;
            if (form.popformrefrepeat.value != "") {
                openmarkup = openmarkup + "|" + form.popformrefrepeat.value;
            }
        }
        openmarkup = openmarkup + "}";
        var closemarkup = "{endref}";
        addSimpleMarkup(openmarkup, closemarkup);
        form.popformreffirst.value = '';
        form.popformrefrepeat.value = '';
    }

    function idEditButtons() {
        var e = document.getElementById("bigeditdiv");
        if (e.style.display == 'block') return (document.getElementById("wikieditor-buttons-big"));
        return (document.getElementById('wikieditor-buttons-reg'));
    }

    // we have 2 editor textareas - one normal, one full screen. Figure out which one is active and return it
    function idEditField() {
        var e = document.getElementById("bigeditdiv");
        if (e.style.display == 'block') return (document.getElementById("editfield"));
        return (document.getElementById("body"));
    }

    function addSimpleMarkup(openmarkup, closemarkup) {
        var e = idEditField();

        if (e.selectionStart || e.selectionStart == '0') {
            var start = e.selectionStart;
            var end = e.selectionEnd;
            var sel = e.value.substring(start, end);
        }
        else if (document.selection) {
            var sel = window.SavedSelection.sel;
            var start = window.SavedSelection.start;
            var end = window.SavedSelection.end;
            restoreSavedSelection(e);
        }

        if (start == end) {
            e.focus();
            insertAtCursor(e, openmarkup, closemarkup);
        }
        else {
            insertAtCursor(e, openmarkup + sel + closemarkup, '');
        }
        e.focus();
        closeAllParams();
        return true;
    }

    function addImageMarkup() {
        var image_id = document.getElementById("wikiimage_id").value;
        var image_name = document.getElementById("wikieditimg_name").value;
        var image_alt = document.getElementById("wikieditimg_alttext").value;
        var image_align = document.getElementById("wikiimage_align").value;
        var image_maxwidth = document.getElementById("wikiimage_maxwidth").value;
        var markup = '';
        if (image_id == 0 || image_name == '') {
            alert("<{$smarty.const._MD_GWIKI_IMAGES_NO_SELECTION}>");
            return false;
        }
        // going backwards - only add empty values if we already have arguments that will be behind in the markup
        if (image_maxwidth != '' || markup != '') {
            markup = '|' + image_maxwidth + markup;
        }
        if (image_align != '' || markup != '') {
            markup = '|' + image_align + markup;
        }
        // we will always use the default alt tag, so leave empty
        if (markup != '') {
            markup = '|' + markup;
        }
        markup = '{{' + image_name + markup + '}}';
        var e = idEditField();
        insertAtCursor(e, markup, '');
        toggleDiv('wikiimageedit');
        e.focus();
        return true;
    }

    function insertAtCursor(myField, openmarkup, closemarkup) {
        setWikiChanged();
        // modern
        if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)
                    + openmarkup + closemarkup
                    + myField.value.substring(endPos, myField.value.length);
            var pos = startPos;
            myField.setSelectionRange(pos, pos);
            moveCaretPosition(myField, openmarkup.length);
        }
        // try older IE
        else if (document.selection) {
            myField.focus();
            pos = getCaretPosition(myField);
            sel = document.selection.createRange();
            var org = sel.text;
            sel.text = openmarkup + closemarkup;
            pos = pos + openmarkup.length;
            setCaretPosition(myField, pos);
            myField.focus();
        } else {
            myField.value += (openmarkup + closemarkup);
        }
    }

    function moveCaretPosition(myField, delta) {
        setCaretPosition(myField, getCaretPosition(myField) + delta);
    }

    function getCaretPosition(ctrl) {
        var CaretPos = 0;
        // modern
        if (ctrl.selectionStart || ctrl.selectionStart == '0') {
            CaretPos = ctrl.selectionStart;
        }
        // older IE
        else if (document.selection) {
            ctrl.focus();
            CaretPos = getInputSelection(ctrl).start;
        }
        return (CaretPos);
    }

    function setCaretPosition(ctrl, pos) {
        // modern
        if (ctrl.setSelectionRange) {
            ctrl.focus();
            ctrl.setSelectionRange(pos, pos);
        }
        // older IE
        else {
            setSelection(ctrl, pos, pos);
        }
    }

    // enable a full screen textarea for editing
    function viewport() {
        var e = window, a = 'inner';
        if (!( 'innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return {width: e[a + 'Width'], height: e[a + 'Height']}
    }

    function bigWindow(op) {
        closeAllParams();
        var w = viewport();
        var e = document.getElementById("bigeditdiv");
        var f1 = document.getElementById("body");
        var f2 = document.getElementById("editfield");
        if (op == 1) {
            f2.value = f1.value;
            e.style.display = "block";
            e.style.position = "fixed";
            var basewidth = w.width;
            e.style.width = basewidth + "px";
            var baseheight = w.height;
            e.style.height = baseheight + "px";
            e.style.left = 0;
            e.style.top = 0;
            e.style.zIndex = "3000";
            f2.style.width = (basewidth - 20) + "px";
            f2.style.height = (baseheight - 36 - 20) + "px";
            setCaretPosition(f2, getCaretPosition(f1));
            f2.focus();
        }
        else {
            f1.value = f2.value;
            setCaretPosition(f1, getCaretPosition(f2));
            f1.focus();
            e.style.display = "none";
        }
    }

    // output status and other info
    function Output(msg) {
        var m = document.getElementById("wikieditimg_dd_msg");
        m.innerHTML = msg;
    }


    function ClearOutput() {
        var m = document.getElementById("wikieditimg_dd_msg");
        if (window.wikisaysdumbbrowser == true) {
            m.innerHTML = '';
        }
        else {
            m.innerHTML = "<{$smarty.const._MD_GWIKI_IMAGES_DROPHERE}>";
        }
        m = document.getElementById("wikieditimg_progress");
        m.innerHTML = '';
    }


    function toggleDiv(id) {
        var ele = document.getElementById(id);
        if (ele.style.display == "block") {
            ele.style.display = "none";
        }
        else {
            if (id == 'wikifileedit') {
                clearFileDetail();
                fetchFileList();
                var newZ = document.getElementById('wikiimageedit').style.zIndex;
            }
            if (id == 'wikiimageedit') {
                clearImageDetail();
                fetchImageList();
                var newZ = document.getElementById('wikifileedit').style.zIndex;
            }
            if (newZ < 4000) newZ = 4000;
            ele.style.zIndex = (++newZ);

            ele.style.position = "absolute";
            if (typeof(window.pageYOffset) == 'number') {
                ele.style.top = (window.pageYOffset) + "px";
                ele.style.left = window.pageXOffset + "px";
            }
            else {
                ele.style.top = document.documentElement.scrollTop + "px";
                ele.style.left = document.documentElement.scrollLeft + "px";
            }
            ele.style.display = "block";
        }
    }

    function hideDiv(id) {
        var ele = document.getElementById(id);
        ele.style.display = "none";
    }

    function clearImageDetail() {
        document.getElementById("wikiimage_id").value = '0';
        document.getElementById("wikieditimg_name").value = '';
        document.getElementById("wikieditimg_alttext").value = '';
        document.getElementById("wikieditimg_represent").checked = false;

        document.getElementById("wikieditimg_img").src = 'assets/images/blank.png';

        document.getElementById("wikieditimg_fileselect").value = '';
        document.getElementById("wikieditimg_progress").innerHTML = '';
        document.getElementById("wikieditimg_name").className = 'wikifield_input_reset';
        document.getElementById("wikieditimg_alttext").className = 'wikifield_input_reset';
        ClearOutput();
    }

    function getImageListById(imageid) {

        clearImageDetail();

        document.getElementById("wikiimage_id").value = window.imageList[imageid].image_id;
        document.getElementById("wikieditimg_name").value = window.imageList[imageid].image_name;
        document.getElementById("wikieditimg_alttext").value = window.imageList[imageid].image_alt_text;
        document.getElementById("wikieditimg_represent").checked = (window.imageList[imageid].use_to_represent != "0"); // make boolean

        document.getElementById("wikieditimg_img").src = window.imageList[imageid].link;

    }

    function fetchImageList() {
        document.getElementById("wikiimagelist").innerHTML = '<img id="loadanim" src="assets/images/loading-anim.gif" />';
        window.imageList = new Object;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var ajaxreturn = JSON.parse(xmlhttp.responseText);
                var cnt = 0;
                var body = '';
                for (i in ajaxreturn) {
                    ++cnt;
                    window.imageList[ajaxreturn[i].image_id] = ajaxreturn[i];
                    body = body + '<br><img src="' + ajaxreturn[i].link + '" alt="' + ajaxreturn[i].image_alt_text + '" title="' + ajaxreturn[i].image_name + '" onclick="getImageListById(\'' + ajaxreturn[i].image_id + '\');" /><br>';
                    body = body + ajaxreturn[i].image_name + '<br>';
                }
                if (cnt == 0) {
                    body = '<{$smarty.const._MD_GWIKI_IMAGES_EMPTY}>';
                }
                document.getElementById("wikiimagelist").innerHTML = body + '<br>';
            }
        };

        xmlhttp.open("GET", "ajaximglist.php?page=" + document.getElementById('imagelib').value, true); // +'&cachekill=' + Math.random()
        xmlhttp.send();
    }

    function serializeImageDetail() {
        var image = new Object;

        image.page = document.getElementById("page").value;
        image.image_id = document.getElementById("wikiimage_id").value;
        image.image_name = document.getElementById("wikieditimg_name").value;
        image.image_alt_text = document.getElementById("wikieditimg_alttext").value;
        image.use_to_represent = document.getElementById("wikieditimg_represent").checked;
        var jsonimg = JSON.stringify(image);
        return jsonimg;
    }

    function deleteImageDetail() {
        var doit = confirm("<{$smarty.const._MD_GWIKI_IMAGES_DELETE_CONFIRM}>");
        if (doit) {
            var deletereq = new Object;
            deletereq.page = document.getElementById("page").value;
            deletereq.image_id = document.getElementById("wikiimage_id").value;
            deletereq.op = 'delete';
            var x_jsondata = JSON.stringify(deletereq);

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function (e) {
                if (xhr.readyState == 4) {
                    try {
                        var ajaxreturn = JSON.parse(xhr.responseText);
                        if (typeof(ajaxreturn.message) != 'undefined') {
                            clearImageDetail();
                            document.getElementById("wikieditimg_dd_msg").innerHTML = ajaxreturn.message;
                            fetchImageList();
                        }
                    }
                    catch (err) {
                        document.getElementById("wikieditimg_dd_msg").innerHTML = 'JSON response error';
                    }
                }
            };
            // send data
            xhr.open("POST", document.getElementById("wikieditimg_form").action, true);
            xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
            xhr.send();
        }
    }

    function newImageDetail() {
        clearImageDetail();
    }

    function updateImageDetail() {
        alert("To be completed");
    }

    // handles all the individual field changes
    function fieldEventHandler(event, field) {
        if (event == "focus") {
            document.getElementById(field).className = 'wikifield_input_reset';
            return;
        }

        var x_jsondata = serializeImageDetail();

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (e) {
            if (xhr.readyState == 4) {
                try {
                    var ajaxreturn = JSON.parse(xhr.responseText);
                    if (typeof(ajaxreturn.message) != 'undefined') {
                        Output(ajaxreturn.message);
                    }
                }
                catch (err) {
                    Output('JSON response error');
                }
            }
            document.getElementById(field).className = 'wikifield_input_ok';
            fetchImageList();
        };
        // send data
        xhr.open("POST", document.getElementById("wikieditimg_form").action, true);
        xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
        xhr.send();
    }

    /*
     taken from
     filedrag.js - HTML5 File Drag & Drop demonstration
     Featured on SitePoint.com
     Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
     */

    (function () {

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
            ClearOutput();
            Output(
                    "<p><strong>" + file.name +
                    "</strong> : <strong>" + file.type +
                    "</strong> : <strong>" + file.size +
                    "</strong> bytes</p>"
            );

            // display an image
            if (file.type.indexOf("image") == 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var i = document.getElementById('wikieditimg_img');
                    i.src = e.target.result;
                    Output(file.name);
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
            var x_jsondata = serializeImageDetail();

            var xhr = new XMLHttpRequest();
            if (xhr.upload && (file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/gif")) {

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
                            if (typeof(ajaxreturn.image_id) != 'undefined') {
                                document.getElementById("wikiimage_id").value = ajaxreturn.image_id;
                                document.getElementById("wikieditimg_name").value = ajaxreturn.image_name;
                                fetchImageList();
                            }
                            if (typeof(ajaxreturn.message) != 'undefined') {
                                Output(ajaxreturn.message);
                            }
                        }
                        catch (err) {
                            Output('JSON response error');
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

                // remove submit button
                submitbutton.style.display = "none";
                window.wikisaysdumbbrowser = false;
            } else {
                document.getElementById("gwikiimgform_nofiledrag").style.display = 'none';
                document.getElementById("wikieditimg_dd_msg").innerHTML = "";
            }
        }

        // call initialization file
        window.wikisaysdumbbrowser = true; // set to false when proven otherwise
        if (window.File && window.FileList && window.FileReader) {
            Init();
        } else {
            document.getElementById("gwikiimgform_nofiledrag").style.display = 'none';
            document.getElementById("wikieditimg_dd_msg").innerHTML = "";
        }

    })();

    // attachment specific
    function fileOutput(msg) {
        var m = document.getElementById("wikieditfile_dd_msg");
        m.innerHTML = msg;
    }

    function fileClearOutput() {
        var m = document.getElementById("wikieditfile_dd_msg");
        if (window.wikisaysdumbbrowser == true) {
            m.innerHTML = '';
        }
        else {
            m.innerHTML = "<{$smarty.const._MD_GWIKI_FILES_DROPHERE}>";
        }
        m = document.getElementById("wikieditfile_progress");
        m.innerHTML = '';
    }

    function clearFileDetail() {
        document.getElementById("wikifile_id").value = '0';
        document.getElementById("wikieditfile_name").innerHTML = '';
        document.getElementById("wikieditfile_userlink").innerHTML = '';
        document.getElementById("wikieditfile_type").innerHTML = '';
        document.getElementById("wikieditfile_size").innerHTML = '';
        document.getElementById("wikieditfile_date").innerHTML = '';
        document.getElementById("wikieditfile_description").value = '';

        document.getElementById("wikieditfile_img").src = 'assets/images/blank.png';

        document.getElementById("wikieditfile_fileselect").value = '';
        document.getElementById("wikieditfile_progress").innerHTML = '';
        document.getElementById("wikieditfile_description").className = 'wikifield_input_reset';
        fileClearOutput();
    }

    function getFileListById(fileid) {

        clearFileDetail();

        document.getElementById("wikifile_id").value = window.fileList[fileid].file_id;
        document.getElementById("wikieditfile_name").innerHTML = window.fileList[fileid].file_name;
        document.getElementById("wikieditfile_userlink").innerHTML = window.fileList[fileid].userlink;
        document.getElementById("wikieditfile_type").innerHTML = window.fileList[fileid].file_type;
        document.getElementById("wikieditfile_size").innerHTML = window.fileList[fileid].size;
        document.getElementById("wikieditfile_date").innerHTML = window.fileList[fileid].date;
        document.getElementById("wikieditfile_description").value = window.fileList[fileid].file_description;
        document.getElementById("wikieditfile_img").src = window.fileList[fileid].iconlink;

    }

    function fetchFileList() {
        document.getElementById("wikifilelist").innerHTML = '<img id="loadanim" src="assets/images/loading-anim.gif" />';
        window.fileList = new Object;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var ajaxreturn = JSON.parse(xmlhttp.responseText);
                var cnt = 0;
                var body = '';
                for (i in ajaxreturn) {
                    ++cnt;
                    window.fileList[ajaxreturn[i].file_id] = ajaxreturn[i];
                    body = body + '<br><img src="' + ajaxreturn[i].iconlink + '" alt="' + ajaxreturn[i].file_icon + '" title="' + ajaxreturn[i].file_icon + '" onclick="getFileListById(\'' + ajaxreturn[i].file_id + '\');" /><br>';
                    body = body + ajaxreturn[i].file_name + '<br>';
                }
                if (cnt == 0) {
                    body = '<{$smarty.const._MD_GWIKI_FILES_EMPTY}>';
                }
                document.getElementById("wikifilelist").innerHTML = body + '<br>';
            }
        };

        xmlhttp.open("GET", "ajaxfilelist.php?page=<{$gwiki.keyword}>", true);
        xmlhttp.send();
    }

    function serializeFileDetail() {
        var file = new Object;

        file.page = document.getElementById("page").value;
        file.file_id = document.getElementById("wikifile_id").value;
        file.file_description = document.getElementById("wikieditfile_description").value;
        var jsonfile = JSON.stringify(file);
        return jsonfile;
    }

    function deleteFileDetail() {
        var file_id = document.getElementById("wikifile_id").value;
        if (file_id == 0) {
            alert("<{$smarty.const._MD_GWIKI_FILES_NO_SELECTION}>");
            return;
        }
        var doit = confirm("<{$smarty.const._MD_GWIKI_FILES_DELETE_CONFIRM}>");
        if (doit) {
            var deletereq = new Object;
            deletereq.page = document.getElementById("page").value;
            deletereq.file_id = document.getElementById("wikifile_id").value;
            deletereq.op = 'delete';
            var x_jsondata = JSON.stringify(deletereq);

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function (e) {
                if (xhr.readyState == 4) {
                    try {
                        var ajaxreturn = JSON.parse(xhr.responseText);
                        if (typeof(ajaxreturn.message) != 'undefined') {
                            clearFileDetail();
                            document.getElementById("wikieditfile_dd_msg").innerHTML = ajaxreturn.message;
                            fetchFileList();
                        }
                    }
                    catch (err) {
                        document.getElementById("wikieditfile_dd_msg").innerHTML = 'JSON response error';
                    }
                }
            };
            // send data
            xhr.open("POST", document.getElementById("wikieditfile_form").action, true);
            xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
            xhr.send();
        }
    }

    function newFileDetail() {
        clearFileDetail();
    }

    function updateFileDetail() {
        alert("To be completed");
    }

    // handles all the individual field changes
    function fileFieldEventHandler(event, field) {
        if (event == "focus") {
            document.getElementById(field).className = 'wikifield_input_reset';
            return;
        }

        var x_jsondata = serializeFileDetail();

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (e) {
            if (xhr.readyState == 4) {
                try {
                    var ajaxreturn = JSON.parse(xhr.responseText);
                    if (typeof(ajaxreturn.message) != 'undefined') {
                        fileOutput(ajaxreturn.message);
                    }
                }
                catch (err) {
                    fileOutput('JSON response error');
                }
            }
            document.getElementById(field).className = 'wikifield_input_ok';
            fetchFileList();
        };
        // send data
        xhr.open("POST", document.getElementById("wikieditfile_form").action, true);
        xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
        xhr.send();
    }

    /*
     taken from
     filedrag.js - HTML5 File Drag & Drop demonstration
     Featured on SitePoint.com
     Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
     */

    (function () {

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
            fileClearOutput();
            fileOutput(
                    "<p><strong>" + file.name +
                    "</strong> : <strong>" + file.type +
                    "</strong> : <strong>" + file.size +
                    "</strong> bytes</p>"
            );

            // display an image
//      if (file.type.indexOf("image") == 0) {
//          var reader = new FileReader();
//          reader.onload = function(e) {
//              var i = document.getElementById('wikieditfile_img');
//              i.src=e.target.result;
//              Output(file.name);
//          }
//          reader.readAsDataURL(file);
//      }

        }

        // upload JPEG files
        function UploadFile(file) {
            // create progress bar
            var o = document.getElementById("wikieditfile_progress");
            var progress = o.appendChild(document.createElement("p"));
            progress.appendChild(document.createTextNode(file.name));

            if (file.size > document.getElementById("MAX_FILE_SIZE").value) {
                progress.className = "toobig";
                return;
            }
            var x_jsondata = serializeFileDetail();

            var xhr = new XMLHttpRequest();
            if (xhr.upload) { // && (file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/gif")) {

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
                            if (typeof(ajaxreturn.file_id) != 'undefined') {
                                document.getElementById("wikifile_id").value = ajaxreturn.file_id;

                                document.getElementById("wikieditfile_name").innerHTML = ajaxreturn.file_name;
                                document.getElementById("wikieditfile_userlink").innerHTML = ajaxreturn.userlink;
                                document.getElementById("wikieditfile_type").innerHTML = ajaxreturn.file_type;
                                document.getElementById("wikieditfile_size").innerHTML = ajaxreturn.size;
                                document.getElementById("wikieditfile_date").innerHTML = ajaxreturn.date;
                                document.getElementById("wikieditfile_description").value = ajaxreturn.file_description;
                                document.getElementById("wikieditfile_img").src = ajaxreturn.iconlink;
                                fetchFileList();
                            }
                            if (typeof(ajaxreturn.message) != 'undefined') {
                                fileOutput(ajaxreturn.message);
                            }
                        }
                        catch (err) {
                            fileOutput('JSON response error');
                        }
                    }
                };

                // start upload
                xhr.open("POST", document.getElementById("wikieditfile_form").action, true);
                xhr.setRequestHeader("GW-FILENAME", file.name);
                xhr.setRequestHeader("GW-JSONDATA", x_jsondata);
                xhr.send(file);

            }

        }


        // initialize
        function Init() {

            var fileselect = document.getElementById("wikieditfile_fileselect"),
                    filedrag = document.getElementById("wikieditfile_dd"),
                    submitbutton = document.getElementById("wikieditfile_submitbutton");

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

                // remove submit button
                submitbutton.style.display = "none";
                window.wikisaysdumbbrowser = false;
            } else {
                document.getElementById("wikifileform_nofiledrag").style.display = 'none';
                document.getElementById("wikieditfile_dd_msg").innerHTML = "";
            }
        }

        // call initialization file
        window.wikisaysdumbbrowser = true; // set to false when proven otherwise
        if (window.File && window.FileList && window.FileReader) {
            Init();
        } else {
            document.getElementById("wikifileform_nofiledrag").style.display = 'none';
            document.getElementById("wikieditfile_dd_msg").innerHTML = "";
        }

    })();

    // support for warning on exit with unsaved changes
    if (document.getElementById("pagechanged").value == "yes") { // set in php
        setWikiChanged();
    }

    // ok to exit via submit or preview
    function prepForSubmit() {
        window.wikiChanged = false;
    }

    function prepForPreview() {
        window.wikiChanged = false;
        document.forms.gwikiform.op.value = "preview";
        document.forms.gwikiform.action = document.forms.gwikiform.action + "#wikipage";
        document.forms.gwikiform.submit.click();
    }

    function setWikiChanged() {
        window.wikiChanged = true;
    }

    function confirmExitEdit(e) {
        if (window.wikiChanged) {
            var confirmationMessage = "<{$smarty.const._MD_GWIKI_PAGENOTSAVED}>";
            (e || window.event).returnValue = confirmationMessage;     //Gecko + IE
            return confirmationMessage;            //Webkit, Safari, Chrome etc.
        }
    }

    if (window.addEventListener) {
        window.addEventListener("beforeunload", confirmExitEdit);
    } else {
        window.attachEvent("onbeforeunload", confirmExitEdit);
    }

    // start IE workarounds for selections, line endings, caret positioning
    // these are only used for IE8-
    function saveSelectionOnblur(e) {
        var s = getInputSelection(e);
        window.SavedSelection = {};

        window.SavedSelection.start = s.start;
        window.SavedSelection.end = s.end;
        window.SavedSelection.sel = s.val;
    }

    function restoreSavedSelection(e) {
        e.focus();
        setSelection(e, window.SavedSelection.start, window.SavedSelection.end);
    }

    // use onbeforedeactivate to capture current selection, otherwise the
    // selection will be lost when the toolbar is used
    function setEditOnblur() {
        e = document.getElementById("editfield");
        if (e.selectionStart || e.selectionStart == '0') {
            return; // this is only for older IE
        }
        e.onbeforedeactivate = function () {
            saveSelectionOnblur(e);
        };
        b = document.getElementById("body");
        b.onbeforedeactivate = function () {
            saveSelectionOnblur(b);
        };
    }

    setEditOnblur();

    // adapted from code by Tim Down on stackoverflow.com
    function getInputSelection(el) {
        var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;

        range = document.selection.createRange();

        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }

        return {
            start: start,
            end: end,
            val: range.text
        };
    }

    function offsetToRangeCharacterMove(el, offset) {
        return offset - (el.value.slice(0, offset).split("\r\n").length - 1);
    }

    function setSelection(el, startOffset, endOffset) {
        var range = el.createTextRange();
        var startCharMove = offsetToRangeCharacterMove(el, startOffset);
        range.collapse(true);
        if (startOffset == endOffset) {
            range.move("character", startCharMove);
        } else {
            range.moveEnd("character", offsetToRangeCharacterMove(el, endOffset));
            range.moveStart("character", startCharMove);
        }
        range.select();
    }
    // end IE workarounds
</script>
