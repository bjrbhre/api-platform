<?php
// api/src/Entity/User.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ApiResource(
 *     normalizationContext={"groups"={"user", "user:read"}},
 *     denormalizationContext={"groups"={"user", "user:write"}},
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('ROLE_ADMIN')"},
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"},
 *     },
 *     itemOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER') and object == user"},
 *     },
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Groups({"user"})
     */
    protected $email;

    /**
     * @Groups({"user:write"})
     */
    protected $plainPassword;

    /**
     * @Groups({"user:read"})
     */
    protected $roles;

    /**
     * @Groups({"user:read"})
     */
    protected $username;

    public function isUser(?UserInterface $user = null): bool
    {
        return $user instanceof self && $user->id === $this->id;
    }

    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
    }
}
