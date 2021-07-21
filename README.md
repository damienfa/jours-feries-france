# Jours Fériés France

Classe de calcul des dates de jours fériés en France (métropole + cas spécifiques).
J'4'ai ajouté une classe JoursAdministratifs pour calculer les délais administratifs courants par rapport à une date.

Très très (très) inspiré de https://github.com/etalab/jours-feries-france

## Usage de la classe `JoursFeries`

```php

// Obtenir les jours fériés pour une année, pour la métropole
$res = JoursFeries::forYear(2018)
// res est un dictionnaire
// [
// 'Jour de l’an' => new DateTime('01-01-2018'),
// 'Lundi de Pâques' => new DateTime('02-04-2018'),
// 'Fête du Travail' => new DateTime('01-05-2018'),
// 'Victoire de 1945' => new DateTime('08-05-2018'),
// 'Ascension' => new DateTime('10-05-2018'),
// 'Lundi de Pentecôte' => new DateTime('21-05-2018'),
// 'Fête Nationale' => new DateTime('14-07-2018'),
// 'Assomption' => new DateTime('15-08-2018'),
// 'Toussaint' => new DateTime('01-11-2018'),
// 'Armistice' => new DateTime('11-11-2018'),
// 'Jour de Noël' => new DateTime('25-12-2018'),
// ]

// Vous pouvez aussi obtenir certains jours fériés en tant que datetime.date
echo JoursFeries::lundiPaques(2018);
echo JoursFeries::ascension(2018);
echo JoursFeries::lundiPentecote(2018);

// Obtenir les jours fériés pour une zone spécifique
$res = JoursFeries::forYear(2018, "Alsace-Moselle");

# Quelques fonctions d'aide
JoursFeries::isFerie(new DateTime("25-12-2019"), "Métropole");
# -> True
JoursFeries::getNextFerie(new DateTime("24-12-2019"),"Métropole");
# -> new DateTime("25-12-2019")
```

## Usage de la classe `JoursAdministratifs`

```php

// Obtenir la date après 10 jours calendaires
JoursAdministratifs::addJourCalendaire(new DateTime(), 10);
// Obtenir la date après 10 jours ouvrables
JoursAdministratifs::addJourOuvrable(new DateTime(), 10);
// Obtenir la date après 10 jours ouvrés
JoursAdministratifs::addJourOuvre(new DateTime(), 10);


// Obtenir la date il y a 10 jours ouvrés (dans le passé donc)
JoursAdministratifs::subJourOuvre(new DateTime(), 10);

// Obtenir la date après 10 jours ouvrables en Alsasce-Moselle
JoursAdministratifs::addJourOuvrable(new DateTime('20-12-2017'), 10, 'Alsace-Moselle');

// Obtenir la date après 10 jours calendaires mais si le dernier jour n'est pas ouvré on renvoie le prochain jour ouvré
JoursAdministratifs::addJourFranc(new DateTime('14-12-2020'), 10);

```

## TODO

- Automatiser les tests dans gitlab-ci
- Publier sur packagist

## FAQ

(ouais bon pour l'instant y'a que moi qui me pose des questions)

- Pourquoi pas de JoursAdministratifs::subJourFranc() ?

  Parce que la règle de fin de délai rend le calcul aléatoire.
  Prenons par exemple un délai finissant le 28 décembre 2020, un lundi : les jours précédents sont respectivement un dimanche, un samedi et un vendredi de Noël. Il est très possible que le délai se soit fini le vendredi, le samedi ou le dimanche reporté au lundi... ce qui veut dire qu'on ne peut pas vraiment calculer une date de départ mais un intervalle de départs possibles. Peut-être qu'on le fera un jour. :)
