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

Version: 0.2
Author: Rob Weber
Source: https://github.com/robweber/kodi-shield
License: GPL-3.0 https://github.com/robweber/kodi-shield/blob/master/LICENSE

*/

//valid kodi imports
$kodiImports = array('xbmc.python','xbmc.gui','xbmc.json','xbmc.metadata','xbmc.addon');

//mappings of imports to kodi versions
$kodiNames = array('13.x'=>'Gotham','14.x'=>'Helix','15.x'=>'Isengard','16.x'=>'Jarvis','17.x'=>'Krypton','18.x'=>'Leia');
$kodiMatrix = array('xbmc.python'=>array('2.14.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x'),'2.19.0'=>array('14.x','15.x','16.x','17.x','18.x'),'2.20.0'=>array('15.x','16.x','17.x','18.x'),'2.24.0'=>array('16.x','17.x','18.x'),'2.25.0'=>array('17.x','18.x'),'2.26.0'=>array('18.x')));

//get the url params
$urlParams = getParams();

$jsonOutput = array('schemaVersion'=>1,'label'=>'kodi version','message'=>'unknown','color'=>'blue');
$validImport = findImport($urlParams,$kodiImports);

if($validImport != null)
{
    $versions = $kodiMatrix[(string)$validImport['addon']][(string)$validImport['version']];

    //check if we should only show the most current
    if(isset($urlParams['currentonly']) && $urlParams['currentonly'] == 'true')
    {
        $versions = array_slice($versions,0,1);
    }

    //check if we should show versions and names
    if(!isset($urlParams['shownames']) || $urlParams['shownames'] == 'false')
    {
        $jsonOutput['message'] = implode(', ',$versions);
    }
    else
    {
        $names = array();
        foreach($versions as $aVersion)
        {
            $names[] = sprintf('%s %s',$aVersion,$kodiNames[$aVersion]);
        }

        $jsonOutput['message'] = implode(', ', $names);
    }
}

echo json_encode($jsonOutput);

//finds the first kodi import statement in the addon.xml file
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

        foreach($xml->requires->import as $anImport){

            if(in_array((string)$anImport['addon'],$kodiImports))
            {
                $validImport = $anImport;
                break; //we found one
            }
        }
    }

    return $validImport;
}

function getParams() {
    $result = array();

    // cut the subdirectory off the path (if any)
    $paramString = substr($_SERVER['REQUEST_URI'],strlen(dirname($_SERVER['PHP_SELF'])) + 1);

    // break the string into an array and get the var wanted
    $urlParts = explode('/', preg_replace('/\?.+/', '', $paramString));

    //we need at least 2 positional params (username, and repo)
    for($i = 0; $i < count($urlParts); $i ++)
    {
        switch($i){
            case 0:
                $result['username'] = $urlParts[$i];
                break;
            case 1:
                $result['repo'] = $urlParts[$i];
                break;
            case 2:
                $result['branch'] = $urlParts[$i];
                break;
            case 3:
                $result['shownames'] = $urlParts[$i];
                break;
            case 4:
                $result['currentonly'] = $urlParts[$i];
                break;
        }

    }

    return $result;
}

?>
