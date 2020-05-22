<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Idee;

use App\Domain\Idee\Idee;
use App\Domain\Utilisateur\Utilisateur;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="tkdo_idee")
 */
class DoctrineIdee implements Idee
{
    /**
     * @var int
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var Utilisateur
     * @ManyToOne(targetEntity="App\Infrastructure\Persistence\Utilisateur\DoctrineUtilisateur")
     * @todo intégrer à la PK (PK = utilisateur, id)
     */
    private $utilisateur;

    /**
     * @var string
     * @Column()
     */
    private $description;

    /**
     * @var Utilisateur
     * @ManyToOne(targetEntity="App\Infrastructure\Persistence\Utilisateur\DoctrineUtilisateur")
     */
    private $auteur;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    private $dateProposition;

    public function __construct(?int $id = NULL)
    {
        if (isset($id)) $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuteur(): Utilisateur
    {
        return $this->auteur;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateProposition(): \DateTime
    {
        return $this->dateProposition;
    }

    /**
     * {@inheritdoc}
     */
    public function setUtilisateur(Utilisateur $utilisateur): Idee
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): Idee
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAuteur(Utilisateur $auteur): Idee
    {
        $this->auteur = $auteur;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDateProposition(\DateTime $dateProposition): Idee
    {
        $this->dateProposition = $dateProposition;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __clone() {
        if (isset($this->utilisateur)) $this->utilisateur = clone $this->utilisateur;
        if (isset($this->auteur)) $this->auteur = clone $this->auteur;
        if (isset($this->dateProposition)) $this->dateProposition = clone $this->dateProposition;
    }
}
