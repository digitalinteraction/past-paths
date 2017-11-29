# Past Paths Project
The Past Paths project has developed a web platform and novel search engine encouraging people to search and discover museum objects. It has been produced by Tyne & Wear Archives & Museums, Newcastle University and Microsoft Research.
 
The search model for museum collections is often designed to support audiences who know broadly what they are looking for and how to look for it. This project is designed for the casual browser and delivers content that provokes unexpected discovery.
 
Explore Tyne & Wear Archives & Museums collection using this codebase here -http://collectionsdivetwmuseums.org.uk
 
Objects are presented based on how you use the site. The more you explore certain artefacts, the more related the content will be. The faster you scroll, the more random the results.
 
The system also provokes audiences to explore across a wide range of collections by exposing the shared metadata that connects objects.
 
This documentation and codebase is freely available for cultural heritage organisations to use. Commercial reuse and exploitation of this system is prohibited.
 
Any reuse of this codebase should credit and hyperlink ‘Powered by Past Paths discovery engine’.
 
Please contact john.coburn@twmuseums.org.uk with any questions about reuse.
 
This project is supported by the Digital R&D Fund for the Arts: Nesta, Arts and Humanities Research Council and public funding by the National Lottery through Arts Council England.

## How to install
You will require:

### Services
- Neo4J Community Edition
- MongoDb + PHP MongoDB drivers
- Apache

### Vagrant
This project has a Vagrant file allowing you to automate the installation of required packages (https://www.vagrantup.com/).

Once Vagrant is installed open a terminal and head into the working directory and type vagrant up. The server will install all packages required.

Your server will be available on localhost:2200

### Importing data
You will require xml dumps from Culture Grid, place these within the app/webroot/files/artefact_xml_exports folder before running the import scripts.

After the server is live;
- Add your Alchemy API key in the bootstrap file (app/config/bootstrap.php Line 76)
- Place the LidoXML files in app/webroot/files/artefact_xml_exports
- Visit /import to import the data into mongodb, run keywords through Alchemy API, and create neo4j graph of keywords + artefacts
- Visit /import_images which will download images specified in the LidoXML and place them under app/webroot/img/artefacts/{medium, large}/{Lido Record Id}/{0..*.jpeg}
- Congratulations, the engine should be up and running. Visit localhost:2200 to start browsing.

### Data
Working collection of artefacts - Available from Culture Grid (http://www.culturegrid.org.uk)

### API Keys
- Culture Grid API Key
- Alchemy API - Available from http://www.alchemyapi.com

You will need to edit your alchemy API key in the bootstrap.php file (app/config/bootstrap.php Line 76)

#### Images
The folder structure is as follows: /app/webroot/img/artefacts/(large/medium/small)/lidoRecID

