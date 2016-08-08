<?php
namespace DrdPlus\Person;

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Entity\Entity;
use Drd\Genders\Gender;
use DrdPlus\Codes\MeleeWeaponCode;
use DrdPlus\Health\Health;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Person\Background\Background;
use DrdPlus\GamingSession\Memories;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\Skills\PersonSkills;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\PropertiesByLevels\PropertiesByLevels;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Races\Race;
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
     * @var Gender
     * @ORM\Column(type="gender")
     */
    private $gender;
    /**
     * @var Exceptionality
     * @ORM\OneToOne(targetEntity="DrdPlus\Exceptionalities\Exceptionality", cascade={"persist"})
     */
    private $exceptionality;
    /**
     * @var PropertiesByLevels
     * Does not need Doctrine annotation - it is just an on-demand built container
     */
    private $propertiesByLevels;
    /**
     * @var ProfessionLevels
     * @ORM\OneToOne(targetEntity="DrdPlus\Person\ProfessionLevels\ProfessionLevels", cascade={"persist"})
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
     * @ORM\OneToOne(targetEntity="DrdPlus\Person\Background\Background", cascade={"persist"})
     */
    private $background;
    /**
     * @var PersonSkills
     * @ORM\OneToOne(targetEntity="DrdPlus\Person\Skills\PersonSkills", cascade={"persist"})
     */
    private $personSkills;
    /**
     * @var WeightInKg
     * @ORM\Column(type="weight_in_kg")
     */
    private $weightInKgAdjustment;
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
     * Person constructor.
     * @param Race $race
     * @param Gender $gender
     * @param Name $name
     * @param Exceptionality $exceptionality
     * @param Memories $memories
     * @param ProfessionLevels $professionLevels
     * @param Background $background
     * @param PersonSkills $personSkills
     * @param WeightInKg $weightInKgAdjustment
     * @param HeightInCm $heightInCm
     * @param Age $age
     * @param Tables $tables
     */
    public function __construct(
        Race $race, // enum
        Gender $gender, // enum
        Name $name, // enum
        Exceptionality $exceptionality, // entity
        Memories $memories, // entity
        ProfessionLevels $professionLevels, // entity
        Background $background, // entity
        PersonSkills $personSkills, // entity
        WeightInKg $weightInKgAdjustment, // value
        HeightInCm $heightInCm, // value
        Age $age, // value
        Tables $tables // data helper
    )
    {
        $this->race = $race;
        $this->gender = $gender;
        $this->name = $name;
        $this->exceptionality = $exceptionality;
        $this->checkLevelsAgainstExperiences(
            $professionLevels,
            $memories,
            $tables->getExperiencesTable()
        );
        $this->memories = $memories;
        $this->professionLevels = $professionLevels;
        $this->background = $background;
        $this->weightInKgAdjustment = $weightInKgAdjustment;
        $this->heightInCm = $heightInCm;
        $this->age = $age;
        $this->personSkills = $personSkills;
        $this->health = new Health(new WoundBoundary($this->getPropertiesByLevels($tables)->getToughness(), $tables->getWoundsTable()));
        $this->stamina = new Stamina();
    }

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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Name is an enum, therefore de facto a constant, therefore only way how to change the name is to replace it
     *
     * @param Name $name
     *
     * @return $this
     */
    public function setName(Name $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Race
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @return Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return Exceptionality
     */
    public function getExceptionality()
    {
        return $this->exceptionality;
    }

    /**
     * @return Memories
     */
    public function getMemories()
    {
        return $this->memories;
    }

    /**
     * @return Health
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * @return Stamina
     */
    public function getStamina()
    {
        return $this->stamina;
    }

    /**
     * @return ProfessionLevels
     */
    public function getProfessionLevels()
    {
        return $this->professionLevels;
    }

    /**
     * @return Background
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @return PersonSkills
     */
    public function getPersonSkills()
    {
        return $this->personSkills;
    }

    /**
     * Those are lazy loaded and re-calculated on every entity reload if those requested
     *
     * @param Tables $tables
     * @return PropertiesByLevels
     */
    public function getPropertiesByLevels(Tables $tables)
    {
        if ($this->propertiesByLevels === null) {
            $this->propertiesByLevels = new PropertiesByLevels( // enums aggregate
                $this->getRace(),
                $this->getGender(),
                $this->getExceptionality()->getExceptionalityProperties(),
                $this->getProfessionLevels(),
                $this->weightInKgAdjustment,
                $this->heightInCm,
                $this->age,
                $tables
            );
        }

        return $this->propertiesByLevels;
    }

    /**
     * @return \DrdPlus\Professions\Profession
     */
    public function getProfession()
    {
        return $this->getProfessionLevels()->getFirstLevel()->getProfession();
    }

    /**
     * @param MeleeWeaponCode $meleeWeaponCode
     * @param Tables $tables
     * @return int
     */
    public function getMalusToFightNumberWithMeleeWeapon(MeleeWeaponCode $meleeWeaponCode, Tables $tables)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return
            $this->getPersonSkills()->getMalusToFightNumber(
                $meleeWeaponCode,
                $tables->getMissingWeaponSkillsTable()
            )
            + $tables->getArmourer()->getMeleeWeaponFightNumberMalus(
                $meleeWeaponCode,
                $this->getPropertiesByLevels($tables)->getStrength()->getValue()
            );
    }

    /**
     * @param MeleeWeaponCode $meleeWeaponCode
     * @param Tables $tables
     * @return int
     */
    public function getMalusToAttackNumberWithMeleeWeapon(MeleeWeaponCode $meleeWeaponCode, Tables $tables)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return
            $this->getPersonSkills()->getMalusToAttackNumber(
                $meleeWeaponCode,
                $tables->getMissingWeaponSkillsTable()
            )
            + $tables->getArmourer()->getMeleeWeaponAttackNumberMalus(
                $meleeWeaponCode,
                $this->getPropertiesByLevels($tables)->getStrength()->getValue()
            );
    }

    /**
     * @param MeleeWeaponCode $meleeWeaponCode
     * @param Tables $tables
     * @return int
     */
    public function getMalusToDefenseNumberWithMeleeWeapon(MeleeWeaponCode $meleeWeaponCode, Tables $tables)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return
            $this->getPersonSkills()->getMalusToCover(
                $meleeWeaponCode,
                $tables->getMissingWeaponSkillsTable()
            )
            + $tables->getArmourer()->getMeleeWeaponDefenseNumberMalus(
                $meleeWeaponCode,
                $this->getPropertiesByLevels($tables)->getStrength()->getValue()
            );
    }

    /**
     * @param MeleeWeaponCode $meleeWeaponCode
     * @param Tables $tables
     * @return int
     */
    public function getMalusToBaseOfWoundsWithMeleeWeapon(MeleeWeaponCode $meleeWeaponCode, Tables $tables)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return
            $this->getPersonSkills()->getMalusToBaseOfWounds(
                $meleeWeaponCode,
                $tables->getMissingWeaponSkillsTable()
            )
            + $tables->getArmourer()->getMeleeWeaponBaseOfWoundsMalus(
                $meleeWeaponCode,
                $this->getPropertiesByLevels($tables)->getStrength()->getValue()
            );
    }

}