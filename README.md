#Past Paths Project
The Past Paths Project is currently developing a web platform and novel search engine encouraging people to search and discover museum objects. It will creatively connect objects to rich web content and inspire new public explorations of online collections.

It will transform searching online object catalogues into a playful museum experience that connects object records to diverse online content (text, images, multimedia) from across the web and social media. Engaging, content-rich experiences will be developed that place museum objects at their core.

The search model for museum collections is traditionally designed for research audiences who know what they are looking for. This project will design for the casual browser and deliver content that provokes unexpected discovery.

Another benefit of this development will be the transformation of a static data collection into a living, evolving digital archive. User-centered systems will be developed to capture audience interaction with collections. The object record will expand to incorporate associated web content that audience search has deemed relevant and engaging. The search engine will refine its understanding of what web content and search results are most likely to encourage a user journey through museum collections.

Past Paths was one of 12 schemes selected for funding through the Digital R&D Fund for the Arts, which supports collaboration between organisations with arts projects, technology providers and researchers. The fund is a The Fund is a three way partnership between Arts Council England, the Arts and Humanities Research Council (AHRC) and innovation charity Nesta.

## How to install
You will require:

###Services
- Neo4J Community Edition
- MongoDb + PHP MongoDB drivers
- Apache

###Vagrant
This project has a Vagrant file allowing you to automate the installation of required packages (https://www.vagrantup.com/).

Once Vagrant is installed open a terminal and head into the working directory and type vagrant up. The server will install all packages required.

Your server will be available on localhost:2200

###Importing data
You will require xml dumps from Culture Grid, place these within the app/webroot/files/artefact_xml_exports folder before running the import scripts.

After the server is live;
- Add your Alchemy API key in the bootstrap file (app/config/bootstrap.php Line 76)
- Place the LidoXML files in app/webroot/files/artefact_xml_exports
- Visit /import to import the data into mongodb, run keywords through Alchemy API, and create neo4j graph of keywords + artefacts
- Visit /import_images which will download images specified in the LidoXML and place them under app/webroot/img/artefacts/{medium, large}/{Lido Record Id}/{0..*.jpeg}
- Congratulations, the engine should be up and running. Visit localhost:2200 to start browsing.

###Data
Working collection of artefacts - Available from Culture Grid (http://www.culturegrid.org.uk)

###API Keys
- Culture Grid API Key
- Alchemy API - Available from http://www.alchemyapi.com

You will need to edit your alchemy API key in the bootstrap.php file (app/config/bootstrap.php Line 76)

####Images
The folder structure is as follows: /app/webroot/img/artefacts/(large/medium/small)/lidoRecID

