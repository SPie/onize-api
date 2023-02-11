<?php

namespace DoctrineProxies\__CG__\App\Projects;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class ProjectDoctrineModel extends \App\Projects\ProjectDoctrineModel implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'label', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'description', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'metaData', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'roles', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'metaDataElements', 'id', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'deletedAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'createdAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'updatedAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'uuid'];
        }

        return ['__isInitialized__', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'label', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'description', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'metaData', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'roles', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'metaDataElements', 'id', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'deletedAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'createdAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'updatedAt', '' . "\0" . 'App\\Projects\\ProjectDoctrineModel' . "\0" . 'uuid'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (ProjectDoctrineModel $proxy) {
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
    public function __load(): void
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized(): bool
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized): void
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null): void
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer(): ?\Closure
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null): void
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner(): ?\Closure
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @deprecated no longer in use - generated code now relies on internal components rather than generated public API
     * @static
     */
    public function __getLazyProperties(): array
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setLabel(string $label): \App\Projects\ProjectDoctrineModel
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
    public function setDescription(string $description): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', [$description]);

        return parent::setDescription($description);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', []);

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaData(array $metaData): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMetaData', [$metaData]);

        return parent::setMetaData($metaData);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaData(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMetaData', []);

        return parent::getMetaData();
    }

    /**
     * {@inheritDoc}
     */
    public function setRoles(array $roles): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRoles', [$roles]);

        return parent::setRoles($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function addRole(\App\Projects\RoleModel $role): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRole', [$role]);

        return parent::addRole($role);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRoles', []);

        return parent::getRoles();
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaDataElements(array $metaDataElements): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMetaDataElements', [$metaDataElements]);

        return parent::setMetaDataElements($metaDataElements);
    }

    /**
     * {@inheritDoc}
     */
    public function addMetaDataElement(\App\Projects\MetaDataElementModel $metaDataElement): \App\Projects\ProjectDoctrineModel
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addMetaDataElement', [$metaDataElement]);

        return parent::addMetaDataElement($metaDataElement);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaDataElements(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMetaDataElements', []);

        return parent::getMetaDataElements();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toArray', []);

        return parent::toArray();
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
    public function hasMemberWithEmail(string $email): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'hasMemberWithEmail', [$email]);

        return parent::hasMemberWithEmail($email);
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
    public function setDeletedAt(?\DateTime $deletedAt): \App\Projects\ProjectDoctrineModel
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
    public function restore(): \App\Projects\ProjectDoctrineModel
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
    public function setCreatedAt(?\DateTime $createdAt): \App\Projects\ProjectDoctrineModel
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
    public function setUpdatedAt(?\DateTime $updatedAt): \App\Projects\ProjectDoctrineModel
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
    public function setUuid(string $uuid): \App\Projects\ProjectDoctrineModel
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
