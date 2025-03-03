# Cocoque

Cocoque® est une plateforme d'e-commerce spécialisée dans la vente de coques de téléphones.

# Les livrables :

Vous trouverez tous les documents/livrables destinés à chaque ressource 

| Ressource | Nom | fichier | Date rendu |
| :---         | :---:    |:---:    | ---:          |
| R3.01       | Dev web   | [projet gitlab](https://gitlab.univ-nantes.fr/E232643Y/sae2-3_symfony)       | 15/01/25 |
| R3.02       | Analyse   | [Presentation_SAE_R303_Groupe_2_3.pdf](./Livrables/Presentation_SAE_R303_Groupe_2_3.pdf) <br> [Livrable_SAE_R303_Groupe_2_3.pdf](./Livrables/Livrable_SAE_R303_Groupe_2_3.pdf) | 10/01/25 |
| R3.03       | Dev efficace   | [SAE-R302-Dev efficace-GRP2-3.pdf](./Livrables/SAE-R302-Dev efficace-GRP2-3.pdf)        | 17/01/25 |
| R3.04       | Qdev   | [rapportR304.md](./Livrables/rapportR304.md)| 09/01/25 |
| R3.05       | Prog Sys   | projet puissance 4       | 18/12/24 |
| R3.06       | Archi reseaux   | sae | 17/01/25 |
| R3.07       | SQL   | ?¿?¿?¿? | ??/??/?? |
| R3.10       | Management    | [Livrable_SAE_R310_Groupe_2_3.pdf](./Livrables/Livrable_SAE_R310_Groupe_2_3.pdf) | ???? |
| R3.12       | Anglais    | [Canva slideShow ](https://www.canva.com/design/DAGcLR9wNrk/8fjBhpeYvQGqaqxV6_AFxw/view?utm_content=DAGcLR9wNrk&utm_campaign=designshare&utm_medium=link2&utm_source=uniquelinks&utlId=haca2248d0c) | 17/01/25 |
| R3.12       | Communication    | [COMMUNICATION_Groupe_2_3.pdf ](./Livrables/COMMUNICATION_Groupe_2_3.pdf) | 17/01/25 |




# Les designs

Vous trouverez dans le dossier ` website/HTML&CSS_design/` les designs utilisés pour ce projet avant de les implémenter dans le twig

# Le site

Vous pouvez accéder au site via l'URL suivante :
- Via le réseau de l'IUT en HTTP -> http://172.12.44:8000
- Via le VPN en HTTP -> http://172.12.44
- Via le VPN en HTTPS -> https://172.12.44:80

*les urls sont différentes car le vpn n'accepte que les connexions depuis les ports : 22(ssh) ,80(HTTP) et 443(HTTPS)*

## La page principale (/)

Sur la page principale, vous trouverez les éléments principaux de la marque : Nos produits phares ainsi que nos collections

## La page panier (/cart)

Sur cette page, vous trouverez les articles que vous avez ajoutés dans votre panier. Via cette page, vous avez la possibilité de valider votre panier et donc de passer au paiement

## La page compte (/account)

Sur cette page, il est possible de visualiser nos informations personnelles (informations sur le compte et informations sur les adresses de livraisons). Il est aussi possible de modifier ces informations via le petit bouton avec un crayon en bas à droite de l'écran

## La page products (/products)

Sur cette page, vous trouverez toutes nos coques disponibles. Vous y trouverez aussi un menu et une barre de recherche pour affiner votre recherche 

## La page product (/product)

Sur cette page, vous trouverez toutes les informations concernant un produit : son nom, sa description, son prix. Il est également possible de mettre dans son panier ce produit en différentes quantités (en fonction des stocks restants, évidemment). Il y a aussi un petit message dans le cas où le stock est inférieur à 10 produits ou que le stock est vide. Vous pouvez aussi accéder aux produits de la même collection

## La page collection (/collection)

Sur cette page, vous trouverez toutes les collections disponibles sur notre site

## La page admin (/admin)

Sur cette page réservé aux personnes possédant le rôle 'ROLE_ADMIN', il est possible de monitorer certaines valeurs et de gérer les user (suppression, modif), les produits (ajouts), les promotions (création) et les collections (création).
