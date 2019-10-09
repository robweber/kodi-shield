<?php

/**
Kodi Shield Generator

This script will generate the JSON needed to format a badge based on the Shields.io JSON Endpoint specification for Kodi addons. The goal is to show a badge that lists all compatible versions of Kodi for a given addon. 

Query Params: 

* username __required__ - your Github username
* repo __required__ - the name of the repository for your addon
* branch - the branch name, master is assumed by default
* shownames - a true/false value on if the codenames for each Kodi version should also be shown
* currentonly - another true/false value. By default all compatible Kodi versions are shown, this shows only the most current supported version. 

Version: 0.1
Author: Rob Weber
Source: https://github.com/robweber/kodi-shield
License: GPL-3.0 https://github.com/robweber/kodi-shield/blob/master/LICENSE

*/

//valid kodi imports
$kodiImports = array('xbmc.python','xbmc.gui','xbmc.json','xbmc.metadata','xbmc.addon');

//mappings of imports to kodi versions
$kodiNames = array('13.x'=>'Gotham','14.x'=>'Helix','15.x'=>'Isengard','16.x'=>'Jarvis','17.x'=>'Krypton','18.x'=>'Leia');
$kodiMatrix = array('xbmc.python'=>array('2.14.0'=>array('13.x','14.x','15.x','16.x','17.x','18.x'),'2.19.0'=>array('14.x','15.x','16.x','17.x','18.x'),'2.20.0'=>array('15.x','16.x','17.x','18.x'),'2.24.0'=>array('16.x','17.x','18.x'),'2.25.0'=>array('17.x','18.x'),'2.26.0'=>array('18.x')));


$jsonOutput = array('schemaVersion'=>1,'label'=>'kodi version','message'=>'unknown','color'=>'blue');
$validImport = findImport($kodiImports);

if($validImport != null)
{
    $versions = $kodiMatrix[(string)$validImport['addon']][(string)$validImport['version']];

    //check if we should only show the most current
    if(isset($_GET['currentonly']) && $_GET['currentonly'] == 'true')
    {
        $versions = array_slice($versions,0,1);
    }

    //check if we should show versions and names
    if(!isset($_GET['shownames']) || $_GET['shownames'] == 'false')
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
function findImport($kodiImports){
    $validImport = null;

    //we need the user and repo at minimum
    if(isset($_GET['username']) && isset($_GET['repo']))
    {
        //if no branch, assume master
        $branch = 'master';
        if(isset($_GET['branch']))
        {
            $branch = $_GET['branch'];
        }

        //we need the usern, repo, and branch to pull the addon.xml file from
        $repoUrl = sprintf('https://raw.githubusercontent.com/%s/%s/%s/addon.xml',$_GET['username'],$_GET['repo'],$branch);

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

?>
