<?php

class JoursFeries
{
    const ZONES = [
        'Métropole',
        'Alsace-Moselle',
        'Guadeloupe',
        'Guyane',
        'Martinique',
        'Mayotte',
        'Nouvelle-Calédonie',
        'La Réunion',
        'Polynésie Française',
        'Saint-Barthélémy',
        'Saint-Martin',
        'Wallis-et-Futuna',
        'Saint-Pierre-et-Miquelon',
    ];

    /**
     * Returns the DateTime $date at midnight the same day.
     *
     * @param DateTime $date date
     * @return DateTime
     */
    private static function normalizeDateTime(DateTime $date): DateTime
    {
        return $date->setTime(0, 0, 0);
    }

    /**
     * Returns the current Zone as a string (same as argument) if it's correct, throws Exception otherwise.
     *
     * @param string $zone
     * @return string
     * @throws InvalidArgumentException on invalid zone
     */
    private static function checkZone(string $zone = 'Métropole'): string
    {
        if (! in_array($zone, self::ZONES)) {
            throw new InvalidArgumentException('Zone non valide, les valeurs attentues sont : '.implode(', ', self::ZONES));
        }

        return $zone;
    }

    /**
     * Returns an associative array of $year's jours fériés with keys as days's name and value as days DateTime.
     *
     * @param int $year year to get jours fériés from
     * @param string $zone geographic zone for férié days
     * @return array [day's name => DateTime]
     */
    public static function forYear(int $year, string $zone = 'Métropole'): array
    {
        $zone = self::checkZone($zone);

        $joursFeries = [

            'Jour de l’an' => self::jourDeLAn($year),
            'Lundi de Pâques' => self::lundiPaques($year),
            'Fête du Travail' => self::feteTravail($year),
            'Victoire de 1945' => self::victoire1945($year),
            'Ascension' => self::ascension($year),
            'Lundi de Pentecôte' => self::lundiPentecote($year),
            'Fête Nationale' => self::feteNationale($year),
            'Assomption' => self::assomption($year),
            'Toussaint' => self::toussaint($year),
            'Armistice' => self::armistice($year),
            'Jour de Noël' => self::noel($year),
            'Vendredi saint' => self::vendrediSaint($year, $zone),
            '2ème jour de Noël' => self::deuxiemeJourDeNoel($year, $zone),
            "Abolition de l'esclavage" => self::abolitionEsclavage($year, $zone),
        ];

        return array_filter($joursFeries);
    }

    /**
     * Check if $date is a jour férié.
     *
     * @param DateTime $date date to check
     * @param string $zone geographic zone for férié days
     * @return bool
     */
    public static function isFerie(DateTime $date, string $zone = 'Métropole'): bool
    {
        $date = self::normalizeDateTime($date);
        // return date in $date.for_year(date.year, zone).values()
        return in_array($date, self::forYear((int) $date->format('Y'), $zone));
    }

    /**
     * Returns next jour férié starting from $date.
     *
     * @param DateTime $date date to start search for a jour férié
     * @param string $zone geographic zone for férié days
     * @return array [day's name, DateTime]
     */
    public static function getNextFerie(DateTime $date, string $zone = 'Métropole'): array
    {
        $foundDate = null;
        // there should be better ways to do this, I mean really
        // TODO optimiser en limitant le re-calcul des jours fériés d'une année.
        while (! $foundDate) {
            if (self::isFerie($date, $zone)) {
                $foundDate = $date;
                break;
            }
            $date->add(DateInterval::createFromDateString('1 day'));
        }

        return [array_search($foundDate, self::forYear((int) $foundDate->format('Y'), $zone)), $foundDate];
    }

    /**
     * Returns Pâques (easter) day for $year.
     *
     * @param int $year
     * @return DateTime|null
     */
    public static function paques(int $year): ?DateTime
    {
        if ($year < 1886) {
            return null;
        }
        // cf. note on this page : https://www.php.net/manual/fr/function.easter-date.php
        $base = new DateTime("$year-03-21");
        $days = easter_days($year);

        return $base->add(new DateInterval("P{$days}D"));
    }

