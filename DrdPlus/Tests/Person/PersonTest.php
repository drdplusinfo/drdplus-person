<?php
namespace DrdPlus\Tests\Person;

use Drd\Genders\Gender;
use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Codes\RaceCodes;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Exceptionalities\Properties\ExceptionalityProperties;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Person\Background\Background;
use DrdPlus\Person\Background\BackgroundParts\BackgroundSkillPoints;
use DrdPlus\Person\GamingSession\Memories;
use DrdPlus\Person\Person;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\Skills\PersonSkills;
use DrdPlus\PersonProperties\PersonProperties;
use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Measurements\Experiences\Experiences;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class PersonTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $person = new Person(
            $race = $this->createRace(),
            $gender = $this->createGender(),
            $name = $this->createName(),
            $exceptionality = $this->createExceptionality(),
            $memories = $this->createMemories(),
            $professionLevels = $this->createProfessionLevels(),
            $background = $this->createBackground(),
            $personSkills = $this->createPersonSkills(),
            $weightInKgAdjustment = $this->createWeightInKgAdjustment(),
            $heightInCm = $this->createHeightInCm(),
            $age = $this->createAge(),
            new Tables()
        );
        self::assertNotNull($person);
        self::assertNull($person->getId());

        self::assertSame($race, $person->getRace());
        self::assertSame($gender, $person->getGender());
        self::assertSame($name, $person->getName());
        self::assertSame($exceptionality, $person->getExceptionality());
        self::assertSame($memories, $person->getMemories());
        self::assertSame($professionLevels, $person->getProfessionLevels());
        self::assertSame($background, $person->getBackground());
        self::assertSame($personSkills, $person->getPersonSkills());
        self::assertInstanceOf(
            PersonProperties::class,
            $personProperties = $person->getPersonProperties(new Tables())
        );
        self::assertSame(
            $personProperties,
            $personProperties = $person->getPersonProperties(new Tables()),
            'Same instance of person properties expected'
        );
        // note: tables are for inner purpose only, does not have getter
        self::assertSame($weightInKgAdjustment, $personProperties->getWeightInKgAdjustment());
        self::assertSame($professionLevels->getFirstLevel()->getProfession(), $person->getProfession());
        self::assertSame($heightInCm, $personProperties->getHeightInCm());
        self::assertSame($age, $personProperties->getAge());
    }

    /**
     * @test
     */
    public function I_can_change_name()
    {
        $person = new Person(
            $this->createRace(),
            $this->createGender(),
            $oldName = $this->createName(),
            $this->createExceptionality(),
            $this->createMemories(),
            $this->createProfessionLevels(),
            $this->createBackground(),
            $this->createPersonSkills(),
            $this->createWeightInKgAdjustment(),
            $this->createHeightInCm(),
            $this->createAge(),
            new Tables()
        );
        self::assertSame($oldName, $person->getName());
        NameType::registerSelf();
        $name = Name::getEnum($nameString = 'foo');
        self::assertNotSame($oldName, $name);
        $person->setName($name);
        self::assertSame($name, $person->getName());
        $person->setName($newName = Name::getEnum($newNameString = 'bar'));
        self::assertSame($newName, $person->getName());
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
     * @return Memories
     */
    private function createMemories()
    {
        $memories = $this->mockery(Memories::class);
        $memories->shouldReceive('getExperiences')
            ->andReturn($experiences = $this->mockery(Experiences::class));
        $experiences->shouldReceive('getValue')
            ->andReturn(0);

        return $memories;
    }

    /**
     * @param int $highestLevelRankValue = 1
     * @param string $professionCode
     * @return ProfessionLevels
     */
    private function createProfessionLevels($highestLevelRankValue = 1, $professionCode = ProfessionCodes::FIGHTER)
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);

        $professionLevels->shouldReceive('getFirstLevel')
            ->andReturn($firstLevel = $this->mockery(ProfessionLevel::class));
        $firstLevel->shouldReceive('getProfession')->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);

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

        $professionLevels->shouldReceive('getCurrentLevel')
            ->andReturn($currentLevel = $this->mockery(ProfessionLevel::class));
        $currentLevel->shouldReceive('getLevelRank')
            ->andReturn($highestLevelRank = $this->mockery(LevelRank::class));
        $highestLevelRank->shouldReceive('getValue')
            ->andReturn($highestLevelRankValue);

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
     * @param float $value
     * @return \Mockery\MockInterface|HeightInCm
     */
    private function createHeightInCm($value = 180.0)
    {
        $heightInCm = $this->mockery(HeightInCm::class);
        $heightInCm->shouldReceive('getValue')
            ->andReturn($value);

        return $heightInCm;
    }

    /**
     * @return \Mockery\MockInterface|Name
     */
    private function createName()
    {
        return $this->mockery(Name::class);
    }

    /**
     * @return \Mockery\MockInterface|Age
     */
    private function createAge()
    {
        return $this->mockery(Age::class);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\Exceptions\InsufficientExperiences
     */
    public function I_can_not_create_it_with_insufficient_experiences()
    {
        new Person(
            $this->createRace(),
            $this->createGender(),
            $this->createName(),
            $this->createExceptionality(),
            $this->createMemories(),
            $professionLevels = $this->createProfessionLevels(2 /* highest level rank */),
            $this->createBackground(),
            $this->createPersonSkills(),
            $this->createWeightInKgAdjustment(),
            $this->createHeightInCm(),
            $this->createAge(),
            new Tables()
        );
    }

}
