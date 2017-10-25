<?php
/**
* @version   $Id: default.php 188 2014-07-22 15:36:04Z edo888 $
* @package   GTranslate
* @copyright Copyright (C) 2008-2017 Edvard Ananyan. All rights reserved.
* @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

include 'native_names_map.php';

if($pro_version or $enterprise_version)
    $method = 'standard';
else
    $method = 'onfly';

$lang_array = array('en'=>'English','ar'=>'Arabic','bg'=>'Bulgarian','zh-CN'=>'Chinese (Simplified)','zh-TW'=>'Chinese (Traditional)','hr'=>'Croatian','cs'=>'Czech','da'=>'Danish','nl'=>'Dutch','fi'=>'Finnish','fr'=>'French','de'=>'German','el'=>'Greek','hi'=>'Hindi','it'=>'Italian','ja'=>'Japanese','ko'=>'Korean','no'=>'Norwegian','pl'=>'Polish','pt'=>'Portuguese','ro'=>'Romanian','ru'=>'Russian','es'=>'Spanish','sv'=>'Swedish','ca'=>'Catalan','tl'=>'Filipino','iw'=>'Hebrew','id'=>'Indonesian','lv'=>'Latvian','lt'=>'Lithuanian','sr'=>'Serbian','sk'=>'Slovak','sl'=>'Slovenian','uk'=>'Ukrainian','vi'=>'Vietnamese','sq'=>'Albanian','et'=>'Estonian','gl'=>'Galician','hu'=>'Hungarian','mt'=>'Maltese','th'=>'Thai','tr'=>'Turkish','fa'=>'Persian','af'=>'Afrikaans','ms'=>'Malay','sw'=>'Swahili','ga'=>'Irish','cy'=>'Welsh','be'=>'Belarusian','is'=>'Icelandic','mk'=>'Macedonian','yi'=>'Yiddish','hy'=>'Armenian','az'=>'Azerbaijani','eu'=>'Basque','ka'=>'Georgian','ht'=>'Haitian Creole','ur'=>'Urdu','bn' => 'Bengali','bs' => 'Bosnian','ceb' => 'Cebuano','eo' => 'Esperanto','gu' => 'Gujarati','ha' => 'Hausa','hmn' => 'Hmong','ig' => 'Igbo','jw' => 'Javanese','kn' => 'Kannada','km' => 'Khmer','lo' => 'Lao','la' => 'Latin','mi' => 'Maori','mr' => 'Marathi','mn' => 'Mongolian','ne' => 'Nepali','pa' => 'Punjabi','so' => 'Somali','ta' => 'Tamil','te' => 'Telugu','yo' => 'Yoruba','zu' => 'Zulu','my' => 'Myanmar (Burmese)','ny' => 'Chichewa','kk' => 'Kazakh','mg' => 'Malagasy','ml' => 'Malayalam','si' => 'Sinhala','st' => 'Sesotho','su' => 'Sudanese','tg' => 'Tajik','uz' => 'Uzbek','am' => 'Amharic','co' => 'Corsican','haw' => 'Hawaiian','ku' => 'Kurdish (Kurmanji)','ky' => 'Kyrgyz','lb' => 'Luxembourgish','ps' => 'Pashto','sm' => 'Samoan','gd' => 'Scottish Gaelic','sn' => 'Shona','sd' => 'Sindhi','fy' => 'Frisian','xh' => 'Xhosa');
$flag_map = array();
$i = $j = 0;
foreach($lang_array as $lang => $lang_name) {
    $flag_map[$lang] = array($i*100, $j*100);
    if($i == 7) {
        $i = 0;
        $j++;
    } else {
        $i++;
    }
}

$flag_map_vertical = array();
$i = 0;
foreach($lang_array as $lang => $lang_name) {
    $flag_map_vertical[$lang] = $i*16;
    $i++;
}

asort($lang_array);
// Move the default language to the first position
$lang_array = array_merge(array($language => $lang_array[$language]), $lang_array);

$request_uri = $_SERVER['REQUEST_URI'];

if(!defined('GTRANSLATE_INCLUDED')) {
    define('GTRANSLATE_INCLUDED', 1);
    //echo '<noscript>Javascript is required to use <a href="http://gtranslate.net/">GTranslate</a> <a href="http://gtranslate.net/">multilingual website</a> and <a href="http://gtranslate.net/">translation delivery network</a></noscript>';
?>

<?php if($method == 'standard' or $method == 'ajax'): ?>
<script type="text/javascript">
/* <![CDATA[ */
<?php if($new_tab): ?>
    function openTab(url) {var form=document.createElement('form');form.method='post';form.action=url;form.target='_blank';document.body.appendChild(form);form.submit();}
    <?php if($pro_version): ?>
    function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];<?php if($analytics): ?>if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent', 'GTranslate', lang, location.pathname+location.search]);}else {if(typeof ga!='undefined')ga('send', 'event', 'GTranslate', lang, location.pathname+location.search);}<?php endif; ?>var plang=location.pathname.split('/')[1];if(plang.length !=2 && plang != 'zh-CN' && plang != 'zh-TW')plang='<?php echo $language; ?>';openTab(location.protocol+'//'+location.host+'/'+lang+'<?php echo $request_uri; ?>');}
    <?php elseif($enterprise_version): ?>
    function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];<?php if($analytics): ?>if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent', 'GTranslate', lang, location.hostname+location.pathname+location.search]);}else {if(typeof ga!='undefined')ga('send', 'event', 'GTranslate', lang, location.hostname+location.pathname+location.search);}<?php endif; ?>var plang=location.hostname.split('.')[0];if(plang.length !=2 && plang.toLowerCase() != 'zh-cn' && plang.toLowerCase() != 'zh-tw')plang='<?php echo $language; ?>';openTab(location.protocol+'//'+(lang == '<?php echo $language; ?>' ? '' : lang+'.')+location.hostname.replace('www.', '').replace(RegExp("^" + plang + '\\.'), '')+'<?php echo $request_uri; ?>');}
    <?php endif; ?>