    /**
     * Return jour férié for Abolition de l'esclavage.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function abolitionEsclavage(int $year, string $zone = 'Métropole'): ?DateTime
    {
        $zone = self::checkZone($zone);
        $date = self::normalizeDateTime(new DateTime());

        switch ($zone) {
            case 'Mayotte':
                return $date->setDate($year, 4, 27);
                break;
            case 'Martinique':
                return $date->setDate($year, 5, 22);
                break;
            case 'Guadeloupe':
                return $date->setDate($year, 5, 27);
                break;
            case 'Saint-Martin':
                return $date->setDate($year, 5, $year >= 2018 ? 28 : 27);
                break;
            case 'Guyane':
                return $date->setDate($year, 6, 10);
                break;
            case 'Saint-Barthélémy':
                return $date->setDate($year, 10, 9);
                break;
            case 'La Réunion':
                return $year >= 1981 ? $date->setDate($year, 12, 20) : null;
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Return jour férié for Vendredi Saint.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function vendrediSaint(int $year, string $zone = 'Métropole'): ?DateTime
    {
        $zone = self::checkZone($zone);
        if ($zone == 'Alsace-Moselle') {
            $dateInterval = DateInterval::createFromDateString('2 day');

            return self::paques($year)->sub($dateInterval);
        }

        return null;
    }

    /**
     * Return jour férié for Ascension.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function ascension(int $year): ?DateTime
    {
        if ($year >= 1802) {
            $dateInterval = DateInterval::createFromDateString('39 day');

            return self::paques($year)->add($dateInterval);
        }

        return null;
    }

    /**
     * Return jour férié for Lundi de Pentecôte.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function lundiPentecote(int $year): ?DateTime
    {
        if ($year >= 1886) {
            $dateInterval = DateInterval::createFromDateString('50 day');

            return self::paques($year)->add($dateInterval);
        }

        return null;
    }

    /**
     * Return jour férié for Lundi de Pâques.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function lundiPaques(int $year): ?DateTime
    {
        $dateInterval = DateInterval::createFromDateString('1 day');

        return self::paques($year)->add($dateInterval);
    }

    /**
     * Return jour férié for Jour de l'An.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function jourDeLAn(int $year): ?DateTime
    {
        if ($year > 1810) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 1, 1);
        }

        return null;
    }

    /**
     * Return jour férié for Fête Du Travail.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function feteTravail(int $year): ?DateTime
    {
        if ($year > 1919) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 5, 1);
        }

        return null;
    }

    /**
     * Return jour férié for Victoire de 1945.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function victoire1945(int $year): ?DateTime
    {
        if (($year >= 1953 && $year <= 1959) || $year > 1981) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 5, 8);
        }

        return null;
    }

    /**
     * Return jour férié for Fête Nationale.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function feteNationale(int $year): ?DateTime
    {
        if ($year >= 1880) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 7, 14);
        }

        return null;
    }

    /**
     * Return jour férié for La Toussaint.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function toussaint(int $year): ?DateTime
    {
        if ($year >= 1802) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 11, 1);
        }

        return null;
    }

    /**
     * Return jour férié for Assomption.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function assomption(int $year): ?DateTime
    {
        if ($year >= 1802) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 8, 15);
        }

        return null;
    }

    /**
     * Return jour férié for Armistice.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function armistice(int $year): ?DateTime
    {
        if ($year >= 1918) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 11, 11);
        }

        return null;
    }

    /**
     * Return jour férié for Noël.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function noel(int $year): ?DateTime
    {
        if ($year >= 1802) {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 12, 25);
        }

        return null;
    }

    /**
     * Return jour férié for 2e jour de Noël.
     *
     * @param int $year
     * @param string $zone geographic zone for férié days
     * @return DateTime|null
     */
    public static function deuxiemeJourDeNoel(int $year, string $zone = 'Métropole'): ?DateTime
    {
        $zone = self::checkZone($zone);
        if ($zone === 'Alsace-Moselle') {
            $date = self::normalizeDateTime(new DateTime());

            return  $date->setDate($year, 12, 26);
        }

        return null;
    }
}
