# Jours Fériés, jours ouvrés, ouvrables, en France et ses régions

Cette bibliothéque PHP propose des méthodes simples pour :
- retrouver l'ensemble des Jours Fériés Français 🇫🇷 (même ceux spécifiques à des territoires, tel que l'Alsace-Moselle, ou la France d'Outre-Mer) ;
- effectuer des calculs de dates en jours ouvrés, ouvrables, tout en tenant compte des jours fériés du territoire spécifié ;
- calculer un "délai franc administratif" ; 
- … et bien d'autres surprises !  

Très très (très) inspiré de https://github.com/etalab/jours-feries-france (mais en PHP, et avec quelques bonus utiles) ⭐️ 


## Usage de la classe `JoursFeries`

```php

// Obtenir les jours fériés pour une année, pour la métropole Française
$Jours = JoursFeries::forYear(2018); 
// $Jours = [
//   'Jour de l’an' => new DateTime('01-01-2018'),
//   'Lundi de Pâques' => new DateTime('02-04-2018'),
//   'Fête du Travail' => new DateTime('01-05-2018'),
//   'Victoire de 1945' => new DateTime('08-05-2018'),
//   'Ascension' => new DateTime('10-05-2018'),
//   'Lundi de Pentecôte' => new DateTime('21-05-2018'),
//   'Fête Nationale' => new DateTime('14-07-2018'),
//   'Assomption' => new DateTime('15-08-2018'),
//   'Toussaint' => new DateTime('01-11-2018'),
//   'Armistice' => new DateTime('11-11-2018'),
//   'Jour de Noël' => new DateTime('25-12-2018'),
// ]

// Méthode spécifique pour obtenir certains jours fériés (en DateTime) :
echo JoursFeries::lundiPaques(2018); // => new DateTime('02-04-2018')
echo JoursFeries::ascension(2018); // new DateTime('10-05-2018')
echo JoursFeries::lundiPentecote(2018); // new DateTime('21-05-2018')

// Obtenir les jours fériés pour une zone spécifique
$Jours = JoursFeries::forYear(2018, "Alsace-Moselle");

# Quelques fonctions pratiques :
JoursFeries::isFerie(new DateTime("25-12-2019"), "Métropole"); // => True
JoursFeries::getNextFerie(new DateTime("24-12-2019"),"Métropole"); // => new DateTime("25-12-2019")

```

## Usage de la classe `JoursAdministratifs` (calculs de délais)

```php

// Obtenir la date correspondante à 10 jours calendaires à partir d'aujourd'hui
JoursAdministratifs::addJourCalendaire(new DateTime(), 10);
// Obtenir la date correspondante à 10 jours ouvrables à partir d'aujourd'hui
JoursAdministratifs::addJourOuvrable(new DateTime(), 10);
// Obtenir la date correspondante à 8 jours ouvrés à partir d'aujourd'hui
JoursAdministratifs::addJourOuvre(new DateTime(), 8);

// Obtenir la date correspondante d'il y a 10 jours ouvrés à partir d'aujourd'hui  (dans le passé donc)
JoursAdministratifs::subJourOuvre(new DateTime(), 10);

// Obtenir la date correspondante à 10 jours ouvrables après le 20/12/2017, en Alsasce-Moselle
JoursAdministratifs::addJourOuvrable(new DateTime('20-12-2017'), 10, 'Alsace-Moselle');

// Obtenir un délais franc, c'est à dire 10 jours calendaires à partir du 14/12/2020, 
// mais si le dernier jour n'est pas ouvré, alors on renverra le prochain jour ouvré
JoursAdministratifs::addJourFranc(new DateTime('14-12-2020'), 10);

```

## TO DO AND KNOWN ISSUES

- Ajouter un fichier .gitlab-ci.yml et automatiser les tests dans gitlab-ci

- La méthode `JoursAdministratifs::subJourFranc()` n'existe pas. Pourquoi ? 
  Car la fin de délais correspond au prochain jour ouvré, de ce fait, le calcul de la date d'origine va rester flou / aléatoire si plusieurs jours non-ouvrés se suivent en fin de délais ! 
  Si je cherche la date correspondante au début d'un délais franc de 10 jours, se terminant le 31 Déc. 2020, un lundi donc, dont les jours précédents sont respectivement un dimanche, un samedi et un vendredi de Noël. La date de début du délais est imprécise et délicate à calculer car elle peut correspondre au 15 Déc., au 16 Déc, au 17 Déc ou encore au 18 Déc. ! 
  Un solution serait de demander le délais "max" ou le délais "min". Voyons si un jour cette méthode est nécessaire. 

