#Past Paths Project
The Past Paths Project is currently developing a web platform and novel search engine encouraging people to search and discover museum objects. It will creatively connect objects to rich web content and inspire new public explorations of online collections.

Explore Tyne & Wear Archives & Museums collection using this codebase here - http://collectionsdivetwmuseums.org.uk 

The search model for museum collections is traditionally designed for research audiences who know what they are looking for. This project is designed for the casual browser and delivers content that provokes unexpected discovery.

Objects are presented based on how you use the site. The more you explore certain artefacts, the more related the content will be. The faster you scroll, the more random the results.

The system also provokes audiences to explore across a wide range of collections by exposing the shared metadata that connects objects.

This documentation and codebase is freely available for cultural heritage organisations to use. Commercial reuse and exploitation of this system is prohibited

Past Paths was one of 12 schemes selected for funding through the Digital R&D Fund for the Arts, which supports collaboration between organisations with arts projects, technology providers and researchers. The fund is a The Fund is a three way partnership between Arts Council England, the Arts and Humanities Research Council (AHRC) and innovation charity Nesta.

## How to install
You will require:

###Services
- Neo4J Community Edition
- MongoDb + PHP MongoDB drivers
- Apache

###Data
Working collection of artefacts - Available from Culture Grid (http://www.culturegrid.org.uk)

###API Keys
- Culture Grid API Key
- Alchemy API - Available from http://www.alchemyapi.com
- Open Calais API - Available from http://www.opencalais.com


###Importing Data
You will require xml dumps from Culture Grid, place these within the app/webroot/files/artefact_xml_exports folder.

####Images
The folder structure is as follows: /app/webroot/img/artefacts/(large/medium/small)/lidoRecID

