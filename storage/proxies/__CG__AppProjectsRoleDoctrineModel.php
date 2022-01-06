<?php

namespace DoctrineProxies\__CG__\App\Projects;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class RoleDoctrineModel extends \App\Projects\RoleDoctrineModel implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array<string, null> properties to be lazy loaded, indexed by property name
     */
    public static $lazyPropertiesNames = array (
);

    /**
     * @var array<string, mixed> default values of properties to be lazy loaded, with keys being the property names
     *
     * @see \Doctrine\Common\Proxy\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array (
);



    public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'label', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'owner', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'project', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'members', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'permissions', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'invitations', 'id', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'deletedAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'createdAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'updatedAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'uuid'];
        }

        return ['__isInitialized__', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'label', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'owner', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'project', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'members', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'permissions', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'invitations', 'id', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'deletedAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'createdAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'updatedAt', '' . "\0" . 'App\\Projects\\RoleDoctrineModel' . "\0" . 'uuid'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (RoleDoctrineModel $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy::$lazyPropertiesDefaults as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @deprecated no longer in use - generated code now relies on internal components rather than generated public API
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setLabel(string $label): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLabel', [$label]);

        return parent::setLabel($label);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLabel', []);

        return parent::getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function setOwner(bool $owner): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOwner', [$owner]);

        return parent::setOwner($owner);
    }

    /**
     * {@inheritDoc}
     */
    public function isOwner(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOwner', []);

        return parent::isOwner();
    }

    /**
     * {@inheritDoc}
     */
    public function getProject(): \App\Projects\ProjectModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProject', []);

        return parent::getProject();
    }

    /**
     * {@inheritDoc}
     */
    public function setMembers(array $members): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMembers', [$members]);

        return parent::setMembers($members);
    }

    /**
     * {@inheritDoc}
     */
    public function addMember(\App\Projects\MemberModel $member): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addMember', [$member]);

        return parent::addMember($member);
    }

    /**
     * {@inheritDoc}
     */
    public function getMembers(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMembers', []);

        return parent::getMembers();
    }

    /**
     * {@inheritDoc}
     */
    public function setPermissions(array $permissions): \App\Projects\RoleDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPermissions', [$permissions]);

        return parent::setPermissions($permissions);
    }

    /**
     * {@inheritDoc}
     */
    public function addPermission(\App\Projects\PermissionModel $permission): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addPermission', [$permission]);

        return parent::addPermission($permission);
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPermissions', []);

        return parent::getPermissions();
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission(string $permissionName): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'hasPermission', [$permissionName]);

        return parent::hasPermission($permissionName);
    }

    /**
     * {@inheritDoc}
     */
    public function setInvitations(array $invitations): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInvitations', [$invitations]);

        return parent::setInvitations($invitations);
    }

    /**
     * {@inheritDoc}
     */
    public function addInvitation(\App\Projects\Invites\InvitationModel $invitation): \App\Projects\RoleModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addInvitation', [$invitation]);

        return parent::addInvitation($invitation);
    }

    /**
     * {@inheritDoc}
     */
    public function getInvitations(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInvitations', []);

        return parent::getInvitations();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(bool $withProject = false): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toArray', [$withProject]);

        return parent::toArray($withProject);
    }

    /**
     * {@inheritDoc}
     */
    public function setId(?int $id): \App\Models\Model
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setDeletedAt(?\DateTime $deletedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDeletedAt', [$deletedAt]);

        return parent::setDeletedAt($deletedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeletedAt(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDeletedAt', []);

        return parent::getDeletedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function restore()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'restore', []);

        return parent::restore();
    }

    /**
     * {@inheritDoc}
     */
    public function isDeleted(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isDeleted', []);

        return parent::isDeleted();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(?\DateTime $createdAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedAt', [$createdAt]);

        return parent::setCreatedAt($createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedAt', []);

        return parent::getCreatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt(?\DateTime $updatedAt): \App\Projects\RoleDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdatedAt', [$updatedAt]);

        return parent::setUpdatedAt($updatedAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdatedAt', []);

        return parent::getUpdatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setUuid(string $uuid): \App\Projects\RoleDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUuid', [$uuid]);

        return parent::setUuid($uuid);
    }

    /**
     * {@inheritDoc}
     */
    public function getUuid(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUuid', []);

        return parent::getUuid();
    }

}