<?php else: ?>
    <?php if($pro_version): ?>
    function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];<?php if($analytics): ?>if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent', 'GTranslate', lang, location.pathname+location.search]);}else {if(typeof ga!='undefined')ga('send', 'event', 'GTranslate', lang, location.pathname+location.search);}<?php endif; ?>var plang=location.pathname.split('/')[1];if(plang.length !=2 && plang != 'zh-CN' && plang != 'zh-TW')plang='<?php echo $language; ?>';location.href=location.protocol+'//'+location.host+'/'+lang+'<?php echo $request_uri; ?>';}
    <?php elseif($enterprise_version): ?>
    function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];<?php if($analytics): ?>if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent', 'GTranslate', lang, location.hostname+location.pathname+location.search]);}else {if(typeof ga!='undefined')ga('send', 'event', 'GTranslate', lang, location.hostname+location.pathname+location.search);}<?php endif; ?>var plang=location.hostname.split('.')[0];if(plang.length !=2 && plang.toLowerCase() != 'zh-cn' && plang.toLowerCase() != 'zh-tw')plang='<?php echo $language; ?>';location.href=location.protocol+'//'+(lang == '<?php echo $language; ?>' ? '' : lang+'.')+location.hostname.replace('www.', '').replace(RegExp("^" + plang + '\\.'), '')+'<?php echo $request_uri; ?>';}
    <?php endif; ?>
<?php endif; ?>
/* ]]> */
</script>
<?php endif; ?>

