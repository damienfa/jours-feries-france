# Jours Fériés France

Classe de calcul des dates de jours fériés en France (métropole + cas spécifiques)

Très très (très) inspiré de https://github.com/etalab/jours-feries-france

## Usage

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
