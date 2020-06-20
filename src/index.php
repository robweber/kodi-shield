<?php

/**
Kodi Shield Generator

This script will generate the JSON needed to format a badge based on the Shields.io JSON Endpoint specification for Kodi addons. The goal is to show a badge that lists all compatible versions of Kodi for a given addon. 

Url Params:

Example: /kodi-shield/:username/:repo/:branch 

Params are positional so using an optional one means you must set the ones before it. 

* username (required) - your Github username
* repo (required) - the name of the repository for your addon
* branch - the branch name, master is assumed by default
* shownames - a true/false value on if the codenames for each Kodi version should also be shown
* currentonly - another true/false value. By default all compatible Kodi versions are shown, this shows only the most current supported version. 

Version: 2.0
Author: Rob Weber
Source: https://github.com/robweber/kodi-shield
License: GPL-3.0 https://github.com/robweber/kodi-shield/blob/master/LICENSE

*/
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App as App;

require __DIR__ . '/../vendor/autoload.php';

//valid kodi imports
$kodiImports = array('xbmc.python','xbmc.gui','xbmc.json','xbmc.metadata','xbmc.addon');
//mappings of imports to kodi versions
$kodiNames = array('13.x'=>'Gotham','14.x'=>'Helix','15.x'=>'Isengard','16.x'=>'Jarvis','17.x'=>'Krypton','18.x'=>'Leia','19.x'=>'Matrix');
$kodiMatrix = array('xbmc.python'=>array('2.14.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x'),'2.19.0'=>array('14.x','15.x','16.x','17.x','18.x'),'2.20.0'=>array('15.x','16.x','17.x','18.x'),'2.24.0'=>array('16.x','17.x','18.x'),'2.25.0'=>array('17.x','18.x'),'2.26.0'=>array('18.x'),'3.0.0'=>array('19.x')),
                   'xbmc.gui'=>array('5.0.1'=>array('13.x','14.x','15.x'),'5.3.0'=>array('14.x','15.x'),'5.9.0'=>array('15.x','16.x','17.x','18.x'),'5.10.0'=>array('16.x','17.x','18.x'),'5.12.0'=>array('17.x','18.x'),'5.14.0'=>array('18.x')),
                   'xbmc.json'=>array('6.6.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x'),'6.20.0'=>array('14.x','15.x','16.x','17.x','18.x'),'6.25.1'=>array('15.x','16.x','17.x','18.x'),'6.32.4'=>array('16.x','17.x','18.x'),'7.0.0'=>array('17.x','18.x'),'9.7.2'=>array('18.x')),
		   'xbmc.metadata'=>array('2.1.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x')),
		   'xbmc.addon'=>array('13.0.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x'),'14.0.0'=>array('14.x','15.x','16.x','17.x','18.x'),'15.0.0'=>array('15.x','16.x','17.x','18.x'),'16.0.0'=>array('16.x','17.x','18.x'),'17.0.0'=>array('17.x','18.x'),'17.9.910'=>array('18.x')));

//MODIFY THESE TO MATCH YOUR SITUATION
$domainPath = "https://weberjr.com";
$basePath = '/kodi-shield'; //set this if the basePath changes


$app = new App();

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', [
        'cache' => False
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

//DEFAULT ROUTE
$app->get($basePath . '/', function(Request $request, Response $response, $urlParams) use($basePath, $domainPath) {
    return $this->view->render($response, 'index.html', [
        'basePath' => $basePath,
        'urlRoot' => $domainPath . $basePath
    ]);
});

// ROUTE FOR /version endpoint
$app->get($basePath . '/version/{username}/{repo}[/{branch}[/{shownames}[/{currentonly}]]]', function (Request $request, Response $response, $urlParams) use($kodiImports,$kodiNames,$kodiMatrix) {

    $jsonOutput = array('schemaVersion'=>1,'label'=>'kodi version','message'=>'unknown','color'=>'blue');
    $validImport = findImport($urlParams,$kodiImports);

	if($validImport != null)
	{
		if(array_key_exists((string)$validImport['version'],$kodiMatrix[(string)$validImport['addon']]))
		{
			$versions = $kodiMatrix[(string)$validImport['addon']][(string)$validImport['version']];
			//add version names if wanted
			if(isset($urlParams['shownames']) && $urlParams['shownames'] == 'true')
			{
				$names = array();
				foreach($versions as $aVersion)
				{
					$names[] = sprintf('%s %s',$aVersion,$kodiNames[$aVersion]);
				}
				$versions = $names;
			}
			//create display message
			$message = '';
			//if only the most current get the last in array
			if(isset($urlParams['currentonly']) && $urlParams['currentonly'] == 'true')
			{
				$message = sprintf('%s',$versions[count($versions) -1]);
			}
			else
			{
				$message = implode(', ',$versions);
			}
			$jsonOutput['message'] = $message;
		}
	}
	else
	{
		$jsonOutput['color'] = 'red';
		$jsonOutput['message'] = 'addon.xml error';
	}

		$response->getBody()->write(json_encode($jsonOutput));
		return $response->withHeader('Content-Type','application/json');
	});

//ROUTE FOR /downloads ENDPOINT
$app->get($basePath . '/downloads/{repo}/{addon_name}/{addon_version}', function (Request $request, Response $response, $urlParams) {

	$jsonOutput = array('schemaVersion'=>1,'label'=>sprintf('v%s downloads',$urlParams['addon_version']),'message'=>'unknown','color'=>'green');

	//construct the stats url
	$statUrl = sprintf('http://mirrors.kodi.tv/addons/%s/%s/%s-%s.zip?stats',$urlParams['repo'],$urlParams['addon_name'],$urlParams['addon_name'],$urlParams['addon_version']);

	//if the addon version or name is unknown you just get 0 for everything
	$stats = json_decode(file_get_contents($statUrl));

	$totalDownloads = sprintf('%d', $stats->Total);

	if($stats->Total/1000 > 1)
	{
		$totalDownloads = sprintf('%sK', number_format($stats->Total/1000,1));
	}

	if($stats->Total/1000000 > 1)
	{
		$totalDownloads = sprintf('%sM', number_format($stats->Total/1000000,1));
	}

	$jsonOutput['message'] = $totalDownloads;

	$response->getBody()->write(json_encode($jsonOutput));
	return $response->withHeader('Content-Type','application/json');

	});

$app->run();


//function to find the module imported
function findImport($urlParams,$kodiImports){
    $validImport = null;
    //we need the user and repo at minimum
    if(isset($urlParams['username']) && isset($urlParams['repo']))
    {
        //if no branch, assume master
        $branch = 'master';
        if(isset($urlParams['branch']))
        {
            $branch = $urlParams['branch'];
        }
        //we need the usern, repo, and branch to pull the addon.xml file from
        $repoUrl = sprintf('https://raw.githubusercontent.com/%s/%s/%s/addon.xml',$urlParams['username'],$urlParams['repo'],$branch);
        $xml = simplexml_load_file($repoUrl);
        if($xml !== False)
        {
            foreach($xml->requires->import as $anImport){
                if(in_array((string)$anImport['addon'],$kodiImports))
                {
                    $validImport = $anImport;
                    break; //we found one
                }
            }
        }
    }
    return $validImport;
}

?>
