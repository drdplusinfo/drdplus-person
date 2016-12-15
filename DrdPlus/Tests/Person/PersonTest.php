<?php
namespace DrdPlus\Tests\Person;

use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\CurrentProperties\CurrentProperties;
use DrdPlus\Equipment\Equipment;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Exceptionalities\Properties\ExceptionalityProperties;
use DrdPlus\Health\Health;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Person\Background\Background;
use DrdPlus\Person\Background\BackgroundParts\BackgroundSkillPoints;
use DrdPlus\GamingSession\Memories;
use DrdPlus\Person\Person;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
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
use DrdPlus\PropertiesByLevels\PropertiesByLevels;
use DrdPlus\Races\Race;
use DrdPlus\Skills\Skills;
use DrdPlus\Stamina\Stamina;
use DrdPlus\Tables\Measurements\Experiences\Experiences;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class PersonTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $tables = new Tables();
        $person = new Person(
            $name = $this->createName(),
            $race = $this->createRace(),
            $gender = $this->createGender(),
            $exceptionality = $this->createExceptionality(),
            $memories = $this->createMemories(),
            $professionLevels = $this->createProfessionLevels(),
            $background = $this->createBackground(),
            $personSkills = $this->createSkills(),
            $weightInKgAdjustment = $this->createWeightInKgAdjustment(),
            $heightInCmAdjustment = $this->createHeightInCmAdjustment(),
            $age = $this->createAge(),
            $equipment = $this->createEquipment(
                BodyArmorCode::getIt(BodyArmorCode::HOBNAILED_ARMOR),
                HelmCode::getIt(HelmCode::CONICAL_HELM),
                RangedWeaponCode::getIt(RangedWeaponCode::JAVELIN),
                MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
                new Weight(1, Weight::KG, $tables->getWeightTable())
            ),
            $tables
        );
        self::assertNotNull($person);
        self::assertNull($person->getId());

        self::assertSame($race, $person->getRace());
        self::assertSame($gender, $person->getGenderCode());
        self::assertSame($name, $person->getName());
        self::assertSame($exceptionality, $person->getExceptionality());
        self::assertSame($memories, $person->getMemories());
        self::assertInstanceOf(Health::class, $person->getHealth());
        self::assertInstanceOf(Stamina::class, $person->getStamina());
        self::assertSame($memories, $person->getMemories());
        self::assertSame($professionLevels, $person->getProfessionLevels());
        self::assertSame($background, $person->getBackground());
        self::assertSame($personSkills, $person->getSkills());
        self::assertSame($equipment, $person->getEquipment());
        self::assertInstanceOf(
            PropertiesByLevels::class,
            $propertiesByLevels = $person->getPropertiesByLevels($tables)
        );
        self::assertSame(
            $propertiesByLevels,
            $propertiesByLevels = $person->getPropertiesByLevels($tables),
            'Same instance of person properties expected'
        );
        // note: tables are for inner purpose only, does not have getter
        self::assertSame($weightInKgAdjustment, $propertiesByLevels->getWeightInKgAdjustment());
        self::assertSame($professionLevels->getFirstLevel()->getProfession(), $person->getProfession());
        self::assertSame($heightInCmAdjustment, $propertiesByLevels->getHeightInCmAdjustment());
        self::assertSame($age, $propertiesByLevels->getAge());
        self::assertInstanceOf(
            CurrentProperties::class,
            $currentProperties = $person->getCurrentProperties($tables)
        );
        $currentPropertiesReflection = new \ReflectionClass(CurrentProperties::class);
        $valuesFromCurrentProperties = [];
        foreach ($currentPropertiesReflection->getProperties() as $property) {
            $property->setAccessible(true);
            $valuesFromCurrentProperties[] = $property->getValue($currentProperties);
        }
        self::assertNotEmpty($valuesFromCurrentProperties);
        $expectedValuesInCurrentProperties = [
            $propertiesByLevels,
            $person->getHealth(),
            $person->getRace(),
            $equipment->getWornBodyArmor(),
            $equipment->getWornHelm(),
            $equipment->getWeight($tables->getWeightTable()),
        ];
        foreach ($expectedValuesInCurrentProperties as $expectedValueInCurrentProperties) {
            self::assertContains($expectedValueInCurrentProperties, $valuesFromCurrentProperties);
        }
    }

    /**
     * @test
     */
    public function I_can_change_name()
    {
        $person = new Person(
            $oldName = $this->createName(),
            $this->createRace(),
            $this->createGender(),
            $this->createExceptionality(),
            $this->createMemories(),
            $this->createProfessionLevels(),
            $this->createBackground(),
            $this->createSkills(),
            $this->createWeightInKgAdjustment(),
            $this->createHeightInCmAdjustment(),
            $this->createAge(),
            $this->createEquipment(),
            new Tables()
        );
        self::assertSame($oldName, $person->getName());
        NameType::registerSelf();
        $name = new Name($nameString = 'foo');
        self::assertNotSame($oldName, $name);
        $person->setName($name);
        self::assertSame($name, $person->getName());
        $person->setName($newName = new Name($newNameString = 'bar'));
        self::assertSame($newName, $person->getName());
    }

    /**
     * @return Race|\Mockery\MockInterface
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
        $race->shouldReceive('getHeightInCm')
            ->andReturn(0);
        $race->shouldReceive('getSize')
            ->andReturn(0);
        $race->shouldReceive('getRaceCode')
            ->andReturn(RaceCode::getIt(RaceCode::HUMAN));
        $race->shouldReceive('getSubraceCode')
            ->andReturn(SubRaceCode::getIt(SubRaceCode::COMMON));
        $race->shouldReceive('getSenses')
            ->andReturn(0);

        return $race;
    }

    /**
     * @return GenderCode|\Mockery\MockInterface
     */
    private function createGender()
    {
        return $this->mockery(GenderCode::class);
    }

    /**
     * @return Exceptionality|\Mockery\MockInterface
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
     * @return Memories|\Mockery\MockInterface
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
     * @return ProfessionLevels|\Mockery\MockInterface
     */
    private function createProfessionLevels($highestLevelRankValue = 1, $professionCode = ProfessionCode::FIGHTER)
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
     * @return \Mockery\MockInterface|Skills
     */
    private function createSkills()
    {
        return $this->mockery(Skills::class);
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
    private function createHeightInCmAdjustment($value = 180.0)
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
     * @param $armor
     * @param $helm
     * @param $mainHandWeaponlike
     * @param $offhandWeaponlike
     * @param $weight
     * @return \Mockery\MockInterface|Equipment
     */
    private function createEquipment($armor = null, $helm = null, $mainHandWeaponlike = null, $offhandWeaponlike = null, $weight = null)
    {
        $equipment = $this->mockery(Equipment::class);
        if ($armor !== null) {
            $equipment->shouldReceive('getWornBodyArmor')
                ->andReturn($armor);
        }
        if ($helm !== null) {
            $equipment->shouldReceive('getWornHelm')
                ->andReturn($helm);
        }
        if ($mainHandWeaponlike !== null) {
            $equipment->shouldReceive('getMainHandWeaponlike')
                ->andReturn($mainHandWeaponlike);
        }
        if ($offhandWeaponlike !== null) {
            $equipment->shouldReceive('getOffhandWeaponlike')
                ->andReturn($offhandWeaponlike);
        }
        if ($weight !== null) {
            $equipment->shouldReceive('getWeight')
                ->with($this->type(WeightTable::class))
                ->andReturn($weight);
        }

        return $equipment;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\Exceptions\InsufficientExperiences
     */
    public function I_can_not_create_person_with_insufficient_experiences()
    {
        new Person(
            $this->createName(),
            $this->createRace(),
            $this->createGender(),
            $this->createExceptionality(),
            $this->createMemories(),
            $professionLevels = $this->createProfessionLevels(2 /* highest level rank */),
            $this->createBackground(),
            $this->createSkills(),
            $this->createWeightInKgAdjustment(),
            $this->createHeightInCmAdjustment(),
            $this->createAge(),
            $this->createEquipment(),
            new Tables()
        );
    }

}