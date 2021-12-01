<?php

/*
 * This file is part of the VipxBotDetectBundle package.
 *
 * (c) Lennart Hildebrandt <http://github.com/lennerd>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vipx\BotDetectBundle\Tests\Security;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Vipx\BotDetect\Metadata\MetadataInterface;
use Vipx\BotDetectBundle\Security\BotToken;

class BotTokenTest extends TestCase
{

    public function testBotRole()
    {
        /** @var MetadataInterface $metadata */
        $metadata = $this->getMockBuilder(MetadataInterface::class)->getMock();
        $token = new BotToken('test', $metadata);

        $this->assertEquals($metadata, $token->getMetadata());

        $roles = $token->getRoleNames();

        $contains = false;

        foreach ($roles as $role) {
            if ('ROLE_BOT' === $role) {
                $contains = true;
            }
        }

        $this->assertTrue($contains, 'The BotToken has no role "ROLE_BOT"');
    }

    public function testCanCreateBotTokenFromAnonymousToken()
    {
        /** @var MetadataInterface $metadata */
        $metadata = $this->getMockBuilder(MetadataInterface::class)->getMock();

        /** @var AnonymousToken|MockObject $anonymousToken */
        $anonymousToken = $this->getMockBuilder(AnonymousToken::class)
            ->disableOriginalConstructor()
            ->getMock();

        $anonymousToken->method('getSecret')->willReturn('theSecretKey');
        $anonymousToken->method('getRoleNames')->willReturn([]);

        $botToken = BotToken::fromAnonymousToken($metadata, $anonymousToken);

        $this->assertInstanceOf(BotToken::class, $botToken);
        $this->assertSame($botToken->getSecret(), $anonymousToken->getSecret());
    }

}
