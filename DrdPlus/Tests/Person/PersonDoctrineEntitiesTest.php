<?php
namespace DrdPlus\Tests\Person;

use Doctrineum\Tests\Entity\AbstractDoctrineEntitiesTest;
use Drd\Genders\Male;
use DrdPlus\Exceptionalities\Choices\PlayerDecision;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Exceptionalities\Fates\FateOfGoodRear;
use DrdPlus\Exceptionalities\Properties\ExceptionalityPropertiesFactory;
use DrdPlus\Health\Health;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Person\Background\Background;
use DrdPlus\Person\EnumTypes\PersonEnumRegistrar;
use DrdPlus\GamingSession\Adventure;
use DrdPlus\GamingSession\GamingSession;
use DrdPlus\GamingSession\GamingSessionCategoryExperiences;
use DrdPlus\GamingSession\Memories;
use DrdPlus\Person\Person;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\Skills;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Professions\Fighter;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Races\Humans\CommonHuman;
use DrdPlus\Stamina\Stamina;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Exceptionalities\ExceptionalitiesDoctrineEntitiesTest;
use DrdPlus\Tests\Health\HealthDoctrineEntitiesTest;
use DrdPlus\Tests\Person\Background\PersonBackgroundDoctrineEntitiesTest;
use DrdPlus\Tests\Person\ProfessionLevels\ProfessionLevelsDoctrineEntitiesTest;
use DrdPlus\Tests\Skills\SkillsDoctrineEntitiesTest;
use DrdPlus\Tests\Stamina\StaminaDoctrineEntitiesTest;

class PersonDoctrineEntitiesTest extends AbstractDoctrineEntitiesTest
{
    protected function setUp()
    {
        PersonEnumRegistrar::registerAll();
        parent::setUp();
    }

    protected function getDirsWithEntities()
    {
        $personReflection = new \ReflectionClass(Person::class);
        $personSkillReflection = new \ReflectionClass(Skill::class);
        $backgroundReflection = new \ReflectionClass(Background::class);
        $gamingSessionReflection = new \ReflectionClass(GamingSession::class);
        $professionLevelReflection = new \ReflectionClass(ProfessionLevel::class);
        $exceptionalityReflection = new \ReflectionClass(Exceptionality::class);
        $staminaEntityReflection = new \ReflectionClass(Stamina::class);
        $healthEntityReflection = new \ReflectionClass(Health::class);

        return [
            dirname($personReflection->getFileName()),
            dirname($personSkillReflection->getFileName()),
            dirname($backgroundReflection->getFileName()),
            dirname($gamingSessionReflection->getFileName()),
            dirname($professionLevelReflection->getFileName()),
            dirname($exceptionalityReflection->getFileName()),
            dirname($staminaEntityReflection->getFileName()),
            dirname($healthEntityReflection->getFileName()),
        ];
    }

    protected function getExpectedEntityClasses()
    {
        return [Person::class];
    }

    protected function createEntitiesToPersist()
    {
        $tables = new Tables();
        $exceptionalityPropertiesFactory = new ExceptionalityPropertiesFactory();

        return array_merge(
            (new SkillsDoctrineEntitiesTest())->createEntitiesToPersist(),
            (new HealthDoctrineEntitiesTest())->createEntitiesToPersist(),
            (new StaminaDoctrineEntitiesTest())->createEntitiesToPersist(),
            [
                $this->createPersonEntity($tables, $exceptionalityPropertiesFactory),
                SkillsDoctrineEntitiesTest::createSkillsEntity($tables),
                PersonBackgroundDoctrineEntitiesTest::createBackgroundEntity(),
                new Memories(),
                new Adventure(new Memories(), 'foo'),
                new GamingSession(
                    new Adventure(new Memories(), 'bar'),
                    GamingSessionCategoryExperiences::getIt(0),
                    GamingSessionCategoryExperiences::getIt(1),
                    GamingSessionCategoryExperiences::getIt(2),
                    GamingSessionCategoryExperiences::getIt(3),
                    GamingSessionCategoryExperiences::getIt(2),
                    'baz'
                ),
            ],
            ExceptionalitiesDoctrineEntitiesTest::createEntities(),
            ProfessionLevelsDoctrineEntitiesTest::createEntities()
        );
    }

    private function createPersonEntity(
        Tables $tables,
        ExceptionalityPropertiesFactory $exceptionalityPropertiesFactory
    )
    {
        return new Person(
            CommonHuman::getIt(),
            Male::getIt(),
            Name::getIt('foo'),
            new Exceptionality(
                PlayerDecision::getIt(),
                $fate = FateOfGoodRear::getIt(),
                $exceptionalityPropertiesFactory->createChosenProperties(
                    $fate,
                    $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(Fighter::getIt()),
                    0,
                    1,
                    1,
                    0,
                    1,
                    0
                )
            ),
            new Memories(),
            $professionLevels = new ProfessionLevels($professionFirstLevel),
            $background = Background::createIt(
                $fate,
                4,
                3,
                5
            ),
            Skills::createSkills(
                $professionLevels,
                $background->getBackgroundSkillPoints(),
                $tables,
                new PhysicalSkills(),
                new PsychicalSkills(),
                new CombinedSkills()
            ),
            WeightInKg::getIt(123.45),
            HeightInCm::getIt(78.89),
            Age::getIt(56),
            $tables
        );
    }
}