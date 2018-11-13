<?php
/**
 * Datetime: 13.11.2017 14:43
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\Token;

use DateTime;
use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Model\ModelPointer;
use PHPUnit\Framework\TestCase;
use DjinORM\Models\Token\TokenClass as Token;

class TokenTest extends TestCase
{

    private $pointer;
    private $ip;
    private $userAgent;

    /** @var Token */
    private $token;

    /**
     * @throws InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function setUp()
    {
        parent::setUp();
        $this->pointer = new ModelPointer('token', 777);
        $this->ip = '127.0.0.1';
        $this->userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3260.2 Safari/537.36';

        $this->token = new Token($this->pointer, $this->ip, $this->userAgent);
    }

    public function testConstructInvalidIp()
    {
        $this->expectException(InvalidArgumentException::class);
        new Token($this->pointer, 'ccc', $this->userAgent);
    }

    public function testGetToken()
    {
        $this->assertEquals(65, strlen($this->token->getToken()));
        $this->assertTrue(stripos($this->token->getToken(), $this->token->getId()->toScalar()) !== false);
    }

    public function testIsValidToken()
    {
        $pureToken = explode('.', $this->token->getToken());
        $this->assertTrue($this->token->isTokenValid($pureToken[0]));
        $this->assertFalse($this->token->isTokenValid($pureToken[1]));
    }

    public function testGetPointer()
    {
        $this->assertSame($this->pointer, $this->token->getPointer());
    }

    public function testGetIp()
    {
        $this->assertEquals($this->ip, $this->token->getIp());
    }

    public function testSetIp()
    {
        $ip = '192.168.1.1';
        $this->token->setIp($ip);
        $this->assertEquals($ip, $this->token->getIp());
    }

    public function testGetUserAgent()
    {
        $this->assertEquals($this->userAgent, $this->token->getUserAgent());
    }

    public function testSetUserAgent()
    {
        $ua = 'Chrome/64.0.3260.2 Safari/537.36';
        $this->token->setUserAgent($ua);
        $this->assertEquals($ua, $this->token->getUserAgent());
    }

    public function testGetSignedInAt()
    {
        $this->assertDatetimeNow($this->token->getSignedInAt(), true);
    }

    public function testGetLastAccessAt()
    {
        $this->assertDatetimeNow($this->token->getLastAccessAt(), true);
    }

    public function testSetLastAccessAtNow()
    {
        $this->assertDatetimeNow($this->token->getLastAccessAt(), true);
        sleep(2);

        $this->token->setLastAccessAtNow();
        $this->assertDatetimeNow($this->token->getLastAccessAt(), true);
    }

    protected function assertDatetimeNow(\DateTimeInterface $actual, $withSeconds = false, $message = '')
    {
        $format = 'Y-m-d H:i';
        if ($withSeconds) {
            $format.= ':s';
        }
        $expected = (new DateTime())->format($format);
        $actual = $actual->format($format);
        $this->assertEquals($expected, $actual, $message);
    }

}
