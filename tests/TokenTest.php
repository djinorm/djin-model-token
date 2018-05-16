<?php
/**
 * Datetime: 13.11.2017 14:43
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\Token;

use DateTime;
use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Id\Id;
use PHPUnit\Framework\TestCase;
use DjinORM\Models\Token\TokenMock as Token;

class TokenTest extends TestCase
{

    private $entityId;
    private $ip;
    private $userAgent;

    /** @var Token */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->entityId = new Id(777);
        $this->ip = '127.0.0.1';
        $this->userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3260.2 Safari/537.36';

        $this->model = new Token($this->entityId, $this->ip, $this->userAgent);
    }

    public function testConstructInvalidIp()
    {
        $this->expectException(InvalidArgumentException::class);
        new Token($this->entityId, 'ccc', $this->userAgent);
    }

    public function testGetToken()
    {
        $this->assertEquals(65, strlen($this->model->getToken()));
        $this->assertTrue(stripos($this->model->getToken(), $this->model->getId()->toScalar()) !== false);
    }

    public function testIsValidToken()
    {
        $pureToken = explode('.', $this->model->getToken());
        $this->assertTrue($this->model->isTokenValid($pureToken[0]));
        $this->assertFalse($this->model->isTokenValid($pureToken[1]));
    }

    public function testGetEntityId()
    {
        $this->assertSame($this->entityId, $this->model->getEntityId());
    }

    public function testGetIp()
    {
        $this->assertEquals($this->ip, $this->model->getIp());
    }

    public function testSetIp()
    {
        $ip = '192.168.1.1';
        $this->model->setIp($ip);
        $this->assertEquals($ip, $this->model->getIp());
    }

    public function testGetUserAgent()
    {
        $this->assertEquals($this->userAgent, $this->model->getUserAgent());
    }

    public function testSetUserAgent()
    {
        $ua = 'Chrome/64.0.3260.2 Safari/537.36';
        $this->model->setUserAgent($ua);
        $this->assertEquals($ua, $this->model->getUserAgent());
    }

    public function testGetSignedInAt()
    {
        $this->assertDatetimeNow($this->model->getSignedInAt(), true);
    }

    public function testGetLastAccessAt()
    {
        $this->assertDatetimeNow($this->model->getLastAccessAt(), true);
    }

    public function testSetLastAccessAtNow()
    {
        $this->assertDatetimeNow($this->model->getLastAccessAt(), true);
        sleep(2);

        $this->model->setLastAccessAtNow();
        $this->assertDatetimeNow($this->model->getLastAccessAt(), true);
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
