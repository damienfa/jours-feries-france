<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JoursAdministratifsTest extends TestCase
{
    /**
     * testAddJourCalendaire.
     *
     * @testdox Adding JourCalendaire days should return new datetime `x` days in the future
     */
    public function testAddJourCalendaire(): void
    {
        $this->assertEquals(
            new DateTime('30-12-2017'),
            JoursAdministratifs::addJourCalendaire(new DateTime('20-12-2017'), 10)
        );
    }

    /**
     * testSubJourCalendaire.
     *
     * @testdox Subtracting JourCalendaire days should return new datetime `x` days in the past
     */
    public function testSubJourCalendaire(): void
    {
        $this->assertEquals(
            new DateTime('20-12-2017'),
            JoursAdministratifs::subJourCalendaire(new DateTime('30-12-2017'), 10)
        );
    }

    /**
     * testAddJourOuvrable.
     *
     * @testdox Adding JourOuvrable days should return new datetime `x` days in the future skipping sundays and férié
     */
    public function testAddJourOuvrable(): void
    {
        $this->assertEquals(
            new DateTime('03-01-2018'),
            JoursAdministratifs::addJourOuvrable(new DateTime('20-12-2017'), 10)
        );
    }

    /**
     * testAddJourOuvrable in Alsace-Moselle.
     *
     * @testdox Adding JourOuvrable days in Alsace-Moselle during Noël should return 1 day later than Métropole
     */
    public function testAddJourOuvrableAM(): void
    {
        $this->assertEquals(
            new DateTime('04-01-2018'),
            JoursAdministratifs::addJourOuvrable(new DateTime('20-12-2017'), 10, 'Alsace-Moselle')
        );
    }

    /**
     * testSubJourOuvrable.
     *
     * @testdox Subtracting JourOuvrable days should return new datetime `x` days in the past skipping sundays and férié
     */
    public function testSubJourOuvrable(): void
    {
        $this->assertEquals(
            new DateTime('18-12-2017'),
            JoursAdministratifs::subJourOuvrable(new DateTime('30-12-2017'), 10)
        );
    }

    /**
     * testAddJourOuvre.
     *
     * @testdox Adding JourOuvre days should return new datetime `x` days in the future skipping saturday, sundays and férié
     */
    public function testAddJourOuvre(): void
    {
        $this->assertEquals(
            new DateTime('05-01-2018'),
            JoursAdministratifs::addJourOuvre(new DateTime('20-12-2017'), 10)
        );
    }

    /**
     * testAddJourOuvre in Alsace-Moselle.
     *
     * @testdox Adding JourOuvre days in Alsace-Moselle during Noël should return 3 day later than Métropole (because there's a week-end)
     */
    public function testAddJourOuvreAM(): void
    {
        $this->assertEquals(
            new DateTime('08-01-2018'),
            JoursAdministratifs::addJourOuvre(new DateTime('20-12-2017'), 10, 'Alsace-Moselle')
        );
    }

    /**
     * testSubJourOuvre.
     *
     * @testdox Subtracting JourOuvre days should return new datetime `x` days in the past skipping saturday, sundays and férié
     */
    public function testSubJourOuvre(): void
    {
        $this->assertEquals(
            new DateTime('15-12-2017'),
            JoursAdministratifs::subJourOuvre(new DateTime('30-12-2017'), 10)
        );
    }
}
