
# Kodi Shield [![License](https://img.shields.io/github/license/robweber/kodi-shield)](https://github.com/robweber/kodi-shield/blob/master/LICENSE) 

This is a small PHP script that can be used to generate a badge for your Kodi addon using the [Shields.io JSON endpoint]([https://shields.io/endpoint](https://shields.io/endpoint)) syntax. The result is badge you can include with your Kodi addon so people know at a glance what versions of Kodi your addon will run on. 

This is done by reading in the addon.xml file of your addon from it's repository and calculating the compatible Kodi versions based on the official [Kodi compatibility matrix](https://kodi.wiki/view/Addon.xml#Dependency_versions). 

## Install
You can install this script on any webserver that runs PHP. It does use the Simple XML library for reading in the ```addon.xml``` file of your project so the correct Kodi version can be determined. 

__Please note: this script assumes your project is hosted on Github and is the only project in a given repo, with the addon.xml file in the root of the project folder. I've noticed this is the setup for most Kodi addons__

## Usage
This script uses the [Shields.io JSON endpoint]([https://shields.io/endpoint](https://shields.io/endpoint)) to return the JSON needed to generate the badge. Read the information on the Shields.io website for more information on how this works. An example of using the badge within the markdown file of your project could be: 

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fyourdomain.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster)
```

You can generate a URL of your own using the URL generator on the [Shields.io website]([https://shields.io/endpoint](https://shields.io/endpoint)) (scroll to the bottom of the page). 

The script takes the following GET parameters: 

* username __required__ - your Github username
* repo __required__ - the name of the repository for your addon
* branch - the branch name, master is assumed by default
* shownames - a true/false value on if the codenames for each Kodi version should also be shown
* currentonly - another true/false value. By default all compatible Kodi versions are shown, this shows only the most current supported version. 

## Examples

### Default Badge: 

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster)
```

![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster)

### Badge with code names:
```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster%26shownames%3Dtrue)
```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster%26shownames%3Dtrue)



### Badge with only the most current version: 

```
![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster%26shownames%3Dtrue%26currentonly%3Dtrue)
```

![Kodi Version](https://img.shields.io/endpoint?url=https%3A%2F%2Fweberjr.com%2Fkodi_shield.php%3Fusername%3Drobweber%26repo%3Dxbmcbackup%26branch%3Dmaster%26shownames%3Dtrue%26currentonly%3Dtrue)