## UPGRADE GUIDE

#### Version 1.x to 2.x

Upgrade is not possible. See the installation guide for a fresh install.

#### Version 2.x to 3.x

#### 1. Migration

Perform every step detailed from option 2 of the [Migration guide](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/migration.md#2-move-files-to-new-installation). But, there are some exceptions:
- In the step 2.4 you must download the 3.x version, instead of the your current 2.x version.
- Backup and creation of database is not required, because this is not a migration and in the new version installation the database will be upgraded. Nevertheless, is always recommended have a backup for security.

After that, are required some steps to enable the Monitoring module. If you are not interested in this module, your upgrade is done.

#### 2. Install OSCARS Bridge following [this guide](https://github.com/ufrgs-hyman/oscars-bridge/blob/master/README.md).

#### 3. Update parameters (Location: params.php in config folder)

#### 3.1. Configure the URL of the OSCARS Bridge in params.php

After this line:
```
"provider.force.dummy" => false,
```
Add your OSCARS Bridge URL:
```
"oscars.bridge.provider.url" => 'http://localhost:8080/oscars-bridge/circuits',
```

#### 3.2. Configure the URL of the Esmond REST API in params.php

After the line added above, add your Esmond API URL:
```
"esmond.server.api.url" => 'http://localhost/esmond/v2/',
```

#### 3.3. Certificate param name

Change only the left part of the following params (Do not change your right value (#value#)):
```
'meican.certificate.filename' => "#value#",
'meican.certificate.passphrase' => "#value#",	
```
To this:
```
'certificate.filename' => "#value#",
'certificate.pass' => "#value#",	
```
    
Done! Your application must be ready for use.
