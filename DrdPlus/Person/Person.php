<?php
namespace DrdPlus\Person;

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Entity\Entity;
use DrdPlus\Codes\GenderCode;
use DrdPlus\CurrentProperties\CurrentProperties;
use DrdPlus\Equipment\Equipment;
use DrdPlus\Health\Health;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Background\Background;
use DrdPlus\GamingSession\Memories;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Professions\Profession;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\PropertiesByLevels\PropertiesByLevels;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\BodyWeightInKg;
use DrdPlus\Races\Race;
use DrdPlus\Skills\Skills;
use DrdPlus\Stamina\Stamina;
use DrdPlus\Tables\Measurements\Experiences\ExperiencesTable;
use DrdPlus\Tables\Measurements\Experiences\Level as LevelBonus;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

/**
 * @ORM\Entity()
 */
class Person extends StrictObject implements Entity
{
    /**
     * @var integer
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var Name
     * @ORM\Column(type="name")
     */
    private $name;
    /**
     * @var Race
     * @ORM\Column(type="race")
     */
    private $race;
    /**
     * @var GenderCode
     * @ORM\Column(type="gender_code")
     */
    private $genderCode;
    /**
     * @var PropertiesByFate
     * @ORM\OneToOne(targetEntity="\DrdPlus\PropertiesByFate\PropertiesByFate", cascade={"persist"})
     */
    private $propertiesByFate;
    /**
     * @var PropertiesByLevels
     * Does not need Doctrine annotation - it is just an on-demand built container
     */
    private $propertiesByLevels;
    /**
     * @var ProfessionLevels
     * @ORM\OneToOne(targetEntity="\DrdPlus\Person\ProfessionLevels\ProfessionLevels", cascade={"persist"})
     */
    private $professionLevels;
    /**
     * @var Memories
     * @ORM\OneToOne(targetEntity="\DrdPlus\GamingSession\Memories", cascade={"persist"})
     */
    private $memories;
    /**
     * @var Health
     * @ORM\OneToOne(targetEntity="\DrdPlus\Health\Health", cascade={"persist"})
     */
    private $health;
    /**
     * @var Stamina
     * @ORM\OneToOne(targetEntity="\DrdPlus\Stamina\Stamina", cascade={"persist"})
     */
    private $stamina;
    /**
     * @var Background
     * @ORM\OneToOne(targetEntity="\DrdPlus\Background\Background", cascade={"persist"})
     */
    private $background;
    /**
     * @var Skills
     * @ORM\OneToOne(targetEntity="\DrdPlus\Skills\Skills", cascade={"persist"})
     */
    private $skills;
    /**
     * @var BodyWeightInKg
     * @ORM\Column(type="body_weight_in_kg")
     */
    private $bodyWeightInKgAdjustment;
    /**
     * @var HeightInCm
     * @ORM\Column(type="height_in_cm")
     */
    private $heightInCm;
    /**
     * @var Age
     * @ORM\Column(type="age")
     */
    private $age;
    /**
     * @var Equipment
     * @ORM\OneToOne(targetEntity="\DrdPlus\Equipment\Equipment", cascade={"persist"})
     */
    private $equipment;

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param Name $name
     * @param PropertiesByFate $propertiesByFate
     * @param Memories $memories
     * @param ProfessionLevels $professionLevels
     * @param Background $background
     * @param Skills $skills
     * @param BodyWeightInKg $weightInKgAdjustment
     * @param HeightInCm $heightInCm
     * @param Age $age
     * @param Equipment $equipment
     * @param Tables $tables
     * @throws \DrdPlus\Person\Exceptions\InsufficientExperiences
     */
    public function __construct(
        Name $name, // value
        Race $race, // enum (value)
        GenderCode $genderCode, // enum (value)
        PropertiesByFate $propertiesByFate, // entity
        Memories $memories, // entity
        ProfessionLevels $professionLevels, // entity
        Background $background, // entity
        Skills $skills, // entity
        BodyWeightInKg $weightInKgAdjustment, // enum (value)
        HeightInCm $heightInCm, // enum (value)
        Age $age, // enum (value)
        Equipment $equipment, // entity
        Tables $tables // data helper (can not be persisted)
    )
    {
        $this->name = $name;
        $this->race = $race;
        $this->genderCode = $genderCode;
        $this->propertiesByFate = $propertiesByFate;
        $this->checkLevelsAgainstExperiences(
            $professionLevels,
            $memories,
            $tables->getExperiencesTable()
        );
        $this->memories = $memories;
        $this->professionLevels = $professionLevels;
        $this->background = $background;
        $this->skills = $skills;
        $this->bodyWeightInKgAdjustment = $weightInKgAdjustment;
        $this->heightInCm = $heightInCm;
        $this->age = $age;
        $this->equipment = $equipment;
        $this->health = new Health();
        $this->stamina = new Stamina();
    }

