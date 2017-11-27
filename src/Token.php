<?php
/**
 * Datetime: 13.11.2017 12:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\Token;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;
use LogicException;

class Token implements ModelInterface
{

    use ModelTrait;

    /** @var Id */
    protected $id;

    /** @var Id */
    protected $entityId;

    /** @var string */
    protected $hash;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $userAgent;

    /** @var \DateTimeImmutable */
    protected $signedInAt;

    /** @var \DateTimeImmutable */
    protected $lastAccessAt;

    /** @var string */
    private $token;

    /**
     * AuthToken constructor.
     * @param Id $entityId
     * @param string $ip
     * @param string $userAgent
     */
    public function __construct(Id $entityId, string $ip, string $userAgent)
    {
        $this->id = new Id(bin2hex(random_bytes(16)));

        $this->token = bin2hex(random_bytes(16));
        $this->hash = password_hash($this->token, PASSWORD_BCRYPT);

        $this->entityId = $entityId;
        $this->setIp($ip);
        $this->userAgent = $userAgent;

        $this->signedInAt = new \DateTimeImmutable();
        $this->lastAccessAt = new \DateTimeImmutable();
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
     * @return Id
     */
    public function getEntityId(): Id
    {
        return $this->entityId;
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
     * @return \DateTimeImmutable
     */
    public function getSignedInAt(): \DateTimeImmutable
    {
        return $this->signedInAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLastAccessAt(): \DateTimeImmutable
    {
        return $this->lastAccessAt;
    }

    public function setLastAccessAtNow()
    {
        $this->lastAccessAt = new \DateTimeImmutable();
    }

}