  
# Kodi Shield [![License](https://img.shields.io/github/license/robweber/kodi-shield)](https://github.com/robweber/kodi-shield/blob/master/LICENSE) 

This is a PHP application that can be used to generate badges for your Kodi addon using the [Shields.io JSON endpoint](https://shields.io/endpoint) syntax. You can include these badges on your GitHub, or other pages, so people can see information about your addon at glance. The two badges, described below, are Compatible Kodi Version and Total Downloads. 


## Install
You can install this script on any webserver that runs PHP using composer. It does use the Simple XML library for reading in the ```addon.xml``` file of your project so the correct Kodi version can be determined. A sample Apache ```.htaccess``` file using mod_rewrite is given, for other server configurations [PRs are accepted](https://github.com/robweber/kodi-shield/pulls)!

In the ```src/index.php``` file the following variables needed to be modified to fit your server setup: 

```

//MODIFY THESE TO MATCH YOUR SITUATION
$domainPath = 'http://yourdomain.com' //set this to the full path of your server, can be an IP address if no domain name
$basePath = '/kodi-shield'; //set this to the base path on your server

```

Once running you can load the default homepage to see an area where you can preview badges based on a real-time form control. 

## Usage
This script uses the [Shields.io JSON endpoint](https://shields.io/endpoint) to return the JSON needed to generate the badge. Read the information on the Shields.io website for more information on how this works. An example of using the badge within the markdown file of your project could be: 

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fdomain.com%2Fkodi-shield%2F/version/%2Frobweber%2Fxbmcbackup)
```

### Compatible Kodi Versions

This badge will show the compatible versions of Kodi for a particular addon. This is calculated by reading the addon.xml file in the addon repository on GitHub.

When queried the application will pull the ```addon.xml``` file of your addon from it's repository and calculate the compatible Kodi versions based on the official [Kodi compatibility matrix](https://kodi.wiki/view/Addon.xml#Dependency_versions). 

__Please note: this script assumes your project is hosted on Github and is the only project in a given repo, with the addon.xml file in the root of the project folder. I've noticed this is the setup for most Kodi addons__ 

The syntax for the endpoint is as follows:

```
/kodi-shield/version/:username/:repo/:branch/:shownames/:currentonly
```

* username __required__ - your Github username
* repo __required__ - the name of the repository for your addon
* branch - the branch name, master is assumed by default
* shownames - a true/false value on if the codenames for each Kodi version should also be shown
* currentonly - another true/false value. By default all compatible Kodi versions are shown, this shows only the most current supported version. 

### Total Downloads

This badge will show the total downloads from the Official Kodi Repo for a particular addon version. This uses stats from Kodi's [mirrorbits]([https://github.com/etix/mirrorbits](https://github.com/etix/mirrorbits)) system. 

The syntax for the endpoint is as follows:

```
/kodi-shield/downloads/:kodi_repo/:addon_id/:addon_version
```

* kodi_repo __required__ - the addon repository that this addon was submitted to. This is _not_ always the same as the Kodi version it is running on. 
* addon_id __required__ - the exact addon ID as specified in your ```addon.xml``` file. 
* addon_version __required__ - the addon version you want stats for. This must match what you see in the Kodi repo to get accurate information. 


## Examples

### Default Compatible Versions Badge 

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fdomain.com%2Fkodi-shield%2Fversion%2Frobweber%2Fxbmcbackup)
```

![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi-shield%2Fversion%2Frobweber%2Fxbmcbackup)

###  Compatible Versions Badge with code names:
```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fdomain.com%2Fkodi-shield%2Fversion%2Frobweber%2Fxbmcbackup%2Fmaster%2Ftrue)
```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi-shield%2Fversion%2frobweber%2Fxbmcbackup%2Fmaster%2Ftrue)



### Total Downloads Badge

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fdomain.com%2Fkodi-shield%2Fdownloads%2Fmatrix%2Fscript.xbmcbackup%2F1.6.2)
```

![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi-shield%2Fdownloads%2Fmatrix%2Fscript.xbmcbackup%2F1.6.2)