    /**
     * @param ProfessionLevels $professionLevels
     * @param Memories $memories
     * @param ExperiencesTable $experiencesTable
     * @throws \DrdPlus\Person\Exceptions\InsufficientExperiences
     */
    private function checkLevelsAgainstExperiences(
        ProfessionLevels $professionLevels,
        Memories $memories,
        ExperiencesTable $experiencesTable
    )
    {
        $highestLevelRank = $professionLevels->getCurrentLevel()->getLevelRank();
        $requiredExperiences = $experiencesTable->toTotalExperiences(
            new LevelBonus($highestLevelRank->getValue(), $experiencesTable)
        );
        $availableExperiences = $memories->getExperiences($experiencesTable);
        if ($availableExperiences->getValue() < $requiredExperiences->getValue()) {
            throw new Exceptions\InsufficientExperiences(
                "Given level {$highestLevelRank} needs at least {$requiredExperiences} experiences, got only {$availableExperiences}"
            );
        }
    }

    public function getId():? int
    {
        return $this->id;
    }

    /**
     * Name is an enum, therefore a constant in fact, therefore only way how to change the name is to replace it
     *
     * @param Name $name
     * @return $this
     */
    public function setName(Name $name): Person
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Race
     */
    public function getRace(): Race
    {
        return $this->race;
    }

    /**
     * @return GenderCode
     */
    public function getGenderCode(): GenderCode
    {
        return $this->genderCode;
    }

    /**
     * @return PropertiesByFate
     */
    public function getPropertiesByFate(): PropertiesByFate
    {
        return $this->propertiesByFate;
    }

    /**
     * @return Memories
     */
    public function getMemories(): Memories
    {
        return $this->memories;
    }

    /**
     * @return Health
     */
    public function getHealth(): Health
    {
        return $this->health;
    }

    /**
     * @return Stamina
     */
    public function getStamina(): Stamina
    {
        return $this->stamina;
    }

    /**
     * @return ProfessionLevels
     */
    public function getProfessionLevels(): ProfessionLevels
    {
        return $this->professionLevels;
    }

    /**
     * @return Background
     */
    public function getBackground(): Background
    {
        return $this->background;
    }

    /**
     * @return Skills
     */
    public function getSkills(): Skills
    {
        return $this->skills;
    }

    /**
     * Those are lazy loaded (and re-calculated on every entity reload at first time requested)
     *
     * @param Tables $tables
     * @return PropertiesByLevels
     */
    public function getPropertiesByLevels(Tables $tables): PropertiesByLevels
    {
        if ($this->propertiesByLevels === null) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->propertiesByLevels = new PropertiesByLevels( // enums aggregate
                $this->getRace(),
                $this->getGenderCode(),
                $this->getPropertiesByFate(),
                $this->getProfessionLevels(),
                $this->bodyWeightInKgAdjustment,
                $this->heightInCm,
                $this->age,
                $tables
            );
        }

        return $this->propertiesByLevels;
    }

    /**
     * @param Tables $tables
     * @return CurrentProperties
     * @throws \DrdPlus\CurrentProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    public function getCurrentProperties(Tables $tables): CurrentProperties
    {
        return new CurrentProperties(
            $this->getPropertiesByLevels($tables),
            $this->getHealth(),
            $this->getRace(),
            $this->getEquipment()->getWornBodyArmor(),
            $this->getEquipment()->getWornHelm(),
            $this->getEquipment()->getWeight($tables->getWeightTable()),
            $tables
        );
    }

    /**
     * @return \DrdPlus\Professions\Profession
     */
    public function getProfession(): Profession
    {
        return $this->getProfessionLevels()->getFirstLevel()->getProfession();
    }

    /**
     * @return Equipment
     */
    public function getEquipment(): Equipment
    {
        return $this->equipment;
    }

}