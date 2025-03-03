# Cocoque

Cocoque® est une plateforme de e-commerce spécialisé dans la vente de coques de téléphones.

# Les livrables :

Vous trouverez tous les documents/livrables destinés à chaque Ressource 

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

vous trouverez dans le dossier ` website/HTML&CSS_design/` les designs utilisés pour ce projets avant de les implémenter dans le twig

# Le site

vous pouvez acceder au site via l'url suivante :
- Via le reseau de l'IUT en http -> http://172.12.44:8000
- Via le VPN en http -> http://172.12.44
- Via le VPN en https -> https://172.12.44:80

## La page principale (/)

Sur la page principale, vous trouverez les éléments principaux de la marque : Nos produits phares ainsi que nos collection

## La page panier (/cart)

Sur cette page, vous trouver les articles que vous avez ajouté dans votre panier. Via cette page vous avez la possibilité de valider votre panier et donc ce passer au paiement

## La page compte (/account)

Sur cette page, il est possible de visualiser nos informations personelles (informations sur le compte et informations sur les adresses de livraisons). Il est aussi possible de modifier ces informations via le petit bouton avec un crayon en bas a droite de l'écran

## La page products (/products)

Sur cette page, vous trouverez toutes nos coques disponibles. Vous y trouverez aussi un menu et une barre de recherche pour affiner votre recherche 

## La page product (/product)

Sur cette page, vous trouverez toutes les informations concernant un produit : son nom, sa description, son prix. Il est également de mettre dans son panier ce produits en différentes quantités (en fonction des stocks restant evidemment). Il y a aussi un petit message dans le cas ou les stock est inferieur a 10 produits ou que le stock est vide. Vous pouvez aussi acceder aux produits de la meme collections

## La page collection (/collection)

Sur cette page, vous trouverez toutes les collections disponibles sur notre site

## La page admin (/admin)

Sur cette page réservé aux personnes possedant le role 'ROLE_ADMIN', il est possible de monitorer certaines valeurs et de gerer les user(suppression, modif), les produits (ajouts), promotions (création) et collection (création)

Compte Prof (admin) :

> Mail: prof@cocoque.fr

> Password: `#cocoqueProf#`
