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

    /**
     * testAddJourFranc.
     *
     * @testdox Adding JourFranc days should return calendar days + skipping days 6 ans 7 and férié for last day
     */
    public function testAddJourFranc(): void
    {
        $this->assertEquals(
            new DateTime('28-12-2020 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('21-12-2020'), 6)
        );
        $this->assertEquals(
            new DateTime('28-12-2020 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('21-12-2020'), 3)
        );
        // exemples pris ici :
        // https://www.service-public.fr/particuliers/vosdroits/F31111
        $this->assertEquals(
            new DateTime('21-12-2020 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('09-12-2020'), 10)
        );
        $this->assertEquals(
            new DateTime('11-12-2020 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('30-11-2020'), 10)
        );
        $this->assertEquals(
            new DateTime('28-12-2020 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('14-12-2020'), 10)
        );
    }

    /**
     * testAddJourFrancAM.
     *
     * @testdox Adding JourFranc days in Alsace Moselle should return 1 day later (because there is 1 more férié day after chirstmas, les chanceux)
     */
    public function testAddJourFrancAM(): void
    {
        // J'ai pris une année où Noël et le 2e jour de Noël ne tombent pas un samedi :)
        $this->assertEquals(
            new DateTime('28-12-2016 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('21-12-2016'), 6, 'Alsace-Moselle')
        );
        $this->assertEquals(
            new DateTime('27-12-2016 23:59:59'),
            JoursAdministratifs::addJourFranc(new DateTime('21-12-2016'), 3, 'Alsace-Moselle')
        );
    }
}
