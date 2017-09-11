<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$html = "";
$document = JFactory::getDocument();
$db		  = JFactory::getDBO();
$helper	  = new modAccordionfaqHelper();

$cssfile = "modules/mod_accordionfaq/css/accordionfaq.css";
$faqlinks = $params->get("faqlinks","");
if (strtolower($faqlinks) == "space")
{
	$faqlinks = "&nbsp;";
}
$faqid = $params->get("faqid","accordion1");
$faqclass = $params->get("faqclass","lightnessfaq defaulticon headerbackground headerborder contentbackground contentborder round5");
$header = $params->get("header","h3");
if (! $helper->editTrueFalse( "autoheight", $params, "0", $autoheight, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "autonumber", $params, "0", $autonumber, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "alwaysopen", $params, "0", $alwaysopen, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "openmultiple", $params, "0", $openmultiple, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "scrollonopen", $params, "0", $scrollonopen, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "warnings", $params, "1", $warnings, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "usedynamiccssload", $params, "1", $usedynamiccssload, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
if (! $helper->editTrueFalse( "keyaccess", $params, "1", $keyaccess, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}

$event = $params->get('event',"click");
$animation = $params->get("animation","none");
$active = $params->get("active","");
$faqline = "Accordionfaq module: faqid=".$faqid." ";
if (! $helper->editNumeric( "scrolltime", $params, 1000, $scrolltime, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}
$scrolltime = max( $scrolltime, 1000);
if (! $helper->editNumeric( "scrolloffset", $params, 0, $scrolloffset, $html))
{
	echo $helper->formattedError( $html, $faqline );
	return;
}

$activearr[0] = "false";
$faqitem = JRequest::getString( 'faqitem', '' );

if ( $active != "" && $faqitem == "")
{
	$activearr = explode(',',$active );
	for( $i = 0; $i < count($activearr); $i++)
	{
		$activearr[$i] = JString::trim( $activearr[$i]);
		if (! is_numeric($activearr[$i]))
		{
			$activearr[$i] = "'#".$activearr[$i]."'";
		}
	}
}
if ($faqitem != "")
{
	$faqitems = explode(',', $faqitem);
	$i = 0;
	do
	{
		$faqitems[$i] = JString::trim( $faqitems[$i] );
		if (JString::strpos( $faqitems[$i], $faqid ) === 0)
		{
			$faqitemtarget = JString::substr( $faqitems[$i], JString::strlen( $faqid ));
			if ($faqitemtarget != "")
			{
				if (is_numeric($faqitemtarget))
				{
					$faqitemid = (int)$faqitemtarget;
					$activearr[$i] = $faqitemid;
					$jumpto = $faqitems[$i];
				}
				else
				{
					$activearr[$i] = "'#".$faqitemtarget."'";
					$jumpto = $faqitemtarget;
				}
			}
			else
			{
				$jumpto = $faqitem;
			}
		}
		$i++;
		}
	while(! isset($jumpto) && $i < count($faqitems) );
}

if ($animation == "")
{
	$animation = "none";
}
if ($animation == "none")
{
	$animation = "false";
}
else
{
	$animation = "'".$animation."'";
}
$printfaq = JRequest::getString( "print", 'false');
if ($printfaq == "1")
{
	if (isset( $jumpto ))
	{
		unset( $jumpto );
	}
	$animation = 'false';
	$printfaq = 'true';
}
else
{
	$printfaq = 'false';
}
if ($event == "")
{
	$event = "click";
}

jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');
$browser	= JBrowser::getInstance();
$isIE6		= false;

if ($browser->getBrowser() == "msie" && $browser->getMajor() <= 6)
{
	$isIE6 = true;
	$ie6css = JPATH_BASE . DIRECTORY_SEPARATOR . $cssfile;
	$ie6css = JPath::clean( $ie6css, DIRECTORY_SEPARATOR );
	if (JString::substr($ie6css, JString::strlen( $ie6css ) - 4, 4) == '.css')
	{
		$ie6css = JString::substr_replace($ie6css, '-ie6.css', JString::strlen( $ie6css ) - 4, 4 );
	}
	if (JFile::exists($ie6css))
	{
		$styledata = JFile::read($ie6css);
		$newtext = preg_replace( "/src=( )*'( )*([^' ]+)'/i", "src='" . JURI::root(true) . "\\3" . "'", $styledata );
		$newtext = preg_replace( "/url\(( )*'( )*([^' ]+)'/i", "url('" . JURI::root(true) . "\\3" . "'", $newtext );
		$document->addStyleDeclaration($newtext);
	}
}
$document->addStyleSheet( JURI::root(true)."/".$cssfile );

$cssbase = JPATH_BASE . DIRECTORY_SEPARATOR . $cssfile;
$cssbase = JPath::clean( $cssbase, DIRECTORY_SEPARATOR );
$cssfilename = JFile::getName( $cssbase );
$cssbase = str_replace( $cssfilename, '', $cssbase );
$faqbase = str_replace( $cssfilename, '', $cssfile );
$faqclassarray = preg_split( "/[\s]+/", $faqclass );
for($i = 0; $i < count($faqclassarray); $i++)
{
	if (preg_match("/(.*)faq$/i", $faqclassarray[$i], $match ))
	{
		$faqfile = $cssbase.$faqclassarray[$i].".css";
		if (JFile::exists($faqfile))
		{
			if ($usedynamiccssload == 'tru')
			{
				$document->addStyleSheet( JURI::root(true)."/".$faqbase."css.php?id=".$faqid."&amp;faq=".$faqclassarray[$i] );
			}
			else
			{
				$document->addStyleSheet( JURI::root(true)."/".$faqbase.$faqclassarray[$i].".css" );
			}
			if ($isIE6)
			{
				$ie6css = $cssbase.$faqclassarray[$i]."-ie6.css";
				if (JFile::exists($ie6css))
				{
					$styledata = JFile::read($ie6css);
					$newtext = preg_replace( "/src=( )*'( )*([^' ]+)'/i", "src='" . JURI::root(true) . "\\3" . "'", $styledata );
					$newtext = preg_replace( "/url\(( )*'( )*([^' ]+)'/i", "url('" . JURI::root(true) . "\\3" . "'", $newtext );
					$newtext = preg_replace( "/\.".$faqclassarray[$i]."/", "#".$faqid.".".$faqclassarray[$i], $newtext );
					$document->addStyleDeclaration($newtext);
				}
			}
		}
		else
		{
			if ($warnings == 'tru')
			{
				$warntext = "WARNING: CSS file for faqclass ".$faqclassarray[$i]." does not exist (".$faqfile.").";
				$html .= $helper->formattedError( $warntext, $faqline );
			}
		}
	}
	else
	{
		if ($warnings == 'tru')
		{
			if (! $helper->editFaqClass( $faqclassarray[$i], $warntext))
			{
				$html .= $helper->formattedError( $warntext, $faqline );
			}
		}
	}
}

$includejquery = $params->get('includejquery', 1);
if ($includejquery != 0)
{
	$jquerynoconflict = $params->get('jquerynoconflict', 1);
	if ($jquerynoconflict == 1)
	{
		JHTML::_('jquery.framework' );
	}
	else
	{
		JHTML::_('jquery.framework', false );
	}
}

JHTML::_('script', 'modules/mod_accordionfaq/js/preparefaq.js' );
if ($openmultiple == 'tru' && $animation !== 'false')
{
	$duration = 300;
	$easing = 'swing';
	if ($animation == "'slide'")
	{
		$duration = 300;
		$easing = 'swing';
	}
	else
	if ($animation == "'easeslide'")
	{
		$duration = 700;
		$easing = 'easeinout';
	}
	else
	if ($animation == "'bounceslide'")
	{
		$duration = 1000;
		$easing = 'bounceout';
	}
}

$script  = "// <!--\n";
$script .= "preparefaq.onFunctionAvailable( 'jQuery', 300, function() {\n";
$script .= "	preparefaq.setjQuery();\n";
if ($printfaq === 'false' && $openmultiple == 'fals')
{
	$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/modules/mod_accordionfaq/js/jquery.accordionfaq.js' );\n";
}
if ($animation !== "false")
{
	$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/modules/mod_accordionfaq/js/jquery.easing.js' );\n";
}
$script .= "/***********************************************\n";
$script .= "* Scrolling HTML bookmarks- © Dynamic Drive DHTML code library (www.dynamicdrive.com)\n";
$script .= "* This notice MUST stay intact for legal use\n";
$script .= "* Visit Project Page at http://www.dynamicdrive.com for full source code\n";
$script .= "***********************************************/\n";
$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/modules/mod_accordionfaq/js/bookmarkscroll.js' );\n";
$script .= "	preparefaq.getjQuery()(document).ready(function(){ \n";
$script .= "		preparefaq.exec( { \n";
$script .= "		    id: '".$faqid."'\n";
$script .= "		  , header: '".$header."'\n";
$script .= "		  , alwaysopen: ".$alwaysopen."e\n";
$script .= "		  , autonumber: ".$autonumber."e\n";
$script .= "		  , keyaccess: ".$keyaccess."e\n";
$script .= "		  , print: ".$printfaq."\n";
$script .= "		  , scrolltime: ".$scrolltime."\n";
$script .= "		  , scrolloffset: ".$scrolloffset."\n";
if ($faqlinks != "")
{
	$script .= "		  , faqlinks: '".$faqlinks."'\n";
}
$script .= "		  , scrollonopen: ".$scrollonopen."e\n";
if ($openmultiple == 'tru')
{
	$script .= "		  , event: '".$event."'\n";
	$script .= "		  , onevent: function() { \n";
	if ($animation === 'false')
	{
		$script .= "				preparefaq.getjQuery()(this).toggleClass('selected').next().toggle( 1, preparefaq.accordionChange );\n";
	}
	else
	{
		$script .= "				preparefaq.getjQuery()(this).toggleClass('selected').next().slideToggle( ".$duration.", '".$easing."', preparefaq.accordionChange );\n";
	}
	$script .= "				return true;\n";
	$script .= "			}\n";
}
$script .= "		} );\n";
$script .= "		preparefaq.onFunctionAvailable( 'bookmarkscroll.init', 300, function() {\n";
$script .= "				bookmarkscroll.init();\n";
$script .= "		});\n";
if ($openmultiple == 'tru')
{
	$script .= "		preparefaq.getjQuery()('#".$faqid."').addClass('selected');\n";
	if ($activearr[0] !== 'false' && $printfaq === 'false')
	{
		if (count($activearr) == 1 && $activearr[0] == "'#*'")
		{
			$script .= "		preparefaq.getjQuery()('".$header.".accordionfaqheader.".$faqid."').toggleClass('selected').next().toggle();\n";
		}
		else
		{
			$script .= "		var target;\n";
			for ( $i = 0; $i < count($activearr); $i++)
			{
				if (is_numeric($activearr[$i]))
				{
					$script .= "		target = preparefaq.getjQuery()('#".$faqid.$activearr[$i]."');\n";
				}
				else
				{
					$activeval = str_replace( "'", '', $activearr[$i] );
					$script .= "		target = preparefaq.getjQuery()('".$activeval."');\n";
				}
				$script .= "		if (typeof(target) !== 'undefined') {\n";
				$script .= "			target.toggleClass('selected').next().toggle();\n";
				$script .= "		};\n";
			}
		}
	}
}
else
{
	$script .= "		preparefaq.onFunctionAvailable( 'preparefaq.getjQuery().fn.accordionfaq', 300, function() {\n";
	$script .= "			preparefaq.getjQuery()('#".$faqid."').accordionfaq( { \n";
	$script .= "				  header: '".$header.".accordionfaqheader.".$faqid."'\n";
	$script .= "				, autoheight: ".$autoheight."e\n";
	$script .= "				, alwaysOpen: ".$alwaysopen."e\n";
	$script .= "				, active: ".$activearr[0]."\n";
	$script .= "			 	, animated: ".$animation."\n";
	$script .= "			 	, event: '".$event."'\n";
	$script .= "			});\n";
	$script .= "			preparefaq.getjQuery()('#".$faqid."').bind( 'change.faq-accordion', preparefaq.accordionChangeUI );\n";
	$script .= "		});\n";
}
if (isset($jumpto))
{
	$script .= "		preparefaq.onIdAvailable( '".$jumpto."', 300, function() {\n";
	$script .= "			preparefaq.onFunctionAvailable( 'bookmarkscroll.scrollTo', 300, function() {\n";
	$script .= "				preparefaq.jumpToFaqItem( '".$jumpto."' );\n";
	$script .= "			});\n";
	$script .= "		});\n";
}
$script .= "	});\n";
$script .= "});\n";
$script .= "// -->\n";
$document->addScriptDeclaration( $script );

$html 	.= "<div id=\"".$faqid."\" class=\"accordionfaq ".$faqclass."\">";
$html 	.= "<p></p>";
$html 	.= "</div>";
$id		 = $params->get('article');
if ($id != -1)
{
	$query = "SELECT * from `#__content` WHERE `id` = ".$id;
	$db->setQuery( $query );
	if ($db->query())
	{
		$article = $db->loadObject();
		$html .= $article->introtext;
	}
}

echo $html;
