<?php

namespace App\Entity;

use App\Entity\Traits\TimestampeableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity({"username"})
 */
class User implements UserInterface
{
    use TimestampeableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message = "The username could not be blank"
     * )
     * @Assert\Length(
     *     min = 5,
     *     max = 15,
     *     minMessage = "The username should contain at least {{ limit }} characters",
     *     maxMessage = "The username should contain a maximum of {{ limit }} characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message = "The password could not be blank"
     * )
     * @Assert\Length(
     *     min = 50,
     *     max = 255,
     *     minMessage = "The password should contain at least {{ limit }} characters",
     *     maxMessage = "The password should contain a maximum of {{ limit }} characters"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^(\w{3,20})?$/u",
     *     message = "The firstname should contain between 3 and 20 characters and must not include special characters"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^(\w{3,20})?$/u",
     *     message = "The lastname should contain between 3 and 20 characters and must not include special characters"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^((https:\/\/)?github\.com\/.*)?$/",
     *     message = "The url is not valid"
     * )
     */
    private $github;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *     type = "bool"
     * )
     */
    private $isEnable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="user", orphanRemoval=true)
     */
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="user", orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $agreeTerms;

    public function __construct()
    {
        $this->answers   = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->isEnable  = true;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getIsEnable(): ?bool
    {
        return $this->isEnable;
    }

    public function setIsEnable(bool $isEnable): self
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setUser($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getUser() === $this) {
                $answer->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setUser($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getUser() === $this) {
                $question->setUser(null);
            }
        }

        return $this;
    }

    public function getAgreeTerms(): ?\DateTimeInterface
    {
        return $this->agreeTerms;
    }

    public function setAgreeTerms(\DateTimeInterface $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;

        return $this;
    }
}
