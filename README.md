#Past Paths Project
Past Paths, which involves Newcastle University’s Culture Lab and Microsoft Research, will develop a web platform and novel search engine encouraging people to search and discover museum objects. It will creatively connect objects to rich web content and inspire new public explorations of online collections.

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