<?php if($method == 'onfly'): ?>
<script type="text/javascript">
/* <![CDATA[ */
function GTranslateGetCurrentLang() {var keyValue = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');return keyValue ? keyValue[2].split('/')[2] : null;}
function GTranslateFireEvent(element,event){try{if(document.createEventObject){var evt=document.createEventObject();element.fireEvent('on'+event,evt)}else{var evt=document.createEvent('HTMLEvents');evt.initEvent(event,true,true);element.dispatchEvent(evt)}}catch(e){}}
function doGTranslate(lang_pair){if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];if(GTranslateGetCurrentLang() == null && lang == lang_pair.split('|')[0])return;<?php if($analytics): ?>if(typeof ga!='undefined'){ga('send', 'event', 'GTranslate', lang, location.hostname+location.pathname+location.search);}else{if(typeof _gaq!='undefined')_gaq.push(['_trackEvent', 'GTranslate', lang, location.hostname+location.pathname+location.search]);}<?php endif; ?>var teCombo;var sel=document.getElementsByTagName('select');for(var i=0;i<sel.length;i++)if(sel[i].className=='goog-te-combo')teCombo=sel[i];if(document.getElementById('google_translate_element2')==null||document.getElementById('google_translate_element2').innerHTML.length==0||teCombo.length==0||teCombo.innerHTML.length==0){setTimeout(function(){doGTranslate(lang_pair)},500)}else{teCombo.value=lang;GTranslateFireEvent(teCombo,'change');GTranslateFireEvent(teCombo,'change')}}
<?php if($look == 'dropdown_with_flags'): ?>
if(GTranslateGetCurrentLang() != null)jQuery(document).ready(function() {jQuery('div.switcher div.selected a').html(jQuery('div.switcher div.option').find('img[alt="'+GTranslateGetCurrentLang()+'"]').parent().html());});
<?php endif; ?>
/* ]]> */
</script>
<?php
$document = JFactory::getDocument();
$document->addStyleDeclaration("
#goog-gt-tt {display:none !important;}
.goog-te-banner-frame {display:none !important;}
.goog-te-menu-value:hover {text-decoration:none !important;}
body {top:0 !important;}
#google_translate_element2 {display:none!important;}
");
?>
<div id="google_translate_element2"></div>
<script type="text/javascript">function googleTranslateElementInit2() {new google.translate.TranslateElement({pageLanguage: '<?php echo $language; ?>', autoDisplay: false}, 'google_translate_element2');}</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
<?php endif; ?>

<?php
    $document = JFactory::getDocument();
    $document->addStyleDeclaration("
        a.flag {text-decoration:none;}
        a.flag img {vertical-align:middle;padding:0;margin:0;border:0;display:inline;height:{$flag_size}px;opacity:0.8;}
        a.flag:hover img {opacity:1;}
        a.flag span {margin-right:5px;font-size:15px;vertical-align:middle;}
    ");
}

if($look == 'flags' or $look == 'flags_name' or $look == 'flags_code' or $look == 'lang_names' or $look == 'lang_codes') {
    $session = JFactory::getSession();
    $uri = JURI::getInstance();
    foreach($lang_array as $lang => $lang_name) {
        if($pro_version)
            $href = ($language == $lang) ? $uri->toString() : '/' . $lang . str_replace('/' . $session->get('glang', $language) . '/', '/', $uri->toString(array('path', 'query')));
        elseif($enterprise_version)
            $href = ($language == $lang) ? $uri->toString() : $uri->getScheme() . '://' . $lang . '.' . str_replace('www.', '', $uri->toString(array('host', 'path', 'query')));
        else
            $href = '#';

        if($native_language_names)
            $lang_name = $native_names_map[$lang];

        $show_this = 'show_'.str_replace('-', '', $lang);
        if($$show_this) {
            if($lang == 'en' and $$show_this == '3')
                $flag = 'en-us';
            elseif($lang == 'en' and $$show_this == '4')
                $flag = 'en-ca';
            elseif($lang == 'pt' and $$show_this == '3')
                $flag = 'pt-br';
            elseif($lang == 'es' and $$show_this == '3')
                $flag = 'es-mx';
            elseif($lang == 'fr' and $$show_this == '3')
                $flag = 'fr-qc';
            else
                $flag = $lang;

            if($look == 'flags')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/'.$flag_size.'/'.$flag.'.png" height="'.$flag_size.'" width="'.$flag_size.'" alt="'.$lang.'" /></a>';
            elseif($look == 'flags_name')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/'.$flag_size.'/'.$flag.'.png" height="'.$flag_size.'" width="'.$flag_size.'" alt="'.$lang.'" /> <span>'.$lang_name.'</span></a> ';
            elseif($look == 'flags_code')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/'.$flag_size.'/'.$flag.'.png" height="'.$flag_size.'" width="'.$flag_size.'" alt="'.$lang.'" /> <span>'.strtoupper($lang).'</span></a> ';
            elseif($look == 'lang_names')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate">'.$lang_name.'</a> ';
            elseif($look == 'lang_codes')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate">'.strtoupper($lang).'</a> ';

        }
    }
} elseif ($look == 'dropdown') {
    echo '<select onchange="doGTranslate(this);" class="notranslate">';
    echo '<option value="">Select Language</option>';
    $i = 0;
    foreach($lang_array as $lang => $lang_name) {
        $show_this = 'show_'.str_replace('-', '', $lang);

        if($native_language_names)
            $lang_name = $native_names_map[$lang];

        if($$show_this)
            echo '<option value="'.$language.'|'.$lang.'" style="'.($lang == $language ? 'font-weight:bold;' : '').'">'.$lang_name.'</option>';
    }
    echo '</select>';
} elseif ($look == 'both') {
    $session = JFactory::getSession();
    $uri = JURI::getInstance();
    foreach($lang_array as $lang => $lang_name) {
        if($pro_version)
            $href = ($language == $lang) ? $uri->toString() : '/' . $lang . str_replace('/' . $session->get('glang', $language) . '/', '/', $uri->toString(array('path', 'query')));
        elseif($enterprise_version)
            $href = ($language == $lang) ? $uri->toString() : $uri->getScheme() . '://' . $lang . '.' . str_replace('www.', '', $uri->toString(array('host', 'path', 'query')));
        else
            $href = '#';

        if($native_language_names)
            $lang_name = $native_names_map[$lang];

        $show_this = 'show_'.str_replace('-', '', $lang);
        if($$show_this) {
            if($lang == 'en' and $$show_this == '3')
                $flag = 'en-us';
            elseif($lang == 'en' and $$show_this == '4')
                $flag = 'en-ca';
            elseif($lang == 'pt' and $$show_this == '3')
                $flag = 'pt-br';
            elseif($lang == 'es' and $$show_this == '3')
                $flag = 'es-mx';
            elseif($lang == 'fr' and $$show_this == '3')
                $flag = 'fr-qc';
            else
                $flag = $lang;

            if($$show_this != '1' and $$show_this != '0')
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');return false;" title="'.$lang_name.'" class="flag nturl notranslate"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/'.$flag_size.'/'.$flag.'.png" height="'.$flag_size.'" width="'.$flag_size.'" alt="'.$lang.'" /></a>';
        }
    }
    echo '<br/><select onchange="doGTranslate(this);" class="notranslate">';
    echo '<option value="">Select Language</option>';
    foreach($lang_array as $lang => $lang_name) {
        if($native_language_names)
            $lang_name = $native_names_map[$lang];

        $show_this = 'show_'.str_replace('-', '', $lang);
        if($$show_this)
            echo '<option '.($lang == $language ? 'style="font-weight:bold;"' : '').' value="'.$language.'|'.$lang.'">'.$lang_name.'</option>';
    }
    echo '</select>';
} elseif ($look == 'dropdown_with_flags') {

    JHtml::_('jquery.framework');

    $current_language = isset($_SERVER['HTTP_X_GT_LANG']) ? str_replace(array('zh-cn', 'zh-tw'), array('zh-CN', 'zh-TW'), $_SERVER['HTTP_X_GT_LANG']) : $language;

    if($native_language_names)
        $lang_name = $native_names_map[$current_language];
    else
        $lang_name = $lang_array[$current_language];

    if($current_language == 'en' and $show_en == '3')
        $flag = 'en-us';
    elseif($current_language == 'en' and $show_en == '4')
        $flag = 'en-ca';
    elseif($current_language == 'pt' and $show_pt == '3')
        $flag = 'pt-br';
    elseif($current_language == 'es' and $show_es == '3')
        $flag = 'es-mx';
    elseif($current_language == 'fr' and $show_fr == '3')
        $flag = 'fr-qc';
    else
        $flag = $current_language;

    echo '<div class="switcher notranslate">';
    echo '<div class="selected">';
    echo '<a href="#" onclick="return false;"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/16/'.$flag.'.png" height="16" width="16" alt="'.$lang_array[$current_language].'" /> '.$lang_name.'</a>';
    echo '</div>';
    echo '<div class="option">';

    $session = JFactory::getSession();
    $uri = JURI::getInstance();

    foreach($lang_array as $lang => $lang_name) {
        if($pro_version)
            $href = ($language == $lang) ? $uri->toString() : '/' . $lang . str_replace('/' . $session->get('glang', $language) . '/', '/', $uri->toString(array('path', 'query')));
        elseif($enterprise_version)
            $href = ($language == $lang) ? $uri->toString() : $uri->getScheme() . '://' . $lang . '.' . str_replace('www.', '', $uri->toString(array('host', 'path', 'query')));
        else
            $href = '#';

        if($native_language_names)
            $lang_name = $native_names_map[$lang];

        $show_this = 'show_'.str_replace('-', '', $lang);
        if($$show_this) {
            if($lang == 'en' and $$show_this == '3')
                $flag = 'en-us';
            elseif($lang == 'en' and $$show_this == '4')
                $flag = 'en-ca';
            elseif($lang == 'pt' and $$show_this == '3')
                $flag = 'pt-br';
            elseif($lang == 'es' and $$show_this == '3')
                $flag = 'es-mx';
            elseif($lang == 'fr' and $$show_this == '3')
                $flag = 'fr-qc';
            else
                $flag = $lang;

            if($$show_this)
                echo '<a href="'.$href.'" onclick="doGTranslate(\''.$language.'|'.$lang.'\');jQuery(\'div.switcher div.selected a\').html(jQuery(this).html());return false;" title="'.$lang_name.'" class="nturl '.($current_language == $lang ? ' selected' : '').'"><img src="'.JURI::root(true).'/modules/mod_gtranslate/tmpl/lang/16/'.$flag.'.png" height="16" width="16" alt="'.$lang.'" /> '.$lang_name.'</a>';
        }
    }

    echo '</div></div>';

    $document = JFactory::getDocument();

    // Adding slider javascript
    $document->addScriptDeclaration("
        jQuery(document).ready(function() {
            jQuery('.switcher .selected').click(function() {if(!(jQuery('.switcher .option').is(':visible'))) {jQuery('.switcher .option').stop(true,true).delay(100).slideDown(500);jQuery('.switcher .selected a').toggleClass('open')}});
            jQuery('.switcher .option').bind('mousewheel', function(e) {var options = jQuery('.switcher .option');if(options.is(':visible'))options.scrollTop(options.scrollTop() - e.originalEvent.wheelDelta);return false;});
            jQuery('body').not('.switcher').mousedown(function(e) {if(jQuery('.switcher .option').is(':visible') && e.target != jQuery('.switcher .option').get(0)) {jQuery('.switcher .option').stop(true,true).delay(100).slideUp(500);jQuery('.switcher .selected a').toggleClass('open')}});
        });
    ");

    // Adding slider css
    $module_url = JURI::root(true).'/modules/mod_gtranslate/tmpl/lang';
    $document->addStyleDeclaration("
        .switcher {font-family:Arial;font-size:10pt;text-align:left;cursor:pointer;overflow:hidden;width:163px;line-height:17px;}
        .switcher a {text-decoration:none;display:block;font-size:10pt;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;}
        .switcher a img {vertical-align:middle;display:inline;border:0;padding:0;margin:0;opacity:0.8;}
        .switcher a:hover img {opacity:1;}
        .switcher .selected {background:#FFFFFF url($module_url/switcher.png) repeat-x;position:relative;z-index:9999;}
        .switcher .selected a {border:1px solid #CCCCCC;background:url($module_url/arrow_down.png) 146px center no-repeat;color:#666666;padding:3px 5px;width:151px;}
        .switcher .selected a.open {background-image:url($module_url/arrow_up.png)}
        .switcher .selected a:hover {background:#F0F0F0 url($module_url/arrow_down.png) 146px center no-repeat;}
        .switcher .option {position:relative;z-index:9998;border-left:1px solid #CCCCCC;border-right:1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;background-color:#EEEEEE;display:none;width:161px;max-height:198px;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;overflow-y:auto;overflow-x:hidden;}
        .switcher .option a {color:#000;padding:3px 5px;}
        .switcher .option a:hover {background:#FFC;}
        .switcher .option a.selected {background:#FFC;}
        #selected_lang_name {float: none;}
        .l_name {float: none !important;margin: 0;}
        .switcher .option::-webkit-scrollbar-track{-webkit-box-shadow:inset 0 0 3px rgba(0,0,0,0.3);border-radius:5px;background-color:#F5F5F5;}
        .switcher .option::-webkit-scrollbar {width:5px;}
        .switcher .option::-webkit-scrollbar-thumb {border-radius:5px;-webkit-box-shadow: inset 0 0 3px rgba(0,0,0,.3);background-color:#888;}
    ");

}
?>