<?php
namespace DrdPlus\Person;

use Doctrine\ORM\Mapping as ORM;
use Drd\Genders\Gender;
use DrdPlus\Person\Attributes\Experiences;
use DrdPlus\Person\Attributes\Name;
use DrdPlus\Exceptionalities\Exceptionality;
use DrdPlus\Person\Background\Background;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\Skills\PersonSkills;
use DrdPlus\PersonProperties\PersonProperties;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Measurements\Experiences\ExperiencesTable;
use DrdPlus\Tables\Measurements\Experiences\Level as LevelBonus;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Person extends StrictObject
{
    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\OneToOne(targetEntity="DrdPlus\Exceptionalities\Exceptionality")
     */
    private $exceptionality;

    /**
     * @var PersonProperties
     * @ORM\OneToOne(targetEntity="DrdPlus\PersonProperties\PersonProperties")
     */
    private $personProperties;

    /**
     * @var ProfessionLevels
     * @ORM\OneToOne(targetEntity="DrdPlus\ProfessionLevels\ProfessionLevels")
     */
    private $professionLevels;

    /**
     * @var Experiences
     * @ORM\Column(type="experiences")
     */
    private $experiences;

    /**
     * @var Background
     * @ORM\OneToOne(targetEntity="DrdPlus\Person\Background\Background")
     */
    private $background;

    /**
     * @var PersonSkills
     * @ORM\OneToOne(targetEntity="DrdPlus\Person\Skills\PersonSkills")
     */
    private $personSkills;

    /**
     * @var WeightInKg
     * @ORM\Column(type="weight_in_kg")
     */
    private $weightInKgAdjustment;

    public function __construct(
        Race $race, // enum
        Gender $gender, // enum
        Name $name, // enum
        Exceptionality $exceptionality, // entity
        Experiences $experiences, // enum
        ProfessionLevels $professionLevels, // entity
        Background $background, // entity
        PersonSkills $skills, // entity
        WeightInKg $weightInKgAdjustment, // value
        Tables $tables // data helper
    )
    {
        $this->race = $race;
        $this->gender = $gender;
        $this->name = $name;
        $this->exceptionality = $exceptionality;
        $this->checkLevelsAgainstExperiences($professionLevels, $experiences, $tables->getExperiencesTable());
        $this->experiences = $experiences;
        $this->professionLevels = $professionLevels;
        $this->background = $background;
        $this->weightInKgAdjustment = $weightInKgAdjustment;
        $skills->checkSkillPoints(
            $this->getProfessionLevels(),
            $this->getBackground()->getBackgroundSkillPoints(),
            $this->getPersonProperties($tables)->getNextLevelsProperties(),
            $tables
        );
        $this->personSkills = $skills;
    }

    private function checkLevelsAgainstExperiences(
        ProfessionLevels $professionLevels,
        Experiences $experiences,
        ExperiencesTable $experiencesTable
    )
    {
        $highestLevel = $professionLevels->getHighestLevelRank();
        $requiredExperiences = $experiencesTable->toTotalExperiences(
            new LevelBonus($highestLevel->getValue(), $experiencesTable),
            true /* is main profession */
        );
        if ($experiences->getValue() < $requiredExperiences->getValue()) {
            throw new \LogicException(
                "Given level {$highestLevel} needs at least {$requiredExperiences}, got only {$experiences}"
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
     * @return Experiences
     */
    public function getExperiences()
    {
        return $this->experiences;
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
     * @return PersonProperties
     */
    public function getPersonProperties(Tables $tables)
    {
        if (!isset($this->personProperties)) {
            $this->personProperties = new PersonProperties( // enums aggregate
                $this->getRace(),
                $this->getGender(),
                $this->getExceptionality()->getExceptionalityProperties(),
                $this->getProfessionLevels(),
                $this->getWeightInKgAdjustment(),
                $tables
            );
        }

        return $this->personProperties;
    }

    /**
     * @return WeightInKg
     */
    public function getWeightInKgAdjustment()
    {
        return $this->weightInKgAdjustment;
    }

}
