<?php
// src/AppBundle/Entity/UserRole.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRoleRepository")
 * @ORM\Table(name="userrole")
 * @ORM\HasLifecycleCallbacks
 */
class UserRole implements RoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userroles")
     */
    protected $users;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $role;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":1})
     */
    protected $status = 1;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;
    
    
    
    public function __construct()
    {
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    	$this->users = new ArrayCollection();
    }
    
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
    	$this->setUpdated(new \DateTime());
    }
    
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getRole() {
		return $this->role;
	}
	public function setRole($role) {
		$this->role = $role;
		return $this;
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
	public function getUsers() {
		return $this->users;
	}
	public function setUsers($users) {
		$this->users = $users;
		return $this;
	}
	
	
	public function addUser(User $user)
	{
		$this->users[] = $user;
		return $this;
	}
	
	public function removeUser(User $user)
	{
		$this->users->removeElement($user);
		return $this;
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	
	
    
        
}
