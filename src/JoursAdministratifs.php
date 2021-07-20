<?php

class JoursAdministratifs
{
    private static function walkJours(DateTime $date, int $days, array $rejectDays, $rejectFeries, ?string $zone = 'Métropole'): DateTime
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

    public static function addJourCalendaire(DateTime $date, int $days): DateTime
    {
        // TODO : simplifier avec un simple appel à DateInterval ?
        return self::walkJours($date, $days, [], false);
    }

    public static function subJourCalendaire(DateTime $date, int $days): DateTime
    {
        // TODO : simplifier avec un simple appel à DateInterval ?
        return self::walkJours($date, -1 * abs($days), [], false);
    }

    public static function addJourOuvrable(DateTime $date, int $days, ?string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, $days, [7], true, $zone);
    }

    public static function subJourOuvrable(DateTime $date, int $days, ?string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, -1 * abs($days), [7], true, $zone);
    }

    public static function addJourOuvre(DateTime $date, int $days, ?string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, $days, [6, 7], true, $zone);
    }

    public static function subJourOuvre(DateTime $date, int $days, ?string $zone = 'Métropole'): DateTime
    {
        return self::walkJours($date, -1 * abs($days), [6, 7], true, $zone);
    }

    // TODO : ajouter le calcul des jours francs (quand j'aurais vraiment compris ce que c'est)
}
