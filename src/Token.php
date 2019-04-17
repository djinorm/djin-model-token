<?php
/**
 * Datetime: 13.11.2017 12:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\Token;


use DateTimeImmutable;
use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Relation;
use Exception;
use LogicException;

abstract class Token implements ModelInterface
{

    /** @var Id */
    protected $id;

    /** @var Relation */
    protected $owner;

    /** @var string */
    protected $hash;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $userAgent;

    /** @var DateTimeImmutable */
    protected $signedInAt;

    /** @var DateTimeImmutable */
    protected $lastAccessAt;

    /** @var string */
    private $token;

    /**
     * AuthToken constructor.
     * @param Relation $owner
     * @param string $ip
     * @param string $userAgent
     * @throws InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function __construct(Relation $owner, string $ip, string $userAgent)
    {
        $this->id = new Id(bin2hex(random_bytes(16)));

        $this->token = bin2hex(random_bytes(16));
        $this->hash = password_hash($this->token, PASSWORD_BCRYPT);

        $this->owner = $owner;
        $this->setIp($ip);
        $this->userAgent = $userAgent;

        $this->signedInAt = new DateTimeImmutable();
        $this->lastAccessAt = new DateTimeImmutable();
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    public function getToken(): string
    {
        if ($this->token === null) {
            throw new LogicException('Impossible to get token, because only token hash was stored');
        }
        return $this->token . '.' . $this->id->toScalar();
    }

    public function isTokenValid(string $token): bool
    {
        return password_verify($token, $this->hash);
    }

    /**
     * @return Relation
     */
    public function getOwner(): Relation
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @throws InvalidArgumentException
     */
    public function setIp(string $ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Incorrect IP address ' . $ip);
        }

        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getSignedInAt(): DateTimeImmutable
    {
        return $this->signedInAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLastAccessAt(): DateTimeImmutable
    {
        return $this->lastAccessAt;
    }

    /**
     * @throws Exception
     */
    public function setLastAccessAtNow()
    {
        $this->lastAccessAt = new DateTimeImmutable();
    }

}