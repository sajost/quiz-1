<?php
// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use AppBundle\Utils\Ses;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="unid",message="UNID existiert schon")
 * @UniqueEntity(fields="username",message="Benutzer-ID existiert schon")
 * @UniqueEntity(fields="email",message="E-mail existiert schon")
 */
class User implements AdvancedUserInterface, \Serializable {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="bigint")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=50, unique=true)
	 */
	protected $unid;
	
	/**
	 * @ORM\Column(type="string", length=20, unique=true)
	 * @Assert\Length(
	 * 			max = 16,
	 *			min = 2,
	 *			minMessage = "Passwort muss mindestens {{ limit }} Symbolen sein",
	 *			maxMessage = "Passwort muss maximal {{ limit }} Symbolen sein" 
	 * 		)
	 * 
	 * @Assert\NotBlank(message = "Benutzername darf nicht leer sein")
	 */
	protected $username;
	
	/**
	 * @ORM\Column(type="string", length=20)
	 * 
	 * @Assert\NotBlank(message = "Passwort darf nicht leer sein")
	 * @Assert\Length(
	 * 			max = 16,
	 *			min = 3,
	 *			minMessage = "Passwort muss mindestens {{ limit }} Symbolen sein",
	 *			maxMessage = "Passwort muss maximal {{ limit }} Symbolen sein" 
	 * 		)
	 */
	protected $password;
	
	/**
	 * @ORM\Column(type="string", length=64, unique=true)
	 * 
	 * @Assert\NotBlank(message = "Passwort darf nicht leer sein")
	 * @Assert\Length(
	 * 			max = 50,
	 *			min = 5,
	 *			minMessage = "Email muss mindestens {{ limit }} Symbolen sein",
	 *			maxMessage = "Email muss maximal {{ limit }} Symbolen sein" 
	 * 		)
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="string", length=20, nullable=true)
	 */
	protected $phone;
	
	/**
	 * @ORM\Column(type="smallint", nullable=true, options={"default":0})
	 */
	protected $status = 1;
	
	/**
	 * @ORM\Column(type="string", length=20,nullable=true)
	 */
	protected $fname;
	
	/**
	 * @ORM\Column(type="string", length=20,nullable=true)
	 */
	protected $lname;
	
	/**
	 * @ORM\Column(type="string", length=20,nullable=true)
	 */
	protected $tel1;
	
	/**
	 * @ORM\Column(type="string", length=20,nullable=true)
	 */
	protected $tel2;
	
	/**
	 * @ORM\Column(type="string", length=20,nullable=true)
	 */
	protected $tel3;
	
	/**
	 * @ORM\Column(type="smallint",nullable=true)
	 */
	protected $sex;
	
	/**
	 * @ORM\Column(type="datetime",nullable=true)
	 */
	protected $dborn;
	
	/**
	 * @ORM\Column(type="text",nullable=true)
	 */
	protected $about;
	
	/**
	 * @ORM\Column(type="string", length=50,nullable=true)
	 */
	protected $avatar;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $updated;
	
	/**
	 * Date/Time of the last activity
	 *
	 * @var \Datetime @ORM\Column(type="datetime", nullable=true)
	 */
	protected $lastact;
	
	/**
	 * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users")
	 * @ORM\JoinTable(name="users_roles")
	 */
	protected $userroles;
	
	/**
	 *
	 * @var string
	 */
	public $typ = 'user';
	public function __construct() {
		$this->userroles = new ArrayCollection();
		// $g = new RandomStringGenerator();
		// $this->setUnid($g->generate(32));//md5(uniqid('')));
		$this->setUnid ( Ses::uid ( 32 ) );
		$this->setCreated ( new \DateTime () );
		$this->setUpdated ( new \DateTime () );
	}
	
	/**
	 * @ORM\PreUpdate
	 */
	public function setUpdatedValue() {
		$this->setUpdated ( new \DateTime () );
	}
	public function getCreated() {
		return $this->created;
	}
	public function setCreated($created) {
		$this->created = $created;
		return $this;
	}
	public function getUpdated() {
		return $this->updated;
	}
	public function setUpdated($updated) {
		$this->updated = $updated;
		return $this;
	}
	public function getLastact() {
		return $this->lastact;
	}
	public function setLastact($lastact) {
		$this->lastact = $lastact;
		return $this;
	}
	
	/**
	 *
	 * @return Bool Whether the user is active or not
	 */
	public function isActiveNow() {
		// Delay during wich the user will be considered as still active
		$delay = new \DateTime ( '2 minutes ago' );
		return ($this->getLastact () > $delay);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		// you *may* need a real salt depending on your encoder
		// see section on salt below
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPassword() {
		return $this->password;
	}
	
	/**
	 * Overwrite it if roles in the DB
	 */
	public function getRoles()
	{
		$r = array();
		foreach ($this->getUserroles() as $ur) {
		$r[]=$ur->getRole();
		}
		return $r;
	}
	
	/**
	 * @inheritDoc
	 */
	public function eraseCredentials() {
	}
	public function isAccountNonExpired() {
		return true;
	}
	public function isAccountNonLocked() {
		return true;
	}
	public function isCredentialsNonExpired() {
		return true;
	}
	public function isEnabled() {
		return $this->status == 1; // && (time()-(60*60*48)) < $this->getCreated();
	}
	
	/**
	 *
	 * @see \Serializable::serialize()
	 */
	public function serialize() {
		return serialize ( array (
				$this->id,
				$this->username,
				$this->email,
				$this->password,
				$this->status 
		) );
	}
	
	/**
	 *
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized) {
		list ( $this->id, $this->username, $this->email, $this->password, $this->status, ) = unserialize ( $serialized );
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getUnid() {
		return $this->unid;
	}
	public function setUnid($unid) {
		$this->unid = $unid;
		return $this;
	}
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	public function getPhone() {
		return $this->phone;
	}
	public function setPhone($phone) {
		$this->phone = $phone;
		return $this;
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	public function getFname() {
		return $this->fname;
	}
	public function setFname($fname) {
		$this->fname = $fname;
		return $this;
	}
	public function getLname() {
		return $this->lname;
	}
	public function setLname($lname) {
		$this->lname = $lname;
		return $this;
	}
	public function getTel1() {
		return $this->tel1;
	}
	public function setTel1($tel1) {
		$this->tel1 = $tel1;
		return $this;
	}
	public function getTel2() {
		return $this->tel2;
	}
	public function setTel2($tel2) {
		$this->tel2 = $tel2;
		return $this;
	}
	public function getTel3() {
		return $this->tel3;
	}
	public function setTel3($tel3) {
		$this->tel3 = $tel3;
		return $this;
	}
	public function getSex() {
		return $this->sex;
	}
	public function setSex($sex) {
		$this->sex = $sex;
		return $this;
	}
	public function getDborn() {
		return $this->dborn;
	}
	public function setDborn($dborn) {
		$this->dborn = $dborn;
		return $this;
	}
	public function getAbout() {
		return $this->about;
	}
	public function setAbout($about) {
		$this->about = $about;
		return $this;
	}
	public function getAvatar() {
		return $this->avatar;
	}
	public function setAvatar($avatar) {
		$this->avatar = $avatar;
		return $this;
	}
	public function getTyp() {
		return $this->typ;
	}
	
	public function getUserroles() {
		return $this->userroles;
	}
	public function setUserroles($userroles) {
		$this->userroles = $userroles;
		return $this;
	}
	
	/**
	 * Add userroles
	 *
	 * @param \AppBundle\Entity\UserRole $userroles
	 * @return User
	 */
	public function addUserRole(\AppBundle\Entity\UserRole $userrole)
	{
		$userrole->addUser($this); // synchronously updating inverse side
		$this->userroles[] = $userrole;
		return $this;
	}
	
	/**
	 * Remove userroles
	 *
	 * @param \AppBundle\Entity\UserRole $userroles
	 */
	public function removeUserRole(\AppBundle\Entity\UserRole $userrole)
	{
		$userrole->removeUser($this);
		$this->userroles->removeElement($userrole);
		return $this;
	}	
	
	
	// public function __sleep()
	// {
	// return array('id', 'username', 'email', 'password', 'status');
	// }
}
