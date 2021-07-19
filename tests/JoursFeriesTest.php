<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JoursFeriesTest extends TestCase
{
    /**
     * testBadZone.
     *
     * @testdox Non-existent zone should raise InvalidArgumentException
     */
    public function testBadZone(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        JoursFeries::forYear(2018, 'foo');
    }

    /**
     * testCorrectJoursFeries.
     *
     * @testdox isFerie should return true with a "correct" DateTime
     */
    public function testCorrectJoursFeries(): void
    {
        $this->assertTrue(JoursFeries::isFerie(new DateTime('25-12-2019 12:00:00'), 'Métropole'));
        $this->assertTrue(JoursFeries::isFerie(new DateTime('26-12-2019 12:00:00'), 'Alsace-Moselle'));
    }

    /**
     * testIncorrectJoursFeries.
     *
     * @testdox isFerie should return false with a "incorrect" DateTime
     */
    public function testIncorrectJoursFeries(): void
    {
        $this->assertFalse(JoursFeries::isFerie(new DateTime('26-12-2019 12:00:00')));
        $this->assertFalse(JoursFeries::isFerie(new DateTime('26-12-2019 12:00:00'), 'Métropole'));
    }

    /**
     * testNextJoursFeries.
     *
     * @testdox getNextFerie should return the next jour férié, provided a earlier DateTime
     */
    public function testNextFerie(): void
    {
        $this->assertEquals(
            ['Armistice', new DateTime('11-11-2018')],
            JoursFeries::getNextFerie(new DateTime('10-11-2018')),
        );
        $this->assertEquals(
            ['Armistice', new DateTime('11-11-2018')],
            JoursFeries::getNextFerie(new DateTime('11-11-2018')),
        );
        $this->assertEquals(
            ['Jour de Noël', new DateTime('25-12-2018')],
            JoursFeries::getNextFerie(new DateTime('11-12-2018')),
        );
    }

    /**
     * testPaques.
     *
     * @testdox paques() should return proper date for year
     */
    public function testPaques(): void
    {
        $this->assertEquals(
            new DateTime('18-04-1954'),
            JoursFeries::paques(1954),
        );
        $this->assertEquals(
            new DateTime('19-04-1981'),
            JoursFeries::paques(1981),
        );
        $this->assertEquals(
            new DateTime('18-04-2049'),
            JoursFeries::paques(2049),
        );
    }

    private function getAbolitionTestData()
    {
        return [
            'Mayotte' => new DateTime('27-04-2020'),
            'Martinique' => new DateTime('22-05-2020'),
            'Guadeloupe' => new DateTime('27-05-2020'),
            'Saint-Martin' => new DateTime('28-05-2020'),
            'Guyane' => new DateTime('10-06-2020'),
            'Saint-Barthélémy' => new DateTime('09-10-2020'),
            'La Réunion' => new DateTime('20-12-2020'),
        ];
    }

    /**
     * testZoneWithAbolition.
     *
     * @group abolitionEsclavage
     * @testdox abolitionEsclavage should return proper date in 2020 for each concerned zone
     */
    public function testZoneWithAbolition(): void
    {
        foreach ($this->getAbolitionTestData() as $zone => $expectedDate) {
            $this->assertEquals(
                $expectedDate,
                JoursFeries::abolitionEsclavage(2020, $zone)
            );
        }
    }

    /**
     * testZoneWithoutAbolition.
     *
     * @group abolitionEsclavage
     * @testdox abolitionEsclavage should return null for non-concerned zone
     */
    public function testZoneWithoutAbolition(): void
    {
        $concernedZones = array_keys($this->getAbolitionTestData());
        $nonConcernedZone = array_diff(JoursFeries::ZONES, $concernedZones);
        foreach ($nonConcernedZone as $zone) {
            $this->assertEquals(
                JoursFeries::abolitionEsclavage(2020, $zone),
                null
            );
        }
    }

    /**
     * testZoneWithoutAbolition.
     *
     * @group abolitionEsclavage
     * @testdox abolitionEsclavage should return different date for Saint-Martin depending if year is after or before 2018
     */
    public function testAbolitionInSaintMartin(): void
    {
        $this->assertEquals(
            JoursFeries::abolitionEsclavage(2017, 'Saint-Martin'),
            new DateTime('27-05-2017')
        );
        $this->assertEquals(
            JoursFeries::abolitionEsclavage(2018, 'Saint-Martin'),
            new DateTime('28-05-2018')
        );
    }

    /**
     * testForYear.
     *
     * @group forYear
     * @testdox forYear should return all jour fériés depending on year
     */
    public function testForYear(): void
    {
        $this->assertEqualsCanonicalizing(
            JoursFeries::forYear(2018),
            [
                'Jour de l’an' => new DateTime('01-01-2018'),
                'Lundi de Pâques' => new DateTime('02-04-2018'),
                'Fête du Travail' => new DateTime('01-05-2018'),
                'Victoire de 1945' => new DateTime('08-05-2018'),
                'Ascension' => new DateTime('10-05-2018'),
                'Lundi de Pentecôte' => new DateTime('21-05-2018'),
                'Fête Nationale' => new DateTime('14-07-2018'),
                'Assomption' => new DateTime('15-08-2018'),
                'Toussaint' => new DateTime('01-11-2018'),
                'Armistice' => new DateTime('11-11-2018'),
                'Jour de Noël' => new DateTime('25-12-2018'),
            ]
        );
        $this->assertEqualsCanonicalizing(
            JoursFeries::forYear(2020),
            [
                'Armistice' => new DateTime('11-11-2020'),
                'Ascension' => new DateTime('21-05-2020'),
                'Assomption' => new DateTime('15-08-2020'),
                'Fête Nationale' => new DateTime('14-07-2020'),
                'Fête du Travail' => new DateTime('01-05-2020'),
                'Jour de l’an' => new DateTime('01-01-2020'),
                'Lundi de Pâques' => new DateTime('13-04-2020'),
                'Jour de Noël' => new DateTime('25-12-2020'),
                'Lundi de Pentecôte' => new DateTime('01-06-2020'),
                'Toussaint' => new DateTime('01-11-2020'),
                'Victoire de 1945' => new DateTime('08-05-2020'),
            ]
        );
    }

    /**
     * testForYearInAlsace.
     *
     * @group forYear
     * @testdox forYear should return all jour fériés depending on year and Zone
     */
    public function testForYearInAlsace(): void
    {
        $this->assertEqualsCanonicalizing(

            [
                'Armistice' => new DateTime('11-11-2018'),
                'Ascension' => new DateTime('10-05-2018'),
                'Assomption' => new DateTime('15-08-2018'),
                'Fête Nationale' => new DateTime('14-07-2018'),
                'Fête du Travail' => new DateTime('01-05-2018'),
                'Jour de l’an' => new DateTime('01-01-2018'),
                'Lundi de Pâques' => new DateTime('02-04-2018'),
                'Jour de Noël' => new DateTime('25-12-2018'),
                'Lundi de Pentecôte' => new DateTime('21-05-2018'),
                'Toussaint' => new DateTime('01-11-2018'),
                'Victoire de 1945' => new DateTime('08-05-2018'),
                'Vendredi saint' => new DateTime('30-03-2018'),
                '2ème jour de Noël' => new DateTime('26-12-2018'),
            ],
            JoursFeries::forYear(2018, 'Alsace-Moselle')
        );
        $this->assertEqualsCanonicalizing(

            [
                'Armistice' => new DateTime('11-11-2020'),
                'Ascension' => new DateTime('21-05-2020'),
                'Assomption' => new DateTime('15-08-2020'),
                'Fête Nationale' => new DateTime('14-07-2020'),
                'Fête du Travail' => new DateTime('01-05-2020'),
                'Jour de l’an' => new DateTime('01-01-2020'),
                'Lundi de Pâques' => new DateTime('13-04-2020'),
                'Jour de Noël' => new DateTime('25-12-2020'),
                'Lundi de Pentecôte' => new DateTime('01-06-2020'),
                'Toussaint' => new DateTime('01-11-2020'),
                'Victoire de 1945' => new DateTime('08-05-2020'),
                'Vendredi saint' => new DateTime('10-04-2020'),
                '2ème jour de Noël' => new DateTime('26-12-2020'),
            ],
            JoursFeries::forYear(2020, 'Alsace-Moselle')
        );
    }

    /**
     * testNames.
     *
     * @testdox forYear should return specfic names depending on zone
     */
    public function testNames(): void
    {
        $base = [
            'Jour de l’an',
            'Fête du Travail',
            'Victoire de 1945',
            'Fête Nationale',
            'Assomption',
            'Toussaint',
            'Armistice',
            'Jour de Noël',
            'Lundi de Pâques',
            'Ascension',
            'Lundi de Pentecôte',
        ];
        $extra_holidays = [
            'Alsace-Moselle' => ['Vendredi saint', '2ème jour de Noël'],
            'Guadeloupe' => ["Abolition de l'esclavage"],
            'Guyane' => ["Abolition de l'esclavage"],
            'Martinique' => ["Abolition de l'esclavage"],
            'Mayotte' => ["Abolition de l'esclavage"],
            'Nouvelle-Calédonie' => [],
            'La Réunion' => ["Abolition de l'esclavage"],
            'Polynésie Française' => [],
            'Saint-Barthélémy' => ["Abolition de l'esclavage"],
            'Saint-Martin' => ["Abolition de l'esclavage"],
            'Wallis-et-Futuna' => [],
            'Saint-Pierre-et-Miquelon' => [],
        ];

        $this->assertEqualsCanonicalizing($base, array_keys(JoursFeries::forYear(2020)));

        foreach ($extra_holidays as $zone => $extra) {
            $this->assertEqualsCanonicalizing(
                array_merge($base, $extra),
                array_keys(JoursFeries::forYear(2020, $zone))

            );
        }
    }
}
