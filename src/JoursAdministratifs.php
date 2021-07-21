<?php

class JoursAdministratifs
{
    /**
     * Increments $date by $days days and return the result.
     *
     * @param DateTime $date DateTime to increment
     * @param int $days number of days to add (or sub if negative)
     * @param array $rejectDays array of days of the week (monday =1, sunday =7) that skips incrementing date
     * @param bool $rejectFeries if current day is ferié, skip incremeting
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    private static function walkJours(DateTime $date, int $days, array $rejectDays = [], bool $rejectFeries = true, string $zone = 'Métropole'): DateTime
    {
        $inc = 0;
        $dayInterval = DateInterval::createFromDateString('1 day');
        while ($inc < abs($days)) {
            if ($days > 0) {
                $date->add($dayInterval);
            } else {
                $date->sub($dayInterval);
            }
            if (
                (! $rejectFeries || ! JoursFeries::isFerie($date, $zone))
                && ! in_array($date->format('N'), $rejectDays)
            ) {
                $inc++;
            }
        }

        return $date;
    }

    /**
     * Returns DateTime set up from $date with $days in the future .
     *
     * @param DateTime $date starting date
     * @param int $days days to add
     * @return DateTime
     */
    public static function addJourCalendaire(DateTime $date, int $days): DateTime
    {
        return $date->add(DateInterval::createFromDateString($days.' day'));
    }

    /**
     * Returns DateTime set up from $date with $days in the past .
     *
     * @param DateTime $date starting date
     * @param int $days days to sub
     * @return DateTime
     */
    public static function subJourCalendaire(DateTime $date, int $days): DateTime
    {
        return self::addJourCalendaire($date, -1 * abs($days));
    }

    /**
     * Returns DateTime set up from $date with $days in the future not counting sundays and fériés.
     *
     * @param DateTime $date starting date
     * @param int $days days to add
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    public static function addJourOuvrable(DateTime $date, int $days, string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, $days, [7], true, $zone);
    }

    /**
     * Returns DateTime set up from $date with $days in the past not counting sundays and fériés.
     *
     * @param DateTime $date starting date
     * @param int $days days to sub
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    public static function subJourOuvrable(DateTime $date, int $days, string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, -1 * abs($days), [7], true, $zone);
    }

    /**
     * Returns DateTime set up from $date with $days in the future not counting saturdays, sundays and fériés.
     *
     * @param DateTime $date starting date
     * @param int $days days to add
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    public static function addJourOuvre(DateTime $date, int $days, string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, $days, [6, 7], true, $zone);
    }

    /**
     * Returns DateTime set up from $date with $days in the past not counting saturdays, sundays and fériés.
     *
     * @param DateTime $date starting date
     * @param int $days days to sub
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    public static function subJourOuvre(DateTime $date, int $days, string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, -1 * abs($days), [6, 7], true, $zone);
    }

    /**
     * Returns DateTime set up from $date with $days in the past not counting saturdays, sundays and fériés for the last day only.
     * see : https://www.demarches.interieur.gouv.fr/particuliers/jour-ouvrable-jour-ouvre-jour-franc-jour-calendaire-quelles-differences.
     *
     * @param DateTime $date starting date
     * @param int $days days to add
     * @param string $zone geographic zone for férié days (see JoursFeries::ZONES)
     * @return DateTime
     */
    public static function addJourFranc(DateTime $date, int $days, string $zone = 'Métropole'): DateTime
    {
        if ($days < 0) {
            throw new Exception("The number of days shouldn't be negative for jours francs, the results wouldn't be accurate.", 1);
        }
        $date->add(DateInterval::createFromDateString($days.' day'));

        return self::walkJours($date, 1, [6, 7], true, $zone)->setTime(23, 59, 59);
    }
}
