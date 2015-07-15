<?php

require_once __DIR__ . '/console.php';
require_once __DIR__ . '/color.php';

$urls = array();
$responseCodes = array();

function xml2Array($xml)
{
	$xml = simplexml_load_string($xml);
	return array($xml->getName() => json_decode(json_encode($xml), true));
}

function proccessSiteMapUrl($url)
{
	$siteMapXml = file_get_contents($url);
	$siteMapXmlArray = xml2Array($siteMapXml);

	if(isset($siteMapXmlArray['sitemapindex'])) {
		proccessSiteMapIndex($siteMapXmlArray);
	} elseif(isset($siteMapXmlArray['urlset'])) {
		proccessSiteMap($siteMapXmlArray);
	}
}

function proccessSiteMapIndex($siteMapXmlArray)
{
	if(isset($siteMapXmlArray['sitemapindex']['sitemap']['loc'])) {
		$siteMapXmlArray['sitemapindex']['sitemap'] = array($siteMapXmlArray['sitemapindex']['sitemap']);
	}
	foreach($siteMapXmlArray['sitemapindex']['sitemap'] as $sitemap) {
		proccessSiteMapUrl($sitemap['loc']);
	}
}

function proccessSiteMap($siteMapXmlArray)
{
	if(isset($siteMapXmlArray['urlset']['url']['loc'])) {
		$siteMapXmlArray['urlset']['url'] = array($siteMapXmlArray['urlset']['url']);
	}
	foreach($siteMapXmlArray['urlset']['url'] as $url) {
		$GLOBALS['urls'][] = $url;
	}
}

function get_http_response_code($theURL)
{
	$headers = get_headers($theURL);
	return (int)(substr($headers[0], 9, 3));
}

$console = Console::getInstance();

if(!isset($argv[1])) {
	$console->writeLine("Sitemap url not set", Color::RED);
	die();
}

$sitemapUrl = $argv[1];

proccessSiteMapUrl($sitemapUrl);

$countUrls = count($urls);
$console->writeLine("Url count: " . $countUrls);

$i = 0;
foreach($urls as $url) {
	$i++;
	echo "On " . $i . " of " . $countUrls . (($i === $countUrls) ? "\n" : "\r");
	usleep(1000);
	$code = get_http_response_code($url['loc']);
	if(!isset($responseCodes[$code])) {
		$responseCodes[$code] = 0;
	}
	$responseCodes[$code]++;
	$url['code'] = $code;
}

ksort($responseCodes);

foreach($responseCodes as $code => $count) {
	$console->writeLine("Code " . $code . " : " . $count);
}

if(count($responseCodes) === 1 && isset($responseCodes[200])) {
	$console->writeLine("Success", Color::GREEN);
} else {
	$console->writeLine("Errors found", Color::RED);
}