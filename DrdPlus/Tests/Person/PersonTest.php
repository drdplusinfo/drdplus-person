<?php
namespace DrdPlus\Person;

use Drd\Genders\Gender;
use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Codes\RaceCodes;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Exceptionalities\Properties\ExceptionalityProperties;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Experiences\Experiences;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Person\Background\Background;
use DrdPlus\Person\Background\BackgroundSkillPoints;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\Skills\PersonSkills;
use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class PersonTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $instance = new Person(
            $this->createRace(),
            $this->createGender(),
            $this->createName(),
            $this->createExceptionality(),
            $this->createExperiences(),
            $professionLevels = $this->createProfessionLevels(),
            $this->createBackground(),
            $this->createPersonSkills(),
            $this->createWeightInKgAdjustment(),
            new Tables()
        );
        self::assertNotNull($instance);
        self::assertNull($instance->getId());
    }

    /**
     * @test
     */
    public function returns_same_race_as_got()
    {
        $person = new Person(
            $race = $this->createRace(),
            $this->createGender(),
            $this->createName(),
            $this->createExceptionality(),
            $this->createExperiences(),
            $this->createProfessionLevels(),
            $this->createBackground(),
            $this->createPersonSkills(),
            $this->createWeightInKgAdjustment(),
            new Tables()
        );
        self::assertSame($race, $person->getRace());
    }

    /**
     * @test
     */
    public function returns_same_items_as_got()
    {
        $person = new Person(
            $race = $this->createRace(),
            $gender = $this->createGender(),
            $name = $this->createName(),
            $exceptionality = $this->createExceptionality(),
            $experiences = $this->createExperiences(),
            $professionLevels = $this->createProfessionLevels(),
            $background = $this->createBackground(),
            $skills = $this->createPersonSkills(),
            $weighInKgAdjustment = $this->createWeightInKgAdjustment(),
            new Tables()
        );
        self::assertSame($race, $person->getRace());
        self::assertSame($gender, $person->getGender());
        self::assertSame($name, $person->getName());
        self::assertSame($exceptionality, $person->getExceptionality());
        self::assertSame($experiences, $person->getExperiences());
        self::assertSame($professionLevels, $person->getProfessionLevels());
        self::assertSame($background, $person->getBackground());
        self::assertSame($skills, $person->getPersonSkills());
        self::assertSame($skills, $person->getPersonSkills());
        // note: tables are for inner purpose only, does not have getter
    }

    /**
     * @test
     */
    public function I_can_change_name()
    {
        $person = new Person(
            $this->createRace(),
            $this->createGender(),
            $this->createName(),
            $this->createExceptionality(),
            $this->createExperiences(),
            $this->createProfessionLevels(),
            $this->createBackground(),
            $this->createPersonSkills(),
            $this->createWeightInKgAdjustment(),
            new Tables()
        );
        NameType::registerSelf();
        $person->setName($name = Name::getEnum($nameString = 'foo'));
        self::assertSame($name, $person->getName());
        self::assertSame($nameString, (string)$person->getName());
        $person->setName($newName = Name::getEnum($newNameString = 'bar'));
        self::assertSame($newName, $person->getName());
        self::assertSame($newNameString, (string)$person->getName());
    }

    /**
     * @return Race
     */
    private function createRace()
    {
        $race = $this->mockery(Race::class);
        $race->shouldReceive('getProperty')
            ->andReturn(0);
        $race->shouldReceive('getStrengthModifier')
            ->andReturn(0);
        $race->shouldReceive('getAgilityModifier')
            ->andReturn(0);
        $race->shouldReceive('getKnackModifier')
            ->andReturn(0);
        $race->shouldReceive('getWillModifier')
            ->andReturn(0);
        $race->shouldReceive('getIntelligenceModifier')
            ->andReturn(0);
        $race->shouldReceive('getCharismaModifier')
            ->andReturn(0);
        $race->shouldReceive('getToughnessModifier')
            ->andReturn(0);
        $race->shouldReceive('getSizeModifier')
            ->andReturn(0);
        $race->shouldReceive('getSensesModifier')
            ->andReturn(0);
        $race->shouldReceive('getWeightInKg')
            ->andReturn(0);
        $race->shouldReceive('getSize')
            ->andReturn(0);
        $race->shouldReceive('getRaceCode')
            ->andReturn(RaceCodes::HUMAN);
        $race->shouldReceive('getSubraceCode')
            ->andReturn(RaceCodes::COMMON);
        $race->shouldReceive('getSenses')
            ->andReturn(0);

        return $race;
    }

    /**
     * @return Gender
     */
    private function createGender()
    {
        return $this->mockery(Gender::class);
    }

    /**
     * @return Exceptionality
     */
    private function createExceptionality()
    {
        $exceptionality = $this->mockery(Exceptionality::class);
        $exceptionality->shouldReceive('getExceptionalityProperties')
            ->andReturn($exceptionalityProperties = $this->mockery(ExceptionalityProperties::class));
        $exceptionalityProperties->shouldReceive('getProperty')
            ->andReturn($property = $this->mockery(BaseProperty::class));
        $property->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getStrength')
            ->andReturn($strength = $this->mockery(Strength::class));
        $strength->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getAgility')
            ->andReturn($agility = $this->mockery(Agility::class));
        $agility->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getKnack')
            ->andReturn($knack = $this->mockery(Knack::class));
        $knack->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getWill')
            ->andReturn($will = $this->mockery(Will::class));
        $will->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getIntelligence')
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $intelligence->shouldReceive('getValue')
            ->andReturn(0);
        $exceptionalityProperties->shouldReceive('getCharisma')
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $charisma->shouldReceive('getValue')
            ->andReturn(0);

        return $exceptionality;
    }

    /**
     * @return Experiences
     */
    private function createExperiences()
    {
        $experiences = $this->mockery(Experiences::class);
        $experiences->shouldReceive('getValue')
            ->andReturn(0);

        return $experiences;
    }

    /**
     * @return ProfessionLevels
     */
    private function createProfessionLevels()
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);

        $professionLevels->shouldReceive('getFirstLevel')
            ->andReturn($firstLevel = $this->mockery(ProfessionLevel::class));
        $firstLevel->shouldReceive('getProfession')->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn(ProfessionCodes::FIGHTER);

        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsPropertyModifier')
            ->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelStrengthModifier')
            ->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsStrengthModifier')
            ->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelAgilityModifier')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsAgilityModifier')->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelKnackModifier')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsKnackModifier')->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelWillModifier')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsWillModifier')->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelIntelligenceModifier')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsIntelligenceModifier')->andReturn(0);

        $professionLevels->shouldReceive('getFirstLevelCharismaModifier')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsCharismaModifier')->andReturn(0);

        $professionLevels->shouldReceive('getWeightKgModifierForFirstLevel')->andReturn(0);
        $professionLevels->shouldReceive('getNextLevelsWeightModifier')->andReturn(0);

        $professionLevels->shouldReceive('getHighestLevelRank')->andReturn($highestLevelRank = $this->mockery(LevelRank::class));
        $highestLevelRank->shouldReceive('getValue')
            ->andReturn(0);

        return $professionLevels;
    }

    /**
     * @return \Mockery\MockInterface|Background
     */
    private function createBackground()
    {
        $background = $this->mockery(Background::class);
        $background->shouldReceive('getBackgroundSkillPoints')
            ->andReturn($backgroundSkills = $this->mockery(BackgroundSkillPoints::class));

        return $background;
    }

    /**
     * @return \Mockery\MockInterface|PersonSkills
     */
    private function createPersonSkills()
    {
        return $this->mockery(PersonSkills::class);
    }

    /**
     * @param float $value
     * @return \Mockery\MockInterface|WeightInKg
     */
    private function createWeightInKgAdjustment($value = 0.0)
    {
        $weightInKg = $this->mockery(WeightInKg::class);
        $weightInKg->shouldReceive('getValue')
            ->andReturn($value);

        return $weightInKg;
    }

    /**
     * @return Name
     */
    private function createName()
    {
        return $this->mockery(Name::class);
    }
}
